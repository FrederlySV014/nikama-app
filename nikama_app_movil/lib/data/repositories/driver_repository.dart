import 'package:dio/dio.dart';
import '../../core/network/api_client.dart';

// ============================================================
// NIKAMA — Driver Repository
// ============================================================

class DriverRepository {
  final ApiClient _client = ApiClient();

  /// OBTENER DASHBOARD DEL REPARTIDOR → GET /api/v1/driver/dashboard
  Future<Map<String, dynamic>?> getDashboard() async {
    try {
      final response = await _client.dio.get('/driver/dashboard');
      return response.data;
    } catch (_) {
      return null;
    }
  }

  /// ACEPTAR PEDIDO ASIGNADO → POST /api/v1/driver/assignments/{assignment_id}/accept
  Future<bool> acceptAssignment(String assignmentId) async {
    try {
      final response = await _client.dio.post('/driver/assignments/$assignmentId/accept');
      return response.data['success'] ?? false;
    } catch (_) {
      return false;
    }
  }

  /// EMITIR GEOLOCALIZACIÓN EN VIVO (GPS) → POST /api/v1/driver/deliveries/{delivery_id}/emit-location
  Future<bool> emitLocation({
    required String deliveryId,
    required double latitude,
    required double longitude,
  }) async {
    try {
      final response = await _client.dio.post(
        '/driver/deliveries/$deliveryId/emit-location',
        data: {
          'latitude': latitude,
          'longitude': longitude,
        },
      );
      return response.data['success'] ?? false;
    } catch (_) {
      return false;
    }
  }

  /// COMPLETAR ENTREGA → POST /api/v1/driver/deliveries/{delivery_id}/complete
  Future<bool> completeDelivery(String deliveryId) async {
    try {
      final response = await _client.dio.post('/driver/deliveries/$deliveryId/complete');
      return response.data['success'] ?? false;
    } catch (_) {
      return false;
    }
  }
}
