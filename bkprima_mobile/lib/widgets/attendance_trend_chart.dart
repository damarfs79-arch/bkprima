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
    return MonthlyTrendPoint(
      month: json['month'] is int ? json['month'] : int.tryParse(json['month'].toString()) ?? 1,
      shortName: json['short_name']?.toString() ?? 'Bln',
      monthName: json['month_name']?.toString() ?? '',
      val2025: (json['val_2025'] as num?)?.toDouble() ?? 80.0,
      val2026: (json['val_2026'] as num?)?.toDouble() ?? 85.0,
      note: json['note']?.toString() ?? '',
    );
  }
}

class AttendanceTrendChart extends StatefulWidget {
  final List<MonthlyTrendPoint>? data;
  final double targetRate;
  final double avg2026;
  final double avg2025;

  const AttendanceTrendChart({
    super.key,
    this.data,
    this.targetRate = 90.0,
    this.avg2026 = 91.3,
    this.avg2025 = 88.2,
  });

  @override
  State<AttendanceTrendChart> createState() => _AttendanceTrendChartState();
}

class _AttendanceTrendChartState extends State<AttendanceTrendChart> {
  int _selectedMonthIndex = 10; // Default: November (Bulan ke-11)

  // Default Master Admin Dataset
  static const List<MonthlyTrendPoint> _defaultDataset = [
    MonthlyTrendPoint(month: 1, shortName: 'Jan', monthName: 'Januari', val2025: 86.5, val2026: 88.2, note: 'Awal Masuk Semester Genap'),
    MonthlyTrendPoint(month: 2, shortName: 'Feb', monthName: 'Februari', val2025: 82.0, val2026: 85.4, note: 'Kegiatan Pembelajaran Efektif'),
    MonthlyTrendPoint(month: 3, shortName: 'Mar', monthName: 'Maret', val2025: 89.1, val2026: 92.0, note: 'Ujian Tengah Semester (UTS)'),
    MonthlyTrendPoint(month: 4, shortName: 'Apr', monthName: 'April', val2025: 84.5, val2026: 87.8, note: 'Pesantren Kilat & Libur Idul Fitri'),
    MonthlyTrendPoint(month: 5, shortName: 'Mei', monthName: 'Mei', val2025: 93.0, val2026: 94.6, note: 'Praktik Kerja Industri & Sertifikasi'),
    MonthlyTrendPoint(month: 6, shortName: 'Jun', monthName: 'Juni', val2025: 78.4, val2026: 83.1, note: 'Penilaian Akhir Tahun & Libur'),
    MonthlyTrendPoint(month: 7, shortName: 'Jul', monthName: 'Juli', val2025: 94.2, val2026: 91.5, note: 'MPLS & Awal Tahun Ajaran'),
    MonthlyTrendPoint(month: 8, shortName: 'Agu', monthName: 'Agustus', val2025: 81.3, val2026: 89.0, note: 'Lomba HUT RI & Ekstrakurikuler'),
    MonthlyTrendPoint(month: 9, shortName: 'Sep', monthName: 'September', val2025: 90.5, val2026: 96.8, note: 'PTS Ganjil & Patroli Disiplin'),
    MonthlyTrendPoint(month: 10, shortName: 'Okt', monthName: 'Oktober', val2025: 87.2, val2026: 90.4, note: 'Bulan Bahasa & Karakter'),
    MonthlyTrendPoint(month: 11, shortName: 'Nov', monthName: 'November', val2025: 95.8, val2026: 97.4, note: 'Pemantapan Jelang Ujian Akhir'),
    MonthlyTrendPoint(month: 12, shortName: 'Des', monthName: 'Desember', val2025: 85.0, val2026: 91.0, note: 'PAS Ganjil & Pembagian Rapor'),
  ];

  @override
  Widget build(BuildContext context) {
    final list = (widget.data != null && widget.data!.isNotEmpty) ? widget.data! : _defaultDataset;
    final safeIndex = _selectedMonthIndex.clamp(0, list.length - 1);
    final selectedPoint = list[safeIndex];
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
                    const Text(
                      'Tren Kehadiran Sekolah (1.500 Siswa)',
                      style: TextStyle(
                        fontFamily: AppTheme.fontFamily,
                        fontSize: 13,
                        fontWeight: FontWeight.w700,
                        color: AppTheme.textPrimary,
                        letterSpacing: -0.2,
                      ),
                    ),
                    const SizedBox(height: 2),
                    const Text(
                      'Komparasi Master Data TA 2026 vs 2025',
                      style: TextStyle(
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
                  'Avg: ${widget.avg2026.toStringAsFixed(1)}%',
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
                label: 'TA 2026',
              ),
              const SizedBox(width: 14),
              _buildLegendItem(
                color: const Color(0xFF94A3B8),
                label: 'TA 2025',
              ),
              const SizedBox(width: 14),
              _buildLegendItem(
                color: const Color(0xFF059669),
                label: 'Target (90%)',
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
                if (box != null) {
                  final localX = details.localPosition.dx;
                  final availableWidth = box.size.width - 28; // minus padding
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
                          const SizedBox(width: 6),
                          Text(
                            diffText,
                            style: TextStyle(
                              fontFamily: AppTheme.fontFamily,
                              fontSize: 10,
                              fontWeight: FontWeight.w700,
                              color: diff >= 0 ? const Color(0xFF059669) : const Color(0xFFE11D48),
                            ),
                          ),
                        ],
                      ),
                      const SizedBox(height: 1),
                      Text(
                        selectedPoint.note,
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
                          '2026',
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
                    const SizedBox(width: 12),
                    Column(
                      crossAxisAlignment: CrossAxisAlignment.end,
                      children: [
                        const Text(
                          '2025',
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
                ),
              ],
            ),
          ),
          const SizedBox(height: 12),

          // 3 Kolom Metrik Ringkas
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
                    value: '97.4%',
                    subtext: 'Bulan Nov',
                    valueColor: const Color(0xFF059669),
                  ),
                ),
                Container(width: 1, height: 28, color: const Color(0xFFE2E8F0)),
                Expanded(
                  child: _buildSummaryMetric(
                    label: 'Terendah',
                    value: '83.1%',
                    subtext: 'Bulan Jun',
                    valueColor: const Color(0xFFD97706),
                  ),
                ),
                Container(width: 1, height: 28, color: const Color(0xFFE2E8F0)),
                Expanded(
                  child: _buildSummaryMetric(
                    label: 'Kenaikan YoY',
                    value: '+3.1%',
                    subtext: 'vs TA 2025',
                    valueColor: AppTheme.primary,
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildLegendItem({required Color color, required String label, bool isDashed = false}) {
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

  _TrendChartPainter({
    required this.data,
    required this.selectedIndex,
    required this.targetRate,
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

    const minY = 70.0;
    const maxY = 100.0;

    double getY(double val) {
      final clamped = val.clamp(minY, maxY);
      final ratio = (clamped - minY) / (maxY - minY);
      return topPadding + chartHeight * (1.0 - ratio);
    }

    double getX(int index) {
      if (data.length <= 1) return leftPadding;
      return leftPadding + (index * (chartWidth / (data.length - 1)));
    }

    // Gridlines horizontal & labels (70, 80, 90, 100)
    final gridPaint = Paint()
      ..color = const Color(0xFFF1F5F9)
      ..strokeWidth = 1;

    final gridValues = [70.0, 80.0, 90.0, 100.0];
    for (final gv in gridValues) {
      final y = getY(gv);
      canvas.drawLine(Offset(leftPadding, y), Offset(size.width - rightPadding, y), gridPaint);

      final tp = TextPainter(
        text: TextSpan(
          text: '${gv.toInt()}%',
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

    // Target Line (90%) - Green Dashed
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

    // Line 2025 (Slate Muted)
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

    // Line 2026 (Navy Primary)
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

    // Highlight line vertikal pada bulan terpilih
    if (selectedIndex >= 0 && selectedIndex < data.length) {
      final selX = getX(selectedIndex);
      final selY2026 = getY(data[selectedIndex].val2026);

      final selLinePaint = Paint()
        ..color = const Color(0xFFCBD5E1)
        ..strokeWidth = 1;
      canvas.drawLine(Offset(selX, topPadding), Offset(selX, topPadding + chartHeight), selLinePaint);

      // Dot 2026
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
    return oldDelegate.selectedIndex != selectedIndex || oldDelegate.data != data;
  }
}
