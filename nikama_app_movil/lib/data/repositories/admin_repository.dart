import '../../core/network/api_client.dart';

// ============================================================
// NIKAMA — Admin Repository
// ============================================================

class AdminRepository {
  final ApiClient _client = ApiClient();

  /// OBTENER DASHBOARD STATS → GET /api/v1/admin/dashboard
  Future<Map<String, dynamic>?> getDashboardStats() async {
    try {
      final response = await _client.dio.get('/admin/dashboard');
      return response.data;
    } catch (_) {
      return null;
    }
  }

  /// OBTENER REPARTIDORES
  Future<Map<String, dynamic>?> getDrivers({String status = 'all'}) async {
    try {
      final response = await _client.dio.get('/admin/drivers', queryParameters: {'status': status});
      return response.data;
    } catch (_) {
      return null;
    }
  }

  /// OBTENER NEGOCIOS
  Future<Map<String, dynamic>?> getBusinesses({String status = 'all'}) async {
    try {
      final response = await _client.dio.get('/admin/businesses', queryParameters: {'status': status});
      return response.data;
    } catch (_) {
      return null;
    }
  }

  /// APROBAR REPARTIDOR
  Future<bool> approveDriver(String id) async {
    try {
      final response = await _client.dio.post('/admin/applications/driver/$id/approve');
      return response.data['success'] == true;
    } catch (_) {
      return false;
    }
  }

  /// RECHAZAR REPARTIDOR
  Future<bool> rejectDriver(String id, String reason) async {
    try {
      final response = await _client.dio.post('/admin/applications/driver/$id/reject', data: {'rejected_reason': reason});
      return response.data['success'] == true;
    } catch (_) {
      return false;
    }
  }

  /// APROBAR NEGOCIO
  Future<bool> approveBusiness(String id) async {
    try {
      final response = await _client.dio.post('/admin/applications/business/$id/approve');
      return response.data['success'] == true;
    } catch (_) {
      return false;
    }
  }

  /// RECHAZAR NEGOCIO
  Future<bool> rejectBusiness(String id, String reason) async {
    try {
      final response = await _client.dio.post('/admin/applications/business/$id/reject', data: {'rejected_reason': reason});
      return response.data['success'] == true;
    } catch (_) {
      return false;
    }
  }

  /// SUSPENDER / ACTIVAR NEGOCIO
  Future<bool> toggleBusiness(String id) async {
    try {
      final response = await _client.dio.post('/admin/businesses/$id/toggle');
      return response.data['success'] == true;
    } catch (_) {
      return false;
    }
  }
}
