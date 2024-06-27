import React, { useState } from 'react';
import { View, TextInput, StyleSheet } from 'react-native';
import { useLocalSearchParams, router } from 'expo-router';

import { Button, PaperProvider, Portal, Modal, Text } from 'react-native-paper';
import Header from './Appbar';
import Footer from './BottomNavBar';

import Styles from './Styles';

function Session_join() {
  const { user, tokens } = useLocalSearchParams();
  const tokensObj = JSON.parse(tokens);
  const userObj = JSON.parse(user);

  const [inputValue, setInputValue] = useState('');

  //UI
  const [visible, setVisible] = useState(false)
  const hideModal = () => setVisible(false);

  //Normal on click 
  const handleJoinClick = () => {
    const ws = new WebSocket('ws://194.152.25.94:8080');

    ws.onopen = () => {
      //console.log('WebSocket connection opened');
      const message = {
        action: 'room_exists',
        roomCode: inputValue,
      };
      ws.send(JSON.stringify(message));
    };

    ws.onmessage = (e) => {
      const response = JSON.parse(e.data);
      //console.log('Received response:', response);
      if (response.action === 'room_exists' && response.exists) {
        ws.close();
        //console.log('WebSocket connection manually closed');
        router.push({
          pathname: '/moduls/Session_biometric_location',
          params: {
            user: JSON.stringify(userObj),
            data: JSON.stringify(inputValue),
          },
        });
      } else {
        // UI Modal
        setVisible(true)
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
    <PaperProvider>
      <Portal>
        <Modal
          visible={visible}
          onDismiss={hideModal}
          contentContainerStyle={Styles.containerStyleModal}
        >
          <Text style={Styles.fonts_roboto}>Soba ne obstaja!</Text>
          <Button style={Styles.buttonStyle} mode='contained' onPress={hideModal}>Skrij</Button>
        </Modal>
      </Portal>
      <Header />
      <View style={Styles.containerPaper}>
        <Text style={styles.fontText}
        >Dobrodošli, {userObj.name}</Text>

        <TextInput
          style={styles.inputField}
          onChangeText={setInputValue}
          value={inputValue}
          placeholder="Vnesite kodo za prisotnost"
        />
        <Button
          mode="contained"
          onPress={handleJoinClick}
          style={[styles.buttonStyle]} // Adjust width as needed
        >
          Potrdi
        </Button>
        <Button
          mode="contained"
          onPress={handleBarcodeSubpageClick}
          style={[styles.buttonStyle]} // Adjust width as needed
        >
          QR Koda
        </Button>
      </View>
      <Footer />
    </PaperProvider>
  );
}

const styles = StyleSheet.create({
  inputField: {
    height: 40,
    borderColor: 'gray',
    borderWidth: 1,
    marginTop: 10,
    width: 200,
    paddingHorizontal: 10,
    marginBottom: 10,
    borderRadius: 8,
  },
  fontText: {
    fontFamily: 'Roboto',
    marginBottom: 10,
    fontSize: 16,
  },
  buttonStyle: {
    backgroundColor: '#10CEED',
    borderRadius: 8,
    marginVertical: 10,
    width: 110,
  }
});

export default Session_join;
