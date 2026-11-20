import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../core/theme/app_theme.dart';
import '../providers/auth_provider.dart';
import '../screens/violation_records_screen.dart';
import 'rule_lookup_sheet.dart';

class AppDrawer extends StatelessWidget {
  final ValueChanged<int>? onSelectTab;

  const AppDrawer({super.key, this.onSelectTab});

  @override
  Widget build(BuildContext context) {
    final auth = context.watch<AuthProvider>();
    final user = auth.user;

    return Drawer(
      backgroundColor: AppTheme.surface,
      surfaceTintColor: Colors.transparent,
      elevation: 0,
      shape: const RoundedRectangleBorder(
        borderRadius: BorderRadius.horizontal(right: Radius.zero),
      ),
      child: Column(
        children: [
          // Header
          Container(
            width: double.infinity,
            padding: EdgeInsets.only(
              top: MediaQuery.of(context).padding.top + 20,
              bottom: 16,
              left: 20,
              right: 20,
            ),
            decoration: const BoxDecoration(
              color: AppTheme.surface,
              border: Border(bottom: BorderSide(color: AppTheme.border, width: 1)),
            ),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Image.asset(
                      'assets/images/logo-bkprima.png',
                      height: 30,
                      fit: BoxFit.contain,
                    ),
                    Container(
                      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                      decoration: BoxDecoration(
                        color: AppTheme.successLight,
                        borderRadius: BorderRadius.circular(6),
                        border: Border.all(color: AppTheme.successBorder, width: 1),
                      ),
                      child: const Text(
                        'Sem. Genap 2026/2027',
                        style: TextStyle(
                          fontFamily: AppTheme.fontFamily,
                          fontSize: 10,
                          fontWeight: FontWeight.w600,
                          color: AppTheme.successDark,
                        ),
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 16),
                Text(
                  user?.name ?? 'Guru / Guru BK',
                  style: const TextStyle(
                    fontFamily: AppTheme.fontFamily,
                    fontWeight: FontWeight.w700,
                    fontSize: 15,
                    color: AppTheme.textPrimary,
                    letterSpacing: -0.3,
                  ),
                ),
                const SizedBox(height: 2),
                Text(
                  user?.jabatan ?? 'Guru BK / Kedisiplinan',
                  style: const TextStyle(
                    fontFamily: AppTheme.fontFamily,
                    fontSize: 12,
                    fontWeight: FontWeight.w500,
                    color: AppTheme.textSecondary,
                  ),
                ),
              ],
            ),
          ),

          // Menu List
          Expanded(
            child: ListView(
              padding: const EdgeInsets.symmetric(vertical: 8),
              children: [
                _buildDrawerItem(
                  icon: Icons.home_outlined,
                  title: 'Beranda Presensi & Piket',
                  onTap: () {
                    Navigator.pop(context);
                    onSelectTab?.call(0);
                  },
                ),
                _buildDrawerItem(
                  icon: Icons.add_circle_outline_rounded,
                  title: 'Input Pelanggaran Baru',
                  onTap: () {
                    Navigator.pop(context);
                    onSelectTab?.call(1);
                  },
                ),
                _buildDrawerItem(
                  icon: Icons.assignment_outlined,
                  title: 'Catatan Pelanggaran & SP',
                  onTap: () {
                    Navigator.pop(context);
                    Navigator.push(
                      context,
                      MaterialPageRoute(builder: (_) => const ViolationRecordsScreen()),
                    );
                  },
                ),
                _buildDrawerItem(
                  icon: Icons.menu_book_outlined,
                  title: 'Buku Saku Tata Tertib & Poin',
                  onTap: () {
                    Navigator.pop(context);
                    RuleLookupSheet.show(context);
                  },
                ),
                _buildDrawerItem(
                  icon: Icons.person_outline_rounded,
                  title: 'Profil Guru BK',
                  onTap: () {
                    Navigator.pop(context);
                    onSelectTab?.call(2);
                  },
                ),
              ],
            ),
          ),

          // Footer
          const Divider(height: 1, color: AppTheme.border),
          Material(
            color: Colors.transparent,
            child: InkWell(
              onTap: () async {
                Navigator.pop(context);
                await auth.logout();
              },
              child: const Padding(
                padding: EdgeInsets.symmetric(horizontal: 16, vertical: 12),
                child: Row(
                  children: [
                    Icon(Icons.logout_rounded, color: AppTheme.danger, size: 20),
                    SizedBox(width: 14),
                    Text(
                      'Keluar Akun',
                      style: TextStyle(
                        fontFamily: AppTheme.fontFamily,
                        color: AppTheme.danger,
                        fontWeight: FontWeight.w600,
                        fontSize: 13,
                      ),
                    ),
                  ],
                ),
              ),
            ),
          ),
          SizedBox(height: MediaQuery.of(context).padding.bottom + 8),
        ],
      ),
    );
  }

  Widget _buildDrawerItem({
    required IconData icon,
    required String title,
    required VoidCallback onTap,
  }) {
    return Material(
      color: Colors.transparent,
      child: InkWell(
        onTap: onTap,
        child: Padding(
          padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
          child: Row(
            children: [
              Icon(icon, color: AppTheme.textPrimary, size: 20),
              const SizedBox(width: 14),
              Expanded(
                child: Text(
                  title,
                  style: const TextStyle(
                    fontFamily: AppTheme.fontFamily,
                    color: AppTheme.textPrimary,
                    fontSize: 13,
                    fontWeight: FontWeight.w500,
                  ),
                ),
              ),
              const Icon(Icons.chevron_right_rounded, size: 18, color: AppTheme.textMuted),
            ],
          ),
        ),
      ),
    );
  }
}
