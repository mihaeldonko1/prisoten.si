import React from 'react';
import { View } from 'react-native';
import OfficeSignIn from './moduls/OfficeSignIn';
import Styles from './moduls/Styles';
import { PaperProvider } from 'react-native-paper';
import Header from './moduls/Appbar';
import Footer from './moduls/BottomNavBar';

export default function App() {
  return (
    <PaperProvider>
      <Header />
      <OfficeSignIn />
      <Footer />
    </PaperProvider>
  );
}
