import React, { useState } from 'react';
import { View, Text, StyleSheet, Button } from 'react-native';
import OfficeSignIn from './moduls/OfficeSignIn';
import Styles from './moduls/Styles';


import { router } from 'expo-router';



export default function App() {
  

  const handleBarcodeSubpageClick = () => {
    router.push({
      pathname: '/moduls/QRScanner',
    });
  };

  return (
    <View style={Styles.mainContainer}>
      <OfficeSignIn />
      <Button
        title="QR KODA"
        onPress={handleBarcodeSubpageClick}
        style={Styles.margin_vertical}
      />
    </View>
  );
}
