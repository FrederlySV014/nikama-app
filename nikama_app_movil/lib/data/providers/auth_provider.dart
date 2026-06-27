import 'package:flutter/foundation.dart';
import '../repositories/auth_repository.dart';
import '../models/user_model.dart';

// ============================================================
// NIKAMA — Auth Provider (ChangeNotifier)
// Estado global de autenticación: usuario, token, loading
// ============================================================

enum AuthStatus { unknown, authenticated, unauthenticated }

class AuthProvider with ChangeNotifier {
  final AuthRepository _repo = AuthRepository();

  AuthStatus _status = AuthStatus.unknown;
  UserModel? _user;
  bool _isLoading = false;
  String? _errorMessage;

  AuthStatus get status => _status;
  UserModel? get user => _user;
  bool get isLoading => _isLoading;
  String? get errorMessage => _errorMessage;
  bool get isAuthenticated => _status == AuthStatus.authenticated;

  // ─────────────────────────────────────────────────────────
  // Verificar sesión al iniciar la app
  // ─────────────────────────────────────────────────────────
  Future<void> checkAuthStatus() async {
    _isLoading = true;
    notifyListeners();

    try {
      final token = await _repo.getSavedToken();
      if (token == null) {
        _status = AuthStatus.unauthenticated;
      } else if (token.startsWith('mock_')) {
        // Token simulado (sin servidor): limpiar y forzar login real
        await _repo.logout();
        _status = AuthStatus.unauthenticated;
      } else {
        // Token real: verificar con el backend
        final user = await _repo.getMe();
        if (user != null) {
          _user = user;
          _status = AuthStatus.authenticated;
        } else {
          _status = AuthStatus.unauthenticated;
        }
      }
    } catch (_) {
      _status = AuthStatus.unauthenticated;
    }

    _isLoading = false;
    notifyListeners();
  }

  // ─────────────────────────────────────────────────────────
  // LOGIN
  // ─────────────────────────────────────────────────────────
  Future<bool> login(String email, String password) async {
    _setLoading(true);
    _errorMessage = null;

    try {
      final result = await _repo.login(email: email, password: password);
      _user = result['user'] as UserModel;
      _status = AuthStatus.authenticated;
      _setLoading(false);
      return true;
    } catch (e) {
      _errorMessage = e.toString();
      _status = AuthStatus.unauthenticated;
      _setLoading(false);
      return false;
    }
  }

  // ─────────────────────────────────────────────────────────
  // REGISTRO CLIENTE
  // ─────────────────────────────────────────────────────────
  Future<bool> registerCustomer({
    required String firstName,
    required String lastName,
    required String email,
    required String password,
    required String passwordConfirmation,
    required String phone,
    required String dni,
  }) async {
    _setLoading(true);
    _errorMessage = null;

    try {
      final result = await _repo.registerCustomer(
        firstName: firstName,
        lastName: lastName,
        email: email,
        password: password,
        passwordConfirmation: passwordConfirmation,
        phone: phone,
        dni: dni,
      );
      _user = result['user'] as UserModel;
      _status = AuthStatus.authenticated;
      _setLoading(false);
      return true;
    } catch (e) {
      _errorMessage = e.toString();
      _setLoading(false);
      return false;
    }
  }

  // ─────────────────────────────────────────────────────────
  // REGISTRO DRIVER
  // ─────────────────────────────────────────────────────────
  Future<bool> registerDriver({
    required String firstName,
    required String lastName,
    required String email,
    required String password,
    required String passwordConfirmation,
    required String phone,
    required String dni,
    required String vehicleType,
    required String vehiclePlate,
    required String licenseNumber,
  }) async {
    _setLoading(true);
    _errorMessage = null;

    try {
      final result = await _repo.registerDriver(
        firstName: firstName,
        lastName: lastName,
        email: email,
        password: password,
        passwordConfirmation: passwordConfirmation,
        phone: phone,
        dni: dni,
        vehicleType: vehicleType,
        vehiclePlate: vehiclePlate,
        licenseNumber: licenseNumber,
      );
      _user = result['user'] as UserModel;
      _status = AuthStatus.authenticated;
      _setLoading(false);
      return true;
    } catch (e) {
      _errorMessage = e.toString();
      _setLoading(false);
      return false;
    }
  }

  // ─────────────────────────────────────────────────────────
  // LOGOUT
  // ─────────────────────────────────────────────────────────
  Future<void> logout() async {
    await _repo.logout();
    _user = null;
    _status = AuthStatus.unauthenticated;
    notifyListeners();
  }

  void clearError() {
    _errorMessage = null;
    notifyListeners();
  }

  void _setLoading(bool value) {
    _isLoading = value;
    notifyListeners();
  }
}
