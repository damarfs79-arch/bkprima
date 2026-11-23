import 'dart:convert';
import 'package:flutter/foundation.dart';
import 'package:shared_preferences/shared_preferences.dart';
import '../models/violation_type_model.dart';
import 'api_service.dart';

class ViolationTypeService {
  static const String _storageKey = 'bkprima_custom_violation_types';

  /// Tidak ada preset bawaan statis. Semua jenis pelanggaran dibuat manual oleh guru.
  static const List<ViolationTypeItem> defaultPresets = [];

  /// Membaca jenis pelanggaran kustom yang ditambahkan sendiri oleh guru
  static Future<List<ViolationTypeItem>> getCustomTypes() async {
    try {
      final prefs = await SharedPreferences.getInstance();
      final rawJson = prefs.getString(_storageKey);
      if (rawJson == null || rawJson.trim().isEmpty) {
        return [];
      }
      final decoded = jsonDecode(rawJson) as List<dynamic>;
      return decoded
          .map((item) => ViolationTypeItem.fromJson(item as Map<String, dynamic>))
          .where((item) => item.isCustom == true)
          .toList();
    } catch (e) {
      debugPrint('Error reading custom violation types: $e');
      return [];
    }
  }

  /// Mengambil daftar jenis pelanggaran buatan guru (dengan filter kategori opsional)
  static Future<List<ViolationTypeItem>> getAllTypes({String? category}) async {
    final customItems = await getCustomTypes();
    var allList = List<ViolationTypeItem>.from(customItems);

    if (category != null && category.isNotEmpty && category != 'Semua') {
      allList = allList.where((item) => item.category == category).toList();
    }

    return allList;
  }

  /// Menambahkan jenis pelanggaran baru buatan guru
  static Future<ViolationTypeItem> addCustomType({
    required String name,
    required String category,
    required int defaultPoints,
    required String defaultPenalty,
  }) async {
    final trimmedName = name.trim();
    if (trimmedName.isEmpty) {
      throw Exception('Nama jenis pelanggaran tidak boleh kosong.');
    }

    final currentCustom = await getCustomTypes();

    final isDuplicate = currentCustom.any(
      (c) => c.name.toLowerCase().trim() == trimmedName.toLowerCase(),
    );

    if (isDuplicate) {
      throw Exception('Jenis pelanggaran "$trimmedName" sudah ada di dalam daftar.');
    }

    final newId = 'CST-${DateTime.now().millisecondsSinceEpoch.toString().substring(7)}';
    final newItem = ViolationTypeItem(
      id: newId,
      name: trimmedName,
      category: category,
      defaultPoints: defaultPoints,
      defaultPenalty: defaultPenalty,
      isCustom: true,
    );

    currentCustom.insert(0, newItem);

    // Simpan ke SharedPreferences
    final prefs = await SharedPreferences.getInstance();
    final jsonString = jsonEncode(currentCustom.map((e) => e.toJson()).toList());
    await prefs.setString(_storageKey, jsonString);

    // Sinkronkan ke API backend secara background (jika backend aktif)
    try {
      ApiService.storeRule(
        name: newItem.name,
        category: newItem.category,
        points: newItem.defaultPoints,
        sanction: newItem.defaultPenalty,
      );
    } catch (_) {}

    return newItem;
  }

  /// Menghapus jenis pelanggaran buatan guru
  static Future<bool> deleteCustomType(String id) async {
    final currentCustom = await getCustomTypes();
    final updatedList = currentCustom.where((item) => item.id != id).toList();

    final prefs = await SharedPreferences.getInstance();
    final jsonString = jsonEncode(updatedList.map((e) => e.toJson()).toList());
    return await prefs.setString(_storageKey, jsonString);
  }
}
