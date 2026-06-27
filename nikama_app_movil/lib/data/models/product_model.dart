// ============================================================
// NIKAMA — Modelo de Producto
// ============================================================

class ProductModel {
  final String id;
  final String businessId;
  final String name;
  final String slug;
  final String? description;
  final double price;
  final double? comparePrice;
  final String? mainImageUrl;
  final double ratingAverage;
  final int totalReviews;
  final bool isAvailable;
  final bool isFeatured;
  final int? preparationTimeMinutes;
  final String status;

  ProductModel({
    required this.id,
    required this.businessId,
    required this.name,
    required this.slug,
    this.description,
    required this.price,
    this.comparePrice,
    this.mainImageUrl,
    this.ratingAverage = 0,
    this.totalReviews = 0,
    this.isAvailable = true,
    this.isFeatured = false,
    this.preparationTimeMinutes,
    this.status = 'published',
  });

  bool get hasDiscount =>
      comparePrice != null && comparePrice! > price;

  double get discountPercentage {
    if (!hasDiscount) return 0;
    return ((comparePrice! - price) / comparePrice! * 100).roundToDouble();
  }

  factory ProductModel.fromJson(Map<String, dynamic> json) {
    return ProductModel(
      id: json['id']?.toString() ?? '',
      businessId: json['business_id']?.toString() ?? '',
      name: json['name'] ?? '',
      slug: json['slug'] ?? '',
      description: json['description'],
      price: double.tryParse(json['price']?.toString() ?? '0') ?? 0,
      comparePrice: json['compare_price'] != null
          ? double.tryParse(json['compare_price'].toString())
          : null,
      mainImageUrl: json['main_image_url'],
      ratingAverage:
          double.tryParse(json['rating_average']?.toString() ?? '0') ?? 0,
      totalReviews: json['total_reviews'] ?? 0,
      isAvailable: json['is_available'] ?? true,
      isFeatured: json['is_featured'] ?? false,
      preparationTimeMinutes: json['preparation_time_minutes'],
      status: json['status'] ?? 'published',
    );
  }
}
