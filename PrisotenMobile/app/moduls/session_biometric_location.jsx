import React, { useEffect, useState } from 'react';
import { View, Text, TouchableOpacity, Image, Animated } from 'react-native';
import { router } from 'expo-router';
import Styles from './Styles';
import { getLocation } from './Location';
//import { Biometrics } from './Biometrics';
import { authenticateAsync } from 'expo-local-authentication';

function session_biometric_location() {
    const imageSourceFingerprint = require('../../assets/fingerprint.png');
    const imageSourceLocation = require('../../assets/location.png');

    //States for check-ups
    const [fingerprintPressed, setFingerprintPressed] = useState(false);
    const [locationPressed, setLocationPressed] = useState(false);

    //States for button colors
    const [fingerprintButtonColor, setFingerprintButtonColor] = useState(new Animated.Value(0));
    const [locationButtonColor, setLocationButtonColor] = useState(new Animated.Value(0));


    //Biometrija
    let bio = false
    const handleFingreprintPress = () => {
        console.log('Fingerprint pressed!');
        bio = Biometrics()

    };

    async function Biometrics() {               //Funkcija za preverjanje biometrije začasno na tej lokaciji

        const res = await authenticateAsync();

        console.log(res.success);

        
        if (res.success) {
            setFingerprintPressed(true);        
        }


    }

    //Lokacija
    const handleLocationPress = () => {
        console.log('Location pressed!');

        if (!locationPressed) {
            const loc = handleLocation() //Objekt s pridobljeno lokacijo
            console.log(`---------Loc: ${JSON.stringify(loc)}`);
            check(loc);
        }

    };
    // setLocationPressed(true); //Vstavljena mora biti funkcija ki preverja locakijo

    const check = (ch) => {         //Potrebni popravki
        if (ch == null) {
            setLocationPressed(false);
            console.log('False');
        } else {
            setLocationPressed(true);
            console.log('True');
        }
    }

    async function handleLocation() {
        const location = await getLocation();
        console.log('Location:', location);
        const loc = JSON.stringify(location)
        alert(`Lokacija je ${loc}`)
        return loc
    }

    //Reroute na stran sprejema lokacije
    useEffect(() => {
        if (fingerprintPressed && locationPressed) {
            router.push({
                pathname: '/moduls/session_attendance',
            });
        }
    }, [fingerprintPressed, locationPressed]);


    //Animacija
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


export default session_biometric_location;