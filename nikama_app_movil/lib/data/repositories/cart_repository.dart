import 'package:dio/dio.dart';
import '../../core/network/api_client.dart';
import '../../core/constants/api_constants.dart';
import '../models/cart_model.dart';

// ============================================================
// NIKAMA — Cart Repository
// GET /api/v1/customer/cart
// POST /api/v1/customer/cart/add
// GET /api/v1/customer/addresses
// ============================================================

class CartRepository {
  final ApiClient _client = ApiClient();

  /// Obtener el carrito activo del cliente
  Future<CartModel?> getCart() async {
    try {
      final response = await _client.dio.get(ApiConstants.customerCart);
      final data = response.data;
      if (data == null) return null;
      // La respuesta puede venir directo como objeto o dentro de "data"
      final cartData = data is Map && data.containsKey('data') ? data['data'] : data;
      if (cartData == null) return null;
      return CartModel.fromJson(cartData as Map<String, dynamic>);
    } on DioException catch (e) {
      // Si no hay carrito (404) devolvemos null
      if (e.response?.statusCode == 404) return null;
      throw _client.extractErrorMessage(e);
    }
  }

  /// Agregar producto al carrito
  Future<CartModel> addToCart({
    required String productId,
    required int quantity,
    String? notes,
    List<Map<String, dynamic>> options = const [],
  }) async {
    try {
      final response = await _client.dio.post(
        ApiConstants.customerCartAdd,
        data: {
          'product_id': productId,
          'quantity': quantity,
          if (notes != null && notes.isNotEmpty) 'notes': notes,
          if (options.isNotEmpty) 'options': options,
        },
      );
      final data = response.data;
      final cartData = data['data'] as Map<String, dynamic>;
      return CartModel.fromJson(cartData);
    } on DioException catch (e) {
      throw _client.extractErrorMessage(e);
    }
  }

  /// Obtener las direcciones guardadas del cliente
  Future<List<AddressModel>> getAddresses() async {
    try {
      final response = await _client.dio.get(ApiConstants.customerAddresses);
      final data = response.data;
      List<dynamic> list;
      if (data is List) {
        list = data;
      } else if (data is Map && data.containsKey('data')) {
        list = data['data'] as List<dynamic>;
      } else {
        list = [];
      }
      return list
          .map((e) => AddressModel.fromJson(e as Map<String, dynamic>))
          .toList();
    } on DioException catch (e) {
      throw _client.extractErrorMessage(e);
    }
  }

  /// Obtener detalle de un producto con sus grupos de opciones
  Future<Map<String, dynamic>> getProductDetail(String productId) async {
    try {
      final response = await _client.dio.get('${ApiConstants.products}/$productId');
      final data = response.data;
      return data['data'] as Map<String, dynamic>;
    } on DioException catch (e) {
      throw _client.extractErrorMessage(e);
    }
  }
}
