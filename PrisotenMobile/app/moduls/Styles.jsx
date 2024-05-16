import { StyleSheet } from 'react-native';

export default StyleSheet.create({
  container: {
    justifyContent: 'center',
    alignItems: 'center',
    flex: 1,
  },
  errorText: {
    marginTop: 10,
    color: 'red',
  },
  circulat_button: {
    width: 100,
    height: 100,
    borderRadius: 50, // Half of width and height to make it circular
    justifyContent: 'center',
    alignItems: 'center',

    borderWidth: 2,
    borderColor: 'black',
    padding: 2,

  },
  circular_container: {
    width: 100,
    height: 100,
    borderRadius: 50,
    justifyContent: 'center',
    alignItems: 'center',
    overflow: 'hidden',
  },
  image_circular: {
    width: '80%',
    height: '80%',
  },
  text_in_button: {
    color: 'white',
  },
  margin_vertical: {
    marginVertical: 10,
  },
});