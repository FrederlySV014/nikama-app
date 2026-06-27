# Guía de Desarrollo para App Móvil Flutter (Nikama)

**Contexto para la IA de Flutter:**
Estás desarrollando la aplicación móvil para el proyecto **Nikama**, una plataforma de marketplace y delivery (estilo Uber Eats / Rappi). El backend ya está desarrollado en **Laravel 13** y expone una API RESTFul en la ruta `/api/v1/`.

Tu objetivo es vincular el proyecto existente de Flutter (que actualmente está vacío o recién creado) con esta API y desarrollar las primeras vistas.

---

## 1. Configuración Inicial y Conexión al Backend

### Variables de Entorno (Constantes)
Crea un archivo o clase de constantes en Flutter (por ejemplo, `lib/core/constants/api_constants.dart`) para almacenar la URL base.
*   **Base URL (Emulador Android):** `http://10.0.2.2:8000/api/v1`
*   **Base URL (Dispositivo Físico/iOS):** Usa la IP local de la máquina de desarrollo (ej. `http://192.168.X.X:8000/api/v1`).
*   **Headers por defecto:** `Accept: application/json` y `Content-Type: application/json`.

### Paquetes Recomendados
Agrega estas dependencias al `pubspec.yaml`:
*   `dio` o `http` para peticiones de red.
*   `shared_preferences` o `flutter_secure_storage` para guardar el token de sesión y el rol del usuario.
*   `provider`, `riverpod` o `bloc` para gestión de estado (a tu elección según las mejores prácticas).

---

## 2. Autenticación y Manejo de Sesión (El Login)

El sistema utiliza **Laravel Sanctum**. Al iniciar sesión, el servidor devuelve un token y los datos del usuario, incluyendo su **rol** (es vital para saber a qué pantalla redirigir).

### Endpoint de Login
*   **URL:** `POST /login`
*   **Body:**
    ```json
    {
      "email": "correo@ejemplo.com",
      "password": "Password123"
    }
    ```
*   **Respuesta Exitosa (200 OK):**
    ```json
    {
      "message": "Login successful",
      "data": {
        "user": {
          "id": 1,
          "name": "Nombre Usuario",
          "email": "correo@ejemplo.com",
          "role": "customer" // Puede ser: "customer", "driver", "seller", "admin"
        },
        "token": "1|abcdef12345..."
      }
    }
    ```

### Flujo de Vistas en Flutter (Paso a Paso)

#### Paso 1: Vista `Splash/Loading` (Pantalla Inicial)
*   Al abrir la app, lee el almacenamiento local (`secure_storage`).
*   Si **hay un token guardado**, redirige al usuario a la pantalla principal correspondiente a su rol.
*   Si **no hay token**, redirige a la pantalla de Login.

#### Paso 2: Vista `LoginScreen`
*   **UI:** Dos campos de texto (`Email` y `Contraseña`) y un botón de "Iniciar Sesión". Un enlace a "Crear cuenta" en la parte inferior.
*   **Lógica:** Al presionar "Iniciar Sesión", envía la petición al endpoint `POST /login`.
*   **Acción tras Login Exitoso:** 
    1. Guarda el `token` localmente.
    2. Guarda el `role` y datos del usuario localmente.
    3. Pasa el `token` en el header `Authorization: Bearer <token>` de todas las peticiones futuras.
    4. Redirige a la pantalla base según el rol (ver Paso 3).
*   **Manejo de Errores (422 o 401):** Muestra un `SnackBar` o diálogo si las credenciales son incorrectas o faltan datos (Lee el mensaje desde la respuesta del servidor).

#### Paso 3: Enrutador Basado en Roles (Role Router)
Una vez autenticado, la aplicación debe mostrar interfaces completamente distintas según el tipo de usuario. Crea una lógica de navegación que lea el rol:

1.  **Si rol == `customer` (Cliente / Usuario que compra):**
    *   Redirigir a `CustomerMainScreen` (con Bottom Navigation Bar: Inicio, Buscar, Carrito, Perfil).
    *   *Nota:* Aquí listaremos los negocios y se podrán crear pedidos.

2.  **Si rol == `driver` (Repartidor):**
    *   Redirigir a `DriverMainScreen`.
    *   *Nota:* Aquí se verán los pedidos disponibles para aceptar y el estado de entrega actual.

3.  **Si rol == `seller` (Vendedor / Negocio):**
    *   Redirigir a `SellerMainScreen`.
    *   *Nota:* Panel para que el dueño del negocio vea los pedidos entrantes y los ponga en estado "preparando".

4.  **Si rol == `admin` o `superadmin` (Administrador):**
    *   Redirigir a `AdminMainScreen`.
    *   *Nota:* Vista general de métricas o gestión de usuarios. (En móvil suele ser más limitada que en web).

---

## 3. Manejo de Registro (Signup)

Si el usuario no tiene cuenta y presiona "Crear cuenta" en el Login, envíalo a una pantalla donde primero deba **elegir su tipo de cuenta**:
*   *¿Qué deseas ser?* -> Cliente | Repartidor | Vendedor

Dependiendo de la selección, consumir los endpoints de registro respectivos (Todos requieren enviar nombre, email, teléfono, contraseña):
*   `POST /register/customer`
*   `POST /register/driver`
*   `POST /register/seller`

---

## 4. Instrucciones Finales para el Agente Flutter

1.  **Estructura Limpia:** Crea carpetas separadas para `screens/` (las vistas), `services/` (llamadas a la API) y `models/` (clases de Dart que mapean los JSON).
2.  **Manejo de Errores Global:** Configura un interceptor en tu cliente HTTP para capturar errores `401 Unauthorized`. Si ocurre, borra el token local y envía al usuario de regreso a `LoginScreen`.
3.  **Comienza ya:** Inicia creando la estructura de carpetas, implementando el cliente HTTP con la URL base, y construyendo la vista `LoginScreen` con la lógica de redirección basada en roles descrita en esta guía.
