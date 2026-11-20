import 'dart:math' as math;
import 'package:flutter/material.dart';
import '../core/theme/app_theme.dart';

class PieSliceData {
  final String id;
  final String label;
  final double rate;
  final int count;
  final Color color;

  const PieSliceData({
    required this.id,
    required this.label,
    required this.rate,
    required this.count,
    required this.color,
  });

  factory PieSliceData.fromJson(Map<String, dynamic> json) {
    Color parseColor(String? hex) {
      if (hex == null || !hex.startsWith('#')) return AppTheme.primary;
      final clean = hex.replaceAll('#', '');
      return Color(int.parse('FF$clean', radix: 16));
    }

    return PieSliceData(
      id: json['id']?.toString() ?? '',
      label: json['label']?.toString() ?? '',
      rate: (json['rate'] as num?)?.toDouble() ?? 0.0,
      count: (json['count'] as num?)?.toInt() ?? 0,
      color: parseColor(json['color']?.toString()),
    );
  }
}

class AttendancePieChart extends StatelessWidget {
  final List<PieSliceData> slices;
  final double centerRate;
  final String centerLabel;

  const AttendancePieChart({
    super.key,
    required this.slices,
    this.centerRate = 95.8,
    this.centerLabel = 'Tingkat Hadir',
  });

  @override
  Widget build(BuildContext context) {
    return SizedBox(
      height: 190,
      width: double.infinity,
      child: Row(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          // Donut/Pie Chart Canvas
          SizedBox(
            width: 170,
            height: 170,
            child: Stack(
              alignment: Alignment.center,
              children: [
                CustomPaint(
                  size: const Size(170, 170),
                  painter: _PieChartPainter(slices: slices),
                ),
                // Center Stats
                Column(
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    Text(
                      '${centerRate.toStringAsFixed(1)}%',
                      style: const TextStyle(
                        fontFamily: AppTheme.fontFamily,
                        fontSize: 22,
                        fontWeight: FontWeight.w800,
                        color: Color(0xFF0F172A),
                        letterSpacing: -0.5,
                      ),
                    ),
                    Text(
                      centerLabel,
                      style: const TextStyle(
                        fontFamily: AppTheme.fontFamily,
                        fontSize: 10,
                        fontWeight: FontWeight.w600,
                        color: Color(0xFF64748B),
                      ),
                    ),
                  ],
                ),
              ],
            ),
          ),
          const SizedBox(width: 20),

          // Side Legend with Count & Percentage
          Expanded(
            child: Column(
              mainAxisAlignment: MainAxisAlignment.center,
              crossAxisAlignment: CrossAxisAlignment.start,
              children: slices.map((slice) {
                return Padding(
                  padding: const EdgeInsets.symmetric(vertical: 3.5),
                  child: Row(
                    children: [
                      Container(
                        width: 10,
                        height: 10,
                        decoration: BoxDecoration(
                          color: slice.color,
                          borderRadius: BorderRadius.circular(3),
                        ),
                      ),
                      const SizedBox(width: 8),
                      Expanded(
                        child: Text(
                          slice.label,
                          style: const TextStyle(
                            fontFamily: AppTheme.fontFamily,
                            fontSize: 12,
                            fontWeight: FontWeight.w600,
                            color: Color(0xFF334155),
                          ),
                        ),
                      ),
                      Text(
                        '${slice.rate.toStringAsFixed(1)}%',
                        style: TextStyle(
                          fontFamily: AppTheme.fontFamily,
                          fontSize: 12,
                          fontWeight: FontWeight.w700,
                          color: slice.color,
                        ),
                      ),
                    ],
                  ),
                );
              }).toList(),
            ),
          ),
        ],
      ),
    );
  }
}

class _PieChartPainter extends CustomPainter {
  final List<PieSliceData> slices;

  _PieChartPainter({required this.slices});

  @override
  void paint(Canvas canvas, Size size) {
    if (slices.isEmpty) return;

    final center = Offset(size.width / 2, size.height / 2);
    final outerRadius = size.width / 2;
    const strokeWidth = 24.0;
    final innerRadius = outerRadius - strokeWidth;

    double currentAngle = -math.pi / 2; // Mulai dari jam 12

    for (final slice in slices) {
      final sweepAngle = (slice.rate / 100) * 2 * math.pi;

      final paint = Paint()
        ..color = slice.color
        ..style = PaintingStyle.stroke
        ..strokeWidth = strokeWidth
        ..strokeCap = StrokeCap.butt;

      final rect = Rect.fromCircle(
        center: center,
        radius: (outerRadius + innerRadius) / 2,
      );

      canvas.drawArc(rect, currentAngle, sweepAngle, false, paint);
      currentAngle += sweepAngle;
    }

    // Hairline outer & inner borders
    final borderPaint = Paint()
      ..color = const Color(0xFFE2E8F0)
      ..style = PaintingStyle.stroke
      ..strokeWidth = 1;

    canvas.drawCircle(center, outerRadius, borderPaint);
    canvas.drawCircle(center, innerRadius, borderPaint);
  }

  @override
  bool shouldRepaint(covariant _PieChartPainter oldDelegate) {
    return oldDelegate.slices != slices;
  }
}
