# Tasks: Rechazo de rangos de fechas invertidos en reportes de ventas

**Input**: Design documents from `/specs/002-reportes-fechas-validas/`

**Prerequisites**: plan.md (required), spec.md (required for user stories)

## Phase 1: Validación del rango en el controlador (US1)

- [x] T001 [US1] Modificar `ReporteController`: añadir `public string $error = ''` y rechazar el rango invertido (desde > hasta) con un mensaje comprensible, sin intercambiar las fechas — controllers/ReporteController.php

## Phase 2: Mensaje comprensible y falta de resultados/exportaciones (US2)

- [x] T002 [US2] Mostrar el mensaje de rechazo y evitar generar resultados ni exportaciones en `views/reportes/_inicio.php` (salida temprana)

## Phase 3: Verificación real

- [x] T003 [US1,US2] Ejecutar pruebas reales de rangos válidos, iguales e invertidos y `php -l` sobre los PHP modificados — tests/spec-kit/002-reportes-fechas-validas/run-pruebas.php

<!-- Nota: los checkmarks marcan tareas verificadas contra la implementación y las pruebas reales registradas en quickstart.md -->
