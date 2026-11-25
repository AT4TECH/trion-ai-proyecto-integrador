# Implementation Plan: Correos electrónicos únicos en el módulo de clientes

**Branch**: `recuperacion/triana` | **Date**: 2026-11-25 | **Spec**: `specs/001-clientes-correo-unico/spec.md`

**Input**: Feature specification from `specs/001-clientes-correo-unico/spec.md`

## Summary

Se amplía la arquitectura MVC existente mediante un nuevo
método Cliente::existeEmail() con consultas preparadas PDO,
comparación sin distinguir mayúsculas y minúsculas, sin
equivalencia de tildes y exclusión del propio cliente.

La validación se incorporará en ClienteController::validar(),
conservando la validación de formato y el carácter opcional
del correo electrónico.

Las operaciones de creación y edición estarán protegidas
mediante un bloqueo compartido GET_LOCK() y RELEASE_LOCK()
sobre la misma conexión PDO, desde antes de la validación
hasta finalizar el guardado, con liberación mediante finally.

No se modificará el esquema ni se alterarán automáticamente
los registros preexistentes. La solución deberá verificarse
mediante pruebas funcionales y concurrentes en un entorno
aislado.

## Technical Context

**Language/Version**: PHP 8.2

**Primary Dependencies**: PDO, sesión, CSRF (helpers/funciones.php)

**Storage**: MySQL/MariaDB `sistema_ventas`; tabla `clientes` (`email VARCHAR(100) NULL`)

**Testing**: Validación de sintaxis PHP obligatoria; pruebas funcionales derivadas de los criterios de aceptación (ver `quickstart.md`), ejecutadas en entorno aislado

**Target Platform**: Aplicación web PHP/MySQL (XAMPP), estructura `public/`

**Project Type**: web-application MVC

**Performance Goals**: N/A — búsqueda previa SELECT LIMIT 1

**Constraints**: Sin cambios de esquema ni datos preexistentes; cambios mínimos en capa de modelo/controlador

**Scale/Scope**: Módulo de clientes existente; sin contratos externos

## Constitution Check

*GATE: Must pass before Phase 0 research. Re-check after Phase 1 design.*

- Conservación de la arquitectura PHP 8.2 — MySQL — MVC — PDO: se modifica `models/Cliente.php` y `controllers/ClienteController.php` (capas existentes); sin nuevos archivos de capa.
- Seguridad: validación del lado del servidor ya existente conservada; mensaje de error comprensible agregado (FR-005); sin nueva salida sin escape.
- Cambios mínimos y compatibilidad: comprobación de duplicados previa al INSERT/UPDATE; sin modificación ni eliminación de registros preexistentes (FR-006).
- Desarrollo SDD con trazabilidad: artefactos en `specs/001-clientes-correo-unico/`.
- Verificación con pruebas reales en entorno aislado: pruebas funcionales derivadas de los criterios de aceptación de la especificación.

## Project Structure

### Documentation (this feature)

```text
specs/001-clientes-correo-unico/
├── spec.md                # (/speckit.specify)
├── plan.md                # Este archivo (/speckit.plan)
├── research.md            # Phase 0
├── data-model.md          # Phase 1
├── quickstart.md          # Phase 1
└── checklists/            # (/speckit.checklist)
```

### Source Code (repository root)

Se conserva la estructura MVC existente:

- `models/Cliente.php`: nuevo método `existeEmail()` mediante
  consultas preparadas PDO.
- `controllers/ClienteController.php`: incorporación de la
  comprobación de correos duplicados dentro de `validar()`.
- `controllers/ClienteController.php`: protección de los
  métodos `crear()` y `actualizar()` mediante un bloqueo
  compartido, con tiempo de espera limitado y liberación
  mediante finally.

Se conservarán las validaciones existentes, los formatos
de retorno y el registro de auditoría.

**Structure Decision**: extensión de la arquitectura MVC
existente, reutilizando el patrón de `existeDocumento()`
y la conexión PDO compartida entre controlador y modelo.

## Complexity Tracking

| Violation | Why Needed | Simpler Alternative Rejected Because |
|-----------|------------|-------------------------------------|
| (ninguna) | (n/a) | (n/a) |
