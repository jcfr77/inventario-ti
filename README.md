# Inventario TI — Backend

API REST desarrollada con **Laravel 10** y **Laravel Sanctum** para el sistema de inventario de equipos tecnológicos de **CORMUDESI**.

## Tecnologías

- PHP 8.x
- Laravel 10
- Laravel Sanctum (autenticación por tokens)
- MySQL 5.7+
- XAMPP (entorno local recomendado)

## Requisitos

- PHP >= 8.1
- Composer
- MySQL >= 5.7

## Instalación

```bash
# 1. Clonar el repositorio dentro de htdocs (XAMPP)
cd C:/xampp/htdocs
git clone https://github.com/jcfr77/inventario-ti.git
cd inventario-ti

# 2. Instalar dependencias PHP
composer install

# 3. Copiar y configurar variables de entorno
cp .env.example .env
php artisan key:generate
```

Editar `.env` con los datos de la base de datos:

```env
APP_URL=http://localhost
DB_DATABASE=inventario_ti
DB_USERNAME=root
DB_PASSWORD=
SANCTUM_TOKEN_EXPIRATION=480
```

```bash
# 4. Crear la base de datos en MySQL
# (desde phpMyAdmin o MySQL CLI: CREATE DATABASE inventario_ti;)

# 5. Ejecutar migraciones y seeders
php artisan migrate --seed
```

> **XAMPP:** si `php` no está en el PATH, usar la ruta completa:
> `C:/xampp/php/php.exe artisan migrate --seed`

## Usuario inicial (generado por el seeder)

| Campo       | Valor                  |
|-------------|------------------------|
| Email       | `admin@cormudesi.cl`   |
| Contraseña  | `Admin@2024`           |

Al primer inicio de sesión el sistema solicitará cambiar la contraseña.

## Roles predeterminados

| Rol                 | Tipo  | Descripción            |
|---------------------|-------|------------------------|
| Super Administrador | CRUD  | Acceso completo        |
| Administrador       | CRU   | Crear, ver y editar    |
| Usuario             | CR    | Crear y ver            |
| Lector              | R     | Solo lectura           |

## Sistema de permisos (tres niveles)

1. **Rol base** — asignado al usuario (Super Administrador / Administrador / Usuario / Lector)
2. **Grupos por usuario** — qué secciones del menú ve el usuario (`infra_usuario_grupo`), con nivel de acceso CRUD independiente por grupo
3. **Sub-ítems por usuario** — dentro de cada grupo, qué sub-items son visibles (`infra_usuario_permiso`)

La visibilidad del menú se rige por los niveles 2 y 3. El nivel 1 controla botones de acción (crear/editar/eliminar).

## Estructura de la base de datos

| Tabla                         | Descripción                                        |
|-------------------------------|----------------------------------------------------|
| `infra_rol`                   | Roles del sistema                                  |
| `infra_permiso`               | Sub-ítems del menú                                 |
| `infra_rol_permiso`           | Permisos por rol base (heredado)                   |
| `infra_usuario`               | Usuarios del sistema                               |
| `infra_usuario_grupo`         | Grupos del menú asignados por usuario              |
| `infra_usuario_permiso`       | Sub-ítems visibles por usuario dentro de su grupo  |
| `infra_usuario_sucursal`      | Sedes restringidas por usuario                     |
| `infra_sucursal`              | Sedes / sucursales de CORMUDESI                    |
| `infra_tipo_producto`         | Tipos de producto (notebook, impresora…)           |
| `infra_marca_producto`        | Marcas de productos                                |
| `infra_producto`              | Catálogo de productos                              |
| `infra_tipo_movimiento`       | Tipos de movimiento (ingreso, egreso…)             |
| `infra_encabezado_mov`        | Encabezado de movimientos                          |
| `infra_detalle_mov`           | Detalle de movimientos                             |
| `infra_estado`                | Estados de equipos                                 |
| `infra_movimientos_productos` | Movimientos históricos por equipo                  |
| `infra_log`                   | Bitácora de auditoría del sistema                  |
| `personal_access_tokens`      | Tokens de Sanctum                                  |

## Endpoints principales

| Método | Ruta                              | Descripción                            |
|--------|-----------------------------------|----------------------------------------|
| POST   | `/api/auth/login`                 | Iniciar sesión                         |
| POST   | `/api/auth/logout`                | Cerrar sesión                          |
| GET    | `/api/auth/me`                    | Usuario autenticado                    |
| PUT    | `/api/auth/password`              | Cambiar contraseña                     |
| GET    | `/api/usuarios`                   | Listar usuarios                        |
| PUT    | `/api/usuarios/{id}`              | Actualizar usuario (incluye grupos)    |
| GET    | `/api/usuarios/{id}/permisos`     | Sub-ítems asignados al usuario         |
| POST   | `/api/usuarios/{id}/permisos`     | Guardar sub-ítems del usuario          |
| GET    | `/api/usuarios/{id}/sucursales`   | Sedes asignadas al usuario             |
| GET    | `/api/productos`                  | Listar productos                       |
| GET    | `/api/ingresos`                   | Listar ingresos                        |
| GET    | `/api/egresos`                    | Listar egresos                         |
| GET    | `/api/prestamos`                  | Listar préstamos                       |
| GET    | `/api/bitacora`                   | Bitácora del sistema                   |

## Frontend

El frontend de este sistema está en el repositorio [inventario-ti-front](https://github.com/jcfr77/inventario-ti-front).
