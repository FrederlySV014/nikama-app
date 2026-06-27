# Contrato de APIs de Nikama (Laravel para Flutter)

Este documento describe todas las rutas de la API (`routes/api.php`) disponibles para la aplicación móvil Flutter. Utiliza esta referencia para construir tus modelos de datos, servicios HTTP, repositorios y proveedores de estado.

---

## 🔒 Cabeceras Requeridas (Headers)
Para todas las peticiones, se deben enviar las siguientes cabeceras:
```http
Accept: application/json
Content-Type: application/json
```
Para rutas protegidas por autenticación, se debe añadir el token de tipo **Bearer**:
```http
Authorization: Bearer <auth_token>
```

---

## 🚪 Grupo 1: Autenticación y Registro

### 1. Iniciar Sesión (Login)
* **Endpoint:** `POST /api/v1/auth/login`
* **Cuerpo de la Petición (Payload):**
```json
{
  "email": "cliente@nikama.com",
  "password": "Password123"
}
```
* **Respuesta Exitosa (200 OK):**
```json
{
  "success": true,
  "token": "1|abCDeFGhIjK...",
  "user": {
    "id": "7ca641fa-e95c-473d-9d41-0f40fb1764eb",
    "first_name": "Juan",
    "last_name": "Pérez",
    "email": "cliente@nikama.com",
    "phone": "+51987654321",
    "dni": "76543210",
    "avatar_url": "https://url-servidor.com/storage/avatars/avatar.png",
    "is_active": true,
    "roles": ["customer"]
  }
}
```

### 2. Registrar Cliente (Register Customer)
* **Endpoint:** `POST /api/v1/auth/register/customer`
* **Cuerpo de la Petición (Payload):**
```json
{
  "first_name": "Pedro",
  "last_name": "Gomez",
  "email": "pedro@nikama.com",
  "password": "Password123",
  "password_confirmation": "Password123",
  "phone": "+51912345678",
  "dni": "87654321"
}
```
* **Respuesta Exitosa (201 Created):**
```json
{
  "success": true,
  "message": "Registro completado con éxito.",
  "token": "2|xyZAbCDe...",
  "user": {
    "id": "e93f92b7-a3f2-45e0-b6f1-da28fe1901a1",
    "first_name": "Pedro",
    "last_name": "Gomez",
    "email": "pedro@nikama.com",
    "phone": "+51912345678",
    "dni": "87654321",
    "avatar_url": null,
    "is_active": true,
    "roles": ["customer"]
  }
}
```

### 3. Registrar Repartidor (Register Driver)
* **Endpoint:** `POST /api/v1/auth/register/driver`
* **Cuerpo de la Petición (Payload):**
```json
{
  "first_name": "Carlos",
  "last_name": "Conductor",
  "email": "carlos@nikama.com",
  "password": "Password123",
  "password_confirmation": "Password123",
  "phone": "+51999888777",
  "dni": "70605040",
  "vehicle_type": "motorcycle", 
  "vehicle_plate": "ABC-123",
  "license_number": "Q70605040"
}
```
* **Respuesta Exitosa (201 Created):**
```json
{
  "success": true,
  "message": "Registro de repartidor completado con éxito. Su cuenta está pendiente de aprobación por el administrador.",
  "token": "3|mnOPqRSt...",
  "user": {
    "id": "18f92b74-291b-4b2a-89a1-5d9c8e1e7f09",
    "first_name": "Carlos",
    "last_name": "Conductor",
    "email": "carlos@nikama.com",
    "phone": "+51999888777",
    "dni": "70605040",
    "avatar_url": null,
    "is_active": true,
    "roles": ["driver"]
  }
}
```

### 4. Obtener Perfil Autenticado (Profile)
* **Endpoint:** `GET /api/v1/auth/profile`
* **Respuesta Exitosa (200 OK):**
```json
{
  "id": "7ca641fa-e95c-473d-9d41-0f40fb1764eb",
  "first_name": "Juan",
  "last_name": "Pérez",
  "email": "cliente@nikama.com",
  "phone": "+51987654321",
  "dni": "76543210",
  "avatar_url": "https://url-servidor.com/storage/avatars/avatar.png",
  "is_active": true,
  "roles": ["customer"]
}
```

### 5. Cerrar Sesión (Logout)
* **Endpoint:** `POST /api/v1/auth/logout`
* **Respuesta Exitosa (200 OK):**
```json
{
  "success": true,
  "message": "Sesión cerrada correctamente."
}
```

---

## 🍽️ Grupo 2: Catálogo de Productos y Categorías (Público)

### 1. Listar Categorías
* **Endpoint:** `GET /api/v1/categories`
* **Respuesta Exitosa (200 OK):**
```json
{
  "data": [
    {
      "id": "e5b8d2a1-ef4b-4b10-91a3-2bfd6e3c6f08",
      "parent_id": null,
      "name": "Hamburguesas",
      "slug": "hamburguesas",
      "description": "Las mejores hamburguesas artesanales de la ciudad",
      "icon": "fastfood",
      "image_url": "https://url-servidor.com/images/hamburguesas.png",
      "sort_order": 1,
      "is_active": true
    }
  ]
}
```

### 2. Listar Productos
* **Endpoint:** `GET /api/v1/products`
* **Parámetros Opcionales (Query string):** `?category_id=uuid&is_featured=1`
* **Respuesta Exitosa (200 OK):**
```json
{
  "data": [
    {
      "id": "d748f309-1ba2-4a0d-9b1b-12d8fc9d01b1",
      "business_id": "c129f123-5bf4-4f2e-bf91-fa8d7e9b08d2",
      "business_name": "Nikama Burguers",
      "name": "Hamburguesa Doble Queso",
      "slug": "hamburguesa-doble-queso",
      "description": "Doble carne de res, doble queso cheddar fundido, tocino y salsas de la casa.",
      "price": "22.50",
      "compare_price": "25.00",
      "sku": "HMB-001",
      "stock_quantity": 45,
      "track_stock": true,
      "status": "active",
      "is_featured": true,
      "requires_preparation": true,
      "preparation_time_minutes": 15,
      "main_image_url": "https://url-servidor.com/images/doble-queso.png",
      "rating_average": 4.8,
      "total_reviews": 15
    }
  ]
}
```

### 3. Detalle de Producto con Opciones y Modificadores
* **Endpoint:** `GET /api/v1/products/{product_id}`
* **Respuesta Exitosa (200 OK):**
```json
{
  "data": {
    "id": "d748f309-1ba2-4a0d-9b1b-12d8fc9d01b1",
    "business_id": "c129f123-5bf4-4f2e-bf91-fa8d7e9b08d2",
    "business_name": "Nikama Burguers",
    "name": "Hamburguesa Doble Queso",
    "slug": "hamburguesa-doble-queso",
    "description": "Doble carne de res, doble queso cheddar fundido, tocino y salsas de la casa.",
    "price": "22.50",
    "compare_price": "25.00",
    "sku": "HMB-001",
    "stock_quantity": 45,
    "track_stock": true,
    "status": "active",
    "is_featured": true,
    "requires_preparation": true,
    "preparation_time_minutes": 15,
    "main_image_url": "https://url-servidor.com/images/doble-queso.png",
    "rating_average": 4.8,
    "total_reviews": 15,
    "option_groups": [
      {
        "id": "f8a91b2c-...",
        "name": "Acompañamientos",
        "description": "Elige tu acompañamiento favorito",
        "min_selectable": 1,
        "max_selectable": 1,
        "is_required": true,
        "options": [
          {
            "id": "9a7d6e5c-...",
            "name": "Papas fritas clásicas",
            "additional_price": 0.00,
            "is_available": true
          },
          {
            "id": "8a7d6e5c-...",
            "name": "Camotes fritos",
            "additional_price": 2.50,
            "is_available": true
          }
        ]
      }
    ]
  }
}
```

---

## 🛒 Grupo 3: Gestión de Direcciones y Carrito (Requiere Token)

### 1. Listar Direcciones del Cliente
* **Endpoint:** `GET /api/v1/customer/addresses`
* **Respuesta Exitosa (200 OK):**
```json
[
  {
    "id": "8da8c2b7-...",
    "label": "Mi Casa",
    "address": "Av. Larco 123, Chiclayo",
    "address_type": "home",
    "reference": "Frente al parque principal",
    "delivery_notes": "Tocar el timbre verde",
    "contact_name": "Juan Pérez",
    "contact_phone": "+51987654321",
    "province": "Chiclayo",
    "district": "Chiclayo",
    "department": "Lambayeque",
    "country": "PE",
    "postal_code": "14001",
    "latitude": -6.7719,
    "longitude": -79.8394,
    "is_default": true,
    "is_active": true
  }
]
```

### 2. Agregar al Carrito
* **Endpoint:** `POST /api/v1/customer/cart/add`
* **Cuerpo de la Petición (Payload):**
```json
{
  "product_id": "d748f309-1ba2-4a0d-9b1b-12d8fc9d01b1",
  "quantity": 1,
  "notes": "Sin cebolla",
  "options": [
    {
      "product_option_id": "9a7d6e5c-..."
    }
  ]
}
```
* **Respuesta Exitosa (200 OK):**
```json
{
  "success": true,
  "message": "Producto agregado al carrito con éxito.",
  "data": {
    "id": "fa82d712-...",
    "status": "active",
    "subtotal": 22.50,
    "items_count": 1,
    "items": [
      {
        "id": "18f92b74-...",
        "product_id": "d748f309-...",
        "product_combo_id": null,
        "product_name": "Hamburguesa Doble Queso",
        "product_image": "https://url-servidor.com/images/doble-queso.png",
        "business_name": "Nikama Burguers",
        "quantity": 1,
        "unit_price": 22.50,
        "total_price": 22.50,
        "notes": "Sin cebolla",
        "options": [
          {
            "id": "9b8a213e-...",
            "product_option_id": "9a7d6e5c-...",
            "option_name": "Papas fritas clásicas",
            "additional_price": 0.00
          }
        ]
      }
    ]
  }
}
```

### 3. Ver Carrito Actual
* **Endpoint:** `GET /api/v1/customer/cart`
* **Respuesta Exitosa (200 OK):**
*(Devuelve el mismo formato del campo `data` del método agregar).*

---

## 💳 Grupo 4: Checkout y Pedidos (Requiere Token)

### 1. Crear Pedido (Checkout)
* **Endpoint:** `POST /api/v1/customer/checkout`
* **Cuerpo de la Petición (Payload):**
```json
{
  "address_selection_type": "saved", // O "new"
  "customer_address_id": "8da8c2b7-...", // Requerido si es "saved"
  "payment_method": "yape", // cash, yape, plin, card, bank_transfer
  "notes": "Dejar en recepción"
}
```
* **Respuesta Exitosa (201 Created):**
```json
{
  "success": true,
  "message": "Pedido realizado con éxito.",
  "data": {
    "id": "ca93a218-e921-4f1b-b18c-c1a938a8e1b3",
    "order_number": "NKM-X8Y7Z2",
    "status": "pending",
    "payment_status": "paid",
    "subtotal": 22.50,
    "delivery_fee": 5.00,
    "total": 27.50,
    "delivery_address": "Av. Larco 123, Chiclayo",
    "delivery_reference": "Frente al parque principal",
    "delivery_latitude": -6.7719,
    "delivery_longitude": -79.8394,
    "notes": "Dejar en recepción",
    "confirmed_at": null,
    "delivered_at": null,
    "cancelled_at": null,
    "created_at": "2026-06-26T15:27:00.000000Z",
    "items": [
      {
        "id": "bc82d392-...",
        "product_id": "d748f309-...",
        "product_combo_id": null,
        "product_name": "Hamburguesa Doble Queso",
        "unit_price": 22.50,
        "quantity": 1,
        "subtotal": 22.50,
        "product_image_url": "https://url-servidor.com/images/doble-queso.png",
        "options": [
          {
            "id": "38a8f123-...",
            "option_name": "Papas fritas clásicas",
            "option_group_name": "Acompañamientos",
            "additional_price": 0.00
          }
        ]
      }
    ],
    "payment": {
      "payment_method": "yape",
      "status": "paid",
      "amount": 27.50
    },
    "active_delivery": null,
    "status_history": [
      {
        "status": "pending",
        "description": "Pedido recibido y pago aprobado vía Yape. Pendiente de confirmación por el negocio.",
        "created_at": "2026-06-26T15:27:01.000000Z"
      }
    ]
  }
}
```

### 2. Rastrear Pedido en Vivo
* **Endpoint:** `GET /api/v1/customer/orders/{order_id}/track`
* **Respuesta Exitosa (200 OK):**
```json
{
  "success": true,
  "order_id": "ca93a218-e921-4f1b-b18c-c1a938a8e1b3",
  "status": "out_for_delivery",
  "driver": {
    "first_name": "Carlos",
    "last_name": "Conductor",
    "phone": "+51999888777",
    "vehicle_type": "motorcycle",
    "vehicle_plate": "ABC-123"
  },
  "live_location": {
    "latitude": -6.7725,
    "longitude": -79.8402,
    "last_updated_at": "2026-06-26T15:28:30Z"
  }
}
```

---

## 🛵 Grupo 5: Funcionalidades del Repartidor (Driver)

### 1. Dashboard del Repartidor
* **Endpoint:** `GET /api/v1/driver/dashboard`
* **Respuesta Exitosa (200 OK):**
```json
{
  "active_assignments": [
    {
      "id": "ea8b1a8d-...",
      "status": "assigned",
      "assigned_at": "2026-06-26T15:28:00Z",
      "accepted_at": null,
      "completed_at": null,
      "order": {
        "id": "ca93a218-...",
        "order_number": "NKM-X8Y7Z2",
        "total": 27.50,
        "delivery_address": "Av. Larco 123, Chiclayo",
        "delivery_reference": "Frente al parque principal",
        "delivery_latitude": -6.7719,
        "delivery_longitude": -79.8394,
        "notes": "Dejar en recepción",
        "items_count": 1
      },
      "delivery": {
        "id": "da8f1b2c-...",
        "status": "pending",
        "business_name": "Nikama Burguers",
        "business_address": "Av. Bolognesi 456, Chiclayo",
        "business_latitude": -6.7745,
        "business_longitude": -79.8431
      }
    }
  ],
  "driver_profile": {
    "status": "active", // active, inactive, busy
    "rating_average": 4.9,
    "total_deliveries": 32
  }
}
```

### 2. Aceptar Pedido Asignado
* **Endpoint:** `POST /api/v1/driver/assignments/{assignment_id}/accept`
* **Respuesta Exitosa (200 OK):**
```json
{
  "success": true,
  "message": "Asignación aceptada con éxito."
}
```

### 3. Emitir Geolocalización en Vivo (GPS)
* **Endpoint:** `POST /api/v1/driver/deliveries/{delivery_id}/emit-location`
* **Cuerpo de la Petición (Payload):**
```json
{
  "latitude": -6.7725,
  "longitude": -79.8402
}
```
* **Respuesta Exitosa (200 OK):**
```json
{
  "success": true,
  "message": "Ubicación actualizada con éxito."
}
```

### 4. Completar Entrega
* **Endpoint:** `POST /api/v1/driver/deliveries/{delivery_id}/complete`
* **Respuesta Exitosa (200 OK):**
```json
{
  "success": true,
  "message": "Pedido entregado con éxito."
}
```
