import 'package:flutter/material.dart';

class ErrorScreenLogin extends StatelessWidget {
  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text('Error'),
        backgroundColor: Colors.red, // Red app bar to indicate an error
      ),
      body: Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: <Widget>[
            Icon(
              Icons.error_outline,
              size: 120,
              color: Colors.red, // Red icon to emphasize the error
            ),
            Padding(
              padding: const EdgeInsets.all(16.0),
              child: Text(
                'An error occurred!',
                style: TextStyle(
                  fontSize: 24,
                  fontWeight: FontWeight.bold,
                  color: Colors.black,
                ),
              ),
            ),
            Padding(
              padding: const EdgeInsets.all(8.0),
              child: Text(
                'Please try again later or contact support.',
                style: TextStyle(
                  fontSize: 18,
                  color: Colors.black54,
                ),
              ),
            ),
            ElevatedButton(
              onPressed: () => Navigator.of(context).pop(), // Go back to the previous screen
              child: Text('Go Back'),
              style: ElevatedButton.styleFrom(
                backgroundColor: Colors.red, // Red button
              ),
            ),
          ],
        ),
      ),
    );
  }
}
