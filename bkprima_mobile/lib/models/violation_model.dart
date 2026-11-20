class ViolationRecord {
  final int id;
  final int? studentId;
  final String studentName;
  final String studentClass;
  final String category;
  final String violationName;
  final int? points;
  final String date;
  final String time;
  final String reporter;
  final String penaltyAction;
  final String status;
  final String notes;
  final String? fotoBukti;

  ViolationRecord({
    required this.id,
    this.studentId,
    required this.studentName,
    required this.studentClass,
    required this.category,
    required this.violationName,
    this.points,
    required this.date,
    required this.time,
    required this.reporter,
    required this.penaltyAction,
    required this.status,
    required this.notes,
    this.fotoBukti,
  });

  factory ViolationRecord.fromJson(Map<String, dynamic> json) {
    return ViolationRecord(
      id: json['id'] ?? 0,
      studentId: json['student_id'],
      studentName: json['student_name'] ?? '',
      studentClass: json['student_class'] ?? '',
      category: json['category'] ?? 'Kedisiplinan',
      violationName: json['violation_name'] ?? '',
      points: json['points'],
      date: json['date'] != null ? json['date'].toString().split('T')[0] : '',
      time: json['time'] ?? '07:15',
      reporter: json['reporter'] ?? 'Guru BK',
      penaltyAction: json['penalty_action'] ?? 'Pembinaan Lisan',
      status: json['status'] ?? 'Dalam Pembinaan',
      notes: json['notes'] ?? '',
      fotoBukti: json['foto_bukti'],
    );
  }
}

class SuratPanggilan {
  final int studentId;
  final String name;
  final String nisn;
  final String className;
  final int totalPoints;
  final String parentName;
  final String parentPhone;
  final String spLevel;

  SuratPanggilan({
    required this.studentId,
    required this.name,
    required this.nisn,
    required this.className,
    required this.totalPoints,
    required this.parentName,
    required this.parentPhone,
    required this.spLevel,
  });

  factory SuratPanggilan.fromJson(Map<String, dynamic> json) {
    return SuratPanggilan(
      studentId: json['student_id'] ?? 0,
      name: json['name'] ?? '',
      nisn: json['nisn']?.toString() ?? '',
      className: json['class_name'] ?? '',
      totalPoints: json['total_points'] ?? 0,
      parentName: json['parent_name'] ?? 'Wali Murid',
      parentPhone: json['parent_phone'] ?? '-',
      spLevel: json['sp_level'] ?? 'SP-1',
    );
  }
}

class ViolationStats {
  final int totalKasus;
  final int kasusRingan;
  final int peringatanSp;
  final int panggilanOrtu;

  ViolationStats({
    required this.totalKasus,
    required this.kasusRingan,
    required this.peringatanSp,
    required this.panggilanOrtu,
  });

  factory ViolationStats.fromJson(Map<String, dynamic> json) {
    return ViolationStats(
      totalKasus: json['total_kasus'] ?? 0,
      kasusRingan: json['kasus_ringan'] ?? 0,
      peringatanSp: json['peringatan_sp'] ?? 0,
      panggilanOrtu: json['panggilan_ortu'] ?? 0,
    );
  }
}
