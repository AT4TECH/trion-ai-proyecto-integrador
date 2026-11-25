# Quickstart: Rechazo de rangos de fechas invertidos en reportes de ventas (002-reportes-fechas-validas)

Executor: `tests/spec-kit/002-reportes-fechas-validas/run-pruebas.php` (PHP 8.2 CLI; carga los controladores reales de la aplicaciÃ³n; instanciaciÃ³n directa de ReporteController).

EjecuciÃ³n: php -f tests/spec-kit/002-reportes-fechas-validas/run-pruebas.php

REAL_DB=sistema_ventas CONN_DB=sistema_ventas
Isolation: pruebas de lectura (esumenVentas()) sobre el estado actual; sin escritos reales en la base original.

Resultados reales (2026-11-25):

`
[PASS] INVERTIDO(true): mensaje comprensible con las fechas
[PASS] INVERTIDO(true): sin intercambio de fechas
[PASS] INVERTIDO(false): comportamiento original (intercambio)
[PASS] IGUAL: permitido y sin error
[PASS] IGUAL: se generan resultados para el dÃ­a
[PASS] VALIDO: sin error y rango conservado
[PASS] VALIDO: se generan resultados del rango
RESUMEN: PASS
`

RESUMEN: PASS=7 FAIL=0 TOTAL=7

php -l: sin errores en controllers/ReporteController.php, iews/reportes/_inicio.php, iews/reportes/ventas.php, 	ests/spec-kit/002-reportes-fechas-validas/run-pruebas.php.

Tareas marcadas [x]: T001, T002, T003, T004, T005 (verificadas contra este ejecutor). Pendientes: T006 (interfaz: rango invertido en iews/reportes/ventas.php), T007 (exportaciÃ³n a CSV con rango invertido) â€” sin ejecuciÃ³n real registrada.