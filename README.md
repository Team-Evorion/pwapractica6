# 🏨 Hotel Evorion

### Sistema de reservas y administración hotelera | Programación Web Avanzada

**Hotel Evorion** es una aplicación web desarrollada como proyecto académico para la asignatura de **Programación Web Avanzada** de la Universidad Bolivariana del Ecuador.

La plataforma integra la administración de usuarios, habitaciones, reservaciones, pagos, finanzas, necesidades del hotel y ofertas de proveedores. El acceso está organizado mediante cinco roles: **Administrador**, **Gerente del hotel**, **Recepcionista**, **Cliente** y **Proveedor**. Cada usuario dispone únicamente de las funciones correspondientes a sus responsabilidades.

El proyecto combina frontend, backend y base de datos mediante Laravel y MySQL, con una interfaz moderna y adaptable a computadoras, tablets y teléfonos.

---

## 🌐 Enlaces del proyecto

- **Repositorio:** [GitHub — Team-Evorion/pwapractica6](https://github.com/Team-Evorion/pwapractica6)
- **Aplicación publicada:** [https://pwapractica6ube.infinityfreeapp.com](https://pwapractica6ube.infinityfreeapp.com)

---

## ✨ Características principales

- Registro, inicio y cierre de sesión mediante correo electrónico y contraseña.
- Contraseñas almacenadas mediante hash seguro.
- Limitación de intentos fallidos de inicio de sesión.
- Administración de usuarios, roles y estado activo o inactivo.
- Cierre de sesión automático para cuentas desactivadas.
- Catálogo público de habitaciones con fotografías y detalles.
- Búsqueda por tipo y filtros por precio.
- CRUD de habitaciones con precio, capacidad, descripción y disponibilidad operativa.
- Reservaciones por intervalos de fechas.
- Validación de disponibilidad para evitar reservas superpuestas.
- Estados de reservación: pendiente, confirmada y cancelada.
- Precio total conservado como referencia histórica de cada reservación.
- Panel financiero con ventas, cobros, saldos, reembolsos y ocupación.
- Registro de pagos parciales y métodos de pago.
- Gestión de necesidades de suministros del hotel.
- Presentación de ofertas por parte de proveedores.
- Restricción de ofertas a necesidades abiertas.
- Dashboards adaptados a las responsabilidades de cada rol.
- Políticas y middleware de autorización.
- Validaciones del lado del servidor y protección CSRF.
- Diseño responsive con Bootstrap 5, CSS y JavaScript.
- Pruebas automáticas de autenticación, permisos, habitaciones, reservaciones, suministros y finanzas.

---

## 👥 Roles y permisos

### Administrador

El Administrador dispone de control general sobre el sistema:

- Crear, editar, activar, desactivar y eliminar usuarios.
- Asignar los cinco roles disponibles.
- Consultar el panel administrativo y sus indicadores.
- Administrar las necesidades de suministros del hotel.
- Consultar y actualizar reservaciones.
- Administrar habitaciones.
- Consultar finanzas y registrar pagos o reembolsos.

La desactivación conserva la información del usuario, pero impide nuevos accesos y cierra las sesiones activas.

### Gerente del hotel

El Gerente supervisa la operación y el rendimiento del hotel:

- Crear, editar y eliminar habitaciones.
- Gestionar precios, capacidades y disponibilidad operativa.
- Consultar las reservaciones del hotel.
- Revisar el panel financiero.
- Registrar pagos y reembolsos.
- Consultar ventas, cobros, saldos y ocupación.

El Gerente no puede administrar cuentas de usuarios ni asumir funciones exclusivas del Administrador.

### Recepcionista

El Recepcionista gestiona la atención y las reservaciones:

- Consultar las habitaciones.
- Consultar las reservaciones del hotel.
- Registrar reservaciones para clientes.
- Confirmar o cancelar reservaciones.
- Verificar fechas y disponibilidad antes de reservar.

El Recepcionista no puede administrar usuarios, habitaciones ni finanzas.

### Cliente

El Cliente utiliza las funciones relacionadas con su propia estadía:

- Registrarse e iniciar sesión.
- Consultar el catálogo y los detalles de las habitaciones.
- Crear reservaciones para fechas disponibles.
- Consultar únicamente sus propias reservaciones.
- Actualizar el estado de una reservación propia cuando las reglas lo permiten.

El Cliente no puede consultar reservaciones ajenas ni acceder a los paneles administrativos.

### Proveedor

El Proveedor participa en el abastecimiento del hotel:

- Consultar las necesidades abiertas del hotel.
- Presentar ofertas vinculadas a una necesidad.
- Consultar, editar y eliminar sus propias ofertas.
- Revisar el estado de sus registros desde el portal de proveedores.

El Proveedor no puede ofertar sobre necesidades cerradas ni modificar ofertas de otros proveedores.

---

## 🛠️ Tecnologías utilizadas

| Tecnología | Aplicación |
|---|---|
| **Laravel 13** | Arquitectura MVC, rutas, autenticación y autorización |
| **PHP 8.3+** | Lógica del servidor |
| **MySQL / MariaDB** | Persistencia de usuarios, habitaciones y operaciones |
| **Blade** | Plantillas de la interfaz |
| **Bootstrap 5.3** | Componentes y diseño responsive |
| **CSS3** | Identidad visual y adaptación móvil |
| **JavaScript** | Filtros, vistas previas y confirmaciones |
| **Vite** | Compilación de recursos frontend |
| **PHPUnit** | Pruebas automáticas |
| **Composer** | Administración de dependencias PHP |
| **Git y GitHub** | Control de versiones y colaboración |
| **InfinityFree** | Publicación académica de la aplicación |

---

## 🗄️ Modelo de datos

La base de datos local recomendada se denomina `pwapractica6` y contiene las entidades necesarias para el funcionamiento del sistema:

- `roles`: roles disponibles en la plataforma.
- `users`: usuarios, credenciales, estado y rol asignado.
- `rooms`: habitaciones, tipos, precios, capacidad y disponibilidad operativa.
- `bookings`: reservaciones, fechas, estado, precio y total histórico.
- `payments`: pagos, reembolsos, métodos y responsable del registro.
- `supplies`: necesidades del hotel y ofertas de proveedores.
- `sessions`: sesiones de autenticación cuando se utiliza el controlador de base de datos.
- `cache`, `cache_locks`, `jobs`, `job_batches` y `failed_jobs`: infraestructura de Laravel.
- `migrations`: control de las migraciones ejecutadas.

### Relaciones principales

- Un usuario pertenece a un rol.
- Un usuario puede tener varias reservaciones.
- Una habitación puede estar asociada a varias reservaciones en fechas diferentes.
- Una reservación pertenece a un usuario y a una habitación.
- Una reservación puede registrar varios pagos o reembolsos.
- Un proveedor puede presentar varias ofertas.
- Una oferta puede estar vinculada a una necesidad abierta del hotel.

La disponibilidad por fechas se calcula a partir de las reservaciones; el campo `is_available` representa la disponibilidad operativa o mantenimiento de la habitación.

---

## 🏗️ Estructura del proyecto

```text
pwapractica6/
├── app/
│   ├── Http/
│   │   ├── Controllers/          # Controladores por módulo
│   │   ├── Middleware/           # Roles y cuentas activas
│   │   └── Requests/             # Validación y autorización
│   ├── Models/                   # Entidades del sistema
│   ├── Policies/                 # Reglas de acceso
│   └── Providers/                # Configuración de la aplicación
├── bootstrap/                    # Inicialización de Laravel
├── config/                       # Configuración del sistema
├── database/
│   ├── factories/                # Datos para pruebas
│   ├── migrations/               # Estructura de la base de datos
│   └── seeders/                  # Roles, habitaciones y datos iniciales
├── public/
│   ├── css/                      # Estilos públicos
│   ├── images/rooms/             # Fotografías de habitaciones
│   └── js/                       # Interactividad del catálogo
├── resources/
│   ├── css/modules/              # Estilos de paneles y módulos
│   ├── js/                       # Código fuente JavaScript
│   └── views/
│       ├── admin/                # Usuarios, suministros y dashboard
│       ├── auth/                 # Registro e inicio de sesión
│       ├── bookings/             # Reservaciones
│       ├── finances/             # Finanzas y pagos
│       ├── rooms/                # Catálogo y administración
│       ├── suppliers/            # Portal de proveedores
│       ├── layouts/              # Plantillas generales
│       ├── dashboard.blade.php   # Panel según el rol
│       └── home.blade.php        # Portada pública
├── routes/
│   ├── modules/                  # Rutas separadas por módulo
│   └── web.php                   # Registro principal de rutas
├── storage/                      # Sesiones, caché y registros
├── tests/                        # Pruebas automáticas
├── composer.json
├── package.json
└── README.md
```

---

## 🚀 Instalación local con XAMPP

### Requisitos

- XAMPP con PHP 8.3 o superior.
- MySQL 8 o MariaDB 10.11.
- Composer.
- Node.js y npm.
- Git.
- Navegador web moderno.

### 1. Clonar el repositorio

```powershell
git clone https://github.com/Team-Evorion/pwapractica6.git
cd pwapractica6
```

También puedes descargar el proyecto y colocarlo en:

```text
C:\xampp\htdocs\pwapractica6
```

### 2. Instalar las dependencias de PHP

```powershell
composer install
```

### 3. Crear el archivo de configuración

```powershell
Copy-Item .env.example .env
php artisan key:generate
```

### 4. Configurar la base de datos

Crea en phpMyAdmin una base de datos denominada `pwapractica6`.

Después, configura estas variables en `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pwapractica6
DB_USERNAME=root
DB_PASSWORD=
```

Para generar el administrador inicial, añade una contraseña temporal únicamente en tu entorno local:

```env
INITIAL_ADMIN_PASSWORD=ColocaAquiUnaClaveSegura
```

### 5. Crear las tablas y datos iniciales

```powershell
php artisan migrate:fresh --seed
```

El seeder principal crea los cinco roles, el inventario inicial de habitaciones y, cuando se configura `INITIAL_ADMIN_PASSWORD`, la cuenta `admin@hotel.test`.

### 6. Instalar y compilar los recursos frontend

```powershell
npm install
npm run build
```

Para trabajar con actualización automática:

```powershell
npm run dev
```

### 7. Iniciar la aplicación

Primero, inicia **MySQL** desde el panel de XAMPP. Luego ejecuta:

```powershell
php artisan serve
```

La aplicación estará disponible normalmente en:

```text
http://127.0.0.1:8000
```

---

## 🔑 Acceso de demostración

La versión publicada dispone de cuentas institucionales para comprobar los cinco roles.

Por seguridad, las contraseñas no se incluyen en el repositorio. Las credenciales de revisión deben compartirse directamente con el docente o evaluador.

Las cuentas demostrativas no deben reutilizarse como credenciales personales ni en un entorno comercial.

---

## 🧪 Pruebas automáticas

Para ejecutar la suite completa:

```powershell
php artisan test
```

La suite comprueba:

- Autenticación, cierre de sesión y limitación de intentos.
- Protección de cuentas desactivadas.
- Permisos correspondientes a cada rol.
- CRUD y filtros de habitaciones.
- Disponibilidad por intervalos de fechas.
- Creación y actualización de reservaciones.
- Protección de reservaciones ajenas.
- Gestión de necesidades y ofertas de suministros.
- Rechazo de ofertas sobre necesidades cerradas.
- Indicadores financieros, pagos y reembolsos.
- Navegación y presentación de los dashboards.

La versión estable del commit `1c32f48` fue verificada con:

```text
43 pruebas superadas
197 verificaciones realizadas
```

Además, la publicación fue comprobada directamente sobre MySQL de InfinityFree con los cinco roles.

---

## ✅ Flujo recomendado de prueba

1. Abrir la portada pública y revisar el catálogo de habitaciones.
2. Iniciar sesión como Administrador.
3. Revisar los usuarios y probar la activación o desactivación de una cuenta.
4. Registrar una necesidad de suministros.
5. Iniciar sesión como Gerente del hotel.
6. Crear o editar una habitación y revisar el panel financiero.
7. Iniciar sesión como Recepcionista.
8. Crear una reservación y actualizar su estado.
9. Iniciar sesión como Cliente.
10. Consultar habitaciones y crear una reservación propia.
11. Comprobar que el Cliente no pueda consultar reservaciones ajenas.
12. Iniciar sesión como Proveedor.
13. Consultar una necesidad abierta y presentar una oferta.
14. Regresar al Administrador y revisar los indicadores generales.

---

## 🌿 Flujo de ramas

- `feature/jesus`: autenticación, roles, usuarios y proveedores.
- `feature/terry`: reservaciones, disponibilidad y flujo de recepción.
- `feature/rosado`: habitaciones, presentación e integración.
- `main`: versión estable, revisada y publicada.

Las nuevas funciones deben desarrollarse en ramas independientes y pasar las pruebas antes de integrarse en `main`.

---

## 👨‍💻 Equipo de desarrollo

Proyecto desarrollado por estudiantes de la **Universidad Bolivariana del Ecuador**:

- Jesús Rafael Fernández Pérez
- José Andrés Merlin Gamboa
- José Alberto Rosado Domínguez
- Terry Steven Ponce Farías
- Elkin Daniel Velastegui Zambrano
- Zahid Joel Zambrano Moreira

---

## 📚 Contexto académico

- **Institución:** Universidad Bolivariana del Ecuador
- **Asignatura:** Programación Web Avanzada
- **Actividad:** Clase Práctica 6
- **Año:** 2026

Este proyecto aplica arquitectura MVC, autenticación, autorización, bases de datos relacionales, diseño responsive, pruebas automáticas, seguridad web y colaboración mediante Git y GitHub.

---

## 🔒 Consideraciones de seguridad

- El archivo `.env` no debe subirse al repositorio.
- No deben publicarse contraseñas reales ni credenciales de MySQL.
- Las contraseñas se almacenan mediante hash.
- Las rutas privadas utilizan autenticación, middleware y políticas.
- Las cuentas desactivadas no pueden conservar acceso al sistema.
- Los formularios emplean protección CSRF.
- La validación de datos se realiza en el servidor.
- El inicio de sesión limita los intentos fallidos.
- Las credenciales demostrativas deben compartirse de forma privada.
- `vendor`, `node_modules` y los archivos temporales no deben añadirse a Git.

---

## 📌 Alcance

La aplicación fue desarrollada con fines académicos. Permite simular la operación de un hotel, pero no representa un servicio comercial ni una plataforma institucional oficial de la Universidad Bolivariana del Ecuador.

---

### 🏨 Hotel Evorion

**Reserva con confianza. Administra con claridad.**
