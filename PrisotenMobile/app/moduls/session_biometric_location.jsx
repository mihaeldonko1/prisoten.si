import React, { useState } from 'react';
import { View, Text, TouchableOpacity, Image } from 'react-native';
import Styles from './Styles';



function session_biometric_location() {
    const imageSourceFingerprint = require('../../assets/fingerprint.png');
    const imageSourceLocation = require('../../assets/location.png');

    return (
        <View style={{ flex: 1, justifyContent: 'center', alignItems: 'center' }}>

            <TouchableOpacity style={Styles.circulat_button}>
                <View style={Styles.circular_container}>
                    <Image  source={imageSourceFingerprint} style={Styles.image_circular}/>
                </View>
            </TouchableOpacity>

            <Text style={Styles.margin_vertical}>Pritisnite za identifikacijo</Text>

            <TouchableOpacity style={Styles.circulat_button}>
            <View style={Styles.circular_container}>
                    <Image  source={imageSourceLocation} style={Styles.image_circular}/>
                </View>
            </TouchableOpacity>

            <Text style={Styles.margin_vertical}>Pritisnite za lokacijo</Text>

        </View>
    )
}


export default session_biometric_location;