# Etapa 1 Fundacion del modulo de Habitaciones

## Objetivo

Crear la base de datos y el modelo de Habitaciones respetando el contrato académico y dejando datos repetibles para desarrollo y pruebas.

## Archivos propios del modulo

- app/Models/Room.php
- database/migrations/0000_01_01_000003_create_rooms_table.php
- database/factories/RoomFactory.php
- database/seeders/RoomSeeder.php
- tests/Feature/RoomFoundationTest.php

## Contrato implementado

Los campos académicos id, room_number, room_type, room_price e is_available conservan sus nombres. room_number permanece como entero y room_price usa precisión decimal de 10 dígitos con 2 decimales.

Se añadieron description, capacity e image_path para el catálogo. Ninguno modifica la responsabilidad de Terry sobre la disponibilidad por fechas.

## Integración pendiente

Esta etapa no modifica rutas, autenticación, navegación, proveedores ni reservaciones. RoomSeeder se ejecuta de forma independiente porque el DatabaseSeeder compartido debe ser corregido por Jesús.

## Etapa 2 CRUD y catalogo

La segunda etapa incorpora RoomController, Form Requests, RoomPolicy, rutas modulares, catálogo público, filtros por tipo y precio, panel de administración y formularios Blade.

Administrator y Hotel Manager pueden administrar habitaciones. Customer no puede acceder a las operaciones de gestión. El catálogo público solo presenta habitaciones con is_available activo.

El layout de Habitaciones permanece aislado para evitar conflictos con la navegación de Jesús. Durante la integración se decidirá si se conserva como layout propio o se incorpora al layout general.

El catálogo incluye cuatro fotografías WebP generadas para el proyecto, una por cada tipo de habitación. Los archivos se encuentran en public/images/rooms y no dependen de servicios externos.

## Verificacion responsive

El catálogo se comprobó con viewports reales de 360, 768 y 1280 píxeles.

- 360 px: una tarjeta por fila, filtro de 321 px dentro del viewport y sin desbordamiento horizontal.
- 768 px: dos tarjetas por fila y filtro de 696 px.
- 1280 px: tres tarjetas por fila y filtro de 1116 px.

Las tres medidas cargaron las ocho habitaciones, las hojas de estilo, JavaScript y las cuatro fotografías locales.
