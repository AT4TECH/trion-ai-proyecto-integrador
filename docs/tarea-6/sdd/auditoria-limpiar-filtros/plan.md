# Plan — Limpiar filtros de auditoría

## Rutas, capas y archivos

Añadir en `views/auditoria/index.php` un enlace «Limpiar filtros» junto a «Filtrar», con el estilo de botón secundario existente y destino escapado a la ruta base mediante `APP_URL`. Usar un enlace para que la navegación descarte toda la consulta y funcione con teclado. Mantener el formulario GET y el generador de enlaces de paginación/exportación. `controllers/AuditoriaController.php`, `models/Auditoria.php` y `helpers/permisos.php` son referencias para verificar el comportamiento.

## Datos, permisos y riesgos

La operación reutiliza la lectura filtrada de auditoría y el permiso `auditoria.ver`. La URL debe omitir la consulta completa, incluidos los parámetros de exportación y página. La vista debe cargar las fechas predeterminadas del servidor. Para verificar los resultados, usar datos sintéticos con fechas controladas según America/Bogota.

## Factor Ámbar

| Dimensión | Puntos | Justificación |
|---|---:|---|
| Superficie | 0 | Un control en la vista existente. |
| Datos | 1 | Se reutiliza la lectura filtrada de registros y resumen. |
| Estado y permisos | 0 | Se reutilizan la página inicial y el permiso vigente. |
| Verificación | 2 | Hay que comprobar filtros combinados, página posterior, resultados vacíos y regresiones. |
| **Total** | **3/9** | Caso directo y regresión, con límites de navegación y permiso. |

## Controles y preparación

Tras autorizar la implementación, usar exclusivamente una copia y una instancia descartables conforme al [README](../../../../tests/tarea-6/README.md). Preparar un usuario sintético con `auditoria.ver` y conservar al vendedor de humo sin ese permiso. Sembrar al menos 51 registros sintéticos que coincidan con los seis filtros, además de controles fuera de rango y no coincidentes. Usar descripciones identificables para separar el fixture de eventos de login. Conservar el marcador y la atestación previa al login y por respuesta.

Ejecutar `powershell -NoProfile -File tools/quality/lint-php.ps1`, `php tests/tarea-6/run.php auditoria-limpiar-filtros` una vez creado el caso y `php tests/tarea-6/run.php humo`. Registrar SHA, estado del candidato y resultados sanitizados. Repetir los controles si cambia el candidato.
