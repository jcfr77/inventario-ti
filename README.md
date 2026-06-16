# Inventario TI — Backend

API REST desarrollada con **Laravel 10** y **Laravel Sanctum** para el sistema de inventario de equipos tecnológicos de **CORMUDESI**.

## Tecnologías

- PHP 8.x
- Laravel 10
- Laravel Sanctum (autenticación por tokens)
- MySQL
- XAMPP (entorno local)

## Requisitos

- PHP >= 8.1
- Composer
- MySQL >= 5.7
- XAMPP o servidor web compatible

## Instalación

```bash
# 1. Clonar el repositorio
git clone <url-del-repo> inventario-ti
cd inventario-ti

# 2. Instalar dependencias
composer install

# 3. Copiar y configurar variables de entorno
cp .env.example .env
php artisan key:generate

# 4. Configurar base de datos en .env
DB_DATABASE=inventario_ti
DB_USERNAME=root
DB_PASSWORD=

# 5. Ejecutar migraciones y seeders
php artisan migrate --seed
```

## Variables de entorno relevantes

```env
APP_URL=http://localhost
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=inventario_ti
DB_USERNAME=root
DB_PASSWORD=

SANCTUM_TOKEN_EXPIRATION=480
```

## Estructura de la base de datos

| Tabla                         | Descripción                              |
|-------------------------------|------------------------------------------|
| `infra_rol`                   | Roles del sistema                        |
| `infra_permiso`               | Permisos por módulo                      |
| `infra_rol_permiso`           | Relación roles-permisos                  |
| `infra_usuario`               | Usuarios del sistema                     |
| `infra_sucursal`              | Sedes / sucursales de CORMUDESI          |
| `infra_tipo_producto`         | Tipos de producto (notebook, impresora…) |
| `infra_marca_producto`        | Marcas de productos                      |
| `infra_producto`              | Catálogo de productos                    |
| `infra_tipo_movimiento`       | Tipos de movimiento (ingreso, egreso…)   |
| `infra_encabezado_mov`        | Encabezado de movimientos                |
| `infra_detalle_mov`           | Detalle de movimientos                   |
| `infra_estado`                | Estados de equipos                       |
| `infra_movimientos_productos` | Movimientos históricos por equipo        |
| `infra_log`                   | Bitácora de auditoría del sistema        |
| `personal_access_tokens`      | Tokens de Sanctum                        |

## Roles predeterminados

| Rol           | Descripción                         |
|---------------|-------------------------------------|
| Administrador | Acceso total al sistema             |
| Técnico TI    | Gestión de productos y movimientos  |
| Supervisor    | Solo lectura e informes             |

## Usuario inicial

- **Email:** `admin@cormudesi.cl`
- **Contraseña:** `Admin@2024`
- Al primer inicio de sesión se solicitará cambiar la contraseña.

## Endpoints principales

| Método | Ruta                  | Descripción          |
|--------|-----------------------|----------------------|
| POST   | `/api/auth/login`     | Iniciar sesión       |
| POST   | `/api/auth/logout`    | Cerrar sesión        |
| GET    | `/api/auth/me`        | Usuario autenticado  |
| PUT    | `/api/auth/password`  | Cambiar contraseña   |
| GET    | `/api/productos`      | Listar productos     |
| GET    | `/api/ingresos`       | Listar ingresos      |
| GET    | `/api/egresos`        | Listar egresos       |
| GET    | `/api/prestamos`      | Listar préstamos     |
| GET    | `/api/bitacora`       | Bitácora del sistema |

## Frontend

El frontend de este sistema está en el repositorio [inventario-ti-front](https://github.com/jcfr77/inventario-ti-front).
