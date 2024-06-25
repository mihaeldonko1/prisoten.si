import React, { useEffect } from 'react';

import * as Loc from 'expo-location';


export async function getLocation() {
    try {
        const servicesEnabled = await Loc.hasServicesEnabledAsync();
        
        if (!servicesEnabled) {
            //UI fixes
            alert('Lokacija mora biti omogočena!');
            return null;
        }

        let { status } = await Loc.requestForegroundPermissionsAsync();

        if (status === 'granted') {
            console.log('Permission granted!');
            const location = await Loc.getCurrentPositionAsync();
            return location;
        } else {
            console.log('Permission denied!');
            //UI fixes
            alert('Potrebno je dovoljenje za lokacijo!');
            return null;
        }
    } catch (error) {
        console.error('Error getting location:', error);
        //UI fixes
        alert('Prišlo je do napake pri pridobivanju lokacije!');
        return null;
    }
}
