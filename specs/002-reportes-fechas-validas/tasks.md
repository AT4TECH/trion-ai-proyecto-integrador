# Tasks: Rechazo de rangos de fechas invertidos en reportes de ventas

**Input**: Design documents from `/specs/002-reportes-fechas-validas/`

**Prerequisites**: plan.md (required), spec.md (required for user stories)

## Phase 1: Validación del rango en el controlador (US1)

- [x] T001 [US1] Modificar `ReporteController`: añadir `public string $error = ''` y parámetro opcional `bool $rechazarRangoInvertido = false`; rechazar rango invertido (desde > hasta) con mensaje comprensible conservando las fechas cuando flag es `true`; conservar el intercambio original cuando es `false` (predeterminado) — controllers/ReporteController.php

## Phase 2: Mensaje comprensible y falta de resultados/exportaciones; ámbito de ventas (US2)

- [x] T002 [US2] En `views/reportes/ventas.php`: establecer `$reporteActual = 'ventas'` y, tras incluir `_inicio.php`, si hay error: cargar `layouts/header.php`, ejecutar `cabeceraReporte($rep, 'ventas', $pestanas)`, mostrar el mensaje con `e()` y `return` antes de consultar o exportar
- [x] T003 [US2] En `views/reportes/_inicio.php`: instanciar `new ReporteController($_GET, ($reporteActual ?? '') === 'ventas')` y ocultar el enlace de exportar en `cabecedaReporte()` cuando haya error (conservando el formulario, pestañas y accesos directos); corrección aplicada: nombre `cabeceraReporte()` — views/reportes/_inicio.php, views/reportes/ventas.php

## Phase 3: Verificación real

- [x] T004 [US1,US2] Actualizar y ejecutar `tests/spec-kit/002-reportes-fechas-validas/run-pruebes.php`: rangos invertidos (`true`), comportamiento original con `false`, iguales y válidos → RESUMEN: PASS
- [x] T005 [US1,US2] Ejecutar `php -l` sobre los archivos modificados: `controllers/ReporteController.php`, `views/reportes/_inicio.php`, `views/reportes/ventas.php`, `tests/spec-kit/002-reportes-fechas-validas/run-pruebas.php`

## Pendientes de documentación/verificación

- [ ] T006 Prueba de interfaz: carga de `views/reportes/ventas.php` con rango invertido (mensaje visible, formulario conservado, sin exportación) — pendiente de ejecución de interface (verificado por inspección de código en quickstart.md).
- [ ] T007 Prueba de exportación con rango invertido — pendiente de herramienta HTTP/S para ejecución real.

<!-- Nota: T004/T005 verificados contra las pruebas reales registradas en quickstart.md; T006/T007 pendientes de ejecución de interfaz/exportación. -->
