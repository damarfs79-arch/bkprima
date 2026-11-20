import 'dart:io';
import 'package:flutter/material.dart';
import '../models/violation_model.dart';
import '../services/api_service.dart';

class ViolationProvider extends ChangeNotifier {
  ViolationStats? _stats;
  List<ViolationRecord> _violations = [];
  List<SuratPanggilan> _suratPanggilan = [];
  bool _isLoading = false;
  bool _isSubmitting = false;
  String? _message;

  ViolationStats? get stats => _stats;
  List<ViolationRecord> get violations => _violations;
  List<SuratPanggilan> get suratPanggilan => _suratPanggilan;
  bool get isLoading => _isLoading;
  bool get isSubmitting => _isSubmitting;
  String? get message => _message;

  Future<void> fetchViolations() async {
    _isLoading = true;
    notifyListeners();

    final result = await ApiService.getViolations();
    _isLoading = false;

    if (result['success'] == true) {
      _stats = result['stats'];
      _violations = result['violations'];
      _suratPanggilan = result['surat_panggilan'];
    }
    notifyListeners();
  }

  Future<bool> createViolation({
    int? studentId,
    required String studentName,
    required String studentClass,
    required String category,
    required String violationName,
    int? points,
    required String penaltyAction,
    required String notes,
    File? photoFile,
  }) async {
    _isSubmitting = true;
    _message = null;
    notifyListeners();

    final result = await ApiService.storeViolation(
      studentId: studentId,
      studentName: studentName,
      studentClass: studentClass,
      category: category,
      violationName: violationName,
      points: points,
      penaltyAction: penaltyAction,
      notes: notes,
      photoFile: photoFile,
    );

    _isSubmitting = false;
    _message = result['message'];

    if (result['success'] == true) {
      await fetchViolations();
      return true;
    }
    notifyListeners();
    return false;
  }
}
