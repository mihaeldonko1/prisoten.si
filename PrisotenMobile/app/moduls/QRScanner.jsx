import React, { useState } from 'react';
import { View, Text, Button } from 'react-native';
import { router } from 'expo-router';
import { CameraView, useCameraPermissions } from 'expo-camera';

import Styles from './Styles';

function QRScanner() {
    const [permission, requestPermission] = useCameraPermissions();

    const [scanned, setScanned] = useState(false);

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
        //alert(`Poskenirano: ${data}`);

        const TestingTrue = true //Logika ista kot pri session_join da za reroute availability
        if (TestingTrue) {
            router.push({
                pathname: '/moduls/session_biometric_location',
            });
        } else {
            alert('Napaka pri branju QR kode!')
        };
    }

    const handleScannAgain = () => {
        setScanned(false)
    }

    return (

        <View style={Styles.cameraContainer}>
            <CameraView style={Styles.camera}
                onBarcodeScanned={scanned ? undefined : handleBarCodeScanned}
                barcodeScannerSettings={{
                    barcodeTypes: ['qr', 'aztec']
                }}
            />
            <Button
                title="Skeniraj"
                onPress={handleScannAgain}
                style={Styles.margin_vertical}
            />
        </View>

    )
}


export default QRScanner;