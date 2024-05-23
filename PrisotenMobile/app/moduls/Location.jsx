import React, { useEffect } from 'react';

import * as Loc from 'expo-location';


//Prižig gps denied dodaj za unhandled rejection
export async function getLocation() {

    let { status } = await Loc.requestForegroundPermissionsAsync(); //Zahteva za uporabo lokacije

    if (status == 'granted') {
        console.log('Permission granted!');
        const location = await Loc.getCurrentPositionAsync()
        return location
    } else {
        console.log('Permission denied!');
        alert('Potrebno je dovoljenje za lokacijo!')
        return null;
    }
    
}

/*
export async function getLocation() {
    // Request permission to access location
    let { status } = await Loc.requestForegroundPermissionsAsync();

    if (status === 'granted') {
        console.log('Permission granted!');

        // Check if location services (GPS) are enabled
        const isLocationEnabled = await Loc.hasServicesEnabledAsync();
        
        if (isLocationEnabled) {
            // Get the current position if location services are enabled
            const location = await Loc.getCurrentPositionAsync();
            return location;
        } else {
            console.log('Location services are disabled!');
            Alert.alert('Location Services Disabled', 'Please enable location services (GPS) to use this feature.');
            return null;
        }
    } else {
        console.log('Permission denied!');
        Alert.alert('Permission Denied', 'Potrebno je dovoljenje za lokacijo!');
        return null;
    }
}
*/