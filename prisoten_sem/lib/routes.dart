import 'package:flutter/material.dart';
import 'package:prisoten_sem/screens/auth/login.dart';
import 'screens/home/home_screen_user.dart'; 
import 'screens/settings/settings.dart';

final Map<String, WidgetBuilder> appRoutes = {
  '/': (context) => LoginScreen(),
  '/settings': (context) => SettingsScreen(), 
};
