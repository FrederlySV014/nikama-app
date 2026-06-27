import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../core/constants/app_theme.dart';
import '../../data/models/product_model.dart';
import '../../data/models/cart_model.dart';
import '../../data/providers/cart_provider.dart';
import '../../data/repositories/cart_repository.dart';
import 'cart_screen.dart';

// ============================================================
// NIKAMA — Pantalla de Detalle del Producto
// Paso 2.2 del Flujo: Ver detalles, modificadores, añadir al carrito
// ============================================================

class ProductDetailScreen extends StatefulWidget {
  final ProductModel product;
  const ProductDetailScreen({super.key, required this.product});

  @override
  State<ProductDetailScreen> createState() => _ProductDetailScreenState();
}

class _ProductDetailScreenState extends State<ProductDetailScreen> {
  final CartRepository _cartRepo = CartRepository();

  // Estado de la pantalla
  int _quantity = 1;
  String _notes = '';
  bool _loadingOptions = true;
  List<ProductOptionGroup> _optionGroups = [];

  // Opciones seleccionadas: groupId → optionId
  final Map<String, String> _selectedOptions = {};

  @override
  void initState() {
    super.initState();
    _loadProductOptions();
  }

  Future<void> _loadProductOptions() async {
    try {
      final data = await _cartRepo.getProductDetail(widget.product.id);
      final groups = (data['option_groups'] as List<dynamic>? ?? [])
          .map((g) => ProductOptionGroup.fromJson(g as Map<String, dynamic>))
          .toList();
      if (mounted) {
        setState(() {
          _optionGroups = groups;
          _loadingOptions = false;
        });
      }
    } catch (_) {
      if (mounted) setState(() => _loadingOptions = false);
    }
  }

  double get _totalPrice {
    double base = widget.product.price * _quantity;
    for (final group in _optionGroups) {
      final selectedId = _selectedOptions[group.id];
      if (selectedId != null) {
        final opt = group.options.firstWhere(
          (o) => o.id == selectedId,
          orElse: () => ProductOption(
              id: '', name: '', additionalPrice: 0, isAvailable: true),
        );
        base += opt.additionalPrice * _quantity;
      }
    }
    return base;
  }

  Future<void> _addToCart() async {
    // Verificar opciones requeridas
    for (final group in _optionGroups) {
      if (group.isRequired && !_selectedOptions.containsKey(group.id)) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text('Por favor elige una opción de: ${group.name}'),
            backgroundColor: NikamaColors.error,
          ),
        );
        return;
      }
    }

    final options = _selectedOptions.values
        .map((optId) => {'product_option_id': optId})
        .toList();

    final success = await context.read<CartProvider>().addToCart(
          productId: widget.product.id,
          quantity: _quantity,
          notes: _notes,
          options: options,
        );

    if (!mounted) return;

    if (success) {
      _showAddedToCartSheet();
    } else {
      final err = context.read<CartProvider>().errorMessage ?? 'Error al agregar';
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text(err), backgroundColor: NikamaColors.error),
      );
    }
  }

  void _showAddedToCartSheet() {
    showModalBottomSheet(
      context: context,
      backgroundColor: Colors.transparent,
      builder: (_) => Container(
        padding: const EdgeInsets.all(24),
        decoration: const BoxDecoration(
          color: NikamaColors.surfaceWhite,
          borderRadius: BorderRadius.vertical(top: Radius.circular(24)),
        ),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            Container(
              width: 48,
              height: 48,
              decoration: BoxDecoration(
                color: const Color(0xFFE8F5E9),
                borderRadius: BorderRadius.circular(14),
              ),
              child: const Icon(Icons.check_rounded,
                  color: Color(0xFF2E7D32), size: 28),
            ),
            const SizedBox(height: 16),
            Text(
              '¡${widget.product.name} añadido!',
              style: const TextStyle(
                fontFamily: 'Inter',
                fontSize: 18,
                fontWeight: FontWeight.w700,
                color: NikamaColors.black,
              ),
            ),
            const SizedBox(height: 8),
            const Text(
              'El producto fue agregado al carrito.',
              style: TextStyle(
                fontFamily: 'Inter',
                fontSize: 13,
                color: NikamaColors.mediumGray,
              ),
            ),
            const SizedBox(height: 24),
            Row(
              children: [
                Expanded(
                  child: OutlinedButton(
                    onPressed: () => Navigator.pop(context),
                    style: OutlinedButton.styleFrom(
                      padding: const EdgeInsets.symmetric(vertical: 14),
                      side: BorderSide(color: NikamaColors.primary),
                      shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(12)),
                    ),
                    child: Text('Seguir comprando',
                        style: TextStyle(
                            fontFamily: 'Inter',
                            fontWeight: FontWeight.w600,
                            color: NikamaColors.black)),
                  ),
                ),
                const SizedBox(width: 12),
                Expanded(
                  child: ElevatedButton(
                    onPressed: () {
                      Navigator.pop(context);
                      Navigator.push(
                        context,
                        MaterialPageRoute(builder: (_) => const CartScreen()),
                      );
                    },
                    style: ElevatedButton.styleFrom(
                      backgroundColor: NikamaColors.primary,
                      padding: const EdgeInsets.symmetric(vertical: 14),
                      shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(12)),
                    ),
                    child: const Text('Ver carrito',
                        style: TextStyle(
                            fontFamily: 'Inter',
                            fontWeight: FontWeight.w700,
                            color: NikamaColors.black)),
                  ),
                ),
              ],
            ),
          ],
        ),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    final product = widget.product;
    final cartProvider = context.watch<CartProvider>();

    return Scaffold(
      backgroundColor: NikamaColors.offWhite,
      body: CustomScrollView(
        slivers: [
          // ── App Bar con imagen ─────────────────────────────
          SliverAppBar(
            expandedHeight: 280,
            pinned: true,
            backgroundColor: NikamaColors.black,
            leading: GestureDetector(
              onTap: () => Navigator.pop(context),
              child: Container(
                margin: const EdgeInsets.all(8),
                decoration: BoxDecoration(
                  color: NikamaColors.black.withOpacity(0.5),
                  borderRadius: BorderRadius.circular(10),
                ),
                child: const Icon(Icons.arrow_back_ios_new,
                    color: NikamaColors.white, size: 18),
              ),
            ),
            actions: [
              Stack(
                children: [
                  GestureDetector(
                    onTap: () => Navigator.push(
                      context,
                      MaterialPageRoute(builder: (_) => const CartScreen()),
                    ),
                    child: Container(
                      margin: const EdgeInsets.all(8),
                      padding: const EdgeInsets.all(8),
                      decoration: BoxDecoration(
                        color: NikamaColors.black.withOpacity(0.5),
                        borderRadius: BorderRadius.circular(10),
                      ),
                      child: const Icon(Icons.shopping_cart_outlined,
                          color: NikamaColors.white, size: 22),
                    ),
                  ),
                  if (cartProvider.itemCount > 0)
                    Positioned(
                      right: 6,
                      top: 6,
                      child: Container(
                        width: 16,
                        height: 16,
                        decoration: BoxDecoration(
                          color: NikamaColors.primary,
                          borderRadius: BorderRadius.circular(8),
                        ),
                        child: Center(
                          child: Text(
                            '${cartProvider.itemCount}',
                            style: const TextStyle(
                              fontSize: 9,
                              fontWeight: FontWeight.w800,
                              color: NikamaColors.black,
                            ),
                          ),
                        ),
                      ),
                    ),
                ],
              ),
            ],
            flexibleSpace: FlexibleSpaceBar(
              background: product.mainImageUrl != null &&
                      product.mainImageUrl!.isNotEmpty
                  ? Image.network(
                      product.mainImageUrl!,
                      fit: BoxFit.cover,
                      errorBuilder: (_, __, ___) => Container(
                        decoration:
                            const BoxDecoration(gradient: NikamaColors.heroGradient),
                        child: const Center(
                          child: Text('🍽️', style: TextStyle(fontSize: 80)),
                        ),
                      ),
                    )
                  : Container(
                      decoration:
                          const BoxDecoration(gradient: NikamaColors.heroGradient),
                      child: const Center(
                        child: Text('🍽️', style: TextStyle(fontSize: 80)),
                      ),
                    ),
            ),
          ),

          // ── Contenido ──────────────────────────────────────
          SliverToBoxAdapter(
            child: Container(
              decoration: const BoxDecoration(
                color: NikamaColors.offWhite,
                borderRadius: BorderRadius.vertical(top: Radius.circular(24)),
              ),
              child: Padding(
                padding: const EdgeInsets.all(20),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    // Nombre y rating
                    Row(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Expanded(
                          child: Text(
                            product.name,
                            style: const TextStyle(
                              fontFamily: 'Inter',
                              fontSize: 22,
                              fontWeight: FontWeight.w800,
                              color: NikamaColors.black,
                            ),
                          ),
                        ),
                        const SizedBox(width: 12),
                        Container(
                          padding: const EdgeInsets.symmetric(
                              horizontal: 10, vertical: 6),
                          decoration: BoxDecoration(
                            color: NikamaColors.primaryLight,
                            borderRadius: BorderRadius.circular(10),
                          ),
                          child: Row(
                            children: [
                              Icon(Icons.star_rounded,
                                  color: NikamaColors.primary, size: 16),
                              const SizedBox(width: 4),
                              Text(
                                product.ratingAverage.toStringAsFixed(1),
                                style: const TextStyle(
                                  fontFamily: 'Inter',
                                  fontSize: 13,
                                  fontWeight: FontWeight.w700,
                                  color: NikamaColors.black,
                                ),
                              ),
                            ],
                          ),
                        ),
                      ],
                    ),
                    const SizedBox(height: 8),

                    // Negocio y tiempo
                    Row(
                      children: [
                        const Icon(Icons.store_outlined,
                            size: 14, color: NikamaColors.mediumGray),
                        const SizedBox(width: 4),
                        Text(
                          product.businessId, // Usamos businessId como fallback
                          style: const TextStyle(
                            fontFamily: 'Inter',
                            fontSize: 12,
                            color: NikamaColors.mediumGray,
                          ),
                        ),
                        const SizedBox(width: 12),
                        const Icon(Icons.access_time_rounded,
                            size: 14, color: NikamaColors.mediumGray),
                        const SizedBox(width: 4),
                        Text(
                          '${product.preparationTimeMinutes ?? 15} min',
                          style: const TextStyle(
                            fontFamily: 'Inter',
                            fontSize: 12,
                            color: NikamaColors.mediumGray,
                          ),
                        ),
                      ],
                    ),
                    const SizedBox(height: 14),

                    // Precio
                    Row(
                      children: [
                        Text(
                          'S/. ${product.price.toStringAsFixed(2)}',
                          style: TextStyle(
                            fontFamily: 'Inter',
                            fontSize: 26,
                            fontWeight: FontWeight.w800,
                            color: NikamaColors.primary,
                          ),
                        ),
                        if (product.hasDiscount) ...[
                          const SizedBox(width: 10),
                          Text(
                            'S/. ${product.comparePrice!.toStringAsFixed(2)}',
                            style: const TextStyle(
                              fontFamily: 'Inter',
                              fontSize: 16,
                              color: NikamaColors.lightGray,
                              decoration: TextDecoration.lineThrough,
                            ),
                          ),
                          const SizedBox(width: 8),
                          Container(
                            padding: const EdgeInsets.symmetric(
                                horizontal: 8, vertical: 3),
                            decoration: BoxDecoration(
                              color: const Color(0xFFFFEBEE),
                              borderRadius: BorderRadius.circular(6),
                            ),
                            child: Text(
                              '-${product.discountPercentage.toInt()}%',
                              style: const TextStyle(
                                fontFamily: 'Inter',
                                fontSize: 11,
                                fontWeight: FontWeight.w700,
                                color: NikamaColors.error,
                              ),
                            ),
                          ),
                        ],
                      ],
                    ),

                    if (product.description != null &&
                        product.description!.isNotEmpty) ...[
                      const SizedBox(height: 16),
                      Text(
                        product.description!,
                        style: const TextStyle(
                          fontFamily: 'Inter',
                          fontSize: 14,
                          color: NikamaColors.darkGray,
                          height: 1.5,
                        ),
                      ),
                    ],

                    const SizedBox(height: 24),
                    const Divider(color: Color(0xFFEEEEEE)),
                    const SizedBox(height: 16),

                    // ── Opciones / Modificadores ───────────────
                    if (_loadingOptions)
                      const Center(
                        child: CircularProgressIndicator(
                            color: NikamaColors.primary),
                      )
                    else
                      for (final group in _optionGroups) ...[
                        _OptionGroupWidget(
                          group: group,
                          selectedOptionId: _selectedOptions[group.id],
                          onSelect: (optId) {
                            setState(() {
                              _selectedOptions[group.id] = optId;
                            });
                          },
                        ),
                        const SizedBox(height: 16),
                      ],

                    const SizedBox(height: 8),

                    // ── Nota especial ──────────────────────────
                    const Text(
                      'Notas especiales',
                      style: TextStyle(
                        fontFamily: 'Inter',
                        fontSize: 16,
                        fontWeight: FontWeight.w700,
                        color: NikamaColors.black,
                      ),
                    ),
                    const SizedBox(height: 10),
                    Container(
                      decoration: BoxDecoration(
                        color: NikamaColors.surfaceWhite,
                        borderRadius: BorderRadius.circular(14),
                        border: Border.all(color: const Color(0xFFE0E0E0)),
                      ),
                      child: TextField(
                        onChanged: (v) => _notes = v,
                        maxLines: 3,
                        style: const TextStyle(
                            fontFamily: 'Inter',
                            fontSize: 13,
                            color: NikamaColors.black),
                        decoration: const InputDecoration(
                          hintText:
                              'Ej: Sin cebolla, sin picante, extra salsa...',
                          hintStyle: TextStyle(
                              fontFamily: 'Inter',
                              fontSize: 13,
                              color: NikamaColors.lightGray),
                          border: InputBorder.none,
                          contentPadding: EdgeInsets.all(14),
                        ),
                      ),
                    ),

                    const SizedBox(height: 24),

                    // ── Selector de cantidad ───────────────────
                    Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        const Text(
                          'Cantidad',
                          style: TextStyle(
                            fontFamily: 'Inter',
                            fontSize: 16,
                            fontWeight: FontWeight.w700,
                            color: NikamaColors.black,
                          ),
                        ),
                        Container(
                          decoration: BoxDecoration(
                            color: NikamaColors.surfaceWhite,
                            borderRadius: BorderRadius.circular(12),
                            border: Border.all(color: const Color(0xFFE0E0E0)),
                          ),
                          child: Row(
                            children: [
                              _QtyButton(
                                icon: Icons.remove,
                                onTap: () {
                                  if (_quantity > 1) {
                                    setState(() => _quantity--);
                                  }
                                },
                              ),
                              Padding(
                                padding:
                                    const EdgeInsets.symmetric(horizontal: 16),
                                child: Text(
                                  '$_quantity',
                                  style: const TextStyle(
                                    fontFamily: 'Inter',
                                    fontSize: 16,
                                    fontWeight: FontWeight.w700,
                                    color: NikamaColors.black,
                                  ),
                                ),
                              ),
                              _QtyButton(
                                icon: Icons.add,
                                onTap: () => setState(() => _quantity++),
                                filled: true,
                              ),
                            ],
                          ),
                        ),
                      ],
                    ),

                    const SizedBox(height: 32),

                    // ── Botón Añadir al carrito ────────────────
                    SizedBox(
                      width: double.infinity,
                      child: ElevatedButton(
                        onPressed:
                            cartProvider.isAdding ? null : _addToCart,
                        style: ElevatedButton.styleFrom(
                          backgroundColor: NikamaColors.primary,
                          disabledBackgroundColor:
                              NikamaColors.primary.withOpacity(0.6),
                          padding: const EdgeInsets.symmetric(vertical: 18),
                          shape: RoundedRectangleBorder(
                            borderRadius: BorderRadius.circular(16),
                          ),
                          elevation: 0,
                        ),
                        child: cartProvider.isAdding
                            ? const SizedBox(
                                width: 22,
                                height: 22,
                                child: CircularProgressIndicator(
                                    strokeWidth: 2.5,
                                    color: NikamaColors.black),
                              )
                            : Row(
                                mainAxisAlignment: MainAxisAlignment.center,
                                children: [
                                  const Icon(Icons.add_shopping_cart_rounded,
                                      color: NikamaColors.black, size: 20),
                                  const SizedBox(width: 10),
                                  Text(
                                    'Añadir al carrito · S/. ${_totalPrice.toStringAsFixed(2)}',
                                    style: const TextStyle(
                                      fontFamily: 'Inter',
                                      fontSize: 15,
                                      fontWeight: FontWeight.w800,
                                      color: NikamaColors.black,
                                    ),
                                  ),
                                ],
                              ),
                      ),
                    ),
                    const SizedBox(height: 16),
                  ],
                ),
              ),
            ),
          ),
        ],
      ),
    );
  }
}

// ── Widget: Grupo de Opciones ────────────────────────────────
class _OptionGroupWidget extends StatelessWidget {
  final ProductOptionGroup group;
  final String? selectedOptionId;
  final ValueChanged<String> onSelect;

  const _OptionGroupWidget({
    required this.group,
    required this.selectedOptionId,
    required this.onSelect,
  });

  @override
  Widget build(BuildContext context) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Row(
          children: [
            Expanded(
              child: Text(
                group.name,
                style: const TextStyle(
                  fontFamily: 'Inter',
                  fontSize: 16,
                  fontWeight: FontWeight.w700,
                  color: NikamaColors.black,
                ),
              ),
            ),
            if (group.isRequired)
              Container(
                padding:
                    const EdgeInsets.symmetric(horizontal: 8, vertical: 3),
                decoration: BoxDecoration(
                  color: const Color(0xFFFFF3E0),
                  borderRadius: BorderRadius.circular(6),
                ),
                child: const Text(
                  'Obligatorio',
                  style: TextStyle(
                    fontFamily: 'Inter',
                    fontSize: 10,
                    fontWeight: FontWeight.w700,
                    color: Color(0xFFE65100),
                  ),
                ),
              ),
          ],
        ),
        if (group.description != null) ...[
          const SizedBox(height: 4),
          Text(
            group.description!,
            style: const TextStyle(
              fontFamily: 'Inter',
              fontSize: 12,
              color: NikamaColors.mediumGray,
            ),
          ),
        ],
        const SizedBox(height: 12),
        ...group.options
            .where((o) => o.isAvailable)
            .map((opt) {
          final isSelected = selectedOptionId == opt.id;
          return GestureDetector(
            onTap: () => onSelect(opt.id),
            child: AnimatedContainer(
              duration: const Duration(milliseconds: 200),
              margin: const EdgeInsets.only(bottom: 8),
              padding:
                  const EdgeInsets.symmetric(horizontal: 14, vertical: 12),
              decoration: BoxDecoration(
                color: isSelected
                    ? NikamaColors.primaryLight
                    : NikamaColors.surfaceWhite,
                borderRadius: BorderRadius.circular(12),
                border: Border.all(
                  color: isSelected
                      ? NikamaColors.primary
                      : const Color(0xFFE0E0E0),
                  width: isSelected ? 2 : 1,
                ),
              ),
              child: Row(
                children: [
                  Container(
                    width: 20,
                    height: 20,
                    decoration: BoxDecoration(
                      shape: BoxShape.circle,
                      border: Border.all(
                        color: isSelected
                            ? NikamaColors.primary
                            : NikamaColors.lightGray,
                        width: 2,
                      ),
                      color: isSelected ? NikamaColors.primary : Colors.transparent,
                    ),
                    child: isSelected
                        ? const Icon(Icons.check_rounded,
                            size: 12, color: NikamaColors.black)
                        : null,
                  ),
                  const SizedBox(width: 12),
                  Expanded(
                    child: Text(
                      opt.name,
                      style: TextStyle(
                        fontFamily: 'Inter',
                        fontSize: 14,
                        fontWeight: isSelected
                            ? FontWeight.w600
                            : FontWeight.w400,
                        color: NikamaColors.black,
                      ),
                    ),
                  ),
                  if (opt.additionalPrice > 0)
                    Text(
                      '+S/. ${opt.additionalPrice.toStringAsFixed(2)}',
                      style: TextStyle(
                        fontFamily: 'Inter',
                        fontSize: 13,
                        fontWeight: FontWeight.w600,
                        color: NikamaColors.primary,
                      ),
                    ),
                ],
              ),
            ),
          );
        }),
      ],
    );
  }
}

// ── Botón de cantidad ────────────────────────────────────────
class _QtyButton extends StatelessWidget {
  final IconData icon;
  final VoidCallback onTap;
  final bool filled;

  const _QtyButton({
    required this.icon,
    required this.onTap,
    this.filled = false,
  });

  @override
  Widget build(BuildContext context) {
    return GestureDetector(
      onTap: onTap,
      child: Container(
        width: 36,
        height: 36,
        decoration: BoxDecoration(
          color: filled ? NikamaColors.primary : Colors.transparent,
          borderRadius: BorderRadius.circular(10),
        ),
        child: Icon(
          icon,
          size: 18,
          color: filled ? NikamaColors.black : NikamaColors.mediumGray,
        ),
      ),
    );
  }
}
