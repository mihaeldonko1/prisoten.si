import * as React from 'react';
import { StyleSheet } from 'react-native';
import { Appbar, FAB, useTheme } from 'react-native-paper';
import { useSafeAreaInsets } from 'react-native-safe-area-context';

const BOTTOM_APPBAR_HEIGHT = 64;

const MyComponent = () => {
  const { bottom } = useSafeAreaInsets();
  const theme = useTheme();

  return (
    <Appbar
      style={[
        styles.bottom,
        {
          height: BOTTOM_APPBAR_HEIGHT + bottom,
        },
      ]}
    >
      
      
    </Appbar>
  );
};

const styles = StyleSheet.create({
  bottom: {
    backgroundColor: 'white',
    left: 0,
    right: 0,
    bottom: 0,
    borderTopWidth: 1,
    borderTopColor: '#CCCCCC'
  },
});

export default MyComponent;