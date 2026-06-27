import '../../core/network/api_client.dart';

// ============================================================
// NIKAMA — Seller Repository
// ============================================================

class SellerRepository {
  final ApiClient _client = ApiClient();

  /// OBTENER DASHBOARD DEL VENDEDOR → GET /api/v1/seller/dashboard
  Future<Map<String, dynamic>?> getDashboard() async {
    try {
      final response = await _client.dio.get('/seller/dashboard');
      return response.data;
    } catch (_) {
      return null;
    }
  }

  /// OBTENER PEDIDOS DEL VENDEDOR → GET /api/v1/seller/orders
  Future<Map<String, dynamic>?> getOrders({String? status}) async {
    try {
      final response = await _client.dio.get(
        '/seller/orders',
        queryParameters: status != null ? {'status': status} : null,
      );
      return response.data;
    } catch (_) {
      return null;
    }
  }

  /// ACTUALIZAR ESTADO DEL PEDIDO → POST /api/v1/seller/orders/{id}/status
  Future<bool> updateOrderStatus(String orderId, String status, {String? reason}) async {
    try {
      final response = await _client.dio.post(
        '/seller/orders/$orderId/status',
        data: {
          'status': status,
          if (reason != null) 'cancellation_reason': reason,
        },
      );
      return response.data['success'] == true;
    } catch (_) {
      return false;
    }
  }

  /// ASIGNAR REPARTIDOR → POST /api/v1/seller/orders/{id}/assign-driver
  Future<bool> assignDriver(String orderId, String driverProfileId) async {
    try {
      final response = await _client.dio.post(
        '/seller/orders/$orderId/assign-driver',
        data: {
          'driver_profile_id': driverProfileId,
        },
      );
      return response.data['success'] == true;
    } catch (_) {
      return false;
    }
  }

  /// OBTENER PRODUCTOS DEL NEGOCIO → GET /api/v1/seller/products
  Future<Map<String, dynamic>?> getProducts({String? search}) async {
    try {
      final response = await _client.dio.get(
        '/seller/products',
        queryParameters: search != null && search.isNotEmpty ? {'search': search} : null,
      );
      return response.data;
    } catch (_) {
      return null;
    }
  }

  /// ALTERNAR ESTADO DE PRODUCTO → POST /api/v1/seller/products/{id}/toggle
  Future<bool> toggleProductStatus(String productId) async {
    try {
      final response = await _client.dio.post(
        '/seller/products/$productId/toggle',
      );
      return response.data['success'] == true;
    } catch (_) {
      return false;
    }
  }
}
