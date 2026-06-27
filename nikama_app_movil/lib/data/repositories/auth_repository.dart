import 'package:dio/dio.dart';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import '../../core/network/api_client.dart';
import '../../core/constants/api_constants.dart';
import '../models/user_model.dart';

// ============================================================
// NIKAMA — Auth Repository
// Maneja login, registro, logout y persistencia de sesión
// ============================================================

class AuthRepository {
  final ApiClient _client = ApiClient();
  final FlutterSecureStorage _storage = const FlutterSecureStorage();

  /// LOGIN → POST /api/v1/auth/login
  Future<Map<String, dynamic>> login({
    required String email,
    required String password,
  }) async {
    final cleanEmail = email.trim().toLowerCase();

    // Función interna para generar el bypass de prueba si el servidor está apagado
    Future<Map<String, dynamic>> getMockSession() async {
      List<String> roles = ['customer'];
      String firstName = 'Usuario';
      String lastName = 'Prueba';

      if (cleanEmail == 'repartidor@nikama.com' || cleanEmail == 'driver@nikama.com') {
        roles = ['driver'];
        firstName = 'Repartidor';
        lastName = 'Nikama';
      } else if (cleanEmail == 'socio@nikama.com' || cleanEmail == 'seller@nikama.com') {
        roles = ['seller'];
        firstName = 'Socio';
        lastName = 'Negocio';
      } else if (cleanEmail == 'admin@nikama.com') {
        roles = ['super_admin'];
        firstName = 'Administrador';
        lastName = 'Nikama';
      }

      final mockUser = UserModel(
        id: '999',
        firstName: firstName,
        lastName: lastName,
        email: cleanEmail,
        phone: '999999999',
        dni: '12345678',
        roles: roles,
        isActive: true,
      );

      final mockToken = 'mock_token_for_${roles.first}';
      await _storage.write(key: 'auth_token', value: mockToken);

      return {'user': mockUser, 'token': mockToken};
    }

    try {
      // 1. Intentar conectar primero al servidor real de Laravel
      final response = await _client.dio.post(
        ApiConstants.login,
        data: {'email': email, 'password': password},
      );

      final data = response.data;
      final token = data['token'] as String;
      final user = UserModel.fromJson(data['user']);

      // Persistir token de forma segura
      await _storage.write(key: 'auth_token', value: token);

      return {'user': user, 'token': token};
    } on DioException catch (e) {
      // 2. Si el error es de conexión de red (servidor apagado, puerto incorrecto, etc.)
      final isNetworkError = e.type == DioExceptionType.connectionTimeout ||
          e.type == DioExceptionType.sendTimeout ||
          e.type == DioExceptionType.receiveTimeout ||
          e.type == DioExceptionType.connectionError ||
          e.message?.contains('SocketException') == true ||
          e.message?.contains('connection refused') == true;

      // Si es un error de conexión Y el correo ingresado es de los configurados de prueba
      if (isNetworkError &&
          (cleanEmail == 'cliente@nikama.com' ||
              cleanEmail == 'customer@nikama.com' ||
              cleanEmail == 'repartidor@nikama.com' ||
              cleanEmail == 'driver@nikama.com' ||
              cleanEmail == 'socio@nikama.com' ||
              cleanEmail == 'seller@nikama.com' ||
              cleanEmail == 'admin@nikama.com')) {
        return await getMockSession();
      }

      // Si no es un error de conexión, o es una cuenta de producción, devolver el error real de la API
      throw _client.extractErrorMessage(e);
    }
  }

  /// REGISTRO CLIENTE → POST /api/v1/auth/register/customer
  Future<Map<String, dynamic>> registerCustomer({
    required String firstName,
    required String lastName,
    required String email,
    required String password,
    required String passwordConfirmation,
    required String phone,
    required String dni,
  }) async {
    try {
      final response = await _client.dio.post(
        ApiConstants.registerCustomer,
        data: {
          'first_name': firstName,
          'last_name': lastName,
          'email': email,
          'password': password,
          'password_confirmation': passwordConfirmation,
          'phone': phone,
          'dni': dni,
        },
      );

      final data = response.data;
      final token = data['token'] as String;
      final user = UserModel.fromJson(data['user']);

      await _storage.write(key: 'auth_token', value: token);

      return {'user': user, 'token': token};
    } on DioException catch (e) {
      throw _client.extractErrorMessage(e);
    }
  }

  /// REGISTRO DRIVER → POST /api/v1/auth/register/driver
  Future<Map<String, dynamic>> registerDriver({
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
    try {
      final response = await _client.dio.post(
        ApiConstants.registerDriver,
        data: {
          'first_name': firstName,
          'last_name': lastName,
          'email': email,
          'password': password,
          'password_confirmation': passwordConfirmation,
          'phone': phone,
          'dni': dni,
          'vehicle_type': vehicleType,
          'vehicle_plate': vehiclePlate,
          'license_number': licenseNumber,
        },
      );

      final data = response.data;
      final token = data['token'] as String;
      final user = UserModel.fromJson(data['user']);

      await _storage.write(key: 'auth_token', value: token);

      return {'user': user, 'token': token};
    } on DioException catch (e) {
      throw _client.extractErrorMessage(e);
    }
  }

  /// LOGOUT → POST /api/v1/auth/logout
  Future<void> logout() async {
    // 1. Obtener el token antes de borrarlo
    final token = await _storage.read(key: 'auth_token');

    // 2. Borrar inmediatamente local para que el cierre sea instantáneo
    await _storage.delete(key: 'auth_token');
    await _storage.delete(key: 'user_data');

    // 3. Si es un token simulado o no hay token, no hacemos llamada de red
    if (token == null || token.startsWith('mock_')) {
      return;
    }

    // 4. Hacer la petición al backend en segundo plano sin bloquear el hilo principal
    try {
      _client.dio.post(ApiConstants.logout).catchError((_) {});
    } catch (_) {
      // Ignorar fallos de red
    }
  }

  /// Recuperar usuario autenticado → GET /api/v1/auth/profile
  Future<UserModel?> getMe() async {
    try {
      final response = await _client.dio.get(ApiConstants.me);
      final data = response.data;
      // Laravel puede devolver {user: {...}} o directamente {...}
      if (data is Map<String, dynamic>) {
        final userJson = data.containsKey('user') ? data['user'] : data;
        return UserModel.fromJson(userJson as Map<String, dynamic>);
      }
      return null;
    } on DioException catch (_) {
      return null;
    }
  }

  /// Verificar si hay sesión activa guardada
  Future<String?> getSavedToken() async {
    return await _storage.read(key: 'auth_token');
  }
}
