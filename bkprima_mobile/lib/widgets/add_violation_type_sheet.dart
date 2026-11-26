import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import '../core/theme/app_theme.dart';
import '../core/widgets/app_text_field.dart';
import '../models/violation_type_model.dart';
import '../services/api_service.dart';
import '../services/violation_type_service.dart';

class AddViolationTypeSheet extends StatefulWidget {
  final String initialCategory;

  const AddViolationTypeSheet({
    super.key,
    this.initialCategory = 'Kedisiplinan',
  });

  static Future<ViolationTypeItem?> show(
    BuildContext context, {
    String initialCategory = 'Kedisiplinan',
  }) {
    return showModalBottomSheet<ViolationTypeItem>(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      builder: (ctx) => AddViolationTypeSheet(initialCategory: initialCategory),
    );
  }

  @override
  State<AddViolationTypeSheet> createState() => _AddViolationTypeSheetState();
}

class _AddViolationTypeSheetState extends State<AddViolationTypeSheet> {
  final _formKey = GlobalKey<FormState>();
  final _nameController = TextEditingController();
  final _pointsController = TextEditingController(text: '5');

  late String _selectedCategory;
  int _selectedPointPreset = 5;
  String _selectedPenalty = 'Pembinaan Lisan & Pengarahan BK';
  bool _isSaving = false;
  String? _errorMessage;

  static const List<String> _categoryOptions = [
    'Kedisiplinan',
    'Kerapian',
    'Ketertiban & Berat',
    'Lainnya',
  ];

  static const List<int> _pointOptions = [0, 5, 10, 15, 20, 25, 50];

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
    _selectedCategory = _categoryOptions.contains(widget.initialCategory)
        ? widget.initialCategory
        : _categoryOptions.first;
    _loadSanctions();
  }

  Future<void> _loadSanctions() async {
    final fetched = await ApiService.getSanctions();
    if (fetched.isNotEmpty && mounted) {
      setState(() {
        _penaltyOptions = fetched;
        if (!_penaltyOptions.contains(_selectedPenalty)) {
          _selectedPenalty = _penaltyOptions.first;
        }
      });
    }
  }

  @override
  void dispose() {
    _nameController.dispose();
    _pointsController.dispose();
    super.dispose();
  }

  void _onPointPresetSelected(int points) {
    HapticFeedback.selectionClick();
    setState(() {
      _selectedPointPreset = points;
      _pointsController.text = points == 0 ? '0' : points.toString();
    });
  }

  Future<void> _handleSave() async {
    if (!_formKey.currentState!.validate()) return;

    setState(() {
      _isSaving = true;
      _errorMessage = null;
    });

    final name = _nameController.text.trim();
    final points = int.tryParse(_pointsController.text.trim()) ?? 0;

    try {
      final newItem = await ViolationTypeService.addCustomType(
        name: name,
        category: _selectedCategory,
        defaultPoints: points,
        defaultPenalty: _selectedPenalty,
      );

      if (mounted) {
        Navigator.of(context).pop(newItem);
      }
    } catch (e) {
      if (mounted) {
        setState(() {
          _isSaving = false;
          _errorMessage = e.toString().replaceAll('Exception: ', '');
        });
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    final bottomInset = MediaQuery.of(context).viewInsets.bottom;
    final maxSheetHeight = MediaQuery.of(context).size.height * 0.88;

    return Container(
      constraints: BoxConstraints(maxHeight: maxSheetHeight),
      margin: EdgeInsets.only(bottom: bottomInset),
      decoration: const BoxDecoration(
        color: AppTheme.surface,
        borderRadius: BorderRadius.vertical(top: Radius.circular(20)),
        border: Border(
          top: BorderSide(color: AppTheme.border, width: 1),
        ),
      ),
      child: SafeArea(
        top: false,
        child: Column(
          mainAxisSize: MainAxisSize.min,
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Drag Handle
            const SizedBox(height: 12),
            Center(
              child: Container(
                width: 36,
                height: 4,
                decoration: BoxDecoration(
                  color: AppTheme.borderDark,
                  borderRadius: BorderRadius.circular(8),
                ),
              ),
            ),
            const SizedBox(height: 12),

            // Header Section
            Padding(
              padding: const EdgeInsets.symmetric(horizontal: 16),
              child: Row(
                children: [
                  const Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          'Tambah Jenis Pelanggaran',
                          style: TextStyle(
                            fontFamily: AppTheme.fontFamily,
                            fontSize: 16,
                            fontWeight: FontWeight.w700,
                            color: AppTheme.textPrimary,
                            letterSpacing: -0.3,
                          ),
                        ),
                        SizedBox(height: 2),
                        Text(
                          'Daftarkan kasus pelanggaran kustom ke sistem BK',
                          style: TextStyle(
                            fontFamily: AppTheme.fontFamily,
                            fontSize: 11,
                            color: AppTheme.textSecondary,
                          ),
                        ),
                      ],
                    ),
                  ),
                  IconButton(
                    icon: const Icon(Icons.close, size: 20, color: AppTheme.textSecondary),
                    visualDensity: VisualDensity.compact,
                    onPressed: () => Navigator.of(context).pop(),
                  ),
                ],
              ),
            ),
            const SizedBox(height: 12),
            const Divider(color: AppTheme.border, height: 1),

            // Form Body (Scrollable)
            Flexible(
              child: SingleChildScrollView(
                padding: const EdgeInsets.all(16),
                child: Form(
                  key: _formKey,
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      if (_errorMessage != null) ...[
                        Container(
                          padding: const EdgeInsets.all(12),
                          decoration: BoxDecoration(
                            color: AppTheme.dangerLight,
                            borderRadius: BorderRadius.circular(8),
                            border: Border.all(color: AppTheme.dangerBorder, width: 1),
                          ),
                          child: Row(
                            children: [
                              const Icon(Icons.info_outline, size: 16, color: AppTheme.danger),
                              const SizedBox(width: 8),
                              Expanded(
                                child: Text(
                                  _errorMessage!,
                                  style: const TextStyle(
                                    fontFamily: AppTheme.fontFamily,
                                    fontSize: 12,
                                    color: AppTheme.danger,
                                    fontWeight: FontWeight.w500,
                                  ),
                                ),
                              ),
                            ],
                          ),
                        ),
                        const SizedBox(height: 12),
                      ],

                      // 1. Nama Jenis Pelanggaran
                      AppTextField(
                        controller: _nameController,
                        label: 'Nama Jenis Pelanggaran',
                        hintText: 'Contoh: Membawa motor berknalpot brong',
                        validator: (value) {
                          if (value == null || value.trim().isEmpty) {
                            return 'Nama jenis pelanggaran wajib diisi.';
                          }
                          if (value.trim().length < 3) {
                            return 'Nama pelanggaran minimal 3 karakter.';
                          }
                          return null;
                        },
                      ),
                      const SizedBox(height: 16),

                      // 2. Kategori Pelanggaran
                      const Text(
                        'Kategori Pelanggaran',
                        style: TextStyle(
                          fontFamily: AppTheme.fontFamily,
                          fontSize: 12,
                          fontWeight: FontWeight.w600,
                          color: AppTheme.textPrimary,
                        ),
                      ),
                      const SizedBox(height: 8),
                      Wrap(
                        spacing: 8,
                        runSpacing: 8,
                        children: _categoryOptions.map((cat) {
                          final isSelected = _selectedCategory == cat;
                          return InkWell(
                            onTap: () {
                              HapticFeedback.selectionClick();
                              setState(() {
                                _selectedCategory = cat;
                              });
                            },
                            borderRadius: BorderRadius.circular(8),
                            child: AnimatedContainer(
                              duration: const Duration(milliseconds: 150),
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
                          );
                        }).toList(growable: false),
                      ),
                      const SizedBox(height: 16),

                      // 3. Poin Pelanggaran (Default)
                      const Text(
                        'Poin Pelanggaran (Default)',
                        style: TextStyle(
                          fontFamily: AppTheme.fontFamily,
                          fontSize: 12,
                          fontWeight: FontWeight.w600,
                          color: AppTheme.textPrimary,
                        ),
                      ),
                      const SizedBox(height: 8),
                      Wrap(
                        spacing: 8,
                        runSpacing: 8,
                        children: _pointOptions.map((pts) {
                          final isSelected = _selectedPointPreset == pts;
                          final label = pts == 0 ? 'Tanpa Poin' : '+$pts';
                          return InkWell(
                            onTap: () => _onPointPresetSelected(pts),
                            borderRadius: BorderRadius.circular(8),
                            child: AnimatedContainer(
                              duration: const Duration(milliseconds: 150),
                              padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
                              decoration: BoxDecoration(
                                color: isSelected ? AppTheme.primaryLight : AppTheme.surface,
                                borderRadius: BorderRadius.circular(8),
                                border: Border.all(
                                  color: isSelected ? AppTheme.primary : AppTheme.border,
                                  width: isSelected ? 1.5 : 1,
                                ),
                              ),
                              child: Text(
                                label,
                                style: TextStyle(
                                  fontFamily: AppTheme.fontFamily,
                                  fontSize: 12,
                                  fontWeight: isSelected ? FontWeight.w700 : FontWeight.w500,
                                  color: isSelected ? AppTheme.primary : AppTheme.textSecondary,
                                ),
                              ),
                            ),
                          );
                        }).toList(growable: false),
                      ),
                      const SizedBox(height: 12),

                      Row(
                        children: [
                          Expanded(
                            child: AppTextField(
                              controller: _pointsController,
                              label: 'Skor Poin Kustom',
                              hintText: '5',
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
                                color: _selectedPointPreset > 0
                                    ? AppTheme.dangerLight
                                    : AppTheme.successLight,
                                borderRadius: BorderRadius.circular(8),
                                border: Border.all(
                                  color: _selectedPointPreset > 0
                                      ? AppTheme.dangerBorder
                                      : AppTheme.successBorder,
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
                                  color: _selectedPointPreset > 0
                                      ? AppTheme.danger
                                      : AppTheme.success,
                                ),
                              ),
                            ),
                          ),
                        ],
                      ),
                      const SizedBox(height: 16),

                      // 4. Tindakan Sanksi / Pembinaan Default
                      const Text(
                        'Tindakan Pembinaan Default',
                        style: TextStyle(
                          fontFamily: AppTheme.fontFamily,
                          fontSize: 12,
                          fontWeight: FontWeight.w600,
                          color: AppTheme.textPrimary,
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
                            value: _penaltyOptions.contains(_selectedPenalty)
                                ? _selectedPenalty
                                : _penaltyOptions.first,
                            isExpanded: true,
                            icon: const Icon(
                              Icons.keyboard_arrow_down_rounded,
                              size: 18,
                              color: AppTheme.textMuted,
                            ),
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
                                  _selectedPenalty = val;
                                });
                              }
                            },
                          ),
                        ),
                      ),
                      const SizedBox(height: 20),
                    ],
                  ),
                ),
              ),
            ),

            // Footer Submit Action
            Container(
              padding: const EdgeInsets.all(16),
              decoration: const BoxDecoration(
                color: AppTheme.surface,
                border: Border(
                  top: BorderSide(color: AppTheme.border, width: 1),
                ),
              ),
              child: Row(
                children: [
                  Expanded(
                    child: OutlinedButton(
                      onPressed: _isSaving ? null : () => Navigator.of(context).pop(),
                      style: OutlinedButton.styleFrom(
                        padding: const EdgeInsets.symmetric(vertical: 12),
                        shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(8),
                        ),
                      ),
                      child: const Text('Batal'),
                    ),
                  ),
                  const SizedBox(width: 12),
                  Expanded(
                    flex: 2,
                    child: ElevatedButton(
                      onPressed: _isSaving ? null : _handleSave,
                      style: ElevatedButton.styleFrom(
                        backgroundColor: AppTheme.primary,
                        disabledBackgroundColor: AppTheme.primary.withValues(alpha: 0.6),
                        padding: const EdgeInsets.symmetric(vertical: 12),
                        shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(8),
                        ),
                        elevation: 0,
                      ),
                      child: _isSaving
                          ? const SizedBox(
                              width: 16,
                              height: 16,
                              child: CircularProgressIndicator(
                                strokeWidth: 2,
                                color: Colors.white,
                              ),
                            )
                          : const Text(
                              'Simpan Jenis Pelanggaran',
                              style: TextStyle(
                                fontFamily: AppTheme.fontFamily,
                                fontSize: 12,
                                fontWeight: FontWeight.w700,
                                color: Colors.white,
                              ),
                            ),
                    ),
                  ),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }
}
