import 'dart:io';
import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import '../theme/app_theme.dart';
import '../../screens/field_camera_screen.dart';

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

  Future<void> _openCameraScreen(BuildContext context) async {
    HapticFeedback.selectionClick();
    final result = await Navigator.push<File?>(
      context,
      PageRouteBuilder(
        pageBuilder: (ctx, anim, secAnim) => FieldCameraScreen(initialPhoto: photo),
        transitionsBuilder: (ctx, anim, secAnim, child) => FadeTransition(
          opacity: anim,
          child: child,
        ),
        transitionDuration: const Duration(milliseconds: 200),
      ),
    );

    if (result != null) {
      onPhotoChanged(result);
    }
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
        const SizedBox(height: 8),
        if (photo == null)
          InkWell(
            onTap: () => _openCameraScreen(context),
            borderRadius: BorderRadius.circular(10),
            child: Container(
              width: double.infinity,
              padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
              decoration: BoxDecoration(
                color: AppTheme.background,
                borderRadius: BorderRadius.circular(10),
                border: Border.all(color: AppTheme.border, width: 1),
              ),
              child: const Row(
                children: [
                  Icon(
                    Icons.camera_alt_outlined,
                    size: 20,
                    color: AppTheme.primary,
                  ),
                  SizedBox(width: 12),
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          'Buka Kamera Bukti Lapangan',
                          style: TextStyle(
                            fontFamily: AppTheme.fontFamily,
                            fontSize: 13,
                            fontWeight: FontWeight.w600,
                            color: AppTheme.textPrimary,
                          ),
                        ),
                        SizedBox(height: 2),
                        Text(
                          'Ambil foto bukti pelanggaran atau pilih dari galeri',
                          style: TextStyle(
                            fontFamily: AppTheme.fontFamily,
                            fontSize: 11,
                            color: AppTheme.textSecondary,
                          ),
                        ),
                      ],
                    ),
                  ),
                  Icon(
                    Icons.chevron_right_rounded,
                    size: 20,
                    color: AppTheme.textMuted,
                  ),
                ],
              ),
            ),
          )
        else
          Container(
            padding: const EdgeInsets.all(12),
            decoration: BoxDecoration(
              color: AppTheme.surface,
              borderRadius: BorderRadius.circular(10),
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
                    cacheWidth: 160,
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
                          fontSize: 13,
                          fontWeight: FontWeight.w700,
                          color: AppTheme.textPrimary,
                        ),
                      ),
                      const SizedBox(height: 4),
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
                  icon: const Icon(
                    Icons.fullscreen_rounded,
                    size: 22,
                    color: AppTheme.textSecondary,
                  ),
                  tooltip: 'Lihat / Foto Ulang',
                  onPressed: () => _openCameraScreen(context),
                ),
                IconButton(
                  icon: const Icon(
                    Icons.delete_outline_rounded,
                    size: 20,
                    color: AppTheme.danger,
                  ),
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
