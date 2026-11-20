# Tareas — Filtrar el CSV de inventario por tipo

| ID | CA | Acción | Archivos | Prueba | Estado |
|---|---|---|---|---|---|
| T-01 | CA-01 a CA-04 | Validar tipos, enlazar el predicado opcional y conservar fechas y permisos. | `views/reportes/inventario.php`, `models/Reporte.php` | INV-01 a INV-03, INV-05 | Implementado. Lint PASS, HTTP SKIP |
| T-02 | CA-02, CA-04 | Incorporar el selector y propagarlo en los controles de Inventario. | `views/reportes/inventario.php`, `views/reportes/_inicio.php` | INV-02, INV-05 | Implementado. Revisión estática sin bloqueantes, HTTP SKIP |
| T-03 | CA-05 | Conservar el formato y reforzar la protección de textos del CSV de Inventario. | `views/reportes/inventario.php` | INV-04 | Implementado. Comprobación CSV local PASS, HTTP SKIP |
| T-04 | CA-01 a CA-05 | Preparar fixture, suite, controles y evidencia del candidato. | `tests/tarea-6/casos/reporte-inventario-filtro-tipo.php`, `tests/tarea-6/fixtures/crear-reporte-inventario.php`, runner, README y `evidencia.md` | INV-01 a INV-05 y HUMO | Suite implementada. Lint y diff PASS, HTTP SKIP |

| Caso | CA | Cobertura propuesta |
|---|---|---|
| INV-01 | CA-01, CA-04 | Comparar tipo ausente y vacío con todos los movimientos del intervalo. Comprobar bordes inclusivos, días fuera, fechas invertidas y orden. |
| INV-02 | CA-02, CA-04 | Probar los cuatro tipos, rango combinado y tipo sin filas. Comprobar conservación en formulario, atajos y enlace de descarga. |
| INV-03 | CA-03 | Probar valores desconocidos, no canónicos y arreglos. Esperar 400 genérico sin descarga. Revisar lista cerrada y enlace preparado. |
| INV-04 | CA-05 | Parsear con `fgetcsv` tras comprobar el BOM, sin dividir por líneas. Validar nueve encabezados y celdas, Unicode, delimitadores, comillas, saltos, fórmulas y decimales. |
| INV-05 | CA-04, CA-05 | Comprobar redirección anónima, 403 sin permiso y descarga autorizada. Comparar resumen, valoración, kardex y otro reporte, incluidas pestañas sin tipo de Inventario. |

INV-01 a INV-05 y HUMO-01 a HUMO-04 quedaron en SKIP por falta de configuración descartable. Ver [evidencia.md](evidencia.md).
