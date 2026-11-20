# Tareas — Filtrar clientes por ciudad

| ID | CA | Acción | Archivos | Prueba | Estado |
|---|---|---|---|---|---|
| T-01 | CA-01 a CA-03, CA-05 | Implementar condiciones compartidas, conteo, consulta paginada y límites de página. | `models/Cliente.php`, `controllers/ClienteController.php` | CLI-01 a CLI-03, CLI-05 | Implementado. Lint PASS, HTTP SKIP |
| T-02 | CA-01 a CA-05 | Incorporar Ciudad y enlaces GET, conservar el permiso y el nuevo envío desde página 1. | `views/clientes/index.php` | CLI-01 a CLI-05 | Implementado. Lint PASS, HTTP SKIP |
| T-03 | CA-01 a CA-05 | Preparar y ejecutar casos con datos sintéticos en la instancia aislada. | `tests/tarea-6/casos/clientes-filtro-ciudad.php`, `tests/tarea-6/fixtures/crear-clientes.php`, runner y README | CLI-01 a CLI-05, HUMO-01 a HUMO-04 | Implementado. HTTP SKIP, ejecución aislada pendiente |
| T-04 | CA-01 a CA-05 | Revisar el candidato, ejecutar lint y registrar resultados y SHA. | `evidencia.md` | Lint, diff y suites del plan | Revisión estática sin bloqueantes. Lint y diff PASS |

| Caso | CA | Tipo | Verificación esperada |
|---|---|---|---|
| CLI-01 | CA-01 | Positiva | Ciudad Unicode con espacios externos conserva el valor recortado y coincide por valor completo. Excluye prefijos similares, ciudad nula y vacía. Comprobar límite de 100 caracteres. |
| CLI-02 | CA-02 | Positiva y negativa | Combinar ciudad, búsqueda en los campos existentes y estados. Excluir un control por cada condición y comprobar el total y el estado vacío para ciudad desconocida. |
| CLI-03 | CA-03, CA-04 | Integración | Obtener dos páginas reales, navegar en ambos sentidos y conservar los tres filtros, incluido Todos. Verificar orden estable, ausencia de filas repetidas y límites de página válidos e inválidos. |
| CLI-04 | CA-01, CA-04 | Límite | Reenviar Ciudad vacía, omitida, con espacios o como arreglo conservando los demás filtros. Comprobar página 1 al reenviar y valores iniciales al abrir la ruta base. |
| CLI-05 | CA-02, CA-05 | Regresión | Confirmar permisos, búsqueda y agregados de compras sin ciudad activa. Verificar el agregado con compras anuladas y el cliente con cero compras. |

CLI-01 a CLI-05 y HUMO-01 a HUMO-04 quedaron en SKIP por falta de configuración descartable. Consultar [evidencia.md](evidencia.md).
