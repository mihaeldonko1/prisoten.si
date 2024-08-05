import { StyleSheet } from 'react-native';

export default StyleSheet.create({
  containerPaper: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
    padding: 16,
    backgroundColor: '#F5F5F5'
  },

  fonts_roboto: {
    fontFamily: 'Roboto'
  },

  buttonStyle: {
    backgroundColor: '#10CEED',
    borderRadius: 8,
    marginTop: 32,
    width: 110,
  },

  containerStyleModal: {
    backgroundColor: 'white',
    padding: 20,
    height: '20%',
    width: '60%',
    justifyContent: 'center',
    alignItems: 'center',
    alignSelf: "center",
    borderRadius: 8
  },

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

  //Barcode scanner
  barcode_scanner: {
    flex: 1,
    flexDirection: "column",
    justifyContent: "center",
  },

  mainContainer: {
    flex: 1,
    height: '10%',
    backgroundColor: '#F5F5F5'
  },
  cameraContainer: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
    //overflow: 'hidden',
  },
  camera: {
    width: '80%', // Adjust as needed
    height: '80%', // Adjust as needed
  },
});