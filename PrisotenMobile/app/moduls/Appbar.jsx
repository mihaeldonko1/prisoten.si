import * as React from 'react';
import { Appbar } from 'react-native-paper';
import { View, Image, StyleSheet } from 'react-native';

const Header = () => (
    <Appbar.Header style={styles.appBarBg}>
        <Appbar.Content titleStyle={styles.appBarTittleFont} title="Prisoten.si" />
        <View style={styles.rightContainer}>
            <Image
                source={require('../../assets/locationMarker.png')}
                style={styles.image}
            />
        </View>
    </Appbar.Header>
);

const styles = StyleSheet.create({
    rightContainer: {
        flexDirection: 'row',
        alignItems: 'center',
        marginRight: 10,
    },
    image: {
        width: 40,
        height: 40,
    },
    appBarBg: {
        backgroundColor: 'white',
        height: 64,
        borderBottomWidth: 1,
        borderBottomColor: '#CCCCCC'
    },
    appBarTittleFont: {
        fontFamily: 'Roboto'
    }
});

export default Header;