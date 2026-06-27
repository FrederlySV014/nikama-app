# Guía de Desarrollo para la App Móvil Flutter (Nikama)

Esta guía está diseñada para que tú, como IA de Flutter, configures, programes y vincules la aplicación móvil existente en Flutter con el backend en Laravel 13 del proyecto **Nikama**. El objetivo es conectar la app móvil con las APIs del servidor, gestionar la autenticación por tokens y dirigir a los usuarios a sus respectivas pantallas según sus roles.

---

## 📌 Contexto General de la Arquitectura
* **Nombre del Proyecto:** Nikama (App Móvil y Web)
* **Backend:** Laravel 13 (Servidor API corriendo localmente por defecto en `http://localhost:8000` o la IP local de la red).
* **Rutas API:** Prefijadas con `/api/v1/` y administradas mediante **Laravel Sanctum** (Autenticación basada en Bearer Tokens).
* **Formatos de datos:** Entrada y salida siempre en formato JSON limpio a través de **Resources** y **Requests** de Laravel.

---

## 🛠 Paso 1: Configurar Dependencias en Flutter (`pubspec.yaml`)
Para asegurar la comunicación HTTP, persistencia del token, y manejo de estado, asegúrate de tener añadidas estas dependencias:

```yaml
dependencies:
  flutter:
    sdk: flutter
  
  # Cliente HTTP potente para llamadas, interceptores y subida de archivos
  dio: ^5.4.0
  
  # Guardado seguro del Bearer Token en el llavero/keystore del dispositivo
  flutter_secure_storage: ^9.0.0
  
  # Gestión de estado (puedes usar Provider, Bloc, Riverpod o GetX; aquí asumiremos Provider/ChangeNotifier por simplicidad)
  provider: ^6.1.1
  
  # Manejo de mapas/geolocalización para conductores y rastreo de pedidos
  google_maps_flutter: ^2.5.3
  geolocator: ^10.1.0
```

---

## 🌐 Paso 2: Configuración del Cliente HTTP (Dio) y Base URL
Crea una clase de configuración del cliente API (`lib/services/api_service.dart`).
> ⚠️ **IMPORTANTE:** Si usas el emulador de Android, `localhost` apunta al emulador. Debes usar la IP `http://10.0.2.2:8000` o la IP física de la máquina en tu red local (ej: `http://192.168.1.50:8000`).

```dart
import 'package:dio/dio.dart';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';

class ApiService {
  final Dio dio = Dio();
  final _storage = const FlutterSecureStorage();

  // Cambia esto por tu IP local de red para pruebas en celular físico
  static const String baseUrl = 'http://10.0.2.2:8000/api/v1'; 

  ApiService() {
    dio.options.baseUrl = baseUrl;
    dio.options.connectTimeout = const Duration(seconds: 10);
    dio.options.receiveTimeout = const Duration(seconds: 10);
    dio.options.headers = {
      'Accept': 'application/json',
      'Content-Type': 'application/json',
    };

    // Interceptor para inyectar automáticamente el Token si existe
    dio.interceptors.add(
      InterceptorsWrapper(
        onRequest: (options, handler) async {
          final token = await _storage.read(key: 'auth_token');
          if (token != null) {
            options.headers['Authorization'] = 'Bearer $token';
          }
          return handler.next(options);
        },
        onError: (DioException e, handler) {
          // Si el servidor retorna 401 Unauthorized, maneja el deslogueo
          if (e.response?.statusCode == 401) {
            _storage.delete(key: 'auth_token');
            // Aquí puedes disparar una redirección al login
          }
          return handler.next(e);
        },
      ),
    );
  }
}
```

---

## 🔐 Paso 3: Flujo de Autenticación y Registro

### 1. Endpoint de Login (`POST /api/v1/auth/login`)
Envía las credenciales y recibe el token junto con el perfil del usuario y sus roles asignados.
* **Modelo del Usuario (`lib/models/user_model.dart`):**
  ```dart
  class UserModel {
    final String id;
    final String firstName;
    final String lastName;
    final String email;
    final String phone;
    final List<String> roles; // ['customer', 'driver', 'super_admin', 'seller']

    UserModel({
      required this.id,
      required this.firstName,
      required this.lastName,
      required this.email,
      required this.phone,
      required this.roles,
    });

    factory UserModel.fromJson(Map<String, dynamic> json) {
      return UserModel(
        id: json['id'],
        firstName: json['first_name'],
        lastName: json['last_name'],
        email: json['email'],
        phone: json['phone'] ?? '',
        roles: List<String>.from(json['roles']),
      );
    }
  }
  ```

* **Función de Login en `ApiService`:**
  ```dart
  Future<Map<String, dynamic>?> login(String email, String password) async {
    try {
      final response = await dio.post('/auth/login', data: {
        'email': email,
        'password': password,
      });

      if (response.statusCode == 200 && response.data['success'] == true) {
        final token = response.data['token'];
        final userData = response.data['user'];

        // Guardar token de manera segura
        await _storage.write(key: 'auth_token', value: token);
        
        return {
          'user': UserModel.fromJson(userData),
          'token': token,
        };
      }
    } on DioException catch (e) {
      // Manejar errores de validación (422) o credenciales incorrectas (401)
      throw e.response?.data['message'] ?? 'Error de autenticación';
    }
    return null;
  }
  ```

### 2. Endpoints de Registro
La aplicación tiene registros separados según el tipo de usuario:
* **Cliente (`POST /api/v1/auth/register/customer`):**
  Campos requeridos: `first_name`, `last_name`, `email`, `password`, `password_confirmation`, `phone`, `dni`.
* **Repartidor (`POST /api/v1/auth/register/driver`):**
  Campos requeridos: `first_name`, `last_name`, `email`, `password`, `password_confirmation`, `phone`, `dni`, `vehicle_type` (car, motorcycle, bicycle), `vehicle_plate`, `license_number`.

---

## 🚦 Paso 4: Enrutamiento y Navegación por Roles
El backend define cuatro roles principales en la constante del modelo `Role.php`:
1. `customer` (Cliente que realiza pedidos)
2. `driver` (Repartidor que entrega pedidos)
3. `seller` (Administrador de un negocio/restaurante)
4. `super_admin` (Administrador global del sistema)

Al iniciar sesión exitosamente, verifica la lista de `roles` del usuario para redireccionarlo a su flujo correcto:

```dart
void redirectUserByRole(BuildContext context, UserModel user) {
  if (user.roles.contains('super_admin')) {
    Navigator.pushReplacementNamed(context, '/super-admin-home');
  } else if (user.roles.contains('seller')) {
    Navigator.pushReplacementNamed(context, '/seller-home');
  } else if (user.roles.contains('driver')) {
    Navigator.pushReplacementNamed(context, '/driver-home');
  } else if (user.roles.contains('customer')) {
    Navigator.pushReplacementNamed(context, '/customer-home');
  } else {
    Navigator.pushReplacementNamed(context, '/unauthorized');
  }
}
```

---

## 🖥️ Paso 5: Pantallas del Flujo Inicial (Login en adelante)

Crea la siguiente estructura de carpetas en `lib/`:
```text
lib/
├── models/
│   └── user_model.dart
├── services/
│   └── api_service.dart
├── providers/
│   └── auth_provider.dart
└── screens/
    ├── auth/
    │   ├── login_screen.dart
    │   └── register_screen.dart
    ├── customer/
    │   └── customer_home_screen.dart
    ├── driver/
    │   └── driver_home_screen.dart
    ├── seller/
    │   └── seller_home_screen.dart
    └── admin/
        └── admin_home_screen.dart
```

### 1. Pantalla de Login (`lib/screens/auth/login_screen.dart`)
Debe contener:
* Formulario con inputs para `email` y `password`.
* Indicador de carga (Spinner) mientras se procesa la petición API.
* Gestión de alertas de error en caso de credenciales incorrectas o campos vacíos.
* Al tener éxito, llamar a `redirectUserByRole()`.

### 2. Vistas Específicas por Rol:

#### **A. Cliente (`customer_home_screen.dart`)**
Interactúa con los siguientes endpoints del cliente:
* **Categorías:** `GET /categories` (público)
* **Productos:** `GET /products` o `GET /products/featured` (públicos)
* **Carrito de compras:** 
  * Ver carrito: `GET /customer/cart` (requiere token)
  * Añadir producto: `POST /customer/add` (parámetros: `product_id` o `product_combo_id`, `quantity`, `options[]`)
* **Checkout:** `POST /customer/checkout` (parámetros: `address_selection_type` ('saved' o 'new'), `payment_method` ('cash', 'yape', 'plin', etc.))
* **Seguimiento del pedido en tiempo real:** `GET /customer/orders/{id}/track`

#### **B. Repartidor (`driver_home_screen.dart`)**
Interactúa con los endpoints del repartidor (debe estar aprobado por el administrador):
* **Dashboard / Tareas Pendientes:** `GET /driver/dashboard` (Lista asignaciones y entregas activas).
* **Aceptar Asignación:** `POST /driver/assignments/{assignmentId}/accept`
* **Rechazar Asignación:** `POST /driver/assignments/{assignmentId}/reject`
* **Emitir Ubicación en Tiempo Real (GPS):** `POST /driver/deliveries/{deliveryId}/emit-location` (parámetros: `latitude`, `longitude`). Ejecutar periódicamente mediante un Timer con la librería `geolocator`.
* **Completar Entrega:** `POST /driver/deliveries/{deliveryId}/complete`

#### **C. Negocio / Proveedor (`seller_home_screen.dart`)**
Si se requiere administrar el restaurante/negocio desde la app móvil:
* Carga de estadísticas y pedidos listos para despachar. (Para un flujo completo de gestión, el backend tiene rutas Seller protegidas en `web.php`, pero si implementas endpoints específicos de API para el vendedor, se pueden añadir en `routes/api.php`).

#### **D. Administrador (`admin_home_screen.dart`)**
* Pantallas informativas, aprobación de repartidores y visualización global de ventas.

---

## 🚀 Paso 6: Validación de Errores del Backend (Ejemplo en Flutter)
Cuando el backend en Laravel devuelve un error de validación (por ejemplo, si el correo electrónico ya está registrado), responderá con código `422`. 
Debes capturar ese JSON en la app móvil y renderizarlo en pantalla:

```dart
try {
  await authProvider.registerCustomer(...);
} catch (error) {
  // error contendrá los campos con problemas de validación devueltos por Laravel
  ScaffoldMessenger.of(context).showSnackBar(
    SnackBar(content: Text(error.toString())),
  );
}
```
El formato JSON devuelto por Laravel en caso de error de validación es:
```json
{
  "success": false,
  "errors": {
    "email": ["El campo correo electrónico ya ha sido registrado."],
    "password": ["La contraseña debe tener al menos 8 caracteres."]
  }
}
```
Asegúrate de parsear el campo `errors` u obtener el primer mensaje de error para mostrárselo al usuario de la app móvil.
