import * as React from 'react';
import { useState, useEffect } from 'react';
import { View, StyleSheet } from 'react-native';
import { IconButton, Provider as PaperProvider, Text, ActivityIndicator } from 'react-native-paper';
import Header from './Appbar';
import Footer from './BottomNavBar';

import { getLocation } from './Location';
import { Biometrics } from './Biometrics';

const TestModule = () => {
    // Icons
    const fingerprintIcon = require('../../assets/fingerprint.png')
    const locationIcon = require('../../assets/location.png')

    // Pressed state
    const [fingerprintPressed, setFingerprintPressed] = useState(false);
    const [locationPressed, setLocationPressed] = useState(false);

    // Loading biometrics states
    const [fingerprintLoading, setFingerprintLoading] = useState(false)
    const [locationLoading, setLocationLoading] = useState(false)

    // Location data state
    const [location, setLocation] = useState(null);
    const [biometricData, setBiometricData] = useState(null);

    // States for check-ups
    const [biometricsState, setbiometricsState] = useState(false);
    const [locationState, setlocationState] = useState(false);

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
};

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

export default TestModule;
