// ============================================================
// NIKAMA APP — API Constants
// Backend: Laravel 13 con Sanctum (Bearer Token)
// Prefijo de rutas: /api/v1/
// ============================================================

class ApiConstants {
  // ---------------------------------------------------------
  // BASE URL
  // Emulador Android: 10.0.2.2 → localhost de la PC
  // Dispositivo físico o iOS Simulator: IP local de red, ej:
  //   static const String baseUrl = 'http://192.168.1.50:8000/api/v1';
  // ---------------------------------------------------------
  static const String baseUrl = 'http://192.168.100.12:8000/api/v1';

  // ---------------------------------------------------------
  // AUTH
  // ---------------------------------------------------------
  static const String login = '/auth/login';
  static const String logout = '/auth/logout';
  static const String me = '/auth/profile';
  static const String registerCustomer = '/auth/register/customer';
  static const String registerDriver = '/auth/register/driver';
  static const String forgotPassword = '/auth/forgot-password';
  static const String resetPassword = '/auth/reset-password';

  // ---------------------------------------------------------
  // PÚBLICO — Catálogo
  // ---------------------------------------------------------
  static const String categories = '/categories';
  static const String products = '/products';
  static const String productsFeatured = '/products/featured';
  static const String businesses = '/businesses';
  static const String banners = '/banners';

  // ---------------------------------------------------------
  // CLIENTE — Carrito y Pedidos
  // ---------------------------------------------------------
  static const String customerCart = '/customer/cart';
  static const String customerCartAdd = '/customer/cart/add';
  static const String customerCheckout = '/customer/checkout';
  static const String customerOrders = '/customer/orders';
  static const String customerAddresses = '/customer/addresses';
  static const String customerProfile = '/customer/profile';
  static const String customerFavorites = '/customer/favorites';

  // Tracking dinámico: usar customerOrderTrack(id)
  static String customerOrderTrack(String orderId) =>
      '/customer/orders/$orderId/track';
  static String customerOrderDetail(String orderId) =>
      '/customer/orders/$orderId';

  // ---------------------------------------------------------
  // DRIVER — Entregas
  // ---------------------------------------------------------
  static const String driverDashboard = '/driver/dashboard';
  static const String driverAssignments = '/driver/assignments';
  static const String driverProfile = '/driver/profile';

  static String driverAssignmentAccept(String assignmentId) =>
      '/driver/assignments/$assignmentId/accept';
  static String driverAssignmentReject(String assignmentId) =>
      '/driver/assignments/$assignmentId/reject';
  static String driverDeliveryEmitLocation(String deliveryId) =>
      '/driver/deliveries/$deliveryId/emit-location';
  static String driverDeliveryComplete(String deliveryId) =>
      '/driver/deliveries/$deliveryId/complete';

  // ---------------------------------------------------------
  // SELLER — Gestión de negocio
  // ---------------------------------------------------------
  static const String sellerDashboard = '/seller/dashboard';
  static const String sellerOrders = '/seller/orders';
  static const String sellerProducts = '/seller/products';
  static const String sellerBusiness = '/seller/business';

  // ---------------------------------------------------------
  // ADMIN
  // ---------------------------------------------------------
  static const String adminDashboard = '/admin/dashboard';
  static const String adminDriverApprovals = '/admin/drivers/pending';

  // ---------------------------------------------------------
  // TIMEOUT
  // ---------------------------------------------------------
  static const int connectTimeoutSeconds = 15;
  static const int receiveTimeoutSeconds = 15;
}
