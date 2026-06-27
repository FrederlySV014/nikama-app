import 'package:flutter/foundation.dart';
import '../repositories/cart_repository.dart';
import '../models/cart_model.dart';

// ============================================================
// NIKAMA — Cart Provider
// Estado global del carrito del cliente
// ============================================================

class CartProvider with ChangeNotifier {
  final CartRepository _repo = CartRepository();

  CartModel? _cart;
  bool _isLoading = false;
  bool _isAdding = false;
  String? _errorMessage;

  CartModel? get cart => _cart;
  bool get isLoading => _isLoading;
  bool get isAdding => _isAdding;
  String? get errorMessage => _errorMessage;

  int get itemCount => _cart?.itemsCount ?? 0;
  double get subtotal => _cart?.subtotal ?? 0;
  List<CartItem> get items => _cart?.items ?? [];

  // ──────────────────────────────────────────────────────────
  // Cargar carrito
  // ──────────────────────────────────────────────────────────
  Future<void> loadCart() async {
    _isLoading = true;
    _errorMessage = null;
    notifyListeners();

    try {
      _cart = await _repo.getCart();
    } catch (e) {
      _errorMessage = e.toString();
    }

    _isLoading = false;
    notifyListeners();
  }

  // ──────────────────────────────────────────────────────────
  // Agregar al carrito
  // ──────────────────────────────────────────────────────────
  Future<bool> addToCart({
    required String productId,
    required int quantity,
    String? notes,
    List<Map<String, dynamic>> options = const [],
  }) async {
    _isAdding = true;
    _errorMessage = null;
    notifyListeners();

    try {
      _cart = await _repo.addToCart(
        productId: productId,
        quantity: quantity,
        notes: notes,
        options: options,
      );
      _isAdding = false;
      notifyListeners();
      return true;
    } catch (e) {
      _errorMessage = e.toString();
      _isAdding = false;
      notifyListeners();
      return false;
    }
  }

  // ──────────────────────────────────────────────────────────
  // Obtener direcciones del cliente
  // ──────────────────────────────────────────────────────────
  Future<List<AddressModel>> getAddresses() async {
    try {
      return await _repo.getAddresses();
    } catch (_) {
      return [];
    }
  }

  // ──────────────────────────────────────────────────────────
  // Limpiar el carrito localmente (post-checkout)
  // ──────────────────────────────────────────────────────────
  void clearCart() {
    _cart = null;
    notifyListeners();
  }

  void clearError() {
    _errorMessage = null;
    notifyListeners();
  }
}
