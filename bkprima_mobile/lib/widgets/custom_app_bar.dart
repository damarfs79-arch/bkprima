import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../core/theme/app_theme.dart';
import '../providers/auth_provider.dart';
import '../providers/attendance_provider.dart';
import 'absence_alert_sheet.dart';

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
    final attendance = context.watch<AttendanceProvider>();
    final alerts = attendance.absenceAlerts;
    final alertCount = alerts.length;
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
            IconButton(
              tooltip: 'Notifikasi Absensi (>= 3 Hari)',
              onPressed: () => AbsenceAlertDialogs.showListSheet(context, alerts),
              icon: Stack(
                clipBehavior: Clip.none,
                children: [
                  Icon(
                    alertCount > 0
                        ? Icons.notifications_active_outlined
                        : Icons.notifications_outlined,
                    size: 22,
                    color: alertCount > 0 ? AppTheme.danger : AppTheme.textSecondary,
                  ),
                  if (alertCount > 0)
                    Positioned(
                      right: -4,
                      top: -4,
                      child: Container(
                        padding: const EdgeInsets.symmetric(horizontal: 4, vertical: 1),
                        constraints: const BoxConstraints(minWidth: 16, minHeight: 16),
                        decoration: BoxDecoration(
                          color: AppTheme.danger,
                          borderRadius: BorderRadius.circular(8),
                        ),
                        alignment: Alignment.center,
                        child: Text(
                          alertCount > 99 ? '99+' : '$alertCount',
                          style: const TextStyle(
                            fontFamily: AppTheme.fontFamily,
                            color: Colors.white,
                            fontSize: 9,
                            fontWeight: FontWeight.w700,
                          ),
                        ),
                      ),
                    ),
                ],
              ),
            ),
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
