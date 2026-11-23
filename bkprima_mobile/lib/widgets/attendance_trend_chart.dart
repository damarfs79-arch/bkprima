import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import '../core/theme/app_theme.dart';

class MonthlyTrendPoint {
  final int month;
  final String shortName;
  final String monthName;
  final double val2025;
  final double val2026;
  final String note;

  const MonthlyTrendPoint({
    required this.month,
    required this.shortName,
    required this.monthName,
    required this.val2025,
    required this.val2026,
    required this.note,
  });

  factory MonthlyTrendPoint.fromJson(Map<String, dynamic> json) {
    final rate = (json['rate'] as num?)?.toDouble() ??
        (json['val_2026'] as num?)?.toDouble() ??
        0.0;
    return MonthlyTrendPoint(
      month: json['month'] is int
          ? json['month']
          : int.tryParse(json['month'].toString()) ?? 1,
      shortName: json['short_name']?.toString() ?? 'Bln',
      monthName: json['month_name']?.toString() ?? '',
      val2025: (json['val_2025'] as num?)?.toDouble() ?? rate,
      val2026: rate,
      note: json['note']?.toString() ?? '',
    );
  }
}

class AttendanceTrendChart extends StatefulWidget {
  final List<MonthlyTrendPoint>? data;
  final double targetRate;
  final double avg2026;
  final double avg2025;
  final int? totalStudents;

  const AttendanceTrendChart({
    super.key,
    this.data,
    this.targetRate = 90.0,
    this.avg2026 = 91.3,
    this.avg2025 = 88.2,
    this.totalStudents,
  });

  @override
  State<AttendanceTrendChart> createState() => _AttendanceTrendChartState();
}

class _AttendanceTrendChartState extends State<AttendanceTrendChart> {
  int _selectedMonthIndex = 0;

  @override
  void initState() {
    super.initState();
    if (widget.data != null && widget.data!.isNotEmpty) {
      _selectedMonthIndex = widget.data!.length - 1;
    }
  }

  @override
  void didUpdateWidget(covariant AttendanceTrendChart oldWidget) {
    super.didUpdateWidget(oldWidget);
    if (widget.data != null && widget.data!.isNotEmpty) {
      if (_selectedMonthIndex >= widget.data!.length) {
        _selectedMonthIndex = widget.data!.length - 1;
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    if (widget.data == null || widget.data!.isEmpty) {
      return Container(
        width: double.infinity,
        decoration: BoxDecoration(
          color: AppTheme.surface,
          borderRadius: BorderRadius.circular(12),
          border: Border.all(color: AppTheme.border, width: 1),
        ),
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                const Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      'Tren Kehadiran Siswa',
                      style: TextStyle(
                        fontFamily: AppTheme.fontFamily,
                        fontSize: 13,
                        fontWeight: FontWeight.w700,
                        color: AppTheme.textPrimary,
                        letterSpacing: -0.2,
                      ),
                    ),
                    SizedBox(height: 2),
                    Text(
                      'Rekap Presensi Bulanan',
                      style: TextStyle(
                        fontFamily: AppTheme.fontFamily,
                        fontSize: 11,
                        color: AppTheme.textMuted,
                      ),
                    ),
                  ],
                ),
                Container(
                  padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 3),
                  decoration: BoxDecoration(
                    color: const Color(0xFFF1F5F9),
                    borderRadius: BorderRadius.circular(6),
                    border: Border.all(color: const Color(0xFFE2E8F0)),
                  ),
                  child: const Text(
                    'Belum Ada Data',
                    style: TextStyle(
                      fontFamily: AppTheme.fontFamily,
                      fontSize: 11,
                      fontWeight: FontWeight.w600,
                      color: AppTheme.textMuted,
                    ),
                  ),
                ),
              ],
            ),
            const SizedBox(height: 24),
            Center(
              child: Column(
                children: [
                  Icon(
                    Icons.trending_up,
                    size: 36,
                    color: AppTheme.textMuted.withValues(alpha: 0.5),
                  ),
                  const SizedBox(height: 8),
                  const Text(
                    'Belum ada data rekap kehadiran bulanan.',
                    style: TextStyle(
                      fontFamily: AppTheme.fontFamily,
                      fontSize: 12,
                      fontWeight: FontWeight.w600,
                      color: AppTheme.textSecondary,
                    ),
                    textAlign: TextAlign.center,
                  ),
                  const SizedBox(height: 4),
                  const Text(
                    'Grafik tren akan terisi otomatis setelah presensi siswa tercatat.',
                    style: TextStyle(
                      fontFamily: AppTheme.fontFamily,
                      fontSize: 11,
                      color: AppTheme.textMuted,
                    ),
                    textAlign: TextAlign.center,
                  ),
                ],
              ),
            ),
            const SizedBox(height: 16),
          ],
        ),
      );
    }

    final list = widget.data!;
    final safeIndex = _selectedMonthIndex.clamp(0, list.length - 1);
    final selectedPoint = list[safeIndex];

    final avgRate = list.map((e) => e.val2026).reduce((a, b) => a + b) / list.length;
    MonthlyTrendPoint highestPoint = list.first;
    MonthlyTrendPoint lowestPoint = list.first;
    for (final p in list) {
      if (p.val2026 > highestPoint.val2026) highestPoint = p;
      if (p.val2026 < lowestPoint.val2026) lowestPoint = p;
    }

    final hasComparison = list.any((p) => p.val2025 != p.val2026 && p.val2025 > 0);
    final diff = selectedPoint.val2026 - selectedPoint.val2025;
    final diffText = diff >= 0 ? '+${diff.toStringAsFixed(1)}%' : '${diff.toStringAsFixed(1)}%';

    return Container(
      decoration: BoxDecoration(
        color: AppTheme.surface,
        borderRadius: BorderRadius.circular(12),
        border: Border.all(color: AppTheme.border, width: 1),
      ),
      padding: const EdgeInsets.all(14),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          // Header Judul
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      widget.totalStudents != null && widget.totalStudents! > 0
                          ? 'Tren Kehadiran Siswa (${widget.totalStudents} Siswa)'
                          : 'Tren Kehadiran Siswa',
                      style: const TextStyle(
                        fontFamily: AppTheme.fontFamily,
                        fontSize: 13,
                        fontWeight: FontWeight.w700,
                        color: AppTheme.textPrimary,
                        letterSpacing: -0.2,
                      ),
                    ),
                    const SizedBox(height: 2),
                    Text(
                      hasComparison
                          ? 'Komparasi Data Kehadiran Bulanan'
                          : 'Rekap Kehadiran Berdasarkan Data Presensi',
                      style: const TextStyle(
                        fontFamily: AppTheme.fontFamily,
                        fontSize: 11,
                        color: AppTheme.textMuted,
                      ),
                    ),
                  ],
                ),
              ),
              Container(
                padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 3),
                decoration: BoxDecoration(
                  color: const Color(0xFFF1F5F9),
                  borderRadius: BorderRadius.circular(6),
                  border: Border.all(color: const Color(0xFFE2E8F0)),
                ),
                child: Text(
                  'Avg: ${avgRate.toStringAsFixed(1)}%',
                  style: const TextStyle(
                    fontFamily: AppTheme.fontFamily,
                    fontSize: 11,
                    fontWeight: FontWeight.w700,
                    color: AppTheme.primary,
                  ),
                ),
              ),
            ],
          ),
          const SizedBox(height: 12),

          // Legenda
          Row(
            children: [
              _buildLegendItem(
                color: AppTheme.primary,
                label: 'Kehadiran',
              ),
              if (hasComparison) ...[
                const SizedBox(width: 14),
                _buildLegendItem(
                  color: const Color(0xFF94A3B8),
                  label: 'Tahun Lalu',
                ),
              ],
              const SizedBox(width: 14),
              _buildLegendItem(
                color: const Color(0xFF059669),
                label: 'Target (${widget.targetRate.toInt()}%)',
                isDashed: true,
              ),
            ],
          ),
          const SizedBox(height: 14),

          // Area Canvas Chart
          SizedBox(
            height: 175,
            width: double.infinity,
            child: GestureDetector(
              behavior: HitTestBehavior.opaque,
              onTapDown: (details) {
                final box = context.findRenderObject() as RenderBox?;
                if (box != null && list.isNotEmpty) {
                  final localX = details.localPosition.dx;
                  final availableWidth = box.size.width - 28;
                  final step = availableWidth / list.length;
                  final index = (localX / step).floor().clamp(0, list.length - 1);
                  if (index != _selectedMonthIndex) {
                    HapticFeedback.selectionClick();
                    setState(() {
                      _selectedMonthIndex = index;
                    });
                  }
                }
              },
              child: CustomPaint(
                painter: _TrendChartPainter(
                  data: list,
                  selectedIndex: safeIndex,
                  targetRate: widget.targetRate,
                  hasComparison: hasComparison,
                ),
              ),
            ),
          ),
          const SizedBox(height: 10),

          // Detail Card Bulan Terpilih
          Container(
            padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 9),
            decoration: BoxDecoration(
              color: const Color(0xFFF8FAFC),
              borderRadius: BorderRadius.circular(8),
              border: Border.all(color: const Color(0xFFE2E8F0)),
            ),
            child: Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Row(
                        children: [
                          Text(
                            selectedPoint.monthName,
                            style: const TextStyle(
                              fontFamily: AppTheme.fontFamily,
                              fontSize: 12,
                              fontWeight: FontWeight.w700,
                              color: AppTheme.textPrimary,
                            ),
                          ),
                          if (hasComparison) ...[
                            const SizedBox(width: 6),
                            Text(
                              diffText,
                              style: TextStyle(
                                fontFamily: AppTheme.fontFamily,
                                fontSize: 10,
                                fontWeight: FontWeight.w700,
                                color: diff >= 0
                                    ? const Color(0xFF059669)
                                    : const Color(0xFFE11D48),
                              ),
                            ),
                          ],
                        ],
                      ),
                      const SizedBox(height: 1),
                      Text(
                        selectedPoint.note.isNotEmpty
                            ? selectedPoint.note
                            : 'Tingkat kehadiran ${selectedPoint.val2026.toStringAsFixed(1)}%',
                        style: const TextStyle(
                          fontFamily: AppTheme.fontFamily,
                          fontSize: 10,
                          color: AppTheme.textMuted,
                        ),
                        maxLines: 1,
                        overflow: TextOverflow.ellipsis,
                      ),
                    ],
                  ),
                ),
                Row(
                  children: [
                    Column(
                      crossAxisAlignment: CrossAxisAlignment.end,
                      children: [
                        const Text(
                          'Tingkat Hadir',
                          style: TextStyle(
                            fontFamily: AppTheme.fontFamily,
                            fontSize: 9,
                            fontWeight: FontWeight.w600,
                            color: AppTheme.primary,
                          ),
                        ),
                        Text(
                          '${selectedPoint.val2026.toStringAsFixed(1)}%',
                          style: const TextStyle(
                            fontFamily: AppTheme.fontFamily,
                            fontSize: 13,
                            fontWeight: FontWeight.w800,
                            color: AppTheme.primary,
                          ),
                        ),
                      ],
                    ),
                    if (hasComparison) ...[
                      const SizedBox(width: 12),
                      Column(
                        crossAxisAlignment: CrossAxisAlignment.end,
                        children: [
                          const Text(
                            'Tahun Lalu',
                            style: TextStyle(
                              fontFamily: AppTheme.fontFamily,
                              fontSize: 9,
                              fontWeight: FontWeight.w500,
                              color: Color(0xFF94A3B8),
                            ),
                          ),
                          Text(
                            '${selectedPoint.val2025.toStringAsFixed(1)}%',
                            style: const TextStyle(
                              fontFamily: AppTheme.fontFamily,
                              fontSize: 13,
                              fontWeight: FontWeight.w700,
                              color: Color(0xFF64748B),
                            ),
                          ),
                        ],
                      ),
                    ],
                  ],
                ),
              ],
            ),
          ),
          const SizedBox(height: 12),

          // 3 Kolom Metrik Ringkas Dihitung Dinamis
          Container(
            padding: const EdgeInsets.symmetric(vertical: 8),
            decoration: const BoxDecoration(
              border: Border(
                top: BorderSide(color: Color(0xFFE2E8F0), width: 1),
              ),
            ),
            child: Row(
              children: [
                Expanded(
                  child: _buildSummaryMetric(
                    label: 'Tertinggi',
                    value: '${highestPoint.val2026.toStringAsFixed(1)}%',
                    subtext: 'Bulan ${highestPoint.shortName}',
                    valueColor: const Color(0xFF059669),
                  ),
                ),
                Container(width: 1, height: 28, color: const Color(0xFFE2E8F0)),
                Expanded(
                  child: _buildSummaryMetric(
                    label: 'Terendah',
                    value: '${lowestPoint.val2026.toStringAsFixed(1)}%',
                    subtext: 'Bulan ${lowestPoint.shortName}',
                    valueColor: const Color(0xFFD97706),
                  ),
                ),
                Container(width: 1, height: 28, color: const Color(0xFFE2E8F0)),
                Expanded(
                  child: _buildSummaryMetric(
                    label: 'Rata-rata',
                    value: '${avgRate.toStringAsFixed(1)}%',
                    subtext: '${list.length} Bulan Rekap',
                    valueColor: avgRate >= widget.targetRate
                        ? const Color(0xFF059669)
                        : AppTheme.primary,
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildLegendItem({
    required Color color,
    required String label,
    bool isDashed = false,
  }) {
    return Row(
      mainAxisSize: MainAxisSize.min,
      children: [
        if (isDashed)
          Container(
            width: 12,
            height: 2,
            color: color,
          )
        else
          Container(
            width: 8,
            height: 8,
            decoration: BoxDecoration(
              color: color,
              shape: BoxShape.circle,
            ),
          ),
        const SizedBox(width: 5),
        Text(
          label,
          style: const TextStyle(
            fontFamily: AppTheme.fontFamily,
            fontSize: 10,
            fontWeight: FontWeight.w500,
            color: AppTheme.textSecondary,
          ),
        ),
      ],
    );
  }

  Widget _buildSummaryMetric({
    required String label,
    required String value,
    required String subtext,
    required Color valueColor,
  }) {
    return Column(
      children: [
        Text(
          label,
          style: const TextStyle(
            fontFamily: AppTheme.fontFamily,
            fontSize: 10,
            color: AppTheme.textMuted,
          ),
        ),
        const SizedBox(height: 1),
        Text(
          value,
          style: TextStyle(
            fontFamily: AppTheme.fontFamily,
            fontSize: 13,
            fontWeight: FontWeight.w800,
            color: valueColor,
          ),
        ),
        Text(
          subtext,
          style: const TextStyle(
            fontFamily: AppTheme.fontFamily,
            fontSize: 9,
            color: AppTheme.textMuted,
          ),
        ),
      ],
    );
  }
}

class _TrendChartPainter extends CustomPainter {
  final List<MonthlyTrendPoint> data;
  final int selectedIndex;
  final double targetRate;
  final bool hasComparison;

  _TrendChartPainter({
    required this.data,
    required this.selectedIndex,
    required this.targetRate,
    this.hasComparison = false,
  });

  @override
  void paint(Canvas canvas, Size size) {
    if (data.isEmpty) return;

    const leftPadding = 32.0;
    const rightPadding = 12.0;
    const topPadding = 16.0;
    const bottomPadding = 24.0;

    final chartWidth = size.width - leftPadding - rightPadding;
    final chartHeight = size.height - topPadding - bottomPadding;

    final double minVal = data.map((p) => p.val2026).reduce((a, b) => a < b ? a : b);
    final double minY = minVal < 70.0 ? (minVal - 10.0).clamp(0.0, 60.0) : 70.0;
    const maxY = 100.0;

    double getY(double val) {
      final clamped = val.clamp(minY, maxY);
      final range = maxY - minY;
      final ratio = range > 0 ? (clamped - minY) / range : 0.5;
      return topPadding + chartHeight * (1.0 - ratio);
    }

    double getX(int index) {
      if (data.length <= 1) return leftPadding + (chartWidth / 2);
      return leftPadding + (index * (chartWidth / (data.length - 1)));
    }

    // Gridlines horizontal & labels
    final gridPaint = Paint()
      ..color = const Color(0xFFF1F5F9)
      ..strokeWidth = 1;

    const gridSteps = 3;
    for (int i = 0; i <= gridSteps; i++) {
      final gv = minY + (i * ((maxY - minY) / gridSteps));
      final y = getY(gv);
      canvas.drawLine(Offset(leftPadding, y), Offset(size.width - rightPadding, y), gridPaint);

      final tp = TextPainter(
        text: TextSpan(
          text: '${gv.round()}%',
          style: const TextStyle(
            fontFamily: AppTheme.fontFamily,
            fontSize: 9,
            fontWeight: FontWeight.w500,
            color: Color(0xFF94A3B8),
          ),
        ),
        textDirection: TextDirection.ltr,
      )..layout();
      tp.paint(canvas, Offset(leftPadding - tp.width - 5, y - (tp.height / 2)));
    }

    // Target Line - Green Dashed
    final targetY = getY(targetRate);
    final targetPaint = Paint()
      ..color = const Color(0xFF059669).withValues(alpha: 0.6)
      ..strokeWidth = 1.2
      ..style = PaintingStyle.stroke;

    double dashX = leftPadding;
    while (dashX < size.width - rightPadding) {
      canvas.drawLine(Offset(dashX, targetY), Offset(dashX + 4, targetY), targetPaint);
      dashX += 7;
    }

    // Line Tahun Lalu (jika hasComparison)
    if (hasComparison && data.length > 1) {
      final line2025Paint = Paint()
        ..color = const Color(0xFF94A3B8)
        ..strokeWidth = 1.5
        ..style = PaintingStyle.stroke;

      final path2025 = Path();
      for (int i = 0; i < data.length; i++) {
        final x = getX(i);
        final y = getY(data[i].val2025);
        if (i == 0) {
          path2025.moveTo(x, y);
        } else {
          path2025.lineTo(x, y);
        }
      }
      canvas.drawPath(path2025, line2025Paint);
    }

    // Line Kehadiran (Navy Primary)
    if (data.length > 1) {
      final line2026Paint = Paint()
        ..color = AppTheme.primary
        ..strokeWidth = 2.4
        ..style = PaintingStyle.stroke;

      final path2026 = Path();
      for (int i = 0; i < data.length; i++) {
        final x = getX(i);
        final y = getY(data[i].val2026);
        if (i == 0) {
          path2026.moveTo(x, y);
        } else {
          path2026.lineTo(x, y);
        }
      }
      canvas.drawPath(path2026, line2026Paint);
    } else {
      // Single month point
      final x = getX(0);
      final y = getY(data[0].val2026);
      final dotPaint = Paint()
        ..color = AppTheme.primary
        ..style = PaintingStyle.fill;
      canvas.drawCircle(Offset(x, y), 6, dotPaint);
    }

    // Highlight line vertikal pada bulan terpilih
    if (selectedIndex >= 0 && selectedIndex < data.length) {
      final selX = getX(selectedIndex);
      final selY2026 = getY(data[selectedIndex].val2026);

      final selLinePaint = Paint()
        ..color = const Color(0xFFCBD5E1)
        ..strokeWidth = 1;
      canvas.drawLine(
        Offset(selX, topPadding),
        Offset(selX, topPadding + chartHeight),
        selLinePaint,
      );

      // Dot Terpilih
      final selDotBg = Paint()
        ..color = Colors.white
        ..style = PaintingStyle.fill;
      final selDotBorder = Paint()
        ..color = AppTheme.primary
        ..strokeWidth = 2.5
        ..style = PaintingStyle.stroke;

      canvas.drawCircle(Offset(selX, selY2026), 4.5, selDotBg);
      canvas.drawCircle(Offset(selX, selY2026), 4.5, selDotBorder);
    }

    // Sumbu X: Nama Bulan
    for (int i = 0; i < data.length; i++) {
      final x = getX(i);
      final isSelected = i == selectedIndex;

      final tp = TextPainter(
        text: TextSpan(
          text: data[i].shortName,
          style: TextStyle(
            fontFamily: AppTheme.fontFamily,
            fontSize: 9,
            fontWeight: isSelected ? FontWeight.w800 : FontWeight.w500,
            color: isSelected ? AppTheme.primary : const Color(0xFF64748B),
          ),
        ),
        textDirection: TextDirection.ltr,
      )..layout();

      tp.paint(canvas, Offset(x - (tp.width / 2), size.height - bottomPadding + 6));
    }
  }

  @override
  bool shouldRepaint(covariant _TrendChartPainter oldDelegate) {
    return oldDelegate.selectedIndex != selectedIndex ||
        oldDelegate.data != data ||
        oldDelegate.hasComparison != hasComparison;
  }
}
