import 'package:flutter/material.dart';
import 'package:prisoten_sem/screens/auth/login.dart';
import 'package:provider/provider.dart';
import 'package:prisoten_sem/screens/auth/UserModel.dart';
import 'package:prisoten_sem/providers/theme_provider.dart';
import 'package:prisoten_sem/routes.dart';

void main() async {
  WidgetsFlutterBinding.ensureInitialized();
  runApp(MyApp());
}

class MyApp extends StatelessWidget {
  @override
  Widget build(BuildContext context) {
    return MultiProvider(
      providers: [
        ChangeNotifierProvider(create: (_) => ThemeProvider(MyThemes.darkTheme)),
        ChangeNotifierProvider(create: (_) => UserModel()),
      ],
      child: Consumer<ThemeProvider>(  // Use Consumer to listen to ThemeProvider
        builder: (context, themeProvider, child) {
          return MaterialApp(
            title: 'Prisoten.si',
            theme: themeProvider.themeData,  // Apply theme from ThemeProvider
            navigatorKey: navigatorKey,  // Assuming navigatorKey is defined elsewhere
            initialRoute: '/',  // Optionally set an initial route
            routes: appRoutes,  // Make sure 'appRoutes' is properly defined
          );
        },
      ),
    );
  }
}
