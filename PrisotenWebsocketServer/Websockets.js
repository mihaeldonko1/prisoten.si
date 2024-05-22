const WebSocket = require('ws');

const server = new WebSocket.Server({ port: 8080 });

const rooms = {};  // To store active rooms with their WebSocket connections

function generateRoomCode() {
    let code;
    do {
        code = Math.floor(10000000 + Math.random() * 90000000).toString();
    } while (rooms[code]);  // Ensure the code is unique
    return code;
}

function checkLegitimacy(email, biometric_rule, location) {
    // Implement your legitimacy check logic here.
    // For now, it returns true for demonstration purposes.
    console.log(email,biometric_rule,location);
    console.log("confirmed");
    return true;
}

server.on('connection', (ws) => {
    ws.on('message', (message) => {
        const parsedMessage = JSON.parse(message);

        if (parsedMessage.action === 'create') {
            const roomCode = generateRoomCode();
            rooms[roomCode] = { host: ws, clients: [] };
            ws.roomCode = roomCode;
            ws.isHost = true;
            ws.send(JSON.stringify({ action: 'created', roomCode: roomCode }));
        } else if (parsedMessage.action === 'join') {
            const { roomCode, email, biometric_rule, location } = parsedMessage;

            if (!email || typeof biometric_rule !== 'boolean' || !location) {
                ws.send(JSON.stringify({ action: 'error', message: 'Email, biometric_rule, and location are required to join the room' }));
                return;
            }

            if (!checkLegitimacy(email, biometric_rule, location)) {
                ws.send(JSON.stringify({ action: 'error', message: 'Legitimacy check failed' }));
                return;
            }

            if (rooms[roomCode]) {
                rooms[roomCode].clients.push({ ws: ws, email: email, biometric_rule: biometric_rule, location: location });
                ws.roomCode = roomCode;
                ws.email = email;

                console.log(`User with email ${email} joined room ${roomCode}`);
                ws.send(JSON.stringify({ action: 'joined', roomCode: roomCode }));

                // Notify the host
                const host = rooms[roomCode].host;
                if (host && host.readyState === WebSocket.OPEN) {
                    host.send(JSON.stringify({ action: 'user_joined', email: email, biometric_rule: biometric_rule, location: location }));
                }
            } else {
                ws.send(JSON.stringify({ action: 'error', message: 'Room not found' }));
            }
        }
    });

    ws.on('close', () => {
        const roomCode = ws.roomCode;
        if (roomCode && rooms[roomCode]) {
            if (rooms[roomCode].host === ws) {
                // Host disconnected, notify all clients and remove the room
                rooms[roomCode].clients.forEach(client => client.ws.send(JSON.stringify({ action: 'error', message: 'Host disconnected' })));
                delete rooms[roomCode];
            } else {
                // Remove client from the room
                rooms[roomCode].clients = rooms[roomCode].clients.filter(client => client.ws !== ws);
            }
        }
        console.log('Client disconnected');
    });
});

console.log('WebSocket server is running on ws://localhost:8080');
