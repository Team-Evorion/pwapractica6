# Hotel Evorion — Sistema de Reservas

Proyecto académico de la Clase Práctica 6 de Programación Web Avanzada. Integra los módulos de autenticación y usuarios, proveedores, habitaciones y reservaciones en Laravel 13.

## Requisitos

- PHP 8.3 o superior con las extensiones requeridas por Laravel.
- Composer.
- MySQL/MariaDB (XAMPP o Laragon).
- Node.js y npm solo si se recompilan los recursos frontend.

## Instalación en Windows/XAMPP

```powershell
composer install
Copy-Item .env.example .env
php artisan key:generate
```

En `.env`, configure la base `pwapractica6` y asigne una contraseña temporal segura a `INITIAL_ADMIN_PASSWORD`. Después ejecute:

```powershell
php artisan migrate:fresh --seed
php artisan serve
```

El administrador inicial usa `admin@hotel.test` y la contraseña definida en `INITIAL_ADMIN_PASSWORD`. No publique el archivo `.env` ni conserve una contraseña de entrega en producción.

## Pruebas

```powershell
php artisan test
```

La integración base fue comprobada tanto con SQLite como con MariaDB/MySQL. Después de los ajustes de presentación, la suite local contiene 34 pruebas y 142 aserciones.

## Roles

- Administrator: administración general, usuarios y reservaciones.
- Hotel Manager: supervisión y gestión de habitaciones.
- Receptionist: consulta, creación para clientes y actualización de reservaciones.
- Customer: catálogo y reservaciones propias.
- Supplier: necesidades del hotel y presentación de ofertas.

La guía técnica y las decisiones de integración están en [`docs/integracion-final.md`](docs/integracion-final.md).
