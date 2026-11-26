import 'dart:io';
import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:provider/provider.dart';
import '../core/theme/app_theme.dart';
import '../core/widgets/app_surface.dart';
import '../core/widgets/app_text_field.dart';
import '../core/widgets/compact_photo_picker.dart';
import '../core/widgets/searchable_bottom_sheet.dart';
import '../core/widgets/selectable_chip_wrap.dart';
import '../providers/attendance_provider.dart';
import '../providers/violation_provider.dart';
import '../services/api_service.dart';
import '../widgets/rule_lookup_sheet.dart';

class InputViolationScreen extends StatefulWidget {
  const InputViolationScreen({super.key});

  @override
  State<InputViolationScreen> createState() => _InputViolationScreenState();
}

class _InputViolationScreenState extends State<InputViolationScreen> {
  final _formKey = GlobalKey<FormState>();

  final bool _isManualInput = false;
  int? _selectedStudentId;
  String _studentName = '';
  String _studentClass = 'X TKKR 1';

  String _category = 'Kedisiplinan';
  final _violationNameController = TextEditingController();
  final _pointsController = TextEditingController(text: '5');
  final _notesController = TextEditingController();
  String _penaltyAction = 'Pembinaan Lisan & Pengarahan BK';

  File? _selectedPhoto;
  int _selectedPointPreset = 5;

  static const List<String> _categoryTabs = [
    'Kedisiplinan',
    'Kerapian',
    'Ketertiban & Berat',
    'Lainnya',
  ];

  static const List<ChipItem<int>> _pointPresets = [
    ChipItem(label: 'Tanpa Poin', value: 0),
    ChipItem(label: '+5', value: 5),
    ChipItem(label: '+10', value: 10),
    ChipItem(label: '+15', value: 15),
    ChipItem(label: '+20', value: 20),
    ChipItem(label: '+25', value: 25),
    ChipItem(label: '+50', value: 50),
  ];

  List<String> _penaltyOptions = [
    'Pembinaan Lisan & Pengarahan BK',
    'Piket Kebersihan Lingkungan Sekolah',
    'Teguran Tertulis & Surat Perjanjian',
    'Pemanggilan Orang Tua (SP-1)',
    'Pemanggilan Orang Tua (SP-2)',
    'Skorsing Sementara (SP-3)',
  ];

  @override
  void initState() {
    super.initState();
    _loadSanctions();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      final att = context.read<AttendanceProvider>();
      if (att.students.isEmpty) {
        att.fetchAttendance();
      }
      context.read<ViolationProvider>().fetchViolations();
    });
  }

  @override
  void dispose() {
    _violationNameController.dispose();
    _pointsController.dispose();
    _notesController.dispose();
    super.dispose();
  }

  Future<void> _loadSanctions() async {
    final fetched = await ApiService.getSanctions();
    if (fetched.isNotEmpty && mounted) {
      setState(() {
        _penaltyOptions = fetched;
        if (!_penaltyOptions.contains(_penaltyAction)) {
          _penaltyAction = _penaltyOptions.first;
        }
      });
    }
  }

  void _setPoints(int points) {
    _selectedPointPreset = points;
    _pointsController.text = points == 0 ? '' : points.toString();
  }

  Future<void> _openStudentPicker() async {
    final att = context.read<AttendanceProvider>();
    if (att.students.isEmpty) {
      await att.fetchAttendance();
    }
    final students = att.students;

    if (!mounted) return;

    if (students.isEmpty) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Sedang memuat data siswa, silakan coba beberapa detik lagi.'),
          backgroundColor: AppTheme.warning,
        ),
      );
      return;
    }

    final items = students.map((s) {
      return BottomSheetItem<int>(
        title: s.name,
        subtitle: '${s.className} • NISN: ${s.nisn}',
        tag: s.className,
        value: s.id,
      );
    }).toList(growable: false);

    final selectedId = await SearchableBottomSheet.show<int>(
      context: context,
      title: 'Pilih Siswa Terlapor',
      searchHint: 'Cari nama siswa atau kelas...',
      items: items,
      initialSelectedValue: _selectedStudentId,
    );

    if (selectedId != null && mounted) {
      final selectedStudent = students.firstWhere(
        (s) => s.id == selectedId,
        orElse: () => students.first,
      );
      setState(() {
        _selectedStudentId = selectedStudent.id;
        _studentName = selectedStudent.name;
        _studentClass = selectedStudent.className;
      });
    }
  }

  Future<void> _openRuleLookup() async {
    final rule = await RuleLookupSheet.show(context);
    if (rule != null && mounted) {
      setState(() {
        _violationNameController.text = rule.description;
        _category = rule.category;
        _setPoints(rule.points);
        _penaltyAction = rule.defaultPenalty;
      });
    }
  }

  void _submitForm() async {
    if (!_formKey.currentState!.validate()) return;
    _formKey.currentState!.save();

    if (!_isManualInput && (_selectedStudentId == null || _studentName.isEmpty)) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Silakan pilih nama siswa yang dilaporkan.'),
          backgroundColor: AppTheme.danger,
        ),
      );
      return;
    }

    final provider = context.read<ViolationProvider>();
    final points = int.tryParse(_pointsController.text.trim()) ?? 0;

    final success = await provider.createViolation(
      studentId: _isManualInput ? null : _selectedStudentId,
      studentName: _studentName.trim(),
      studentClass: _studentClass.trim(),
      category: _category,
      violationName: _violationNameController.text.trim(),
      points: points,
      penaltyAction: _penaltyAction,
      notes: _notesController.text.trim(),
      photoFile: _selectedPhoto,
    );

    if (mounted) {
      if (success) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(
            content: Text('Catatan pelanggaran siswa berhasil tersimpan ke sistem BK.'),
            backgroundColor: AppTheme.success,
          ),
        );
        _violationNameController.clear();
        _notesController.clear();
        setState(() {
          _selectedStudentId = null;
          _studentName = '';
          _selectedPhoto = null;
          _setPoints(5);
        });
      } else {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text(provider.message ?? 'Gagal menyimpan catatan pelanggaran.'),
            backgroundColor: AppTheme.danger,
          ),
        );
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    final violationProvider = context.watch<ViolationProvider>();
    final bottomInset = MediaQuery.of(context).viewInsets.bottom;

    return Container(
      color: AppTheme.background,
      child: SafeArea(
        child: Column(
          children: [
            // Scrollable Form Body
            Expanded(
              child: SingleChildScrollView(
                physics: const AlwaysScrollableScrollPhysics(),
                padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 16),
                child: Form(
                  key: _formKey,
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      // Header Screen
                      Row(
                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                        children: [
                          const Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Text(
                                'Catat Pelanggaran',
                                style: TextStyle(
                                  fontFamily: AppTheme.fontFamily,
                                  fontSize: 18,
                                  fontWeight: FontWeight.w700,
                                  color: AppTheme.textPrimary,
                                  letterSpacing: -0.3,
                                ),
                              ),
                              SizedBox(height: 4),
                              Text(
                                'Pencatatan kasus tata tertib & skor kedisiplinan',
                                style: TextStyle(
                                  fontFamily: AppTheme.fontFamily,
                                  fontSize: 12,
                                  color: AppTheme.textSecondary,
                                ),
                              ),
                            ],
                          ),
                          InkWell(
                            onTap: _openRuleLookup,
                            borderRadius: BorderRadius.circular(8),
                            child: Container(
                              padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
                              decoration: BoxDecoration(
                                color: AppTheme.primaryLight,
                                borderRadius: BorderRadius.circular(8),
                                border: Border.all(color: AppTheme.primary.withValues(alpha: 0.2)),
                              ),
                              child: const Row(
                                mainAxisSize: MainAxisSize.min,
                                children: [
                                  Icon(Icons.menu_book_outlined, size: 16, color: AppTheme.primary),
                                  SizedBox(width: 4),
                                  Text(
                                    'Buku Tatib',
                                    style: TextStyle(
                                      fontFamily: AppTheme.fontFamily,
                                      fontSize: 12,
                                      fontWeight: FontWeight.w600,
                                      color: AppTheme.primary,
                                    ),
                                  ),
                                ],
                              ),
                            ),
                          ),
                        ],
                      ),
                      const SizedBox(height: 16),

                      // Card 1: Identitas Siswa
                      AppSurface(
                        padding: const EdgeInsets.all(16),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            const Text(
                              'Identitas Siswa',
                              style: TextStyle(
                                fontFamily: AppTheme.fontFamily,
                                fontSize: 14,
                                fontWeight: FontWeight.w700,
                                color: AppTheme.textPrimary,
                              ),
                            ),
                            const SizedBox(height: 12),

                            if (!_isManualInput) ...[
                              InkWell(
                                onTap: _openStudentPicker,
                                borderRadius: BorderRadius.circular(8),
                                child: Container(
                                  width: double.infinity,
                                  padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
                                  decoration: BoxDecoration(
                                    color: AppTheme.background,
                                    borderRadius: BorderRadius.circular(8),
                                    border: Border.all(
                                      color: _selectedStudentId != null ? AppTheme.primary : AppTheme.border,
                                      width: _selectedStudentId != null ? 1.5 : 1.0,
                                    ),
                                  ),
                                  child: Row(
                                    children: [
                                      Icon(
                                        Icons.person_search_outlined,
                                        size: 20,
                                        color: _selectedStudentId != null ? AppTheme.primary : AppTheme.textSecondary,
                                      ),
                                      const SizedBox(width: 12),
                                      Expanded(
                                        child: Column(
                                          crossAxisAlignment: CrossAxisAlignment.start,
                                          children: [
                                            Text(
                                              _studentName.isNotEmpty ? _studentName : 'Pilih Nama Siswa...',
                                              style: TextStyle(
                                                fontFamily: AppTheme.fontFamily,
                                                fontSize: 14,
                                                fontWeight: _studentName.isNotEmpty ? FontWeight.w700 : FontWeight.w400,
                                                color: _studentName.isNotEmpty ? AppTheme.textPrimary : AppTheme.textMuted,
                                              ),
                                            ),
                                            if (_studentClass.isNotEmpty && _studentName.isNotEmpty) ...[
                                              const SizedBox(height: 4),
                                              Text(
                                                'Kelas: $_studentClass',
                                                style: const TextStyle(
                                                  fontFamily: AppTheme.fontFamily,
                                                  fontSize: 12,
                                                  color: AppTheme.textSecondary,
                                                ),
                                              ),
                                            ],
                                          ],
                                        ),
                                      ),
                                      const Icon(Icons.keyboard_arrow_down_rounded, size: 20, color: AppTheme.textMuted),
                                    ],
                                  ),
                                ),
                              ),
                            ] else ...[
                              AppTextField(
                                label: 'Nama Lengkap Siswa',
                                hintText: 'Ketik nama lengkap siswa...',
                                initialValue: _studentName,
                                onChanged: (val) => _studentName = val,
                                validator: (val) => val == null || val.trim().isEmpty ? 'Nama siswa wajib diisi' : null,
                              ),
                              const SizedBox(height: 12),
                              AppTextField(
                                label: 'Kelas / Rombel Siswa',
                                hintText: 'Contoh: X TKKR 1, XI RPL 1',
                                initialValue: _studentClass,
                                onChanged: (val) => _studentClass = val,
                                validator: (val) => val == null || val.trim().isEmpty ? 'Kelas wajib diisi' : null,
                              ),
                            ],
                          ],
                        ),
                      ),
                      const SizedBox(height: 12),

                      // Card 2: Pelanggaran & Kategori
                      AppSurface(
                        padding: const EdgeInsets.all(16),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            const Text(
                              'Jenis Pelanggaran',
                              style: TextStyle(
                                fontFamily: AppTheme.fontFamily,
                                fontSize: 14,
                                fontWeight: FontWeight.w700,
                                color: AppTheme.textPrimary,
                              ),
                            ),
                            const SizedBox(height: 12),

                            // Kategori Selector
                            const Text(
                              'Kategori Pelanggaran:',
                              style: TextStyle(
                                fontFamily: AppTheme.fontFamily,
                                fontSize: 12,
                                fontWeight: FontWeight.w500,
                                color: AppTheme.textSecondary,
                              ),
                            ),
                            const SizedBox(height: 8),
                            SingleChildScrollView(
                              scrollDirection: Axis.horizontal,
                              child: Row(
                                children: _categoryTabs.map((cat) {
                                  final isSelected = _category == cat;
                                  return Padding(
                                    padding: const EdgeInsets.only(right: 8),
                                    child: InkWell(
                                      onTap: () {
                                        HapticFeedback.selectionClick();
                                        setState(() {
                                          _category = cat;
                                        });
                                      },
                                      borderRadius: BorderRadius.circular(8),
                                      child: Container(
                                        padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
                                        decoration: BoxDecoration(
                                          color: isSelected ? AppTheme.textPrimary : AppTheme.surfaceMuted,
                                          borderRadius: BorderRadius.circular(8),
                                          border: Border.all(
                                            color: isSelected ? AppTheme.textPrimary : AppTheme.border,
                                            width: 1,
                                          ),
                                        ),
                                        child: Text(
                                          cat,
                                          style: TextStyle(
                                            fontFamily: AppTheme.fontFamily,
                                            fontSize: 12,
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

                            AppTextField(
                              controller: _violationNameController,
                              label: 'Nama Kasus / Pelanggaran',
                              hintText: 'Ketik nama pelanggaran oleh siswa...',
                              validator: (val) => val == null || val.trim().isEmpty ? 'Nama jenis pelanggaran wajib diisi' : null,
                            ),
                            const SizedBox(height: 12),

                            AppTextField(
                              controller: _notesController,
                              label: 'Catatan Kronologi / Saksi (Opsional)',
                              hintText: 'Tuliskan lokasi, alasan siswa, atau keterangan saksi...',
                              maxLines: 2,
                            ),
                          ],
                        ),
                      ),
                      const SizedBox(height: 12),

                      // Card 3: Poin & Tindak Lanjut Sanksi
                      AppSurface(
                        padding: const EdgeInsets.all(16),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            const Text(
                              'Poin & Tindakan Pembinaan',
                              style: TextStyle(
                                fontFamily: AppTheme.fontFamily,
                                fontSize: 14,
                                fontWeight: FontWeight.w700,
                                color: AppTheme.textPrimary,
                              ),
                            ),
                            const SizedBox(height: 12),

                            // Preset Poin
                            SelectableChipWrap<int>(
                              items: _pointPresets,
                              selectedValue: _selectedPointPreset,
                              onSelected: (val) {
                                setState(() {
                                  _setPoints(val);
                                });
                              },
                            ),
                            const SizedBox(height: 12),

                            Row(
                              children: [
                                Expanded(
                                  child: AppTextField(
                                    controller: _pointsController,
                                    label: 'Skor Poin Tatib',
                                    hintText: '0',
                                    keyboardType: TextInputType.number,
                                    onChanged: (val) {
                                      final parsed = int.tryParse(val) ?? 0;
                                      setState(() {
                                        _selectedPointPreset = parsed;
                                      });
                                    },
                                  ),
                                ),
                                const SizedBox(width: 12),
                                Expanded(
                                  child: Container(
                                    padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 12),
                                    margin: const EdgeInsets.only(top: 20),
                                    decoration: BoxDecoration(
                                      color: _selectedPointPreset > 0 ? AppTheme.dangerLight : AppTheme.successLight,
                                      borderRadius: BorderRadius.circular(8),
                                      border: Border.all(
                                        color: _selectedPointPreset > 0 ? AppTheme.dangerBorder : AppTheme.successBorder,
                                        width: 1,
                                      ),
                                    ),
                                    alignment: Alignment.center,
                                    child: Text(
                                      _selectedPointPreset > 0
                                          ? '+$_selectedPointPreset Poin Pelanggaran'
                                          : 'Tanpa Poin (Pembinaan)',
                                      style: TextStyle(
                                        fontFamily: AppTheme.fontFamily,
                                        fontSize: 12,
                                        fontWeight: FontWeight.w700,
                                        color: _selectedPointPreset > 0 ? AppTheme.danger : AppTheme.success,
                                      ),
                                    ),
                                  ),
                                ),
                              ],
                            ),
                            const SizedBox(height: 12),

                            // Tindakan Sanksi Dropdown
                            const Text(
                              'Tindakan Sanksi / Pembinaan:',
                              style: TextStyle(
                                fontFamily: AppTheme.fontFamily,
                                fontSize: 12,
                                fontWeight: FontWeight.w500,
                                color: AppTheme.textSecondary,
                              ),
                            ),
                            const SizedBox(height: 8),
                            Container(
                              padding: const EdgeInsets.symmetric(horizontal: 12),
                              decoration: BoxDecoration(
                                color: AppTheme.background,
                                borderRadius: BorderRadius.circular(8),
                                border: Border.all(color: AppTheme.border, width: 1),
                              ),
                              child: DropdownButtonHideUnderline(
                                child: DropdownButton<String>(
                                  value: _penaltyOptions.contains(_penaltyAction) ? _penaltyAction : _penaltyOptions.first,
                                  isExpanded: true,
                                  icon: const Icon(Icons.keyboard_arrow_down_rounded, size: 18, color: AppTheme.textMuted),
                                  style: const TextStyle(
                                    fontFamily: AppTheme.fontFamily,
                                    fontSize: 12,
                                    fontWeight: FontWeight.w600,
                                    color: AppTheme.textPrimary,
                                  ),
                                  items: _penaltyOptions.map((opt) {
                                    return DropdownMenuItem(
                                      value: opt,
                                      child: Text(opt),
                                    );
                                  }).toList(growable: false),
                                  onChanged: (val) {
                                    if (val != null) {
                                      setState(() {
                                        _penaltyAction = val;
                                      });
                                    }
                                  },
                                ),
                              ),
                            ),
                            const SizedBox(height: 16),

                            // Photo Picker
                            CompactPhotoPicker(
                              photo: _selectedPhoto,
                              onPhotoChanged: (newFile) {
                                setState(() {
                                  _selectedPhoto = newFile;
                                });
                              },
                            ),
                          ],
                        ),
                      ),
                      const SizedBox(height: 16),
                    ],
                  ),
                ),
              ),
            ),

            // Sticky Bottom Submit Button
            Container(
              padding: EdgeInsets.only(
                left: 16,
                right: 16,
                top: 8,
                bottom: bottomInset > 0 ? bottomInset + 8 : 12,
              ),
              decoration: const BoxDecoration(
                color: AppTheme.surface,
                border: Border(
                  top: BorderSide(color: AppTheme.border, width: 1),
                ),
              ),
              child: SizedBox(
                width: double.infinity,
                child: ElevatedButton(
                  onPressed: violationProvider.isSubmitting ? null : _submitForm,
                  style: ElevatedButton.styleFrom(
                    backgroundColor: AppTheme.primary,
                    disabledBackgroundColor: AppTheme.primary.withValues(alpha: 0.6),
                    padding: const EdgeInsets.symmetric(vertical: 12),
                    shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(8),
                    ),
                    elevation: 0,
                  ),
                  child: violationProvider.isSubmitting
                      ? const SizedBox(
                          height: 16,
                          width: 16,
                          child: CircularProgressIndicator(
                            strokeWidth: 2,
                            color: Colors.white,
                          ),
                        )
                      : const Text(
                          'Simpan Catatan Pelanggaran',
                          style: TextStyle(
                            fontFamily: AppTheme.fontFamily,
                            fontSize: 14,
                            fontWeight: FontWeight.w700,
                            color: Colors.white,
                          ),
                        ),
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }
}
