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
    const ws = new WebSocket('ws://86.58.51.113:8080');

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
      console.log('Received response:', response);

      if (response.action === 'room_exists' && response.exists) {
        ws.close();
        console.log('WebSocket connection manually closed');
        router.push({
          pathname: '/moduls/Session_biometric_location',
          params: {
            user: JSON.stringify(userObj),
            data: JSON.stringify(inputValue),
          },
        });
      } else {
        console.error('Room does not exist or invalid response');
      }
    };

    ws.onerror = (e) => {
      console.error('WebSocket error:', e.message);
    };

    ws.onclose = (e) => {
      console.log('WebSocket connection closed:', e.code, e.reason);
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
