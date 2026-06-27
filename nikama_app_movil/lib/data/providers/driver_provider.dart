import 'package:flutter/foundation.dart';
import '../repositories/driver_repository.dart';

// ============================================================
// NIKAMA — Driver Provider (ChangeNotifier)
// Mantiene el estado del repartidor (dashboard, asignaciones)
// ============================================================

class DriverProvider with ChangeNotifier {
  final DriverRepository _repo = DriverRepository();

  List<dynamic> _assignments = [];
  Map<String, dynamic>? _profileStats;
  bool _isLoading = false;

  List<dynamic> get assignments => _assignments;
  Map<String, dynamic>? get profileStats => _profileStats;
  bool get isLoading => _isLoading;

  /// Cargar Dashboard del Repartidor
  Future<void> loadDashboard() async {
    _isLoading = true;
    notifyListeners();

    try {
      final response = await _repo.getDashboard();
      if (response != null && response['success'] == true) {
        final data = response['data'];
        _assignments = data['pending_assignments'] ?? [];
        
        // Mapear active_delivery si existe a assignments para mostrarlo
        if (data['active_delivery'] != null) {
           _assignments = [
             {
               'id': data['active_delivery']['id'],
               'status': data['active_delivery']['status'],
               'order': data['active_delivery']['order'],
               'delivery': {
                 'id': data['active_delivery']['id'],
                 'business_name': data['active_delivery']['business']['business_name'],
                 'address': data['active_delivery']['business']['address'],
               }
             },
             ..._assignments
           ];
        }

        _profileStats = {
          'today_completed_count': data['today_completed_count'] ?? 0,
          'today_failed_count': data['today_failed_count'] ?? 0,
        };
      }
    } catch (_) {
      // Manejar error silenciosamente
    } finally {
      _isLoading = false;
      notifyListeners();
    }
  }

  /// Aceptar Asignación
  Future<bool> acceptAssignment(String assignmentId) async {
    _isLoading = true;
    notifyListeners();

    final success = await _repo.acceptAssignment(assignmentId);
    if (success) {
      await loadDashboard(); // Recargar datos
    }

    _isLoading = false;
    notifyListeners();
    return success;
  }

  /// Completar Entrega
  Future<bool> completeDelivery(String deliveryId) async {
    _isLoading = true;
    notifyListeners();

    final success = await _repo.completeDelivery(deliveryId);
    if (success) {
      await loadDashboard(); // Recargar datos
    }

    _isLoading = false;
    notifyListeners();
    return success;
  }
}
