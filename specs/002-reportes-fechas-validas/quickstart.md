# Quickstart: Rechazo de rangos de fechas invertidos en reportes de ventas (002-reportes-fechas-validas)

Executor: `tests/spec-kit/002-reportes-fechas-validas/run-pruebas.php` (PHP 8.2 CLI; carga los controladores/vistas reales de la aplicación).

Ejecución: `php -f tests/spec-kit/002-reportes-fechas-validas/run-pruebas.php`

REAL_DB=sistema_ventas CONN_DB=sistema_ventas
Isolation: sin BD aislada; pruebas de lectura (`resumenVentas()`) solo sobre el estado actual; sin escrites reales.

Resultados reales (2026-11-25):

```
[PASS] INVERTIDO: mensaje comprensible con las fechas
[PASS] INVERTIDO: sin intercambio de fechas
[PASS] IGUAL: permitido y sin error
[PASS] IGUAL: se generan resultados para el día
[PASS] VALIDO: sin error y rango conservado
[PASS] VALIDO: se generan resultados del rango
RESUMEN: PASS
```

RESUMEN: PASS=6 FAIL=0 TOTAL=6

php -l: sin errores en controllers/ReporteController.php y views/reportes/_inicio.php

Tareas marcadas [x]: T001, T002, T003 (verificadas contra este ejecutor).
