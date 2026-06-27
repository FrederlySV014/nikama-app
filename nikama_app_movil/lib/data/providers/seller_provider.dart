import 'package:flutter/foundation.dart';
import '../repositories/seller_repository.dart';

// ============================================================
// NIKAMA — Seller Provider (ChangeNotifier)
// Mantiene el estado del vendedor (dashboard, órdenes)
// ============================================================

class SellerProvider with ChangeNotifier {
  final SellerRepository _repo = SellerRepository();

  Map<String, dynamic>? _dashboardStats;
  List<dynamic> _recentOrders = [];
  List<dynamic> _orders = [];
  List<dynamic> _products = [];
  bool _isLoading = false;
  bool _isLoadingOrders = false;
  bool _isLoadingProducts = false;

  Map<String, dynamic>? get dashboardStats => _dashboardStats;
  List<dynamic> get recentOrders => _recentOrders;
  List<dynamic> get orders => _orders;
  List<dynamic> get products => _products;
  bool get isLoading => _isLoading;
  bool get isLoadingOrders => _isLoadingOrders;
  bool get isLoadingProducts => _isLoadingProducts;

  /// Cargar Dashboard del Vendedor
  Future<void> loadDashboard() async {
    _isLoading = true;
    notifyListeners();

    try {
      final response = await _repo.getDashboard();
      if (response != null && response['success'] == true) {
        final data = response['data'];
        
        _dashboardStats = {
          'today_orders_count': data['today_orders_count'] ?? 0,
          'active_products_count': data['active_products_count'] ?? 0,
          'today_sales': data['today_sales'] ?? 0,
          'pending_orders_count': data['pending_orders_count'] ?? 0,
        };
        
        _recentOrders = data['recent_orders'] ?? [];
      }
    } catch (_) {
      // Manejar error silenciosamente
    } finally {
      _isLoading = false;
      notifyListeners();
    }
  }

  /// Cargar Historial de Pedidos
  Future<void> loadOrders({String? status}) async {
    _isLoadingOrders = true;
    notifyListeners();

    try {
      final response = await _repo.getOrders(status: status);
      if (response != null && response['success'] == true) {
        _orders = response['data']['data'] ?? [];
      }
    } catch (_) {
    } finally {
      _isLoadingOrders = false;
      notifyListeners();
    }
  }

  /// Actualizar estado de pedido
  Future<bool> updateOrderStatus(String orderId, String status, {String? reason}) async {
    _isLoadingOrders = true;
    notifyListeners();

    final success = await _repo.updateOrderStatus(orderId, status, reason: reason);
    if (success) {
      await loadOrders();
      await loadDashboard();
    } else {
      _isLoadingOrders = false;
      notifyListeners();
    }
    return success;
  }

  /// Asignar repartidor a pedido
  Future<bool> assignDriver(String orderId, String driverProfileId) async {
    _isLoadingOrders = true;
    notifyListeners();

    final success = await _repo.assignDriver(orderId, driverProfileId);
    if (success) {
      await loadOrders();
      await loadDashboard();
    } else {
      _isLoadingOrders = false;
      notifyListeners();
    }
    return success;
  }

  /// Cargar Productos
  Future<void> loadProducts({String? search}) async {
    _isLoadingProducts = true;
    notifyListeners();

    try {
      final response = await _repo.getProducts(search: search);
      if (response != null && response['success'] == true) {
        _products = response['data'] ?? [];
      }
    } catch (_) {
    } finally {
      _isLoadingProducts = false;
      notifyListeners();
    }
  }

  /// Cambiar estado del producto
  Future<bool> toggleProductStatus(String productId) async {
    final success = await _repo.toggleProductStatus(productId);
    if (success) {
      await loadProducts();
    }
    return success;
  }
}
