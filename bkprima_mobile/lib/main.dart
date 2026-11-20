import 'package:flutter/material.dart';
import 'package:intl/date_symbol_data_local.dart';
import 'package:provider/provider.dart';

import 'theme/app_theme.dart';
import 'providers/auth_provider.dart';
import 'providers/attendance_provider.dart';
import 'providers/violation_provider.dart';
import 'providers/profile_provider.dart';
import 'screens/splash_screen.dart';

void main() async {
  WidgetsFlutterBinding.ensureInitialized();
  await initializeDateFormatting('id_ID', null);

  runApp(
    MultiProvider(
      providers: [
        ChangeNotifierProvider(create: (_) => AuthProvider()),
        ChangeNotifierProvider(create: (_) => AttendanceProvider()),
        ChangeNotifierProvider(create: (_) => ViolationProvider()),
        ChangeNotifierProvider(create: (_) => ProfileProvider()),
      ],
      child: const BkPrimaApp(),
    ),
  );
}

class BkPrimaApp extends StatelessWidget {
  const BkPrimaApp({super.key});

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      title: 'BK Prima Mobile',
      debugShowCheckedModeBanner: false,
      theme: AppTheme.lightTheme,
      home: const SplashScreen(),
    );
  }
}
