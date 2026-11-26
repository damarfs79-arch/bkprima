class AbsenceAlertDate {
  final String date;
  final String status;

  AbsenceAlertDate({
    required this.date,
    required this.status,
  });

  factory AbsenceAlertDate.fromJson(Map<String, dynamic> json) {
    return AbsenceAlertDate(
      date: json['date']?.toString() ?? '',
      status: json['status']?.toString() ?? 'Alpa',
    );
  }
}

class AbsenceAlert {
  final int studentId;
  final String name;
  final String nisn;
  final String className;
  final String parentName;
  final String parentPhone;
  final int consecutiveDays;
  final int alpaCount;
  final String lastStatus;
  final String? lastDate;
  final String alertType;
  final String reason;
  final List<AbsenceAlertDate> recentDates;

  AbsenceAlert({
    required this.studentId,
    required this.name,
    required this.nisn,
    required this.className,
    required this.parentName,
    required this.parentPhone,
    required this.consecutiveDays,
    required this.alpaCount,
    required this.lastStatus,
    this.lastDate,
    required this.alertType,
    required this.reason,
    required this.recentDates,
  });

  bool get isConsecutive => alertType == 'consecutive' || consecutiveDays >= 3;

  factory AbsenceAlert.fromJson(Map<String, dynamic> json) {
    final rawDates = json['recent_dates'] as List<dynamic>? ?? [];
    return AbsenceAlert(
      studentId: (json['student_id'] as num?)?.toInt() ?? 0,
      name: json['name']?.toString() ?? '-',
      nisn: json['nisn']?.toString() ?? '-',
      className: json['class_name']?.toString() ?? '-',
      parentName: json['parent_name']?.toString() ?? 'Wali Murid',
      parentPhone: json['parent_phone']?.toString() ?? '-',
      consecutiveDays: (json['consecutive_days'] as num?)?.toInt() ?? 0,
      alpaCount: (json['alpa_count'] as num?)?.toInt() ?? 0,
      lastStatus: json['last_status']?.toString() ?? 'Alpa',
      lastDate: json['last_date']?.toString(),
      alertType: json['alert_type']?.toString() ?? 'accumulated',
      reason: json['reason']?.toString() ?? '',
      recentDates: rawDates
          .whereType<Map<String, dynamic>>()
          .map(AbsenceAlertDate.fromJson)
          .toList(),
    );
  }
}
