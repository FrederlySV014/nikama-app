// ============================================================
// NIKAMA — Modelo de Negocio/Restaurante
// ============================================================

class BusinessModel {
  final String id;
  final String businessName;
  final String slug;
  final String? description;
  final String? logoUrl;
  final String? bannerUrl;
  final String? contactPhone;
  final double ratingAverage;
  final int totalReviews;
  final int estimatedPreparationTimeMinutes;
  final double minimumOrderAmount;
  final bool offersDelivery;
  final bool offersPickup;
  final bool isFeatured;
  final bool acceptsOrders;
  final String status;

  BusinessModel({
    required this.id,
    required this.businessName,
    required this.slug,
    this.description,
    this.logoUrl,
    this.bannerUrl,
    this.contactPhone,
    this.ratingAverage = 0,
    this.totalReviews = 0,
    this.estimatedPreparationTimeMinutes = 15,
    this.minimumOrderAmount = 0,
    this.offersDelivery = true,
    this.offersPickup = true,
    this.isFeatured = false,
    this.acceptsOrders = true,
    this.status = 'active',
  });

  bool get isOpen => acceptsOrders && status == 'active';

  factory BusinessModel.fromJson(Map<String, dynamic> json) {
    return BusinessModel(
      id: json['id']?.toString() ?? '',
      businessName: json['business_name'] ?? '',
      slug: json['slug'] ?? '',
      description: json['description'],
      logoUrl: json['logo_url'],
      bannerUrl: json['banner_url'],
      contactPhone: json['contact_phone'],
      ratingAverage:
          double.tryParse(json['rating_average']?.toString() ?? '0') ?? 0,
      totalReviews: json['total_reviews'] ?? 0,
      estimatedPreparationTimeMinutes:
          json['estimated_preparation_time_minutes'] ?? 15,
      minimumOrderAmount:
          double.tryParse(json['minimum_order_amount']?.toString() ?? '0') ?? 0,
      offersDelivery: json['offers_delivery'] ?? true,
      offersPickup: json['offers_pickup'] ?? true,
      isFeatured: json['is_featured'] ?? false,
      acceptsOrders: json['accepts_orders'] ?? true,
      status: json['status'] ?? 'active',
    );
  }
}
