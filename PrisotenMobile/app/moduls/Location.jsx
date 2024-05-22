import React, { useEffect, useState } from 'react';

import * as Loc from 'expo-location';

export async function Location() {

    let { status } = await Loc.requestForegroundPermissionsAsync(); //Zahteva za uporabo lokacije

    if (status == 'granted') {
        console.log('Permission granted!');
        const location = await Loc.getCurrentPositionAsync()
        return location
    } else {
        console.log('Permission denied!');
        alert('Potrebno je dovoljenje za lokacijo!')
    }
    
}