import React, { useEffect, useState } from 'react';
import { View, Text, TouchableOpacity, Image, Animated } from 'react-native';
import { router, useLocalSearchParams } from 'expo-router';
import Styles from './Styles';
import { getLocation } from './Location';
import { Biometrics } from './Biometrics';
import { WebSocketObject } from './WebSocketObject';



function Session_biometric_location() {
    const { user, data } = useLocalSearchParams()
    const userObj = JSON.parse(user);
    const codeData = JSON.parse(data);

    ws = new WebSocket('ws://86.58.51.113:8080');

    ws.onopen = () => {
    console.log('WebSocket connection opened');
    };

    ws.onmessage = (e) => {
    const response = JSON.parse(e.data);
    console.log('Received response:', response);

    if (response.action === 'joined') {
        router.push({
             pathname: '/moduls/Session_attendance',
        });
    } else {
        console.error('Room does not exist or invalid response');
    }
    };

    ws.onerror = (e) => {
    console.error('WebSocket error:', e.message);
    };

    ws.onclose = (e) => {
    console.log('WebSocket connection closed:', e.code, e.reason);
    };
    
    const imageSourceFingerprint = require('../../assets/fingerprint.png');
    const imageSourceLocation = require('../../assets/location.png');
    
    //States for check-ups
    const [biometricsState, setbiometricsState] = useState(true);
    const [locationState, setlocationState] = useState(false);

    //States for button colors
    const [fingerprintButtonColor, setFingerprintButtonColor] = useState(new Animated.Value(0));
    const [locationButtonColor, setLocationButtonColor] = useState(new Animated.Value(0));


    //Location data state
    const [location, setLocation] = useState(null);
    const [biometricData, setBiometricData] = useState(null);

    //Biometrija
    let bio = false
    const handleFingreprintPress = async () => {
        console.log('Fingerprint pressed!');
        bio = await Biometrics()
        setBiometricData(bio)
        console.log(`bio state: ${bio}`);

    };

    //Biometrija state

    useEffect(() => {
        if (biometricData) {
            console.log(`biostate data: ${JSON.stringify(biometricData)}`);
            setbiometricsState(true) //Ta del kode povzroči reroute
            console.log(`Podatki: ${userObj.email}, Code data: ${codeData}`);
        }
    }, [biometricData]);



    //Lokacija
    const handleLocationPress = async () => {
        console.log('Location pressed!');
        const loc = await getLocation();
        if (loc) {
            console.log(`Location received: ${JSON.stringify(loc)}`);
            setLocation(loc);
        } 
    };

    // Lokacija state
    useEffect(() => {
        if (location) {
            console.log(`location data: ${JSON.stringify(location)}`);
            setlocationState(true);   //Ta del kode povzroči reroute
        }
    }, [location]);


    //Preverjanje in ustvarjanje 
    useEffect(() => {
        if (biometricsState && locationState) {
            const oseba = new WebSocketObject('join', codeData, userObj.name, userObj.email, biometricData, location)
            console.log(JSON.stringify(oseba));
            ws.send(JSON.stringify(oseba))
        }
    }, [biometricsState, locationState]);






    //Animacija
    useEffect(() => {
        if (biometricsState) {
            Animated.timing(fingerprintButtonColor, {
                toValue: 1,
                duration: 500,
                useNativeDriver: true,
            }).start();
        }
    }, [biometricsState, fingerprintButtonColor]);

    useEffect(() => {
        if (locationState) {
            Animated.timing(locationButtonColor, {
                toValue: 1,
                duration: 500,
                useNativeDriver: true,
            }).start();
        }
    }, [locationState, locationButtonColor]);


    const fingerprintButtonBackgroundColor = fingerprintButtonColor.interpolate({
        inputRange: [0, 1],
        outputRange: ['white', '#10f52c'],
    });


    const locationButtonBackgroundColor = locationButtonColor.interpolate({
        inputRange: [0, 1],
        outputRange: ['white', '#10f52c'],
    });



    return (
        <View style={{ flex: 1, justifyContent: 'center', alignItems: 'center' }}>

            <TouchableOpacity style={[Styles.circulat_button, { backgroundColor: fingerprintButtonBackgroundColor }]} onPress={handleFingreprintPress}>
                <View style={Styles.circular_container}>
                    <Image source={imageSourceFingerprint} style={Styles.image_circular} />
                </View>
            </TouchableOpacity>

            <Text style={Styles.margin_vertical}>Pritisnite za identifikacijo</Text>

            <TouchableOpacity style={[Styles.circulat_button, { backgroundColor: locationButtonBackgroundColor }]} onPress={handleLocationPress}>
                <View style={Styles.circular_container}>
                    <Image source={imageSourceLocation} style={Styles.image_circular} />
                </View>
            </TouchableOpacity>

            <Text style={Styles.margin_vertical}>Pritisnite za lokacijo</Text>

        </View>
    )
}


export default Session_biometric_location;
