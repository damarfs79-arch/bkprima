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

  String _scope = 'my_rombel_today';
  int _selectedMonth = DateTime.now().month;
  final int _selectedYear = DateTime.now().year;

  static const List<Map<String, dynamic>> _months = [
    {'value': 1, 'label': 'Januari'},
    {'value': 2, 'label': 'Februari'},
    {'value': 3, 'label': 'Maret'},
    {'value': 4, 'label': 'April'},
    {'value': 5, 'label': 'Mei'},
    {'value': 6, 'label': 'Juni'},
    {'value': 7, 'label': 'Juli'},
    {'value': 8, 'label': 'Agustus'},
    {'value': 9, 'label': 'September'},
    {'value': 10, 'label': 'Oktober'},
    {'value': 11, 'label': 'November'},
    {'value': 12, 'label': 'Desember'},
  ];

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
      final res = await ApiService.getOverallStats(
        scope: _scope,
        month: _scope == 'school_month' ? _selectedMonth : null,
        year: _scope == 'school_month' ? _selectedYear : null,
      );
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
    final isMonthlySchool = _scope == 'school_month';
    final hasRombel = _masterData?['has_rombel'] != false;
    final List<String> assignedRombels = List<String>.from(_masterData?['assigned_rombels'] ?? []);

    final hadirRate = (summary?['hadir_rate'] as num?)?.toDouble() ?? 0.0;
    final hadirCount = (summary?['hadir_count'] as num?)?.toInt() ?? 0;
    final totalRecorded = (summary?['total_recorded'] as num?)?.toInt() ?? 0;
    final totalStudents = (summary?['total_students'] as num?)?.toInt() ?? 0;

    final terlambatRate = (summary?['terlambat_rate'] as num?)?.toDouble() ?? 0.0;
    final terlambatCount = (summary?['terlambat_count'] as num?)?.toInt() ?? 0;

    final totalViolations = (summary?['total_violations'] as num?)?.toInt() ?? 0;
    final spCasesCount = (summary?['sp_cases'] as num?)?.toInt() ?? 0;

    final targetRate = (benchmark?['target_rate'] as num?)?.toDouble() ?? 90.0;

    // Distribusi Kategori Pelanggaran murni sesuai scope aktif
    final rawDist = _masterData?['category_distribution'] as List?;
    final List<CategoryDistribution> distribution = rawDist != null
        ? rawDist.map((e) => CategoryDistribution.fromJson(e)).toList()
        : [];

    // Siswa Tahap SP sesuai scope aktif
    final List spRaw = _masterData?['surat_panggilan'] ?? [];
    final List<SuratPanggilan> highRiskStudents =
        spRaw.map((item) => SuratPanggilan.fromJson(item)).toList();

    final selectedMonthLabel = _months.firstWhere(
      (m) => m['value'] == _selectedMonth,
      orElse: () => {'label': 'Bulan $_selectedMonth'},
    )['label'];

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
                  Expanded(
                    child: Column(
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
                            Expanded(
                              child: Text(
                                isMonthlySchool
                                    ? 'Rekap 1 Bulan ($selectedMonthLabel $_selectedYear) • $totalStudents Siswa'
                                    : (hasRombel
                                        ? 'Hari Ini (${assignedRombels.join(', ')}) • $totalStudents Siswa'
                                        : 'Belum ada rombel ditugaskan'),
                                overflow: TextOverflow.ellipsis,
                                style: const TextStyle(
                                  fontFamily: AppTheme.fontFamily,
                                  fontSize: 11,
                                  fontWeight: FontWeight.w500,
                                  color: AppTheme.textMuted,
                                ),
                              ),
                            ),
                          ],
                        ),
                      ],
                    ),
                  ),
                  if (_isLoading) ...[
                    const SizedBox(width: 8),
                    const SizedBox(
                      width: 16,
                      height: 16,
                      child: CircularProgressIndicator(strokeWidth: 2, color: AppTheme.primary),
                    ),
                  ],
                ],
              ),
              const SizedBox(height: 12),

              // Menu Pengalih Mode Statistik (1 Hari Rombel Saya vs 1 Bulan Seluruh Siswa)
              Container(
                padding: const EdgeInsets.all(4),
                decoration: BoxDecoration(
                  color: AppTheme.surfaceMuted,
                  borderRadius: BorderRadius.circular(10),
                  border: Border.all(color: AppTheme.border),
                ),
                child: Row(
                  children: [
                    Expanded(
                      child: InkWell(
                        onTap: () {
                          if (_scope != 'my_rombel_today') {
                            HapticFeedback.selectionClick();
                            setState(() => _scope = 'my_rombel_today');
                            _fetchMasterStats();
                          }
                        },
                        borderRadius: BorderRadius.circular(7),
                        child: Container(
                          padding: const EdgeInsets.symmetric(vertical: 8, horizontal: 8),
                          decoration: BoxDecoration(
                            color: !isMonthlySchool ? AppTheme.surface : Colors.transparent,
                            borderRadius: BorderRadius.circular(7),
                            border: !isMonthlySchool ? Border.all(color: AppTheme.border) : null,
                          ),
                          child: Text(
                            'Hari Ini (Rombel)',
                            textAlign: TextAlign.center,
                            style: TextStyle(
                              fontFamily: AppTheme.fontFamily,
                              fontSize: 11,
                              fontWeight: !isMonthlySchool ? FontWeight.w700 : FontWeight.w500,
                              color: !isMonthlySchool ? AppTheme.primary : AppTheme.textSecondary,
                            ),
                          ),
                        ),
                      ),
                    ),
                    const SizedBox(width: 4),
                    Expanded(
                      child: InkWell(
                        onTap: () {
                          if (_scope != 'school_month') {
                            HapticFeedback.selectionClick();
                            setState(() => _scope = 'school_month');
                            _fetchMasterStats();
                          }
                        },
                        borderRadius: BorderRadius.circular(7),
                        child: Container(
                          padding: const EdgeInsets.symmetric(vertical: 8, horizontal: 8),
                          decoration: BoxDecoration(
                            color: isMonthlySchool ? AppTheme.surface : Colors.transparent,
                            borderRadius: BorderRadius.circular(7),
                            border: isMonthlySchool ? Border.all(color: AppTheme.border) : null,
                          ),
                          child: Text(
                            '1 Bulan (Sekolah)',
                            textAlign: TextAlign.center,
                            style: TextStyle(
                              fontFamily: AppTheme.fontFamily,
                              fontSize: 11,
                              fontWeight: isMonthlySchool ? FontWeight.w700 : FontWeight.w500,
                              color: isMonthlySchool ? AppTheme.primary : AppTheme.textSecondary,
                            ),
                          ),
                        ),
                      ),
                    ),
                  ],
                ),
              ),

              if (isMonthlySchool) ...[
                const SizedBox(height: 10),
                AppSurface(
                  padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
                  child: Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      const Text(
                        'Periode Bulan',
                        style: TextStyle(
                          fontFamily: AppTheme.fontFamily,
                          fontSize: 12,
                          fontWeight: FontWeight.w600,
                          color: AppTheme.textSecondary,
                        ),
                      ),
                      DropdownButtonHideUnderline(
                        child: DropdownButton<int>(
                          value: _selectedMonth,
                          isDense: true,
                          icon: const Icon(Icons.keyboard_arrow_down_rounded, size: 18, color: AppTheme.primary),
                          style: const TextStyle(
                            fontFamily: AppTheme.fontFamily,
                            fontSize: 12,
                            fontWeight: FontWeight.w700,
                            color: AppTheme.textPrimary,
                          ),
                          items: _months.map((m) {
                            return DropdownMenuItem<int>(
                              value: m['value'] as int,
                              child: Text('${m['label']} $_selectedYear'),
                            );
                          }).toList(),
                          onChanged: (val) {
                            if (val != null && val != _selectedMonth) {
                              setState(() => _selectedMonth = val);
                              _fetchMasterStats();
                            }
                          },
                        ),
                      ),
                    ],
                  ),
                ),
              ],

              if (!isMonthlySchool && !hasRombel) ...[
                const SizedBox(height: 10),
                AppSurface(
                  padding: const EdgeInsets.all(12),
                  child: const Row(
                    children: [
                      Icon(Icons.info_outline_rounded, size: 18, color: AppTheme.textSecondary),
                      SizedBox(width: 10),
                      Expanded(
                        child: Text(
                          'Akun Anda belum memiliki rombel yang ditugaskan. Data hari ini ditampilkan kosong.',
                          style: TextStyle(
                            fontFamily: AppTheme.fontFamily,
                            fontSize: 11,
                            color: AppTheme.textSecondary,
                            height: 1.4,
                          ),
                        ),
                      ),
                    ],
                  ),
                ),
              ],
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
                    title: isMonthlySchool ? 'Kehadiran Sekolah' : 'Kehadiran Rombel',
                    value: '${hadirRate.toStringAsFixed(1)}%',
                    subtitle: '$hadirCount / ${totalRecorded > 0 ? totalRecorded : totalStudents} Tercatat',
                    valueColor: const Color(0xFF059669),
                  ),
                  StatCard(
                    title: 'Keterlambatan',
                    value: '${terlambatRate.toStringAsFixed(1)}%',
                    subtitle: isMonthlySchool ? '$terlambatCount Tercatat Bulan Ini' : '$terlambatCount Siswa Hari Ini',
                    valueColor: const Color(0xFFD97706),
                  ),
                  StatCard(
                    title: isMonthlySchool ? 'Pelanggaran Bulan Ini' : 'Pelanggaran Hari Ini',
                    value: '$totalViolations',
                    subtitle: isMonthlySchool ? 'Seluruh Siswa ($selectedMonthLabel)' : 'Rombel Guru Hari Ini',
                    valueColor: AppTheme.textPrimary,
                  ),
                  StatCard(
                    title: isMonthlySchool ? 'Kasus SP Bulan Ini' : 'Kasus SP Rombel',
                    value: '$spCasesCount',
                    subtitle: 'Dalam Pembinaan Khusus',
                    valueColor: const Color(0xFFE11D48),
                  ),
                ],
              ),
              const SizedBox(height: 14),

              // Grafik Kehadiran 1 Bulan (Hanya tampil di mode 1 Bulan Seluruh Siswa)
              if (isMonthlySchool) ...[
                AttendanceTrendChart(
                  data: _monthlyTrend,
                  targetRate: targetRate,
                  totalStudents: totalStudents,
                ),
                const SizedBox(height: 14),
              ],

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
