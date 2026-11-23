import 'dart:io';
import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:image_picker/image_picker.dart';
import '../core/theme/app_theme.dart';

typedef BuktiKameraPage = FieldCameraScreen;

class FieldCameraScreen extends StatefulWidget {
  final File? initialPhoto;

  const FieldCameraScreen({
    super.key,
    this.initialPhoto,
  });

  @override
  State<FieldCameraScreen> createState() => _FieldCameraScreenState();
}

class _FieldCameraScreenState extends State<FieldCameraScreen> {
  final ImagePicker _picker = ImagePicker();
  File? _capturedPhoto;
  bool _isLoading = false;
  CameraDevice _cameraDevice = CameraDevice.rear;
  int _flashModeIndex = 0; // 0: Auto, 1: On, 2: Off
  String? _errorMessage;

  static const List<String> _flashLabels = ['Otomatis', 'Aktif', 'Mati'];
  static const List<IconData> _flashIcons = [
    Icons.flash_auto_rounded,
    Icons.flash_on_rounded,
    Icons.flash_off_rounded,
  ];

  @override
  void initState() {
    super.initState();
    _capturedPhoto = widget.initialPhoto;
  }

  void _toggleFlash() {
    HapticFeedback.selectionClick();
    setState(() {
      _flashModeIndex = (_flashModeIndex + 1) % _flashLabels.length;
    });
    ScaffoldMessenger.of(context).clearSnackBars();
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        content: Text('Lampu kilat: ${_flashLabels[_flashModeIndex]}'),
        duration: const Duration(seconds: 1),
        behavior: SnackBarBehavior.floating,
      ),
    );
  }

  void _toggleCameraDevice() {
    HapticFeedback.selectionClick();
    setState(() {
      _cameraDevice = _cameraDevice == CameraDevice.rear
          ? CameraDevice.front
          : CameraDevice.rear;
    });
    ScaffoldMessenger.of(context).clearSnackBars();
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        content: Text(
          _cameraDevice == CameraDevice.rear
              ? 'Menggunakan Kamera Belakang'
              : 'Menggunakan Kamera Depan',
        ),
        duration: const Duration(seconds: 1),
        behavior: SnackBarBehavior.floating,
      ),
    );
  }

  Future<void> _captureOrPickImage(ImageSource source) async {
    setState(() {
      _isLoading = true;
      _errorMessage = null;
    });

    try {
      final XFile? pickedFile = await _picker.pickImage(
        source: source,
        preferredCameraDevice: _cameraDevice,
        imageQuality: 85,
        maxWidth: 1920,
        maxHeight: 1920,
      );

      if (pickedFile != null && mounted) {
        setState(() {
          _capturedPhoto = File(pickedFile.path);
          _isLoading = false;
        });
      } else {
        if (mounted) {
          setState(() {
            _isLoading = false;
          });
        }
      }
    } catch (e) {
      debugPrint('Camera error: $e');
      if (mounted) {
        setState(() {
          _isLoading = false;
          _errorMessage =
              'Tidak dapat mengakses kamera. Pastikan izin kamera aktif atau gunakan pilihan dari galeri.';
        });
      }
    }
  }

  void _confirmAndReturnPhoto() {
    if (_capturedPhoto == null) return;
    HapticFeedback.mediumImpact();
    Navigator.of(context).pop(_capturedPhoto);
  }

  void _retakePhoto() {
    HapticFeedback.selectionClick();
    setState(() {
      _capturedPhoto = null;
      _errorMessage = null;
    });
    _captureOrPickImage(ImageSource.camera);
  }

  @override
  Widget build(BuildContext context) {
    final isPreviewMode = _capturedPhoto != null;

    return Scaffold(
      backgroundColor: AppTheme.textPrimary,
      body: SafeArea(
        child: Column(
          children: [
            // Top Navigation Bar
            Container(
              padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
              decoration: const BoxDecoration(
                color: AppTheme.textPrimary,
                border: Border(
                  bottom: BorderSide(color: AppTheme.textBody, width: 1),
                ),
              ),
              child: Row(
                children: [
                  IconButton(
                    icon: const Icon(
                      Icons.arrow_back_rounded,
                      color: AppTheme.surface,
                      size: 20,
                    ),
                    visualDensity: VisualDensity.compact,
                    onPressed: () => Navigator.of(context).pop(),
                  ),
                  const SizedBox(width: 8),
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          isPreviewMode
                              ? 'Konfirmasi Foto Bukti'
                              : 'Kamera Bukti Lapangan',
                          style: const TextStyle(
                            fontFamily: AppTheme.fontFamily,
                            fontSize: 16,
                            fontWeight: FontWeight.w700,
                            color: AppTheme.surface,
                            letterSpacing: -0.3,
                          ),
                        ),
                        const SizedBox(height: 4),
                        Text(
                          isPreviewMode
                              ? 'Periksa kejelasan objek sebelum disimpan'
                              : 'Arahkan kamera ke siswa atau atribut pelanggaran',
                          style: const TextStyle(
                            fontFamily: AppTheme.fontFamily,
                            fontSize: 12,
                            color: AppTheme.textMuted,
                          ),
                        ),
                      ],
                    ),
                  ),
                  if (!isPreviewMode) ...[
                    IconButton(
                      icon: Icon(
                        _flashIcons[_flashModeIndex],
                        color: _flashModeIndex == 2
                            ? AppTheme.textMuted
                            : AppTheme.warning,
                        size: 20,
                      ),
                      tooltip: 'Lampu Kilat',
                      onPressed: _toggleFlash,
                    ),
                    IconButton(
                      icon: const Icon(
                        Icons.cameraswitch_outlined,
                        color: AppTheme.surface,
                        size: 20,
                      ),
                      tooltip: 'Ganti Kamera Depan/Belakang',
                      onPressed: _toggleCameraDevice,
                    ),
                  ],
                ],
              ),
            ),

            // Main Camera Viewfinder or Photo Preview Area
            Expanded(
              child: isPreviewMode
                  ? _buildPhotoConfirmationView()
                  : _buildCameraViewfinder(),
            ),

            // Bottom Control Toolbar
            Container(
              padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 20),
              decoration: const BoxDecoration(
                color: AppTheme.textPrimary,
                border: Border(
                  top: BorderSide(color: AppTheme.textBody, width: 1),
                ),
              ),
              child: isPreviewMode
                  ? _buildConfirmationActions()
                  : _buildShutterControls(),
            ),
          ],
        ),
      ),
    );
  }

  /// Tampilan Viewfinder Kamera sebelum mengambil gambar
  Widget _buildCameraViewfinder() {
    return Container(
      color: AppTheme.textPrimary,
      width: double.infinity,
      child: Stack(
        alignment: Alignment.center,
        children: [
          // Grid lines frame
          Positioned.fill(
            child: CustomPaint(
              painter: _ViewfinderGridPainter(),
            ),
          ),

          // Viewfinder Target Frame
          Center(
            child: Container(
              width: 280,
              height: 360,
              decoration: BoxDecoration(
                borderRadius: BorderRadius.circular(12),
                border: Border.all(
                  color: AppTheme.textSecondary,
                  width: 1,
                ),
              ),
              child: Stack(
                children: [
                  // Corner brackets
                  _buildCornerBracket(top: 0, left: 0, isTop: true, isLeft: true),
                  _buildCornerBracket(top: 0, right: 0, isTop: true, isLeft: false),
                  _buildCornerBracket(bottom: 0, left: 0, isTop: false, isLeft: true),
                  _buildCornerBracket(bottom: 0, right: 0, isTop: false, isLeft: false),

                  // Center Guidance
                  Center(
                    child: Column(
                      mainAxisSize: MainAxisSize.min,
                      children: [
                        Container(
                          padding: const EdgeInsets.all(16),
                          decoration: BoxDecoration(
                            color: AppTheme.textBody,
                            shape: BoxShape.circle,
                            border: Border.all(
                              color: AppTheme.textSecondary,
                              width: 1,
                            ),
                          ),
                          child: const Icon(
                            Icons.camera_alt_outlined,
                            size: 32,
                            color: AppTheme.surface,
                          ),
                        ),
                        const SizedBox(height: 12),
                        const Text(
                          'Posisikan Objek di Tengah',
                          style: TextStyle(
                            fontFamily: AppTheme.fontFamily,
                            fontSize: 14,
                            fontWeight: FontWeight.w600,
                            color: AppTheme.surface,
                          ),
                        ),
                        const SizedBox(height: 4),
                        const Text(
                          'Ketuk tombol shutter di bawah untuk memotret',
                          textAlign: TextAlign.center,
                          style: TextStyle(
                            fontFamily: AppTheme.fontFamily,
                            fontSize: 12,
                            color: AppTheme.textMuted,
                          ),
                        ),
                      ],
                    ),
                  ),
                ],
              ),
            ),
          ),

          // Error Notification Card if needed
          if (_errorMessage != null)
            Positioned(
              bottom: 20,
              left: 20,
              right: 20,
              child: Container(
                padding: const EdgeInsets.all(12),
                decoration: BoxDecoration(
                  color: AppTheme.dangerLight,
                  borderRadius: BorderRadius.circular(8),
                  border: Border.all(color: AppTheme.dangerBorder, width: 1),
                ),
                child: Row(
                  children: [
                    const Icon(Icons.warning_amber_rounded, size: 18, color: AppTheme.danger),
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
            ),

          if (_isLoading)
            Container(
              color: AppTheme.textPrimary.withValues(alpha: 0.7),
              child: const Center(
                child: CircularProgressIndicator(
                  strokeWidth: 2,
                  color: AppTheme.surface,
                ),
              ),
            ),
        ],
      ),
    );
  }

  Widget _buildCornerBracket({
    double? top,
    double? bottom,
    double? left,
    double? right,
    required bool isTop,
    required bool isLeft,
  }) {
    return Positioned(
      top: top,
      bottom: bottom,
      left: left,
      right: right,
      child: Container(
        width: 20,
        height: 20,
        decoration: BoxDecoration(
          border: Border(
            top: isTop
                ? const BorderSide(color: AppTheme.primaryFocus, width: 3)
                : BorderSide.none,
            bottom: !isTop
                ? const BorderSide(color: AppTheme.primaryFocus, width: 3)
                : BorderSide.none,
            left: isLeft
                ? const BorderSide(color: AppTheme.primaryFocus, width: 3)
                : BorderSide.none,
            right: !isLeft
                ? const BorderSide(color: AppTheme.primaryFocus, width: 3)
                : BorderSide.none,
          ),
        ),
      ),
    );
  }

  /// Tampilan Pratinjau Foto setelah pemotretan berhasil
  Widget _buildPhotoConfirmationView() {
    return Container(
      color: AppTheme.textPrimary,
      width: double.infinity,
      child: Stack(
        fit: StackFit.expand,
        children: [
          // Gambar Pratinjau Full Screen dengan Zoom
          InteractiveViewer(
            maxScale: 3.0,
            child: Center(
              child: Image.file(
                _capturedPhoto!,
                fit: BoxFit.contain,
              ),
            ),
          ),

          // Header Badge Pratinjau
          Positioned(
            top: 16,
            left: 16,
            child: Container(
              padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
              decoration: BoxDecoration(
                color: AppTheme.textPrimary,
                borderRadius: BorderRadius.circular(8),
                border: Border.all(color: AppTheme.textSecondary, width: 1),
              ),
              child: Row(
                mainAxisSize: MainAxisSize.min,
                children: [
                  Container(
                    width: 8,
                    height: 8,
                    decoration: const BoxDecoration(
                      color: AppTheme.success,
                      shape: BoxShape.circle,
                    ),
                  ),
                  const SizedBox(width: 8),
                  const Text(
                    'Foto Berhasil Diambil',
                    style: TextStyle(
                      fontFamily: AppTheme.fontFamily,
                      fontSize: 12,
                      fontWeight: FontWeight.w600,
                      color: AppTheme.surface,
                    ),
                  ),
                ],
              ),
            ),
          ),
        ],
      ),
    );
  }

  /// Kontrol Tombol Shutter & Galeri di bagian bawah
  Widget _buildShutterControls() {
    return Row(
      mainAxisAlignment: MainAxisAlignment.spaceEvenly,
      children: [
        // Tombol Pilih dari Galeri
        InkWell(
          onTap: _isLoading ? null : () => _captureOrPickImage(ImageSource.gallery),
          borderRadius: BorderRadius.circular(12),
          child: Container(
            padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
            decoration: BoxDecoration(
              color: AppTheme.textBody,
              borderRadius: BorderRadius.circular(12),
              border: Border.all(color: AppTheme.textSecondary, width: 1),
            ),
            child: const Row(
              mainAxisSize: MainAxisSize.min,
              children: [
                Icon(Icons.photo_library_outlined, size: 18, color: AppTheme.surface),
                SizedBox(width: 8),
                Text(
                  'Galeri',
                  style: TextStyle(
                    fontFamily: AppTheme.fontFamily,
                    fontSize: 12,
                    fontWeight: FontWeight.w600,
                    color: AppTheme.surface,
                  ),
                ),
              ],
            ),
          ),
        ),

        // Shutter Button Utama
        GestureDetector(
          onTap: _isLoading
              ? null
              : () {
                  HapticFeedback.heavyImpact();
                  _captureOrPickImage(ImageSource.camera);
                },
          child: Container(
            width: 72,
            height: 72,
            decoration: BoxDecoration(
              shape: BoxShape.circle,
              border: Border.all(color: AppTheme.surface, width: 4),
            ),
            padding: const EdgeInsets.all(4),
            child: Container(
              decoration: const BoxDecoration(
                shape: BoxShape.circle,
                color: AppTheme.surface,
              ),
              child: const Icon(
                Icons.camera_alt_rounded,
                size: 28,
                color: AppTheme.textPrimary,
              ),
            ),
          ),
        ),

        // Tombol Balik Kamera (Depan/Belakang)
        InkWell(
          onTap: _isLoading ? null : _toggleCameraDevice,
          borderRadius: BorderRadius.circular(12),
          child: Container(
            padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
            decoration: BoxDecoration(
              color: AppTheme.textBody,
              borderRadius: BorderRadius.circular(12),
              border: Border.all(color: AppTheme.textSecondary, width: 1),
            ),
            child: const Row(
              mainAxisSize: MainAxisSize.min,
              children: [
                Icon(Icons.flip_camera_ios_rounded, size: 18, color: AppTheme.surface),
                SizedBox(width: 8),
                Text(
                  'Putar',
                  style: TextStyle(
                    fontFamily: AppTheme.fontFamily,
                    fontSize: 12,
                    fontWeight: FontWeight.w600,
                    color: AppTheme.surface,
                  ),
                ),
              ],
            ),
          ),
        ),
      ],
    );
  }

  /// Tombol Aksi Konfirmasi Foto (Foto Ulang / Gunakan Foto)
  Widget _buildConfirmationActions() {
    return Row(
      children: [
        // Tombol Foto Ulang
        Expanded(
          child: OutlinedButton.icon(
            onPressed: _isLoading ? null : _retakePhoto,
            style: OutlinedButton.styleFrom(
              foregroundColor: AppTheme.surface,
              side: const BorderSide(color: AppTheme.borderDark, width: 1),
              padding: const EdgeInsets.symmetric(vertical: 12),
              shape: RoundedRectangleBorder(
                borderRadius: BorderRadius.circular(8),
              ),
            ),
            icon: const Icon(Icons.replay_rounded, size: 18),
            label: const Text(
              'Foto Ulang',
              style: TextStyle(
                fontFamily: AppTheme.fontFamily,
                fontSize: 12,
                fontWeight: FontWeight.w600,
              ),
            ),
          ),
        ),
        const SizedBox(width: 12),

        // Tombol Gunakan Foto
        Expanded(
          flex: 2,
          child: ElevatedButton.icon(
            onPressed: _isLoading ? null : _confirmAndReturnPhoto,
            style: ElevatedButton.styleFrom(
              backgroundColor: AppTheme.primary,
              foregroundColor: AppTheme.surface,
              elevation: 0,
              padding: const EdgeInsets.symmetric(vertical: 12),
              shape: RoundedRectangleBorder(
                borderRadius: BorderRadius.circular(8),
              ),
            ),
            icon: const Icon(Icons.check_rounded, size: 18, color: AppTheme.surface),
            label: const Text(
              'Gunakan Foto Bukti',
              style: TextStyle(
                fontFamily: AppTheme.fontFamily,
                fontSize: 12,
                fontWeight: FontWeight.w700,
                color: AppTheme.surface,
              ),
            ),
          ),
        ),
      ],
    );
  }
}

/// Grid Rule of Thirds untuk Kamera
class _ViewfinderGridPainter extends CustomPainter {
  @override
  void paint(Canvas canvas, Size size) {
    final paint = Paint()
      ..color = AppTheme.textSecondary.withValues(alpha: 0.15)
      ..strokeWidth = 1.0;

    // Horizontal grid lines
    canvas.drawLine(
      Offset(0, size.height / 3),
      Offset(size.width, size.height / 3),
      paint,
    );
    canvas.drawLine(
      Offset(0, 2 * size.height / 3),
      Offset(size.width, 2 * size.height / 3),
      paint,
    );

    // Vertical grid lines
    canvas.drawLine(
      Offset(size.width / 3, 0),
      Offset(size.width / 3, size.height),
      paint,
    );
    canvas.drawLine(
      Offset(2 * size.width / 3, 0),
      Offset(2 * size.width / 3, size.height),
      paint,
    );
  }

  @override
  bool shouldRepaint(covariant CustomPainter oldDelegate) => false;
}
