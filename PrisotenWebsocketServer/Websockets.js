const fs = require('fs');
const https = require('https');
const WebSocket = require('ws');

const server = https.createServer({
  cert: fs.readFileSync('cert.pem'),
  key: fs.readFileSync('key.pem')
});

const wss = new WebSocket.Server({ server });

const rooms = {};

function generateRoomCode() {
    let code;
    do {
        code = Math.floor(10000000 + Math.random() * 90000000).toString();
    } while (rooms[code]);
    return code;
}

function locationCheck(room_location, student_location, diameter) {
    function toRadians(degrees) {
        return degrees * Math.PI / 180;
    }
    function haversine(lat1, lon1, lat2, lon2) {
        const R = 6371e3; 
        const φ1 = toRadians(lat1);
        const φ2 = toRadians(lat2);
        const Δφ = toRadians(lat2 - lat1);
        const Δλ = toRadians(lon2 - lon1);

        const a = Math.sin(Δφ / 2) * Math.sin(Δφ / 2) +
                  Math.cos(φ1) * Math.cos(φ2) *
                  Math.sin(Δλ / 2) * Math.sin(Δλ / 2);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));

        return R * c; 
    }

    const roomLat = room_location.coords.latitude;
    const roomLon = room_location.coords.longitude;
    const studentLat = student_location.coords.latitude;
    const studentLon = student_location.coords.longitude;

    const distance = haversine(roomLat, roomLon, studentLat, studentLon);

    return distance <= diameter;
}

function checkLegitimacy(email, biometric_rule, location, roomCode) {
    const room = rooms[roomCode];
    if (!room) {
        return false; 
    }

    if (biometric_rule === false) {
        return false;
    } else {
        let hostLocation = room.hostDetails.location;
        let hostDiameter = room.hostDetails.diameter;
        return locationCheck(hostLocation, location, hostDiameter);
    }
}

wss.on('connection', (ws) => {
    ws.on('message', (message) => {
        const parsedMessage = JSON.parse(message);

        if (parsedMessage.action === 'create') {
            const { name, email, location, diameter } = parsedMessage;
            if (!name || !email || !location || diameter === undefined) {
                ws.send(JSON.stringify({ action: 'error', message: 'All fields are required to create a room' }));
                return;
            }

            const roomCode = generateRoomCode();
            rooms[roomCode] = {
                host: ws,
                clients: [],
                hostDetails: { name, email, location, diameter }
            };
            ws.roomCode = roomCode;
            ws.isHost = true;
            ws.send(JSON.stringify({ action: 'created', roomCode: roomCode }));
        } else if (parsedMessage.action === 'join') {
            const { roomCode, name, email, biometric_rule, location } = parsedMessage;

            if (!roomCode || !name || !email || typeof biometric_rule !== 'boolean' || !location) {
                ws.send(JSON.stringify({ action: 'error', message: 'All fields are required to join the room' }));
                return;
            }

            if (!rooms[roomCode]) {
                ws.send(JSON.stringify({ action: 'error', message: 'Room not found' }));
                return;
            }

            if (!checkLegitimacy(email, biometric_rule, location, roomCode)) {
                ws.send(JSON.stringify({ action: 'error', message: 'Legitimacy check failed' }));
                return;
            }

            rooms[roomCode].clients.push({ ws: ws, name: name, email: email, biometric_rule: biometric_rule, location: location });
            ws.roomCode = roomCode;
            ws.name = name;
            ws.email = email;

            console.log(`User with email ${email} joined room ${roomCode}`);
            ws.send(JSON.stringify({ action: 'joined', roomCode: roomCode }));

            const host = rooms[roomCode].host;
            if (host && host.readyState === WebSocket.OPEN) {
                host.send(JSON.stringify({ action: 'user_joined', name: name, email: email, biometric_rule: biometric_rule, location: location }));
            }
        } else if (parsedMessage.action === 'room_exists') {
            const { roomCode } = parsedMessage;
            if (!roomCode) {
                ws.send(JSON.stringify({ action: 'error', message: 'Room code is required' }));
                return;
            }

            const exists = !!rooms[roomCode];
            ws.send(JSON.stringify({ action: 'room_exists', roomCode: roomCode, exists: exists }));
        }
    });

    ws.on('close', () => {
        const roomCode = ws.roomCode;
        if (roomCode && rooms[roomCode]) {
            if (rooms[roomCode].host === ws) {
                rooms[roomCode].clients.forEach(client => client.ws.send(JSON.stringify({ action: 'error', message: 'Host disconnected' })));
                delete rooms[roomCode];
            } else {
                rooms[roomCode].clients = rooms[roomCode].clients.filter(client => client.ws !== ws);
            }
        }
        console.log('Client disconnected');
    });
});

server.listen(8080, () => {
    console.log('WebSocket server is running on wss://localhost:8080');
});
