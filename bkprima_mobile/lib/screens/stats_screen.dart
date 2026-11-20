import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:provider/provider.dart';
import '../core/theme/app_theme.dart';
import '../core/widgets/app_surface.dart';
import '../models/violation_model.dart';
import '../providers/attendance_provider.dart';
import '../providers/violation_provider.dart';
import '../services/api_service.dart';
import '../widgets/attendance_trend_chart.dart';
import '../widgets/stat_card.dart';
import 'violation_records_screen.dart';

class StatsScreen extends StatefulWidget {
  const StatsScreen({super.key});

  @override
  State<StatsScreen> createState() => _StatsScreenState();
}

class _StatsScreenState extends State<StatsScreen> {
  bool _isLoading = false;
  Map<String, dynamic>? _masterData;
  List<MonthlyTrendPoint>? _monthlyTrend;

  @override
  void initState() {
    super.initState();
    _fetchMasterStats();
  }

  Future<void> _fetchMasterStats() async {
    setState(() {
      _isLoading = true;
    });

    try {
      final res = await ApiService.getOverallStats();
      if (res['success'] == true && res['data'] != null) {
        final data = res['data'] as Map<String, dynamic>;
        final List trendJson = data['monthly_trend'] ?? [];
        final parsedTrend = trendJson.map((item) => MonthlyTrendPoint.fromJson(item)).toList();

        if (mounted) {
          setState(() {
            _masterData = data;
            _monthlyTrend = parsedTrend;
            _isLoading = false;
          });
        }
        return;
      }
    } catch (_) {}

    if (mounted) {
      setState(() {
        _isLoading = false;
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    final att = context.watch<AttendanceProvider>();
    final vio = context.watch<ViolationProvider>();

    // Metrik Keseluruhan Master Admin (1.500 Siswa)
    final summary = _masterData?['summary'] as Map<String, dynamic>?;
    final benchmark = _masterData?['benchmark'] as Map<String, dynamic>?;

    final hadirRate = (summary?['hadir_rate'] as num?)?.toDouble() ?? 94.8;
    final hadirCount = (summary?['hadir_count'] as num?)?.toInt() ?? 1422;
    final totalStudents = (summary?['total_students'] as num?)?.toInt() ?? 1500;

    final terlambatRate = (summary?['terlambat_rate'] as num?)?.toDouble() ?? 2.8;
    final terlambatCount = (summary?['terlambat_count'] as num?)?.toInt() ?? 42;

    final totalViolations = (summary?['total_violations'] as num?)?.toInt() ?? (vio.stats?.totalKasus ?? 3);
    final spCasesCount = (summary?['sp_cases'] as num?)?.toInt() ?? (vio.stats?.panggilanOrtu ?? 2);

    final avg2026 = (benchmark?['avg_2026'] as num?)?.toDouble() ?? 91.3;
    final avg2025 = (benchmark?['avg_2025'] as num?)?.toDouble() ?? 88.2;
    final targetRate = (benchmark?['target_rate'] as num?)?.toDouble() ?? 90.0;

    // Siswa Tahap SP (Seluruh Sekolah)
    final List spRaw = _masterData?['surat_panggilan'] ?? [];
    List<SuratPanggilan> highRiskStudents = spRaw.map((item) => SuratPanggilan.fromJson(item)).toList();
    if (highRiskStudents.isEmpty && vio.suratPanggilan.isNotEmpty) {
      highRiskStudents = vio.suratPanggilan;
    }

    return Container(
      color: AppTheme.background,
      child: RefreshIndicator(
        onRefresh: () async {
          await Future.wait([
            _fetchMasterStats(),
            att.fetchAttendance(),
            vio.fetchViolations(),
          ]);
        },
        color: AppTheme.primary,
        child: SingleChildScrollView(
          physics: const AlwaysScrollableScrollPhysics(),
          padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              // Header Screen
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      const Text(
                        'Statistik Kedisiplinan',
                        style: TextStyle(
                          fontFamily: AppTheme.fontFamily,
                          fontSize: 20,
                          fontWeight: FontWeight.w800,
                          color: AppTheme.textPrimary,
                          letterSpacing: -0.4,
                        ),
                      ),
                      const SizedBox(height: 2),
                      Row(
                        children: [
                          Container(
                            width: 6,
                            height: 6,
                            decoration: const BoxDecoration(
                              color: Color(0xFF059669),
                              shape: BoxShape.circle,
                            ),
                          ),
                          const SizedBox(width: 5),
                          Text(
                            'Data Master Sekolah • $totalStudents Siswa',
                            style: const TextStyle(
                              fontFamily: AppTheme.fontFamily,
                              fontSize: 11,
                              fontWeight: FontWeight.w500,
                              color: AppTheme.textMuted,
                            ),
                          ),
                        ],
                      ),
                    ],
                  ),
                  if (_isLoading)
                    const SizedBox(
                      width: 16,
                      height: 16,
                      child: CircularProgressIndicator(strokeWidth: 2, color: AppTheme.primary),
                    ),
                ],
              ),
              const SizedBox(height: 14),

              // KPI Grid 2x2 (Mengambil Keseluruhan 1.500 Siswa)
              GridView.count(
                shrinkWrap: true,
                physics: const NeverScrollableScrollPhysics(),
                crossAxisCount: 2,
                crossAxisSpacing: 8,
                mainAxisSpacing: 8,
                childAspectRatio: 1.75,
                children: [
                  StatCard(
                    title: 'Kehadiran Sekolah',
                    value: '${hadirRate.toStringAsFixed(1)}%',
                    subtitle: '$hadirCount / $totalStudents Siswa',
                    valueColor: const Color(0xFF059669),
                  ),
                  StatCard(
                    title: 'Keterlambatan',
                    value: '${terlambatRate.toStringAsFixed(1)}%',
                    subtitle: '$terlambatCount Siswa Hari Ini',
                    valueColor: const Color(0xFFD97706),
                  ),
                  StatCard(
                    title: 'Total Pelanggaran',
                    value: '$totalViolations',
                    subtitle: 'Tercatat Semester Ini',
                    valueColor: AppTheme.textPrimary,
                  ),
                  StatCard(
                    title: 'Kasus SP Sekolah',
                    value: '$spCasesCount',
                    subtitle: 'Dalam Pembinaan Khusus',
                    valueColor: const Color(0xFFE11D48),
                  ),
                ],
              ),
              const SizedBox(height: 14),

              // Grafik Kehadiran Master Admin (12 Bulan Perbandingan 2026 vs 2025)
              AttendanceTrendChart(
                data: _monthlyTrend,
                targetRate: targetRate,
                avg2026: avg2026,
                avg2025: avg2025,
              ),
              const SizedBox(height: 14),

              // Distribusi Kategori
              AppSurface(
                padding: const EdgeInsets.all(14),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    const Text(
                      'Distribusi Kategori Pelanggaran',
                      style: TextStyle(
                        fontFamily: AppTheme.fontFamily,
                        fontSize: 13,
                        fontWeight: FontWeight.w700,
                        color: AppTheme.textPrimary,
                      ),
                    ),
                    const SizedBox(height: 12),
                    _buildProgressRow('Kedisiplinan', 0.65, '65%', AppTheme.primary),
                    const SizedBox(height: 8),
                    _buildProgressRow('Kerapian', 0.20, '20%', const Color(0xFFD97706)),
                    const SizedBox(height: 8),
                    _buildProgressRow('Ketertiban', 0.10, '10%', const Color(0xFF475569)),
                    const SizedBox(height: 8),
                    _buildProgressRow('Pelanggaran Khusus', 0.05, '5%', const Color(0xFFE11D48)),
                  ],
                ),
              ),
              const SizedBox(height: 14),

              // Siswa Tahap SP Header & List
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  const Text(
                    'Siswa Tahap SP (Sekolah)',
                    style: TextStyle(
                      fontFamily: AppTheme.fontFamily,
                      fontSize: 13,
                      fontWeight: FontWeight.w700,
                      color: AppTheme.textPrimary,
                    ),
                  ),
                  InkWell(
                    onTap: () {
                      HapticFeedback.selectionClick();
                      Navigator.push(
                        context,
                        MaterialPageRoute(builder: (_) => const ViolationRecordsScreen()),
                      );
                    },
                    child: const Text(
                      'Lihat Semua',
                      style: TextStyle(
                        fontFamily: AppTheme.fontFamily,
                        fontSize: 11,
                        fontWeight: FontWeight.w600,
                        color: AppTheme.primary,
                      ),
                    ),
                  ),
                ],
              ),
              const SizedBox(height: 8),

              if (highRiskStudents.isEmpty)
                AppSurface(
                  padding: const EdgeInsets.all(20),
                  child: const Center(
                    child: Text(
                      'Tidak ada siswa dalam tahap SP.',
                      style: TextStyle(
                        fontFamily: AppTheme.fontFamily,
                        fontSize: 12,
                        color: AppTheme.textSecondary,
                      ),
                    ),
                  ),
                )
              else
                ...highRiskStudents.map((sp) {
                  return Padding(
                    padding: const EdgeInsets.only(bottom: 6),
                    child: AppSurface(
                      padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 10),
                      child: Row(
                        children: [
                          Container(
                            padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                            decoration: BoxDecoration(
                              color: AppTheme.dangerLight,
                              borderRadius: BorderRadius.circular(6),
                              border: Border.all(color: AppTheme.dangerBorder, width: 1),
                            ),
                            child: Text(
                              sp.spLevel,
                              style: const TextStyle(
                                fontFamily: AppTheme.fontFamily,
                                fontSize: 10,
                                fontWeight: FontWeight.w800,
                                color: AppTheme.danger,
                              ),
                            ),
                          ),
                          const SizedBox(width: 10),
                          Expanded(
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Text(
                                  sp.name,
                                  style: const TextStyle(
                                    fontFamily: AppTheme.fontFamily,
                                    fontSize: 13,
                                    fontWeight: FontWeight.w700,
                                    color: AppTheme.textPrimary,
                                  ),
                                ),
                                const SizedBox(height: 1),
                                Text(
                                  sp.className,
                                  style: const TextStyle(
                                    fontFamily: AppTheme.fontFamily,
                                    fontSize: 11,
                                    color: AppTheme.textSecondary,
                                  ),
                                ),
                              ],
                            ),
                          ),
                          Text(
                            '${sp.totalPoints} Poin',
                            style: const TextStyle(
                              fontFamily: AppTheme.fontFamily,
                              fontSize: 12,
                              fontWeight: FontWeight.w700,
                              color: AppTheme.danger,
                            ),
                          ),
                        ],
                      ),
                    ),
                  );
                }),

              const SizedBox(height: 20),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildProgressRow(String label, double value, String percentage, Color color) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Row(
          mainAxisAlignment: MainAxisAlignment.spaceBetween,
          children: [
            Text(
              label,
              style: const TextStyle(
                fontFamily: AppTheme.fontFamily,
                fontSize: 11,
                fontWeight: FontWeight.w500,
                color: AppTheme.textSecondary,
              ),
            ),
            Text(
              percentage,
              style: const TextStyle(
                fontFamily: AppTheme.fontFamily,
                fontSize: 11,
                fontWeight: FontWeight.w700,
                color: AppTheme.textPrimary,
              ),
            ),
          ],
        ),
        const SizedBox(height: 3),
        ClipRRect(
          borderRadius: BorderRadius.circular(4),
          child: LinearProgressIndicator(
            value: value,
            minHeight: 5,
            backgroundColor: AppTheme.surfaceMuted,
            color: color,
          ),
        ),
      ],
    );
  }
}
