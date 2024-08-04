// __mocks__/react-native-paper.js
import React from 'react';
import { View, Text } from 'react-native';

// Mocked components
const MockAppbar = {
  Header: ({ children }) => <View>{children}</View>,
  Content: ({ title, titleStyle }) => <Text style={titleStyle}>{title}</Text>
};

export const Appbar = MockAppbar;
export const Button = ({ children }) => <View>{children}</View>;
export const TextInput = ({ ...props }) => <View {...props} />;
export const Dialog = {
  Title: ({ children }) => <Text>{children}</Text>
};
export const Portal = { Provider: ({ children }) => <>{children}</> };
export const Snackbar = ({ children }) => <View>{children}</View>;
export const Provider = ({ children }) => <>{children}</>;