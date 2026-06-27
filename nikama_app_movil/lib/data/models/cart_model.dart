// ============================================================
// NIKAMA — Modelos del Carrito, Direcciones y Opciones
// ============================================================

class AddressModel {
  final String id;
  final String label;
  final String address;
  final String addressType;
  final String? reference;
  final String? deliveryNotes;
  final String? contactName;
  final String? contactPhone;
  final String? province;
  final String? district;
  final String? department;
  final double? latitude;
  final double? longitude;
  final bool isDefault;
  final bool isActive;

  AddressModel({
    required this.id,
    required this.label,
    required this.address,
    required this.addressType,
    this.reference,
    this.deliveryNotes,
    this.contactName,
    this.contactPhone,
    this.province,
    this.district,
    this.department,
    this.latitude,
    this.longitude,
    this.isDefault = false,
    this.isActive = true,
  });

  factory AddressModel.fromJson(Map<String, dynamic> json) {
    return AddressModel(
      id: json['id']?.toString() ?? '',
      label: json['label'] ?? 'Mi Dirección',
      address: json['address'] ?? '',
      addressType: json['address_type'] ?? 'home',
      reference: json['reference'],
      deliveryNotes: json['delivery_notes'],
      contactName: json['contact_name'],
      contactPhone: json['contact_phone'],
      province: json['province'],
      district: json['district'],
      department: json['department'],
      latitude: json['latitude'] != null
          ? double.tryParse(json['latitude'].toString())
          : null,
      longitude: json['longitude'] != null
          ? double.tryParse(json['longitude'].toString())
          : null,
      isDefault: json['is_default'] ?? false,
      isActive: json['is_active'] ?? true,
    );
  }

  String get typeIcon {
    switch (addressType) {
      case 'home':
        return '🏠';
      case 'work':
        return '🏢';
      default:
        return '📍';
    }
  }
}

// ─── Opción seleccionada de un ítem del carrito ──────────────
class CartItemOption {
  final String id;
  final String productOptionId;
  final String optionName;
  final double additionalPrice;

  CartItemOption({
    required this.id,
    required this.productOptionId,
    required this.optionName,
    required this.additionalPrice,
  });

  factory CartItemOption.fromJson(Map<String, dynamic> json) {
    return CartItemOption(
      id: json['id']?.toString() ?? '',
      productOptionId: json['product_option_id']?.toString() ?? '',
      optionName: json['option_name'] ?? '',
      additionalPrice:
          double.tryParse(json['additional_price']?.toString() ?? '0') ?? 0,
    );
  }
}

// ─── Ítem del carrito ───────────────────────────────────────
class CartItem {
  final String id;
  final String productId;
  final String productName;
  final String? productImage;
  final String? businessName;
  final int quantity;
  final double unitPrice;
  final double totalPrice;
  final String? notes;
  final List<CartItemOption> options;

  CartItem({
    required this.id,
    required this.productId,
    required this.productName,
    this.productImage,
    this.businessName,
    required this.quantity,
    required this.unitPrice,
    required this.totalPrice,
    this.notes,
    this.options = const [],
  });

  factory CartItem.fromJson(Map<String, dynamic> json) {
    return CartItem(
      id: json['id']?.toString() ?? '',
      productId: json['product_id']?.toString() ?? '',
      productName: json['product_name'] ?? '',
      productImage: json['product_image'],
      businessName: json['business_name'],
      quantity: json['quantity'] ?? 1,
      unitPrice:
          double.tryParse(json['unit_price']?.toString() ?? '0') ?? 0,
      totalPrice:
          double.tryParse(json['total_price']?.toString() ?? '0') ?? 0,
      notes: json['notes'],
      options: (json['options'] as List<dynamic>? ?? [])
          .map((o) => CartItemOption.fromJson(o as Map<String, dynamic>))
          .toList(),
    );
  }
}

// ─── Carrito principal ───────────────────────────────────────
class CartModel {
  final String id;
  final String status;
  final double subtotal;
  final int itemsCount;
  final List<CartItem> items;

  CartModel({
    required this.id,
    required this.status,
    required this.subtotal,
    required this.itemsCount,
    required this.items,
  });

  factory CartModel.fromJson(Map<String, dynamic> json) {
    return CartModel(
      id: json['id']?.toString() ?? '',
      status: json['status'] ?? 'active',
      subtotal:
          double.tryParse(json['subtotal']?.toString() ?? '0') ?? 0,
      itemsCount: json['items_count'] ?? 0,
      items: (json['items'] as List<dynamic>? ?? [])
          .map((i) => CartItem.fromJson(i as Map<String, dynamic>))
          .toList(),
    );
  }

  CartModel copyWith({
    String? id,
    String? status,
    double? subtotal,
    int? itemsCount,
    List<CartItem>? items,
  }) {
    return CartModel(
      id: id ?? this.id,
      status: status ?? this.status,
      subtotal: subtotal ?? this.subtotal,
      itemsCount: itemsCount ?? this.itemsCount,
      items: items ?? this.items,
    );
  }
}

// ─── Opciones y Grupos del Producto (para Detalle) ──────────
class ProductOption {
  final String id;
  final String name;
  final double additionalPrice;
  final bool isAvailable;

  ProductOption({
    required this.id,
    required this.name,
    required this.additionalPrice,
    required this.isAvailable,
  });

  factory ProductOption.fromJson(Map<String, dynamic> json) {
    return ProductOption(
      id: json['id']?.toString() ?? '',
      name: json['name'] ?? '',
      additionalPrice:
          double.tryParse(json['additional_price']?.toString() ?? '0') ?? 0,
      isAvailable: json['is_available'] ?? true,
    );
  }
}

class ProductOptionGroup {
  final String id;
  final String name;
  final String? description;
  final int minSelectable;
  final int maxSelectable;
  final bool isRequired;
  final List<ProductOption> options;

  ProductOptionGroup({
    required this.id,
    required this.name,
    this.description,
    required this.minSelectable,
    required this.maxSelectable,
    required this.isRequired,
    required this.options,
  });

  factory ProductOptionGroup.fromJson(Map<String, dynamic> json) {
    return ProductOptionGroup(
      id: json['id']?.toString() ?? '',
      name: json['name'] ?? '',
      description: json['description'],
      minSelectable: json['min_selectable'] ?? 0,
      maxSelectable: json['max_selectable'] ?? 1,
      isRequired: json['is_required'] ?? false,
      options: (json['options'] as List<dynamic>? ?? [])
          .map((o) => ProductOption.fromJson(o as Map<String, dynamic>))
          .toList(),
    );
  }
}
