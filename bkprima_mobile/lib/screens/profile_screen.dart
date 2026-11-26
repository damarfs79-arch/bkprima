import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:image_picker/image_picker.dart';
import 'package:provider/provider.dart';
import '../core/theme/app_theme.dart';
import '../core/widgets/app_surface.dart';
import '../core/widgets/app_text_field.dart';
import '../providers/auth_provider.dart';
import '../providers/profile_provider.dart';
import '../services/api_service.dart';
import '../widgets/rule_lookup_sheet.dart';
import 'login_screen.dart';

class ProfileScreen extends StatefulWidget {
  const ProfileScreen({super.key});

  @override
  State<ProfileScreen> createState() => _ProfileScreenState();
}

class _ProfileScreenState extends State<ProfileScreen> {
  final _formKey = GlobalKey<FormState>();

  final _nameController = TextEditingController();
  final _jabatanController = TextEditingController();
  final _phoneController = TextEditingController();
  final _emailController = TextEditingController();

  // Password Form Controllers
  final _newPasswordController = TextEditingController();
  final _confirmPasswordController = TextEditingController();

  bool _obscureNew = true;
  bool _obscureConfirm = true;
  bool _isUpdatingPassword = false;

  XFile? _selectedAvatar;
  Uint8List? _avatarBytes;

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      _loadProfile();
    });
  }

  void _loadProfile() async {
    final profileProvider = context.read<ProfileProvider>();
    final authProvider = context.read<AuthProvider>();
    await profileProvider.fetchProfile();
    if (!mounted) return;
    final user = profileProvider.profile ?? authProvider.user;
    if (user != null) {
      _nameController.text = user.name;
      _jabatanController.text = user.jabatan;
      _phoneController.text = user.phone ?? '+62 812-3456-7890';
      _emailController.text = user.email;
      setState(() {});
    }
  }

  @override
  void dispose() {
    _nameController.dispose();
    _jabatanController.dispose();
    _phoneController.dispose();
    _emailController.dispose();
    _newPasswordController.dispose();
    _confirmPasswordController.dispose();
    super.dispose();
  }

  Future<void> _pickAvatar(ImageSource source) async {
    try {
      final picker = ImagePicker();
      final picked = await picker.pickImage(
        source: source,
        imageQuality: 75,
        maxWidth: 1280,
      );
      if (picked != null) {
        final bytes = await picked.readAsBytes();
        setState(() {
          _selectedAvatar = picked;
          _avatarBytes = bytes;
        });
      }
    } catch (e) {
      debugPrint('Gagal memilih foto profil: $e');
    }
  }

  void _showAvatarOptions() {
    HapticFeedback.selectionClick();
    showModalBottomSheet(
      context: context,
      backgroundColor: Colors.transparent,
      builder: (ctx) => Container(
        decoration: const BoxDecoration(
          color: AppTheme.surface,
          borderRadius: BorderRadius.vertical(top: Radius.circular(16)),
          border: Border(
            top: BorderSide(color: AppTheme.border, width: 1),
          ),
        ),
        child: SafeArea(
          top: false,
          child: Padding(
            padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
            child: Column(
              mainAxisSize: MainAxisSize.min,
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
                const Text(
                  'Ubah Foto Profil',
                  style: TextStyle(
                    fontFamily: AppTheme.fontFamily,
                    fontSize: 14,
                    fontWeight: FontWeight.w700,
                    color: AppTheme.textPrimary,
                  ),
                ),
                const SizedBox(height: 12),
                Material(
                  color: Colors.transparent,
                  child: InkWell(
                    onTap: () {
                      Navigator.pop(ctx);
                      _pickAvatar(ImageSource.gallery);
                    },
                    borderRadius: BorderRadius.circular(8),
                    child: const Padding(
                      padding: EdgeInsets.symmetric(horizontal: 12, vertical: 10),
                      child: Row(
                        children: [
                          Icon(Icons.photo_library_outlined, color: AppTheme.primary, size: 20),
                          SizedBox(width: 12),
                          Text(
                            'Pilih dari Galeri',
                            style: TextStyle(
                              fontFamily: AppTheme.fontFamily,
                              fontSize: 13,
                              fontWeight: FontWeight.w600,
                              color: AppTheme.textPrimary,
                            ),
                          ),
                        ],
                      ),
                    ),
                  ),
                ),
                const Divider(color: AppTheme.border, height: 1),
                Material(
                  color: Colors.transparent,
                  child: InkWell(
                    onTap: () {
                      Navigator.pop(ctx);
                      _pickAvatar(ImageSource.camera);
                    },
                    borderRadius: BorderRadius.circular(8),
                    child: const Padding(
                      padding: EdgeInsets.symmetric(horizontal: 12, vertical: 10),
                      child: Row(
                        children: [
                          Icon(Icons.camera_alt_outlined, color: AppTheme.primary, size: 20),
                          SizedBox(width: 12),
                          Text(
                            'Ambil Foto',
                            style: TextStyle(
                              fontFamily: AppTheme.fontFamily,
                              fontSize: 13,
                              fontWeight: FontWeight.w600,
                              color: AppTheme.textPrimary,
                            ),
                          ),
                        ],
                      ),
                    ),
                  ),
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }

  void _saveProfile() async {
    if (!_formKey.currentState!.validate()) return;

    final profileProvider = context.read<ProfileProvider>();
    final authProvider = context.read<AuthProvider>();

    final updatedUser = await profileProvider.updateProfile(
      name: _nameController.text.trim(),
      email: _emailController.text.trim(),
      phone: _phoneController.text.trim(),
      jabatan: _jabatanController.text.trim(),
      avatarFile: _selectedAvatar,
    );

    if (updatedUser != null) {
      authProvider.updateUser(updatedUser);
      _selectedAvatar = null;
      _avatarBytes = null;
    }

    if (mounted) {
      final success = updatedUser != null;
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(profileProvider.message ?? (success ? 'Data profil berhasil diperbarui.' : 'Gagal menyimpan profil.')),
          backgroundColor: success ? AppTheme.success : AppTheme.danger,
        ),
      );
    }
  }

  void _handleUpdatePassword() async {
    final newPassword = _newPasswordController.text.trim();
    final confirmPassword = _confirmPasswordController.text.trim();

    if (newPassword.length < 8) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Password baru minimal 8 karakter.'),
          backgroundColor: AppTheme.danger,
        ),
      );
      return;
    }

    if (newPassword != confirmPassword) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Konfirmasi password baru tidak sesuai.'),
          backgroundColor: AppTheme.danger,
        ),
      );
      return;
    }

    setState(() => _isUpdatingPassword = true);

    final provider = context.read<ProfileProvider>();
    final result = await provider.changePassword(
      password: newPassword,
      passwordConfirmation: confirmPassword,
    );

    setState(() => _isUpdatingPassword = false);

    if (mounted) {
      final success = result['success'] == true;
      if (success) {
        _newPasswordController.clear();
        _confirmPasswordController.clear();
      }

      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(result['message'] ?? (success ? 'Password berhasil diperbarui.' : 'Gagal memperbarui password.')),
          backgroundColor: success ? AppTheme.success : AppTheme.danger,
        ),
      );
    }
  }

  void _showServerConfigDialog() async {
    final currentUrl = await ApiService.getBaseUrl();
    final controller = TextEditingController(text: currentUrl);

    if (!mounted) return;

    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      builder: (ctx) => Container(
        padding: EdgeInsets.only(
          left: 16,
          right: 16,
          top: 16,
          bottom: MediaQuery.of(ctx).viewInsets.bottom + 16,
        ),
        decoration: const BoxDecoration(
          color: AppTheme.surface,
          borderRadius: BorderRadius.vertical(top: Radius.circular(20)),
          border: Border(
            top: BorderSide(color: AppTheme.border, width: 1),
            left: BorderSide(color: AppTheme.border, width: 1),
            right: BorderSide(color: AppTheme.border, width: 1),
          ),
        ),
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
            const SizedBox(height: 12),
            const Text(
              'Konfigurasi Endpoint Server',
              style: TextStyle(
                fontFamily: AppTheme.fontFamily,
                fontSize: 15,
                fontWeight: FontWeight.w700,
                color: AppTheme.textPrimary,
              ),
            ),
            const SizedBox(height: 4),
            const Text(
              'Ganti alamat server jika mengakses via LAN Wi-Fi sekolah.',
              style: TextStyle(
                fontFamily: AppTheme.fontFamily,
                fontSize: 11,
                color: AppTheme.textSecondary,
              ),
            ),
            const SizedBox(height: 12),
            AppTextField(
              controller: controller,
              label: 'URL Base API',
              hintText: 'http://192.168.1.x:8000/api',
            ),
            const SizedBox(height: 16),
            SizedBox(
              width: double.infinity,
              child: ElevatedButton(
                onPressed: () async {
                  await ApiService.setBaseUrl(controller.text.trim());
                  final savedUrl = await ApiService.getBaseUrl();
                  final check = await ApiService.testConnection();
                  if (ctx.mounted) {
                    Navigator.pop(ctx);
                  }
                  if (mounted) {
                    ScaffoldMessenger.of(context).showSnackBar(
                      SnackBar(
                        content: Text(
                          check['success'] == true
                              ? 'Terhubung ke API: $savedUrl'
                              : 'Endpoint disimpan ($savedUrl), namun gagal tes koneksi: ${check['message']}',
                        ),
                        backgroundColor: check['success'] == true ? AppTheme.success : AppTheme.warning,
                      ),
                    );
                  }
                },
                child: const Text('Simpan Endpoint'),
              ),
            ),
          ],
        ),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    final profileProvider = context.watch<ProfileProvider>();
    final auth = context.watch<AuthProvider>();
    final user = profileProvider.profile ?? auth.user;

    final initial = user != null && user.name.isNotEmpty
        ? user.name.trim().split(' ').map((e) => e[0]).take(2).join('').toUpperCase()
        : 'DH';

    return Container(
      color: AppTheme.background,
      child: SingleChildScrollView(
        padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
        child: Form(
          key: _formKey,
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              // Header Row
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  const Text(
                    'Profil Guru',
                    style: TextStyle(
                      fontFamily: AppTheme.fontFamily,
                      fontSize: 20,
                      fontWeight: FontWeight.w800,
                      color: AppTheme.textPrimary,
                      letterSpacing: -0.4,
                    ),
                  ),
                  ElevatedButton(
                    onPressed: profileProvider.isSaving ? null : _saveProfile,
                    style: ElevatedButton.styleFrom(
                      backgroundColor: AppTheme.primary,
                      padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
                      visualDensity: VisualDensity.compact,
                    ),
                    child: profileProvider.isSaving
                        ? const SizedBox(
                            width: 14,
                            height: 14,
                            child: CircularProgressIndicator(strokeWidth: 2, color: Colors.white),
                          )
                        : const Text(
                            'Simpan',
                            style: TextStyle(
                              fontFamily: AppTheme.fontFamily,
                              fontSize: 12,
                              fontWeight: FontWeight.w700,
                              color: Colors.white,
                            ),
                          ),
                  ),
                ],
              ),
              const SizedBox(height: 14),

              // 1. Teacher Header Card (Interactive Avatar)
              AppSurface(
                padding: const EdgeInsets.all(14),
                child: Row(
                  children: [
                    // Circular Avatar (64x64) with camera overlay
                    GestureDetector(
                      onTap: _showAvatarOptions,
                      child: Stack(
                        clipBehavior: Clip.none,
                        children: [
                          Container(
                            width: 64,
                            height: 64,
                            decoration: BoxDecoration(
                              color: AppTheme.surfaceMuted,
                              shape: BoxShape.circle,
                              border: Border.all(color: AppTheme.border, width: 1.5),
                            ),
                            clipBehavior: Clip.antiAlias,
                            child: _avatarBytes != null
                                ? Image.memory(
                                    _avatarBytes!,
                                    width: 64,
                                    height: 64,
                                    fit: BoxFit.cover,
                                  )
                                : user != null && user.avatarUrl != null && user.avatarUrl!.isNotEmpty
                                    ? Image.network(
                                        user.avatarUrl!,
                                        width: 64,
                                        height: 64,
                                        fit: BoxFit.cover,
                                        errorBuilder: (context, error, stackTrace) => _buildAvatarFallback(initial),
                                      )
                                    : _buildAvatarFallback(initial),
                          ),
                          Positioned(
                            bottom: 0,
                            right: 0,
                            child: Container(
                              width: 22,
                              height: 22,
                              decoration: BoxDecoration(
                                color: Colors.white,
                                shape: BoxShape.circle,
                                border: Border.all(color: AppTheme.border, width: 1),
                              ),
                              alignment: Alignment.center,
                              child: const Icon(
                                Icons.camera_alt_outlined,
                                size: 12,
                                color: AppTheme.primary,
                              ),
                            ),
                          ),
                        ],
                      ),
                    ),
                    const SizedBox(width: 14),
                    // Teacher Name & Role Only (No NIP)
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(
                            user?.name ?? 'Dra. Hj. Nurul Hidayati, M.Pd',
                            style: const TextStyle(
                              fontFamily: AppTheme.fontFamily,
                              fontSize: 15,
                              fontWeight: FontWeight.w700,
                              color: AppTheme.textPrimary,
                              letterSpacing: -0.2,
                            ),
                          ),
                          const SizedBox(height: 3),
                          Text(
                            user?.jabatan ?? 'Koordinator BK & Kedisiplinan',
                            style: const TextStyle(
                              fontFamily: AppTheme.fontFamily,
                              fontSize: 12,
                              fontWeight: FontWeight.w500,
                              color: AppTheme.textSecondary,
                            ),
                          ),
                        ],
                      ),
                    ),
                  ],
                ),
              ),
              const SizedBox(height: 12),

              // 2. Compact Fast Rule Lookup Tile
              AppSurface(
                padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 12),
                onTap: () {
                  HapticFeedback.selectionClick();
                  RuleLookupSheet.show(context);
                },
                child: Row(
                  children: [
                    Container(
                      width: 32,
                      height: 32,
                      decoration: BoxDecoration(
                        color: AppTheme.primaryLight,
                        borderRadius: BorderRadius.circular(8),
                      ),
                      alignment: Alignment.center,
                      child: const Icon(Icons.menu_book_outlined, size: 16, color: AppTheme.primary),
                    ),
                    const SizedBox(width: 10),
                    const Expanded(
                      child: Text(
                        'Buku Saku Tata Tertib & Poin',
                        style: TextStyle(
                          fontFamily: AppTheme.fontFamily,
                          fontSize: 13,
                          fontWeight: FontWeight.w600,
                          color: AppTheme.textPrimary,
                        ),
                      ),
                    ),
                    const Icon(Icons.chevron_right_rounded, size: 18, color: AppTheme.textMuted),
                  ],
                ),
              ),
              const SizedBox(height: 12),

              // 3. Clean Profile Form (Concise Labels, No NIP/NUPTK)
              AppSurface(
                padding: const EdgeInsets.all(14),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    AppTextField(
                      controller: _nameController,
                      label: 'Nama Lengkap',
                      hintText: 'Nama lengkap pendidik...',
                      validator: (val) => val == null || val.trim().isEmpty ? 'Nama lengkap wajib diisi' : null,
                    ),
                    const SizedBox(height: 10),
                    AppTextField(
                      controller: _jabatanController,
                      label: 'Jabatan',
                      hintText: 'Jabatan atau penugasan...',
                    ),
                    const SizedBox(height: 10),
                    AppTextField(
                      controller: _phoneController,
                      label: 'Nomor WhatsApp',
                      hintText: '+62 8xx-xxxx-xxxx',
                      keyboardType: TextInputType.phone,
                    ),
                    const SizedBox(height: 10),
                    AppTextField(
                      controller: _emailController,
                      label: 'Email',
                      hintText: 'nama@smkpgri5jember.sch.id',
                      keyboardType: TextInputType.emailAddress,
                      validator: (val) => val == null || val.trim().isEmpty ? 'Email wajib diisi' : null,
                    ),
                  ],
                ),
              ),
              const SizedBox(height: 12),

              // 4. Ubah Password (Keamanan Akun)
              AppSurface(
                padding: const EdgeInsets.all(14),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    const Text(
                      'Keamanan Akun',
                      style: TextStyle(
                        fontFamily: AppTheme.fontFamily,
                        fontSize: 13,
                        fontWeight: FontWeight.w700,
                        color: AppTheme.textPrimary,
                      ),
                    ),
                    const SizedBox(height: 12),
                    AppTextField(
                      controller: _newPasswordController,
                      label: 'Password Baru',
                      hintText: 'Minimal 8 karakter',
                      obscureText: _obscureNew,
                      suffixIcon: IconButton(
                        icon: Icon(
                          _obscureNew ? Icons.visibility_off_outlined : Icons.visibility_outlined,
                          size: 18,
                          color: AppTheme.textSecondary,
                        ),
                        onPressed: () => setState(() => _obscureNew = !_obscureNew),
                      ),
                    ),
                    const SizedBox(height: 10),
                    AppTextField(
                      controller: _confirmPasswordController,
                      label: 'Konfirmasi Password Baru',
                      hintText: 'Ulangi password baru',
                      obscureText: _obscureConfirm,
                      suffixIcon: IconButton(
                        icon: Icon(
                          _obscureConfirm ? Icons.visibility_off_outlined : Icons.visibility_outlined,
                          size: 18,
                          color: AppTheme.textSecondary,
                        ),
                        onPressed: () => setState(() => _obscureConfirm = !_obscureConfirm),
                      ),
                    ),
                    const SizedBox(height: 14),
                    SizedBox(
                      width: double.infinity,
                      child: ElevatedButton(
                        onPressed: _isUpdatingPassword ? null : _handleUpdatePassword,
                        style: ElevatedButton.styleFrom(
                          backgroundColor: AppTheme.primary,
                          padding: const EdgeInsets.symmetric(vertical: 12),
                          shape: RoundedRectangleBorder(
                            borderRadius: BorderRadius.circular(10),
                          ),
                        ),
                        child: _isUpdatingPassword
                            ? const SizedBox(
                                width: 16,
                                height: 16,
                                child: CircularProgressIndicator(strokeWidth: 2, color: Colors.white),
                              )
                            : const Text(
                                'Perbarui Password',
                                style: TextStyle(
                                  fontFamily: AppTheme.fontFamily,
                                  fontSize: 13,
                                  fontWeight: FontWeight.w700,
                                  color: Colors.white,
                                ),
                              ),
                      ),
                    ),
                  ],
                ),
              ),
              const SizedBox(height: 12),

              // 5. Diagnostics & Logout
              AppSurface(
                padding: const EdgeInsets.all(14),
                child: Column(
                  children: [
                    InkWell(
                      onTap: _showServerConfigDialog,
                      borderRadius: BorderRadius.circular(8),
                      child: const Padding(
                        padding: EdgeInsets.symmetric(vertical: 6),
                        child: Row(
                          children: [
                            Icon(Icons.dns_outlined, color: AppTheme.textPrimary, size: 20),
                            SizedBox(width: 12),
                            Expanded(
                              child: Text(
                                'Konfigurasi Endpoint API',
                                style: TextStyle(
                                  fontFamily: AppTheme.fontFamily,
                                  fontSize: 13,
                                  fontWeight: FontWeight.w600,
                                  color: AppTheme.textPrimary,
                                ),
                              ),
                            ),
                            Icon(Icons.chevron_right_rounded, size: 18, color: AppTheme.textMuted),
                          ],
                        ),
                      ),
                    ),
                    const Divider(color: AppTheme.border, height: 16),
                    InkWell(
                      onTap: () async {
                        HapticFeedback.selectionClick();
                        final navigator = Navigator.of(context, rootNavigator: true);
                        final profileProvider = context.read<ProfileProvider>();
                        await auth.logout();
                        profileProvider.clearProfile();
                        navigator.pushAndRemoveUntil(
                          MaterialPageRoute(builder: (_) => const LoginScreen()),
                          (route) => false,
                        );
                      },
                      borderRadius: BorderRadius.circular(8),
                      child: const Padding(
                        padding: EdgeInsets.symmetric(vertical: 6),
                        child: Row(
                          children: [
                            Icon(Icons.logout_rounded, color: AppTheme.danger, size: 20),
                            SizedBox(width: 12),
                            Expanded(
                              child: Text(
                                'Keluar Akun',
                                style: TextStyle(
                                  fontFamily: AppTheme.fontFamily,
                                  fontSize: 13,
                                  fontWeight: FontWeight.w600,
                                  color: AppTheme.danger,
                                ),
                              ),
                            ),
                            Icon(Icons.chevron_right_rounded, size: 18, color: AppTheme.textMuted),
                          ],
                        ),
                      ),
                    ),
                  ],
                ),
              ),
              const SizedBox(height: 24),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildAvatarFallback(String initial) {
    return Center(
      child: Text(
        initial,
        style: const TextStyle(
          fontFamily: AppTheme.fontFamily,
          fontSize: 20,
          fontWeight: FontWeight.w700,
          color: AppTheme.textMedium,
        ),
      ),
    );
  }
}
