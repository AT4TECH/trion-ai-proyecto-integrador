# Implementation Plan: Rechazo de rangos de fechas invertidos en reportes de ventas

**Branch**: `recuperacion/triana` | **Date**: 2026-11-25 | **Spec**: `specs/002-reportes-fechas-validas/spec.md`

**Input**: Feature specification from `specs/002-reportes-fechas-validas/spec.md`

## Summary

Se modifica `ReporteController` para rechazar (sin intercambiar) rangos invertidos cuando se pasa `bool $rechazarRangoInvertido = true`, y se añade un mensaje comprensible (`public string $error`). Las vistas de reportes muestran el mensaje y no generan resultados ni exportaciones cuando el rango es inválido. La validación se aplica ámbito de reportes de ventas (`views/reportes/ventas.php`); los demás reportes conservan el comportamiento original de intercambio de fechas.

## Technical Context

**Language/Version**: PHP 8.2
**Primary Dependencies**: PDO, helpers/funciones.php, helpers/reportes.php
**Storage**: MySQL/MariaDB `sistema_ventas` (sin cambios de esquema)
**Testing**: Pruebas reales sobre base aislada (copia del sistema + BD aislada, sin datos reales); ejecutor `tests/spec-kit/002-reportes-fechas-validas/run-pruebas.php`
**Target Platform**: Aplicación web PHP/MySQL (XAMPP), estructura `public/`
**Project Type**: web-application MVC
**Performance Goals**: N/A
**Constraints**: Sin cambios de esquema; sin alterar comportamiento de otros reportes; validación del lado del servidor.
**Scale/Scope**: Módulo de reportes de ventas existente (ReporteController, views/reportes/).

## Constitution Check

- Conservación de la arquitectura PHP 8.2 - MVC - PDO: modificación en `controllers/ReporteController.php` y `views/reportes/_inicio.php`/`views/reportes/ventas.php` (capa existente); sin nuevos archivos de capa.
- Seguridad: validación del lado del servidor (controlador); salida del mensaje escapada con `e()`.
- Cambios mínimos: sin cambios de esquema; comportamiento de otros reportes conservado.
- Desarrollo SDD con trazabilidad: artefactos en `specs/002-reportes-fechas-validas/`.
- Verificación en entorno aislado: pruebas reales sobre BD (solo consultas de lectura).

## Project Structure

### Documentation (this feature)

```text
specs/002-reportes-fechas-validas/
├── spec.md
├── plan.md
├── research.md
├── data-model.md
├── quickstart.md
└── checklists/
```

### Source Code (repository root)

- `controllers/ReporteController.php`: añadir `public string $error = ''` y el parámetro opcional `bool $rechazarRangoInvertido = false` en `__construct()`; si `desde > hasta` y flag `true` → mensaje de rechazo sin intercambio; si `false` (predeterminado) → comportamiento original de intercambio.
- `views/reportes/_inicio.php`: instanciar `new ReporteController($_GET, ($reporteActual ?? '') === 'ventas')`; ocultar el enlace de exportar cuando existe error (el formulario, pestañas y accesos directos se conservan).
- `views/reportes/ventas.php`: establecer `$reporteActual = 'ventas'`; tras incluir `_inicio.php`, si existe `$rep->error`: establecer `$titulo`, cargar `layouts/header.php`, ejecutar `cabeceraReporte($rep, 'ventas', $pestanas)`, mostrar el mensaje con `e()` y `return` antes de cualquier consulta o exportación.

**Structure Decision**: modificación dentro de la arquitectura MVC existente.

## Complexity Tracking

| Violation | Why Needed | Simpler Alternative Rejected Because |
|-----------|------------|-------------------------------------|
| (ninguna) | (n/a) | (n/a) |
