import 'package:flutter/material.dart';

class UserModel extends ChangeNotifier {
  String? mail;
  String? name;
  String? surname;
  String? fullname;
  String? phone;
  String? authToken;
  String? mailerID;
  String? title;
  String? domain;

  void setUser(Map<String, dynamic> userData, String accessToken) {
    mail = userData['mail'];
    domain = mail?.split('@').last;
    name = userData['name'];
    surname = userData['surname'];
    fullname = userData['displayName'];
    mailerID = userData['id'];
    phone = userData['mobilePhone'];
    title = userData['jobTitle'];
    authToken = accessToken;

    notifyListeners();  // Notify listeners about data changes
  }
}
