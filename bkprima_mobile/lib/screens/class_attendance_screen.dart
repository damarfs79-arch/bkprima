import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:provider/provider.dart';
import '../core/theme/app_theme.dart';
import '../core/widgets/searchable_bottom_sheet.dart';
import '../models/attendance_model.dart';
import '../providers/attendance_provider.dart';

class ClassAttendanceScreen extends StatefulWidget {
  const ClassAttendanceScreen({super.key});

  @override
  State<ClassAttendanceScreen> createState() => _ClassAttendanceScreenState();
}

class _ClassAttendanceScreenState extends State<ClassAttendanceScreen> {
  final TextEditingController _searchController = TextEditingController();
  String _selectedStatusFilter = 'Semua';

  @override
  void dispose() {
    _searchController.dispose();
    super.dispose();
  }

  void _showAttendanceModal(BuildContext context, AttendanceStudent student) {
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
                // Handle bar
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
                const SizedBox(height: 16),

                // Student Info
                Row(
                  children: [
                    Container(
                      width: 44,
                      height: 44,
                      decoration: BoxDecoration(
                        color: const Color(0xFFF1F5F9),
                        borderRadius: BorderRadius.circular(10),
                      ),
                      alignment: Alignment.center,
                      child: Text(
                        _getInitials(student.name),
                        style: const TextStyle(
                          fontFamily: AppTheme.fontFamily,
                          fontSize: 14,
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
                              fontSize: 15,
                              fontWeight: FontWeight.w700,
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
                              fontSize: 12,
                              color: Color(0xFF64748B),
                            ),
                          ),
                        ],
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 20),

                const Text(
                  'PILIH STATUS KEHADIRAN SISWA',
                  style: TextStyle(
                    fontFamily: AppTheme.fontFamily,
                    fontSize: 11,
                    fontWeight: FontWeight.w700,
                    color: Color(0xFF64748B),
                    letterSpacing: 0.5,
                  ),
                ),
                const SizedBox(height: 10),

                // 5 Status Options
                _buildStatusOption(
                  label: 'Hadir Tepat Waktu',
                  status: 'Hadir',
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
                    if (context.mounted) {
                      _showSnackBar(context, '${student.name} ditandai Hadir');
                    }
                  },
                ),
                const SizedBox(height: 6),
                _buildStatusOption(
                  label: 'Terlambat Masuk',
                  status: 'Terlambat',
                  color: const Color(0xFFD97706),
                  icon: Icons.access_time_filled_rounded,
                  isSelected: student.status == 'Terlambat',
                  onTap: () async {
                    Navigator.pop(ctx);
                    await provider.updateStudentStatus(
                      studentId: student.id,
                      newStatus: 'Terlambat',
                    );
                    if (context.mounted) {
                      _showSnackBar(context, '${student.name} ditandai Terlambat');
                    }
                  },
                ),
                const SizedBox(height: 6),
                _buildStatusOption(
                  label: 'Izin (Keluarga / Dinas)',
                  status: 'Izin',
                  color: const Color(0xFF2563EB),
                  icon: Icons.assignment_turned_in_rounded,
                  isSelected: student.status == 'Izin',
                  onTap: () async {
                    Navigator.pop(ctx);
                    await provider.updateStudentStatus(
                      studentId: student.id,
                      newStatus: 'Izin',
                    );
                    if (context.mounted) {
                      _showSnackBar(context, '${student.name} ditandai Izin');
                    }
                  },
                ),
                const SizedBox(height: 6),
                _buildStatusOption(
                  label: 'Sakit (Surat Dokter / Wali)',
                  status: 'Sakit',
                  color: const Color(0xFF4F46E5),
                  icon: Icons.local_hospital_rounded,
                  isSelected: student.status == 'Sakit',
                  onTap: () async {
                    Navigator.pop(ctx);
                    await provider.updateStudentStatus(
                      studentId: student.id,
                      newStatus: 'Sakit',
                    );
                    if (context.mounted) {
                      _showSnackBar(context, '${student.name} ditandai Sakit');
                    }
                  },
                ),
                const SizedBox(height: 6),
                _buildStatusOption(
                  label: 'Alpa (Tanpa Keterangan)',
                  status: 'Alpa',
                  color: const Color(0xFFE11D48),
                  icon: Icons.cancel_rounded,
                  isSelected: student.status == 'Alpa',
                  onTap: () async {
                    Navigator.pop(ctx);
                    await provider.updateStudentStatus(
                      studentId: student.id,
                      newStatus: 'Alpa',
                    );
                    if (context.mounted) {
                      _showSnackBar(context, '${student.name} ditandai Alpa');
                    }
                  },
                ),
              ],
            ),
          ),
        );
      },
    );
  }

  Widget _buildStatusOption({
    required String label,
    required String status,
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
      borderRadius: BorderRadius.circular(10),
      child: Container(
        padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 11),
        decoration: BoxDecoration(
          color: isSelected ? color.withValues(alpha: 0.08) : const Color(0xFFF8FAFC),
          borderRadius: BorderRadius.circular(10),
          border: Border.all(
            color: isSelected ? color : const Color(0xFFE2E8F0),
            width: isSelected ? 1.5 : 1,
          ),
        ),
        child: Row(
          children: [
            Icon(icon, size: 18, color: color),
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
                padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 3),
                decoration: BoxDecoration(
                  color: color,
                  borderRadius: BorderRadius.circular(6),
                ),
                child: const Text(
                  'Aktif',
                  style: TextStyle(
                    fontFamily: AppTheme.fontFamily,
                    fontSize: 10,
                    fontWeight: FontWeight.w700,
                    color: Colors.white,
                  ),
                ),
              ),
          ],
        ),
      ),
    );
  }

  void _showSnackBar(BuildContext context, String message) {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        content: Text(
          message,
          style: const TextStyle(
            fontFamily: AppTheme.fontFamily,
            fontSize: 12,
            fontWeight: FontWeight.w600,
          ),
        ),
        backgroundColor: const Color(0xFF0F172A),
        behavior: SnackBarBehavior.floating,
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(8)),
        duration: const Duration(seconds: 2),
      ),
    );
  }

  String _getInitials(String name) {
    final parts = name.trim().split(RegExp(r'\s+'));
    if (parts.isEmpty) return '??';
    if (parts.length == 1) return parts[0].substring(0, parts[0].length >= 2 ? 2 : 1).toUpperCase();
    return '${parts[0][0]}${parts[1][0]}'.toUpperCase();
  }

  Color _getStatusColor(String status) {
    switch (status) {
      case 'Hadir':
        return const Color(0xFF059669);
      case 'Terlambat':
        return const Color(0xFFD97706);
      case 'Izin':
        return const Color(0xFF2563EB);
      case 'Sakit':
        return const Color(0xFF4F46E5);
      case 'Alpa':
        return const Color(0xFFE11D48);
      default:
        return const Color(0xFF64748B);
    }
  }

  @override
  Widget build(BuildContext context) {
    final provider = context.watch<AttendanceProvider>();
    final stats = provider.stats;
    final students = provider.students;

    final filteredStudents = students.where((s) {
      if (_selectedStatusFilter != 'Semua' && s.status != _selectedStatusFilter) {
        return false;
      }
      return true;
    }).toList();

    return Scaffold(
      backgroundColor: AppTheme.background,
      body: RefreshIndicator(
        onRefresh: () => provider.fetchAttendance(),
        color: AppTheme.primary,
        child: SingleChildScrollView(
          physics: const AlwaysScrollableScrollPhysics(),
          padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              // Header
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      const Text(
                        'Presensi Kelas Siswa',
                        style: TextStyle(
                          fontFamily: AppTheme.fontFamily,
                          fontSize: 20,
                          fontWeight: FontWeight.w800,
                          color: AppTheme.textPrimary,
                          letterSpacing: -0.4,
                        ),
                      ),
                      const SizedBox(height: 2),
                      const Text(
                        'Absen Langsung Android • Sinkron Realtime ke Web',
                        style: TextStyle(
                          fontFamily: AppTheme.fontFamily,
                          fontSize: 11,
                          fontWeight: FontWeight.w500,
                          color: AppTheme.textMuted,
                        ),
                      ),
                    ],
                  ),
                  InkWell(
                    onTap: () async {
                      HapticFeedback.selectionClick();
                      final confirmed = await showDialog<bool>(
                        context: context,
                        builder: (ctx) => AlertDialog(
                          title: const Text('Tandai Semua Hadir?'),
                          content: Text(
                            'Apakah Anda ingin menandai semua siswa di rombel "${provider.selectedClass}" hadir tepat waktu?',
                          ),
                          actions: [
                            TextButton(
                              onPressed: () => Navigator.pop(ctx, false),
                              child: const Text('Batal'),
                            ),
                            ElevatedButton(
                              onPressed: () => Navigator.pop(ctx, true),
                              style: ElevatedButton.styleFrom(
                                backgroundColor: const Color(0xFF059669),
                              ),
                              child: const Text('Tandai Hadir'),
                            ),
                          ],
                        ),
                      );

                      if (confirmed == true && context.mounted) {
                        final ok = await provider.markAllPresent();
                        if (context.mounted && ok) {
                          _showSnackBar(context, 'Seluruh siswa berhasil ditandai Hadir');
                        }
                      }
                    },
                    borderRadius: BorderRadius.circular(8),
                    child: Container(
                      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 7),
                      decoration: BoxDecoration(
                        color: const Color(0xFFECFDF5),
                        borderRadius: BorderRadius.circular(8),
                        border: Border.all(color: const Color(0xFFA7F3D0)),
                      ),
                      child: const Row(
                        children: [
                          Icon(Icons.done_all_rounded, size: 14, color: Color(0xFF059669)),
                          SizedBox(width: 5),
                          Text(
                            'Semua Hadir',
                            style: TextStyle(
                              fontFamily: AppTheme.fontFamily,
                              fontSize: 11,
                              fontWeight: FontWeight.w700,
                              color: Color(0xFF059669),
                            ),
                          ),
                        ],
                      ),
                    ),
                  ),
                ],
              ),
              const SizedBox(height: 12),

              // Rombel Picker & Search
              Row(
                children: [
                  Expanded(
                    child: Container(
                      height: 42,
                      decoration: BoxDecoration(
                        color: AppTheme.surface,
                        borderRadius: BorderRadius.circular(10),
                        border: Border.all(color: AppTheme.border, width: 1),
                      ),
                      child: TextField(
                        controller: _searchController,
                        style: const TextStyle(
                          fontFamily: AppTheme.fontFamily,
                          fontSize: 13,
                          color: AppTheme.textPrimary,
                        ),
                        decoration: InputDecoration(
                          hintText: 'Cari nama atau NISN...',
                          hintStyle: const TextStyle(
                            fontFamily: AppTheme.fontFamily,
                            fontSize: 12,
                            color: AppTheme.textMuted,
                          ),
                          prefixIcon: const Icon(Icons.search, size: 18, color: AppTheme.textMuted),
                          border: InputBorder.none,
                          contentPadding: const EdgeInsets.symmetric(vertical: 10),
                          suffixIcon: _searchController.text.isNotEmpty
                              ? IconButton(
                                  icon: const Icon(Icons.clear, size: 16, color: AppTheme.textMuted),
                                  onPressed: () {
                                    _searchController.clear();
                                    provider.setSearchQuery('');
                                    setState(() {});
                                  },
                                )
                              : null,
                        ),
                        onChanged: (val) {
                          provider.setSearchQuery(val);
                          setState(() {});
                        },
                      ),
                    ),
                  ),
                  const SizedBox(width: 8),
                  InkWell(
                    onTap: () async {
                      HapticFeedback.selectionClick();
                      final items = provider.availableClasses.map((c) {
                        return BottomSheetItem<String>(title: c, value: c);
                      }).toList();

                      final selected = await SearchableBottomSheet.show<String>(
                        context: context,
                        title: 'Pilih Rombel / Kelas',
                        items: items,
                        initialSelectedValue: provider.selectedClass,
                      );

                      if (selected != null && mounted) {
                        provider.setClassFilter(selected);
                      }
                    },
                    borderRadius: BorderRadius.circular(10),
                    child: Container(
                      height: 42,
                      padding: const EdgeInsets.symmetric(horizontal: 12),
                      decoration: BoxDecoration(
                        color: AppTheme.surface,
                        borderRadius: BorderRadius.circular(10),
                        border: Border.all(color: AppTheme.border, width: 1),
                      ),
                      child: Row(
                        children: [
                          Text(
                            provider.selectedClass,
                            style: const TextStyle(
                              fontFamily: AppTheme.fontFamily,
                              fontSize: 12,
                              fontWeight: FontWeight.w600,
                              color: AppTheme.textPrimary,
                            ),
                          ),
                          const SizedBox(width: 4),
                          const Icon(Icons.keyboard_arrow_down_rounded, size: 18, color: AppTheme.textMuted),
                        ],
                      ),
                    ),
                  ),
                ],
              ),
              const SizedBox(height: 12),

              // Filter Chips Status
              SingleChildScrollView(
                scrollDirection: Axis.horizontal,
                child: Row(
                  children: [
                    _buildFilterChip('Semua', 'Semua'),
                    const SizedBox(width: 6),
                    _buildFilterChip('Hadir (${stats?.hadir ?? 0})', 'Hadir'),
                    const SizedBox(width: 6),
                    _buildFilterChip('Terlambat (${stats?.terlambat ?? 0})', 'Terlambat'),
                    const SizedBox(width: 6),
                    _buildFilterChip('Alpa', 'Alpa'),
                    const SizedBox(width: 6),
                    _buildFilterChip('Izin / Sakit', 'Izin'),
                  ],
                ),
              ),
              const SizedBox(height: 14),

              // Student List with direct tap-to-attend
              if (provider.isLoading)
                Container(
                  padding: const EdgeInsets.all(32),
                  decoration: BoxDecoration(
                    color: AppTheme.surface,
                    borderRadius: BorderRadius.circular(12),
                    border: Border.all(color: AppTheme.border, width: 1),
                  ),
                  child: const Center(
                    child: CircularProgressIndicator(strokeWidth: 2, color: AppTheme.primary),
                  ),
                )
              else if (filteredStudents.isEmpty)
                Container(
                  padding: const EdgeInsets.all(32),
                  decoration: BoxDecoration(
                    color: AppTheme.surface,
                    borderRadius: BorderRadius.circular(12),
                    border: Border.all(color: AppTheme.border, width: 1),
                  ),
                  child: const Center(
                    child: Text(
                      'Tidak ada siswa ditemukan.',
                      style: TextStyle(
                        fontFamily: AppTheme.fontFamily,
                        fontSize: 13,
                        color: AppTheme.textSecondary,
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
                    itemCount: filteredStudents.length,
                    separatorBuilder: (_, index) => const Divider(
                      height: 1,
                      thickness: 1,
                      color: Color(0xFFF1F5F9),
                    ),
                    itemBuilder: (context, index) {
                      final student = filteredStudents[index];
                      final statusColor = _getStatusColor(student.status);

                      return InkWell(
                        onTap: () => _showAttendanceModal(context, student),
                        child: Padding(
                          padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 11),
                          child: Row(
                            children: [
                              // Avatar Initials
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

                              // Name & Class
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
                                        color: Color(0xFF64748B),
                                      ),
                                    ),
                                  ],
                                ),
                              ),
                              const SizedBox(width: 8),

                              // Status Badge with Tap Action
                              Container(
                                padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 5),
                                decoration: BoxDecoration(
                                  color: statusColor.withValues(alpha: 0.1),
                                  borderRadius: BorderRadius.circular(6),
                                  border: Border.all(color: statusColor.withValues(alpha: 0.3)),
                                ),
                                child: Row(
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
                                        fontSize: 11,
                                        fontWeight: FontWeight.w700,
                                        color: statusColor,
                                      ),
                                    ),
                                    const SizedBox(width: 4),
                                    Icon(
                                      Icons.edit_outlined,
                                      size: 12,
                                      color: statusColor,
                                    ),
                                  ],
                                ),
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

  Widget _buildFilterChip(String label, String value) {
    final isSelected = _selectedStatusFilter == value;

    return InkWell(
      onTap: () {
        HapticFeedback.selectionClick();
        setState(() {
          _selectedStatusFilter = value;
        });
      },
      borderRadius: BorderRadius.circular(8),
      child: Container(
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
          label,
          style: TextStyle(
            fontFamily: AppTheme.fontFamily,
            fontSize: 12,
            fontWeight: isSelected ? FontWeight.w700 : FontWeight.w500,
            color: isSelected ? Colors.white : AppTheme.textSecondary,
          ),
        ),
      ),
    );
  }
}
