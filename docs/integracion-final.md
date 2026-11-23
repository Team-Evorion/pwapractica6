# Entrega de integración final

Fecha de verificación: 23 de noviembre de 2026.

## Alcance integrado

- Autenticación, cierre de sesión, limitación de intentos y control de cuentas activas.
- Cinco roles: Administrator, Hotel Manager, Receptionist, Customer y Supplier.
- Administración de usuarios con autorización y borrado seguro.
- Necesidades del hotel y ofertas vinculadas de proveedores.
- Catálogo y CRUD de habitaciones, búsqueda por tipo y filtros de precio.
- Reservaciones por intervalo, prevención de solapamientos y control de estados.
- Panel de recepción con selección de cliente, filtros y habitaciones disponibles.
- Navegación compartida entre los módulos y métricas administrativas reales.

## Contratos de integración

### Habitaciones

La tabla `rooms` conserva los campos académicos `id`, `room_number`, `room_type`, `room_price` e `is_available`. La disponibilidad por fechas no modifica `is_available`: se calcula excluyendo reservaciones `pending` o `confirmed` que se solapen con el intervalo solicitado.

### Reservaciones

Una reservación relaciona un `Customer` activo con una habitación. El cliente solo crea y consulta sus propias reservaciones. Receptionist y Administrator pueden crear reservaciones para un cliente y cambiar su estado. Supplier no tiene acceso al módulo.

### Autenticación y roles

Las rutas privadas usan autenticación y comprobación de cuenta activa. Las reglas de acceso se centralizan en middleware y policies. El seeder exige `INITIAL_ADMIN_PASSWORD`; la contraseña no está incluida en el repositorio.

## Verificación realizada

- `composer validate --strict`: aprobado.
- Análisis de sintaxis PHP: aprobado.
- Compilación de vistas Blade: aprobada.
- Carga de 31 rutas: aprobada.
- SQLite: 31 pruebas, 135 aserciones aprobadas.
- MariaDB 10.4.32: migración limpia, seed y 31 pruebas, 135 aserciones aprobadas.
- Datos del seed: 5 roles, 1 administrador, 8 habitaciones y 2 necesidades abiertas.
- Revisión responsive: 360 px, 768 px y 1280 px sin desbordamiento horizontal.

## Decisiones de seguridad

- No existe contraseña fija del administrador en el código.
- El identificador de usuario enviado por un Customer se ignora para impedir reservas a nombre de otra persona.
- La creación se ejecuta con transacción, bloqueo de habitación y comprobación de solapamientos.
- Una habitación con historial de reservaciones no se elimina; se devuelve un error controlado.
- Las ofertas solo pueden asociarse a necesidades abiertas.

## Preparación para GitHub

La integración se preparó en `integration/final-local`. Antes de publicar, el Tech Lead debe revisar el commit y decidir si crea un Pull Request hacia `develop`. No se debe cambiar la rama predeterminada ni hacer push directo a `main`.
