import 'dart:io';
import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:image_picker/image_picker.dart';
import '../theme/app_theme.dart';

class CompactPhotoPicker extends StatelessWidget {
  final File? photo;
  final ValueChanged<File?> onPhotoChanged;
  final String label;

  const CompactPhotoPicker({
    super.key,
    required this.photo,
    required this.onPhotoChanged,
    this.label = 'Foto Bukti Pelanggaran (Opsional)',
  });

  Future<File?> _pickImageFrom(ImageSource source) async {
    try {
      final picker = ImagePicker();
      final picked = await picker.pickImage(
        source: source,
        imageQuality: 75,
        maxWidth: 1280,
      );
      if (picked != null) {
        return File(picked.path);
      }
    } catch (e) {
      debugPrint('Gagal mengambil gambar: $e');
    }
    return null;
  }

  void _openCameraModal(BuildContext context) {
    File? tempPhoto = photo;

    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      builder: (ctx) {
        return StatefulBuilder(
          builder: (context, setModalState) {
            final bottomInset = MediaQuery.of(context).viewInsets.bottom;

            return Container(
              margin: EdgeInsets.only(bottom: bottomInset),
              decoration: const BoxDecoration(
                color: AppTheme.surface,
                borderRadius: BorderRadius.vertical(top: Radius.circular(20)),
                border: Border(
                  top: BorderSide(color: AppTheme.border, width: 1),
                  left: BorderSide(color: AppTheme.border, width: 1),
                  right: BorderSide(color: AppTheme.border, width: 1),
                ),
              ),
              child: SafeArea(
                top: false,
                child: Padding(
                  padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
                  child: Column(
                    mainAxisSize: MainAxisSize.min,
                    children: [
                      // Drag Handle
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
                      const SizedBox(height: 12),

                      // Header
                      Row(
                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                        children: [
                          Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: const [
                              Text(
                                'Kamera Bukti Lapangan',
                                style: TextStyle(
                                  fontFamily: AppTheme.fontFamily,
                                  fontSize: 15,
                                  fontWeight: FontWeight.w700,
                                  color: AppTheme.textPrimary,
                                  letterSpacing: -0.3,
                                ),
                              ),
                              SizedBox(height: 2),
                              Text(
                                'Arahkan kamera ke siswa atau atribut bukti',
                                style: TextStyle(
                                  fontFamily: AppTheme.fontFamily,
                                  fontSize: 11,
                                  color: AppTheme.textSecondary,
                                ),
                              ),
                            ],
                          ),
                          IconButton(
                            icon: const Icon(Icons.close, size: 20, color: AppTheme.textSecondary),
                            visualDensity: VisualDensity.compact,
                            onPressed: () => Navigator.pop(ctx),
                          ),
                        ],
                      ),
                      const SizedBox(height: 14),

                      // Viewfinder Card / Preview
                      Container(
                        width: double.infinity,
                        height: 220,
                        decoration: BoxDecoration(
                          color: tempPhoto == null ? const Color(0xFF0F172A) : AppTheme.surface,
                          borderRadius: BorderRadius.circular(12),
                          border: Border.all(color: AppTheme.border, width: 1),
                        ),
                        clipBehavior: Clip.antiAlias,
                        child: tempPhoto != null
                            ? Stack(
                                fit: StackFit.expand,
                                children: [
                                  Image.file(
                                    tempPhoto!,
                                    fit: BoxFit.cover,
                                    cacheWidth: 800,
                                  ),
                                  Positioned(
                                    top: 10,
                                    left: 10,
                                    child: Container(
                                      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                                      decoration: BoxDecoration(
                                        color: AppTheme.surface.withValues(alpha: 0.9),
                                        borderRadius: BorderRadius.circular(6),
                                        border: Border.all(color: AppTheme.border, width: 1),
                                      ),
                                      child: const Text(
                                        'Preview Foto Bukti',
                                        style: TextStyle(
                                          fontFamily: AppTheme.fontFamily,
                                          fontSize: 10,
                                          fontWeight: FontWeight.w700,
                                          color: AppTheme.textPrimary,
                                        ),
                                      ),
                                    ),
                                  ),
                                ],
                              )
                            : Stack(
                                children: [
                                  Center(
                                    child: Column(
                                      mainAxisSize: MainAxisSize.min,
                                      children: const [
                                        Icon(
                                          Icons.camera_alt_outlined,
                                          size: 38,
                                          color: Color(0xFF94A3B8),
                                        ),
                                        SizedBox(height: 8),
                                        Text(
                                          'Kamera Siap Jepret',
                                          style: TextStyle(
                                            fontFamily: AppTheme.fontFamily,
                                            fontSize: 13,
                                            fontWeight: FontWeight.w600,
                                            color: Colors.white,
                                          ),
                                        ),
                                        SizedBox(height: 2),
                                        Text(
                                          'Tekan tombol jepret di bawah',
                                          style: TextStyle(
                                            fontFamily: AppTheme.fontFamily,
                                            fontSize: 11,
                                            color: Color(0xFF94A3B8),
                                          ),
                                        ),
                                      ],
                                    ),
                                  ),
                                  // Minimal Viewfinder Frame Corners
                                  Positioned(
                                    top: 14,
                                    left: 14,
                                    child: Container(
                                      width: 16,
                                      height: 16,
                                      decoration: const BoxDecoration(
                                        border: Border(
                                          top: BorderSide(color: Color(0xFF64748B), width: 2),
                                          left: BorderSide(color: Color(0xFF64748B), width: 2),
                                        ),
                                      ),
                                    ),
                                  ),
                                  Positioned(
                                    top: 14,
                                    right: 14,
                                    child: Container(
                                      width: 16,
                                      height: 16,
                                      decoration: const BoxDecoration(
                                        border: Border(
                                          top: BorderSide(color: Color(0xFF64748B), width: 2),
                                          right: BorderSide(color: Color(0xFF64748B), width: 2),
                                        ),
                                      ),
                                    ),
                                  ),
                                  Positioned(
                                    bottom: 14,
                                    left: 14,
                                    child: Container(
                                      width: 16,
                                      height: 16,
                                      decoration: const BoxDecoration(
                                        border: Border(
                                          bottom: BorderSide(color: Color(0xFF64748B), width: 2),
                                          left: BorderSide(color: Color(0xFF64748B), width: 2),
                                        ),
                                      ),
                                    ),
                                  ),
                                  Positioned(
                                    bottom: 14,
                                    right: 14,
                                    child: Container(
                                      width: 16,
                                      height: 16,
                                      decoration: const BoxDecoration(
                                        border: Border(
                                          bottom: BorderSide(color: Color(0xFF64748B), width: 2),
                                          right: BorderSide(color: Color(0xFF64748B), width: 2),
                                        ),
                                      ),
                                    ),
                                  ),
                                ],
                              ),
                      ),
                      const SizedBox(height: 16),

                      // Actions / Shutter Control
                      if (tempPhoto == null) ...[
                        Center(
                          child: GestureDetector(
                            onTap: () async {
                              HapticFeedback.mediumImpact();
                              final file = await _pickImageFrom(ImageSource.camera);
                              if (file != null) {
                                setModalState(() {
                                  tempPhoto = file;
                                });
                              }
                            },
                            child: Container(
                              width: 64,
                              height: 64,
                              decoration: BoxDecoration(
                                shape: BoxShape.circle,
                                color: Colors.transparent,
                                border: Border.all(color: AppTheme.borderDark, width: 3),
                              ),
                              padding: const EdgeInsets.all(4),
                              child: Container(
                                decoration: const BoxDecoration(
                                  shape: BoxShape.circle,
                                  color: AppTheme.primary,
                                ),
                                child: const Icon(
                                  Icons.camera_alt_rounded,
                                  color: Colors.white,
                                  size: 26,
                                ),
                              ),
                            ),
                          ),
                        ),
                        const SizedBox(height: 8),
                        const Text(
                          'Ketuk untuk Jepret',
                          style: TextStyle(
                            fontFamily: AppTheme.fontFamily,
                            fontSize: 11,
                            fontWeight: FontWeight.w600,
                            color: AppTheme.textSecondary,
                          ),
                        ),
                        const SizedBox(height: 8),
                        InkWell(
                          onTap: () async {
                            HapticFeedback.selectionClick();
                            final file = await _pickImageFrom(ImageSource.gallery);
                            if (file != null) {
                              setModalState(() {
                                tempPhoto = file;
                              });
                            }
                          },
                          child: const Padding(
                            padding: EdgeInsets.symmetric(horizontal: 12, vertical: 6),
                            child: Row(
                              mainAxisSize: MainAxisSize.min,
                              children: [
                                Icon(Icons.photo_library_outlined, size: 14, color: AppTheme.textSecondary),
                                SizedBox(width: 6),
                                Text(
                                  'Pilih dari Galeri',
                                  style: TextStyle(
                                    fontFamily: AppTheme.fontFamily,
                                    fontSize: 11,
                                    fontWeight: FontWeight.w600,
                                    color: AppTheme.textSecondary,
                                  ),
                                ),
                              ],
                            ),
                          ),
                        ),
                      ] else ...[
                        Row(
                          children: [
                            Expanded(
                              child: OutlinedButton.icon(
                                onPressed: () async {
                                  HapticFeedback.selectionClick();
                                  final file = await _pickImageFrom(ImageSource.camera);
                                  if (file != null) {
                                    setModalState(() {
                                      tempPhoto = file;
                                    });
                                  }
                                },
                                icon: const Icon(Icons.refresh_rounded, size: 16),
                                label: const Text('Foto Ulang'),
                              ),
                            ),
                            const SizedBox(width: 10),
                            Expanded(
                              child: ElevatedButton.icon(
                                onPressed: () {
                                  HapticFeedback.selectionClick();
                                  onPhotoChanged(tempPhoto);
                                  Navigator.pop(ctx);
                                },
                                icon: const Icon(Icons.check_rounded, size: 16, color: Colors.white),
                                label: const Text(
                                  'Gunakan Foto',
                                  style: TextStyle(color: Colors.white),
                                ),
                              ),
                            ),
                          ],
                        ),
                      ],
                      const SizedBox(height: 8),
                    ],
                  ),
                ),
              ),
            );
          },
        );
      },
    );
  }

  @override
  Widget build(BuildContext context) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      mainAxisSize: MainAxisSize.min,
      children: [
        Text(
          label,
          style: const TextStyle(
            fontFamily: AppTheme.fontFamily,
            fontSize: 12,
            fontWeight: FontWeight.w600,
            color: AppTheme.textSecondary,
          ),
        ),
        const SizedBox(height: 6),
        if (photo == null)
          InkWell(
            onTap: () => _openCameraModal(context),
            borderRadius: BorderRadius.circular(12),
            child: Container(
              width: double.infinity,
              padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
              decoration: BoxDecoration(
                color: AppTheme.background,
                borderRadius: BorderRadius.circular(12),
                border: Border.all(color: AppTheme.border, width: 1),
              ),
              child: Row(
                children: const [
                  Icon(Icons.camera_alt_outlined, size: 20, color: AppTheme.primaryFocus),
                  SizedBox(width: 12),
                  Expanded(
                    child: Text(
                      'Ketuk untuk ambil atau unggah foto bukti',
                      style: TextStyle(
                        fontFamily: AppTheme.fontFamily,
                        fontSize: 12,
                        fontWeight: FontWeight.w500,
                        color: AppTheme.textSecondary,
                      ),
                    ),
                  ),
                  Icon(Icons.add_circle_outline, size: 18, color: AppTheme.textMuted),
                ],
              ),
            ),
          )
        else
          Container(
            padding: const EdgeInsets.all(10),
            decoration: BoxDecoration(
              color: AppTheme.surface,
              borderRadius: BorderRadius.circular(12),
              border: Border.all(color: AppTheme.border, width: 1),
            ),
            child: Row(
              children: [
                ClipRRect(
                  borderRadius: BorderRadius.circular(8),
                  child: Image.file(
                    photo!,
                    width: 64,
                    height: 64,
                    fit: BoxFit.cover,
                    cacheWidth: 128,
                  ),
                ),
                const SizedBox(width: 12),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      const Text(
                        'Foto bukti terlampir',
                        style: TextStyle(
                          fontFamily: AppTheme.fontFamily,
                          fontSize: 12,
                          fontWeight: FontWeight.w700,
                          color: AppTheme.textPrimary,
                        ),
                      ),
                      const SizedBox(height: 2),
                      Text(
                        photo!.path.split('/').last,
                        maxLines: 1,
                        overflow: TextOverflow.ellipsis,
                        style: const TextStyle(
                          fontFamily: AppTheme.fontFamily,
                          fontSize: 11,
                          color: AppTheme.textMuted,
                        ),
                      ),
                    ],
                  ),
                ),
                IconButton(
                  icon: const Icon(Icons.crop_original_rounded, size: 20, color: AppTheme.textSecondary),
                  tooltip: 'Lihat / Ganti foto',
                  onPressed: () => _openCameraModal(context),
                ),
                IconButton(
                  icon: const Icon(Icons.delete_outline_rounded, size: 20, color: AppTheme.danger),
                  tooltip: 'Hapus foto',
                  onPressed: () {
                    HapticFeedback.selectionClick();
                    onPhotoChanged(null);
                  },
                ),
              ],
            ),
          ),
      ],
    );
  }
}
