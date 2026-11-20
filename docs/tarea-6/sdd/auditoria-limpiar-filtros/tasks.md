# Tareas — Limpiar filtros de auditoría

| ID | CA | Acción | Archivos | Prueba | Estado |
|---|---|---|---|---|---|
| T-01 | CA-01 a CA-04 | Agregar el enlace GET a la ruta limpia junto a Filtrar. | `views/auditoria/index.php` | AUD-01 a AUD-04 | Implementado, revisión estática PASS |
| T-02 | CA-01 a CA-04 | Agregar casos y datos sintéticos para probar navegación y permisos en la copia. | `tests/tarea-6/casos/auditoria-limpiar-filtros.php`, `tests/tarea-6/fixtures/crear-auditoria.php`, runner y README | AUD-01 a AUD-04 y HUMO-04 | Implementado, HTTP SKIP |
| T-03 | CA-01 a CA-04 | Revisar el candidato, ejecutar lint, casos y humo, y registrar resultados. | `evidencia.md` | Lint, diff, AUD-01 a AUD-04 y HUMO-01 a HUMO-04 | Lint y diff PASS, HTTP SKIP |

| Caso | CA | Tipo | Verificación esperada |
|---|---|---|---|
| AUD-01 | CA-01, CA-02 | Positiva | Activar los seis filtros y navegar a la página 2 real. Seguir «Limpiar filtros» y comprobar ruta sin consulta, fechas predeterminadas, selectores y texto restablecidos. Comparar listado y resumen con una visita directa y confirmar página 1. |
| AUD-02 | CA-02 | Límite | Limpiar desde una búsqueda sin coincidencias y repetir la acción en la vista inicial. Comprobar que se conserva la primera página por defecto y se muestra HTML en lugar de una descarga. |
| AUD-03 | CA-03, CA-04 | Regresión | Aplicar criterios nuevos después de limpiar, comprobar la página 1 y seguir Siguiente y Anterior con los filtros activos. Confirmar que el CSV contiene resultados filtrados. |
| AUD-04 | CA-04 | Negativa | Acceder a la ruta limpia sin sesión y con el vendedor sin permiso. Comprobar redirección al login y 403, respectivamente. |

AUD-01 a AUD-04 y HUMO-01 a HUMO-04 quedaron en SKIP por falta de configuración descartable. Consultar [evidencia.md](evidencia.md).
