import '../services/api_service.dart';

class UserModel {
  final int id;
  final String name;
  final String? username;
  final String? avatar;
  final String? avatarUrl;
  final String email;
  final String? phone;
  final String role;
  final String jabatan;
  final List<String> rombel;

  UserModel({
    required this.id,
    required this.name,
    this.username,
    this.avatar,
    this.avatarUrl,
    required this.email,
    this.phone,
    required this.role,
    required this.jabatan,
    required this.rombel,
  });

  factory UserModel.fromJson(Map<String, dynamic> json) {
    List<String> parsedRombel = [];
    if (json['rombel'] is List) {
      parsedRombel = (json['rombel'] as List).map((e) => e.toString()).toList();
    }
    return UserModel(
      id: json['id'] ?? 0,
      name: json['name'] ?? '',
      username: json['username'],
      avatar: json['avatar'],
      avatarUrl: ApiService.resolveMediaUrl(json['avatar_url']),
      email: json['email'] ?? '',
      phone: json['phone'],
      role: json['role'] ?? 'guru',
      jabatan: json['jabatan'] ?? 'Guru Pembimbing BK',
      rombel: parsedRombel,
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'name': name,
      'username': username,
      'avatar': avatar,
      'avatar_url': avatarUrl,
      'email': email,
      'phone': phone,
      'role': role,
      'jabatan': jabatan,
      'rombel': rombel,
    };
  }
}
