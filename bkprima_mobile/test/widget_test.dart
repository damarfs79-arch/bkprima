import 'package:flutter_test/flutter_test.dart';
import 'package:bkprima_mobile/screens/login_screen.dart';
import 'package:bkprima_mobile/theme/app_theme.dart';
import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:bkprima_mobile/providers/auth_provider.dart';

void main() {
  testWidgets('Login screen smoke test', (WidgetTester tester) async {
    await tester.pumpWidget(
      ChangeNotifierProvider(
        create: (_) => AuthProvider(),
        child: MaterialApp(
          theme: AppTheme.lightTheme,
          home: const LoginScreen(),
        ),
      ),
    );

    expect(find.text('Masuk Akun Guru BK'), findsOneWidget);
    expect(find.text('Alamat Email Guru BK'), findsOneWidget);
    expect(find.text('Kata Sandi'), findsOneWidget);
  });
}
