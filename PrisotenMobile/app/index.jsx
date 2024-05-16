import React from 'react';
import { View, Text } from 'react-native';
import OfficeSignIn from './moduls/OfficeSignIn';
import Styles from './moduls/Styles';

export default function App() {
  return (
    <View style={Styles.container}>
      <OfficeSignIn />
    </View>
  );
}