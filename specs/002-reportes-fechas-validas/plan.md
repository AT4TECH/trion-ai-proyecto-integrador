# Implementation Plan: Rechazo de rangos de fechas invertidos en reportes de ventas

**Branch**: `recuperacion/triana` | **Date**: 2026-11-25 | **Spec**: `specs/002-reportes-fechas-validas/spec.md`

**Input**: Feature specification from `specs/002-reportes-fechas-validas/spec.md`

## Summary

Se modifica `ReporteController` para rechazar (sin intercambiar) rangos invertidos y se añade un mensaje comprensible; las vistas de reportes muestran el mensaje y no generan resultados ni exportaciones cuando el rango es inválido.

## Technical Context

**Language/Version**: PHP 8.2
**Primary Dependencies**: PDO, helpers/funciones.php, helpers/reportes.php
**Storage**: MySQL/MariaDB `sistema_ventas` (sin cambios de esquema)
**Testing**: Pruebas reales sobre base aislada (copia del sistema + BD aislada, sin datos reales)
**Target Platform**: Aplicación web PHP/MySQL (XAMPP), estructura `public/`
**Project Type**: web-application MVC
**Performance Goals**: N/A
**Constraints**: Sin cambios de esquema; sin alterar comportamiento de otros reportes; validación del lado del servidor.
**Scale/Scope**: Módulo de reportes de ventas existente (ReporteController, views/reportes/).

## Constitution Check

- Conservación de la arquitectura PHP 8.2 — MVC — PDO: modificación en `controllers/ReporteController.php` (capa existente); sin nuevos archivos de capa.
- Seguridad: validación del lado del servidor (controlador); sin nueva salida sin escape.
- Cambios mínimos: sin cambios de esquema; sin alterar otros reportes.
- Desarrollo SDD con trazabilidad: artefactos en `specs/002-reportes-fechas-validas/`.
- Verificación en entorno aislado: pruebas reales sobre BD aislada.

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

- `controllers/ReporteController.php`: añadir `public string $error = ''` y validar en `__construct()` (sin intercambio de fechas).
- `views/reportes/_inicio.php`: mostrar el mensaje de error en `cabeceraReporte()` cuando esté presente.
- Vistas de reportes (ventas, inventario, etc.): retornar temprano si hay error, sin exportar ni consultar.

**Structure Decision**: modificación dentro de la arquitectura MVC existente.

## Complexity Tracking

| Violation | Why Needed | Simpler Alternative Rejected Because |
|-----------|------------|-------------------------------------|
| (ninguna) | (n/a) | (n/a) |
