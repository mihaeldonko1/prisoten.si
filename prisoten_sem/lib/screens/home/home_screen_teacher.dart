import 'package:flutter/material.dart';
import 'package:prisoten_sem/screens/auth/UserModel.dart';
import 'package:provider/provider.dart';
import '../../providers/theme_provider.dart'; 

class HomeScreenUserTeacher extends StatelessWidget {

  @override
  Widget build(BuildContext context) {
    final themeProvider = Provider.of<ThemeProvider>(context, listen: true);
    final userModel = Provider.of<UserModel>(context, listen: false);
    print(userModel.domain);

    return Scaffold(
      appBar: AppBar(
        title: const Text('Home Screen'),
        actions: [
          IconButton(
            icon: const Icon(Icons.brightness_6),
            onPressed: () {
              themeProvider.setTheme(
                themeProvider.themeData.brightness == Brightness.dark
                  ? MyThemes.lightTheme
                  : MyThemes.darkTheme
              );
            },
          ),
        ],
      ),
      body: Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            const Text('Dobrodošli učitelj', style: TextStyle(fontSize: 24)),
            Text('Mail: ${userModel.mail}', style: const TextStyle(fontSize: 18)),
          ],
        ),
      ),
      floatingActionButton: FloatingActionButton(
        onPressed: () => navigateToSettings(context),
        tooltip: 'Settings',
        child: const Icon(Icons.settings),
      ),
    );
  }
}


navigateToSettings(BuildContext context) {
  Navigator.of(context).pushNamed('/settings');
}

