import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../core/constants/app_theme.dart';
import '../../data/providers/order_provider.dart';

// ============================================================
// NIKAMA — Pantalla de Calificación del Pedido
// Paso 6: El cliente califica al repartidor y los productos
// ============================================================

class RateOrderScreen extends StatefulWidget {
  final String orderId;
  final String orderNumber;
  final Map<String, dynamic> order;
  final Map<String, dynamic>? driver;

  const RateOrderScreen({
    super.key,
    required this.orderId,
    required this.orderNumber,
    required this.order,
    this.driver,
  });

  @override
  State<RateOrderScreen> createState() => _RateOrderScreenState();
}

class _RateOrderScreenState extends State<RateOrderScreen>
    with SingleTickerProviderStateMixin {
  // Calificaciones
  int _driverRating = 0;
  int _productRating = 0;
  String _driverComment = '';
  String _productComment = '';

  bool _isSubmitting = false;
  bool _submitted = false;

  late AnimationController _celebrationController;
  late Animation<double> _celebrationAnimation;

  @override
  void initState() {
    super.initState();
    _celebrationController = AnimationController(
      vsync: this,
      duration: const Duration(milliseconds: 600),
    );
    _celebrationAnimation = CurvedAnimation(
      parent: _celebrationController,
      curve: Curves.elasticOut,
    );
  }

  @override
  void dispose() {
    _celebrationController.dispose();
    super.dispose();
  }

  Future<void> _submitRatings() async {
    if (_driverRating == 0 || _productRating == 0) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Por favor califica tanto al repartidor como al pedido'),
          backgroundColor: NikamaColors.error,
        ),
      );
      return;
    }

    setState(() => _isSubmitting = true);

    final orderProvider = context.read<OrderProvider>();

    // Calificar al repartidor
    await orderProvider.rateDriver(
      orderId: widget.orderId,
      rating: _driverRating,
      comment: _driverComment,
    );

    // Calificar los productos del pedido
    final items = widget.order['items'] as List<dynamic>? ?? [];
    for (final item in items) {
      final productId = (item as Map<String, dynamic>)['product_id'] as String?;
      if (productId != null) {
        await orderProvider.rateProduct(
          productId: productId,
          rating: _productRating,
          comment: _productComment,
        );
      }
    }

    if (mounted) {
      setState(() {
        _isSubmitting = false;
        _submitted = true;
      });
      _celebrationController.forward();
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: NikamaColors.offWhite,
      body: _submitted ? _SuccessView(animation: _celebrationAnimation) : _RatingForm(
        order: widget.order,
        driver: widget.driver,
        orderNumber: widget.orderNumber,
        driverRating: _driverRating,
        productRating: _productRating,
        isSubmitting: _isSubmitting,
        onDriverRating: (r) => setState(() => _driverRating = r),
        onProductRating: (r) => setState(() => _productRating = r),
        onDriverComment: (c) => _driverComment = c,
        onProductComment: (c) => _productComment = c,
        onSubmit: _submitRatings,
      ),
    );
  }
}

// ── Vista de éxito ────────────────────────────────────────────
class _SuccessView extends StatelessWidget {
  final Animation<double> animation;
  const _SuccessView({required this.animation});

  @override
  Widget build(BuildContext context) {
    return Center(
      child: Padding(
        padding: const EdgeInsets.all(32),
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            ScaleTransition(
              scale: animation,
              child: Container(
                width: 100,
                height: 100,
                decoration: BoxDecoration(
                  color: NikamaColors.primary,
                  borderRadius: BorderRadius.circular(28),
                  boxShadow: [
                    BoxShadow(
                      color: NikamaColors.primary.withOpacity(0.4),
                      blurRadius: 30,
                      spreadRadius: 5,
                    ),
                  ],
                ),
                child: const Center(
                  child: Text('🎉', style: TextStyle(fontSize: 50)),
                ),
              ),
            ),
            const SizedBox(height: 28),
            const Text(
              '¡Gracias por tu calificación!',
              textAlign: TextAlign.center,
              style: TextStyle(
                fontFamily: 'Inter',
                fontSize: 24,
                fontWeight: FontWeight.w800,
                color: NikamaColors.black,
              ),
            ),
            const SizedBox(height: 12),
            const Text(
              'Tu opinión ayuda a mejorar el servicio\npara toda la comunidad Nikama.',
              textAlign: TextAlign.center,
              style: TextStyle(
                fontFamily: 'Inter',
                fontSize: 15,
                color: NikamaColors.mediumGray,
                height: 1.6,
              ),
            ),
            const SizedBox(height: 36),
            ElevatedButton(
              onPressed: () {
                Navigator.popUntil(context, (r) => r.isFirst);
              },
              style: ElevatedButton.styleFrom(
                backgroundColor: NikamaColors.primary,
                padding:
                    const EdgeInsets.symmetric(horizontal: 40, vertical: 16),
                shape: RoundedRectangleBorder(
                    borderRadius: BorderRadius.circular(14)),
              ),
              child: const Text(
                'Volver al inicio',
                style: TextStyle(
                  fontFamily: 'Inter',
                  fontSize: 15,
                  fontWeight: FontWeight.w700,
                  color: NikamaColors.black,
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }
}

// ── Formulario de calificación ────────────────────────────────
class _RatingForm extends StatelessWidget {
  final Map<String, dynamic> order;
  final Map<String, dynamic>? driver;
  final String orderNumber;
  final int driverRating;
  final int productRating;
  final bool isSubmitting;
  final ValueChanged<int> onDriverRating;
  final ValueChanged<int> onProductRating;
  final ValueChanged<String> onDriverComment;
  final ValueChanged<String> onProductComment;
  final VoidCallback onSubmit;

  const _RatingForm({
    required this.order,
    required this.driver,
    required this.orderNumber,
    required this.driverRating,
    required this.productRating,
    required this.isSubmitting,
    required this.onDriverRating,
    required this.onProductRating,
    required this.onDriverComment,
    required this.onProductComment,
    required this.onSubmit,
  });

  @override
  Widget build(BuildContext context) {
    final driverName = driver != null
        ? '${driver!['first_name'] ?? ''} ${driver!['last_name'] ?? ''}'.trim()
        : null;

    return SafeArea(
      child: SingleChildScrollView(
        child: Padding(
          padding: const EdgeInsets.all(24),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.center,
            children: [
              // Cabecera celebración
              const SizedBox(height: 16),
              const Text('🎉', style: TextStyle(fontSize: 52)),
              const SizedBox(height: 12),
              const Text(
                '¡Tu pedido llegó!',
                style: TextStyle(
                  fontFamily: 'Inter',
                  fontSize: 26,
                  fontWeight: FontWeight.w800,
                  color: NikamaColors.black,
                ),
              ),
              const SizedBox(height: 6),
              Text(
                'Pedido $orderNumber · ¡Buen provecho! 🍽️',
                style: const TextStyle(
                  fontFamily: 'Inter',
                  fontSize: 13,
                  color: NikamaColors.mediumGray,
                ),
              ),

              const SizedBox(height: 32),

              // ── Calificar al repartidor ────────────────────
              _RatingCard(
                title: '🛵 Califica al repartidor',
                subtitle: driverName ?? 'Tu repartidor',
                hint: 'Rapidez, amabilidad, presentación...',
                rating: driverRating,
                onRating: onDriverRating,
                onComment: onDriverComment,
              ),

              const SizedBox(height: 16),

              // ── Calificar el pedido ────────────────────────
              _RatingCard(
                title: '🍔 Califica tu pedido',
                subtitle: 'Calidad y presentación de los productos',
                hint: 'Sabor, temperatura, presentación...',
                rating: productRating,
                onRating: onProductRating,
                onComment: onProductComment,
              ),

              const SizedBox(height: 32),

              // ── Botón de enviar ───────────────────────────
              SizedBox(
                width: double.infinity,
                child: ElevatedButton(
                  onPressed: isSubmitting ? null : onSubmit,
                  style: ElevatedButton.styleFrom(
                    backgroundColor: NikamaColors.primary,
                    disabledBackgroundColor:
                        NikamaColors.primary.withOpacity(0.5),
                    padding: const EdgeInsets.symmetric(vertical: 18),
                    shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(14)),
                  ),
                  child: isSubmitting
                      ? const SizedBox(
                          width: 22,
                          height: 22,
                          child: CircularProgressIndicator(
                              strokeWidth: 2.5, color: NikamaColors.black),
                        )
                      : const Text(
                          'Enviar Calificación ⭐',
                          style: TextStyle(
                            fontFamily: 'Inter',
                            fontSize: 16,
                            fontWeight: FontWeight.w800,
                            color: NikamaColors.black,
                          ),
                        ),
                ),
              ),

              const SizedBox(height: 12),
              TextButton(
                onPressed: () =>
                    Navigator.popUntil(context, (r) => r.isFirst),
                child: Text(
                  'Omitir por ahora',
                  style: TextStyle(
                    fontFamily: 'Inter',
                    fontSize: 13,
                    color: NikamaColors.mediumGray,
                  ),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}

// ── Tarjeta de calificación con estrellas ─────────────────────
class _RatingCard extends StatelessWidget {
  final String title;
  final String subtitle;
  final String hint;
  final int rating;
  final ValueChanged<int> onRating;
  final ValueChanged<String> onComment;

  const _RatingCard({
    required this.title,
    required this.subtitle,
    required this.hint,
    required this.rating,
    required this.onRating,
    required this.onComment,
  });

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        color: NikamaColors.surfaceWhite,
        borderRadius: BorderRadius.circular(18),
        boxShadow: [
          BoxShadow(
            color: NikamaColors.black.withOpacity(0.06),
            blurRadius: 16,
            offset: const Offset(0, 4),
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(
            title,
            style: const TextStyle(
              fontFamily: 'Inter',
              fontSize: 16,
              fontWeight: FontWeight.w700,
              color: NikamaColors.black,
            ),
          ),
          const SizedBox(height: 4),
          Text(
            subtitle,
            style: const TextStyle(
              fontFamily: 'Inter',
              fontSize: 12,
              color: NikamaColors.mediumGray,
            ),
          ),
          const SizedBox(height: 16),

          // Estrellas
          Row(
            mainAxisAlignment: MainAxisAlignment.center,
            children: List.generate(5, (i) {
              final starIndex = i + 1;
              return GestureDetector(
                onTap: () => onRating(starIndex),
                child: AnimatedContainer(
                  duration: const Duration(milliseconds: 150),
                  padding: const EdgeInsets.symmetric(horizontal: 6),
                  child: Icon(
                    starIndex <= rating
                        ? Icons.star_rounded
                        : Icons.star_border_rounded,
                    size: starIndex <= rating ? 44 : 38,
                    color: starIndex <= rating
                        ? NikamaColors.primary
                        : NikamaColors.lightGray,
                  ),
                ),
              );
            }),
          ),

          if (rating > 0) ...[
            const SizedBox(height: 4),
            Center(
              child: Text(
                _ratingLabel(rating),
                style: TextStyle(
                  fontFamily: 'Inter',
                  fontSize: 13,
                  fontWeight: FontWeight.w600,
                  color: NikamaColors.primary,
                ),
              ),
            ),
          ],

          const SizedBox(height: 14),

          // Comentario
          Container(
            decoration: BoxDecoration(
              color: NikamaColors.offWhite,
              borderRadius: BorderRadius.circular(12),
              border: Border.all(color: const Color(0xFFE0E0E0)),
            ),
            child: TextField(
              onChanged: onComment,
              maxLines: 2,
              style: const TextStyle(
                  fontFamily: 'Inter', fontSize: 13, color: NikamaColors.black),
              decoration: InputDecoration(
                hintText: hint,
                hintStyle: const TextStyle(
                    fontFamily: 'Inter',
                    fontSize: 12,
                    color: NikamaColors.lightGray),
                border: InputBorder.none,
                contentPadding: const EdgeInsets.all(12),
              ),
            ),
          ),
        ],
      ),
    );
  }

  String _ratingLabel(int r) {
    switch (r) {
      case 1:
        return 'Muy malo 😞';
      case 2:
        return 'Malo 😕';
      case 3:
        return 'Regular 😐';
      case 4:
        return 'Bueno 😊';
      case 5:
        return '¡Excelente! 🤩';
      default:
        return '';
    }
  }
}
