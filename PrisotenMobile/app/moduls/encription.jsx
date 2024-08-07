import React from "react";
import CryptoJS from 'crypto-js';
import { ENCRYPTION_KEY, ENCRYPTION_IV } from '@env';

const encryptionKey = ENCRYPTION_KEY
const encryptionIV = ENCRYPTION_IV
// Function to encrypt data
export function encrypt(text) {
    console.log("Encrypting:", text);
    const encrypted = CryptoJS.AES.encrypt(text, CryptoJS.enc.Hex.parse(encryptionKey), {
        iv: CryptoJS.enc.Hex.parse(encryptionIV),
        mode: CryptoJS.mode.CBC,
        padding: CryptoJS.pad.Pkcs7
    });
    const encryptedBase64 = encrypted.toString(); // AES encrypt returns a Base64 string by default
    console.log("Encrypted message (Base64):", encryptedBase64);
    return encryptedBase64;
}

// Function to decrypt data
export function decrypt(ciphertext) {
    console.log("Decrypting (Base64):", ciphertext);
    const bytes = CryptoJS.AES.decrypt(ciphertext, CryptoJS.enc.Hex.parse(encryptionKey), {
        iv: CryptoJS.enc.Hex.parse(encryptionIV),
        mode: CryptoJS.mode.CBC,
        padding: CryptoJS.pad.Pkcs7
    });
    const decryptedText = bytes.toString(CryptoJS.enc.Utf8);
    console.log("Decrypted message:", decryptedText);
    return decryptedText;
}

