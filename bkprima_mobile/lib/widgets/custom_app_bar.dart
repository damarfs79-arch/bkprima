import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../core/theme/app_theme.dart';
import '../providers/auth_provider.dart';

class CustomAppBar extends StatelessWidget implements PreferredSizeWidget {
  final String? title;
  final List<Widget>? actions;

  const CustomAppBar({
    super.key,
    this.title,
    this.actions,
  });

  @override
  Size get preferredSize => const Size.fromHeight(56);

  @override
  Widget build(BuildContext context) {
    final auth = context.watch<AuthProvider>();
    final user = auth.user;
    final initial = user != null && user.name.isNotEmpty
        ? user.name.trim().split(' ').map((e) => e[0]).take(2).join('').toUpperCase()
        : 'DH';

    return AppBar(
      backgroundColor: AppTheme.surface,
      elevation: 0,
      scrolledUnderElevation: 0,
      automaticallyImplyLeading: false,
      titleSpacing: 16,
      shape: const Border(
        bottom: BorderSide(color: AppTheme.border, width: 1),
      ),
      title: title != null
          ? Text(
              title!,
              style: const TextStyle(
                fontFamily: AppTheme.fontFamily,
                color: AppTheme.textPrimary,
                fontWeight: FontWeight.w700,
                fontSize: 16,
                letterSpacing: -0.3,
              ),
            )
          : Image.asset(
              'assets/images/logo-bkprima.png',
              height: 26,
              fit: BoxFit.contain,
              alignment: Alignment.centerLeft,
            ),
      actions: actions ??
          [
            Padding(
              padding: const EdgeInsets.only(right: 16),
              child: Container(
                width: 34,
                height: 34,
                decoration: BoxDecoration(
                  color: AppTheme.surfaceMuted,
                  shape: BoxShape.circle,
                  border: Border.all(color: AppTheme.border, width: 1),
                ),
                alignment: Alignment.center,
                clipBehavior: Clip.antiAlias,
                child: user != null && user.avatarUrl != null && user.avatarUrl!.isNotEmpty
                    ? Image.network(
                        user.avatarUrl!,
                        width: 34,
                        height: 34,
                        fit: BoxFit.cover,
                        errorBuilder: (context, error, stackTrace) => _buildInitials(initial),
                      )
                    : _buildInitials(initial),
              ),
            ),
          ],
    );
  }

  Widget _buildInitials(String initial) {
    return Text(
      initial,
      style: const TextStyle(
        fontFamily: AppTheme.fontFamily,
        color: AppTheme.textMedium,
        fontWeight: FontWeight.w700,
        fontSize: 12,
        letterSpacing: -0.2,
      ),
    );
  }
}
