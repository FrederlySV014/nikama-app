import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../data/providers/auth_provider.dart';
import '../../data/providers/catalog_provider.dart';
import '../../data/providers/cart_provider.dart';
import '../../data/providers/order_provider.dart';
import '../../data/models/product_model.dart';
import '../../core/constants/app_theme.dart';
import '../screens/login_screen.dart';
import 'product_detail_screen.dart';
import 'cart_screen.dart';
import 'order_tracking_screen.dart';

// ============================================================
// NIKAMA — Home Screen del CLIENTE
// Pantalla principal con navbar: Inicio | Explorar | Pedidos | Perfil
// ============================================================

class CustomerHomeScreen extends StatefulWidget {
  const CustomerHomeScreen({super.key});

  @override
  State<CustomerHomeScreen> createState() => _CustomerHomeScreenState();
}

class _CustomerHomeScreenState extends State<CustomerHomeScreen> {
  int _currentIndex = 0;

  final List<Widget> _pages = const [
    _HomeTab(),
    _ExploreTab(),
    _OrdersTab(),
    _ProfileTab(),
  ];

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: NikamaColors.offWhite,
      body: IndexedStack(
        index: _currentIndex,
        children: _pages,
      ),
      bottomNavigationBar: Container(
        decoration: BoxDecoration(
          boxShadow: [
            BoxShadow(
              color: NikamaColors.black.withOpacity(0.08),
              blurRadius: 20,
              offset: const Offset(0, -4),
            ),
          ],
        ),
        child: BottomNavigationBar(
          currentIndex: _currentIndex,
          onTap: (i) => setState(() => _currentIndex = i),
          type: BottomNavigationBarType.fixed,
          backgroundColor: NikamaColors.surfaceWhite,
          selectedItemColor: NikamaColors.primary,
          unselectedItemColor: NikamaColors.lightGray,
          selectedLabelStyle: const TextStyle(
            fontFamily: 'Inter',
            fontWeight: FontWeight.w700,
            fontSize: 11,
          ),
          unselectedLabelStyle: const TextStyle(
            fontFamily: 'Inter',
            fontSize: 11,
          ),
          items: const [
            BottomNavigationBarItem(
                icon: Icon(Icons.home_outlined),
                activeIcon: Icon(Icons.home_rounded),
                label: 'Inicio'),
            BottomNavigationBarItem(
                icon: Icon(Icons.grid_view_outlined),
                activeIcon: Icon(Icons.grid_view_rounded),
                label: 'Explorar'),
            BottomNavigationBarItem(
                icon: Icon(Icons.receipt_long_outlined),
                activeIcon: Icon(Icons.receipt_long_rounded),
                label: 'Pedidos'),
            BottomNavigationBarItem(
                icon: Icon(Icons.person_outline_rounded),
                activeIcon: Icon(Icons.person_rounded),
                label: 'Perfil'),
          ],
        ),
      ),
    );
  }
}

// ── TAB: INICIO ─────────────────────────────────────────────
class _HomeTab extends StatefulWidget {
  const _HomeTab();

  @override
  State<_HomeTab> createState() => _HomeTabState();
}

class _HomeTabState extends State<_HomeTab> {
  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      context.read<CatalogProvider>().loadCatalogData();
      context.read<CartProvider>().loadCart();
    });
  }

  String _mapCategoryIcon(String iconName) {
    switch (iconName) {
      case 'burger': return '🍔';
      case 'pizza': return '🍕';
      case 'sushi': return '🍱';
      case 'chicken': return '🍗';
      case 'salad': return '🥗';
      case 'soup': return '🍜';
      case 'drink': return '🥤';
      case 'cake': return '🍰';
      default: return '🍽️';
    }
  }

  @override
  Widget build(BuildContext context) {
    final user = context.watch<AuthProvider>().user;
    final catalog = context.watch<CatalogProvider>();
    final cart = context.watch<CartProvider>();

    return SafeArea(
      child: RefreshIndicator(
        onRefresh: () => catalog.loadCatalogData(),
        color: NikamaColors.primary,
        backgroundColor: NikamaColors.black,
        child: SingleChildScrollView(
          physics: const AlwaysScrollableScrollPhysics(),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              // Header
              Container(
                padding: const EdgeInsets.fromLTRB(20, 20, 20, 24),
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
                            Text(
                              '¡Hola, ${user?.firstName ?? 'Usuario'}! 👋',
                              style: const TextStyle(
                                fontFamily: 'Inter',
                                fontSize: 22,
                                fontWeight: FontWeight.w800,
                                color: NikamaColors.white,
                              ),
                            ),
                            const SizedBox(height: 4),
                            const Text(
                              '¿Qué deseas pedir hoy?',
                              style: TextStyle(
                                fontFamily: 'Inter',
                                fontSize: 13,
                                color: NikamaColors.lightGray,
                              ),
                            ),
                          ],
                        ),
                        GestureDetector(
                          onTap: () => Navigator.push(
                            context,
                            MaterialPageRoute(
                                builder: (_) => const CartScreen()),
                          ),
                          child: Stack(
                            children: [
                              Container(
                                width: 44,
                                height: 44,
                                decoration: BoxDecoration(
                                  color: NikamaColors.primary,
                                  borderRadius: BorderRadius.circular(12),
                                ),
                                child: const Icon(Icons.shopping_cart_outlined,
                                    color: NikamaColors.black, size: 22),
                              ),
                              if (cart.itemCount > 0)
                                Positioned(
                                  right: 0,
                                  top: 0,
                                  child: Container(
                                    width: 18,
                                    height: 18,
                                    decoration: BoxDecoration(
                                      color: NikamaColors.error,
                                      borderRadius: BorderRadius.circular(9),
                                      border: Border.all(
                                          color: NikamaColors.black,
                                          width: 1.5),
                                    ),
                                    child: Center(
                                      child: Text(
                                        '${cart.itemCount}',
                                        style: const TextStyle(
                                          fontSize: 9,
                                          fontWeight: FontWeight.w800,
                                          color: Colors.white,
                                        ),
                                      ),
                                    ),
                                  ),
                                ),
                            ],
                          ),
                        ),
                      ],
                    ),
                    const SizedBox(height: 20),
                    // Barra de búsqueda
                    Container(
                      height: 48,
                      decoration: BoxDecoration(
                        color: const Color(0xFF2A2A2A),
                        borderRadius: BorderRadius.circular(14),
                      ),
                      child: const Row(
                        children: [
                          SizedBox(width: 14),
                          Icon(Icons.search, color: NikamaColors.lightGray, size: 20),
                          SizedBox(width: 10),
                          Text(
                            'Buscar restaurantes, productos...',
                            style: TextStyle(
                              fontFamily: 'Inter',
                              fontSize: 13,
                              color: NikamaColors.lightGray,
                            ),
                          ),
                        ],
                      ),
                    ),
                  ],
                ),
              ),

              const SizedBox(height: 24),

              // Sección Categorías
              Padding(
                padding: const EdgeInsets.symmetric(horizontal: 20),
                child: Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    const Text('Categorías',
                        style: TextStyle(
                          fontFamily: 'Inter',
                          fontSize: 18,
                          fontWeight: FontWeight.w700,
                          color: NikamaColors.black,
                        )),
                    Text('Ver todo',
                        style: TextStyle(
                          fontFamily: 'Inter',
                          fontSize: 13,
                          fontWeight: FontWeight.w600,
                          color: NikamaColors.primary,
                        )),
                  ],
                ),
              ),
              const SizedBox(height: 14),
              
              // Cargar categorías reales
              catalog.isLoading && catalog.categories.isEmpty
                  ? const Center(child: CircularProgressIndicator(color: NikamaColors.primary))
                  : SizedBox(
                      height: 90,
                      child: ListView.builder(
                        scrollDirection: Axis.horizontal,
                        padding: const EdgeInsets.symmetric(horizontal: 16),
                        itemCount: catalog.categories.isNotEmpty 
                            ? catalog.categories.length 
                            : _defaultCategories.length,
                        itemBuilder: (_, i) {
                          if (catalog.categories.isNotEmpty) {
                            final cat = catalog.categories[i];
                            return GestureDetector(
                              onTap: () => catalog.selectCategory(cat.id),
                              child: _CategoryChip(
                                emoji: _mapCategoryIcon(cat.icon ?? ''),
                                label: cat.name,
                              ),
                            );
                          } else {
                            return _CategoryChip(
                              emoji: _defaultCategories[i]['emoji']!,
                              label: _defaultCategories[i]['label']!,
                            );
                          }
                        },
                      ),
                    ),

              const SizedBox(height: 24),

              // Sección Destacados
              const Padding(
                padding: EdgeInsets.symmetric(horizontal: 20),
                child: Text('Destacados',
                    style: TextStyle(
                      fontFamily: 'Inter',
                      fontSize: 18,
                      fontWeight: FontWeight.w700,
                      color: NikamaColors.black,
                    )),
              ),
              const SizedBox(height: 14),
              
              // Cargar destacados reales
              catalog.isLoading && catalog.featuredProducts.isEmpty
                  ? const Center(child: CircularProgressIndicator(color: NikamaColors.primary))
                  : SizedBox(
                      height: 200,
                      child: ListView.builder(
                        scrollDirection: Axis.horizontal,
                        padding: const EdgeInsets.symmetric(horizontal: 16),
                        itemCount: catalog.featuredProducts.isNotEmpty 
                            ? catalog.featuredProducts.length 
                            : 5,
                        itemBuilder: (_, i) {
                          if (catalog.featuredProducts.isNotEmpty) {
                            final product = catalog.featuredProducts[i];
                            return GestureDetector(
                              onTap: () => Navigator.push(
                                context,
                                MaterialPageRoute(
                                  builder: (_) =>
                                      ProductDetailScreen(product: product),
                                ),
                              ),
                              child: _RealProductCard(product: product),
                            );
                          } else {
                            return _FeaturedBusinessCard(index: i);
                          }
                        },
                      ),
                    ),

              // Banner promocional
              Padding(
                padding: const EdgeInsets.symmetric(horizontal: 20),
                child: Container(
                  width: double.infinity,
                  padding: const EdgeInsets.all(20),
                  decoration: BoxDecoration(
                    gradient: const LinearGradient(
                      colors: [Color(0xFFF5C518), Color(0xFFDBA500)],
                      begin: Alignment.topLeft,
                      end: Alignment.bottomRight,
                    ),
                    borderRadius: BorderRadius.circular(20),
                  ),
                  child: Row(
                    children: [
                      Expanded(
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            const Text(
                              '¡Oferta especial!',
                              style: TextStyle(
                                fontFamily: 'Inter',
                                fontSize: 18,
                                fontWeight: FontWeight.w800,
                                color: NikamaColors.black,
                              ),
                            ),
                            const SizedBox(height: 6),
                            const Text(
                              '20% de descuento en tu\nprimera orden del día',
                              style: TextStyle(
                                fontFamily: 'Inter',
                                fontSize: 13,
                                color: NikamaColors.darkGray,
                              ),
                            ),
                            const SizedBox(height: 14),
                            Container(
                              padding: const EdgeInsets.symmetric(
                                  horizontal: 16, vertical: 8),
                              decoration: BoxDecoration(
                                color: NikamaColors.black,
                                borderRadius: BorderRadius.circular(8),
                              ),
                              child: const Text(
                                'Pedir ahora',
                                style: TextStyle(
                                  fontFamily: 'Inter',
                                  fontSize: 12,
                                  fontWeight: FontWeight.w700,
                                  color: NikamaColors.white,
                                ),
                              ),
                            ),
                          ],
                        ),
                      ),
                      const Text('🍔', style: TextStyle(fontSize: 60)),
                    ],
                  ),
                ),
              ),
              const SizedBox(height: 32),
            ],
          ),
        ),
      ),
    );
  }

  static const _defaultCategories = [
    {'emoji': '🍔', 'label': 'Hamburguesas'},
    {'emoji': '🍕', 'label': 'Pizzas'},
    {'emoji': '🍱', 'label': 'Sushi'},
    {'emoji': '🍗', 'label': 'Pollos'},
    {'emoji': '🥗', 'label': 'Ensaladas'},
    {'emoji': '🍜', 'label': 'Sopas'},
    {'emoji': '🥤', 'label': 'Bebidas'},
    {'emoji': '🍰', 'label': 'Postres'},
  ];
}

class _RealProductCard extends StatelessWidget {
  final ProductModel product;
  const _RealProductCard({required this.product});

  @override
  Widget build(BuildContext context) {
    return Container(
      width: 160,
      margin: const EdgeInsets.only(right: 14),
      decoration: BoxDecoration(
        color: NikamaColors.surfaceWhite,
        borderRadius: BorderRadius.circular(16),
        boxShadow: [
          BoxShadow(
            color: NikamaColors.black.withOpacity(0.07),
            blurRadius: 14,
            offset: const Offset(0, 4),
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          // Imagen del producto real
          Container(
            height: 100,
            width: double.infinity,
            decoration: const BoxDecoration(
              gradient: NikamaColors.heroGradient,
              borderRadius: BorderRadius.vertical(top: Radius.circular(16)),
            ),
            child: product.mainImageUrl != null && product.mainImageUrl!.isNotEmpty
                ? ClipRRect(
                    borderRadius: const BorderRadius.vertical(top: Radius.circular(16)),
                    child: Image.network(
                      product.mainImageUrl!,
                      fit: BoxFit.cover,
                      errorBuilder: (_, __, ___) => const Center(
                        child: Text('🍽️', style: TextStyle(fontSize: 44)),
                      ),
                    ),
                  )
                : const Center(
                    child: Text('🍽️', style: TextStyle(fontSize: 44)),
                  ),
          ),
          Padding(
            padding: const EdgeInsets.all(10),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  product.name,
                  style: const TextStyle(
                    fontFamily: 'Inter',
                    fontSize: 13,
                    fontWeight: FontWeight.w700,
                    color: NikamaColors.black,
                  ),
                  maxLines: 1,
                  overflow: TextOverflow.ellipsis,
                ),
                const SizedBox(height: 4),
                Text(
                  'S/. ${product.price.toStringAsFixed(2)}',
                  style: TextStyle(
                    fontFamily: 'Inter',
                    fontSize: 12,
                    fontWeight: FontWeight.w800,
                    color: NikamaColors.primary,
                  ),
                ),
                const SizedBox(height: 6),
                Row(
                  children: [
                    const Icon(Icons.star_rounded,
                        color: NikamaColors.primary, size: 14),
                    const SizedBox(width: 3),
                    Text(
                      product.ratingAverage.toStringAsFixed(1),
                      style: const TextStyle(
                        fontFamily: 'Inter',
                        fontSize: 11,
                        fontWeight: FontWeight.w600,
                        color: NikamaColors.black,
                      ),
                    ),
                    const SizedBox(width: 8),
                    const Icon(Icons.access_time_rounded,
                        color: NikamaColors.lightGray, size: 13),
                    const SizedBox(width: 3),
                    Text(
                      '${product.preparationTimeMinutes ?? 15} min',
                      style: const TextStyle(
                        fontFamily: 'Inter',
                        fontSize: 11,
                        color: NikamaColors.lightGray,
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

class _CategoryChip extends StatelessWidget {
  final String emoji;
  final String label;
  const _CategoryChip({required this.emoji, required this.label});

  @override
  Widget build(BuildContext context) {
    return Container(
      margin: const EdgeInsets.only(right: 12),
      width: 72,
      child: Column(
        children: [
          Container(
            width: 56,
            height: 56,
            decoration: BoxDecoration(
              color: NikamaColors.surfaceWhite,
              borderRadius: BorderRadius.circular(16),
              boxShadow: [
                BoxShadow(
                  color: NikamaColors.black.withOpacity(0.06),
                  blurRadius: 10,
                  offset: const Offset(0, 4),
                ),
              ],
            ),
            child: Center(
              child: Text(emoji, style: const TextStyle(fontSize: 26)),
            ),
          ),
          const SizedBox(height: 6),
          Text(
            label,
            textAlign: TextAlign.center,
            maxLines: 1,
            overflow: TextOverflow.ellipsis,
            style: const TextStyle(
              fontFamily: 'Inter',
              fontSize: 10,
              fontWeight: FontWeight.w500,
              color: NikamaColors.mediumGray,
            ),
          ),
        ],
      ),
    );
  }
}

class _FeaturedBusinessCard extends StatelessWidget {
  final int index;
  const _FeaturedBusinessCard({required this.index});

  static const _names = [
    'Burger House', 'Pizza Palace', 'Sushi Time', 'Pollo Rico', 'Green Bowl'
  ];
  static const _emojis = ['🍔', '🍕', '🍱', '🍗', '🥗'];
  static const _ratings = ['4.8', '4.6', '4.9', '4.5', '4.7'];
  static const _times = ['20-30', '25-35', '30-40', '15-25', '20-30'];

  @override
  Widget build(BuildContext context) {
    return Container(
      width: 160,
      margin: const EdgeInsets.only(right: 14),
      decoration: BoxDecoration(
        color: NikamaColors.surfaceWhite,
        borderRadius: BorderRadius.circular(16),
        boxShadow: [
          BoxShadow(
            color: NikamaColors.black.withOpacity(0.07),
            blurRadius: 14,
            offset: const Offset(0, 4),
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          // Imagen de negocio
          Container(
            height: 100,
            decoration: BoxDecoration(
              gradient: NikamaColors.heroGradient,
              borderRadius:
                  const BorderRadius.vertical(top: Radius.circular(16)),
            ),
            child: Center(
              child: Text(_emojis[index],
                  style: const TextStyle(fontSize: 44)),
            ),
          ),
          Padding(
            padding: const EdgeInsets.all(10),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  _names[index],
                  style: const TextStyle(
                    fontFamily: 'Inter',
                    fontSize: 13,
                    fontWeight: FontWeight.w700,
                    color: NikamaColors.black,
                  ),
                  maxLines: 1,
                  overflow: TextOverflow.ellipsis,
                ),
                const SizedBox(height: 6),
                Row(
                  children: [
                    const Icon(Icons.star_rounded,
                        color: NikamaColors.primary, size: 14),
                    const SizedBox(width: 3),
                    Text(
                      _ratings[index],
                      style: const TextStyle(
                        fontFamily: 'Inter',
                        fontSize: 11,
                        fontWeight: FontWeight.w600,
                        color: NikamaColors.black,
                      ),
                    ),
                    const SizedBox(width: 8),
                    const Icon(Icons.access_time_rounded,
                        color: NikamaColors.lightGray, size: 13),
                    const SizedBox(width: 3),
                    Text(
                      '${_times[index]} min',
                      style: const TextStyle(
                        fontFamily: 'Inter',
                        fontSize: 11,
                        color: NikamaColors.lightGray,
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

// ── TAB: EXPLORAR ────────────────────────────────────────────
class _ExploreTab extends StatelessWidget {
  const _ExploreTab();

  @override
  Widget build(BuildContext context) {
    return SafeArea(
      child: Padding(
        padding: const EdgeInsets.all(20),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            const Text('Explorar',
                style: TextStyle(
                  fontFamily: 'Inter',
                  fontSize: 24,
                  fontWeight: FontWeight.w800,
                  color: NikamaColors.black,
                )),
            const SizedBox(height: 16),
            Container(
              height: 48,
              decoration: BoxDecoration(
                color: NikamaColors.surfaceWhite,
                borderRadius: BorderRadius.circular(14),
                border: Border.all(color: const Color(0xFFE0E0E0)),
              ),
              child: const Row(
                children: [
                  SizedBox(width: 14),
                  Icon(Icons.search, color: NikamaColors.lightGray),
                  SizedBox(width: 10),
                  Text('Buscar negocios o productos...',
                      style: TextStyle(
                          fontFamily: 'Inter',
                          fontSize: 13,
                          color: NikamaColors.lightGray)),
                ],
              ),
            ),
            const SizedBox(height: 24),
            Expanded(
              child: Center(
                child: Column(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: [
                    const Text('🔍',
                        style: TextStyle(fontSize: 64)),
                    const SizedBox(height: 16),
                    const Text('Explora restaurantes\nconectando al backend',
                        textAlign: TextAlign.center,
                        style: TextStyle(
                          fontFamily: 'Inter',
                          fontSize: 16,
                          color: NikamaColors.lightGray,
                        )),
                    const SizedBox(height: 12),
                    Container(
                      padding: const EdgeInsets.symmetric(
                          horizontal: 14, vertical: 8),
                      decoration: BoxDecoration(
                        color: NikamaColors.primaryLight,
                        borderRadius: BorderRadius.circular(8),
                      ),
                      child: const Text(
                        'GET /api/v1/businesses',
                        style: TextStyle(
                          fontFamily: 'Inter',
                          fontSize: 12,
                          fontWeight: FontWeight.w600,
                          color: NikamaColors.primaryDark,
                        ),
                      ),
                    ),
                  ],
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }
}

// ── TAB: PEDIDOS ─────────────────────────────────────────────
class _OrdersTab extends StatefulWidget {
  const _OrdersTab();

  @override
  State<_OrdersTab> createState() => _OrdersTabState();
}

class _OrdersTabState extends State<_OrdersTab> {
  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      context.read<OrderProvider>().loadOrders();
    });
  }

  String _statusLabel(String? s) {
    switch (s) {
      case 'pending': return 'Pendiente';
      case 'confirmed': return 'Confirmado';
      case 'preparing': return 'Preparando';
      case 'ready_for_pickup': return 'Listo para recojo';
      case 'out_for_delivery': return 'En camino';
      case 'delivered': return 'Entregado';
      case 'cancelled': return 'Cancelado';
      default: return s ?? 'Desconocido';
    }
  }

  Color _statusColor(String? s) {
    switch (s) {
      case 'pending': return const Color(0xFFFF9800);
      case 'confirmed': return const Color(0xFF2196F3);
      case 'preparing': return const Color(0xFF9C27B0);
      case 'ready_for_pickup': return const Color(0xFF00BCD4);
      case 'out_for_delivery': return NikamaColors.primary;
      case 'delivered': return const Color(0xFF4CAF50);
      case 'cancelled': return NikamaColors.error;
      default: return NikamaColors.mediumGray;
    }
  }

  bool _isActive(String? s) {
    return s == 'pending' ||
        s == 'confirmed' ||
        s == 'preparing' ||
        s == 'ready_for_pickup' ||
        s == 'out_for_delivery';
  }

  @override
  Widget build(BuildContext context) {
    final orderProvider = context.watch<OrderProvider>();

    return SafeArea(
      child: Padding(
        padding: const EdgeInsets.all(20),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(
              children: [
                const Text('Mis Pedidos',
                    style: TextStyle(
                      fontFamily: 'Inter',
                      fontSize: 24,
                      fontWeight: FontWeight.w800,
                      color: NikamaColors.black,
                    )),
                const Spacer(),
                GestureDetector(
                  onTap: () => context.read<OrderProvider>().loadOrders(),
                  child: const Icon(Icons.refresh_rounded,
                      color: NikamaColors.mediumGray, size: 22),
                ),
              ],
            ),
            const SizedBox(height: 16),
            Expanded(
              child: orderProvider.loadingOrders
                  ? const Center(
                      child: CircularProgressIndicator(
                          color: NikamaColors.primary))
                  : orderProvider.orders.isEmpty
                      ? Center(
                          child: Column(
                            mainAxisAlignment: MainAxisAlignment.center,
                            children: [
                              const Text('📦', style: TextStyle(fontSize: 64)),
                              const SizedBox(height: 16),
                              const Text('Aún no tienes pedidos',
                                  style: TextStyle(
                                    fontFamily: 'Inter',
                                    fontSize: 18,
                                    fontWeight: FontWeight.w700,
                                    color: NikamaColors.black,
                                  )),
                              const SizedBox(height: 8),
                              const Text(
                                  'Tus pedidos aparecerán aquí\ncuando hagas una compra',
                                  textAlign: TextAlign.center,
                                  style: TextStyle(
                                    fontFamily: 'Inter',
                                    fontSize: 14,
                                    color: NikamaColors.lightGray,
                                  )),
                            ],
                          ),
                        )
                      : ListView.builder(
                          itemCount: orderProvider.orders.length,
                          itemBuilder: (_, i) {
                            final order = orderProvider.orders[i];
                            final status = order['status'] as String?;
                            final active = _isActive(status);
                            final total = order['total']?.toString() ?? '--';
                            final orderNumber =
                                order['order_number'] as String? ?? 'NKM-???';
                            final orderId =
                                order['id'] as String? ?? '';

                            return GestureDetector(
                              onTap: active
                                  ? () => Navigator.push(
                                        context,
                                        MaterialPageRoute(
                                          builder: (_) => OrderTrackingScreen(
                                            orderId: orderId,
                                            orderNumber: orderNumber,
                                            order: order,
                                          ),
                                        ),
                                      )
                                  : null,
                              child: Container(
                                margin: const EdgeInsets.only(bottom: 12),
                                padding: const EdgeInsets.all(14),
                                decoration: BoxDecoration(
                                  color: NikamaColors.surfaceWhite,
                                  borderRadius: BorderRadius.circular(16),
                                  border: active
                                      ? Border.all(
                                          color: NikamaColors.primary,
                                          width: 2)
                                      : null,
                                  boxShadow: [
                                    BoxShadow(
                                      color:
                                          NikamaColors.black.withOpacity(0.05),
                                      blurRadius: 10,
                                      offset: const Offset(0, 2),
                                    ),
                                  ],
                                ),
                                child: Column(
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  children: [
                                    Row(
                                      children: [
                                        Text(
                                          orderNumber,
                                          style: const TextStyle(
                                            fontFamily: 'Inter',
                                            fontSize: 15,
                                            fontWeight: FontWeight.w800,
                                            color: NikamaColors.black,
                                          ),
                                        ),
                                        const Spacer(),
                                        Container(
                                          padding: const EdgeInsets.symmetric(
                                              horizontal: 10, vertical: 4),
                                          decoration: BoxDecoration(
                                            color: _statusColor(status)
                                                .withOpacity(0.12),
                                            borderRadius:
                                                BorderRadius.circular(8),
                                          ),
                                          child: Text(
                                            _statusLabel(status),
                                            style: TextStyle(
                                              fontFamily: 'Inter',
                                              fontSize: 11,
                                              fontWeight: FontWeight.w700,
                                              color: _statusColor(status),
                                            ),
                                          ),
                                        ),
                                      ],
                                    ),
                                    const SizedBox(height: 8),
                                    Row(
                                      mainAxisAlignment:
                                          MainAxisAlignment.spaceBetween,
                                      children: [
                                        Text(
                                          'Total: S/. $total',
                                          style: const TextStyle(
                                            fontFamily: 'Inter',
                                            fontSize: 13,
                                            color: NikamaColors.mediumGray,
                                          ),
                                        ),
                                        if (active)
                                          Text(
                                            'Rastrear →',
                                            style: TextStyle(
                                              fontFamily: 'Inter',
                                              fontSize: 12,
                                              fontWeight: FontWeight.w700,
                                              color: NikamaColors.primary,
                                            ),
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
      ),
    );
  }
}

// ── TAB: PERFIL ──────────────────────────────────────────────
class _ProfileTab extends StatelessWidget {
  const _ProfileTab();

  @override
  Widget build(BuildContext context) {
    final user = context.watch<AuthProvider>().user;

    return SafeArea(
      child: SingleChildScrollView(
        child: Column(
          children: [
            // Header perfil
            Container(
              width: double.infinity,
              padding: const EdgeInsets.fromLTRB(20, 32, 20, 28),
              decoration: const BoxDecoration(
                gradient: NikamaColors.heroGradient,
                borderRadius: BorderRadius.only(
                  bottomLeft: Radius.circular(32),
                  bottomRight: Radius.circular(32),
                ),
              ),
              child: Column(
                children: [
                  Container(
                    width: 80,
                    height: 80,
                    decoration: BoxDecoration(
                      color: NikamaColors.primary,
                      borderRadius: BorderRadius.circular(20),
                    ),
                    child: Center(
                      child: Text(
                        (user?.firstName.isNotEmpty == true)
                            ? user!.firstName[0].toUpperCase()
                            : 'U',
                        style: const TextStyle(
                          fontFamily: 'Inter',
                          fontSize: 32,
                          fontWeight: FontWeight.w800,
                          color: NikamaColors.black,
                        ),
                      ),
                    ),
                  ),
                  const SizedBox(height: 14),
                  Text(
                    user?.fullName ?? 'Usuario',
                    style: const TextStyle(
                      fontFamily: 'Inter',
                      fontSize: 20,
                      fontWeight: FontWeight.w700,
                      color: NikamaColors.white,
                    ),
                  ),
                  const SizedBox(height: 4),
                  Text(
                    user?.email ?? '',
                    style: const TextStyle(
                      fontFamily: 'Inter',
                      fontSize: 13,
                      color: NikamaColors.lightGray,
                    ),
                  ),
                  const SizedBox(height: 10),
                  Container(
                    padding: const EdgeInsets.symmetric(
                        horizontal: 14, vertical: 6),
                    decoration: BoxDecoration(
                      color: NikamaColors.primary,
                      borderRadius: BorderRadius.circular(20),
                    ),
                    child: Text(
                      user?.primaryRole.toUpperCase() ?? 'CLIENTE',
                      style: const TextStyle(
                        fontFamily: 'Inter',
                        fontSize: 11,
                        fontWeight: FontWeight.w700,
                        color: NikamaColors.black,
                      ),
                    ),
                  ),
                ],
              ),
            ),

            // Opciones de perfil
            Padding(
              padding: const EdgeInsets.all(20),
              child: Column(
                children: [
                  _ProfileOption(
                      icon: Icons.person_outline,
                      label: 'Mi información',
                      onTap: () {}),
                  _ProfileOption(
                      icon: Icons.location_on_outlined,
                      label: 'Mis direcciones',
                      onTap: () {}),
                  _ProfileOption(
                      icon: Icons.receipt_long_outlined,
                      label: 'Historial de pedidos',
                      onTap: () {}),
                  _ProfileOption(
                      icon: Icons.favorite_outline,
                      label: 'Favoritos',
                      onTap: () {}),
                  _ProfileOption(
                      icon: Icons.notifications_outlined,
                      label: 'Notificaciones',
                      onTap: () {}),
                  _ProfileOption(
                      icon: Icons.help_outline,
                      label: 'Ayuda y soporte',
                      onTap: () {}),
                  const SizedBox(height: 8),
                  // Logout
                  Container(
                    width: double.infinity,
                    decoration: BoxDecoration(
                      color: NikamaColors.surfaceWhite,
                      borderRadius: BorderRadius.circular(14),
                      border: Border.all(color: const Color(0xFFEEEEEE)),
                    ),
                    child: ListTile(
                      leading: const Icon(Icons.logout,
                          color: NikamaColors.error),
                      title: const Text(
                        'Cerrar sesión',
                        style: TextStyle(
                          fontFamily: 'Inter',
                          fontSize: 14,
                          fontWeight: FontWeight.w600,
                          color: NikamaColors.error,
                        ),
                      ),
                      onTap: () async {
                        await context.read<AuthProvider>().logout();
                        if (context.mounted) {
                          Navigator.pushAndRemoveUntil(
                            context,
                            MaterialPageRoute(
                                builder: (_) => const LoginScreen()),
                            (_) => false,
                          );
                        }
                      },
                    ),
                  ),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }
}

class _ProfileOption extends StatelessWidget {
  final IconData icon;
  final String label;
  final VoidCallback onTap;

  const _ProfileOption({
    required this.icon,
    required this.label,
    required this.onTap,
  });

  @override
  Widget build(BuildContext context) {
    return Container(
      margin: const EdgeInsets.only(bottom: 10),
      decoration: BoxDecoration(
        color: NikamaColors.surfaceWhite,
        borderRadius: BorderRadius.circular(14),
        border: Border.all(color: const Color(0xFFEEEEEE)),
      ),
      child: ListTile(
        leading: Container(
          width: 38,
          height: 38,
          decoration: BoxDecoration(
            color: NikamaColors.primaryLight,
            borderRadius: BorderRadius.circular(10),
          ),
          child: Icon(icon, color: NikamaColors.black, size: 20),
        ),
        title: Text(
          label,
          style: const TextStyle(
            fontFamily: 'Inter',
            fontSize: 14,
            fontWeight: FontWeight.w500,
            color: NikamaColors.black,
          ),
        ),
        trailing: const Icon(Icons.arrow_forward_ios,
            color: NikamaColors.lightGray, size: 14),
        onTap: onTap,
      ),
    );
  }
}
