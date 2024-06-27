import React from "react";
import { View } from 'react-native';
import { PaperProvider, Text } from 'react-native-paper';
import Styles from "./Styles";
import Header from "./Appbar";
import Footer from "./BottomNavBar";

function Session_attendance() {

    return (
        <PaperProvider>
            <Header />
            <View style={Styles.containerPaper}>
                <Text style={[Styles.fonts_roboto, {fontSize: 24}]}>Vaša prisotnost je potrjena</Text>
            </View>
            <Footer />
        </PaperProvider>
    )
}

export default Session_attendance;
