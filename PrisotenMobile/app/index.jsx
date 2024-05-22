import React from 'react';
import { View, Button } from 'react-native';
import OfficeSignIn from './moduls/OfficeSignIn';
import Styles from './moduls/Styles';


import { router } from 'expo-router'; //Testing



export default function App() {
  

  const handleBarcodeSubpageClick = () => {   //Testing
    router.push({
      pathname: '/moduls/QRScanner',
    });
  };

  return (
    <View style={Styles.mainContainer}>
      <OfficeSignIn />
      <Button //TTi button je treba izbrisati.. Tukaj zaradi olajšave testiranja
        title="QR KODA"
        onPress={handleBarcodeSubpageClick}
        style={Styles.margin_vertical}
      />
    </View>
  );
}
