import 'package:dio/dio.dart';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import '../constants/api_constants.dart';

// ============================================================
// NIKAMA — Cliente HTTP centralizado (Dio + Bearer Token)
// Versión mejorada con logging en debug y manejo de 401
// ============================================================

class ApiClient {
  late final Dio dio;
  final FlutterSecureStorage _storage = const FlutterSecureStorage();

  // Singleton para reutilización segura
  static final ApiClient _instance = ApiClient._internal();
  factory ApiClient() => _instance;

  ApiClient._internal() {
    dio = Dio(
      BaseOptions(
        baseUrl: ApiConstants.baseUrl,
        connectTimeout: const Duration(seconds: ApiConstants.connectTimeoutSeconds),
        receiveTimeout: const Duration(seconds: ApiConstants.receiveTimeoutSeconds),
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
      ),
    );
    _initInterceptors();
  }

  void _initInterceptors() {
    dio.interceptors.add(
      InterceptorsWrapper(
        onRequest: (options, handler) async {
          // Inyectar token Bearer automáticamente en cada request
          final token = await _storage.read(key: 'auth_token');
          if (token != null) {
            options.headers['Authorization'] = 'Bearer $token';
          }
          return handler.next(options);
        },
        onResponse: (response, handler) {
          return handler.next(response);
        },
        onError: (DioException e, handler) async {
          if (e.response?.statusCode == 401) {
            // Token inválido o expirado → limpiar sesión
            await _storage.delete(key: 'auth_token');
            await _storage.delete(key: 'user_data');
          }
          return handler.next(e);
        },
      ),
    );

    // Logger en modo debug
    dio.interceptors.add(
      LogInterceptor(
        requestBody: true,
        responseBody: true,
        error: true,
        requestHeader: false,
        responseHeader: false,
        logPrint: (obj) => print('[NIKAMA API] $obj'),
      ),
    );
  }

  /// Extrae el mensaje de error del response de Laravel
  String extractErrorMessage(DioException e) {
    if (e.response?.data != null) {
      final data = e.response!.data;
      if (data is Map<String, dynamic>) {
        if (data.containsKey('message')) return data['message'];
        if (data.containsKey('errors')) {
          final errors = data['errors'] as Map<String, dynamic>;
          final firstError = errors.values.first;
          if (firstError is List && firstError.isNotEmpty) {
            return firstError.first.toString();
          }
        }
      }
    }
    switch (e.response?.statusCode) {
      case 401:
        return 'Credenciales inválidas o sesión expirada.';
      case 403:
        return 'No tienes permiso para realizar esta acción.';
      case 404:
        return 'Recurso no encontrado.';
      case 422:
        return 'Por favor revisa los datos ingresados.';
      case 500:
        return 'Error interno del servidor. Intenta más tarde.';
      default:
        return 'Error de conexión. Verifica tu internet.';
    }
  }
}
