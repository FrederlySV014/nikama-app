import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../data/providers/auth_provider.dart';
import '../../core/constants/app_theme.dart';
import 'login_screen.dart';
import '../customer/customer_home_screen.dart';
import '../driver/driver_home_screen.dart';

// ============================================================
// NIKAMA — Pantalla de Registro
// Dos tabs: Cliente | Repartidor
// ============================================================

class RegisterScreen extends StatefulWidget {
  const RegisterScreen({super.key});

  @override
  State<RegisterScreen> createState() => _RegisterScreenState();
}

class _RegisterScreenState extends State<RegisterScreen>
    with SingleTickerProviderStateMixin {
  late TabController _tabController;

  // Campos comunes
  final _firstNameCtrl = TextEditingController();
  final _lastNameCtrl = TextEditingController();
  final _emailCtrl = TextEditingController();
  final _passwordCtrl = TextEditingController();
  final _confirmPasswordCtrl = TextEditingController();
  final _phoneCtrl = TextEditingController();
  final _dniCtrl = TextEditingController();

  // Campos específicos driver
  final _vehiclePlateCtrl = TextEditingController();
  final _licenseCtrl = TextEditingController();
  String _vehicleType = 'motorcycle';

  bool _obscurePass = true;
  bool _obscureConfirm = true;
  final _formKeyCustomer = GlobalKey<FormState>();
  final _formKeyDriver = GlobalKey<FormState>();

  @override
  void initState() {
    super.initState();
    _tabController = TabController(length: 2, vsync: this);
  }

  @override
  void dispose() {
    _tabController.dispose();
    _firstNameCtrl.dispose();
    _lastNameCtrl.dispose();
    _emailCtrl.dispose();
    _passwordCtrl.dispose();
    _confirmPasswordCtrl.dispose();
    _phoneCtrl.dispose();
    _dniCtrl.dispose();
    _vehiclePlateCtrl.dispose();
    _licenseCtrl.dispose();
    super.dispose();
  }

  Future<void> _registerCustomer() async {
    if (!_formKeyCustomer.currentState!.validate()) return;
    final auth = context.read<AuthProvider>();
    final ok = await auth.registerCustomer(
      firstName: _firstNameCtrl.text.trim(),
      lastName: _lastNameCtrl.text.trim(),
      email: _emailCtrl.text.trim(),
      password: _passwordCtrl.text,
      passwordConfirmation: _confirmPasswordCtrl.text,
      phone: _phoneCtrl.text.trim(),
      dni: _dniCtrl.text.trim(),
    );
    if (ok && mounted) {
      Navigator.pushReplacement(context,
          MaterialPageRoute(builder: (_) => const CustomerHomeScreen()));
    }
  }

  Future<void> _registerDriver() async {
    if (!_formKeyDriver.currentState!.validate()) return;
    final auth = context.read<AuthProvider>();
    final ok = await auth.registerDriver(
      firstName: _firstNameCtrl.text.trim(),
      lastName: _lastNameCtrl.text.trim(),
      email: _emailCtrl.text.trim(),
      password: _passwordCtrl.text,
      passwordConfirmation: _confirmPasswordCtrl.text,
      phone: _phoneCtrl.text.trim(),
      dni: _dniCtrl.text.trim(),
      vehicleType: _vehicleType,
      vehiclePlate: _vehiclePlateCtrl.text.trim(),
      licenseNumber: _licenseCtrl.text.trim(),
    );
    if (ok && mounted) {
      Navigator.pushReplacement(context,
          MaterialPageRoute(builder: (_) => const DriverHomeScreen()));
    }
  }

  @override
  Widget build(BuildContext context) {
    final auth = context.watch<AuthProvider>();

    return Scaffold(
      backgroundColor: NikamaColors.offWhite,
      appBar: AppBar(
        title: const Text('Crear Cuenta'),
        backgroundColor: NikamaColors.surfaceWhite,
        elevation: 0,
        leading: IconButton(
          icon: const Icon(Icons.arrow_back_ios_new, size: 18),
          onPressed: () => Navigator.pop(context),
        ),
        bottom: TabBar(
          controller: _tabController,
          labelColor: NikamaColors.black,
          unselectedLabelColor: NikamaColors.lightGray,
          indicatorColor: NikamaColors.primary,
          indicatorWeight: 3,
          labelStyle: const TextStyle(
            fontFamily: 'Inter',
            fontWeight: FontWeight.w700,
            fontSize: 14,
          ),
          tabs: const [
            Tab(text: '🛒  Cliente'),
            Tab(text: '🏍️  Repartidor'),
          ],
        ),
      ),
      body: TabBarView(
        controller: _tabController,
        children: [
          // ── TAB CLIENTE ─────────────────────────────
          _buildForm(
            formKey: _formKeyCustomer,
            auth: auth,
            isDriver: false,
            onSubmit: _registerCustomer,
          ),
          // ── TAB DRIVER ──────────────────────────────
          _buildForm(
            formKey: _formKeyDriver,
            auth: auth,
            isDriver: true,
            onSubmit: _registerDriver,
          ),
        ],
      ),
    );
  }

  Widget _buildForm({
    required GlobalKey<FormState> formKey,
    required AuthProvider auth,
    required bool isDriver,
    required VoidCallback onSubmit,
  }) {
    return SingleChildScrollView(
      padding: const EdgeInsets.all(24),
      child: Form(
        key: formKey,
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            const SizedBox(height: 8),
            // Nombre y Apellido
            Row(
              children: [
                Expanded(child: _field(_firstNameCtrl, 'Nombre', Icons.person_outline)),
                const SizedBox(width: 12),
                Expanded(child: _field(_lastNameCtrl, 'Apellido', Icons.person_outline)),
              ],
            ),
            const SizedBox(height: 16),
            _field(_emailCtrl, 'Correo electrónico', Icons.email_outlined,
                type: TextInputType.emailAddress),
            const SizedBox(height: 16),
            _field(_phoneCtrl, 'Teléfono', Icons.phone_outlined,
                type: TextInputType.phone),
            const SizedBox(height: 16),
            _field(_dniCtrl, 'DNI', Icons.badge_outlined,
                type: TextInputType.number),
            const SizedBox(height: 16),
            _passwordField(_passwordCtrl, 'Contraseña', _obscurePass,
                () => setState(() => _obscurePass = !_obscurePass)),
            const SizedBox(height: 16),
            _passwordField(_confirmPasswordCtrl, 'Confirmar contraseña',
                _obscureConfirm,
                () => setState(() => _obscureConfirm = !_obscureConfirm)),

            // Campos extra Driver
            if (isDriver) ...[
              const SizedBox(height: 16),
              const Text('Tipo de Vehículo',
                  style: TextStyle(
                      fontFamily: 'Inter',
                      fontSize: 13,
                      fontWeight: FontWeight.w600,
                      color: NikamaColors.mediumGray)),
              const SizedBox(height: 8),
              Container(
                decoration: BoxDecoration(
                  color: NikamaColors.surfaceWhite,
                  borderRadius: BorderRadius.circular(12),
                  border: Border.all(color: const Color(0xFFE0E0E0), width: 1.5),
                ),
                child: Row(
                  children: [
                    _vehicleOption('motorcycle', '🏍️ Moto'),
                    _vehicleOption('car', '🚗 Auto'),
                    _vehicleOption('bicycle', '🚲 Bici'),
                  ],
                ),
              ),
              const SizedBox(height: 16),
              _field(_vehiclePlateCtrl, 'Placa del vehículo',
                  Icons.directions_car_outlined),
              const SizedBox(height: 16),
              _field(_licenseCtrl, 'Nro. de licencia', Icons.credit_card_outlined),
            ],

            // Error
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
                child: Text(
                  auth.errorMessage!,
                  style: const TextStyle(
                      fontFamily: 'Inter',
                      fontSize: 13,
                      color: NikamaColors.error),
                ),
              ),
            ],
            const SizedBox(height: 32),

            // Botón
            SizedBox(
              width: double.infinity,
              height: 54,
              child: ElevatedButton(
                onPressed: auth.isLoading ? null : onSubmit,
                style: ElevatedButton.styleFrom(
                  backgroundColor: NikamaColors.primary,
                  foregroundColor: NikamaColors.black,
                  elevation: 0,
                  shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(14)),
                ),
                child: auth.isLoading
                    ? const SizedBox(
                        width: 22,
                        height: 22,
                        child: CircularProgressIndicator(
                            strokeWidth: 2.5, color: NikamaColors.black))
                    : Text(
                        isDriver ? 'Registrarme como Repartidor' : 'Crear mi cuenta',
                        style: const TextStyle(
                          fontFamily: 'Inter',
                          fontSize: 15,
                          fontWeight: FontWeight.w700,
                        ),
                      ),
              ),
            ),
            const SizedBox(height: 16),
            Center(
              child: GestureDetector(
                onTap: () => Navigator.pushReplacement(context,
                    MaterialPageRoute(builder: (_) => const LoginScreen())),
                child: const Text(
                  '¿Ya tienes cuenta? Iniciar sesión',
                  style: TextStyle(
                    fontFamily: 'Inter',
                    fontSize: 13,
                    fontWeight: FontWeight.w600,
                    color: NikamaColors.mediumGray,
                    decoration: TextDecoration.underline,
                  ),
                ),
              ),
            ),
            const SizedBox(height: 16),
          ],
        ),
      ),
    );
  }

  Widget _field(
    TextEditingController ctrl,
    String hint,
    IconData icon, {
    TextInputType type = TextInputType.text,
  }) {
    return TextFormField(
      controller: ctrl,
      keyboardType: type,
      style: const TextStyle(
          fontFamily: 'Inter', fontSize: 14, color: NikamaColors.black),
      decoration: InputDecoration(
        hintText: hint,
        prefixIcon: Icon(icon, color: NikamaColors.lightGray, size: 20),
      ),
      validator: (v) =>
          (v == null || v.isEmpty) ? 'Campo requerido' : null,
    );
  }

  Widget _passwordField(TextEditingController ctrl, String hint, bool obscure,
      VoidCallback toggle) {
    return TextFormField(
      controller: ctrl,
      obscureText: obscure,
      style: const TextStyle(
          fontFamily: 'Inter', fontSize: 14, color: NikamaColors.black),
      decoration: InputDecoration(
        hintText: hint,
        prefixIcon: const Icon(Icons.lock_outline, color: NikamaColors.lightGray, size: 20),
        suffixIcon: IconButton(
          icon: Icon(
              obscure ? Icons.visibility_outlined : Icons.visibility_off_outlined,
              color: NikamaColors.lightGray,
              size: 20),
          onPressed: toggle,
        ),
      ),
      validator: (v) {
        if (v == null || v.isEmpty) return 'Campo requerido';
        if (v.length < 6) return 'Mínimo 6 caracteres';
        return null;
      },
    );
  }

  Widget _vehicleOption(String value, String label) {
    final selected = _vehicleType == value;
    return Expanded(
      child: GestureDetector(
        onTap: () => setState(() => _vehicleType = value),
        child: Container(
          padding: const EdgeInsets.symmetric(vertical: 12),
          decoration: BoxDecoration(
            color: selected ? NikamaColors.primary : Colors.transparent,
            borderRadius: BorderRadius.circular(10),
          ),
          child: Text(
            label,
            textAlign: TextAlign.center,
            style: TextStyle(
              fontFamily: 'Inter',
              fontSize: 12,
              fontWeight: selected ? FontWeight.w700 : FontWeight.w400,
              color: selected ? NikamaColors.black : NikamaColors.mediumGray,
            ),
          ),
        ),
      ),
    );
  }
}
