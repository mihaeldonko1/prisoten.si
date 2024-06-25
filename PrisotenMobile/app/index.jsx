import React from 'react';
import { View } from 'react-native';
import OfficeSignIn from './moduls/OfficeSignIn';
import Styles from './moduls/Styles';
import { NativeBaseProvider, Box } from 'native-base';

export default function App() {
  return (
    <NativeBaseProvider>
      <Box>

        <OfficeSignIn />

      </Box>
    </NativeBaseProvider>
  );
}
