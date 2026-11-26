import 'package:flutter/material.dart';
import '../models/attendance_model.dart';
import '../models/absence_alert_model.dart';
import '../services/api_service.dart';

class AttendanceProvider extends ChangeNotifier {
  AttendanceStats? _stats;
  List<AttendanceStudent> _students = [];
  List<String> _availableClasses = [];
  List<AbsenceAlert> _absenceAlerts = [];
  String _selectedClass = 'Semua Rombel';
  String _searchQuery = '';
  bool _isLoading = false;
  bool _isSyncing = false;
  bool _hasShownStartupAlert = false;
  String? _message;

  AttendanceStats? get stats => _stats;
  List<AttendanceStudent> get students => _students;
  List<String> get availableClasses => _availableClasses;
  List<AbsenceAlert> get absenceAlerts => _absenceAlerts;
  int get absenceAlertCount => _absenceAlerts.length;
  bool get hasShownStartupAlert => _hasShownStartupAlert;
  String get selectedClass => _selectedClass;
  String get searchQuery => _searchQuery;
  bool get isLoading => _isLoading;
  bool get isSyncing => _isSyncing;
  String? get message => _message;

  void markStartupAlertShown() {
    _hasShownStartupAlert = true;
  }

  void resetStartupAlert() {
    _hasShownStartupAlert = false;
  }

  Future<void> fetchAbsenceAlerts() async {
    final res = await ApiService.getAbsenceAlerts();
    if (res['success'] == true) {
      _absenceAlerts = (res['alerts'] as List<AbsenceAlert>?) ?? [];
      notifyListeners();
    }
  }

  Future<void> fetchAttendance() async {
    _isLoading = true;
    notifyListeners();

    final results = await Future.wait([
      ApiService.getAttendance(
        className: _selectedClass == 'Semua Rombel' ? null : _selectedClass,
        search: _searchQuery,
      ),
      ApiService.getAbsenceAlerts(),
    ]);

    final result = results[0];
    final alertResult = results[1];

    _isLoading = false;
    if (result['success'] == true) {
      _stats = result['stats'];
      _students = result['students'];
      _availableClasses = ['Semua Rombel', ...(result['available_classes'] as List<String>)];
    }
    if (alertResult['success'] == true) {
      _absenceAlerts = (alertResult['alerts'] as List<AbsenceAlert>?) ?? [];
    }
    notifyListeners();
  }

  void setClassFilter(String className) {
    _selectedClass = className;
    fetchAttendance();
  }

  void setSearchQuery(String query) {
    _searchQuery = query;
    fetchAttendance();
  }

  Future<bool> syncHadirMu() async {
    _isSyncing = true;
    notifyListeners();

    final result = await ApiService.syncAttendance();
    _isSyncing = false;
    _message = result['message'];

    if (result['success'] == true) {
      await fetchAttendance();
      return true;
    }
    notifyListeners();
    return false;
  }

  /// Update status absensi satu siswa (Hadir, Terlambat, Izin, Sakit, Alpa)
  Future<bool> updateStudentStatus({
    required int studentId,
    required String newStatus,
    String? checkInTime,
    String? notes,
  }) async {
    // 1. Optimistic update di list lokal agar UI langsung merespons seketika
    final index = _students.indexWhere((s) => s.id == studentId);
    if (index != -1) {
      final old = _students[index];
      _students[index] = AttendanceStudent(
        id: old.id,
        nisn: old.nisn,
        name: old.name,
        className: old.className,
        gender: old.gender,
        status: newStatus,
        checkInTime: checkInTime ?? (newStatus == 'Hadir' ? '06:45' : (newStatus == 'Terlambat' ? '07:15' : '-')),
        parentName: old.parentName,
        parentPhone: old.parentPhone,
        totalPoints: old.totalPoints,
      );
      _recalculateLocalStats();
      notifyListeners();
    }

    // 2. Simpan ke database melalui API
    final result = await ApiService.updateAttendanceStatus(
      studentId: studentId,
      status: newStatus,
      checkInTime: checkInTime,
      notes: notes,
    );

    _message = result['message'];
    if (result['success'] == true) {
      // Re-sync data akurat dari server di latar belakang
      final serverRes = await ApiService.getAttendance(
        className: _selectedClass == 'Semua Rombel' ? null : _selectedClass,
        search: _searchQuery,
      );
      if (serverRes['success'] == true) {
        _stats = serverRes['stats'];
        _students = serverRes['students'];
        notifyListeners();
      }
      await fetchAbsenceAlerts();
      return true;
    }

    // Jika gagal, refresh kembali ke data sebelumnya
    await fetchAttendance();
    return false;
  }

  /// Tandai seluruh siswa dalam rombel menjadi Hadir sekaligus
  Future<bool> markAllPresent() async {
    _isLoading = true;
    notifyListeners();

    final result = await ApiService.bulkAttendance(
      className: _selectedClass == 'Semua Rombel' ? null : _selectedClass,
      status: 'Hadir',
    );

    _isLoading = false;
    _message = result['message'];

    if (result['success'] == true) {
      await fetchAttendance();
      return true;
    }

    notifyListeners();
    return false;
  }

  void _recalculateLocalStats() {
    int hadir = 0;
    int terlambat = 0;
    int alpa = 0;

    for (final s in _students) {
      if (s.status == 'Hadir') {
        hadir++;
      } else if (s.status == 'Terlambat') {
        terlambat++;
      } else {
        alpa++;
      }
    }

    _stats = AttendanceStats(
      total: _students.length,
      hadir: hadir,
      terlambat: terlambat,
      alpaIzinSakit: alpa,
    );
  }
}
