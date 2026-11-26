import 'dart:io';
import 'package:flutter/foundation.dart';
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
  bool _isShutterPressed = false;
  bool _showGrid = true;
  CameraDevice _cameraDevice = CameraDevice.rear;
  int _flashModeIndex = 0; // 0: Auto, 1: On, 2: Off
  String? _errorMessage;

  static const Color _canvasDark = Color(0xFF090D16);
  static const Color _barDark = Color(0xFF0F172A);
  static const Color _surfaceDark = Color(0xFF1E293B);
  static const Color _borderDark = Color(0xFF334155);

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
  }

  void _toggleGrid() {
    HapticFeedback.selectionClick();
    setState(() {
      _showGrid = !_showGrid;
    });
  }

  void _toggleCameraDevice() {
    HapticFeedback.selectionClick();
    setState(() {
      _cameraDevice = _cameraDevice == CameraDevice.rear
          ? CameraDevice.front
          : CameraDevice.rear;
    });
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
      } else if (mounted) {
        setState(() {
          _isLoading = false;
        });
      }
    } catch (e) {
      debugPrint('Camera error: $e');
      if (mounted) {
        setState(() {
          _isLoading = false;
          _errorMessage =
              'Tidak dapat mengakses kamera. Pastikan izin kamera aktif atau pilih foto dari galeri.';
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
      backgroundColor: _canvasDark,
      body: SafeArea(
        child: Column(
          children: [
            // Top Studio Header Bar
            Container(
              padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 10),
              decoration: const BoxDecoration(
                color: _barDark,
                border: Border(
                  bottom: BorderSide(color: _borderDark, width: 1),
                ),
              ),
              child: Row(
                children: [
                  SizedBox(
                    width: 44,
                    height: 44,
                    child: IconButton(
                      icon: const Icon(
                        Icons.arrow_back_rounded,
                        color: AppTheme.surface,
                        size: 22,
                      ),
                      tooltip: 'Kembali',
                      onPressed: () => Navigator.of(context).pop(),
                    ),
                  ),
                  const SizedBox(width: 6),
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
                            fontSize: 15,
                            fontWeight: FontWeight.w700,
                            color: AppTheme.surface,
                            letterSpacing: -0.2,
                          ),
                        ),
                        const SizedBox(height: 2),
                        Text(
                          isPreviewMode
                              ? 'Periksa ketajaman objek sebelum digunakan'
                              : 'Dokumentasi pelanggaran atau pembinaan siswa',
                          maxLines: 1,
                          overflow: TextOverflow.ellipsis,
                          style: const TextStyle(
                            fontFamily: AppTheme.fontFamily,
                            fontSize: 11,
                            color: Color(0xFF94A3B8),
                          ),
                        ),
                      ],
                    ),
                  ),
                  if (!isPreviewMode) ...[
                    const SizedBox(width: 8),
                    _buildTopActionButton(
                      icon: _showGrid ? Icons.grid_on_rounded : Icons.grid_off_rounded,
                      active: _showGrid,
                      tooltip: 'Garis Bantu Grid',
                      onTap: _toggleGrid,
                    ),
                    const SizedBox(width: 8),
                    _buildTopActionButton(
                      icon: _flashIcons[_flashModeIndex],
                      active: _flashModeIndex != 2,
                      activeColor: AppTheme.warning,
                      tooltip: 'Lampu Kilat: ${_flashLabels[_flashModeIndex]}',
                      onTap: _toggleFlash,
                    ),
                  ],
                ],
              ),
            ),

            // Viewfinder or Photo Preview
            Expanded(
              child: isPreviewMode
                  ? _buildPhotoConfirmationView()
                  : _buildCameraViewfinder(),
            ),

            // Bottom Control Deck
            Container(
              padding: const EdgeInsets.fromLTRB(20, 16, 20, 18),
              decoration: const BoxDecoration(
                color: _barDark,
                border: Border(
                  top: BorderSide(color: _borderDark, width: 1),
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

  Widget _buildTopActionButton({
    required IconData icon,
    required bool active,
    required String tooltip,
    required VoidCallback onTap,
    Color activeColor = const Color(0xFF60A5FA),
  }) {
    return Tooltip(
      message: tooltip,
      child: InkWell(
        onTap: onTap,
        borderRadius: BorderRadius.circular(10),
        child: Container(
          width: 44,
          height: 44,
          decoration: BoxDecoration(
            color: active ? _surfaceDark : Colors.transparent,
            borderRadius: BorderRadius.circular(10),
            border: Border.all(
              color: active ? _borderDark : Colors.transparent,
              width: 1,
            ),
          ),
          child: Icon(
            icon,
            color: active ? activeColor : const Color(0xFF94A3B8),
            size: 20,
          ),
        ),
      ),
    );
  }

  Widget _buildCameraViewfinder() {
    return Container(
      color: _canvasDark,
      width: double.infinity,
      child: LayoutBuilder(
        builder: (context, constraints) {
          final frameWidth = (constraints.maxWidth * 0.76).clamp(220.0, 320.0);
          final frameHeight = (constraints.maxHeight * 0.62).clamp(240.0, 400.0);

          return Stack(
            alignment: Alignment.center,
            children: [
              // Rule of thirds grid
              if (_showGrid)
                Positioned.fill(
                  child: CustomPaint(
                    painter: _ViewfinderGridPainter(),
                  ),
                ),

              // Top Status Pill (Camera & Flash Mode)
              Positioned(
                top: 14,
                child: Container(
                  padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
                  decoration: BoxDecoration(
                    color: _surfaceDark,
                    borderRadius: BorderRadius.circular(8),
                    border: Border.all(color: _borderDark, width: 1),
                  ),
                  child: Row(
                    mainAxisSize: MainAxisSize.min,
                    children: [
                      Icon(
                        _cameraDevice == CameraDevice.rear
                            ? Icons.camera_rear_rounded
                            : Icons.camera_front_rounded,
                        size: 14,
                        color: const Color(0xFF60A5FA),
                      ),
                      const SizedBox(width: 6),
                      Text(
                        _cameraDevice == CameraDevice.rear
                            ? 'Kamera Belakang'
                            : 'Kamera Depan',
                        style: const TextStyle(
                          fontFamily: AppTheme.fontFamily,
                          fontSize: 11,
                          fontWeight: FontWeight.w600,
                          color: AppTheme.surface,
                        ),
                      ),
                      const SizedBox(width: 8),
                      Container(width: 1, height: 12, color: _borderDark),
                      const SizedBox(width: 8),
                      Icon(
                        _flashIcons[_flashModeIndex],
                        size: 13,
                        color: _flashModeIndex == 2
                            ? const Color(0xFF94A3B8)
                            : AppTheme.warning,
                      ),
                      const SizedBox(width: 4),
                      Text(
                        _flashLabels[_flashModeIndex],
                        style: const TextStyle(
                          fontFamily: AppTheme.fontFamily,
                          fontSize: 11,
                          fontWeight: FontWeight.w500,
                          color: Color(0xFFCBD5E1),
                        ),
                      ),
                    ],
                  ),
                ),
              ),

              // Interactive Target Frame
              Center(
                child: GestureDetector(
                  onTap: _isLoading
                      ? null
                      : () {
                          HapticFeedback.mediumImpact();
                          _captureOrPickImage(ImageSource.camera);
                        },
                  child: Container(
                    width: frameWidth,
                    height: frameHeight,
                    decoration: BoxDecoration(
                      color: _barDark.withValues(alpha: 0.35),
                      borderRadius: BorderRadius.circular(14),
                      border: Border.all(
                        color: _borderDark,
                        width: 1,
                      ),
                    ),
                    child: Stack(
                      children: [
                        _buildCornerBracket(top: 0, left: 0, isTop: true, isLeft: true),
                        _buildCornerBracket(top: 0, right: 0, isTop: true, isLeft: false),
                        _buildCornerBracket(bottom: 0, left: 0, isTop: false, isLeft: true),
                        _buildCornerBracket(bottom: 0, right: 0, isTop: false, isLeft: false),

                        // Center Crosshair & Guidance
                        Center(
                          child: Padding(
                            padding: const EdgeInsets.symmetric(horizontal: 20),
                            child: Column(
                              mainAxisSize: MainAxisSize.min,
                              children: [
                                Container(
                                  width: 64,
                                  height: 64,
                                  decoration: BoxDecoration(
                                    color: _surfaceDark,
                                    shape: BoxShape.circle,
                                    border: Border.all(
                                      color: _borderDark,
                                      width: 1.5,
                                    ),
                                  ),
                                  child: const Icon(
                                    Icons.camera_alt_rounded,
                                    size: 28,
                                    color: AppTheme.surface,
                                  ),
                                ),
                                const SizedBox(height: 14),
                                const Text(
                                  'Posisikan Objek di Tengah',
                                  textAlign: TextAlign.center,
                                  style: TextStyle(
                                    fontFamily: AppTheme.fontFamily,
                                    fontSize: 14,
                                    fontWeight: FontWeight.w700,
                                    color: AppTheme.surface,
                                  ),
                                ),
                                const SizedBox(height: 4),
                                const Text(
                                  'Ketuk area ini atau tombol shutter untuk memotret',
                                  textAlign: TextAlign.center,
                                  style: TextStyle(
                                    fontFamily: AppTheme.fontFamily,
                                    fontSize: 11,
                                    color: Color(0xFF94A3B8),
                                    height: 1.4,
                                  ),
                                ),
                              ],
                            ),
                          ),
                        ),
                      ],
                    ),
                  ),
                ),
              ),

              // Bottom Tip or Error Notification
              if (_errorMessage != null)
                Positioned(
                  bottom: 16,
                  left: 16,
                  right: 16,
                  child: Container(
                    padding: const EdgeInsets.all(12),
                    decoration: BoxDecoration(
                      color: AppTheme.dangerLight,
                      borderRadius: BorderRadius.circular(10),
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
                )
              else
                const Positioned(
                  bottom: 14,
                  left: 24,
                  right: 24,
                  child: Text(
                    'Pastikan pencahayaan cukup agar bukti pelanggaran terlihat jelas',
                    textAlign: TextAlign.center,
                    style: TextStyle(
                      fontFamily: AppTheme.fontFamily,
                      fontSize: 11,
                      color: Color(0xFF64748B),
                    ),
                  ),
                ),

              if (_isLoading)
                Container(
                  color: _canvasDark.withValues(alpha: 0.75),
                  child: const Center(
                    child: CircularProgressIndicator(
                      strokeWidth: 2.5,
                      color: AppTheme.surface,
                    ),
                  ),
                ),
            ],
          );
        },
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
    const bracketColor = Color(0xFF60A5FA);
    return Positioned(
      top: top,
      bottom: bottom,
      left: left,
      right: right,
      child: Container(
        width: 22,
        height: 22,
        decoration: BoxDecoration(
          borderRadius: BorderRadius.only(
            topLeft: (isTop && isLeft) ? const Radius.circular(14) : Radius.zero,
            topRight: (isTop && !isLeft) ? const Radius.circular(14) : Radius.zero,
            bottomLeft: (!isTop && isLeft) ? const Radius.circular(14) : Radius.zero,
            bottomRight: (!isTop && !isLeft) ? const Radius.circular(14) : Radius.zero,
          ),
          border: Border(
            top: isTop ? const BorderSide(color: bracketColor, width: 3) : BorderSide.none,
            bottom: !isTop ? const BorderSide(color: bracketColor, width: 3) : BorderSide.none,
            left: isLeft ? const BorderSide(color: bracketColor, width: 3) : BorderSide.none,
            right: !isLeft ? const BorderSide(color: bracketColor, width: 3) : BorderSide.none,
          ),
        ),
      ),
    );
  }

  Widget _buildPhotoConfirmationView() {
    return Container(
      color: _canvasDark,
      width: double.infinity,
      child: Stack(
        fit: StackFit.expand,
        children: [
          InteractiveViewer(
            minScale: 1.0,
            maxScale: 4.0,
            child: Center(
              child: kIsWeb
                  ? Image.network(_capturedPhoto!.path, fit: BoxFit.contain)
                  : Image.file(_capturedPhoto!, fit: BoxFit.contain),
            ),
          ),

          Positioned(
            top: 14,
            left: 16,
            right: 16,
            child: Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Container(
                  padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 7),
                  decoration: BoxDecoration(
                    color: _surfaceDark,
                    borderRadius: BorderRadius.circular(8),
                    border: Border.all(color: _borderDark, width: 1),
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
                        'Foto Siap Digunakan',
                        style: TextStyle(
                          fontFamily: AppTheme.fontFamily,
                          fontSize: 11,
                          fontWeight: FontWeight.w600,
                          color: AppTheme.surface,
                        ),
                      ),
                    ],
                  ),
                ),
                Container(
                  padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 7),
                  decoration: BoxDecoration(
                    color: _surfaceDark,
                    borderRadius: BorderRadius.circular(8),
                    border: Border.all(color: _borderDark, width: 1),
                  ),
                  child: const Row(
                    mainAxisSize: MainAxisSize.min,
                    children: [
                      Icon(Icons.zoom_in_rounded, size: 14, color: Color(0xFF94A3B8)),
                      SizedBox(width: 4),
                      Text(
                        'Cubit untuk Zoom',
                        style: TextStyle(
                          fontFamily: AppTheme.fontFamily,
                          fontSize: 11,
                          color: Color(0xFFCBD5E1),
                        ),
                      ),
                    ],
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildShutterControls() {
    return Row(
      mainAxisAlignment: MainAxisAlignment.spaceEvenly,
      children: [
        // Galeri (48dp touch target)
        _buildDeckButton(
          icon: Icons.photo_library_outlined,
          label: 'Galeri',
          onTap: _isLoading ? null : () => _captureOrPickImage(ImageSource.gallery),
        ),

        // Shutter Button Utama dengan animasi tekan
        GestureDetector(
          onTapDown: (_) => setState(() => _isShutterPressed = true),
          onTapUp: (_) => setState(() => _isShutterPressed = false),
          onTapCancel: () => setState(() => _isShutterPressed = false),
          onTap: _isLoading
              ? null
              : () {
                  HapticFeedback.heavyImpact();
                  _captureOrPickImage(ImageSource.camera);
                },
          child: AnimatedScale(
            scale: _isShutterPressed ? 0.92 : 1.0,
            duration: const Duration(milliseconds: 110),
            child: Container(
              width: 74,
              height: 74,
              decoration: BoxDecoration(
                shape: BoxShape.circle,
                border: Border.all(color: AppTheme.surface, width: 3.5),
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
                  color: _canvasDark,
                ),
              ),
            ),
          ),
        ),

        // Putar Kamera (48dp touch target)
        _buildDeckButton(
          icon: Icons.flip_camera_ios_rounded,
          label: 'Putar',
          onTap: _isLoading ? null : _toggleCameraDevice,
        ),
      ],
    );
  }

  Widget _buildDeckButton({
    required IconData icon,
    required String label,
    required VoidCallback? onTap,
  }) {
    return InkWell(
      onTap: onTap,
      borderRadius: BorderRadius.circular(12),
      child: Container(
        constraints: const BoxConstraints(minWidth: 88, minHeight: 48),
        padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 10),
        decoration: BoxDecoration(
          color: _surfaceDark,
          borderRadius: BorderRadius.circular(12),
          border: Border.all(color: _borderDark, width: 1),
        ),
        child: Row(
          mainAxisSize: MainAxisSize.min,
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(icon, size: 18, color: AppTheme.surface),
            const SizedBox(width: 8),
            Text(
              label,
              style: const TextStyle(
                fontFamily: AppTheme.fontFamily,
                fontSize: 12,
                fontWeight: FontWeight.w600,
                color: AppTheme.surface,
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildConfirmationActions() {
    return Row(
      children: [
        Expanded(
          child: SizedBox(
            height: 48,
            child: OutlinedButton.icon(
              onPressed: _isLoading ? null : _retakePhoto,
              style: OutlinedButton.styleFrom(
                foregroundColor: AppTheme.surface,
                side: const BorderSide(color: _borderDark, width: 1),
                shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(10),
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
        ),
        const SizedBox(width: 12),
        Expanded(
          flex: 2,
          child: SizedBox(
            height: 48,
            child: ElevatedButton.icon(
              onPressed: _isLoading ? null : _confirmAndReturnPhoto,
              style: ElevatedButton.styleFrom(
                backgroundColor: AppTheme.primary,
                foregroundColor: AppTheme.surface,
                elevation: 0,
                shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(10),
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
        ),
      ],
    );
  }
}

class _ViewfinderGridPainter extends CustomPainter {
  @override
  void paint(Canvas canvas, Size size) {
    final paint = Paint()
      ..color = const Color(0xFF334155).withValues(alpha: 0.45)
      ..strokeWidth = 0.8;

    canvas.drawLine(Offset(0, size.height / 3), Offset(size.width, size.height / 3), paint);
    canvas.drawLine(Offset(0, 2 * size.height / 3), Offset(size.width, 2 * size.height / 3), paint);
    canvas.drawLine(Offset(size.width / 3, 0), Offset(size.width / 3, size.height), paint);
    canvas.drawLine(Offset(2 * size.width / 3, 0), Offset(2 * size.width / 3, size.height), paint);
  }

  @override
  bool shouldRepaint(covariant CustomPainter oldDelegate) => false;
}
