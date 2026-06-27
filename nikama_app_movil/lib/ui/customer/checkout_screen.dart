import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../core/constants/app_theme.dart';
import '../../data/models/cart_model.dart';
import '../../data/providers/cart_provider.dart';
import '../../data/providers/order_provider.dart';
import 'order_tracking_screen.dart';

// ============================================================
// NIKAMA — Pantalla de Checkout
// Paso 2.4: Dirección + Método de Pago + Confirmar Pedido
// ============================================================

class CheckoutScreen extends StatefulWidget {
  final double subtotal;
  final double deliveryFee;
  final double total;

  const CheckoutScreen({
    super.key,
    required this.subtotal,
    required this.deliveryFee,
    required this.total,
  });

  @override
  State<CheckoutScreen> createState() => _CheckoutScreenState();
}

class _CheckoutScreenState extends State<CheckoutScreen> {
  // Direcciones
  List<AddressModel> _addresses = [];
  AddressModel? _selectedAddress;
  bool _loadingAddresses = true;

  // Método de pago
  String _selectedPayment = 'cash';
  final List<_PaymentMethod> _paymentMethods = [
    _PaymentMethod(id: 'cash', label: 'Efectivo', emoji: '💵'),
    _PaymentMethod(id: 'yape', label: 'Yape', emoji: '📱'),
    _PaymentMethod(id: 'plin', label: 'Plin', emoji: '💜'),
    _PaymentMethod(id: 'card', label: 'Tarjeta', emoji: '💳'),
  ];

  // Notas
  String _notes = '';

  @override
  void initState() {
    super.initState();
    _loadAddresses();
  }

  Future<void> _loadAddresses() async {
    final addresses =
        await context.read<CartProvider>().getAddresses();
    if (mounted) {
      setState(() {
        _addresses = addresses;
        _selectedAddress =
            addresses.isNotEmpty ? addresses.firstWhere(
                (a) => a.isDefault,
                orElse: () => addresses.first) : null;
        _loadingAddresses = false;
      });
    }
  }

  Future<void> _placeOrder() async {
    if (_selectedAddress == null && _addresses.isNotEmpty) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Por favor selecciona una dirección de entrega'),
          backgroundColor: NikamaColors.error,
        ),
      );
      return;
    }

    final orderProvider = context.read<OrderProvider>();
    final success = await orderProvider.checkout(
      addressSelectionType: _selectedAddress != null ? 'saved' : 'new',
      customerAddressId: _selectedAddress?.id,
      paymentMethod: _selectedPayment,
      notes: _notes,
    );

    if (!mounted) return;

    if (success) {
      // Limpiar el carrito local
      context.read<CartProvider>().clearCart();

      final order = orderProvider.lastOrder;
      final orderId = order?['id'] as String? ?? '';
      final orderNumber = order?['order_number'] as String? ?? 'NKM-????';

      Navigator.pushAndRemoveUntil(
        context,
        MaterialPageRoute(
          builder: (_) => OrderTrackingScreen(
            orderId: orderId,
            orderNumber: orderNumber,
            order: order ?? {},
          ),
        ),
        (route) => route.isFirst,
      );
    } else {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(orderProvider.checkoutError ?? 'Error al realizar el pedido'),
          backgroundColor: NikamaColors.error,
        ),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    final orderProvider = context.watch<OrderProvider>();
    final isLoading = orderProvider.checkoutStatus == OrderStatus.loading;

    return Scaffold(
      backgroundColor: NikamaColors.offWhite,
      appBar: AppBar(
        backgroundColor: NikamaColors.black,
        foregroundColor: NikamaColors.white,
        elevation: 0,
        title: const Text(
          'Checkout',
          style: TextStyle(
              fontFamily: 'Inter', fontSize: 18, fontWeight: FontWeight.w700),
        ),
        leading: GestureDetector(
          onTap: () => Navigator.pop(context),
          child: const Icon(Icons.arrow_back_ios_new, size: 18),
        ),
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(20),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // ── Dirección de entrega ──────────────────────────
            _SectionTitle(title: '📍 Dirección de Entrega'),
            const SizedBox(height: 12),

            if (_loadingAddresses)
              const Center(
                child: CircularProgressIndicator(color: NikamaColors.primary),
              )
            else if (_addresses.isEmpty)
              _NoAddressCard()
            else
              Column(
                children: _addresses.map((addr) {
                  final isSelected = _selectedAddress?.id == addr.id;
                  return GestureDetector(
                    onTap: () => setState(() => _selectedAddress = addr),
                    child: AnimatedContainer(
                      duration: const Duration(milliseconds: 200),
                      margin: const EdgeInsets.only(bottom: 10),
                      padding: const EdgeInsets.all(14),
                      decoration: BoxDecoration(
                        color: NikamaColors.surfaceWhite,
                        borderRadius: BorderRadius.circular(14),
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
                            width: 40,
                            height: 40,
                            decoration: BoxDecoration(
                              color: isSelected
                                  ? NikamaColors.primaryLight
                                  : const Color(0xFFF5F5F5),
                              borderRadius: BorderRadius.circular(10),
                            ),
                            child: Center(
                              child: Text(addr.typeIcon,
                                  style: const TextStyle(fontSize: 20)),
                            ),
                          ),
                          const SizedBox(width: 12),
                          Expanded(
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Row(
                                  children: [
                                    Text(
                                      addr.label,
                                      style: const TextStyle(
                                        fontFamily: 'Inter',
                                        fontSize: 14,
                                        fontWeight: FontWeight.w700,
                                        color: NikamaColors.black,
                                      ),
                                    ),
                                    if (addr.isDefault) ...[
                                      const SizedBox(width: 6),
                                      Container(
                                        padding: const EdgeInsets.symmetric(
                                            horizontal: 6, vertical: 2),
                                        decoration: BoxDecoration(
                                          color: NikamaColors.primaryLight,
                                          borderRadius:
                                              BorderRadius.circular(4),
                                        ),
                                        child: Text(
                                          'Default',
                                          style: TextStyle(
                                            fontFamily: 'Inter',
                                            fontSize: 9,
                                            fontWeight: FontWeight.w700,
                                            color: NikamaColors.primary,
                                          ),
                                        ),
                                      ),
                                    ],
                                  ],
                                ),
                                const SizedBox(height: 3),
                                Text(
                                  addr.address,
                                  style: const TextStyle(
                                    fontFamily: 'Inter',
                                    fontSize: 12,
                                    color: NikamaColors.mediumGray,
                                  ),
                                  maxLines: 1,
                                  overflow: TextOverflow.ellipsis,
                                ),
                                if (addr.reference != null &&
                                    addr.reference!.isNotEmpty) ...[
                                  const SizedBox(height: 2),
                                  Text(
                                    addr.reference!,
                                    style: const TextStyle(
                                      fontFamily: 'Inter',
                                      fontSize: 11,
                                      color: NikamaColors.lightGray,
                                      fontStyle: FontStyle.italic,
                                    ),
                                    maxLines: 1,
                                    overflow: TextOverflow.ellipsis,
                                  ),
                                ],
                              ],
                            ),
                          ),
                          if (isSelected)
                            Icon(Icons.check_circle_rounded,
                                color: NikamaColors.primary, size: 22),
                        ],
                      ),
                    ),
                  );
                }).toList(),
              ),

            const SizedBox(height: 24),

            // ── Método de pago ────────────────────────────────
            _SectionTitle(title: '💳 Método de Pago'),
            const SizedBox(height: 12),
            GridView.builder(
              shrinkWrap: true,
              physics: const NeverScrollableScrollPhysics(),
              gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
                crossAxisCount: 2,
                mainAxisSpacing: 10,
                crossAxisSpacing: 10,
                childAspectRatio: 2.8,
              ),
              itemCount: _paymentMethods.length,
              itemBuilder: (_, i) {
                final method = _paymentMethods[i];
                final isSelected = _selectedPayment == method.id;
                return GestureDetector(
                  onTap: () =>
                      setState(() => _selectedPayment = method.id),
                  child: AnimatedContainer(
                    duration: const Duration(milliseconds: 200),
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
                      mainAxisAlignment: MainAxisAlignment.center,
                      children: [
                        Text(method.emoji,
                            style: const TextStyle(fontSize: 18)),
                        const SizedBox(width: 8),
                        Text(
                          method.label,
                          style: TextStyle(
                            fontFamily: 'Inter',
                            fontSize: 13,
                            fontWeight: isSelected
                                ? FontWeight.w700
                                : FontWeight.w500,
                            color: NikamaColors.black,
                          ),
                        ),
                      ],
                    ),
                  ),
                );
              },
            ),

            const SizedBox(height: 24),

            // ── Instrucciones adicionales ─────────────────────
            _SectionTitle(title: '📝 Notas para el repartidor'),
            const SizedBox(height: 12),
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
                      'Ej: Timbre malogrado, llamar al llegar, dejar en portería...',
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

            // ── Resumen del pedido ────────────────────────────
            Container(
              padding: const EdgeInsets.all(16),
              decoration: BoxDecoration(
                color: NikamaColors.surfaceWhite,
                borderRadius: BorderRadius.circular(16),
                border: Border.all(color: const Color(0xFFEEEEEE)),
              ),
              child: Column(
                children: [
                  _SummaryLine(
                      label: 'Subtotal productos',
                      value:
                          'S/. ${widget.subtotal.toStringAsFixed(2)}'),
                  const SizedBox(height: 8),
                  _SummaryLine(
                      label: 'Costo de envío',
                      value:
                          'S/. ${widget.deliveryFee.toStringAsFixed(2)}'),
                  const Padding(
                    padding: EdgeInsets.symmetric(vertical: 10),
                    child: Divider(color: Color(0xFFEEEEEE)),
                  ),
                  _SummaryLine(
                    label: 'TOTAL',
                    value: 'S/. ${widget.total.toStringAsFixed(2)}',
                    isTotal: true,
                  ),
                ],
              ),
            ),

            const SizedBox(height: 28),

            // ── Botón Confirmar ───────────────────────────────
            SizedBox(
              width: double.infinity,
              child: ElevatedButton(
                onPressed: isLoading ? null : _placeOrder,
                style: ElevatedButton.styleFrom(
                  backgroundColor: NikamaColors.primary,
                  disabledBackgroundColor:
                      NikamaColors.primary.withOpacity(0.6),
                  padding: const EdgeInsets.symmetric(vertical: 18),
                  shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(16)),
                  elevation: 0,
                ),
                child: isLoading
                    ? const SizedBox(
                        width: 22,
                        height: 22,
                        child: CircularProgressIndicator(
                            strokeWidth: 2.5, color: NikamaColors.black),
                      )
                    : Column(
                        mainAxisSize: MainAxisSize.min,
                        children: [
                          const Text(
                            'Confirmar y Realizar Pedido',
                            style: TextStyle(
                              fontFamily: 'Inter',
                              fontSize: 16,
                              fontWeight: FontWeight.w800,
                              color: NikamaColors.black,
                            ),
                          ),
                          Text(
                            'S/. ${widget.total.toStringAsFixed(2)} · $_selectedPayment',
                            style: const TextStyle(
                              fontFamily: 'Inter',
                              fontSize: 12,
                              color: NikamaColors.darkGray,
                            ),
                          ),
                        ],
                      ),
              ),
            ),
            const SizedBox(height: 20),
          ],
        ),
      ),
    );
  }
}

// ── Widgets auxiliares ────────────────────────────────────────
class _SectionTitle extends StatelessWidget {
  final String title;
  const _SectionTitle({required this.title});

  @override
  Widget build(BuildContext context) {
    return Text(
      title,
      style: const TextStyle(
        fontFamily: 'Inter',
        fontSize: 16,
        fontWeight: FontWeight.w700,
        color: NikamaColors.black,
      ),
    );
  }
}

class _SummaryLine extends StatelessWidget {
  final String label;
  final String value;
  final bool isTotal;

  const _SummaryLine({
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
            fontSize: isTotal ? 15 : 13,
            fontWeight: isTotal ? FontWeight.w700 : FontWeight.w400,
            color:
                isTotal ? NikamaColors.black : NikamaColors.mediumGray,
          ),
        ),
        Text(
          value,
          style: TextStyle(
            fontFamily: 'Inter',
            fontSize: isTotal ? 17 : 13,
            fontWeight: isTotal ? FontWeight.w800 : FontWeight.w600,
            color:
                isTotal ? NikamaColors.primary : NikamaColors.black,
          ),
        ),
      ],
    );
  }
}

class _NoAddressCard extends StatelessWidget {
  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: NikamaColors.surfaceWhite,
        borderRadius: BorderRadius.circular(14),
        border: Border.all(color: const Color(0xFFE0E0E0)),
      ),
      child: const Row(
        children: [
          Icon(Icons.info_outline, color: NikamaColors.mediumGray),
          SizedBox(width: 12),
          Expanded(
            child: Text(
              'No tienes direcciones guardadas. Se utilizará tu ubicación actual.',
              style: TextStyle(
                fontFamily: 'Inter',
                fontSize: 13,
                color: NikamaColors.mediumGray,
              ),
            ),
          ),
        ],
      ),
    );
  }
}

class _PaymentMethod {
  final String id;
  final String label;
  final String emoji;
  _PaymentMethod({required this.id, required this.label, required this.emoji});
}
