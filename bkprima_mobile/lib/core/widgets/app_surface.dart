import 'package:flutter/material.dart';
import '../theme/app_theme.dart';

class AppSurface extends StatelessWidget {
  final Widget child;
  final EdgeInsetsGeometry padding;
  final EdgeInsetsGeometry margin;
  final VoidCallback? onTap;
  final Color backgroundColor;
  final Color borderColor;
  final double borderRadius;

  const AppSurface({
    super.key,
    required this.child,
    this.padding = const EdgeInsets.all(16),
    this.margin = EdgeInsets.zero,
    this.onTap,
    this.backgroundColor = AppTheme.surface,
    this.borderColor = AppTheme.border,
    this.borderRadius = 12.0,
  });

  @override
  Widget build(BuildContext context) {
    final borderShape = RoundedRectangleBorder(
      borderRadius: BorderRadius.circular(borderRadius),
      side: BorderSide(color: borderColor, width: 1),
    );

    if (onTap != null) {
      return Padding(
        padding: margin,
        child: Material(
          color: backgroundColor,
          shape: borderShape,
          clipBehavior: Clip.antiAlias,
          child: InkWell(
            onTap: onTap,
            child: Padding(
              padding: padding,
              child: child,
            ),
          ),
        ),
      );
    }

    return Padding(
      padding: margin,
      child: Material(
        color: backgroundColor,
        shape: borderShape,
        clipBehavior: Clip.antiAlias,
        child: Padding(
          padding: padding,
          child: child,
        ),
      ),
    );
  }
}
