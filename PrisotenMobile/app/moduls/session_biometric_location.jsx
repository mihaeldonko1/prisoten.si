import React, { useEffect, useState } from 'react';
import { View, Text, TouchableOpacity, Image, Animated } from 'react-native';
import { router } from 'expo-router';
import Styles from './Styles';


function session_biometric_location() {
    const imageSourceFingerprint = require('../../assets/fingerprint.png');
    const imageSourceLocation = require('../../assets/location.png');

    //States for check-ups
    const [fingerprintPressed, setFingerprintPressed] = useState(false);
    const [locationPressed, setLocationPressed] = useState(false);

    //States for button colors
    const [fingerprintButtonColor, setFingerprintButtonColor] = useState(new Animated.Value(0)); 
    const [locationButtonColor, setLocationButtonColor] = useState(new Animated.Value(0)); 


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
    }, [fingerprintPressed, locationPressed]);

    useEffect(() => {
        if (fingerprintPressed) {
           Animated.timing(fingerprintButtonColor, {
            toValue: 1,
            duration: 500,
            useNativeDriver: true,
           }).start(); 
        }
    }, [fingerprintPressed, fingerprintButtonColor]);

    useEffect(() => {
        if (locationPressed) {
           Animated.timing(locationButtonColor, {
            toValue: 1,
            duration: 500,
            useNativeDriver: true,
           }).start(); 
        }
    }, [locationPressed, locationButtonColor]);




    const fingerprintButtonBackgroundColor = fingerprintButtonColor.interpolate({
        inputRange: [0,1],
        outputRange: ['white', '#10f52c'],
    });

    
    const locationButtonBackgroundColor = locationButtonColor.interpolate({
        inputRange: [0,1],
        outputRange: ['white', '#10f52c'],
    });


    return (
        <View style={{ flex: 1, justifyContent: 'center', alignItems: 'center' }}>

            <TouchableOpacity style={[Styles.circulat_button, {backgroundColor: fingerprintButtonBackgroundColor}]} onPress={handleFingreprintPress}>
                <View style={Styles.circular_container}>
                    <Image source={imageSourceFingerprint} style={Styles.image_circular} />
                </View>
            </TouchableOpacity>

            <Text style={Styles.margin_vertical}>Pritisnite za identifikacijo</Text>

            <TouchableOpacity style={[Styles.circulat_button, {backgroundColor: locationButtonBackgroundColor}]} onPress={handleLocationPress}>
                <View style={Styles.circular_container}>
                    <Image source={imageSourceLocation} style={Styles.image_circular} />
                </View>
            </TouchableOpacity>

            <Text style={Styles.margin_vertical}>Pritisnite za lokacijo</Text>

        </View>
    )
}


export default session_biometric_location;