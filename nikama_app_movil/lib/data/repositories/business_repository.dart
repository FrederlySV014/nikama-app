import 'package:dio/dio.dart';
import '../../core/network/api_client.dart';
import '../models/business_model.dart';
import '../models/category_model.dart';
import '../models/product_model.dart';

// ============================================================
// NIKAMA — Business & Catalog Repository
// ============================================================

class BusinessRepository {
  final ApiClient _client = ApiClient();

  /// OBTENER CATEGORÍAS → GET /api/v1/categories
  Future<List<CategoryModel>> getCategories() async {
    try {
      final response = await _client.dio.get('/categories');
      final List<dynamic> data = response.data['data'] ?? [];
      return data.map((json) => CategoryModel.fromJson(json)).toList();
    } catch (_) {
      return []; // Devolver lista vacía en caso de error
    }
  }

  /// OBTENER PRODUCTOS → GET /api/v1/products
  /// Soporta filtros como ?category_id=uuid&is_featured=1
  Future<List<ProductModel>> getProducts({String? categoryId, bool? isFeatured}) async {
    try {
      final Map<String, dynamic> queryParams = {};
      if (categoryId != null) queryParams['category_id'] = categoryId;
      if (isFeatured != null) queryParams['is_featured'] = isFeatured ? 1 : 0;

      final response = await _client.dio.get(
        '/products',
        queryParameters: queryParams,
      );
      final List<dynamic> data = response.data['data'] ?? [];
      return data.map((json) => ProductModel.fromJson(json)).toList();
    } catch (_) {
      return [];
    }
  }

  /// OBTENER DETALLE DE PRODUCTO → GET /api/v1/products/{product_id}
  Future<ProductModel?> getProductDetail(String productId) async {
    try {
      final response = await _client.dio.get('/products/$productId');
      final data = response.data['data'];
      if (data != null) {
        return ProductModel.fromJson(data);
      }
      return null;
    } catch (_) {
      return null;
    }
  }
}
