# Divertilandia Backend API

API REST para el sistema de administración de Divertilandia, desarrollada con Laravel 12.

## 🚀 Características

- **API REST** completa para gestión de paquetes, eventos, inventario y dashboard
- **Base de datos MySQL** con XAMPP para desarrollo y producción
- **Sistema de eventos** completo con gestión de contactos, ubicación y costos
- **Sistema de inventario** con tipos de juguetes y control de estado
- **Configuraciones dinámicas** para costos y parámetros del sistema
- **Modelos Eloquent** para manejo de datos
- **Migraciones** para estructura de base de datos
- **Controladores API** organizados por funcionalidad

## 📋 Requisitos

- PHP 8.2+
- Composer
- XAMPP (MySQL)
- MySQL 5.7+ o MariaDB 10.3+

## 🛠️ Instalación

1. **Instalar dependencias:**
   ```bash
   composer install
   ```

2. **Configurar XAMPP:**
   - Iniciar XAMPP y activar MySQL
   - Crear base de datos llamada `divertialdia` en phpMyAdmin

3. **Configurar variables de entorno:**
   El archivo `.env` está configurado para MySQL con XAMPP:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=divertialdia
   DB_USERNAME=root
   DB_PASSWORD=
   ```

4. **Generar clave de aplicación:**
   ```bash
   php artisan key:generate
   ```

5. **Ejecutar migraciones:**
   ```bash
   php artisan migrate
   ```

6. **Ejecutar seeders (opcional):**
   ```bash
   php artisan db:seed
   ```

7. **Ejecutar servidor de desarrollo:**
   ```bash
   php artisan serve
   ```

   La API estará disponible en `http://localhost:8000`

## 📊 Estructura de Base de Datos

### Tablas Principales

#### packages
- `id` - Identificador único
- `name` - Nombre del paquete
- `description` - Descripción del paquete
- `price` - Precio del paquete
- `year` - Año de vigencia del precio
- `max_children` - Máximo número de niños
- `duration` - Duración en horas
- `is_active` - Estado activo/inactivo
- `created_at` / `updated_at` - Timestamps

#### events
- `id` - Identificador único
- `contact_name` - Nombre de la persona de contacto
- `contact_phone` - Teléfono de contacto
- `secondary_phone` - Teléfono secundario (opcional)
- `address` - Dirección del evento
- `google_maps_url` - URL de Google Maps (opcional)
- `latitude` - Latitud GPS (opcional)
- `longitude` - Longitud GPS (opcional)
- `event_date` - Fecha del evento
- `start_time` - Hora de inicio
- `extra_hours` - Cantidad de horas extras (0-3)
- `extra_hours_cost` - Costo total de horas extras
- `package_id` - ID del paquete (FK)
- `advance_payment` - Anticipo (por defecto $300)
- `status` - Estado del evento (pending, confirmed, in_progress, completed, cancelled)
- `notes` - Notas adicionales
- `created_at` / `updated_at` - Timestamps

#### toy_types
- `id` - Identificador único
- `name` - Nombre del tipo de juguete (único)
- `description` - Descripción del tipo
- `is_active` - Si el tipo está activo
- `created_at` / `updated_at` - Timestamps

#### inventory_items
- `id` - Identificador único
- `name` - Nombre del juguete
- `description` - Descripción del juguete
- `health_status` - Estado de salud (excellent, good, fair, poor, broken)
- `status` - Status (available, in_use, maintenance, retired)
- `quantity` - Cantidad disponible
- `unit_price` - Precio unitario
- `toy_type_id` - ID del tipo de juguete (FK)
- `brand` - Marca del juguete
- `model` - Modelo del juguete
- `serial_number` - Número de serie
- `purchase_date` - Fecha de compra
- `notes` - Notas adicionales
- `created_at` / `updated_at` - Timestamps

#### event_settings
- `id` - Identificador único
- `setting_key` - Clave única para cada configuración
- `setting_name` - Nombre descriptivo
- `setting_value` - Valor de la configuración
- `description` - Descripción de la configuración
- `is_active` - Si la configuración está activa
- `created_at` / `updated_at` - Timestamps

#### games
- `id` - Identificador único
- `name` - Nombre del juego
- `type` - Tipo de juego (inflatable, mechanical, etc.)
- `quantity` - Cantidad disponible
- `condition` - Estado del juego
- `last_maintenance` - Último mantenimiento
- `next_maintenance` - Próximo mantenimiento
- `is_available` - Disponibilidad
- `created_at` / `updated_at` - Timestamps

## 🔗 Endpoints API

### Paquetes
- `GET /api/packages` - Listar todos los paquetes
- `GET /api/packages/{id}` - Obtener paquete específico
- `POST /api/packages` - Crear nuevo paquete
- `PUT /api/packages/{id}` - Actualizar paquete
- `DELETE /api/packages/{id}` - Eliminar paquete
- `GET /api/packages?year={year}` - Filtrar por año

### Eventos
- `GET /api/events` - Listar todos los eventos
- `GET /api/events/{id}` - Obtener evento específico
- `POST /api/events` - Crear nuevo evento
- `PUT /api/events/{id}` - Actualizar evento
- `DELETE /api/events/{id}` - Eliminar evento
- `GET /api/events?date={date}` - Filtrar por fecha
- `GET /api/events?start_date={start}&end_date={end}` - Filtrar por rango de fechas
- `GET /api/events?status={status}` - Filtrar por estado

### Tipos de Juguetes
- `GET /api/toy-types` - Listar todos los tipos de juguetes
- `GET /api/toy-types/{id}` - Obtener tipo específico
- `POST /api/toy-types` - Crear nuevo tipo
- `PUT /api/toy-types/{id}` - Actualizar tipo
- `DELETE /api/toy-types/{id}` - Eliminar tipo
- `GET /api/toy-types?active=true` - Filtrar tipos activos

### Inventario
- `GET /api/inventory` - Listar inventario
- `GET /api/inventory/{id}` - Obtener item específico
- `POST /api/inventory` - Crear nuevo item
- `PUT /api/inventory/{id}` - Actualizar item
- `DELETE /api/inventory/{id}` - Eliminar item
- `GET /api/inventory/available` - Obtener items disponibles
- `GET /api/inventory?toy_type={id}` - Filtrar por tipo de juguete
- `GET /api/inventory?status={status}` - Filtrar por estado
- `GET /api/inventory?health_status={status}` - Filtrar por estado de salud

### Configuraciones
- `GET /api/settings` - Listar todas las configuraciones
- `GET /api/settings/{key}` - Obtener configuración específica
- `PUT /api/settings/{key}` - Actualizar configuración
- `GET /api/settings/active` - Obtener configuraciones activas

### Juegos
- `GET /api/games` - Listar todos los juegos
- `GET /api/games/{id}` - Obtener juego específico
- `POST /api/games` - Crear nuevo juego
- `PUT /api/games/{id}` - Actualizar juego
- `DELETE /api/games/{id}` - Eliminar juego
- `GET /api/games?type={type}` - Filtrar por tipo

### Dashboard
- `GET /api/dashboard/stats` - Estadísticas generales
- `GET /api/dashboard/revenue/{year}` - Ingresos por mes
- `GET /api/dashboard/events-summary` - Resumen de eventos
- `GET /api/dashboard/inventory-summary` - Resumen de inventario

## 📝 Ejemplos de Uso

### Crear un Paquete
```bash
curl -X POST http://localhost:8000/api/packages \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Paquete Premium",
    "description": "Paquete completo con inflables y juegos",
    "price": 2500,
    "year": 2024,
    "max_children": 20,
    "duration": 4,
    "is_active": true
  }'
```

### Crear un Evento
```bash
curl -X POST http://localhost:8000/api/events \
  -H "Content-Type: application/json" \
  -d '{
    "contact_name": "María González",
    "contact_phone": "555-1234",
    "secondary_phone": "555-5678",
    "address": "Calle Principal 123, Colonia Centro",
    "google_maps_url": "https://maps.google.com/...",
    "latitude": 19.4326,
    "longitude": -99.1332,
    "event_date": "2024-01-15",
    "start_time": "14:00",
    "extra_hours": 1,
    "extra_hours_cost": 100.00,
    "package_id": 1,
    "advance_payment": 300.00,
    "status": "pending",
    "notes": "Evento de cumpleaños para 15 niños"
  }'
```

### Crear un Tipo de Juguete
```bash
curl -X POST http://localhost:8000/api/toy-types \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Inflables Grandes",
    "description": "Inflables de gran tamaño para eventos al aire libre",
    "is_active": true
  }'
```

### Crear un Item de Inventario
```bash
curl -X POST http://localhost:8000/api/inventory \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Brincolín Castillo",
    "description": "Brincolín en forma de castillo medieval",
    "health_status": "excellent",
    "status": "available",
    "quantity": 1,
    "unit_price": 1500.00,
    "toy_type_id": 1,
    "brand": "JumpKing",
    "model": "Castle Pro",
    "serial_number": "JK-CP-001",
    "purchase_date": "2024-01-01",
    "notes": "En excelente estado, recién comprado"
  }'
```

### Actualizar Configuración
```bash
curl -X PUT http://localhost:8000/api/settings/extra_hour_cost \
  -H "Content-Type: application/json" \
  -d '{
    "setting_value": "120.00",
    "description": "Costo actualizado por hora extra"
  }'
```

## 🔧 Comandos Útiles

```bash
# Crear nueva migración
php artisan make:migration create_nueva_tabla

# Crear nuevo modelo
php artisan make:model Modelo

# Crear nuevo controlador API
php artisan make:controller Api/ControladorController --api

# Crear nuevo seeder
php artisan make:seeder NombreSeeder

# Ejecutar migraciones
php artisan migrate

# Ejecutar seeders
php artisan db:seed

# Ejecutar seeder específico
php artisan db:seed --class=NombreSeeder

# Revertir última migración
php artisan migrate:rollback

# Ver estado de migraciones
php artisan migrate:status

# Ver rutas registradas
php artisan route:list

# Limpiar cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear

# Verificar conexión a base de datos
php artisan tinker --execute="DB::connection()->getPdo(); echo 'Conexión exitosa';"
```

## 🧪 Testing

```bash
# Ejecutar pruebas
php artisan test

# Ejecutar pruebas específicas
php artisan test --filter=PackageTest
```

## 📦 Estructura del Proyecto

```
divertilandia-backend/
├── app/
│   ├── Http/Controllers/Api/
│   │   ├── PackageController.php
│   │   ├── EventController.php
│   │   ├── GameController.php
│   │   ├── InventoryController.php
│   │   └── DashboardController.php
│   └── Models/
│       ├── Package.php
│       ├── Event.php
│       ├── Game.php
│       ├── InventoryItem.php
│       ├── ToyType.php
│       └── EventSetting.php
├── database/
│   ├── migrations/
│   │   ├── create_packages_table.php
│   │   ├── create_events_table.php
│   │   ├── add_event_details_to_events_table.php
│   │   ├── create_toy_types_table.php
│   │   ├── create_inventory_items_table.php
│   │   ├── add_inventory_details_to_inventory_items_table.php
│   │   └── create_event_settings_table.php
│   └── seeders/
│       ├── DatabaseSeeder.php
│       ├── EventSettingsSeeder.php
│       └── ToyTypesSeeder.php
├── routes/
│   └── api.php
└── .env
```

## 🎯 Características del Sistema

### Sistema de Eventos
- **Gestión completa de contactos** con teléfonos primario y secundario
- **Ubicación detallada** con dirección, Google Maps y coordenadas GPS
- **Control de duración** con eventos fijos de 4 horas + hasta 3 horas extras
- **Costos configurables** para horas extras sin afectar eventos anteriores
- **Anticipo fijo** de $300 configurable
- **Estados de evento** para seguimiento completo del proceso

### Sistema de Inventario
- **Tipos de juguetes** con CRUD completo
- **Control de estado de salud** (excellent, good, fair, poor, broken)
- **Gestión de disponibilidad** (available, in_use, maintenance, retired)
- **Información detallada** de cada juguete (marca, modelo, serie, etc.)
- **Relación con tipos** para organización y filtrado

### Sistema de Configuraciones
- **Costos dinámicos** para horas extras
- **Parámetros configurables** sin afectar datos históricos
- **Configuraciones activas/inactivas** para control de versiones

## 🔒 Seguridad

- Validación de datos en todos los endpoints
- Sanitización de inputs
- Protección CSRF (deshabilitada para API)
- Rate limiting (configurable)

## 🚀 Despliegue

### Producción
1. **Configurar base de datos MySQL:**
   - Crear base de datos `divertialdia`
   - Configurar usuario con permisos apropiados

2. **Actualizar variables de entorno:**
   ```env
   APP_ENV=production
   APP_DEBUG=false
   DB_CONNECTION=mysql
   DB_HOST=tu-servidor-mysql
   DB_PORT=3306
   DB_DATABASE=divertialdia
   DB_USERNAME=tu-usuario
   DB_PASSWORD=tu-password
   ```

3. **Ejecutar migraciones y seeders:**
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

4. **Configurar servidor web** (Apache/Nginx)
5. **Configurar SSL**
6. **Optimizar para producción:**
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

### Docker (Opcional)
```bash
# Construir imagen
docker build -t divertilandia-backend .

# Ejecutar contenedor
docker run -p 8000:8000 divertilandia-backend
```

## 📊 Tipos de Juguetes Predefinidos

El sistema incluye 10 tipos de juguetes predefinidos:

1. **Inflables** - Brincolines, toboganes y castillos
2. **Juegos de Mesa** - Entretenimiento en eventos
3. **Juguetes de Exterior** - Actividades al aire libre
4. **Juguetes Educativos** - Diversión con aprendizaje
5. **Juguetes Electrónicos** - Requieren baterías o electricidad
6. **Juguetes de Construcción** - Bloques, legos
7. **Juguetes de Rol** - Disfraces y accesorios
8. **Juguetes Deportivos** - Pelotas, raquetas
9. **Juguetes de Agua** - Actividades acuáticas
10. **Juguetes Musicales** - Instrumentos y sonidos

## ⚙️ Configuraciones del Sistema

### Configuraciones Predefinidas
- **Costo por hora extra**: $100.00 (configurable)
- **Monto de anticipo**: $300.00 (configurable)
- **Duración por defecto**: 4 horas
- **Máximo de horas extras**: 3 horas

### Estados de Eventos
- `pending` - Pendiente de confirmación
- `confirmed` - Confirmado
- `in_progress` - En progreso
- `completed` - Completado
- `cancelled` - Cancelado

### Estados de Salud de Juguetes
- `excellent` - Excelente
- `good` - Bueno
- `fair` - Regular
- `poor` - Malo
- `broken` - Roto

### Estados de Disponibilidad
- `available` - Disponible
- `in_use` - En uso
- `maintenance` - En mantenimiento
- `retired` - Retirado

## 📞 Soporte

Para consultas técnicas o reportar bugs, contactar al equipo de desarrollo.

---

**Divertilandia Backend** - API robusta para gestión de eventos infantiles 🎪