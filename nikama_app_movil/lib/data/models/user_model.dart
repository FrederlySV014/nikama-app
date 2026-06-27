// ============================================================
// NIKAMA — Modelo de Usuario
// Mapea la respuesta JSON del backend Laravel
// Roles: customer, driver, seller, super_admin
// ============================================================

class UserModel {
  final String id;
  final String firstName;
  final String lastName;
  final String email;
  final String phone;
  final String? dni;
  final String? avatarUrl;
  final List<String> roles;
  final bool isActive;
  final String? emailVerifiedAt;

  UserModel({
    required this.id,
    required this.firstName,
    required this.lastName,
    required this.email,
    required this.phone,
    this.dni,
    this.avatarUrl,
    required this.roles,
    this.isActive = true,
    this.emailVerifiedAt,
  });

  /// Nombre completo
  String get fullName => '$firstName $lastName';

  /// Primer rol principal
  String get primaryRole => roles.isNotEmpty ? roles.first : 'customer';

  bool get isCustomer => roles.contains('customer');
  bool get isDriver => roles.contains('driver');
  bool get isSeller => roles.contains('seller');
  bool get isSuperAdmin => roles.contains('super_admin');

  factory UserModel.fromJson(Map<String, dynamic> json) {
    return UserModel(
      id: json['id']?.toString() ?? '',
      firstName: json['first_name'] ?? '',
      lastName: json['last_name'] ?? '',
      email: json['email'] ?? '',
      phone: json['phone'] ?? '',
      dni: json['dni'],
      avatarUrl: json['avatar_url'],
      roles: (json['roles'] as List<dynamic>?)
              ?.map((r) => r.toString())
              .toList() ??
          [],
      isActive: json['is_active'] ?? true,
      emailVerifiedAt: json['email_verified_at'],
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'first_name': firstName,
      'last_name': lastName,
      'email': email,
      'phone': phone,
      'dni': dni,
      'avatar_url': avatarUrl,
      'roles': roles,
      'is_active': isActive,
    };
  }
}
