const fs = require('fs');
const http = require('http');
const WebSocket = require('ws');
const CryptoJS = require('crypto-js');
require('dotenv').config(); // Load environment variables

const server = http.createServer();
const wss = new WebSocket.Server({ server });

const rooms = {};

// Load keys from .env file
const encryptionKey = process.env.ENCRYPTION_KEY;
const encryptionIV = process.env.ENCRYPTION_IV;

// Function to log messages with a timestamp
function logMessage(message) {
    const timestamp = new Date().toISOString();
    console.log(`[${timestamp}] ${message}`);
}

// Function to encrypt a message
function encrypt(text) {
    logMessage(`Encrypting message: ${text}`);
    const ciphertext = CryptoJS.AES.encrypt(text, CryptoJS.enc.Hex.parse(encryptionKey), {
        iv: CryptoJS.enc.Hex.parse(encryptionIV),
        mode: CryptoJS.mode.CBC,
        padding: CryptoJS.pad.Pkcs7
    }).toString();
    logMessage(`Encrypted message: ${ciphertext}`);
    return ciphertext;
}

// Function to decrypt a message
function decrypt(ciphertext) {
    const sendText = ciphertext.toString();
    logMessage(`Decrypting message: ${sendText}`);
    try {
        // Directly decrypt the Base64 ciphertext
        const bytes = CryptoJS.AES.decrypt(sendText, CryptoJS.enc.Hex.parse(encryptionKey), {
            iv: CryptoJS.enc.Hex.parse(encryptionIV),
            mode: CryptoJS.mode.CBC,
            padding: CryptoJS.pad.Pkcs7
        });
        const decryptedText = bytes.toString(CryptoJS.enc.Utf8);
        if (!decryptedText) {
            logMessage('Decryption resulted in an empty string. Possible decryption failure.');
            return null;
        }
        logMessage(`Decrypted message: ${decryptedText}`);
        return decryptedText;
    } catch (error) {
        logMessage(`Decryption error: ${error.message}`);
        return null;
    }
}

function generateRoomCode() {
    let code;
    do {
        code = Math.floor(10000000 + Math.random() * 90000000).toString();
    } while (rooms[code]);
    logMessage(`Generated room code: ${code}`);
    return code;
}

function locationCheck(room_location, student_location, diameter) {
    logMessage(`Performing location check...`);
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
    logMessage(`Distance calculated: ${distance}, Diameter allowed: ${diameter}`);

    return distance <= diameter;
}

function checkLegitimacy(email, biometric_rule, location, roomCode) {
    logMessage(`Checking legitimacy for email: ${email}, room code: ${roomCode}`);
    const room = rooms[roomCode];
    if (!room) {
        logMessage(`Room not found for code: ${roomCode}`);
        return false; 
    }

    if (biometric_rule === false) {
        logMessage(`Biometric rule failed for email: ${email}`);
        return false;
    } else {
        let hostLocation = room.hostDetails.location;
        let hostDiameter = room.hostDetails.diameter;
        return locationCheck(hostLocation, location, hostDiameter);
    }
}

wss.on('connection', (ws) => {
    logMessage('New client connected');

    ws.on('message', (message) => {
        logMessage(`Received message: ${message}`);
        const decryptedMessage = decrypt(message);

        if (decryptedMessage === null) {
            logMessage('Failed to decrypt the message. Skipping processing.');
            return;
        }

        logMessage(`Decrypted message: ${decryptedMessage}`);
        try {
            const parsedMessage = JSON.parse(decryptedMessage);
            logMessage(`Parsed message: ${JSON.stringify(parsedMessage)}`);

            if (parsedMessage.action === 'create') {
                const { name, email, location, diameter } = parsedMessage;
                logMessage(`Create room request received from: ${email}`);

                if (!name || !email || !location || diameter === undefined) {
                    logMessage(`Invalid create room request: Missing fields`);
                    ws.send(encrypt(JSON.stringify({ action: 'error', message: 'All fields are required to create a room' })));
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
                logMessage(`Room created with code: ${roomCode}`);
                ws.send(encrypt(JSON.stringify({ action: 'created', roomCode: roomCode })));
            } else if (parsedMessage.action === 'join') {
                const { roomCode, name, email, biometric_rule, location } = parsedMessage;
                logMessage(`Join room request received from: ${email}, for room code: ${roomCode}`);

                if (!roomCode || !name || !email || typeof biometric_rule !== 'boolean' || !location) {
                    logMessage(`Invalid join room request: Missing fields`);
                    ws.send(encrypt(JSON.stringify({ action: 'error', message: 'All fields are required to join the room' })));
                    return;
                }

                if (!rooms[roomCode]) {
                    logMessage(`Room not found for code: ${roomCode}`);
                    ws.send(encrypt(JSON.stringify({ action: 'error', message: 'Room not found' })));
                    return;
                }

                if (!checkLegitimacy(email, biometric_rule, location, roomCode)) {
                    logMessage(`Legitimacy check failed for email: ${email}`);
                    ws.send(encrypt(JSON.stringify({ action: 'error', message: 'Legitimacy check failed' })));
                    return;
                }

                rooms[roomCode].clients.push({ ws: ws, name: name, email: email, biometric_rule: biometric_rule, location: location });
                ws.roomCode = roomCode;
                ws.name = name;
                ws.email = email;

                logMessage(`User with email ${email} joined room ${roomCode}`);
                ws.send(encrypt(JSON.stringify({ action: 'joined', roomCode: roomCode })));

                const host = rooms[roomCode].host;
                if (host && host.readyState === WebSocket.OPEN) {
                    logMessage(`Notifying host about new user join: ${email}`);
                    host.send(encrypt(JSON.stringify({ action: 'user_joined', name: name, email: email, biometric_rule: biometric_rule, location: location })));
                }
            } else if (parsedMessage.action === 'room_exists') {
                const { roomCode } = parsedMessage;
                logMessage(`Room exists check request for code: ${roomCode}`);

                if (!roomCode) {
                    logMessage(`Invalid room exists check: Missing room code`);
                    ws.send(encrypt(JSON.stringify({ action: 'error', message: 'Room code is required' })));
                    return;
                }

                const exists = !!rooms[roomCode];
                logMessage(`Room exists for code ${roomCode}: ${exists}`);
                ws.send(encrypt(JSON.stringify({ action: 'room_exists', roomCode: roomCode, exists: exists })));
            }
        } catch (jsonError) {
            logMessage(`Error parsing JSON: ${jsonError.message}`);
        }
    });

    ws.on('close', () => {
        logMessage('Client disconnected');
        const roomCode = ws.roomCode;
        if (roomCode && rooms[roomCode]) {
            if (rooms[roomCode].host === ws) {
                logMessage(`Host disconnected, notifying clients and deleting room: ${roomCode}`);
                rooms[roomCode].clients.forEach(client => client.ws.send(encrypt(JSON.stringify({ action: 'error', message: 'Host disconnected' }))));
                delete rooms[roomCode];
            } else {
                logMessage(`Client disconnected from room: ${roomCode}`);
                rooms[roomCode].clients = rooms[roomCode].clients.filter(client => client.ws !== ws);
            }
        }
    });
});

server.listen(8080, () => {
    logMessage('WebSocket server is running on ws://localhost:8080');
});
