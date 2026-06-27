import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../data/providers/auth_provider.dart';
import '../../data/providers/admin_provider.dart';
import '../../core/constants/app_theme.dart';
import '../screens/login_screen.dart';

// ============================================================
// NIKAMA — Home Screen del SUPER ADMIN
// ============================================================

class AdminHomeScreen extends StatefulWidget {
  const AdminHomeScreen({super.key});

  @override
  State<AdminHomeScreen> createState() => _AdminHomeScreenState();
}

class _AdminHomeScreenState extends State<AdminHomeScreen> {
  int _currentIndex = 0;

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      context.read<AdminProvider>().loadDashboard();
    });
  }

  @override
  Widget build(BuildContext context) {
    final user = context.watch<AuthProvider>().user;

    return Scaffold(
      backgroundColor: NikamaColors.offWhite,
      body: IndexedStack(
        index: _currentIndex,
        children: [
          _AdminDashboard(name: user?.firstName ?? 'Admin'),
          const _AdminDrivers(),
          const _AdminBusinesses(),
          _AdminProfile(user: user),
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
        items: const [
          BottomNavigationBarItem(
              icon: Icon(Icons.dashboard_outlined),
              activeIcon: Icon(Icons.dashboard_rounded),
              label: 'Dashboard'),
          BottomNavigationBarItem(
              icon: Icon(Icons.person_search_outlined),
              activeIcon: Icon(Icons.person_search_rounded),
              label: 'Drivers'),
          BottomNavigationBarItem(
              icon: Icon(Icons.store_outlined),
              activeIcon: Icon(Icons.store_rounded),
              label: 'Negocios'),
          BottomNavigationBarItem(
              icon: Icon(Icons.admin_panel_settings_outlined),
              activeIcon: Icon(Icons.admin_panel_settings_rounded),
              label: 'Admin'),
        ],
      ),
    );
  }
}

class _AdminDashboard extends StatelessWidget {
  final String name;
  const _AdminDashboard({required this.name});

  @override
  Widget build(BuildContext context) {
    final adminData = context.watch<AdminProvider>();
    final stats = adminData.dashboardStats;

    return SafeArea(
      child: SingleChildScrollView(
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
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
                    children: [
                      Container(
                        width: 44,
                        height: 44,
                        decoration: BoxDecoration(
                          color: NikamaColors.primary,
                          borderRadius: BorderRadius.circular(12),
                        ),
                        child: const Icon(Icons.admin_panel_settings_rounded,
                            color: NikamaColors.black, size: 22),
                      ),
                      const SizedBox(width: 12),
                      Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text('Hola, $name ⚙️',
                              style: const TextStyle(
                                fontFamily: 'Inter',
                                fontSize: 20,
                                fontWeight: FontWeight.w800,
                                color: NikamaColors.white,
                              )),
                          const Text('Super Administrador',
                              style: TextStyle(
                                  fontFamily: 'Inter',
                                  fontSize: 12,
                                  color: NikamaColors.lightGray)),
                        ],
                      ),
                    ],
                  ),
                  const SizedBox(height: 20),
                  // Stats grid
                  GridView.count(
                    shrinkWrap: true,
                    physics: const NeverScrollableScrollPhysics(),
                    crossAxisCount: 2,
                    mainAxisSpacing: 12,
                    crossAxisSpacing: 12,
                    childAspectRatio: 1.6,
                    children: [
                      _AdminStatCard(
                          label: 'Total Usuarios',
                          value: '${stats?['total_users'] ?? '—'}',
                          icon: Icons.people_rounded,
                          color: NikamaColors.primary),
                      _AdminStatCard(
                          label: 'Total Pedidos',
                          value: '${stats?['total_orders'] ?? '—'}',
                          icon: Icons.receipt_long_rounded,
                          color: NikamaColors.success),
                      _AdminStatCard(
                          label: 'Negocios activos',
                          value: '${stats?['active_sellers'] ?? '—'}',
                          icon: Icons.store_rounded,
                          color: NikamaColors.info),
                      _AdminStatCard(
                          label: 'Drivers activos',
                          value: '${stats?['active_drivers'] ?? '—'}',
                          icon: Icons.delivery_dining_rounded,
                          color: NikamaColors.warning),
                    ],
                  ),
                ],
              ),
            ),
            const SizedBox(height: 24),
            const Padding(
              padding: EdgeInsets.symmetric(horizontal: 20),
              child: Text('Acceso rápido',
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
              child: Column(
                children: [
                  _AdminOption(
                      icon: Icons.pending_actions_rounded,
                      label: 'Aprobar Repartidores',
                      subtitle: 'GET /admin/drivers/pending',
                      color: NikamaColors.primary,
                      onTap: () {}),
                  const SizedBox(height: 10),
                  _AdminOption(
                      icon: Icons.business_center_rounded,
                      label: 'Gestionar Negocios',
                      subtitle: 'GET /admin/businesses',
                      color: NikamaColors.info,
                      onTap: () {}),
                  const SizedBox(height: 10),
                  _AdminOption(
                      icon: Icons.bar_chart_rounded,
                      label: 'Reportes y Estadísticas',
                      subtitle: 'GET /admin/dashboard',
                      color: NikamaColors.success,
                      onTap: () {}),
                ],
              ),
            ),
            const SizedBox(height: 32),
          ],
        ),
      ),
    );
  }
}

class _AdminStatCard extends StatelessWidget {
  final String label;
  final String value;
  final IconData icon;
  final Color color;

  const _AdminStatCard({
    required this.label,
    required this.value,
    required this.icon,
    required this.color,
  });

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(14),
      decoration: BoxDecoration(
        color: color.withOpacity(0.15),
        borderRadius: BorderRadius.circular(14),
        border: Border.all(color: color.withOpacity(0.3)),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Icon(icon, color: color, size: 22),
          Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(value,
                  style: const TextStyle(
                    fontFamily: 'Inter',
                    fontSize: 20,
                    fontWeight: FontWeight.w800,
                    color: NikamaColors.white,
                  )),
              Text(label,
                  style: const TextStyle(
                    fontFamily: 'Inter',
                    fontSize: 10,
                    color: NikamaColors.lightGray,
                  )),
            ],
          ),
        ],
      ),
    );
  }
}

class _AdminOption extends StatelessWidget {
  final IconData icon;
  final String label;
  final String subtitle;
  final Color color;
  final VoidCallback onTap;

  const _AdminOption({
    required this.icon,
    required this.label,
    required this.subtitle,
    required this.color,
    required this.onTap,
  });

  @override
  Widget build(BuildContext context) {
    return GestureDetector(
      onTap: onTap,
      child: Container(
        padding: const EdgeInsets.all(16),
        decoration: BoxDecoration(
          color: NikamaColors.surfaceWhite,
          borderRadius: BorderRadius.circular(14),
          border: Border.all(color: const Color(0xFFEEEEEE)),
        ),
        child: Row(
          children: [
            Container(
              width: 44,
              height: 44,
              decoration: BoxDecoration(
                color: color.withOpacity(0.12),
                borderRadius: BorderRadius.circular(12),
              ),
              child: Icon(icon, color: color, size: 22),
            ),
            const SizedBox(width: 14),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(label,
                      style: const TextStyle(
                        fontFamily: 'Inter',
                        fontSize: 14,
                        fontWeight: FontWeight.w600,
                        color: NikamaColors.black,
                      )),
                  Text(subtitle,
                      style: const TextStyle(
                        fontFamily: 'Inter',
                        fontSize: 11,
                        color: NikamaColors.lightGray,
                      )),
                ],
              ),
            ),
            const Icon(Icons.arrow_forward_ios,
                color: NikamaColors.lightGray, size: 14),
          ],
        ),
      ),
    );
  }
}

class _AdminDrivers extends StatefulWidget {
  const _AdminDrivers();

  @override
  State<_AdminDrivers> createState() => _AdminDriversState();
}

class _AdminDriversState extends State<_AdminDrivers> {
  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      context.read<AdminProvider>().loadDrivers(status: 'pending');
    });
  }

  void _approveDriver(String id) async {
    final provider = context.read<AdminProvider>();
    final success = await provider.approveDriver(id);
    if (mounted && success) {
      ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Repartidor Aprobado')));
    }
  }

  void _rejectDriver(String id) async {
    final provider = context.read<AdminProvider>();
    final success = await provider.rejectDriver(id, 'Rechazado por el administrador');
    if (mounted && success) {
      ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Repartidor Rechazado')));
    }
  }

  @override
  Widget build(BuildContext context) {
    final adminData = context.watch<AdminProvider>();

    return SafeArea(
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Padding(
            padding: EdgeInsets.all(20),
            child: Text('Repartidores Pendientes',
                style: TextStyle(
                    fontFamily: 'Inter',
                    fontSize: 24,
                    fontWeight: FontWeight.w800,
                    color: NikamaColors.black)),
          ),
          Expanded(
            child: adminData.isLoadingDrivers
                ? const Center(child: CircularProgressIndicator())
                : adminData.drivers.isEmpty
                    ? const Center(child: Text('No hay repartidores pendientes'))
                    : ListView.builder(
                        itemCount: adminData.drivers.length,
                        itemBuilder: (context, index) {
                          final driver = adminData.drivers[index];
                          final user = driver['user'];
                          return Card(
                            margin: const EdgeInsets.symmetric(horizontal: 20, vertical: 6),
                            color: NikamaColors.surfaceWhite,
                            shape: RoundedRectangleBorder(
                              borderRadius: BorderRadius.circular(12),
                              side: const BorderSide(color: Color(0xFFEEEEEE)),
                            ),
                            child: Padding(
                              padding: const EdgeInsets.all(16),
                              child: Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  Row(
                                    children: [
                                      const Icon(Icons.motorcycle, color: NikamaColors.primary),
                                      const SizedBox(width: 12),
                                      Expanded(
                                        child: Column(
                                          crossAxisAlignment: CrossAxisAlignment.start,
                                          children: [
                                            Text('${user?['first_name']} ${user?['last_name']}',
                                                style: const TextStyle(fontWeight: FontWeight.w700, fontSize: 16)),
                                            Text('${driver['vehicle_type']} - Placa: ${driver['license_plate']}'),
                                          ],
                                        ),
                                      ),
                                    ],
                                  ),
                                  const SizedBox(height: 12),
                                  Row(
                                    mainAxisAlignment: MainAxisAlignment.end,
                                    children: [
                                      TextButton(
                                        onPressed: () => _rejectDriver(driver['id']),
                                        style: TextButton.styleFrom(foregroundColor: NikamaColors.error),
                                        child: const Text('Rechazar'),
                                      ),
                                      const SizedBox(width: 8),
                                      ElevatedButton(
                                        onPressed: () => _approveDriver(driver['id']),
                                        style: ElevatedButton.styleFrom(
                                            backgroundColor: NikamaColors.success,
                                            foregroundColor: NikamaColors.white),
                                        child: const Text('Aprobar'),
                                      ),
                                    ],
                                  )
                                ],
                              ),
                            ),
                          );
                        },
                      ),
          ),
        ],
      ),
    );
  }
}

class _AdminBusinesses extends StatefulWidget {
  const _AdminBusinesses();

  @override
  State<_AdminBusinesses> createState() => _AdminBusinessesState();
}

class _AdminBusinessesState extends State<_AdminBusinesses> {
  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      context.read<AdminProvider>().loadBusinesses(status: 'pending');
    });
  }

  void _approveBusiness(String id) async {
    final provider = context.read<AdminProvider>();
    final success = await provider.approveBusiness(id);
    if (mounted && success) {
      ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Negocio Aprobado')));
    }
  }

  void _rejectBusiness(String id) async {
    final provider = context.read<AdminProvider>();
    final success = await provider.rejectBusiness(id, 'Rechazado por el administrador');
    if (mounted && success) {
      ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Negocio Rechazado')));
    }
  }

  @override
  Widget build(BuildContext context) {
    final adminData = context.watch<AdminProvider>();

    return SafeArea(
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Padding(
            padding: EdgeInsets.all(20),
            child: Text('Negocios Pendientes',
                style: TextStyle(
                    fontFamily: 'Inter',
                    fontSize: 24,
                    fontWeight: FontWeight.w800,
                    color: NikamaColors.black)),
          ),
          Expanded(
            child: adminData.isLoadingBusinesses
                ? const Center(child: CircularProgressIndicator())
                : adminData.businesses.isEmpty
                    ? const Center(child: Text('No hay negocios pendientes'))
                    : ListView.builder(
                        itemCount: adminData.businesses.length,
                        itemBuilder: (context, index) {
                          final business = adminData.businesses[index];
                          return Card(
                            margin: const EdgeInsets.symmetric(horizontal: 20, vertical: 6),
                            color: NikamaColors.surfaceWhite,
                            shape: RoundedRectangleBorder(
                              borderRadius: BorderRadius.circular(12),
                              side: const BorderSide(color: Color(0xFFEEEEEE)),
                            ),
                            child: Padding(
                              padding: const EdgeInsets.all(16),
                              child: Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  Row(
                                    children: [
                                      const Icon(Icons.store, color: NikamaColors.primary),
                                      const SizedBox(width: 12),
                                      Expanded(
                                        child: Column(
                                          crossAxisAlignment: CrossAxisAlignment.start,
                                          children: [
                                            Text('${business['business_name']}',
                                                style: const TextStyle(fontWeight: FontWeight.w700, fontSize: 16)),
                                            Text('RUC: ${business['ruc']}'),
                                          ],
                                        ),
                                      ),
                                    ],
                                  ),
                                  const SizedBox(height: 12),
                                  Row(
                                    mainAxisAlignment: MainAxisAlignment.end,
                                    children: [
                                      TextButton(
                                        onPressed: () => _rejectBusiness(business['id']),
                                        style: TextButton.styleFrom(foregroundColor: NikamaColors.error),
                                        child: const Text('Rechazar'),
                                      ),
                                      const SizedBox(width: 8),
                                      ElevatedButton(
                                        onPressed: () => _approveBusiness(business['id']),
                                        style: ElevatedButton.styleFrom(
                                            backgroundColor: NikamaColors.success,
                                            foregroundColor: NikamaColors.white),
                                        child: const Text('Aprobar'),
                                      ),
                                    ],
                                  )
                                ],
                              ),
                            ),
                          );
                        },
                      ),
          ),
        ],
      ),
    );
  }
}

class _AdminProfile extends StatelessWidget {
  final dynamic user;
  const _AdminProfile({this.user});

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
                  const Icon(Icons.admin_panel_settings_rounded,
                      color: NikamaColors.primary, size: 48),
                  const SizedBox(height: 12),
                  Text(
                    user?.fullName ?? 'Administrador',
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
                    child: const Text('SUPER ADMIN',
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
