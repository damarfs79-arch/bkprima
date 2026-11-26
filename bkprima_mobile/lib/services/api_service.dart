import 'dart:convert';
import 'dart:io';
import 'package:flutter/foundation.dart';
import 'package:http/http.dart' as http;
import 'package:image_picker/image_picker.dart';
import 'package:shared_preferences/shared_preferences.dart';
import '../models/user_model.dart';
import '../models/attendance_model.dart';
import '../models/violation_model.dart';

class ApiService {
  // Deteksi URL server backend: Android Emulator menggunakan 10.0.2.2, Browser Web/Desktop menggunakan 127.0.0.1
  static String get defaultBaseUrl {
    if (kIsWeb) {
      return 'http://127.0.0.1:8000/api';
    }
    try {
      if (Platform.isAndroid) {
        return 'http://10.0.2.2:8000/api';
      }
    } catch (_) {}
    return 'http://127.0.0.1:8000/api';
  }

  static String? _cachedBaseUrl;

  static String normalizeBaseUrl(String rawUrl) {
    String url = rawUrl.trim();
    if (url.isEmpty) return defaultBaseUrl;
    if (!url.startsWith('http://') && !url.startsWith('https://')) {
      url = 'http://$url';
    }
    while (url.endsWith('/')) {
      url = url.substring(0, url.length - 1);
    }
    if (!url.endsWith('/api')) {
      url = '$url/api';
    }
    return url;
  }

  static Future<String> getBaseUrl() async {
    final prefs = await SharedPreferences.getInstance();
    String current = prefs.getString('server_url') ?? defaultBaseUrl;
    current = normalizeBaseUrl(current);

    // Normalisasi jika running di browser (Flutter Web) tapi tersimpan IP emulator Android
    if (kIsWeb && current.contains('10.0.2.2')) {
      current = current.replaceAll('10.0.2.2', '127.0.0.1');
      await prefs.setString('server_url', current);
    }

    _cachedBaseUrl = current;
    return current;
  }

  static Future<void> setBaseUrl(String url) async {
    final prefs = await SharedPreferences.getInstance();
    _cachedBaseUrl = normalizeBaseUrl(url);
    await prefs.setString('server_url', _cachedBaseUrl!);
  }

  static String getRootUrlSync() {
    final base = _cachedBaseUrl ?? defaultBaseUrl;
    return base.endsWith('/api') ? base.substring(0, base.length - 4) : base;
  }

  static String? resolveMediaUrl(String? rawUrl) {
    if (rawUrl == null || rawUrl.trim().isEmpty) return null;
    final trimmed = rawUrl.trim();
    final root = getRootUrlSync();
    if (trimmed.startsWith('/')) {
      return '$root$trimmed';
    }
    final uri = Uri.tryParse(trimmed);
    if (uri != null && (uri.host == 'localhost' || uri.host == '127.0.0.1')) {
      final rootUri = Uri.tryParse(root);
      if (rootUri != null && rootUri.host.isNotEmpty) {
        return uri.replace(
          scheme: rootUri.scheme,
          host: rootUri.host,
          port: rootUri.hasPort ? rootUri.port : null,
        ).toString();
      }
    }
    return trimmed;
  }

  static Map<String, dynamic> _safeDecode(String body) {
    try {
      final decoded = jsonDecode(body);
      if (decoded is Map<String, dynamic>) {
        return decoded;
      }
      return {};
    } catch (_) {
      return {};
    }
  }

  static Future<Map<String, dynamic>> testConnection([String? customUrl]) async {
    try {
      final url = customUrl != null ? normalizeBaseUrl(customUrl) : await getBaseUrl();
      final response = await http.get(
        Uri.parse('$url/health'),
        headers: {'Accept': 'application/json'},
      ).timeout(const Duration(seconds: 6));
      final data = _safeDecode(response.body);
      if (response.statusCode == 200 && data['success'] == true) {
        return {'success': true, 'message': 'Terhubung ke server BK Prima.'};
      }
      return {'success': false, 'message': 'Server merespons (${response.statusCode}), tetapi endpoint API tidak sesuai.'};
    } catch (e) {
      return {'success': false, 'message': 'Tidak dapat menjangkau server: $e'};
    }
  }

  static Future<String?> getToken() async {
    final prefs = await SharedPreferences.getInstance();
    return prefs.getString('auth_token');
  }

  static Future<void> saveToken(String token) async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.setString('auth_token', token);
  }

  static Future<void> removeToken() async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.remove('auth_token');
  }

  static Future<Map<String, String>> _headers() async {
    final token = await getToken();
    return {
      'Accept': 'application/json',
      'Content-Type': 'application/json',
      if (token != null) 'Authorization': 'Bearer $token',
    };
  }

  /// 1. Auth: Login
  static Future<Map<String, dynamic>> login(String identifier, String password) async {
    try {
      final url = await getBaseUrl();
      final response = await http.post(
        Uri.parse('$url/login'),
        headers: {'Accept': 'application/json', 'Content-Type': 'application/json'},
        body: jsonEncode({
          'identifier': identifier.trim(),
          'password': password.trim(),
        }),
      ).timeout(const Duration(seconds: 12));

      final data = _safeDecode(response.body);
      if (response.statusCode == 200 && data['success'] == true) {
        if (data['token'] != null) {
          await saveToken(data['token']);
        }
        return {
          'success': true,
          'user': UserModel.fromJson(data['user']),
          'message': data['message'] ?? 'Login berhasil',
        };
      } else {
        return {
          'success': false,
          'message': data['message'] ?? 'NIP/Username atau kata sandi tidak sesuai.',
        };
      }
    } catch (e) {
      return {
        'success': false,
        'message': 'Gagal terhubung ke server BK Prima: $e',
      };
    }
  }

  /// 1b. Auth: Logout
  static Future<void> logout() async {
    try {
      final url = await getBaseUrl();
      await http.post(
        Uri.parse('$url/logout'),
        headers: await _headers(),
      ).timeout(const Duration(seconds: 5));
    } catch (_) {
      // Tetap lanjut hapus token lokal meskipun jaringan sedang offline
    } finally {
      await removeToken();
    }
  }

  /// 2. Presensi Harian
  static Future<Map<String, dynamic>> getAttendance({
    String? date,
    String? className,
    String? search,
  }) async {
    try {
      final url = await getBaseUrl();
      final queryParams = <String, String>{};
      if (date != null) queryParams['date'] = date;
      if (className != null && className != 'Semua Kelas' && className != 'Semua Rombel') {
        queryParams['class_name'] = className;
      }
      if (search != null && search.isNotEmpty) queryParams['search'] = search;

      final uri = Uri.parse('$url/absensi').replace(queryParameters: queryParams);
      final response = await http.get(uri, headers: await _headers()).timeout(const Duration(seconds: 12));

      if (response.statusCode == 200) {
        final data = _safeDecode(response.body);
        final stats = AttendanceStats.fromJson(data['stats'] ?? {});
        final List studentsJson = data['students'] ?? [];
        final students = studentsJson.map((s) => AttendanceStudent.fromJson(s)).toList();
        final List<String> availableClasses = List<String>.from(data['available_classes'] ?? []);

        return {
          'success': true,
          'stats': stats,
          'students': students,
          'available_classes': availableClasses,
          'date': data['date'] ?? '',
        };
      } else {
        return {'success': false, 'message': 'Gagal memuat data presensi.'};
      }
    } catch (e) {
      return {'success': false, 'message': 'Kesalahan jaringan: $e'};
    }
  }

  /// 3. Sinkronkan Mesin HadirMu
  static Future<Map<String, dynamic>> syncAttendance() async {
    try {
      final url = await getBaseUrl();
      final response = await http.post(
        Uri.parse('$url/absensi/sync'),
        headers: await _headers(),
      ).timeout(const Duration(seconds: 15));
      final data = _safeDecode(response.body);
      return {
        'success': response.statusCode == 200 && data['success'] == true,
        'message': data['message'] ?? 'Sinkronisasi selesai.',
      };
    } catch (e) {
      return {'success': false, 'message': 'Gagal sinkronisasi: $e'};
    }
  }

  /// 3a. Update Status Absensi Siswa (Hadir, Terlambat, Izin, Sakit, Alpa)
  static Future<Map<String, dynamic>> updateAttendanceStatus({
    required int studentId,
    required String status,
    String? checkInTime,
    String? notes,
    String? date,
  }) async {
    try {
      final url = await getBaseUrl();
      final payload = <String, dynamic>{
        'student_id': studentId,
        'status': status,
      };
      if (checkInTime != null) payload['check_in_time'] = checkInTime;
      if (notes != null) payload['notes'] = notes;
      if (date != null) payload['date'] = date;

      final response = await http.post(
        Uri.parse('$url/absensi/status'),
        headers: await _headers(),
        body: jsonEncode(payload),
      ).timeout(const Duration(seconds: 12));

      final data = _safeDecode(response.body);
      return {
        'success': response.statusCode == 200 && data['success'] == true,
        'message': data['message'] ?? 'Status presensi berhasil diperbarui.',
        'student': data['student'],
      };
    } catch (e) {
      return {'success': false, 'message': 'Gagal memperbarui presensi: $e'};
    }
  }

  /// 3b. Tandai Massal Presensi Siswa (Tandai Semua Hadir per Rombel)
  static Future<Map<String, dynamic>> bulkAttendance({
    String? className,
    String status = 'Hadir',
    String? date,
  }) async {
    try {
      final url = await getBaseUrl();
      final payload = <String, dynamic>{
        'status': status,
      };
      if (className != null && className != 'Semua Kelas' && className != 'Semua Rombel') {
        payload['class_name'] = className;
      }
      if (date != null) payload['date'] = date;

      final response = await http.post(
        Uri.parse('$url/absensi/bulk'),
        headers: await _headers(),
        body: jsonEncode(payload),
      ).timeout(const Duration(seconds: 15));

      final data = _safeDecode(response.body);
      return {
        'success': response.statusCode == 200 && data['success'] == true,
        'message': data['message'] ?? 'Presensi massal berhasil diperbarui.',
        'count': data['count'] ?? 0,
      };
    } catch (e) {
      return {'success': false, 'message': 'Gagal absensi massal: $e'};
    }
  }

  /// 3c. Statistik Kehadiran & Kedisiplinan (Rombel Guru Hari Ini / 1 Bulan Seluruh Siswa)
  static Future<Map<String, dynamic>> getOverallStats({
    String scope = 'my_rombel_today',
    int? month,
    int? year,
  }) async {
    try {
      final url = await getBaseUrl();
      final queryParams = <String, String>{'scope': scope};
      if (month != null) queryParams['month'] = month.toString();
      if (year != null) queryParams['year'] = year.toString();

      final uri = Uri.parse('$url/statistik').replace(queryParameters: queryParams);
      final response = await http.get(
        uri,
        headers: await _headers(),
      ).timeout(const Duration(seconds: 12));

      if (response.statusCode == 200) {
        final data = _safeDecode(response.body);
        return {
          'success': true,
          'data': data,
        };
      }
      return {'success': false, 'message': 'Gagal mengambil data statistik master.'};
    } catch (e) {
      return {'success': false, 'message': 'Kesalahan jaringan: $e'};
    }
  }

  /// 3d. Statistik Kehadiran dari Dashboard Web Admin (Pie Chart, Status 4 Kategori, Filter Bulan & Angkatan)
  static Future<Map<String, dynamic>> getAdminAttendanceStats({
    String bulan = 'mei',
    String angkatan = 'all',
  }) async {
    try {
      final url = await getBaseUrl();
      final uri = Uri.parse('$url/statistik-kehadiran').replace(queryParameters: {
        'bulan': bulan,
        'angkatan': angkatan,
      });

      final response = await http.get(
        uri,
        headers: await _headers(),
      ).timeout(const Duration(seconds: 12));

      if (response.statusCode == 200) {
        final data = _safeDecode(response.body);
        return {
          'success': true,
          'data': data,
        };
      }
      return {'success': false, 'message': 'Gagal mengambil statistik kehadiran admin.'};
    } catch (e) {
      return {'success': false, 'message': 'Kesalahan jaringan: $e'};
    }
  }

  /// 4. Catatan Pelanggaran & Panggilan
  static Future<Map<String, dynamic>> getViolations() async {
    try {
      final url = await getBaseUrl();
      final response = await http.get(
        Uri.parse('$url/pelanggaran'),
        headers: await _headers(),
      ).timeout(const Duration(seconds: 12));

      if (response.statusCode == 200) {
        final data = _safeDecode(response.body);
        final stats = ViolationStats.fromJson(data['stats'] ?? {});
        final List vList = data['violations'] ?? [];
        final List spList = data['surat_panggilan'] ?? [];

        return {
          'success': true,
          'stats': stats,
          'violations': vList.map((v) => ViolationRecord.fromJson(v)).toList(),
          'surat_panggilan': spList.map((sp) => SuratPanggilan.fromJson(sp)).toList(),
        };
      }
      return {'success': false, 'message': 'Gagal mengambil rekaman pelanggaran.'};
    } catch (e) {
      return {'success': false, 'message': 'Kesalahan jaringan: $e'};
    }
  }

  /// 5. Input Pelanggaran Baru (Multipart File Foto Bukti)
  static Future<Map<String, dynamic>> storeViolation({
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
    try {
      final url = await getBaseUrl();
      final uri = Uri.parse('$url/pelanggaran');
      final request = http.MultipartRequest('POST', uri);

      final token = await getToken();
      if (token != null) {
        request.headers['Authorization'] = 'Bearer $token';
      }
      request.headers['Accept'] = 'application/json';

      if (studentId != null) request.fields['student_id'] = studentId.toString();
      request.fields['student_name'] = studentName;
      request.fields['student_class'] = studentClass;
      request.fields['category'] = category;
      request.fields['violation_name'] = violationName;
      if (points != null) request.fields['points'] = points.toString();
      request.fields['penalty_action'] = penaltyAction;
      request.fields['notes'] = notes;

      if (photoFile != null) {
        request.files.add(await http.MultipartFile.fromPath('foto_bukti', photoFile.path));
      }

      final streamedResponse = await request.send().timeout(const Duration(seconds: 25));
      final response = await http.Response.fromStream(streamedResponse);
      final data = _safeDecode(response.body);

      if (response.statusCode == 201 || response.statusCode == 200) {
        return {'success': true, 'message': data['message'] ?? 'Catatan pelanggaran tersimpan.'};
      } else {
        return {'success': false, 'message': data['message'] ?? 'Gagal menyimpan pelanggaran.'};
      }
    } catch (e) {
      return {'success': false, 'message': 'Gagal mengirim form: $e'};
    }
  }

  /// 6. Profil Guru BK
  static Future<Map<String, dynamic>> getProfile() async {
    try {
      final url = await getBaseUrl();
      final response = await http.get(
        Uri.parse('$url/profil'),
        headers: await _headers(),
      ).timeout(const Duration(seconds: 12));
      if (response.statusCode == 200) {
        final data = _safeDecode(response.body);
        return {
          'success': true,
          'user': UserModel.fromJson(data['profile']),
        };
      }
      return {'success': false, 'message': 'Gagal mengambil data profil.'};
    } catch (e) {
      return {'success': false, 'message': 'Kesalahan jaringan: $e'};
    }
  }

  /// 7. Update Profil Guru BK (termasuk upload avatar)
  static Future<Map<String, dynamic>> updateProfile({
    required String name,
    required String email,
    String? phone,
    String? jabatan,
    List<String>? rombel,
    XFile? avatarFile,
  }) async {
    try {
      final url = await getBaseUrl();
      final uri = Uri.parse('$url/profil');
      final request = http.MultipartRequest('POST', uri);

      final token = await getToken();
      if (token != null) {
        request.headers['Authorization'] = 'Bearer $token';
      }
      request.headers['Accept'] = 'application/json';

      request.fields['name'] = name.trim();
      request.fields['email'] = email.trim();
      if (phone != null) request.fields['phone'] = phone.trim();
      if (jabatan != null) request.fields['jabatan'] = jabatan.trim();
      if (rombel != null) {
        for (int i = 0; i < rombel.length; i++) {
          request.fields['rombel[$i]'] = rombel[i];
        }
      }

      if (avatarFile != null) {
        final bytes = await avatarFile.readAsBytes();
        request.files.add(http.MultipartFile.fromBytes(
          'avatar',
          bytes,
          filename: avatarFile.name.isNotEmpty ? avatarFile.name : 'avatar.jpg',
        ));
      }

      final streamedResponse = await request.send().timeout(const Duration(seconds: 20));
      final response = await http.Response.fromStream(streamedResponse);
      final data = _safeDecode(response.body);

      if (response.statusCode == 200 && data['success'] == true) {
        UserModel? updatedUser;
        if (data['profile'] != null) {
          updatedUser = UserModel.fromJson(data['profile']);
        }
        return {
          'success': true,
          'message': data['message'] ?? 'Profil berhasil diperbarui.',
          'user': updatedUser,
        };
      } else {
        return {
          'success': false,
          'message': data['message'] ?? 'Gagal memperbarui profil.',
        };
      }
    } catch (e) {
      return {'success': false, 'message': 'Gagal memperbarui profil: $e'};
    }
  }

  /// 8. Ubah Password Guru BK
  static Future<Map<String, dynamic>> changePassword({
    required String password,
    required String passwordConfirmation,
  }) async {
    try {
      final url = await getBaseUrl();
      final response = await http.put(
        Uri.parse('$url/profile/password'),
        headers: await _headers(),
        body: jsonEncode({
          'password': password,
          'password_confirmation': passwordConfirmation,
        }),
      ).timeout(const Duration(seconds: 12));

      final data = _safeDecode(response.body);
      if (response.statusCode == 200 && data['success'] == true) {
        if (data['token'] != null) {
          await saveToken(data['token'].toString());
        }
        return {
          'success': true,
          'message': data['message'] ?? 'Password berhasil diperbarui.',
        };
      } else {
        return {
          'success': false,
          'message': data['message'] ?? 'Gagal memperbarui password.',
        };
      }
    } catch (e) {
      return {'success': false, 'message': 'Kesalahan jaringan: $e'};
    }
  }

  /// 9. Ambil Daftar Aturan / Jenis Pelanggaran dari Backend
  static Future<Map<String, dynamic>> getRules({String? category}) async {
    try {
      final url = await getBaseUrl();
      final uri = Uri.parse('$url/aturan').replace(
        queryParameters: category != null && category != 'Semua' ? {'category': category} : null,
      );
      final response = await http.get(uri, headers: await _headers()).timeout(const Duration(seconds: 10));

      if (response.statusCode == 200) {
        final data = _safeDecode(response.body);
        return {
          'success': true,
          'rules': data['rules'] ?? [],
        };
      }
      return {'success': false, 'rules': []};
    } catch (e) {
      return {'success': false, 'rules': [], 'message': '$e'};
    }
  }

  /// 10b. Ambil Daftar Tindakan / Sanksi Pembinaan dari Backend
  static Future<List<String>> getSanctions() async {
    try {
      final url = await getBaseUrl();
      final response = await http.get(
        Uri.parse('$url/sanksi'),
        headers: await _headers(),
      ).timeout(const Duration(seconds: 10));

      if (response.statusCode == 200) {
        final data = _safeDecode(response.body);
        final List raw = data['sanctions'] ?? [];
        final list = raw
            .map((item) => (item is Map ? item['name']?.toString() : item?.toString()) ?? '')
            .where((name) => name.trim().isNotEmpty)
            .toList();
        if (list.isNotEmpty) return list;
      }
    } catch (_) {}
    return [];
  }

  /// 10. Tambah Aturan / Jenis Pelanggaran Baru ke Backend
  static Future<Map<String, dynamic>> storeRule({
    required String name,
    required String category,
    int points = 5,
    String? sanction,
  }) async {
    try {
      final url = await getBaseUrl();
      final response = await http.post(
        Uri.parse('$url/aturan'),
        headers: await _headers(),
        body: jsonEncode({
          'name': name.trim(),
          'category': category.trim(),
          'points': points,
          'sanction': sanction?.trim() ?? 'Pembinaan Lisan & Pengarahan BK',
        }),
      ).timeout(const Duration(seconds: 10));

      final data = _safeDecode(response.body);
      if (response.statusCode == 201 && data['success'] == true) {
        return {
          'success': true,
          'message': data['message'] ?? 'Jenis pelanggaran berhasil ditambahkan.',
          'rule': data['rule'],
        };
      }
      return {
        'success': false,
        'message': data['message'] ?? 'Gagal menambahkan jenis pelanggaran.',
      };
    } catch (e) {
      return {'success': false, 'message': 'Kesalahan koneksi server: $e'};
    }
  }
}
