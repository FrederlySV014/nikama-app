import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../core/constants/app_theme.dart';
import '../../data/providers/order_provider.dart';
import 'rate_order_screen.dart';

// ============================================================
// NIKAMA — Pantalla de Rastreo del Pedido en Vivo
// Paso 5: Mapa + barra de progreso + datos del conductor
// ============================================================

class OrderTrackingScreen extends StatefulWidget {
  final String orderId;
  final String orderNumber;
  final Map<String, dynamic> order;

  const OrderTrackingScreen({
    super.key,
    required this.orderId,
    required this.orderNumber,
    required this.order,
  });

  @override
  State<OrderTrackingScreen> createState() => _OrderTrackingScreenState();
}

class _OrderTrackingScreenState extends State<OrderTrackingScreen>
    with TickerProviderStateMixin {
  late AnimationController _pulseController;
  late Animation<double> _pulseAnimation;

  final List<_TrackStep> _steps = const [
    _TrackStep(label: 'Confirmado', icon: Icons.check_circle_outline),
    _TrackStep(label: 'Preparando', icon: Icons.restaurant_outlined),
    _TrackStep(label: 'En camino', icon: Icons.delivery_dining_outlined),
    _TrackStep(label: 'Entregado', icon: Icons.home_rounded),
  ];

  @override
  void initState() {
    super.initState();

    _pulseController = AnimationController(
      vsync: this,
      duration: const Duration(seconds: 2),
    )..repeat(reverse: true);

    _pulseAnimation = Tween<double>(begin: 0.85, end: 1.15).animate(
      CurvedAnimation(parent: _pulseController, curve: Curves.easeInOut),
    );

    // Iniciar tracking
    WidgetsBinding.instance.addPostFrameCallback((_) {
      if (widget.orderId.isNotEmpty) {
        context.read<OrderProvider>().startTracking(widget.orderId);
      }
    });
  }

  @override
  void dispose() {
    _pulseController.dispose();
    context.read<OrderProvider>().stopTracking();
    super.dispose();
  }

  int _statusToStep(String? status) {
    switch (status) {
      case 'pending':
        return 0;
      case 'confirmed':
        return 0;
      case 'preparing':
        return 1;
      case 'ready_for_pickup':
        return 1;
      case 'out_for_delivery':
        return 2;
      case 'delivered':
        return 3;
      default:
        return 0;
    }
  }

  String _statusLabel(String? status) {
    switch (status) {
      case 'pending':
        return 'Pendiente de confirmación';
      case 'confirmed':
        return 'Pedido confirmado ✅';
      case 'preparing':
        return 'El negocio está preparando tu pedido 🍳';
      case 'ready_for_pickup':
        return 'Listo para recojo — buscando repartidor 🛵';
      case 'out_for_delivery':
        return 'Tu repartidor está en camino 🚀';
      case 'delivered':
        return '¡Tu pedido llegó! ¡Buen provecho! 🎉';
      default:
        return 'Procesando...';
    }
  }

  @override
  Widget build(BuildContext context) {
    final orderProvider = context.watch<OrderProvider>();
    final trackData = orderProvider.trackData;
    final trackStatus = trackData?['status'] as String? ??
        widget.order['status'] as String? ??
        'pending';
    final driver = trackData?['driver'] as Map<String, dynamic>?;
    final liveLocation =
        trackData?['live_location'] as Map<String, dynamic>?;
    final stepIndex = _statusToStep(trackStatus);
    final isDelivered = trackStatus == 'delivered';

    return Scaffold(
      backgroundColor: NikamaColors.offWhite,
      appBar: AppBar(
        backgroundColor: NikamaColors.black,
        foregroundColor: NikamaColors.white,
        elevation: 0,
        title: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(
              'Pedido ${widget.orderNumber}',
              style: const TextStyle(
                  fontFamily: 'Inter',
                  fontSize: 16,
                  fontWeight: FontWeight.w700),
            ),
            Text(
              _statusLabel(trackStatus),
              style: const TextStyle(
                  fontFamily: 'Inter',
                  fontSize: 11,
                  color: NikamaColors.lightGray),
            ),
          ],
        ),
        leading: isDelivered
            ? null
            : GestureDetector(
                onTap: () => Navigator.pop(context),
                child: const Icon(Icons.arrow_back_ios_new, size: 18),
              ),
      ),
      body: Column(
        children: [
          // ── Mapa interactivo animado ───────────────────────
          Expanded(
            flex: 5,
            child: _AnimatedMapWidget(
              pulseAnimation: _pulseAnimation,
              driver: driver,
              liveLocation: liveLocation,
              stepIndex: stepIndex,
            ),
          ),

          // ── Panel inferior ─────────────────────────────────
          Expanded(
            flex: 5,
            child: Container(
              padding: const EdgeInsets.all(20),
              decoration: const BoxDecoration(
                color: NikamaColors.surfaceWhite,
                borderRadius:
                    BorderRadius.vertical(top: Radius.circular(28)),
              ),
              child: SingleChildScrollView(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    // Barra de progreso de estado
                    _ProgressStepBar(
                        steps: _steps, currentStep: stepIndex),

                    const SizedBox(height: 20),
                    const Divider(color: Color(0xFFEEEEEE)),
                    const SizedBox(height: 16),

                    // Datos del repartidor
                    if (driver != null) ...[
                      const Text(
                        'Tu Repartidor',
                        style: TextStyle(
                          fontFamily: 'Inter',
                          fontSize: 15,
                          fontWeight: FontWeight.w700,
                          color: NikamaColors.black,
                        ),
                      ),
                      const SizedBox(height: 12),
                      _DriverCard(driver: driver),
                      const SizedBox(height: 16),
                    ] else if (stepIndex >= 2) ...[
                      const Text(
                        'Buscando repartidor...',
                        style: TextStyle(
                          fontFamily: 'Inter',
                          fontSize: 14,
                          color: NikamaColors.mediumGray,
                        ),
                      ),
                      const SizedBox(height: 16),
                    ],

                    // Detalles del pedido
                    _OrderDetailCard(order: widget.order),

                    const SizedBox(height: 20),

                    // Botón al finalizar
                    if (isDelivered)
                      SizedBox(
                        width: double.infinity,
                        child: ElevatedButton(
                          onPressed: () {
                            Navigator.pushReplacement(
                              context,
                              MaterialPageRoute(
                                builder: (_) => RateOrderScreen(
                                  orderId: widget.orderId,
                                  orderNumber: widget.orderNumber,
                                  order: widget.order,
                                  driver: driver,
                                ),
                              ),
                            );
                          },
                          style: ElevatedButton.styleFrom(
                            backgroundColor: NikamaColors.primary,
                            padding:
                                const EdgeInsets.symmetric(vertical: 16),
                            shape: RoundedRectangleBorder(
                                borderRadius: BorderRadius.circular(14)),
                          ),
                          child: const Text(
                            '⭐ Calificar mi pedido',
                            style: TextStyle(
                              fontFamily: 'Inter',
                              fontSize: 15,
                              fontWeight: FontWeight.w800,
                              color: NikamaColors.black,
                            ),
                          ),
                        ),
                      ),
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

// ── Mapa Animado ─────────────────────────────────────────────
class _AnimatedMapWidget extends StatelessWidget {
  final Animation<double> pulseAnimation;
  final Map<String, dynamic>? driver;
  final Map<String, dynamic>? liveLocation;
  final int stepIndex;

  const _AnimatedMapWidget({
    required this.pulseAnimation,
    required this.driver,
    required this.liveLocation,
    required this.stepIndex,
  });

  @override
  Widget build(BuildContext context) {
    return Container(
      width: double.infinity,
      decoration: const BoxDecoration(
        gradient: LinearGradient(
          colors: [Color(0xFF1A1A2E), Color(0xFF16213E), Color(0xFF0F3460)],
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
        ),
      ),
      child: Stack(
        children: [
          // Cuadrícula decorativa de mapa
          CustomPaint(
            size: Size.infinite,
            painter: _MapGridPainter(),
          ),

          // Punto de entrega (cliente)
          Positioned(
            bottom: 60,
            left: MediaQuery.of(context).size.width * 0.5 - 20,
            child: Column(
              children: [
                Container(
                  width: 40,
                  height: 40,
                  decoration: BoxDecoration(
                    color: const Color(0xFF2196F3),
                    borderRadius: BorderRadius.circular(10),
                    boxShadow: [
                      BoxShadow(
                        color: const Color(0xFF2196F3).withOpacity(0.4),
                        blurRadius: 12,
                        spreadRadius: 3,
                      ),
                    ],
                  ),
                  child: const Icon(Icons.home_rounded,
                      color: Colors.white, size: 22),
                ),
                const SizedBox(height: 4),
                const Text('Tu casa',
                    style: TextStyle(
                        color: Colors.white70, fontSize: 10, fontFamily: 'Inter')),
              ],
            ),
          ),

          // Repartidor animado (solo si está en camino)
          if (stepIndex >= 2)
            Positioned(
              top: 80,
              left: MediaQuery.of(context).size.width * 0.3,
              child: AnimatedBuilder(
                animation: pulseAnimation,
                builder: (_, child) => Transform.scale(
                  scale: pulseAnimation.value,
                  child: child,
                ),
                child: Column(
                  children: [
                    Container(
                      width: 48,
                      height: 48,
                      decoration: BoxDecoration(
                        color: NikamaColors.primary,
                        borderRadius: BorderRadius.circular(12),
                        boxShadow: [
                          BoxShadow(
                            color: NikamaColors.primary.withOpacity(0.5),
                            blurRadius: 16,
                            spreadRadius: 4,
                          ),
                        ],
                      ),
                      child: const Icon(Icons.delivery_dining_rounded,
                          color: NikamaColors.black, size: 28),
                    ),
                    const SizedBox(height: 4),
                    Container(
                      padding: const EdgeInsets.symmetric(
                          horizontal: 6, vertical: 2),
                      decoration: BoxDecoration(
                        color: NikamaColors.primary,
                        borderRadius: BorderRadius.circular(6),
                      ),
                      child: Text(
                        driver?['first_name'] as String? ?? 'Repartidor',
                        style: const TextStyle(
                          color: NikamaColors.black,
                          fontSize: 9,
                          fontWeight: FontWeight.w700,
                          fontFamily: 'Inter',
                        ),
                      ),
                    ),
                  ],
                ),
              ),
            ),

          // Línea de ruta
          if (stepIndex >= 2)
            Positioned.fill(
              child: CustomPaint(
                painter: _RoutePainter(),
              ),
            ),

          // Coordenadas si hay GPS real
          if (liveLocation != null)
            Positioned(
              top: 12,
              right: 12,
              child: Container(
                padding:
                    const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                decoration: BoxDecoration(
                  color: Colors.black.withOpacity(0.6),
                  borderRadius: BorderRadius.circular(8),
                ),
                child: Text(
                  '📡 ${(liveLocation!['latitude'] as num?)?.toStringAsFixed(4) ?? '---'}, ${(liveLocation!['longitude'] as num?)?.toStringAsFixed(4) ?? '---'}',
                  style: const TextStyle(
                      color: Colors.white70,
                      fontSize: 9,
                      fontFamily: 'Inter'),
                ),
              ),
            ),
        ],
      ),
    );
  }
}

// ── Painter: Cuadrícula de mapa ───────────────────────────────
class _MapGridPainter extends CustomPainter {
  @override
  void paint(Canvas canvas, Size size) {
    final paint = Paint()
      ..color = Colors.white.withOpacity(0.05)
      ..strokeWidth = 1;

    for (double x = 0; x < size.width; x += 40) {
      canvas.drawLine(Offset(x, 0), Offset(x, size.height), paint);
    }
    for (double y = 0; y < size.height; y += 40) {
      canvas.drawLine(Offset(0, y), Offset(size.width, y), paint);
    }
  }

  @override
  bool shouldRepaint(_) => false;
}

// ── Painter: Línea de ruta ────────────────────────────────────
class _RoutePainter extends CustomPainter {
  @override
  void paint(Canvas canvas, Size size) {
    final paint = Paint()
      ..color = const Color(0xFFF5C518).withOpacity(0.6)
      ..strokeWidth = 3
      ..strokeCap = StrokeCap.round
      ..style = PaintingStyle.stroke;

    final path = Path()
      ..moveTo(size.width * 0.35, 100)
      ..quadraticBezierTo(
        size.width * 0.5, size.height * 0.4,
        size.width * 0.5, size.height - 100,
      );

    canvas.drawPath(path, paint);
  }

  @override
  bool shouldRepaint(_) => false;
}

// ── Barra de progreso de pasos ────────────────────────────────
class _ProgressStepBar extends StatelessWidget {
  final List<_TrackStep> steps;
  final int currentStep;

  const _ProgressStepBar({required this.steps, required this.currentStep});

  @override
  Widget build(BuildContext context) {
    return Row(
      children: List.generate(steps.length * 2 - 1, (i) {
        if (i.isOdd) {
          // Línea entre pasos
          final stepIdx = i ~/ 2;
          return Expanded(
            child: Container(
              height: 3,
              decoration: BoxDecoration(
                color: stepIdx < currentStep
                    ? NikamaColors.primary
                    : const Color(0xFFEEEEEE),
                borderRadius: BorderRadius.circular(2),
              ),
            ),
          );
        } else {
          // Círculo del paso
          final stepIdx = i ~/ 2;
          final isDone = stepIdx <= currentStep;
          final isCurrent = stepIdx == currentStep;
          return Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              AnimatedContainer(
                duration: const Duration(milliseconds: 300),
                width: isCurrent ? 36 : 30,
                height: isCurrent ? 36 : 30,
                decoration: BoxDecoration(
                  color: isDone ? NikamaColors.primary : const Color(0xFFEEEEEE),
                  borderRadius: BorderRadius.circular(isCurrent ? 12 : 10),
                  boxShadow: isCurrent
                      ? [
                          BoxShadow(
                            color: NikamaColors.primary.withOpacity(0.4),
                            blurRadius: 10,
                            spreadRadius: 2,
                          )
                        ]
                      : null,
                ),
                child: Icon(
                  steps[stepIdx].icon,
                  size: isCurrent ? 18 : 15,
                  color: isDone ? NikamaColors.black : NikamaColors.lightGray,
                ),
              ),
              const SizedBox(height: 6),
              Text(
                steps[stepIdx].label,
                style: TextStyle(
                  fontFamily: 'Inter',
                  fontSize: 9,
                  fontWeight:
                      isCurrent ? FontWeight.w700 : FontWeight.w400,
                  color: isDone ? NikamaColors.black : NikamaColors.lightGray,
                ),
              ),
            ],
          );
        }
      }),
    );
  }
}

// ── Tarjeta del conductor ─────────────────────────────────────
class _DriverCard extends StatelessWidget {
  final Map<String, dynamic> driver;
  const _DriverCard({required this.driver});

  @override
  Widget build(BuildContext context) {
    final firstName = driver['first_name'] as String? ?? 'Conductor';
    final lastName = driver['last_name'] as String? ?? '';
    final vehicleType = driver['vehicle_type'] as String? ?? '';
    final vehiclePlate = driver['vehicle_plate'] as String? ?? '';

    return Container(
      padding: const EdgeInsets.all(14),
      decoration: BoxDecoration(
        color: NikamaColors.offWhite,
        borderRadius: BorderRadius.circular(14),
        border: Border.all(color: const Color(0xFFEEEEEE)),
      ),
      child: Row(
        children: [
          Container(
            width: 48,
            height: 48,
            decoration: BoxDecoration(
              color: NikamaColors.primary,
              borderRadius: BorderRadius.circular(12),
            ),
            child: Center(
              child: Text(
                firstName.isNotEmpty ? firstName[0].toUpperCase() : 'R',
                style: const TextStyle(
                  fontFamily: 'Inter',
                  fontSize: 22,
                  fontWeight: FontWeight.w800,
                  color: NikamaColors.black,
                ),
              ),
            ),
          ),
          const SizedBox(width: 12),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  '$firstName $lastName',
                  style: const TextStyle(
                    fontFamily: 'Inter',
                    fontSize: 14,
                    fontWeight: FontWeight.w700,
                    color: NikamaColors.black,
                  ),
                ),
                const SizedBox(height: 3),
                Text(
                  '$vehicleType · $vehiclePlate',
                  style: const TextStyle(
                    fontFamily: 'Inter',
                    fontSize: 12,
                    color: NikamaColors.mediumGray,
                  ),
                ),
              ],
            ),
          ),
          GestureDetector(
            onTap: () {},
            child: Container(
              width: 40,
              height: 40,
              decoration: BoxDecoration(
                color: NikamaColors.primaryLight,
                borderRadius: BorderRadius.circular(10),
              ),
              child: const Icon(Icons.phone_rounded,
                  color: NikamaColors.black, size: 20),
            ),
          ),
        ],
      ),
    );
  }
}

// ── Detalles del pedido ───────────────────────────────────────
class _OrderDetailCard extends StatelessWidget {
  final Map<String, dynamic> order;
  const _OrderDetailCard({required this.order});

  @override
  Widget build(BuildContext context) {
    final total = order['total']?.toString() ?? '--';
    final address = order['delivery_address'] as String? ?? '';
    final payment = (order['payment'] as Map<String, dynamic>?)?['payment_method']
            as String? ??
        order['payment_method'] as String? ??
        '--';

    return Container(
      padding: const EdgeInsets.all(14),
      decoration: BoxDecoration(
        color: NikamaColors.offWhite,
        borderRadius: BorderRadius.circular(14),
        border: Border.all(color: const Color(0xFFEEEEEE)),
      ),
      child: Column(
        children: [
          _DetailRow(label: '💰 Total', value: 'S/. $total'),
          if (address.isNotEmpty) ...[
            const SizedBox(height: 8),
            _DetailRow(label: '📍 Entrega en', value: address),
          ],
          const SizedBox(height: 8),
          _DetailRow(label: '💳 Pago', value: payment.toUpperCase()),
        ],
      ),
    );
  }
}

class _DetailRow extends StatelessWidget {
  final String label;
  final String value;
  const _DetailRow({required this.label, required this.value});

  @override
  Widget build(BuildContext context) {
    return Row(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(
          label,
          style: const TextStyle(
            fontFamily: 'Inter',
            fontSize: 12,
            color: NikamaColors.mediumGray,
          ),
        ),
        const Spacer(),
        Flexible(
          child: Text(
            value,
            textAlign: TextAlign.right,
            style: const TextStyle(
              fontFamily: 'Inter',
              fontSize: 12,
              fontWeight: FontWeight.w600,
              color: NikamaColors.black,
            ),
          ),
        ),
      ],
    );
  }
}

// ── Modelo de paso de tracking ────────────────────────────────
class _TrackStep {
  final String label;
  final IconData icon;
  const _TrackStep({required this.label, required this.icon});
}
