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
