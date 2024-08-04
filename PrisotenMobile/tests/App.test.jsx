// import React from 'react';
// import renderer from 'react-test-renderer';
// import App from '../app/index.jsx'; // Adjust the import path if needed

// describe('<App />', () => {
//   it('renders correctly', () => {
//     const tree = renderer.create(<App />).toJSON();
//     expect(tree).toBeTruthy();
//     expect(tree.children.length).toBe(4);
//   });
// });

// tests/Session_attendance.test.jsx
import React from 'react';
import renderer from 'react-test-renderer';
import Session_attendance from '../app/moduls/Session_attendance';

// Mocking Header and Footer components
jest.mock('../app/moduls/Appbar', () => () => <></>);
jest.mock('../app/moduls/BottomNavBar', () => () => <></>);

describe('<Session_attendance />', () => {
  it('renders correctly', () => {
    const tree = renderer.create(<Session_attendance />).toJSON();
    expect(tree).toMatchSnapshot();
  });
});

