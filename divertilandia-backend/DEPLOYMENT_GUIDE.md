# 🚀 Guía de Despliegue - Divertilandia Management System

## 📋 Resumen

Esta guía te ayudará a migrar todos los datos reales de tu base de datos local al servidor de producción de forma segura y completa.

## 🎯 Estrategia Recomendada: Seeder de Producción

Hemos elegido usar un **ProductionDataSeeder** porque:
- ✅ **Seguro**: Preserva todos tus datos reales
- ✅ **Versionado**: Se puede incluir en Git
- ✅ **Reproducible**: Funciona en cualquier servidor
- ✅ **Completo**: Incluye todas las relaciones y datos
- ✅ **Laravel nativo**: Usa las herramientas estándar de Laravel

## 📁 Archivos Generados

### 1. `ProductionDataSeeder.php`
- **Ubicación**: `database/seeders/ProductionDataSeeder.php`
- **Contenido**: Todos tus datos reales exportados automáticamente
- **Generado**: $(date)

### 2. `ExportProductionData.php`
- **Ubicación**: `app/Console/Commands/ExportProductionData.php`
- **Propósito**: Comando para regenerar el seeder cuando agregues más datos

## 🔄 Proceso de Migración

### Paso 1: Preparar el Servidor de Producción

1. **Subir el código del backend** a tu servidor
2. **Configurar el archivo `.env` de producción**:
   ```bash
   APP_NAME="Divertilandia Management System"
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://thewolveslabs.com
   
   DB_CONNECTION=mysql
   DB_HOST=[tu-host-mysql]
   DB_PORT=3306
   DB_DATABASE=[nombre-base-datos-produccion]
   DB_USERNAME=[usuario-mysql]
   DB_PASSWORD=[contraseña-mysql]
   
   # Otras configuraciones...
   ```

### Paso 2: Configurar la Base de Datos

```bash
# En el servidor de producción:

# 1. Instalar dependencias
composer install --optimize-autoloader --no-dev

# 2. Generar clave de aplicación
php artisan key:generate

# 3. Ejecutar migraciones (crea todas las tablas)
php artisan migrate:fresh

# 4. Importar todos los datos reales
php artisan db:seed --class=ProductionDataSeeder
```

### Paso 3: Configurar el Frontend

1. **Subir la carpeta `build/`** (ya generada con la configuración correcta)
2. **Configurar el servidor web** para servir los archivos estáticos
3. **Configurar rutas** para que las rutas de React funcionen

## 📊 Datos que se Migrarán

El seeder incluye **TODOS** tus datos reales:

### 🎮 Inventario y Juegos
- ✅ Tipos de juguetes
- ✅ Juegos/inventario con cantidades y condiciones
- ✅ Cláusulas de juguetes
- ✅ Relaciones juego-cláusulas

### 📦 Paquetes y Eventos
- ✅ Paquetes con precios y configuraciones
- ✅ Relaciones paquete-juegos
- ✅ Configuraciones de eventos
- ✅ Eventos completos con todos los detalles

### 💰 Finanzas
- ✅ Categorías de gastos
- ✅ Gastos registrados
- ✅ Vehículos
- ✅ Uso de vehículos por evento
- ✅ Precios de combustible

## 🔧 Comandos Útiles

### Regenerar Seeder (si agregas más datos)
```bash
# En tu máquina local:
php artisan export:production-data
```

### Verificar Datos en Producción
```bash
# En el servidor:
php artisan tinker --execute="
echo 'Eventos: ' . App\Models\Event::count() . PHP_EOL;
echo 'Paquetes: ' . App\Models\Package::count() . PHP_EOL;
echo 'Juegos: ' . App\Models\Game::count() . PHP_EOL;
echo 'Gastos: ' . App\Models\Expense::count() . PHP_EOL;
"
```

### Backup de Seguridad (en producción)
```bash
# Crear backup antes de cualquier cambio importante
mysqldump -u [usuario] -p [base-datos] > backup_$(date +%Y%m%d_%H%M%S).sql
```

## 🚨 Consideraciones Importantes

### Antes del Despliegue
- [ ] **Backup local**: Asegúrate de tener respaldo de tu base de datos local
- [ ] **Prueba local**: Verifica que el seeder funcione en tu entorno local
- [ ] **Configuración**: Revisa que el `.env` de producción esté correcto

### Durante el Despliegue
- [ ] **Modo mantenimiento**: Activa el modo mantenimiento si ya hay usuarios
- [ ] **Migraciones**: Ejecuta las migraciones antes del seeder
- [ ] **Verificación**: Verifica que todos los datos se importaron correctamente

### Después del Despliegue
- [ ] **Pruebas**: Verifica que la aplicación funcione completamente
- [ ] **Monitoreo**: Revisa los logs por posibles errores
- [ ] **Backup**: Crea un backup de la base de datos de producción

## 🔄 Actualizaciones Futuras

### Para agregar nuevos datos:
1. **En local**: Agrega tus nuevos datos normalmente
2. **Regenerar**: Ejecuta `php artisan export:production-data`
3. **Subir**: Sube el nuevo seeder al servidor
4. **Aplicar**: Ejecuta `php artisan db:seed --class=ProductionDataSeeder`

### Para cambios de estructura:
1. **Crear migración**: `php artisan make:migration nombre_cambio`
2. **En producción**: `php artisan migrate`
3. **Regenerar seeder**: Si es necesario

## 📞 Soporte

Si encuentras algún problema durante el despliegue:

1. **Revisa los logs**: `storage/logs/laravel.log`
2. **Verifica la conexión**: Asegúrate de que la base de datos esté accesible
3. **Permisos**: Verifica que Laravel tenga permisos de escritura en `storage/` y `bootstrap/cache/`

## ✅ Checklist Final

- [ ] Código del backend subido al servidor
- [ ] Archivo `.env` de producción configurado
- [ ] Base de datos MySQL creada y accesible
- [ ] Dependencias instaladas (`composer install`)
- [ ] Migraciones ejecutadas (`php artisan migrate:fresh`)
- [ ] Datos importados (`php artisan db:seed --class=ProductionDataSeeder`)
- [ ] Frontend compilado y subido
- [ ] Servidor web configurado
- [ ] Aplicación funcionando correctamente

---

**¡Tu sistema Divertilandia estará listo para producción!** 🎉
