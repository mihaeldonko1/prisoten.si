import React from 'react';
import * as Loc from 'expo-location';


export async function getLocation() {
    try {
        const servicesEnabled = await Loc.hasServicesEnabledAsync();
        
        if (!servicesEnabled) {
            return null;
        }

        let { status } = await Loc.requestForegroundPermissionsAsync();

        if (status === 'granted') {
            console.log('Permission granted!');
            const location = await Loc.getCurrentPositionAsync();
            return location;
        } else {
            console.log('Permission denied!');
            return null;
        }
    } catch (error) {
        console.error('Error getting location:', error);
        return null;
    }
}
