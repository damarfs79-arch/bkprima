class ViolationTypeItem {
  final String id;
  final String name;
  final String category;
  final int defaultPoints;
  final String defaultPenalty;
  final bool isCustom;

  const ViolationTypeItem({
    required this.id,
    required this.name,
    required this.category,
    required this.defaultPoints,
    required this.defaultPenalty,
    this.isCustom = false,
  });

  Map<String, dynamic> toJson() => {
    'id': id,
    'name': name,
    'category': category,
    'defaultPoints': defaultPoints,
    'defaultPenalty': defaultPenalty,
    'isCustom': isCustom,
  };

  factory ViolationTypeItem.fromJson(Map<String, dynamic> json) => ViolationTypeItem(
    id: json['id']?.toString() ?? '',
    name: json['name']?.toString() ?? '',
    category: json['category']?.toString() ?? 'Kedisiplinan',
    defaultPoints: (json['defaultPoints'] as num?)?.toInt() ??
        (json['points'] as num?)?.toInt() ??
        5,
    defaultPenalty: json['defaultPenalty']?.toString() ??
        json['sanction']?.toString() ??
        'Pembinaan Lisan & Pengarahan BK',
    isCustom: json['isCustom'] == true,
  );

  @override
  bool operator ==(Object other) =>
      identical(this, other) ||
      other is ViolationTypeItem &&
          runtimeType == other.runtimeType &&
          id == other.id &&
          name.toLowerCase().trim() == other.name.toLowerCase().trim();

  @override
  int get hashCode => id.hashCode ^ name.toLowerCase().trim().hashCode;
}
