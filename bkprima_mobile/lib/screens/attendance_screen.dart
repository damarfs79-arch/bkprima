import 'dart:async';
import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:intl/intl.dart';
import 'package:provider/provider.dart';
import '../core/theme/app_theme.dart';
import '../core/widgets/searchable_bottom_sheet.dart';
import '../widgets/absence_alert_sheet.dart';
import '../models/attendance_model.dart';
import '../providers/attendance_provider.dart';

class AttendanceScreen extends StatefulWidget {
  final ValueChanged<int>? onNavigateToTab;

  const AttendanceScreen({
    super.key,
    this.onNavigateToTab,
  });

  @override
  State<AttendanceScreen> createState() => _AttendanceScreenState();
}

class _AttendanceScreenState extends State<AttendanceScreen> {
  final _searchController = TextEditingController();
  Timer? _debounce;
  String _activeFilter = 'Semua';

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) async {
      final provider = context.read<AttendanceProvider>();
      provider.resetStartupAlert();
      await provider.fetchAttendance();
      if (mounted && provider.absenceAlerts.isNotEmpty && !provider.hasShownStartupAlert) {
        provider.markStartupAlertShown();
        AbsenceAlertDialogs.showStartupModal(context, provider.absenceAlerts);
      }
    });
  }

  @override
  void dispose() {
    _debounce?.cancel();
    _searchController.dispose();
    super.dispose();
  }

  void _onSearchChanged(String query) {
    _debounce?.cancel();
    _debounce = Timer(const Duration(milliseconds: 300), () {
      if (mounted) {
        context.read<AttendanceProvider>().setSearchQuery(query);
      }
    });
  }

  Future<void> _openClassPicker(AttendanceProvider provider) async {
    final items = provider.availableClasses.map((cName) {
      return BottomSheetItem<String>(
        title: cName,
        value: cName,
      );
    }).toList(growable: false);

    final selected = await SearchableBottomSheet.show<String>(
      context: context,
      title: 'Pilih Rombel',
      searchHint: 'Cari kelas...',
      items: items,
      initialSelectedValue: provider.selectedClass,
    );

    if (selected != null && mounted) {
      provider.setClassFilter(selected);
    }
  }

  void _showAttendanceModal(AttendanceStudent student) {
    HapticFeedback.selectionClick();
    final provider = context.read<AttendanceProvider>();

    showModalBottomSheet(
      context: context,
      backgroundColor: Colors.transparent,
      isScrollControlled: true,
      builder: (ctx) {
        return Container(
          decoration: BoxDecoration(
            color: AppTheme.surface,
            borderRadius: const BorderRadius.vertical(top: Radius.circular(20)),
            border: Border.all(color: AppTheme.border, width: 1),
          ),
          padding: const EdgeInsets.fromLTRB(20, 12, 20, 24),
          child: SafeArea(
            top: false,
            child: Column(
              mainAxisSize: MainAxisSize.min,
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Center(
                  child: Container(
                    width: 36,
                    height: 4,
                    decoration: BoxDecoration(
                      color: AppTheme.borderDark,
                      borderRadius: BorderRadius.circular(2),
                    ),
                  ),
                ),
                const SizedBox(height: 14),
                Row(
                  children: [
                    Container(
                      width: 40,
                      height: 40,
                      decoration: BoxDecoration(
                        color: const Color(0xFFF1F5F9),
                        borderRadius: BorderRadius.circular(8),
                      ),
                      alignment: Alignment.center,
                      child: Text(
                        _getInitials(student.name),
                        style: const TextStyle(
                          fontFamily: AppTheme.fontFamily,
                          fontSize: 13,
                          fontWeight: FontWeight.w700,
                          color: Color(0xFF334155),
                        ),
                      ),
                    ),
                    const SizedBox(width: 12),
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(
                            student.name,
                            style: const TextStyle(
                              fontFamily: AppTheme.fontFamily,
                              fontSize: 14,
                              fontWeight: FontWeight.w600,
                              color: Color(0xFF0F172A),
                            ),
                            maxLines: 1,
                            overflow: TextOverflow.ellipsis,
                          ),
                          const SizedBox(height: 2),
                          Text(
                            '${student.className} • NISN: ${student.nisn}',
                            style: const TextStyle(
                              fontFamily: AppTheme.fontFamily,
                              fontSize: 11,
                              color: Color(0xFF64748B),
                            ),
                          ),
                        ],
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 16),
                _buildModalOption(
                  label: 'Hadir',
                  color: const Color(0xFF059669),
                  icon: Icons.check_circle_rounded,
                  isSelected: student.status == 'Hadir',
                  onTap: () async {
                    Navigator.pop(ctx);
                    await provider.updateStudentStatus(
                      studentId: student.id,
                      newStatus: 'Hadir',
                      checkInTime: '06:45',
                    );
                  },
                ),
                const SizedBox(height: 6),
                _buildModalOption(
                  label: 'Terlambat',
                  color: const Color(0xFFD97706),
                  icon: Icons.access_time_filled_rounded,
                  isSelected: student.status == 'Terlambat',
                  onTap: () async {
                    Navigator.pop(ctx);
                    await provider.updateStudentStatus(
                      studentId: student.id,
                      newStatus: 'Terlambat',
                    );
                  },
                ),
                const SizedBox(height: 6),
                _buildModalOption(
                  label: 'Izin',
                  color: const Color(0xFF2563EB),
                  icon: Icons.assignment_turned_in_rounded,
                  isSelected: student.status == 'Izin',
                  onTap: () async {
                    Navigator.pop(ctx);
                    await provider.updateStudentStatus(
                      studentId: student.id,
                      newStatus: 'Izin',
                    );
                  },
                ),
                const SizedBox(height: 6),
                _buildModalOption(
                  label: 'Sakit',
                  color: const Color(0xFF4F46E5),
                  icon: Icons.local_hospital_rounded,
                  isSelected: student.status == 'Sakit',
                  onTap: () async {
                    Navigator.pop(ctx);
                    await provider.updateStudentStatus(
                      studentId: student.id,
                      newStatus: 'Sakit',
                    );
                  },
                ),
                const SizedBox(height: 6),
                _buildModalOption(
                  label: 'Alpa',
                  color: const Color(0xFFE11D48),
                  icon: Icons.cancel_rounded,
                  isSelected: student.status == 'Alpa',
                  onTap: () async {
                    Navigator.pop(ctx);
                    await provider.updateStudentStatus(
                      studentId: student.id,
                      newStatus: 'Alpa',
                    );
                  },
                ),
              ],
            ),
          ),
        );
      },
    );
  }

  Widget _buildModalOption({
    required String label,
    required Color color,
    required IconData icon,
    required bool isSelected,
    required VoidCallback onTap,
  }) {
    return InkWell(
      onTap: () {
        HapticFeedback.selectionClick();
        onTap();
      },
      borderRadius: BorderRadius.circular(8),
      child: Container(
        padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 10),
        decoration: BoxDecoration(
          color: isSelected ? color.withValues(alpha: 0.08) : const Color(0xFFF8FAFC),
          borderRadius: BorderRadius.circular(8),
          border: Border.all(
            color: isSelected ? color : const Color(0xFFE2E8F0),
            width: isSelected ? 1.5 : 1,
          ),
        ),
        child: Row(
          children: [
            Icon(icon, size: 16, color: color),
            const SizedBox(width: 10),
            Expanded(
              child: Text(
                label,
                style: TextStyle(
                  fontFamily: AppTheme.fontFamily,
                  fontSize: 13,
                  fontWeight: isSelected ? FontWeight.w700 : FontWeight.w600,
                  color: const Color(0xFF0F172A),
                ),
              ),
            ),
            if (isSelected)
              Container(
                width: 6,
                height: 6,
                decoration: BoxDecoration(
                  color: color,
                  shape: BoxShape.circle,
                ),
              ),
          ],
        ),
      ),
    );
  }

  List<AttendanceStudent> _filterStudents(List<AttendanceStudent> list) {
    if (_activeFilter == 'Semua') return list;
    if (_activeFilter == 'Hadir') {
      return list.where((s) => s.status.toLowerCase() == 'hadir').toList();
    }
    if (_activeFilter == 'Terlambat') {
      return list.where((s) => s.status.toLowerCase().contains('terlambat')).toList();
    }
    if (_activeFilter == 'Izin') {
      return list.where((s) => s.status.toLowerCase().contains('izin')).toList();
    }
    if (_activeFilter == 'Sakit') {
      return list.where((s) => s.status.toLowerCase().contains('sakit')).toList();
    }
    if (_activeFilter == 'Alpa') {
      return list.where((s) => s.status.toLowerCase().contains('alpa') || s.status.toLowerCase().contains('alpha')).toList();
    }
    return list;
  }

  String _getInitials(String name) {
    final parts = name.trim().split(RegExp(r'\s+'));
    if (parts.isEmpty || parts[0].isEmpty) return '--';
    if (parts.length == 1) {
      return parts[0].substring(0, parts[0].length >= 2 ? 2 : 1).toUpperCase();
    }
    return '${parts[0][0]}${parts[1][0]}'.toUpperCase();
  }

  Color _getStatusColor(String status) {
    final st = status.toLowerCase();
    if (st == 'hadir') return const Color(0xFF059669);
    if (st.contains('terlambat')) return const Color(0xFFD97706);
    if (st.contains('izin')) return const Color(0xFF2563EB);
    if (st.contains('sakit')) return const Color(0xFF4F46E5);
    return const Color(0xFFE11D48);
  }

  @override
  Widget build(BuildContext context) {
    final provider = context.watch<AttendanceProvider>();
    final stats = provider.stats;
    final todayFormatted = DateFormat('EEEE, d MMM yyyy', 'id_ID').format(DateTime.now());
    final displayStudents = _filterStudents(provider.students);

    return Container(
      color: AppTheme.background,
      child: RefreshIndicator(
        onRefresh: () => provider.fetchAttendance(),
        color: AppTheme.primary,
        child: SingleChildScrollView(
          physics: const AlwaysScrollableScrollPhysics(),
          padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              // 1. Ringkasan Presensi (Header singkat + 3 kartu metrik kompak)
              Container(
                decoration: BoxDecoration(
                  color: AppTheme.surface,
                  borderRadius: BorderRadius.circular(12),
                  border: Border.all(color: AppTheme.border, width: 1),
                ),
                padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 12),
                child: Column(
                  children: [
                    // Top row: Presensi Hari Ini di kiri, tanggal ringkas di kanan
                    Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      crossAxisAlignment: CrossAxisAlignment.center,
                      children: [
                        const Text(
                          'Presensi Hari Ini',
                          style: TextStyle(
                            fontFamily: AppTheme.fontFamily,
                            fontSize: 13,
                            fontWeight: FontWeight.w700,
                            color: AppTheme.textPrimary,
                            letterSpacing: -0.2,
                          ),
                        ),
                        Text(
                          todayFormatted,
                          style: const TextStyle(
                            fontFamily: AppTheme.fontFamily,
                            fontSize: 11,
                            fontWeight: FontWeight.w400,
                            color: AppTheme.textSecondary,
                          ),
                        ),
                      ],
                    ),
                    const SizedBox(height: 10),
                    const Divider(height: 1, thickness: 1, color: AppTheme.border),
                    const SizedBox(height: 10),
                    // 3 kartu metrik kompak: Hadir, Terlambat, Alpa
                    IntrinsicHeight(
                      child: Row(
                        children: [
                          Expanded(
                            child: _buildMetricItem(
                              count: '${stats?.hadir ?? 0}',
                              label: 'Hadir',
                              color: const Color(0xFF059669),
                            ),
                          ),
                          const VerticalDivider(width: 1, thickness: 1, color: AppTheme.border),
                          Expanded(
                            child: _buildMetricItem(
                              count: '${stats?.terlambat ?? 0}',
                              label: 'Terlambat',
                              color: const Color(0xFFD97706),
                            ),
                          ),
                          const VerticalDivider(width: 1, thickness: 1, color: AppTheme.border),
                          Expanded(
                            child: _buildMetricItem(
                              count: '${stats?.alpaIzinSakit ?? 0}',
                              label: 'Alpa',
                              color: const Color(0xFFE11D48),
                            ),
                          ),
                        ],
                      ),
                    ),
                  ],
                ),
              ),
              const SizedBox(height: 10),

              // Banner Notifikasi Siswa >= 3 Hari Tidak Masuk
              if (provider.absenceAlerts.isNotEmpty) ...[
                InkWell(
                  onTap: () => AbsenceAlertDialogs.showListSheet(context, provider.absenceAlerts),
                  borderRadius: BorderRadius.circular(10),
                  child: Container(
                    width: double.infinity,
                    padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 10),
                    decoration: BoxDecoration(
                      color: AppTheme.dangerLight,
                      borderRadius: BorderRadius.circular(10),
                      border: Border.all(color: AppTheme.dangerBorder, width: 1),
                    ),
                    child: Row(
                      children: [
                        const Icon(
                          Icons.notifications_active_outlined,
                          size: 18,
                          color: AppTheme.danger,
                        ),
                        const SizedBox(width: 10),
                        Expanded(
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Text(
                                '${provider.absenceAlerts.length} Siswa Tidak Masuk >= 3 Hari',
                                style: const TextStyle(
                                  fontFamily: AppTheme.fontFamily,
                                  fontSize: 12,
                                  fontWeight: FontWeight.w700,
                                  color: AppTheme.dangerDark,
                                ),
                              ),
                              const SizedBox(height: 1),
                              const Text(
                                'Ketuk untuk melihat daftar siswa dan kontak wali murid',
                                style: TextStyle(
                                  fontFamily: AppTheme.fontFamily,
                                  fontSize: 10,
                                  color: AppTheme.danger,
                                ),
                              ),
                            ],
                          ),
                        ),
                        const Icon(
                          Icons.chevron_right_rounded,
                          size: 18,
                          color: AppTheme.danger,
                        ),
                      ],
                    ),
                  ),
                ),
                const SizedBox(height: 10),
              ],

              // 2. Pencarian & Filter Rombel
              Row(
                children: [
                  Expanded(
                    child: Container(
                      height: 40,
                      decoration: BoxDecoration(
                        color: AppTheme.surface,
                        borderRadius: BorderRadius.circular(10),
                        border: Border.all(color: AppTheme.border, width: 1),
                      ),
                      child: TextField(
                        controller: _searchController,
                        onChanged: _onSearchChanged,
                        style: const TextStyle(
                          fontFamily: AppTheme.fontFamily,
                          fontSize: 13,
                          fontWeight: FontWeight.w500,
                          color: AppTheme.textPrimary,
                        ),
                        decoration: InputDecoration(
                          hintText: 'Cari nama / NISN',
                          hintStyle: const TextStyle(
                            fontFamily: AppTheme.fontFamily,
                            fontSize: 12,
                            fontWeight: FontWeight.w400,
                            color: AppTheme.textMuted,
                          ),
                          prefixIcon: const Icon(Icons.search, size: 18, color: AppTheme.textSecondary),
                          suffixIcon: _searchController.text.isNotEmpty
                              ? IconButton(
                                  icon: const Icon(Icons.clear, size: 16, color: AppTheme.textSecondary),
                                  onPressed: () {
                                    _searchController.clear();
                                    provider.setSearchQuery('');
                                  },
                                  padding: EdgeInsets.zero,
                                  constraints: const BoxConstraints(minWidth: 28, minHeight: 28),
                                )
                              : null,
                          border: InputBorder.none,
                          contentPadding: const EdgeInsets.symmetric(vertical: 10),
                        ),
                      ),
                    ),
                  ),
                  const SizedBox(width: 8),
                  InkWell(
                    onTap: () => _openClassPicker(provider),
                    borderRadius: BorderRadius.circular(10),
                    child: Container(
                      height: 40,
                      padding: const EdgeInsets.symmetric(horizontal: 10),
                      decoration: BoxDecoration(
                        color: AppTheme.surface,
                        borderRadius: BorderRadius.circular(10),
                        border: Border.all(color: AppTheme.border, width: 1),
                      ),
                      child: Row(
                        mainAxisSize: MainAxisSize.min,
                        children: [
                          Text(
                            provider.selectedClass,
                            style: const TextStyle(
                              fontFamily: AppTheme.fontFamily,
                              fontSize: 11,
                              fontWeight: FontWeight.w600,
                              color: AppTheme.textPrimary,
                            ),
                          ),
                          const SizedBox(width: 4),
                          const Icon(Icons.keyboard_arrow_down_rounded, size: 16, color: AppTheme.textSecondary),
                        ],
                      ),
                    ),
                  ),
                ],
              ),
              const SizedBox(height: 10),

              // Filter Chips Horizontal (Semua, Hadir, Izin, Sakit, Alpa)
              SingleChildScrollView(
                scrollDirection: Axis.horizontal,
                physics: const BouncingScrollPhysics(),
                child: Row(
                  children: [
                    'Semua',
                    'Hadir',
                    'Izin',
                    'Sakit',
                    'Alpa',
                  ].map((status) {
                    final isSelected = _activeFilter == status;
                    return Padding(
                      padding: const EdgeInsets.only(right: 6),
                      child: InkWell(
                        onTap: () {
                          HapticFeedback.selectionClick();
                          setState(() {
                            _activeFilter = status;
                          });
                        },
                        borderRadius: BorderRadius.circular(8),
                        child: AnimatedContainer(
                          duration: const Duration(milliseconds: 150),
                          padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
                          decoration: BoxDecoration(
                            color: isSelected ? const Color(0xFF0F172A) : AppTheme.surface,
                            borderRadius: BorderRadius.circular(8),
                            border: Border.all(
                              color: isSelected ? const Color(0xFF0F172A) : AppTheme.border,
                              width: 1,
                            ),
                          ),
                          child: Text(
                            status,
                            style: TextStyle(
                              fontFamily: AppTheme.fontFamily,
                              fontSize: 11,
                              fontWeight: isSelected ? FontWeight.w600 : FontWeight.w500,
                              color: isSelected ? Colors.white : AppTheme.textSecondary,
                            ),
                          ),
                        ),
                      ),
                    );
                  }).toList(growable: false),
                ),
              ),
              const SizedBox(height: 12),

              // 4. Daftar Presensi Siswa (Material card dengan ListView.separated)
              if (provider.isLoading)
                Material(
                  color: AppTheme.surface,
                  shape: RoundedRectangleBorder(
                    borderRadius: BorderRadius.circular(12),
                    side: const BorderSide(color: AppTheme.border, width: 1),
                  ),
                  child: const Padding(
                    padding: EdgeInsets.all(32),
                    child: Center(
                      child: SizedBox(
                        width: 20,
                        height: 20,
                        child: CircularProgressIndicator(strokeWidth: 2, color: AppTheme.primary),
                      ),
                    ),
                  ),
                )
              else if (displayStudents.isEmpty)
                Material(
                  color: AppTheme.surface,
                  shape: RoundedRectangleBorder(
                    borderRadius: BorderRadius.circular(12),
                    side: const BorderSide(color: AppTheme.border, width: 1),
                  ),
                  child: const Padding(
                    padding: EdgeInsets.all(32),
                    child: Center(
                      child: Text(
                        'Tidak ada data siswa.',
                        style: TextStyle(
                          fontFamily: AppTheme.fontFamily,
                          fontSize: 12,
                          color: AppTheme.textSecondary,
                        ),
                      ),
                    ),
                  ),
                )
              else
                Material(
                  color: AppTheme.surface,
                  clipBehavior: Clip.antiAlias,
                  shape: RoundedRectangleBorder(
                    borderRadius: BorderRadius.circular(12),
                    side: const BorderSide(color: AppTheme.border, width: 1),
                  ),
                  child: ListView.separated(
                    shrinkWrap: true,
                    physics: const NeverScrollableScrollPhysics(),
                    itemCount: displayStudents.length,
                    separatorBuilder: (_, index) => const Divider(
                      height: 1,
                      thickness: 1,
                      color: Color(0xFFF1F5F9),
                    ),
                    itemBuilder: (context, index) {
                      final student = displayStudents[index];
                      final statusColor = _getStatusColor(student.status);

                      return InkWell(
                        onTap: () => _showAttendanceModal(student),
                        child: Padding(
                          padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 11),
                          child: Row(
                            children: [
                              // Avatar inisial di kiri
                              Container(
                                width: 36,
                                height: 36,
                                decoration: BoxDecoration(
                                  color: const Color(0xFFF1F5F9),
                                  borderRadius: BorderRadius.circular(8),
                                ),
                                alignment: Alignment.center,
                                child: Text(
                                  _getInitials(student.name),
                                  style: const TextStyle(
                                    fontFamily: AppTheme.fontFamily,
                                    fontSize: 12,
                                    fontWeight: FontWeight.w700,
                                    color: Color(0xFF334155),
                                  ),
                                ),
                              ),
                              const SizedBox(width: 12),

                              // Nama siswa dan kelas/NIS di tengah
                              Expanded(
                                child: Column(
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  children: [
                                    Text(
                                      student.name,
                                      style: const TextStyle(
                                        fontFamily: AppTheme.fontFamily,
                                        fontSize: 14,
                                        fontWeight: FontWeight.w600,
                                        color: Color(0xFF0F172A),
                                        letterSpacing: -0.2,
                                      ),
                                      maxLines: 1,
                                      overflow: TextOverflow.ellipsis,
                                    ),
                                    const SizedBox(height: 2),
                                    Text(
                                      '${student.className} • ${student.nisn}',
                                      style: const TextStyle(
                                        fontFamily: AppTheme.fontFamily,
                                        fontSize: 12,
                                        fontWeight: FontWeight.w400,
                                        color: Color(0xFF64748B),
                                      ),
                                      maxLines: 1,
                                      overflow: TextOverflow.ellipsis,
                                    ),
                                  ],
                                ),
                              ),
                              const SizedBox(width: 8),

                              // Badge status dan jam presensi di kanan
                              Column(
                                crossAxisAlignment: CrossAxisAlignment.end,
                                children: [
                                  Row(
                                    mainAxisSize: MainAxisSize.min,
                                    children: [
                                      Container(
                                        width: 6,
                                        height: 6,
                                        decoration: BoxDecoration(
                                          color: statusColor,
                                          shape: BoxShape.circle,
                                        ),
                                      ),
                                      const SizedBox(width: 5),
                                      Text(
                                        student.status,
                                        style: TextStyle(
                                          fontFamily: AppTheme.fontFamily,
                                          fontSize: 12,
                                          fontWeight: FontWeight.w600,
                                          color: statusColor,
                                        ),
                                      ),
                                    ],
                                  ),
                                  const SizedBox(height: 2),
                                  Text(
                                    '${student.checkInTime} WIB',
                                    style: const TextStyle(
                                      fontFamily: AppTheme.fontFamily,
                                      fontSize: 11,
                                      fontWeight: FontWeight.w400,
                                      color: Color(0xFF94A3B8),
                                    ),
                                  ),
                                ],
                              ),
                            ],
                          ),
                        ),
                      );
                    },
                  ),
                ),

              const SizedBox(height: 20),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildMetricItem({
    required String count,
    required String label,
    required Color color,
  }) {
    return Column(
      mainAxisAlignment: MainAxisAlignment.center,
      children: [
        Text(
          count,
          style: TextStyle(
            fontFamily: AppTheme.fontFamily,
            fontSize: 22,
            fontWeight: FontWeight.w700,
            color: color,
            letterSpacing: -0.5,
            fontFeatures: const [FontFeature.tabularFigures()],
          ),
        ),
        const SizedBox(height: 2),
        Text(
          label,
          style: const TextStyle(
            fontFamily: AppTheme.fontFamily,
            fontSize: 11,
            fontWeight: FontWeight.w500,
            color: AppTheme.textSecondary,
          ),
        ),
      ],
    );
  }
}
