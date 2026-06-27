import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../data/providers/auth_provider.dart';
import '../../core/constants/app_theme.dart';
import 'register_screen.dart';
import '../customer/customer_home_screen.dart';
import '../driver/driver_home_screen.dart';
import '../seller/seller_home_screen.dart';
import '../admin/admin_home_screen.dart';

// ============================================================
// NIKAMA — Pantalla de Login
// Diseño: Blanco / Amarillo / Negro
// ============================================================

class LoginScreen extends StatefulWidget {
  const LoginScreen({super.key});

  @override
  State<LoginScreen> createState() => _LoginScreenState();
}

class _LoginScreenState extends State<LoginScreen>
    with SingleTickerProviderStateMixin {
  final _formKey = GlobalKey<FormState>();
  final _emailCtrl = TextEditingController();
  final _passwordCtrl = TextEditingController();
  bool _obscurePassword = true;
  late AnimationController _animController;
  late Animation<double> _fadeAnim;

  @override
  void initState() {
    super.initState();
    _animController = AnimationController(
      vsync: this,
      duration: const Duration(milliseconds: 800),
    );
    _fadeAnim = CurvedAnimation(
      parent: _animController,
      curve: Curves.easeOut,
    );
    _animController.forward();
  }

  @override
  void dispose() {
    _emailCtrl.dispose();
    _passwordCtrl.dispose();
    _animController.dispose();
    super.dispose();
  }

  void _redirectByRole(BuildContext context, AuthProvider auth) {
    final user = auth.user!;
    if (user.isSuperAdmin) {
      Navigator.pushReplacement(context,
          MaterialPageRoute(builder: (_) => const AdminHomeScreen()));
    } else if (user.isSeller) {
      Navigator.pushReplacement(context,
          MaterialPageRoute(builder: (_) => const SellerHomeScreen()));
    } else if (user.isDriver) {
      Navigator.pushReplacement(context,
          MaterialPageRoute(builder: (_) => const DriverHomeScreen()));
    } else {
      Navigator.pushReplacement(context,
          MaterialPageRoute(builder: (_) => const CustomerHomeScreen()));
    }
  }

  Future<void> _handleLogin() async {
    if (!_formKey.currentState!.validate()) return;
    final auth = context.read<AuthProvider>();
    final success = await auth.login(
      _emailCtrl.text.trim(),
      _passwordCtrl.text,
    );
    if (success && mounted) {
      _redirectByRole(context, auth);
    }
  }

  @override
  Widget build(BuildContext context) {
    final auth = context.watch<AuthProvider>();
    final size = MediaQuery.of(context).size;

    return Scaffold(
      backgroundColor: NikamaColors.offWhite,
      body: FadeTransition(
        opacity: _fadeAnim,
        child: SingleChildScrollView(
          child: Column(
            children: [
              // ── HERO HEADER ────────────────────────────────
              Container(
                width: double.infinity,
                height: size.height * 0.38,
                decoration: const BoxDecoration(
                  gradient: NikamaColors.heroGradient,
                  borderRadius: BorderRadius.only(
                    bottomLeft: Radius.circular(40),
                    bottomRight: Radius.circular(40),
                  ),
                ),
                child: SafeArea(
                  child: Column(
                    mainAxisAlignment: MainAxisAlignment.center,
                    children: [
                      // Logo
                      Container(
                        width: 80,
                        height: 80,
                        decoration: BoxDecoration(
                          color: NikamaColors.primary,
                          borderRadius: BorderRadius.circular(20),
                          boxShadow: [
                            BoxShadow(
                              color: NikamaColors.primary.withOpacity(0.4),
                              blurRadius: 20,
                              offset: const Offset(0, 8),
                            ),
                          ],
                        ),
                        child: const Icon(
                          Icons.delivery_dining,
                          size: 44,
                          color: NikamaColors.black,
                        ),
                      ),
                      const SizedBox(height: 16),
                      const Text(
                        'NIKAMA',
                        style: TextStyle(
                          fontFamily: 'Inter',
                          fontSize: 32,
                          fontWeight: FontWeight.w800,
                          color: NikamaColors.white,
                          letterSpacing: 4,
                        ),
                      ),
                      const SizedBox(height: 6),
                      Text(
                        'Tu marketplace favorito',
                        style: TextStyle(
                          fontFamily: 'Inter',
                          fontSize: 14,
                          fontWeight: FontWeight.w400,
                          color: NikamaColors.lightGray,
                        ),
                      ),
                    ],
                  ),
                ),
              ),

              // ── FORM CARD ──────────────────────────────────
              Padding(
                padding: const EdgeInsets.fromLTRB(24, 32, 24, 40),
                child: Form(
                  key: _formKey,
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      const Text(
                        'Iniciar Sesión',
                        style: TextStyle(
                          fontFamily: 'Inter',
                          fontSize: 24,
                          fontWeight: FontWeight.w800,
                          color: NikamaColors.black,
                        ),
                      ),
                      const SizedBox(height: 6),
                      const Text(
                        'Ingresa tus credenciales para continuar',
                        style: TextStyle(
                          fontFamily: 'Inter',
                          fontSize: 14,
                          color: NikamaColors.lightGray,
                        ),
                      ),
                      const SizedBox(height: 28),

                      // Email
                      _buildLabel('Correo electrónico'),
                      const SizedBox(height: 8),
                      TextFormField(
                        controller: _emailCtrl,
                        keyboardType: TextInputType.emailAddress,
                        style: const TextStyle(
                          fontFamily: 'Inter',
                          fontSize: 15,
                          color: NikamaColors.black,
                        ),
                        decoration: const InputDecoration(
                          hintText: 'ejemplo@correo.com',
                          prefixIcon: Icon(Icons.email_outlined,
                              color: NikamaColors.lightGray),
                        ),
                        validator: (v) {
                          if (v == null || v.isEmpty)
                            return 'Ingresa tu correo';
                          if (!v.contains('@')) return 'Correo inválido';
                          return null;
                        },
                      ),
                      const SizedBox(height: 20),

                      // Password
                      _buildLabel('Contraseña'),
                      const SizedBox(height: 8),
                      TextFormField(
                        controller: _passwordCtrl,
                        obscureText: _obscurePassword,
                        style: const TextStyle(
                          fontFamily: 'Inter',
                          fontSize: 15,
                          color: NikamaColors.black,
                        ),
                        decoration: InputDecoration(
                          hintText: '••••••••',
                          prefixIcon: const Icon(Icons.lock_outline,
                              color: NikamaColors.lightGray),
                          suffixIcon: IconButton(
                            icon: Icon(
                              _obscurePassword
                                  ? Icons.visibility_outlined
                                  : Icons.visibility_off_outlined,
                              color: NikamaColors.lightGray,
                            ),
                            onPressed: () => setState(
                                () => _obscurePassword = !_obscurePassword),
                          ),
                        ),
                        validator: (v) {
                          if (v == null || v.isEmpty)
                            return 'Ingresa tu contraseña';
                          if (v.length < 6) return 'Mínimo 6 caracteres';
                          return null;
                        },
                      ),

                      // Error message
                      if (auth.errorMessage != null) ...[
                        const SizedBox(height: 16),
                        Container(
                          width: double.infinity,
                          padding: const EdgeInsets.all(14),
                          decoration: BoxDecoration(
                            color: NikamaColors.error.withOpacity(0.08),
                            borderRadius: BorderRadius.circular(12),
                            border: Border.all(
                                color: NikamaColors.error.withOpacity(0.3)),
                          ),
                          child: Row(
                            children: [
                              const Icon(Icons.error_outline,
                                  color: NikamaColors.error, size: 18),
                              const SizedBox(width: 10),
                              Expanded(
                                child: Text(
                                  auth.errorMessage!,
                                  style: const TextStyle(
                                    fontFamily: 'Inter',
                                    fontSize: 13,
                                    color: NikamaColors.error,
                                  ),
                                ),
                              ),
                            ],
                          ),
                        ),
                      ],
                      const SizedBox(height: 32),

                      // Botón principal
                      SizedBox(
                        width: double.infinity,
                        height: 54,
                        child: ElevatedButton(
                          onPressed: auth.isLoading ? null : _handleLogin,
                          style: ElevatedButton.styleFrom(
                            backgroundColor: NikamaColors.primary,
                            foregroundColor: NikamaColors.black,
                            elevation: 0,
                            shape: RoundedRectangleBorder(
                              borderRadius: BorderRadius.circular(14),
                            ),
                          ),
                          child: auth.isLoading
                              ? const SizedBox(
                                  width: 22,
                                  height: 22,
                                  child: CircularProgressIndicator(
                                    strokeWidth: 2.5,
                                    color: NikamaColors.black,
                                  ),
                                )
                              : const Text(
                                  'Ingresar',
                                  style: TextStyle(
                                    fontFamily: 'Inter',
                                    fontSize: 16,
                                    fontWeight: FontWeight.w700,
                                    color: NikamaColors.black,
                                  ),
                                ),
                        ),
                      ),
                      const SizedBox(height: 20),

                      // Registro
                      Center(
                        child: Row(
                          mainAxisAlignment: MainAxisAlignment.center,
                          children: [
                            const Text(
                              '¿No tienes cuenta? ',
                              style: TextStyle(
                                fontFamily: 'Inter',
                                fontSize: 14,
                                color: NikamaColors.mediumGray,
                              ),
                            ),
                            GestureDetector(
                              onTap: () {
                                Navigator.push(
                                  context,
                                  MaterialPageRoute(
                                      builder: (_) =>
                                          const RegisterScreen()),
                                );
                              },
                              child: const Text(
                                'Regístrate aquí',
                                style: TextStyle(
                                  fontFamily: 'Inter',
                                  fontSize: 14,
                                  fontWeight: FontWeight.w700,
                                  color: NikamaColors.black,
                                  decoration: TextDecoration.underline,
                                ),
                              ),
                            ),
                          ],
                        ),
                      ),
                    ],
                  ),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildLabel(String text) {
    return Text(
      text,
      style: const TextStyle(
        fontFamily: 'Inter',
        fontSize: 13,
        fontWeight: FontWeight.w600,
        color: NikamaColors.mediumGray,
        letterSpacing: 0.3,
      ),
    );
  }
}
