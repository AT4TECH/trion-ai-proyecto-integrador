# Feature Specification: Rechazo de rangos de fechas invertidos en reportes de ventas

**Feature Branch**: `recuperacion/triana`

**Created**: 2026-11-25

**Status**: Draft

**Input**: User description: "Implementar la segunda funcionalidad: rechazar rangos de fechas invertidos en los reportes de ventas, mostrando un mensaje comprensible cuando la fecha inicial sea posterior a la fecha final, en lugar de intercambiarlas silenciosamente."

## Clarifications

### Session 2026-11-25

- Q: ¿Cómo debe reaccionar el sistema cuando la fecha inicial es posterior a la fecha final? → A: Rechazar la operación con un mensaje comprensible; no intercambiar las fechas.
- Q: ¿Están permitidos rangos con fechas iguales? → A: Sí, rangos con fechas iguales y rangos válidos están permitidos.
- Q: ¿Dónde se debe validar? → A: Del lado del servidor (controlador).
- Q: ¿Qué debe hacer el sistema si existe un rango invertido en la exportación (CSV)? → A: No generar resultados ni exportaciones con rangos invertidos; mostrar el mensaje de rechazo.
- Q: ¿Qué comportamiento deben tener los demás reportes? → A: Conservar el comportamiento existente de los demás reportes.

## User Scenarios & Testing *(mandatory)*

### User Story 1 - Rechazar rango de fechas invertido (Priority: P1)

El usuario ingresa en un reporte de ventas una fecha inicial posterior a la fecha final.

**Why this priority**: Rechazar el rango invertido evita resultados ambiguos.

**Independent Test**: Ingresar desde > hasta; el reporte muestra un mensaje comprensible y no muestra resultados.

**Acceptance Scenarios**:

1. **Given** que desde > hasta, **When** se carga el reporte de ventas, **Then** se muestra un mensaje comprensible y no se generan resultados ni exportaciones.
2. **Given** que desde = hasta, **When** se carga el reporte, **Then** se muestran resultados para ese día.
3. **Given** que desde < hasta, **When** se carga el reporte, **Then** se muestran los resultados del rango.

### User Story 2 - Exportar solo rangos válidos (Priority: P2)

El usuario exporta a CSV un rango invertido.

**Independent Test**: Exportar con desde > hasta; se muestra el mensaje de rechazo y no se genera el CSV.

**Acceptance Scenarios**:

1. **Given** que desde > hasta, **When** se exporta el reporte, **Then** no se genera exportación y se muestra el mensaje comprensible.

### Edge Cases

- ¿Qué ocurre si el usuario ingresa fechas inválidas (formato) en desde o hasta? (Comportamiento existente: se usan los defaults; sin cambio.)
- ¿Qué ocurre si las fechas de accesos directos invierten el rango? (Si hasta < desde, el rango es rechazado igualmente.)

## Requirements *(mandatory)*

### Functional Requirements

- **FR-001**: System MUST reject when the initial date is later than the final date in sales reports, showing a comprehensible message.
- **FR-002**: System MUST allow equal dates and valid ranges.
- **FR-003**: System MUST validate on the server side (controller).
- **FR-004**: System MUST not generate results or CSV exports for inverted ranges.
- **FR-005**: System MUST preserve the existing behavior of the other reports.

### Key Entities *(include if feature involves data)*

- **Reporte de ventas**: entidad de reportes de ventas; rango de fechas (desde, hasta) con validación del servidor.

## Success Criteria *(mandatory)*

### Measurable Outcomes

- **SC-001**: Al ingresar un rango invertido (desde > hasta) en los reportes de ventas, se muestra un mensaje comprensible y no se muestran resultados ni exportaciones.
- **SC-002**: Rangos iguales y rangos válidos muestran resultados.
- **SC-003**: La validación ocurre del lado del servidor (controlador).

## Assumptions

- El módulo de reportes de ventas es el módulo existente (`ReporteController`, `views/reportes/`).
- El esquema de base de datos no se modifica.
- No se modifica la funcionalidad de otros reportes.
