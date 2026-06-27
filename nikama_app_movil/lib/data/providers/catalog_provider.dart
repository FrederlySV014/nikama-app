import 'package:flutter/foundation.dart';
import '../repositories/business_repository.dart';
import '../models/category_model.dart';
import '../models/product_model.dart';

// ============================================================
// NIKAMA — Catalog Provider (ChangeNotifier)
// Mantiene el estado del menú, categorías y productos cargados
// ============================================================

class CatalogProvider with ChangeNotifier {
  final BusinessRepository _repo = BusinessRepository();

  List<CategoryModel> _categories = [];
  List<ProductModel> _featuredProducts = [];
  List<ProductModel> _filteredProducts = [];
  bool _isLoading = false;
  String? _selectedCategoryId;

  List<CategoryModel> get categories => _categories;
  List<ProductModel> get featuredProducts => _featuredProducts;
  List<ProductModel> get filteredProducts => _filteredProducts;
  bool get isLoading => _isLoading;
  String? get selectedCategoryId => _selectedCategoryId;

  /// Cargar datos iniciales del catálogo
  Future<void> loadCatalogData() async {
    _isLoading = true;
    notifyListeners();

    try {
      // Cargar categorías y productos destacados en paralelo
      final results = await Future.wait([
        _repo.getCategories(),
        _repo.getProducts(isFeatured: true),
      ]);

      _categories = results[0] as List<CategoryModel>;
      _featuredProducts = results[1] as List<ProductModel>;
      _filteredProducts = List.from(_featuredProducts);
    } catch (_) {
      // Manejar error silenciosamente
    } finally {
      _isLoading = false;
      notifyListeners();
    }
  }

  /// Seleccionar categoría y filtrar productos
  Future<void> selectCategory(String? categoryId) async {
    _selectedCategoryId = categoryId;
    _isLoading = true;
    notifyListeners();

    try {
      if (categoryId == null) {
        _filteredProducts = List.from(_featuredProducts);
      } else {
        _filteredProducts = await _repo.getProducts(categoryId: categoryId);
      }
    } catch (_) {
      _filteredProducts = [];
    } finally {
      _isLoading = false;
      notifyListeners();
    }
  }
}
