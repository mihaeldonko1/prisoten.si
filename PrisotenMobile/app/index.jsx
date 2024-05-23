import React from 'react';
import { View } from 'react-native';
import OfficeSignIn from './moduls/OfficeSignIn';
import Styles from './moduls/Styles';


export default function App() {
  return (
    <View style={Styles.mainContainer}>
      <OfficeSignIn />
    </View>
  );
}
