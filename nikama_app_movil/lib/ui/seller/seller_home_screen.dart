import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../data/providers/auth_provider.dart';
import '../../data/providers/seller_provider.dart';
import '../../core/constants/app_theme.dart';
import '../screens/login_screen.dart';

// ============================================================
// NIKAMA — Home Screen del SELLER (Negocio/Restaurante)
// ============================================================

class SellerHomeScreen extends StatefulWidget {
  const SellerHomeScreen({super.key});

  @override
  State<SellerHomeScreen> createState() => _SellerHomeScreenState();
}

class _SellerHomeScreenState extends State<SellerHomeScreen> {
  int _currentIndex = 0;

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      context.read<SellerProvider>().loadDashboard();
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
          _SellerDashboard(name: user?.firstName ?? 'Vendedor'),
          const _SellerOrders(),
          const _SellerProducts(),
          _SellerProfile(user: user),
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
              icon: Icon(Icons.receipt_long_outlined),
              activeIcon: Icon(Icons.receipt_long_rounded),
              label: 'Pedidos'),
          BottomNavigationBarItem(
              icon: Icon(Icons.restaurant_menu_outlined),
              activeIcon: Icon(Icons.restaurant_menu_rounded),
              label: 'Productos'),
          BottomNavigationBarItem(
              icon: Icon(Icons.store_outlined),
              activeIcon: Icon(Icons.store_rounded),
              label: 'Mi Negocio'),
        ],
      ),
    );
  }
}

class _SellerDashboard extends StatelessWidget {
  final String name;
  const _SellerDashboard({required this.name});

  @override
  Widget build(BuildContext context) {
    final sellerData = context.watch<SellerProvider>();
    final stats = sellerData.dashboardStats;
    final recentOrders = sellerData.recentOrders;

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
                  Text('¡Hola, $name! 🏪',
                      style: const TextStyle(
                        fontFamily: 'Inter',
                        fontSize: 22,
                        fontWeight: FontWeight.w800,
                        color: NikamaColors.white,
                      )),
                  const SizedBox(height: 4),
                  const Text('Panel de tu negocio',
                      style: TextStyle(
                          fontFamily: 'Inter',
                          fontSize: 13,
                          color: NikamaColors.lightGray)),
                  const SizedBox(height: 20),
                  // Stats rápidos
                  Row(
                    children: [
                      Expanded(
                          child: _QuickStat(
                              label: 'Ventas hoy',
                              value: 'S/. ${stats?['today_sales'] ?? '0'}',
                              bg: NikamaColors.primary)),
                      const SizedBox(width: 12),
                      Expanded(
                          child: _QuickStat(
                              label: 'Pedidos hoy',
                              value: '${stats?['today_orders_count'] ?? '0'}',
                              bg: const Color(0xFF2A2A2A))),
                    ],
                  ),
                ],
              ),
            ),
            const SizedBox(height: 24),
            const Padding(
              padding: EdgeInsets.symmetric(horizontal: 20),
              child: Text('Pedidos recientes',
                  style: TextStyle(
                    fontFamily: 'Inter',
                    fontSize: 18,
                    fontWeight: FontWeight.w700,
                    color: NikamaColors.black,
                  )),
            ),
            const SizedBox(height: 12),
            if (sellerData.isLoading)
              const Center(child: CircularProgressIndicator())
            else if (recentOrders.isEmpty)
              Padding(
                padding: const EdgeInsets.symmetric(horizontal: 20),
                child: Container(
                  width: double.infinity,
                  padding: const EdgeInsets.all(24),
                  decoration: BoxDecoration(
                    color: NikamaColors.surfaceWhite,
                    borderRadius: BorderRadius.circular(16),
                    border: Border.all(color: const Color(0xFFEEEEEE)),
                  ),
                  child: const Column(
                    children: [
                      Text('📋', style: TextStyle(fontSize: 48)),
                      SizedBox(height: 12),
                      Text('Sin pedidos recientes',
                          style: TextStyle(
                            fontFamily: 'Inter',
                            fontSize: 15,
                            fontWeight: FontWeight.w600,
                            color: NikamaColors.black,
                          )),
                    ],
                  ),
                ),
              )
            else
              ...recentOrders.map((order) => Padding(
                    padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 6),
                    child: Container(
                      padding: const EdgeInsets.all(16),
                      decoration: BoxDecoration(
                        color: NikamaColors.surfaceWhite,
                        borderRadius: BorderRadius.circular(12),
                        border: Border.all(color: const Color(0xFFEEEEEE)),
                      ),
                      child: Row(
                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                        children: [
                          Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Text('Pedido #${order['order_number']}',
                                  style: const TextStyle(fontWeight: FontWeight.w700)),
                              Text('Total: S/. ${order['total']}'),
                            ],
                          ),
                          Container(
                            padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                            decoration: BoxDecoration(
                              color: NikamaColors.primary.withOpacity(0.2),
                              borderRadius: BorderRadius.circular(8),
                            ),
                            child: Text('${order['status']}', style: const TextStyle(fontSize: 12)),
                          )
                        ],
                      ),
                    ),
                  )),
            const SizedBox(height: 32),
          ],
        ),
      ),
    );
  }
}

class _QuickStat extends StatelessWidget {
  final String label;
  final String value;
  final Color bg;

  const _QuickStat(
      {required this.label, required this.value, required this.bg});

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: bg,
        borderRadius: BorderRadius.circular(14),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(value,
              style: TextStyle(
                fontFamily: 'Inter',
                fontSize: 22,
                fontWeight: FontWeight.w800,
                color: bg == NikamaColors.primary
                    ? NikamaColors.black
                    : NikamaColors.white,
              )),
          Text(label,
              style: TextStyle(
                fontFamily: 'Inter',
                fontSize: 11,
                color: bg == NikamaColors.primary
                    ? NikamaColors.darkGray
                    : NikamaColors.lightGray,
              )),
        ],
      ),
    );
  }
}

class _SellerOrders extends StatefulWidget {
  const _SellerOrders();

  @override
  State<_SellerOrders> createState() => _SellerOrdersState();
}

class _SellerOrdersState extends State<_SellerOrders> {
  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      context.read<SellerProvider>().loadOrders();
    });
  }

  void _updateStatus(String orderId, String status) async {
    final provider = context.read<SellerProvider>();
    final success = await provider.updateOrderStatus(orderId, status);
    if (mounted && success) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Estado actualizado')),
      );
    }
  }

  void _cancelOrder(String orderId) async {
    final provider = context.read<SellerProvider>();
    // Simular un prompt para la razón
    final success = await provider.updateOrderStatus(orderId, 'cancelled', reason: 'Cancelado por administrador');
    if (mounted && success) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Pedido cancelado')),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    final sellerData = context.watch<SellerProvider>();

    return SafeArea(
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Padding(
            padding: EdgeInsets.all(20),
            child: Text('Pedidos',
                style: TextStyle(
                    fontFamily: 'Inter',
                    fontSize: 24,
                    fontWeight: FontWeight.w800,
                    color: NikamaColors.black)),
          ),
          Expanded(
            child: sellerData.isLoadingOrders
                ? const Center(child: CircularProgressIndicator())
                : sellerData.orders.isEmpty
                    ? const Center(child: Text('No hay pedidos'))
                    : ListView.builder(
                        itemCount: sellerData.orders.length,
                        itemBuilder: (context, index) {
                          final order = sellerData.orders[index];
                          return Card(
                            margin: const EdgeInsets.symmetric(horizontal: 20, vertical: 8),
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
                                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                    children: [
                                      Text('Pedido #${order['order_number']}',
                                          style: const TextStyle(fontWeight: FontWeight.w700)),
                                      Container(
                                        padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                                        decoration: BoxDecoration(
                                          color: NikamaColors.primary.withOpacity(0.2),
                                          borderRadius: BorderRadius.circular(8),
                                        ),
                                        child: Text('${order['status']}'),
                                      ),
                                    ],
                                  ),
                                  const SizedBox(height: 8),
                                  Text('Cliente: ${order['user']['first_name']} ${order['user']['last_name']}'),
                                  Text('Total: S/. ${order['total']}'),
                                  const SizedBox(height: 12),
                                  if (order['status'] == 'pending')
                                    Row(
                                      mainAxisAlignment: MainAxisAlignment.end,
                                      children: [
                                        TextButton(
                                          onPressed: () => _cancelOrder(order['id']),
                                          style: TextButton.styleFrom(foregroundColor: NikamaColors.error),
                                          child: const Text('Cancelar'),
                                        ),
                                        const SizedBox(width: 8),
                                        ElevatedButton(
                                          onPressed: () => _updateStatus(order['id'], 'confirmed'),
                                          style: ElevatedButton.styleFrom(backgroundColor: NikamaColors.success, foregroundColor: NikamaColors.white),
                                          child: const Text('Confirmar'),
                                        ),
                                      ],
                                    ),
                                  if (order['status'] == 'confirmed')
                                    Row(
                                      mainAxisAlignment: MainAxisAlignment.end,
                                      children: [
                                        ElevatedButton(
                                          onPressed: () => _updateStatus(order['id'], 'preparing'),
                                          style: ElevatedButton.styleFrom(backgroundColor: NikamaColors.info, foregroundColor: NikamaColors.white),
                                          child: const Text('En Preparación'),
                                        ),
                                      ],
                                    ),
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

class _SellerProducts extends StatefulWidget {
  const _SellerProducts();

  @override
  State<_SellerProducts> createState() => _SellerProductsState();
}

class _SellerProductsState extends State<_SellerProducts> {
  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      context.read<SellerProvider>().loadProducts();
    });
  }

  @override
  Widget build(BuildContext context) {
    final sellerData = context.watch<SellerProvider>();

    return SafeArea(
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Padding(
            padding: const EdgeInsets.all(20),
            child: Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                const Text('Productos',
                    style: TextStyle(
                        fontFamily: 'Inter',
                        fontSize: 24,
                        fontWeight: FontWeight.w800,
                        color: NikamaColors.black)),
                ElevatedButton.icon(
                  onPressed: () {
                    ScaffoldMessenger.of(context).showSnackBar(
                      const SnackBar(content: Text('Por hacer: Formulario nuevo producto')),
                    );
                  },
                  icon: const Icon(Icons.add),
                  label: const Text('Nuevo'),
                  style: ElevatedButton.styleFrom(
                    backgroundColor: NikamaColors.primary,
                    foregroundColor: NikamaColors.black,
                    shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(10)),
                  ),
                ),
              ],
          ),
          Expanded(
            child: sellerData.isLoadingProducts
                ? const Center(child: CircularProgressIndicator())
                : sellerData.products.isEmpty
                    ? const Center(child: Text('No hay productos'))
                    : ListView.builder(
                        itemCount: sellerData.products.length,
                        itemBuilder: (context, index) {
                          final product = sellerData.products[index];
                          final isActive = product['status'] == 'active';
                          return Card(
                            margin: const EdgeInsets.symmetric(horizontal: 20, vertical: 6),
                            color: NikamaColors.surfaceWhite,
                            shape: RoundedRectangleBorder(
                              borderRadius: BorderRadius.circular(12),
                              side: const BorderSide(color: Color(0xFFEEEEEE)),
                            ),
                            child: ListTile(
                              leading: product['main_image_url'] != null
                                  ? ClipRRect(
                                      borderRadius: BorderRadius.circular(8),
                                      child: Image.network(product['main_image_url'], width: 50, height: 50, fit: BoxFit.cover),
                                    )
                                  : Container(
                                      width: 50,
                                      height: 50,
                                      decoration: BoxDecoration(color: NikamaColors.lightGray.withOpacity(0.2), borderRadius: BorderRadius.circular(8)),
                                      child: const Icon(Icons.image, color: NikamaColors.lightGray),
                                    ),
                              title: Text(product['name'], style: const TextStyle(fontWeight: FontWeight.w700)),
                              subtitle: Text('S/. ${product['price']}'),
                              trailing: Switch(
                                value: isActive,
                                activeColor: NikamaColors.primary,
                                onChanged: (val) {
                                  context.read<SellerProvider>().toggleProductStatus(product['id']);
                                },
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

class _SellerProfile extends StatelessWidget {
  final dynamic user;
  const _SellerProfile({this.user});

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
                  const Icon(Icons.store_rounded,
                      color: NikamaColors.primary, size: 48),
                  const SizedBox(height: 12),
                  Text(
                    user?.fullName ?? 'Vendedor',
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
                    child: const Text('SELLER',
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
