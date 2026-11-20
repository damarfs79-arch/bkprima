import 'package:flutter/material.dart';
import 'package:image_picker/image_picker.dart';
import '../models/user_model.dart';
import '../services/api_service.dart';

class ProfileProvider extends ChangeNotifier {
  UserModel? _profile;
  bool _isLoading = false;
  bool _isSaving = false;
  String? _message;

  UserModel? get profile => _profile;
  bool get isLoading => _isLoading;
  bool get isSaving => _isSaving;
  String? get message => _message;

  Future<void> fetchProfile() async {
    _isLoading = true;
    notifyListeners();

    final result = await ApiService.getProfile();
    _isLoading = false;

    if (result['success'] == true) {
      _profile = result['user'];
    }
    notifyListeners();
  }

  Future<UserModel?> updateProfile({
    required String name,
    required String email,
    String? phone,
    String? jabatan,
    List<String>? rombel,
    XFile? avatarFile,
  }) async {
    _isSaving = true;
    _message = null;
    notifyListeners();

    final result = await ApiService.updateProfile(
      name: name,
      email: email,
      phone: phone,
      jabatan: jabatan,
      rombel: rombel,
      avatarFile: avatarFile,
    );

    _isSaving = false;
    _message = result['message'];

    if (result['success'] == true && result['user'] != null) {
      _profile = result['user'] as UserModel;
      notifyListeners();
      return _profile;
    }

    if (result['success'] == true) {
      await fetchProfile();
      return _profile;
    }

    notifyListeners();
    return null;
  }

  Future<Map<String, dynamic>> changePassword({
    required String currentPassword,
    required String password,
    required String passwordConfirmation,
  }) async {
    return await ApiService.changePassword(
      currentPassword: currentPassword,
      password: password,
      passwordConfirmation: passwordConfirmation,
    );
  }
}
