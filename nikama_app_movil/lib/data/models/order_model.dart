// ============================================================
// NIKAMA — Modelo de Orden/Pedido
// ============================================================

class OrderModel {
  final String id;
  final String orderNumber;
  final String status;
  final String paymentStatus;
  final double subtotal;
  final double deliveryFee;
  final double total;
  final String? deliveryAddress;
  final String? notes;
  final DateTime? createdAt;
  final List<OrderItemModel> items;

  OrderModel({
    required this.id,
    required this.orderNumber,
    required this.status,
    required this.paymentStatus,
    required this.subtotal,
    required this.deliveryFee,
    required this.total,
    this.deliveryAddress,
    this.notes,
    this.createdAt,
    this.items = const [],
  });

  String get statusLabel {
    switch (status) {
      case 'pending':      return 'Pendiente';
      case 'confirmed':    return 'Confirmado';
      case 'preparing':    return 'Preparando';
      case 'ready':        return 'Listo';
      case 'on_the_way':   return 'En camino';
      case 'delivered':    return 'Entregado';
      case 'cancelled':    return 'Cancelado';
      default:             return status;
    }
  }

  factory OrderModel.fromJson(Map<String, dynamic> json) {
    return OrderModel(
      id: json['id']?.toString() ?? '',
      orderNumber: json['order_number'] ?? '',
      status: json['status'] ?? 'pending',
      paymentStatus: json['payment_status'] ?? 'pending',
      subtotal: double.tryParse(json['subtotal']?.toString() ?? '0') ?? 0,
      deliveryFee:
          double.tryParse(json['delivery_fee']?.toString() ?? '0') ?? 0,
      total: double.tryParse(json['total']?.toString() ?? '0') ?? 0,
      deliveryAddress: json['delivery_address'],
      notes: json['notes'],
      createdAt: json['created_at'] != null
          ? DateTime.tryParse(json['created_at'])
          : null,
      items: (json['items'] as List<dynamic>?)
              ?.map((i) => OrderItemModel.fromJson(i))
              .toList() ??
          [],
    );
  }
}

class OrderItemModel {
  final String id;
  final String productName;
  final double unitPrice;
  final int quantity;
  final double subtotal;
  final String? productImageUrl;
  final String? notes;

  OrderItemModel({
    required this.id,
    required this.productName,
    required this.unitPrice,
    required this.quantity,
    required this.subtotal,
    this.productImageUrl,
    this.notes,
  });

  factory OrderItemModel.fromJson(Map<String, dynamic> json) {
    return OrderItemModel(
      id: json['id']?.toString() ?? '',
      productName: json['product_name'] ?? '',
      unitPrice: double.tryParse(json['unit_price']?.toString() ?? '0') ?? 0,
      quantity: json['quantity'] ?? 1,
      subtotal: double.tryParse(json['subtotal']?.toString() ?? '0') ?? 0,
      productImageUrl: json['product_image_url'],
      notes: json['notes'],
    );
  }
}
