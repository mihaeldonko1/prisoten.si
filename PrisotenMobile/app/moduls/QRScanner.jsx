import React, { useState } from 'react';
import { View, Text, StyleSheet, TouchableOpacity } from 'react-native';

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
        alert(`Poskenirano: ${data}`);
        console.log(data);
    }

    return (
        <View style={Styles.cameraContainer}>
            <CameraView style={Styles.camera}
                onBarcodeScanned={scanned ? undefined : handleBarCodeScanned}
                barcodeScannerSettings={{
                    barcodeTypes: ['qr', 'aztec']
                }}


            />
        </View>
    )
}





export default QRScanner;