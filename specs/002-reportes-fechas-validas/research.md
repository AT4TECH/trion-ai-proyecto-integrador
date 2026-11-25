# Research — Rechazo de rangos de fechas invertidos en reportes de ventas

**Fecha**: 2026-11-25 | **Rama**: `recuperacion/triana` | **Spec**: `specs/002-reportes-fechas-validas/spec.md`

## Estado del comportamiento actual (antes de la corrección)

Se inspeccionó `controllers/ReporteController.php` (constructor) y `views/reportes/` (vistas de reportes). El comportamiento observado en el controlador era:

```php
if ($this->desde>$this->hasta) [$this->desde,$this->hasta]=[$this->hasta,$this->desde];
```

Es decir, los rangos invertidos se intercambiaban silenciosamente y el reporte se mostraba con las fechas reordenadas.

## Método de trabajo ejecutado

1. Verificación de sintaxis (`php -l`) sobre los archivos modificados.
2. Pruebas reales con el ejecutor `tests/spec-kit/002-reportes-fechas-validas/run-pruebas.php` (PHP CLI; carga los controladores reales de la aplicación; instancia `ReporteController` directamente; solo consultas de lectura sobre `sistema_ventas`; sin escritos en la base original).

## Hallazgos reales

- Con `new ReporteController($get, true)`: para desde > hasta se conservan las fechas y se establece `$error` con el mensaje comprensible.
- Con `new ReporteController($get)` (predeterminado `false`): se conserva el comportamiento original (intercambio de fechas).
- Rangos iguales y válidos (con `true`): sin error; `resumenVentas()` devuelve resultados reales de la base.

## Correcciones humanas documentadas

1. Validación ámbito: al finalizar, la validación rechaza rango invertido en reportes de ventas (flag `true` activado desde `views/reportes/ventas.php`); los demás reportes mantienen el intercambio original.
2. Conservación de la interfaz: el formulario de filtros, pestañas y accesos directos se conservan en `cabeceraReporte()`; el enlace de exportar a Excel se oculta solo cuando existe error de rango.
3. Corrección de nombre: `cabecedaReporte()` → `cabeceraReporte()` (coincide con la declaración en `views/reportes/_inicio.php`).

## Pendientes documentados

- Pruebas de interfaz (navegador): carga de `views/reportes/ventas.php` con rango invertido (mensaje, sin exportación).
- Prueba de exportación: no se ejecutó (no disponible herramienta HTTP/S); se marcará como pendiente.

## Ejecución real registrada

Ejecutores: `php -f tests/spec-kit/002-reportes-fechas-validas/run-pruebas.php` → `RESUMEN: PASS` (PASS=7 FAIL=0).

Ver `specs/002-reportes-fechas-validas/quickstart.md`.
