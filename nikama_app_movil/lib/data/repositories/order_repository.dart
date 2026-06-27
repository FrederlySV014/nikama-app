import 'package:dio/dio.dart';
import '../../core/network/api_client.dart';
import '../../core/constants/api_constants.dart';

// ============================================================
// NIKAMA — Order Repository
// POST /api/v1/customer/checkout
// GET  /api/v1/customer/orders/{id}/track
// POST /api/v1/customer/orders/{id}/rate-driver
// GET  /api/v1/customer/orders
// ============================================================

class OrderRepository {
  final ApiClient _client = ApiClient();

  /// Crear pedido (Checkout)
  Future<Map<String, dynamic>> checkout({
    required String addressSelectionType,
    String? customerAddressId,
    required String paymentMethod,
    String? notes,
  }) async {
    try {
      final payload = <String, dynamic>{
        'address_selection_type': addressSelectionType,
        'payment_method': paymentMethod,
      };
      if (customerAddressId != null) {
        payload['customer_address_id'] = customerAddressId;
      }
      if (notes != null && notes.isNotEmpty) {
        payload['notes'] = notes;
      }

      final response = await _client.dio.post(
        ApiConstants.customerCheckout,
        data: payload,
      );
      return response.data as Map<String, dynamic>;
    } on DioException catch (e) {
      throw _client.extractErrorMessage(e);
    }
  }

  /// Rastrear pedido en vivo
  Future<Map<String, dynamic>> trackOrder(String orderId) async {
    try {
      final response = await _client.dio.get(
        ApiConstants.customerOrderTrack(orderId),
      );
      return response.data as Map<String, dynamic>;
    } on DioException catch (e) {
      throw _client.extractErrorMessage(e);
    }
  }

  /// Calificar al repartidor
  Future<void> rateDriver({
    required String orderId,
    required int rating,
    String? comment,
  }) async {
    try {
      await _client.dio.post(
        '/customer/orders/$orderId/rate-driver',
        data: {
          'rating': rating,
          if (comment != null && comment.isNotEmpty) 'comment': comment,
        },
      );
    } on DioException catch (e) {
      throw _client.extractErrorMessage(e);
    }
  }

  /// Calificar un producto
  Future<void> rateProduct({
    required String productId,
    required int rating,
    String? comment,
  }) async {
    try {
      await _client.dio.post(
        '/customer/products/$productId/reviews',
        data: {
          'rating': rating,
          if (comment != null && comment.isNotEmpty) 'comment': comment,
        },
      );
    } on DioException catch (e) {
      throw _client.extractErrorMessage(e);
    }
  }

  /// Listar órdenes del cliente
  Future<List<Map<String, dynamic>>> getOrders() async {
    try {
      final response =
          await _client.dio.get(ApiConstants.customerOrders);
      final data = response.data;
      List<dynamic> list;
      if (data is List) {
        list = data;
      } else if (data is Map && data.containsKey('data')) {
        list = data['data'] as List<dynamic>;
      } else {
        list = [];
      }
      return list.map((e) => e as Map<String, dynamic>).toList();
    } on DioException catch (e) {
      throw _client.extractErrorMessage(e);
    }
  }
}
