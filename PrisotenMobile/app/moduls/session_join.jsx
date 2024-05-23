import React, { useState } from 'react';
import { View, Text, TextInput, Button } from 'react-native';
import { useLocalSearchParams, useRouter } from 'expo-router';
import { router } from 'expo-router';

import Styles from './Styles';

function Session_join() {
  const { user, tokens } = useLocalSearchParams();
  const tokensObj = JSON.parse(tokens);
  const userObj = JSON.parse(user);
  
  const [inputValue, setInputValue] = useState('');


  //Normal on click 
  const handleJoinClick = () => {
    //TODO join v sejo, ki bo napisana v Laravelu
    console.log(inputValue);
    console.log("asdasd");
    const TestingTrue = true;
    if (TestingTrue) {
      router.push({
        pathname: '/moduls/Session_biometric_location',
        params: {
          user: JSON.stringify(userObj),
          data: JSON.stringify(inputValue),
        },
      });
    };

  };


  //Barcode scanner
  const handleBarcodeSubpageClick = () => {
    
    router.push({
      pathname: '/moduls/QRScanner',
      params: {
        user: JSON.stringify(userObj),
      },
    });
  };

  return (
    <View style={{ flex: 1, justifyContent: 'center', alignItems: 'center' }}>
      <Text style={Styles.margin_vertical}>Dobrodošli, {userObj.name}</Text>
      <TextInput
        style={{ height: 40, borderColor: 'gray', borderWidth: 1, marginTop: 20, width: 200, paddingHorizontal: 10 }}
        onChangeText={setInputValue}
        value={inputValue}
        placeholder="Vnesite kodo za prisotnost"
      />
      <Button
        title="Potrdi"
        onPress={handleJoinClick} 
        style={Styles.margin_vertical}
      />
      <Button
        title="QR KODA"
        onPress={handleBarcodeSubpageClick}
        style={Styles.margin_vertical}
      />
    </View>
  );
}

export default Session_join;
