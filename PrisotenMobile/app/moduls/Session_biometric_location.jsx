import React, { useEffect, useRef, useState } from 'react';
import { View, StyleSheet } from 'react-native';
import { IconButton, Provider as PaperProvider, Text, ActivityIndicator } from 'react-native-paper';
import { router, useLocalSearchParams } from 'expo-router';

import Header from './Appbar';
import Footer from './BottomNavBar';

import { getLocation } from './Location';
import { Biometrics } from './Biometrics';
import { WebSocketObject } from './WebSocketObject';



function Session_biometric_location() {
    const { user, data } = useLocalSearchParams()
    const userObj = JSON.parse(user);
    const codeData = JSON.parse(data);

    // Icons
    const fingerprintIcon = require('../../assets/fingerprint.png')
    const locationIcon = require('../../assets/location.png')

    //States for check-ups
    const [biometricsState, setbiometricsState] = useState(false);
    const [locationState, setlocationState] = useState(false);

    // Loading biometrics states
    const [fingerprintLoading, setFingerprintLoading] = useState(false)
    const [locationLoading, setLocationLoading] = useState(false)

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
        ws.current = new WebSocket('ws://86.58.51.222:8080');
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

    // Biometrija
    let bio = false
    const handleFingreprintPress = async () => {
        console.log('Fingerprint pressed!');
        if (!biometricsState) {
            setFingerprintLoading(true)
            bio = await Biometrics();
            setBiometricData(bio);
        }
    };

    // Biometrija state
    useEffect(() => {
        if (biometricData) {
            setbiometricsState(true); //Ta del kode povzroči reroute
            setFingerprintLoading(false)
        }
    }, [biometricData]);



    // Lokacija 
    const handleLocationPress = async () => {
        console.log('Location pressed!');
        if (!locationState) {
            setLocationLoading(true)
            const loc = await getLocation();
            if (loc) {
                setLocation(loc);
            }
        }
    };

    // Lokacija state
    useEffect(() => {
        if (location) {
            console.log(`location data: ${JSON.stringify(location)}`);
            setlocationState(true);   //Ta del kode povzroči reroute
            setLocationLoading(false)
        }
    }, [location]);



    // Redirect
    useEffect(() => {
        if (biometricsState && locationState) {
            sendWebSocketMessage();
        }
    }, [biometricsState, locationState]);




    return (
        <PaperProvider>
            <Header />
            <View style={styles.container}>
                <View style={styles.buttonContainer}>
                    <IconButton
                        mode='contained'
                        icon={fingerprintIcon}
                        iconColor='black'
                        containerColor={biometricsState ? '#31F786' : '#10CEED'}
                        size={64}
                        onPress={() => handleFingreprintPress()}
                        style={styles.iconButton}
                        loading={fingerprintLoading}
                    />
                    <Text style={styles.text}>Biometrija</Text>
                </View>
                <View style={styles.buttonContainer}>
                    <IconButton
                        mode='contained'
                        icon={locationIcon}
                        iconColor='black'
                        containerColor={locationState ? '#31F786' : '#10CEED'}
                        size={64}
                        onPress={() => handleLocationPress()}
                        style={styles.iconButton}
                        loading={locationLoading}
                    />
                    <Text style={styles.text}>Lokacija</Text>
                </View>
            </View>
            <Footer />
        </PaperProvider>
    );
}

const styles = StyleSheet.create({
    container: {
        flex: 1,
        justifyContent: 'center',
        alignItems: 'center',
        flexDirection: 'row',
        padding: 16,
        backgroundColor: '#F5F5F5'
    },
    buttonContainer: {
        alignItems: 'center',
        margin: 10,
    },
    iconButton: {
        marginBottom: 5,
    },
    text: {
        fontSize: 16,
    },
});


export default Session_biometric_location;
