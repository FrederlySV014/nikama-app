import 'package:flutter/foundation.dart';
import '../repositories/admin_repository.dart';

// ============================================================
// NIKAMA — Admin Provider (ChangeNotifier)
// Mantiene el estado del administrador (dashboard, solicitudes)
// ============================================================

class AdminProvider with ChangeNotifier {
  final AdminRepository _repo = AdminRepository();

  Map<String, dynamic>? _dashboardStats;
  List<dynamic> _pendingSellers = [];
  List<dynamic> _pendingDrivers = [];
  List<dynamic> _drivers = [];
  List<dynamic> _businesses = [];
  bool _isLoading = false;
  bool _isLoadingDrivers = false;
  bool _isLoadingBusinesses = false;

  Map<String, dynamic>? get dashboardStats => _dashboardStats;
  List<dynamic> get pendingSellers => _pendingSellers;
  List<dynamic> get pendingDrivers => _pendingDrivers;
  List<dynamic> get drivers => _drivers;
  List<dynamic> get businesses => _businesses;
  bool get isLoading => _isLoading;
  bool get isLoadingDrivers => _isLoadingDrivers;
  bool get isLoadingBusinesses => _isLoadingBusinesses;

  /// Cargar Dashboard del Admin
  Future<void> loadDashboard() async {
    _isLoading = true;
    notifyListeners();

    try {
      final response = await _repo.getDashboard();
      if (response != null && response['success'] == true) {
        final data = response['data'];
        
        _dashboardStats = data['stats'];
        _pendingSellers = data['pending_applications']['sellers'] ?? [];
        _pendingDrivers = data['pending_applications']['drivers'] ?? [];
      }
    } catch (_) {
      // Manejar error silenciosamente
    } finally {
      _isLoading = false;
      notifyListeners();
    }
  }

  /// Cargar Repartidores
  Future<void> loadDrivers({String status = 'all'}) async {
    _isLoadingDrivers = true;
    notifyListeners();
    try {
      final response = await _repo.getDrivers(status: status);
      if (response != null && response['success'] == true) {
        _drivers = response['data'] ?? [];
      }
    } catch (_) {
    } finally {
      _isLoadingDrivers = false;
      notifyListeners();
    }
  }

  /// Cargar Negocios
  Future<void> loadBusinesses({String status = 'all'}) async {
    _isLoadingBusinesses = true;
    notifyListeners();
    try {
      final response = await _repo.getBusinesses(status: status);
      if (response != null && response['success'] == true) {
        _businesses = response['data'] ?? [];
      }
    } catch (_) {
    } finally {
      _isLoadingBusinesses = false;
      notifyListeners();
    }
  }

  Future<bool> approveDriver(String id) async {
    final success = await _repo.approveDriver(id);
    if (success) {
      await loadDrivers();
      await loadDashboard();
    }
    return success;
  }

  Future<bool> rejectDriver(String id, String reason) async {
    final success = await _repo.rejectDriver(id, reason);
    if (success) {
      await loadDrivers();
      await loadDashboard();
    }
    return success;
  }

  Future<bool> approveBusiness(String id) async {
    final success = await _repo.approveBusiness(id);
    if (success) {
      await loadBusinesses();
      await loadDashboard();
    }
    return success;
  }

  Future<bool> rejectBusiness(String id, String reason) async {
    final success = await _repo.rejectBusiness(id, reason);
    if (success) {
      await loadBusinesses();
      await loadDashboard();
    }
    return success;
  }

  Future<bool> toggleBusiness(String id) async {
    final success = await _repo.toggleBusiness(id);
    if (success) {
      await loadBusinesses();
    }
    return success;
  }
}
