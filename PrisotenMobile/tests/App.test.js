import React from 'react';
import renderer from 'react-test-renderer';
import App from '../app/index.jsx';

// Basic test to ensure the component renders
describe('<App />', () => {
  it('renders correctly', () => {
    const tree = renderer.create(<App />).toJSON();
    expect(tree).toMatchSnapshot(); // Optionally use snapshot testing
  });
});

// import React from 'react';
// import { render } from '@testing-library/react-native';
// import App from '../app/index.jsx';

// // Mock react-native-paper
// jest.mock('react-native-paper', () => {
//     const React = require('react');
//     return {
//       PaperProvider: ({ children }) => <>{children}</>,
//       // Add other components if needed
//     };
//   });

// // Mock components with simple React components
// jest.mock('../app/moduls/OfficeSignIn.jsx', () => () => <>{'OfficeSignIn'}</>);
// jest.mock('../app/moduls/Appbar.jsx', () => () => <>{'Header'}</>);
// jest.mock('../app/moduls/BottomNavBar.jsx', () => () => <>{'Footer'}</>);

// // Mock react-native-paper if needed
// jest.mock('react-native-paper', () => {
//   const React = require('react');
//   return {
//     PaperProvider: ({ children }) => <>{children}</>,
//     // Mock other Paper components if used
//   };
// });

// test('renders correctly', () => {
//   const { getByText } = render(<App />);

//   // Check if components are rendered
//   expect(getByText('OfficeSignIn')).toBeTruthy();
//   expect(getByText('Header')).toBeTruthy();
//   expect(getByText('Footer')).toBeTruthy();
// });
