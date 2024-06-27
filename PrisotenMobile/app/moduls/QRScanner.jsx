import React, { useState, useEffect } from 'react';
import { View, Text as RNText, Button as RNButton, StyleSheet } from 'react-native';
import { router, useLocalSearchParams } from 'expo-router';
import { CameraView, useCameraPermissions } from 'expo-camera';

import Styles from './Styles';
import { Button, PaperProvider, Text } from 'react-native-paper';
import Header from './Appbar';
import Footer from './BottomNavBar';


function QRScanner() {
    const { user } = useLocalSearchParams()
    const userObj = JSON.parse(user);

    const [permission, requestPermission] = useCameraPermissions();

    const [scanned, setScanned] = useState(false);

    const [inputValue, setInputValue] = useState('');

    useEffect(() => {
        if (inputValue != '') {

            // const ws = new WebSocket('ws://194.152.25.94:8080');

            // ws.onopen = () => {
            //     console.log('WebSocket connection opened');
            //     const message = {
            //         action: 'room_exists',
            //         roomCode: inputValue,
            //     };
            //     ws.send(JSON.stringify(message));
            // };

            // ws.onmessage = (e) => {
            //     const response = JSON.parse(e.data);
            //     // console.log('Received response:', response);

            //     if (response.action === 'room_exists' && response.exists) {
            //         ws.close();
            //         // console.log('WebSocket connection manually closed');
            //         router.push({
            //             pathname: '/moduls/Session_biometric_location',
            //             params: {
            //                 user: JSON.stringify(userObj),
            //                 data: JSON.stringify(inputValue),
            //             },
            //         });
            //     } else {
            //         //UI fix needed - 
            //         alert('Soba ne obstaja');
            //     }
            // };

            // ws.onerror = (e) => {
            //     console.error('WebSocket error:', e.message);
            // };

            // ws.onclose = (e) => {
            //     console.log('WebSocket connection closed:', e.code, e.reason);
            // };
        }
    }, [inputValue]);

    if (!permission) {
        // Camera permissions are still loading.
        return <View />;
    }

    if (!permission.granted) {
        // Camera permissions are not granted yet.
        return (
            <View style={styles.container}>
                <Text style={{ textAlign: 'center' }}>We need your permission to show the camera</Text>
                <Button onPress={requestPermission} title="grant permission" />
            </View>
        );
    }

    const handleBarCodeScanned = ({ types, data }) => {
        setScanned(true);
        setInputValue(data);
    }

    const handleScannAgain = () => {
        setScanned(false);
        setInputValue('');
    }



    return (
        <PaperProvider>
            <Header />
            <View style={Styles.containerPaper}>
                <CameraView
                    style={styles.camera}
                    onBarcodeScanned={scanned ? undefined : handleBarCodeScanned}
                    barcodeScannerSettings={{
                        barcodeTypes: ['qr', 'aztec']
                    }}
                />
                <Button
                    mode="contained"
                    onPress={handleScannAgain}
                    style={styles.buttonStyle}
                >
                    Skeniraj
                </Button>
            </View>
            <Footer />
        </PaperProvider>
    )
}

const styles = StyleSheet.create({
    cameraContainer: {
        flex: 1,
        justifyContent: 'center',
        alignItems: 'center',
    },
    camera: {
        width: '80%',
        height: '80%',
    },
    buttonStyle: {
        backgroundColor: '#10CEED',
        borderRadius: 8,
        marginTop: 32,
        width: 110,
    },
});

export default QRScanner;