import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import '../theme/app_theme.dart';

class ChipItem<T> {
  final String label;
  final T value;
  final String? subtitle;

  const ChipItem({
    required this.label,
    required this.value,
    this.subtitle,
  });
}

class SelectableChipWrap<T> extends StatelessWidget {
  final List<ChipItem<T>> items;
  final T? selectedValue;
  final ValueChanged<T> onSelected;
  final double spacing;
  final double runSpacing;

  const SelectableChipWrap({
    super.key,
    required this.items,
    required this.selectedValue,
    required this.onSelected,
    this.spacing = 8.0,
    this.runSpacing = 8.0,
  });

  @override
  Widget build(BuildContext context) {
    return Wrap(
      spacing: spacing,
      runSpacing: runSpacing,
      children: items.map((item) {
        final isSelected = item.value == selectedValue;
        return GestureDetector(
          onTap: () {
            HapticFeedback.selectionClick();
            onSelected(item.value);
          },
          child: AnimatedContainer(
            duration: const Duration(milliseconds: 150),
            curve: Curves.easeOut,
            padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
            decoration: BoxDecoration(
              color: isSelected ? AppTheme.primaryLight : AppTheme.surface,
              borderRadius: BorderRadius.circular(8),
              border: Border.all(
                color: isSelected ? AppTheme.primaryFocus : AppTheme.border,
                width: isSelected ? 1.5 : 1.0,
              ),
            ),
            child: Row(
              mainAxisSize: MainAxisSize.min,
              children: [
                Text(
                  item.label,
                  style: TextStyle(
                    fontFamily: AppTheme.fontFamily,
                    fontSize: 12,
                    fontWeight: isSelected ? FontWeight.w600 : FontWeight.w500,
                    color: isSelected ? AppTheme.primary : AppTheme.textSecondary,
                  ),
                ),
                if (item.subtitle != null) ...[
                  const SizedBox(width: 4),
                  Text(
                    item.subtitle!,
                    style: TextStyle(
                      fontFamily: AppTheme.fontFamily,
                      fontSize: 10,
                      fontWeight: FontWeight.w400,
                      color: isSelected ? AppTheme.primaryFocus : AppTheme.textMuted,
                    ),
                  ),
                ],
              ],
            ),
          ),
        );
      }).toList(growable: false),
    );
  }
}
