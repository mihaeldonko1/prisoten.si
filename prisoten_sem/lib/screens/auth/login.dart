import 'package:aad_oauth/aad_oauth.dart';
import 'package:aad_oauth/model/config.dart';
import 'package:aad_oauth/model/failure.dart';
import 'package:aad_oauth/model/token.dart';
import 'package:flutter/cupertino.dart';
import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;
import 'package:prisoten_sem/screens/auth/UserModel.dart';
import 'package:prisoten_sem/screens/errors/loginError.dart';
import 'package:prisoten_sem/screens/home/home_screen_teacher.dart';
import 'dart:convert';
import 'package:prisoten_sem/screens/home/home_screen_user.dart';
import 'package:provider/provider.dart';


final navigatorKey = GlobalKey<NavigatorState>();

class LoginScreen extends StatefulWidget {
  const LoginScreen({Key? key}) : super(key: key);

  @override
  _LoginScreenState createState() => _LoginScreenState();
}

class _LoginScreenState extends State<LoginScreen> {
  final _microsoftSignIn = AadOAuth(Config(
    tenant: "common",
    clientId: "ba5f1ab0-1f35-4c7f-8c0d-592f4fdab989",
    responseType: "code",
    scope: "User.Read",
    redirectUri: "http://localhost:49710/.auth/login/aad/callback",
    loader: const Center(child: CircularProgressIndicator()),
    navigatorKey: navigatorKey,
  ));

  bool _isLoggedIn = false;
  bool _isTeacher = false;
  bool _isCorrectMailType = false;

  _loginWithMicrosoft() async {
    var result = await _microsoftSignIn.login();

    result.fold(
      (Failure failure) {
        print("failedd");
        setState(() {
          _isLoggedIn = false; // Update login status
        });
      },
      (Token token) async {
        if (token.accessToken == null) {
          return;
        }
        print('Logged in successfully, your access token: ');

        final userData = await getUserData(token.accessToken!);
        Provider.of<UserModel>(context, listen: false).setUser(userData, token.accessToken!);

        var mail = userData['mail'];
        var domain = mail?.split('@').last;
        //Gmail je namesto profesorjevih accountov ker verjetno do njih nebo dostopa
        if(domain == "student.um.si" || domain == "gmail"){
          setState(() {
          _isCorrectMailType = true;
          });
          if(domain == "gmail"){
            setState(() {
              _isTeacher = true;
            });
          }else{
            setState(() {
              _isTeacher = false;
            });
          }
        }
        setState(() {
          _isLoggedIn = true;
        });


      },
    );
  }

Future<Map<String, dynamic>> getUserData(String accessToken) async {
  final response = await http.get(
    Uri.parse('https://graph.microsoft.com/v1.0/me'),
    headers: {
      'Authorization': 'Bearer $accessToken',
      'Accept': 'application/json',
    },
  );

  if (response.statusCode == 200) {
    final Map<String, dynamic> userData = json.decode(response.body);

    return userData;
  } else {
    throw Exception('Failed to load user information');
  }
}


  @override
Widget build(BuildContext context) {
  return Center(
    child: _isLoggedIn
            ? HomeScreenUser() // Pass as named parameters
            : ElevatedButton(
                onPressed: () => _loginWithMicrosoft(),
                child: const Text('Log in with Microsoft'),
              ),  // Navigate to login only if not already there
  );
}


}

