import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:provider/provider.dart';
import 'core/constants/app_theme.dart';
import 'data/providers/auth_provider.dart';
import 'data/providers/catalog_provider.dart';
import 'data/providers/cart_provider.dart';
import 'data/providers/order_provider.dart';
import 'data/providers/driver_provider.dart';
import 'data/providers/seller_provider.dart';
import 'data/providers/admin_provider.dart';
import 'ui/screens/login_screen.dart';
import 'ui/customer/customer_home_screen.dart';
import 'ui/driver/driver_home_screen.dart';
import 'ui/seller/seller_home_screen.dart';
import 'ui/admin/admin_home_screen.dart';

// ============================================================
// NIKAMA APP — Punto de entrada principal
// Color: Blanco | Amarillo (#F5C518) | Negro
// Backend: Laravel 13 + Sanctum | Roles: customer, driver, seller, super_admin
// ============================================================

void main() async {
  WidgetsFlutterBinding.ensureInitialized();

  // Forzar orientación portrait
  await SystemChrome.setPreferredOrientations([
    DeviceOrientation.portraitUp,
    DeviceOrientation.portraitDown,
  ]);

  // Status bar estilo oscuro (fondo claro)
  SystemChrome.setSystemUIOverlayStyle(
    const SystemUiOverlayStyle(
      statusBarColor: Colors.transparent,
      statusBarIconBrightness: Brightness.dark,
    ),
  );

  runApp(
    MultiProvider(
      providers: [
        ChangeNotifierProvider(create: (_) => AuthProvider()),
        ChangeNotifierProvider(create: (_) => CatalogProvider()),
        ChangeNotifierProvider(create: (_) => CartProvider()),
        ChangeNotifierProvider(create: (_) => OrderProvider()),
        ChangeNotifierProvider(create: (_) => DriverProvider()),
        ChangeNotifierProvider(create: (_) => SellerProvider()),
        ChangeNotifierProvider(create: (_) => AdminProvider()),
      ],
      child: const NikamaApp(),
    ),
  );
}

class NikamaApp extends StatelessWidget {
  const NikamaApp({super.key});

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      title: 'Nikama',
      debugShowCheckedModeBanner: false,
      theme: NikamaTheme.lightTheme,
      home: const _AppRouter(),
    );
  }
}

// ─────────────────────────────────────────────────────────────
// Router inicial: verifica sesión → redirige por rol
// ─────────────────────────────────────────────────────────────
class _AppRouter extends StatefulWidget {
  const _AppRouter();

  @override
  State<_AppRouter> createState() => _AppRouterState();
}

class _AppRouterState extends State<_AppRouter> {
  @override
  void initState() {
    super.initState();
    // Verificar sesión existente al abrir la app
    WidgetsBinding.instance.addPostFrameCallback((_) {
      context.read<AuthProvider>().checkAuthStatus();
    });
  }

  @override
  Widget build(BuildContext context) {
    final auth = context.watch<AuthProvider>();

    // Splash / loading mientras se verifica el token
    if (auth.status == AuthStatus.unknown) {
      return const _SplashScreen();
    }

    // No autenticado → Login
    if (auth.status == AuthStatus.unauthenticated) {
      return const LoginScreen();
    }

    // Autenticado → Redirigir según rol
    final user = auth.user;
    if (user == null) return const LoginScreen();

    if (user.isSuperAdmin) return const AdminHomeScreen();
    if (user.isSeller) return const SellerHomeScreen();
    if (user.isDriver) return const DriverHomeScreen();
    return const CustomerHomeScreen(); // default: customer
  }
}

// ─────────────────────────────────────────────────────────────
// Pantalla de Splash (verificando sesión)
// ─────────────────────────────────────────────────────────────
class _SplashScreen extends StatelessWidget {
  const _SplashScreen();

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: NikamaColors.black,
      body: Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            // Logo animado
            Container(
              width: 90,
              height: 90,
              decoration: BoxDecoration(
                color: NikamaColors.primary,
                borderRadius: BorderRadius.circular(24),
                boxShadow: [
                  BoxShadow(
                    color: NikamaColors.primary.withOpacity(0.5),
                    blurRadius: 30,
                    offset: const Offset(0, 10),
                  ),
                ],
              ),
              child: const Icon(
                Icons.delivery_dining,
                size: 50,
                color: NikamaColors.black,
              ),
            ),
            const SizedBox(height: 24),
            const Text(
              'NIKAMA',
              style: TextStyle(
                fontFamily: 'Inter',
                fontSize: 34,
                fontWeight: FontWeight.w800,
                color: NikamaColors.white,
                letterSpacing: 6,
              ),
            ),
            const SizedBox(height: 8),
            const Text(
              'Tu marketplace favorito',
              style: TextStyle(
                fontFamily: 'Inter',
                fontSize: 13,
                color: NikamaColors.lightGray,
                letterSpacing: 1,
              ),
            ),
            const SizedBox(height: 48),
            const SizedBox(
              width: 28,
              height: 28,
              child: CircularProgressIndicator(
                strokeWidth: 2.5,
                color: NikamaColors.primary,
              ),
            ),
          ],
        ),
      ),
    );
  }
}
