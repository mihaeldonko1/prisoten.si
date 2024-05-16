import React, { useEffect, useState } from 'react';
import { View, Text, TouchableOpacity, Image } from 'react-native';
import { router } from 'expo-router';
import Styles from './Styles';


function session_biometric_location() {
    const imageSourceFingerprint = require('../../assets/fingerprint.png');
    const imageSourceLocation = require('../../assets/location.png');

    const [fingerprintPressed, setFingerprintPressed] = useState(false);
    const [locationPressed, setLocationPressed] = useState(false);

    const handleFingreprintPress = () => {
        console.log('Fingerprint pressed!');
        setFingerprintPressed(true); //vstavljena mora bit funkcija ki preverja fingerprint/faceid  

    };

    const handleLocationPress = () => {
        console.log('Location pressed!');
        setLocationPressed(true); //vstavljena mora biti funkcija ki preverja locakijo

    };

    useEffect(() => {
        if (fingerprintPressed && locationPressed) {
            router.push({
                pathname: '/moduls/session_attendance',
            });
        }
    }, [fingerprintPressed, locationPressed])



    return (
        <View style={{ flex: 1, justifyContent: 'center', alignItems: 'center' }}>

            <TouchableOpacity style={Styles.circulat_button} onPress={handleFingreprintPress}>
                <View style={Styles.circular_container}>
                    <Image source={imageSourceFingerprint} style={Styles.image_circular} />
                </View>
            </TouchableOpacity>

            <Text style={Styles.margin_vertical}>Pritisnite za identifikacijo</Text>

            <TouchableOpacity style={Styles.circulat_button} onPress={handleLocationPress}>
                <View style={Styles.circular_container}>
                    <Image source={imageSourceLocation} style={Styles.image_circular} />
                </View>
            </TouchableOpacity>

            <Text style={Styles.margin_vertical}>Pritisnite za lokacijo</Text>

        </View>
    )
}


export default session_biometric_location;