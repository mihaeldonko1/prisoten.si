import React, { useState, useEffect } from 'react';
import { View, StyleSheet } from 'react-native';
import { router, useLocalSearchParams } from 'expo-router';
import { CameraView, useCameraPermissions } from 'expo-camera';

import Styles from './Styles';
import { Button, PaperProvider, Text, Portal, Modal } from 'react-native-paper';
import Header from './Appbar';
import Footer from './BottomNavBar';


function QRScanner() {
    const { user } = useLocalSearchParams()
    const userObj = JSON.parse(user);

    const [permission, requestPermission] = useCameraPermissions();

    const [scanned, setScanned] = useState(false);

    const [inputValue, setInputValue] = useState('');

    //UI
    const [visible, setVisible] = useState(false)
    const hideModal = () => setVisible(false);

    useEffect(() => {
        if (inputValue != '') {
            const ws = new WebSocket('ws://194.152.25.94:8080');

            ws.onopen = () => {
                console.log('WebSocket connection opened');
                const message = {
                    action: 'room_exists',
                    roomCode: inputValue,
                };
                ws.send(JSON.stringify(message));
            };

            ws.onmessage = (e) => {
                const response = JSON.parse(e.data);

                if (response.action === 'room_exists' && response.exists) {
                    ws.close();
                    router.push({
                        pathname: '/moduls/Session_biometric_location',
                        params: {
                            user: JSON.stringify(userObj),
                            data: JSON.stringify(inputValue),
                        },
                    });
                } else {
                    // UI Modal
                    setVisible(true)
                }
            };

            ws.onerror = (e) => {
                console.error('WebSocket error:', e.message);
            };

            ws.onclose = (e) => {
                console.log('WebSocket connection closed:', e.code, e.reason);
            };
        }
    }, [inputValue]);

    if (!permission) {
        // Camera permissions still loading.
        return <View />;
    }

    if (!permission.granted) {
        requestPermission();
        
        return (
            <PaperProvider>
                <Header />
                <View style={Styles.containerPaper}>
                    <Text style={{ textAlign: 'center' }}>Potrebujemo vaše dovoljenje za dostop do kamere</Text>
                    <Button mode="contained" 
                    onPress={requestPermission} 
                    style={styles.buttonStyle}
                    title="grant permission">
                        Dodeli!
                    </Button>
                </View>
                <Footer />
            </PaperProvider>
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
            <Portal>
                <Modal
                    visible={visible}
                    onDismiss={hideModal}
                    contentContainerStyle={Styles.containerStyleModal}
                >
                    <Text style={Styles.fonts_roboto}>Soba ne obstaja!</Text>
                    <Button style={Styles.buttonStyle} mode='contained' onPress={hideModal}>Skrij</Button>
                </Modal>
            </Portal>
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