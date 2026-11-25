# Data Model — Rechazo de rangos de fechas invertidos en reportes de ventas

**Fecha**: 2026-11-25 | **Rama**: `recuperacion/triana` | **Spec**: `specs/002-reportes-fechas-validas/spec.md`

## Cambios realizados

- **Sin cambios de esquema**: no se ejecutó ninguna migración; el esquema de `sistema_ventas` se conserva intacto.
- **No se modifican las entidades ni sus tablas** (`ventas`, `detalle_venta`, `devoluciones`, `movimientos_inventario`, etc.).

## Entidades involucradas

- `ReporteController` (capa MVC existente): añade `public string $error = ''` y el parámetro opcional `bool $rechazarRangoInvertido = false` en `__construct(array $get, bool $rechazarRangoInvertido = false)`.
  - Desde > hasta + flag `true`: se conservan `$desde`/`$hasta` y se establece `$error`.
  - Desde > hasta + flag `false` (predeterminado): se conserva el comportamiento original (intercambio de fechas).
- `views/reportes/_inicio.php`: `$rep = new ReporteController($_GET, ($reporteActual ?? '') === 'ventas')`; la exportación se oculta en `cabeceraReporte()` si existe `$rep->error`.
- `views/reportes/ventas.php`: `$reporteActual = 'ventas'`; salida temprana con retorno tras establecer el título, cargar el layout, ejecutar `cabeceraReporte()` y mostrar el mensaje con `e()`.
- `models/Reporte.php`: sin cambios (consultas reciben rango válido).

## Validación en la aplicación

Pendiente de verificación visual: interfaz del reporte de ventas con rango invertido (mensaje visible, formulario conservado, sin exportar).
