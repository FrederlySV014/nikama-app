// ============================================================
// NIKAMA — Modelo de Categoría
// ============================================================

class CategoryModel {
  final String id;
  final String? parentId;
  final String name;
  final String slug;
  final String? description;
  final String? icon;
  final String? imageUrl;
  final int sortOrder;
  final bool isActive;

  CategoryModel({
    required this.id,
    this.parentId,
    required this.name,
    required this.slug,
    this.description,
    this.icon,
    this.imageUrl,
    this.sortOrder = 0,
    this.isActive = true,
  });

  factory CategoryModel.fromJson(Map<String, dynamic> json) {
    return CategoryModel(
      id: json['id']?.toString() ?? '',
      parentId: json['parent_id']?.toString(),
      name: json['name'] ?? '',
      slug: json['slug'] ?? '',
      description: json['description'],
      icon: json['icon'],
      imageUrl: json['image_url'],
      sortOrder: json['sort_order'] ?? 0,
      isActive: json['is_active'] ?? true,
    );
  }
}
