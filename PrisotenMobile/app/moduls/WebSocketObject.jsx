import React from 'react';

export class WebSocketObject {
    constructor(action, roomCode, name, email, biometric_rule, location) {
        this.action = action;
        this.roomCode = roomCode;
        this.name = name;
        this.email = email;
        this.biometric_rule = biometric_rule;
        this.location = location;
    }

    pregled() {
        return `Ime: ${this.name}, Email: ${this.email}, biometric_rule: ${this.biometric_rule}, location: ${this.location}.`
    }
}