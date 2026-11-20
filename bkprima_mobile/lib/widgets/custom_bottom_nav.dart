import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import '../core/theme/app_theme.dart';

class NavTabItem {
  final String label;
  final IconData activeIcon;
  final IconData inactiveIcon;

  const NavTabItem({
    required this.label,
    required this.activeIcon,
    required this.inactiveIcon,
  });
}

class CustomBottomNav extends StatelessWidget {
  final int currentIndex;
  final ValueChanged<int> onTap;

  const CustomBottomNav({
    super.key,
    required this.currentIndex,
    required this.onTap,
  });

  static const List<NavTabItem> _tabs = [
    NavTabItem(
      label: 'Presensi',
      activeIcon: Icons.check_circle_rounded,
      inactiveIcon: Icons.check_circle_outline_rounded,
    ),
    NavTabItem(
      label: 'Pelanggaran',
      activeIcon: Icons.assignment_late_rounded,
      inactiveIcon: Icons.assignment_late_outlined,
    ),
    NavTabItem(
      label: 'Statistik',
      activeIcon: Icons.bar_chart_rounded,
      inactiveIcon: Icons.bar_chart_outlined,
    ),
    NavTabItem(
      label: 'Profil',
      activeIcon: Icons.person_rounded,
      inactiveIcon: Icons.person_outline_rounded,
    ),
  ];

  void _handleTap(int index) {
    if (currentIndex != index) {
      HapticFeedback.selectionClick();
      onTap(index);
    }
  }

  @override
  Widget build(BuildContext context) {
    final bottomPadding = MediaQuery.of(context).padding.bottom;

    return Container(
      decoration: const BoxDecoration(
        color: AppTheme.primary,
        borderRadius: BorderRadius.vertical(top: Radius.circular(20)),
        boxShadow: [
          BoxShadow(
            color: Color(0x330F172A),
            blurRadius: 12,
            offset: Offset(0, -3),
          ),
        ],
      ),
      padding: EdgeInsets.only(
        top: 8,
        bottom: bottomPadding > 0 ? bottomPadding : 10,
        left: 12,
        right: 12,
      ),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceAround,
        children: List.generate(_tabs.length, (index) {
          final tab = _tabs[index];
          final isActive = currentIndex == index;

          return Expanded(
            child: InkWell(
              onTap: () => _handleTap(index),
              splashFactory: NoSplash.splashFactory,
              highlightColor: Colors.transparent,
              child: Padding(
                padding: const EdgeInsets.symmetric(vertical: 2),
                child: Column(
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    AnimatedContainer(
                      duration: const Duration(milliseconds: 180),
                      padding: const EdgeInsets.all(6),
                      decoration: BoxDecoration(
                        color: isActive ? Colors.white : Colors.transparent,
                        shape: BoxShape.circle,
                      ),
                      child: Icon(
                        isActive ? tab.activeIcon : tab.inactiveIcon,
                        size: 20,
                        color: isActive ? AppTheme.primary : Colors.white.withValues(alpha: 0.8),
                      ),
                    ),
                    const SizedBox(height: 3),
                    Text(
                      tab.label,
                      style: TextStyle(
                        fontFamily: AppTheme.fontFamily,
                        fontSize: 11,
                        fontWeight: isActive ? FontWeight.w700 : FontWeight.w500,
                        color: isActive ? Colors.white : Colors.white.withValues(alpha: 0.75),
                        letterSpacing: -0.2,
                      ),
                    ),
                  ],
                ),
              ),
            ),
          );
        }),
      ),
    );
  }
}
