import React, { useEffect, useRef, useState } from 'react';
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

    //Images
    const imageSourceFingerprint = require('../../assets/fingerprint.png');
    const imageSourceLocation = require('../../assets/location.png');

    //States for check-ups
    const [biometricsState, setbiometricsState] = useState(false);
    const [locationState, setlocationState] = useState(false);

    //States for button colors
    const [fingerprintButtonColor, setFingerprintButtonColor] = useState(new Animated.Value(0));
    const [locationButtonColor, setLocationButtonColor] = useState(new Animated.Value(0));


    //Location data state
    const [location, setLocation] = useState(null);
    const [biometricData, setBiometricData] = useState(null);

    //WebSocket
    const ws = useRef(null)
    const [wsState, setWsState] = useState(false)

    useEffect(() => {
        if (!wsState) {
            webSocketStarter()
            setWsState(true)
        }
    }, []);


    const webSocketStarter = () => {
        ws.current = new WebSocket('ws://194.152.25.94:8080');
        //console.log('------ WebSocket useEffect -----');

        ws.current.onopen = () => {
            //console.log('WebSocket connection opened');
        };

        ws.current.onerror = (e) => {
            console.error('WebSocket error:', e.message);
        };
    }
    const webSocketCloser = () => {
        if (ws.current) {
            ws.current.close();

            ws.current.onclose = (e) => {
                console.log('WebSocket connection closed:', e.code, e.reason);
            };
        }
    }



    const sendWebSocketMessage = async () => {
        if (ws.current && ws.current.readyState === WebSocket.OPEN) {
            const oseba = new WebSocketObject('join', codeData, userObj.name, userObj.email, biometricData, location);
            //console.log(JSON.stringify(oseba));
            const response = await sendWebSocketRequest(JSON.stringify(oseba));
            handleWebSocketResponse(response);
        } else {
            console.log('WebSocket is not open. Retrying...');
            setTimeout(sendWebSocketMessage, 1000);  // Retry after 1 second
        }
    };

    const sendWebSocketRequest = (message) => {
        return new Promise((resolve, reject) => {
            ws.current.onmessage = (e) => {
                let response = JSON.parse(e.data);
                console.log('Resolve: ', response);
                resolve(response);
            };
            ws.current.onerror = (e) => {
                reject(new Error('WebSocket error'));
            };
            ws.current.send(message);
        });
    };

    const handleWebSocketResponse = (response) => {
        console.log('Received response:', response);

        if (response.action === 'joined') {
            webSocketCloser();
            router.push({
                pathname: '/moduls/Session_attendance',
            });
        } else {
            console.error('Room does not exist or invalid response');
        }
    };
    //das



    //Biometrija
    let bio = false
    const handleFingreprintPress = async () => {
        console.log('Fingerprint pressed!');
        bio = await Biometrics();
        setBiometricData(bio);
        //console.log(`bio state: ${bio}`);

    };

    //Biometrija state

    useEffect(() => {
        if (biometricData) {
            //console.log(`biostate data: ${JSON.stringify(biometricData)}`);
            setbiometricsState(true); //Ta del kode povzroči reroute
            // console.log(`Podatki: ${userObj.email}, Code data: ${codeData}`);
        }
    }, [biometricData]);



    //Lokacija
    const handleLocationPress = async () => {
        console.log('Location pressed!');
        const loc = await getLocation();
        if (loc) {
            //console.log(`Location received: ${JSON.stringify(loc)}`);
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



    // Redirect
    useEffect(() => {
        if (biometricsState && locationState) {
            sendWebSocketMessage();
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
