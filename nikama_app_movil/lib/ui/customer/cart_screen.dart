import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../core/constants/app_theme.dart';
import '../../data/providers/cart_provider.dart';
import '../../data/models/cart_model.dart';
import 'checkout_screen.dart';

// ============================================================
// NIKAMA — Pantalla del Carrito de Compras
// Paso 2.3: Lista de productos añadidos, desglose y checkout
// ============================================================

class CartScreen extends StatefulWidget {
  const CartScreen({super.key});

  @override
  State<CartScreen> createState() => _CartScreenState();
}

class _CartScreenState extends State<CartScreen> {
  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      context.read<CartProvider>().loadCart();
    });
  }

  @override
  Widget build(BuildContext context) {
    final cartProvider = context.watch<CartProvider>();

    return Scaffold(
      backgroundColor: NikamaColors.offWhite,
      appBar: AppBar(
        backgroundColor: NikamaColors.black,
        foregroundColor: NikamaColors.white,
        elevation: 0,
        title: Row(
          children: [
            const Text(
              'Mi Carrito',
              style: TextStyle(
                fontFamily: 'Inter',
                fontSize: 18,
                fontWeight: FontWeight.w700,
              ),
            ),
            if (cartProvider.itemCount > 0) ...[
              const SizedBox(width: 10),
              Container(
                padding:
                    const EdgeInsets.symmetric(horizontal: 8, vertical: 3),
                decoration: BoxDecoration(
                  color: NikamaColors.primary,
                  borderRadius: BorderRadius.circular(8),
                ),
                child: Text(
                  '${cartProvider.itemCount}',
                  style: const TextStyle(
                    fontFamily: 'Inter',
                    fontSize: 12,
                    fontWeight: FontWeight.w700,
                    color: NikamaColors.black,
                  ),
                ),
              ),
            ],
          ],
        ),
        leading: GestureDetector(
          onTap: () => Navigator.pop(context),
          child: const Icon(Icons.arrow_back_ios_new, size: 18),
        ),
      ),
      body: cartProvider.isLoading
          ? const Center(
              child: CircularProgressIndicator(color: NikamaColors.primary))
          : cartProvider.items.isEmpty
              ? _EmptyCart()
              : _CartContent(cartProvider: cartProvider),
    );
  }
}

// ── Carrito vacío ─────────────────────────────────────────────
class _EmptyCart extends StatelessWidget {
  @override
  Widget build(BuildContext context) {
    return Center(
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          const Text('🛒', style: TextStyle(fontSize: 72)),
          const SizedBox(height: 20),
          const Text(
            'Tu carrito está vacío',
            style: TextStyle(
              fontFamily: 'Inter',
              fontSize: 20,
              fontWeight: FontWeight.w700,
              color: NikamaColors.black,
            ),
          ),
          const SizedBox(height: 10),
          const Text(
            'Agrega productos para\nrealizar tu pedido',
            textAlign: TextAlign.center,
            style: TextStyle(
              fontFamily: 'Inter',
              fontSize: 14,
              color: NikamaColors.mediumGray,
            ),
          ),
          const SizedBox(height: 28),
          ElevatedButton(
            onPressed: () => Navigator.pop(context),
            style: ElevatedButton.styleFrom(
              backgroundColor: NikamaColors.primary,
              padding:
                  const EdgeInsets.symmetric(horizontal: 32, vertical: 14),
              shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(12)),
            ),
            child: const Text(
              'Explorar productos',
              style: TextStyle(
                fontFamily: 'Inter',
                fontWeight: FontWeight.w700,
                color: NikamaColors.black,
              ),
            ),
          ),
        ],
      ),
    );
  }
}

// ── Contenido del carrito ─────────────────────────────────────
class _CartContent extends StatelessWidget {
  final CartProvider cartProvider;
  const _CartContent({required this.cartProvider});

  static const double _deliveryFee = 5.00;

  double get _total => cartProvider.subtotal + _deliveryFee;

  @override
  Widget build(BuildContext context) {
    return Column(
      children: [
        Expanded(
          child: ListView.builder(
            padding: const EdgeInsets.all(16),
            itemCount: cartProvider.items.length,
            itemBuilder: (_, i) {
              return _CartItemCard(item: cartProvider.items[i]);
            },
          ),
        ),

        // ── Resumen de costos ──────────────────────────────
        Container(
          padding: const EdgeInsets.fromLTRB(20, 20, 20, 0),
          decoration: BoxDecoration(
            color: NikamaColors.surfaceWhite,
            borderRadius:
                const BorderRadius.vertical(top: Radius.circular(24)),
            boxShadow: [
              BoxShadow(
                color: NikamaColors.black.withOpacity(0.08),
                blurRadius: 20,
                offset: const Offset(0, -4),
              ),
            ],
          ),
          child: Column(
            children: [
              _SummaryRow(
                  label: 'Subtotal',
                  value: 'S/. ${cartProvider.subtotal.toStringAsFixed(2)}'),
              const SizedBox(height: 10),
              _SummaryRow(
                  label: 'Costo de envío',
                  value: 'S/. ${_deliveryFee.toStringAsFixed(2)}'),
              const SizedBox(height: 14),
              const Divider(color: Color(0xFFEEEEEE)),
              const SizedBox(height: 14),
              _SummaryRow(
                label: 'Total a pagar',
                value: 'S/. ${_total.toStringAsFixed(2)}',
                isTotal: true,
              ),
              const SizedBox(height: 20),
              SizedBox(
                width: double.infinity,
                child: ElevatedButton(
                  onPressed: () {
                    Navigator.push(
                      context,
                      MaterialPageRoute(
                        builder: (_) => CheckoutScreen(
                          subtotal: cartProvider.subtotal,
                          deliveryFee: _deliveryFee,
                          total: _total,
                        ),
                      ),
                    );
                  },
                  style: ElevatedButton.styleFrom(
                    backgroundColor: NikamaColors.primary,
                    padding: const EdgeInsets.symmetric(vertical: 18),
                    shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(16)),
                    elevation: 0,
                  ),
                  child: const Text(
                    'Proceder al Checkout →',
                    style: TextStyle(
                      fontFamily: 'Inter',
                      fontSize: 16,
                      fontWeight: FontWeight.w800,
                      color: NikamaColors.black,
                    ),
                  ),
                ),
              ),
              const SizedBox(height: 20),
            ],
          ),
        ),
      ],
    );
  }
}

// ── Tarjeta de ítem del carrito ──────────────────────────────
class _CartItemCard extends StatelessWidget {
  final CartItem item;
  const _CartItemCard({required this.item});

  @override
  Widget build(BuildContext context) {
    return Container(
      margin: const EdgeInsets.only(bottom: 12),
      padding: const EdgeInsets.all(14),
      decoration: BoxDecoration(
        color: NikamaColors.surfaceWhite,
        borderRadius: BorderRadius.circular(16),
        boxShadow: [
          BoxShadow(
            color: NikamaColors.black.withOpacity(0.05),
            blurRadius: 10,
            offset: const Offset(0, 2),
          ),
        ],
      ),
      child: Row(
        children: [
          // Imagen del producto
          Container(
            width: 70,
            height: 70,
            decoration: BoxDecoration(
              gradient: NikamaColors.heroGradient,
              borderRadius: BorderRadius.circular(12),
            ),
            child: item.productImage != null && item.productImage!.isNotEmpty
                ? ClipRRect(
                    borderRadius: BorderRadius.circular(12),
                    child: Image.network(
                      item.productImage!,
                      fit: BoxFit.cover,
                      errorBuilder: (_, __, ___) => const Center(
                        child: Text('🍽️', style: TextStyle(fontSize: 30)),
                      ),
                    ),
                  )
                : const Center(
                    child: Text('🍽️', style: TextStyle(fontSize: 30))),
          ),
          const SizedBox(width: 14),

          // Info del producto
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  item.productName,
                  style: const TextStyle(
                    fontFamily: 'Inter',
                    fontSize: 14,
                    fontWeight: FontWeight.w700,
                    color: NikamaColors.black,
                  ),
                  maxLines: 2,
                  overflow: TextOverflow.ellipsis,
                ),
                if (item.options.isNotEmpty) ...[
                  const SizedBox(height: 4),
                  Text(
                    item.options.map((o) => o.optionName).join(', '),
                    style: const TextStyle(
                      fontFamily: 'Inter',
                      fontSize: 11,
                      color: NikamaColors.mediumGray,
                    ),
                    maxLines: 1,
                    overflow: TextOverflow.ellipsis,
                  ),
                ],
                if (item.notes != null && item.notes!.isNotEmpty) ...[
                  const SizedBox(height: 2),
                  Text(
                    '📝 ${item.notes}',
                    style: const TextStyle(
                      fontFamily: 'Inter',
                      fontSize: 11,
                      color: NikamaColors.mediumGray,
                      fontStyle: FontStyle.italic,
                    ),
                    maxLines: 1,
                    overflow: TextOverflow.ellipsis,
                  ),
                ],
                const SizedBox(height: 8),
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Container(
                      padding: const EdgeInsets.symmetric(
                          horizontal: 8, vertical: 4),
                      decoration: BoxDecoration(
                        color: NikamaColors.primaryLight,
                        borderRadius: BorderRadius.circular(8),
                      ),
                      child: Text(
                        'x${item.quantity}',
                        style: const TextStyle(
                          fontFamily: 'Inter',
                          fontSize: 12,
                          fontWeight: FontWeight.w700,
                          color: NikamaColors.black,
                        ),
                      ),
                    ),
                    Text(
                      'S/. ${item.totalPrice.toStringAsFixed(2)}',
                      style: TextStyle(
                        fontFamily: 'Inter',
                        fontSize: 14,
                        fontWeight: FontWeight.w800,
                        color: NikamaColors.primary,
                      ),
                    ),
                  ],
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }
}

// ── Fila de resumen ──────────────────────────────────────────
class _SummaryRow extends StatelessWidget {
  final String label;
  final String value;
  final bool isTotal;

  const _SummaryRow({
    required this.label,
    required this.value,
    this.isTotal = false,
  });

  @override
  Widget build(BuildContext context) {
    return Row(
      mainAxisAlignment: MainAxisAlignment.spaceBetween,
      children: [
        Text(
          label,
          style: TextStyle(
            fontFamily: 'Inter',
            fontSize: isTotal ? 16 : 14,
            fontWeight: isTotal ? FontWeight.w700 : FontWeight.w400,
            color: isTotal ? NikamaColors.black : NikamaColors.mediumGray,
          ),
        ),
        Text(
          value,
          style: TextStyle(
            fontFamily: 'Inter',
            fontSize: isTotal ? 18 : 14,
            fontWeight: isTotal ? FontWeight.w800 : FontWeight.w600,
            color:
                isTotal ? NikamaColors.primary : NikamaColors.black,
          ),
        ),
      ],
    );
  }
}
