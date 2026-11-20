# Tareas — Filtrar stock bajo por categoría

| ID | CA | Acción | Archivos | Prueba | Estado |
|---|---|---|---|---|---|
| T-01 | CA-01 a CA-04 | Incorporar selector, validar la categoría, aplicar el filtro y ajustar el mensaje vacío. | `views/productos/stock.php` | STK-01 a STK-04 | Implementado. Lint PASS, HTTP SKIP |
| T-02 | CA-01 a CA-04 | Agregar casos automatizados y datos sintéticos aislados. | `tests/tarea-6/casos/stock-bajo-categoria.php`, `tests/tarea-6/fixtures/crear-stock-bajo.php`, runner y README | STK-01 a STK-04 | Implementado. Ejecución aislada pendiente |
| T-03 | CA-01 a CA-04 | Revisar el candidato y ejecutar lint, suite funcional y humo. | `evidencia.md` | Lint, diff, STK y HUMO | Revisión estática sin bloqueantes. Lint y diff PASS, HTTP SKIP |

| Caso | CA | Verificación automatizada |
|---|---|---|
| STK-01 | CA-01 | Seleccionar una categoría activa. Incluir productos activos agotados, por debajo e iguales al mínimo. Excluir inactivos, productos por encima del mínimo y de otras categorías. |
| STK-02 | CA-02 | Comparar ruta base y «Todas», incluidas filas de categorías inactivas, orden y tarjetas globales. |
| STK-03 | CA-03 | Comprobar valores malformados, arreglo, ID fuera de rango, inexistente e inactivo contra la vista base. Verificar el estado vacío de una categoría activa válida. |
| STK-04 | CA-04 | Comprobar visitante, usuario sin permiso, lector y editor. Verificar visibilidad del costo y edición, y destinos de los enlaces. |

STK-01 a STK-04 y HUMO-01 a HUMO-04 están en SKIP por falta de configuración descartable. Ver [evidencia.md](evidencia.md).
