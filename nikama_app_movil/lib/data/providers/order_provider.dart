import 'dart:async';
import 'package:flutter/foundation.dart';
import '../repositories/order_repository.dart';

// ============================================================
// NIKAMA — Order Provider
// Estado global de pedidos, checkout y tracking en vivo
// ============================================================

enum OrderStatus { idle, loading, success, error }

class OrderProvider with ChangeNotifier {
  final OrderRepository _repo = OrderRepository();

  // Estado checkout
  OrderStatus _checkoutStatus = OrderStatus.idle;
  Map<String, dynamic>? _lastOrder;
  String? _checkoutError;

  // Estado tracking
  Map<String, dynamic>? _trackData;
  bool _isTracking = false;
  Timer? _trackingTimer;

  // Historial de pedidos
  List<Map<String, dynamic>> _orders = [];
  bool _loadingOrders = false;

  // ── Getters ─────────────────────────────────────────────
  OrderStatus get checkoutStatus => _checkoutStatus;
  Map<String, dynamic>? get lastOrder => _lastOrder;
  String? get checkoutError => _checkoutError;
  Map<String, dynamic>? get trackData => _trackData;
  bool get isTracking => _isTracking;
  List<Map<String, dynamic>> get orders => _orders;
  bool get loadingOrders => _loadingOrders;

  String? get trackOrderStatus => _trackData?['status'] as String?;
  Map<String, dynamic>? get trackDriver =>
      _trackData?['driver'] as Map<String, dynamic>?;
  Map<String, dynamic>? get trackLiveLocation =>
      _trackData?['live_location'] as Map<String, dynamic>?;

  // ── Checkout ─────────────────────────────────────────────
  Future<bool> checkout({
    required String addressSelectionType,
    String? customerAddressId,
    required String paymentMethod,
    String? notes,
  }) async {
    _checkoutStatus = OrderStatus.loading;
    _checkoutError = null;
    notifyListeners();

    try {
      final result = await _repo.checkout(
        addressSelectionType: addressSelectionType,
        customerAddressId: customerAddressId,
        paymentMethod: paymentMethod,
        notes: notes,
      );
      _lastOrder = result['data'] as Map<String, dynamic>?;
      _checkoutStatus = OrderStatus.success;
      notifyListeners();
      return true;
    } catch (e) {
      _checkoutError = e.toString();
      _checkoutStatus = OrderStatus.error;
      notifyListeners();
      return false;
    }
  }

  // ── Tracking en vivo ─────────────────────────────────────
  void startTracking(String orderId) {
    _isTracking = true;
    _fetchTrack(orderId);
    // Polling cada 15 segundos
    _trackingTimer = Timer.periodic(const Duration(seconds: 15), (_) {
      _fetchTrack(orderId);
    });
    notifyListeners();
  }

  Future<void> _fetchTrack(String orderId) async {
    try {
      final result = await _repo.trackOrder(orderId);
      _trackData = result;
      notifyListeners();
    } catch (_) {
      // Silenciar errores de polling
    }
  }

  void stopTracking() {
    _trackingTimer?.cancel();
    _trackingTimer = null;
    _isTracking = false;
    _trackData = null;
    notifyListeners();
  }

  // ── Historial de pedidos ──────────────────────────────────
  Future<void> loadOrders() async {
    _loadingOrders = true;
    notifyListeners();
    try {
      _orders = await _repo.getOrders();
    } catch (_) {
      _orders = [];
    }
    _loadingOrders = false;
    notifyListeners();
  }

  // ── Calificación ─────────────────────────────────────────
  Future<bool> rateDriver({
    required String orderId,
    required int rating,
    String? comment,
  }) async {
    try {
      await _repo.rateDriver(
        orderId: orderId,
        rating: rating,
        comment: comment,
      );
      return true;
    } catch (_) {
      return false;
    }
  }

  Future<bool> rateProduct({
    required String productId,
    required int rating,
    String? comment,
  }) async {
    try {
      await _repo.rateProduct(
        productId: productId,
        rating: rating,
        comment: comment,
      );
      return true;
    } catch (_) {
      return false;
    }
  }

  void resetCheckout() {
    _checkoutStatus = OrderStatus.idle;
    _checkoutError = null;
    _lastOrder = null;
    notifyListeners();
  }

  @override
  void dispose() {
    _trackingTimer?.cancel();
    super.dispose();
  }
}
