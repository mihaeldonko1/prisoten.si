import mockAsyncStorage from '@react-native-async-storage/async-storage/jest/async-storage-mock';

jest.mock('@react-native-async-storage/async-storage', () => mockAsyncStorage);

jest.mock('react-native-reanimated', () => {
    const Reanimated = require('react-native-reanimated/mock');
    Reanimated.default.call = () => { };
    return Reanimated;
});

jest.mock('react-native/Libraries/Animated/NativeAnimatedHelper');

jest.mock('react-native-gesture-handler', () => {
    return {
        GestureHandlerRootView: jest.fn().mockImplementation(({ children }) => children),
        // Mock other components or methods if needed
    };
});

// Mock native modules that are not used in tests
jest.mock('expo-auth-session', () => ({
    // Provide a mock implementation or an empty object
}));

jest.mock('expo-crypto', () => ({
    // Provide a mock implementation or an empty object
}));

jest.mock('expo-router', () => ({
    router: jest.fn(), // or a dummy implementation
}));

jest.mock('@react-navigation/native', () => ({
    useBackButton: jest.fn(), // Mock specific methods or return values as needed
}));