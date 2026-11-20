class AttendanceStudent {
  final int id;
  final String nisn;
  final String name;
  final String className;
  final String gender;
  final String status;
  final String checkInTime;
  final String parentName;
  final String parentPhone;
  final int totalPoints;

  AttendanceStudent({
    required this.id,
    required this.nisn,
    required this.name,
    required this.className,
    required this.gender,
    required this.status,
    required this.checkInTime,
    required this.parentName,
    required this.parentPhone,
    required this.totalPoints,
  });

  factory AttendanceStudent.fromJson(Map<String, dynamic> json) {
    return AttendanceStudent(
      id: json['id'] ?? 0,
      nisn: json['nisn']?.toString() ?? '',
      name: json['name'] ?? '',
      className: json['class_name'] ?? '',
      gender: json['gender'] ?? 'L',
      status: json['status'] ?? 'Hadir',
      checkInTime: json['check_in_time'] ?? '06:45',
      parentName: json['parent_name'] ?? 'Wali Murid',
      parentPhone: json['parent_phone'] ?? '-',
      totalPoints: json['total_points'] ?? 0,
    );
  }
}

class AttendanceStats {
  final int total;
  final int hadir;
  final int terlambat;
  final int alpaIzinSakit;

  AttendanceStats({
    required this.total,
    required this.hadir,
    required this.terlambat,
    required this.alpaIzinSakit,
  });

  factory AttendanceStats.fromJson(Map<String, dynamic> json) {
    return AttendanceStats(
      total: json['total'] ?? 0,
      hadir: json['hadir'] ?? 0,
      terlambat: json['terlambat'] ?? 0,
      alpaIzinSakit: json['alpa_izin_sakit'] ?? 0,
    );
  }
}
