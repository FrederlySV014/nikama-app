import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../data/providers/auth_provider.dart';
import '../../core/constants/app_theme.dart';
import '../screens/login_screen.dart';

// ============================================================
// NIKAMA — Home Screen del DRIVER (Repartidor)
// Dashboard: asignaciones activas, estado online, ubicación GPS
// ============================================================

import '../../data/providers/driver_provider.dart';

class DriverHomeScreen extends StatefulWidget {
  const DriverHomeScreen({super.key});

  @override
  State<DriverHomeScreen> createState() => _DriverHomeScreenState();
}

class _DriverHomeScreenState extends State<DriverHomeScreen> {
  bool _isOnline = false;
  int _currentIndex = 0;

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      context.read<DriverProvider>().loadDashboard();
    });
  }

  @override
  Widget build(BuildContext context) {
    final user = context.watch<AuthProvider>().user;
    final driverData = context.watch<DriverProvider>();

    return Scaffold(
      backgroundColor: NikamaColors.offWhite,
      body: driverData.isLoading && driverData.assignments.isEmpty
          ? const Center(child: CircularProgressIndicator(color: NikamaColors.primary))
          : IndexedStack(
              index: _currentIndex,
              children: [
                _DashboardTab(
                  isOnline: _isOnline,
                  onToggleOnline: () => setState(() => _isOnline = !_isOnline),
                  driverName: user?.firstName ?? 'Repartidor',
                  stats: driverData.profileStats,
                ),
                const _AssignmentsTab(),
                const _EarningsTab(),
                _DriverProfileTab(user: user),
              ],
            ),
      bottomNavigationBar: BottomNavigationBar(
        currentIndex: _currentIndex,
        onTap: (i) => setState(() => _currentIndex = i),
        type: BottomNavigationBarType.fixed,
        backgroundColor: NikamaColors.surfaceWhite,
        selectedItemColor: NikamaColors.primary,
        unselectedItemColor: NikamaColors.lightGray,
        selectedLabelStyle: const TextStyle(
            fontFamily: 'Inter', fontWeight: FontWeight.w700, fontSize: 11),
        unselectedLabelStyle:
            const TextStyle(fontFamily: 'Inter', fontSize: 11),
        items: [
          const BottomNavigationBarItem(
              icon: Icon(Icons.dashboard_outlined),
              activeIcon: Icon(Icons.dashboard_rounded),
              label: 'Dashboard'),
          BottomNavigationBarItem(
              icon: Badge(
                label: Text(driverData.assignments.length.toString()),
                isLabelVisible: driverData.assignments.isNotEmpty,
                child: const Icon(Icons.delivery_dining_outlined),
              ),
              activeIcon: const Icon(Icons.delivery_dining_rounded),
              label: 'Asignaciones'),
          const BottomNavigationBarItem(
              icon: Icon(Icons.payments_outlined),
              activeIcon: Icon(Icons.payments_rounded),
              label: 'Ganancias'),
          const BottomNavigationBarItem(
              icon: Icon(Icons.person_outline_rounded),
              activeIcon: Icon(Icons.person_rounded),
              label: 'Perfil'),
        ],
      ),
    );
  }
}

class _DashboardTab extends StatelessWidget {
  final bool isOnline;
  final VoidCallback onToggleOnline;
  final String driverName;
  final Map<String, dynamic>? stats;

  const _DashboardTab({
    required this.isOnline,
    required this.onToggleOnline,
    required this.driverName,
    this.stats,
  });

  @override
  Widget build(BuildContext context) {
    return SafeArea(
      child: SingleChildScrollView(
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // HEADER
            Container(
              padding: const EdgeInsets.fromLTRB(20, 20, 20, 28),
              decoration: const BoxDecoration(
                gradient: NikamaColors.heroGradient,
                borderRadius: BorderRadius.only(
                  bottomLeft: Radius.circular(32),
                  bottomRight: Radius.circular(32),
                ),
              ),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text('Hola, $driverName 🏍️',
                              style: const TextStyle(
                                fontFamily: 'Inter',
                                fontSize: 22,
                                fontWeight: FontWeight.w800,
                                color: NikamaColors.white,
                              )),
                          const SizedBox(height: 4),
                          const Text('Panel del Repartidor',
                              style: TextStyle(
                                  fontFamily: 'Inter',
                                  fontSize: 13,
                                  color: NikamaColors.lightGray)),
                        ],
                      ),
                      // Toggle Online
                      GestureDetector(
                        onTap: onToggleOnline,
                        child: AnimatedContainer(
                          duration: const Duration(milliseconds: 300),
                          width: 80,
                          height: 40,
                          decoration: BoxDecoration(
                            color: isOnline
                                ? NikamaColors.primary
                                : const Color(0xFF2A2A2A),
                            borderRadius: BorderRadius.circular(20),
                          ),
                          child: Row(
                            mainAxisAlignment: isOnline
                                ? MainAxisAlignment.end
                                : MainAxisAlignment.start,
                            children: [
                              Padding(
                                padding: const EdgeInsets.all(4),
                                child: Container(
                                  width: 32,
                                  height: 32,
                                  decoration: BoxDecoration(
                                    color: isOnline
                                        ? NikamaColors.black
                                        : NikamaColors.lightGray,
                                    borderRadius: BorderRadius.circular(16),
                                  ),
                                  child: Icon(
                                    isOnline
                                        ? Icons.check_rounded
                                        : Icons.power_settings_new,
                                    color: isOnline
                                        ? NikamaColors.primary
                                        : NikamaColors.darkGray,
                                    size: 18,
                                  ),
                                ),
                              ),
                            ],
                          ),
                        ),
                      ),
                    ],
                  ),
                  const SizedBox(height: 8),
                  AnimatedContainer(
                    duration: const Duration(milliseconds: 300),
                    padding: const EdgeInsets.symmetric(
                        horizontal: 12, vertical: 6),
                    decoration: BoxDecoration(
                      color: isOnline
                          ? NikamaColors.success.withOpacity(0.2)
                          : const Color(0xFF2A2A2A),
                      borderRadius: BorderRadius.circular(8),
                    ),
                    child: Row(
                      mainAxisSize: MainAxisSize.min,
                      children: [
                        Container(
                          width: 8,
                          height: 8,
                          decoration: BoxDecoration(
                            color: isOnline
                                ? NikamaColors.success
                                : NikamaColors.lightGray,
                            shape: BoxShape.circle,
                          ),
                        ),
                        const SizedBox(width: 8),
                        Text(
                          isOnline ? 'En línea — Disponible' : 'Fuera de línea',
                          style: TextStyle(
                            fontFamily: 'Inter',
                            fontSize: 12,
                            fontWeight: FontWeight.w600,
                            color: isOnline
                                ? NikamaColors.success
                                : NikamaColors.lightGray,
                          ),
                        ),
                      ],
                    ),
                  ),
                ],
              ),
            ),

            const SizedBox(height: 24),

            // Stats Row
            Padding(
              padding: const EdgeInsets.symmetric(horizontal: 20),
              child: Row(
                children: [
                  Expanded(
                    child: _StatCard(
                      label: 'Entregas hoy',
                      value: '${stats?['today_completed_count'] ?? 0}',
                      icon: Icons.delivery_dining_rounded,
                      color: NikamaColors.primary,
                    ),
                  ),
                  const SizedBox(width: 14),
                  Expanded(
                    child: _StatCard(
                      label: 'Entregas fallidas',
                      value: '${stats?['today_failed_count'] ?? 0}',
                      icon: Icons.cancel_rounded,
                      color: NikamaColors.error,
                    ),
                  ),
                ],
              ),
            ),
            const SizedBox(height: 14),
            Padding(
              padding: const EdgeInsets.symmetric(horizontal: 20),
              child: Row(
                children: [
                  Expanded(
                    child: _StatCard(
                      label: 'Rating',
                      value: '—',
                      icon: Icons.star_rounded,
                      color: NikamaColors.warning,
                    ),
                  ),
                  const SizedBox(width: 14),
                  Expanded(
                    child: _StatCard(
                      label: 'En espera',
                      value: '0',
                      icon: Icons.hourglass_empty_rounded,
                      color: NikamaColors.info,
                    ),
                  ),
                ],
              ),
            ),

            const SizedBox(height: 24),

            // Asignaciones activas
            const Padding(
              padding: EdgeInsets.symmetric(horizontal: 20),
              child: Text('Asignaciones activas',
                  style: TextStyle(
                    fontFamily: 'Inter',
                    fontSize: 18,
                    fontWeight: FontWeight.w700,
                    color: NikamaColors.black,
                  )),
            ),
            const SizedBox(height: 14),
            Padding(
              padding: const EdgeInsets.symmetric(horizontal: 20),
              child: !isOnline
                  ? Container(
                      width: double.infinity,
                      padding: const EdgeInsets.all(24),
                      decoration: BoxDecoration(
                        color: NikamaColors.surfaceWhite,
                        borderRadius: BorderRadius.circular(16),
                        border: Border.all(color: const Color(0xFFEEEEEE)),
                      ),
                      child: const Column(
                        children: [
                          Text('⚡', style: TextStyle(fontSize: 40)),
                          SizedBox(height: 12),
                          Text('Activa tu estado para\nrecibir pedidos',
                              textAlign: TextAlign.center,
                              style: TextStyle(
                                fontFamily: 'Inter',
                                fontSize: 14,
                                color: NikamaColors.lightGray,
                              )),
                        ],
                      ),
                    )
                  : Container(
                      width: double.infinity,
                      padding: const EdgeInsets.all(20),
                      decoration: BoxDecoration(
                        gradient: const LinearGradient(
                          colors: [Color(0xFFF5C518), Color(0xFFDBA500)],
                          begin: Alignment.topLeft,
                          end: Alignment.bottomRight,
                        ),
                        borderRadius: BorderRadius.circular(16),
                      ),
                      child: const Row(
                        children: [
                          Icon(Icons.delivery_dining_rounded,
                              color: NikamaColors.black, size: 36),
                          SizedBox(width: 14),
                          Expanded(
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Text('Esperando pedidos...',
                                    style: TextStyle(
                                      fontFamily: 'Inter',
                                      fontSize: 15,
                                      fontWeight: FontWeight.w700,
                                      color: NikamaColors.black,
                                    )),
                                SizedBox(height: 4),
                                Text('Las asignaciones llegarán aquí',
                                    style: TextStyle(
                                      fontFamily: 'Inter',
                                      fontSize: 12,
                                      color: NikamaColors.darkGray,
                                    )),
                              ],
                            ),
                          ),
                        ],
                      ),
                    ),
            ),
            const SizedBox(height: 32),
          ],
        ),
      ),
    );
  }
}

class _StatCard extends StatelessWidget {
  final String label;
  final String value;
  final IconData icon;
  final Color color;

  const _StatCard({
    required this.label,
    required this.value,
    required this.icon,
    required this.color,
  });

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: NikamaColors.surfaceWhite,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: const Color(0xFFEEEEEE)),
      ),
      child: Row(
        children: [
          Container(
            width: 40,
            height: 40,
            decoration: BoxDecoration(
              color: color.withOpacity(0.12),
              borderRadius: BorderRadius.circular(10),
            ),
            child: Icon(icon, color: color, size: 20),
          ),
          const SizedBox(width: 12),
          Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(value,
                  style: const TextStyle(
                    fontFamily: 'Inter',
                    fontSize: 18,
                    fontWeight: FontWeight.w800,
                    color: NikamaColors.black,
                  )),
              Text(label,
                  style: const TextStyle(
                    fontFamily: 'Inter',
                    fontSize: 11,
                    color: NikamaColors.lightGray,
                  )),
            ],
          ),
        ],
      ),
    );
  }
}

class _AssignmentsTab extends StatelessWidget {
  const _AssignmentsTab();

  @override
  Widget build(BuildContext context) {
    final driverData = context.watch<DriverProvider>();
    final assignments = driverData.assignments;

    return SafeArea(
      child: Padding(
        padding: const EdgeInsets.all(20),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            const Text('Asignaciones',
                style: TextStyle(
                  fontFamily: 'Inter',
                  fontSize: 24,
                  fontWeight: FontWeight.w800,
                  color: NikamaColors.black,
                )),
            const SizedBox(height: 20),
            Expanded(
              child: assignments.isEmpty
                  ? Center(
                      child: Column(
                        mainAxisAlignment: MainAxisAlignment.center,
                        children: [
                          const Text('🏍️', style: TextStyle(fontSize: 64)),
                          const SizedBox(height: 16),
                          const Text('Sin asignaciones activas',
                              style: TextStyle(
                                  fontFamily: 'Inter',
                                  fontSize: 18,
                                  fontWeight: FontWeight.w700,
                                  color: NikamaColors.black)),
                          const SizedBox(height: 8),
                          Text(
                            'Las entregas asignadas por el negocio aparecerán aquí.',
                            textAlign: TextAlign.center,
                            style: TextStyle(
                                fontFamily: 'Inter',
                                fontSize: 13,
                                color: NikamaColors.lightGray),
                          ),
                        ],
                      ),
                    )
                  : ListView.builder(
                      itemCount: assignments.length,
                      itemBuilder: (_, index) {
                        final assign = assignments[index] as Map<String, dynamic>;
                        final order = assign['order'] as Map<String, dynamic>? ?? {};
                        final delivery = assign['delivery'] as Map<String, dynamic>? ?? {};
                        final isAssignedOnly = assign['status'] == 'assigned';

                        return Container(
                          margin: const EdgeInsets.only(bottom: 16),
                          padding: const EdgeInsets.all(16),
                          decoration: BoxDecoration(
                            color: NikamaColors.surfaceWhite,
                            borderRadius: BorderRadius.circular(16),
                            border: Border.all(color: const Color(0xFFEEEEEE)),
                          ),
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Row(
                                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                children: [
                                  Text(
                                    order['order_number'] ?? 'NKM-??????',
                                    style: const TextStyle(
                                      fontFamily: 'Inter',
                                      fontSize: 15,
                                      fontWeight: FontWeight.w800,
                                      color: NikamaColors.black,
                                    ),
                                  ),
                                  Container(
                                    padding: const EdgeInsets.symmetric(
                                        horizontal: 10, vertical: 4),
                                    decoration: BoxDecoration(
                                      color: isAssignedOnly
                                          ? NikamaColors.primaryLight
                                          : NikamaColors.success.withOpacity(0.15),
                                      borderRadius: BorderRadius.circular(20),
                                    ),
                                    child: Text(
                                      isAssignedOnly ? 'Asignado' : 'En Progreso',
                                      style: TextStyle(
                                        fontFamily: 'Inter',
                                        fontSize: 11,
                                        fontWeight: FontWeight.w700,
                                        color: isAssignedOnly
                                            ? NikamaColors.primaryDark
                                            : NikamaColors.success,
                                      ),
                                    ),
                                  ),
                                ],
                              ),
                              const Divider(height: 24),
                              Row(
                                children: [
                                  const Icon(Icons.store_rounded,
                                      color: NikamaColors.lightGray, size: 18),
                                  const SizedBox(width: 8),
                                  Expanded(
                                    child: Text(
                                      'Retirar en: ${delivery['business_name'] ?? 'Negocio'}',
                                      style: const TextStyle(
                                        fontFamily: 'Inter',
                                        fontSize: 13,
                                        fontWeight: FontWeight.w600,
                                        color: NikamaColors.black,
                                      ),
                                    ),
                                  ),
                                ],
                              ),
                              const SizedBox(height: 6),
                              Row(
                                children: [
                                  const Icon(Icons.location_on_rounded,
                                      color: NikamaColors.primary, size: 18),
                                  const SizedBox(width: 8),
                                  Expanded(
                                    child: Text(
                                      'Entregar en: ${order['delivery_address'] ?? 'Dirección'}',
                                      style: const TextStyle(
                                        fontFamily: 'Inter',
                                        fontSize: 13,
                                        color: NikamaColors.darkGray,
                                      ),
                                    ),
                                  ),
                                ],
                              ),
                              const SizedBox(height: 14),
                              Row(
                                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                children: [
                                  Text(
                                    'Total: S/. ${(order['total'] ?? 0.00).toString()}',
                                    style: const TextStyle(
                                      fontFamily: 'Inter',
                                      fontSize: 14,
                                      fontWeight: FontWeight.w700,
                                      color: NikamaColors.black,
                                    ),
                                  ),
                                  Text(
                                    '${order['items_count'] ?? 1} item(s)',
                                    style: const TextStyle(
                                      fontFamily: 'Inter',
                                      fontSize: 12,
                                      color: NikamaColors.lightGray,
                                    ),
                                  ),
                                ],
                              ),
                              const SizedBox(height: 16),
                              SizedBox(
                                width: double.infinity,
                                height: 46,
                                child: isAssignedOnly
                                    ? ElevatedButton(
                                        onPressed: () async {
                                          final success = await context
                                              .read<DriverProvider>()
                                              .acceptAssignment(assign['id']);
                                          if (context.mounted) {
                                            ScaffoldMessenger.of(context)
                                                .showSnackBar(
                                              SnackBar(
                                                content: Text(success
                                                    ? 'Asignación aceptada con éxito.'
                                                    : 'Ocurrió un problema.'),
                                                backgroundColor: success
                                                    ? NikamaColors.success
                                                    : NikamaColors.error,
                                              ),
                                            );
                                          }
                                        },
                                        style: ElevatedButton.styleFrom(
                                          backgroundColor: NikamaColors.primary,
                                          foregroundColor: NikamaColors.black,
                                          shape: RoundedRectangleBorder(
                                              borderRadius:
                                                  BorderRadius.circular(12)),
                                        ),
                                        child: const Text('Aceptar Pedido',
                                            style: TextStyle(
                                                fontFamily: 'Inter',
                                                fontWeight: FontWeight.w700)),
                                      )
                                    : ElevatedButton(
                                        onPressed: () async {
                                          final success = await context
                                              .read<DriverProvider>()
                                              .completeDelivery(delivery['id']);
                                          if (context.mounted) {
                                            ScaffoldMessenger.of(context)
                                                .showSnackBar(
                                              SnackBar(
                                                content: Text(success
                                                    ? 'Pedido entregado con éxito.'
                                                    : 'Ocurrió un problema.'),
                                                backgroundColor: success
                                                    ? NikamaColors.success
                                                    : NikamaColors.error,
                                              ),
                                            );
                                          }
                                        },
                                        style: ElevatedButton.styleFrom(
                                          backgroundColor: NikamaColors.success,
                                          foregroundColor: NikamaColors.white,
                                          shape: RoundedRectangleBorder(
                                              borderRadius:
                                                  BorderRadius.circular(12)),
                                        ),
                                        child: const Text('Entregar Pedido',
                                            style: TextStyle(
                                                fontFamily: 'Inter',
                                                fontWeight: FontWeight.w700)),
                                      ),
                              ),
                            ],
                          ),
                        );
                      },
                    ),
            ),
          ],
        ),
      ),
    );
  }
}

class _EarningsTab extends StatelessWidget {
  const _EarningsTab();

  @override
  Widget build(BuildContext context) {
    return SafeArea(
      child: Padding(
        padding: const EdgeInsets.all(20),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            const Text('Mis Ganancias',
                style: TextStyle(
                  fontFamily: 'Inter',
                  fontSize: 24,
                  fontWeight: FontWeight.w800,
                  color: NikamaColors.black,
                )),
            const SizedBox(height: 16),
            Container(
              width: double.infinity,
              padding: const EdgeInsets.all(24),
              decoration: BoxDecoration(
                gradient: NikamaColors.heroGradient,
                borderRadius: BorderRadius.circular(20),
              ),
              child: const Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text('Ganancias totales',
                      style: TextStyle(
                          fontFamily: 'Inter',
                          fontSize: 13,
                          color: NikamaColors.lightGray)),
                  SizedBox(height: 8),
                  Text('S/. 0.00',
                      style: TextStyle(
                        fontFamily: 'Inter',
                        fontSize: 36,
                        fontWeight: FontWeight.w800,
                        color: NikamaColors.primary,
                      )),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }
}

class _DriverProfileTab extends StatelessWidget {
  final dynamic user;
  const _DriverProfileTab({this.user});

  @override
  Widget build(BuildContext context) {
    return SafeArea(
      child: SingleChildScrollView(
        padding: const EdgeInsets.all(20),
        child: Column(
          children: [
            Container(
              width: double.infinity,
              padding: const EdgeInsets.all(24),
              decoration: BoxDecoration(
                gradient: NikamaColors.heroGradient,
                borderRadius: BorderRadius.circular(20),
              ),
              child: Column(
                children: [
                  Container(
                    width: 72,
                    height: 72,
                    decoration: BoxDecoration(
                      color: NikamaColors.primary,
                      borderRadius: BorderRadius.circular(18),
                    ),
                    child: const Icon(Icons.delivery_dining_rounded,
                        color: NikamaColors.black, size: 36),
                  ),
                  const SizedBox(height: 12),
                  Text(
                    user?.fullName ?? 'Repartidor',
                    style: const TextStyle(
                      fontFamily: 'Inter',
                      fontSize: 18,
                      fontWeight: FontWeight.w700,
                      color: NikamaColors.white,
                    ),
                  ),
                  const SizedBox(height: 6),
                  Container(
                    padding: const EdgeInsets.symmetric(
                        horizontal: 12, vertical: 4),
                    decoration: BoxDecoration(
                      color: NikamaColors.primary,
                      borderRadius: BorderRadius.circular(20),
                    ),
                    child: const Text('REPARTIDOR',
                        style: TextStyle(
                          fontFamily: 'Inter',
                          fontSize: 11,
                          fontWeight: FontWeight.w700,
                          color: NikamaColors.black,
                        )),
                  ),
                ],
              ),
            ),
            const SizedBox(height: 20),
            SizedBox(
              width: double.infinity,
              height: 50,
              child: ElevatedButton.icon(
                onPressed: () async {
                  await context.read<AuthProvider>().logout();
                  if (context.mounted) {
                    Navigator.pushAndRemoveUntil(
                      context,
                      MaterialPageRoute(builder: (_) => const LoginScreen()),
                      (_) => false,
                    );
                  }
                },
                icon: const Icon(Icons.logout),
                label: const Text('Cerrar sesión'),
                style: ElevatedButton.styleFrom(
                  backgroundColor: NikamaColors.error,
                  foregroundColor: NikamaColors.white,
                  shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(14)),
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }
}
