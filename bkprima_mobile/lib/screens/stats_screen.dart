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
  String? _errorMessage;
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
      _errorMessage = null;
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
            _errorMessage = null;
          });
        }
        return;
      } else {
        if (mounted) {
          setState(() {
            _isLoading = false;
            _errorMessage = res['message']?.toString() ?? 'Gagal memuat data statistik';
          });
        }
      }
    } catch (e) {
      if (mounted) {
        setState(() {
          _isLoading = false;
          _errorMessage = 'Gagal terhubung ke server: $e';
        });
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    final att = context.watch<AttendanceProvider>();
    final vio = context.watch<ViolationProvider>();

    // Initial loading state
    if (_isLoading && _masterData == null) {
      return Container(
        color: AppTheme.background,
        child: const Center(
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              SizedBox(
                width: 24,
                height: 24,
                child: CircularProgressIndicator(strokeWidth: 2.5, color: AppTheme.primary),
              ),
              SizedBox(height: 12),
              Text(
                'Memuat data statistik...',
                style: TextStyle(
                  fontFamily: AppTheme.fontFamily,
                  fontSize: 12,
                  color: AppTheme.textSecondary,
                ),
              ),
            ],
          ),
        ),
      );
    }

    // Error state when no data is cached yet
    if (_masterData == null && _errorMessage != null) {
      return Container(
        color: AppTheme.background,
        child: Center(
          child: Padding(
            padding: const EdgeInsets.all(24),
            child: Column(
              mainAxisSize: MainAxisSize.min,
              children: [
                const Icon(Icons.cloud_off_outlined, size: 44, color: AppTheme.textMuted),
                const SizedBox(height: 12),
                Text(
                  _errorMessage!,
                  textAlign: TextAlign.center,
                  style: const TextStyle(
                    fontFamily: AppTheme.fontFamily,
                    fontSize: 13,
                    color: AppTheme.textSecondary,
                  ),
                ),
                const SizedBox(height: 16),
                OutlinedButton.icon(
                  onPressed: _fetchMasterStats,
                  icon: const Icon(Icons.refresh, size: 16),
                  label: const Text('Coba Lagi'),
                  style: OutlinedButton.styleFrom(
                    foregroundColor: AppTheme.primary,
                    side: const BorderSide(color: AppTheme.border),
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(8)),
                  ),
                ),
              ],
            ),
          ),
        ),
      );
    }

    // Metrik Keseluruhan dari API Database Real
    final summary = _masterData?['summary'] as Map<String, dynamic>?;
    final benchmark = _masterData?['benchmark'] as Map<String, dynamic>?;

    final hadirRate = (summary?['hadir_rate'] as num?)?.toDouble() ?? 0.0;
    final hadirCount = (summary?['hadir_count'] as num?)?.toInt() ?? 0;
    final totalStudents = (summary?['total_students'] as num?)?.toInt() ?? 0;

    final terlambatRate = (summary?['terlambat_rate'] as num?)?.toDouble() ?? 0.0;
    final terlambatCount = (summary?['terlambat_count'] as num?)?.toInt() ?? 0;

    final totalViolations = (summary?['total_violations'] as num?)?.toInt() ?? (vio.stats?.totalKasus ?? vio.violations.length);
    final spCasesCount = (summary?['sp_cases'] as num?)?.toInt() ?? (vio.stats?.panggilanOrtu ?? vio.suratPanggilan.length);

    final targetRate = (benchmark?['target_rate'] as num?)?.toDouble() ?? 90.0;

    // Distribusi Kategori Pelanggaran (dari API atau fallback hitung langsung dari data lokal)
    final rawDist = _masterData?['category_distribution'] as List?;
    List<CategoryDistribution> distribution = [];
    if (rawDist != null && rawDist.isNotEmpty) {
      distribution = rawDist.map((e) => CategoryDistribution.fromJson(e)).toList();
    } else if (vio.violations.isNotEmpty) {
      final Map<String, int> catCounts = {};
      for (final v in vio.violations) {
        catCounts[v.category] = (catCounts[v.category] ?? 0) + 1;
      }
      final total = vio.violations.length;
      distribution = catCounts.entries.map((e) {
        final pct = total > 0 ? (e.value / total) * 100 : 0.0;
        return CategoryDistribution(
          name: e.key,
          count: e.value,
          percentage: pct,
          ratio: total > 0 ? e.value / total : 0.0,
        );
      }).toList();
    }

    // Siswa Tahap SP (Seluruh Sekolah)
    final List spRaw = _masterData?['surat_panggilan'] ?? [];
    List<SuratPanggilan> highRiskStudents = spRaw.map((item) => SuratPanggilan.fromJson(item)).toList();
    if (highRiskStudents.isEmpty && vio.suratPanggilan.isNotEmpty) {
      highRiskStudents = vio.suratPanggilan;
    }

    const categoryColors = [
      AppTheme.primary,
      Color(0xFFD97706),
      Color(0xFF475569),
      Color(0xFFE11D48),
      Color(0xFF0D9488),
      Color(0xFF6366F1),
    ];

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
                            decoration: BoxDecoration(
                              color: totalStudents > 0 ? const Color(0xFF059669) : AppTheme.textMuted,
                              shape: BoxShape.circle,
                            ),
                          ),
                          const SizedBox(width: 5),
                          Text(
                            totalStudents > 0
                                ? 'Data Presensi Siswa • $totalStudents Siswa'
                                : 'Data Presensi Siswa • Belum Ada Data',
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

              // KPI Grid 2x2
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

              // Grafik Kehadiran
              AttendanceTrendChart(
                data: _monthlyTrend,
                targetRate: targetRate,
                totalStudents: totalStudents,
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
                    if (distribution.isEmpty)
                      const Padding(
                        padding: EdgeInsets.symmetric(vertical: 12),
                        child: Center(
                          child: Text(
                            'Belum ada catatan pelanggaran siswa.',
                            style: TextStyle(
                              fontFamily: AppTheme.fontFamily,
                              fontSize: 12,
                              color: AppTheme.textSecondary,
                            ),
                          ),
                        ),
                      )
                    else
                      ...distribution.asMap().entries.map((entry) {
                        final idx = entry.key;
                        final item = entry.value;
                        final color = categoryColors[idx % categoryColors.length];
                        return Padding(
                          padding: EdgeInsets.only(bottom: idx == distribution.length - 1 ? 0 : 8),
                          child: _buildProgressRow(
                            item.name,
                            item.ratio,
                            '${item.percentage.toStringAsFixed(0)}% (${item.count})',
                            color,
                          ),
                        );
                      }),
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
            value: value.clamp(0.0, 1.0),
            minHeight: 5,
            backgroundColor: AppTheme.surfaceMuted,
            color: color,
          ),
        ),
      ],
    );
  }
}
