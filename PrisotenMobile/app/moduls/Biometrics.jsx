import React from 'react';
import { authenticateAsync } from 'expo-local-authentication';

export async function Biometrics() {
    
    const res = await authenticateAsync();
    
    console.log(res.success);

    return res.success;

}