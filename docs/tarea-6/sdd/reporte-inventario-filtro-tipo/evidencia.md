# Evidencia — Análisis, especificación y planificación

- Rama `feat/reporte-inventario-filtro-tipo`. HEAD `2b163c674b25bbf21a2cdd10159f662a418ddc71`.
- Fecha 2026-11-20. Entorno Git y PowerShell en Windows. Versión del cliente Codex sin verificar.
- `main` coincidía con la referencia local `origin/main`. El historial muestra las fusiones de las cuatro funcionalidades y del arnés. `dir` era el único archivo sin seguimiento inicial y queda fuera del cambio.

| Perfil ejecutado | Invocación | Resultado |
|---|---|---|
| `analista-requisitos` | `/root/analisis_reporte_tipo` | Trazó rutas, permisos, rango, columna y tipos versionados, formato y riesgos del CSV. |
| `probador` | `/root/pruebas_reporte_tipo` | Propuso cinco casos positivos, negativos, de seguridad y de regresión para una copia descartable. |

| Skill aplicada | Resultado |
|---|---|
| `brujula-semantica` | La sesión principal y el analista delimitaron el filtro a la exportación y definieron el tratamiento de entradas inválidas. |
| `planificar-sdd` | La sesión principal trazó plan y tareas después de la especificación, con Factor Ámbar 5/9. |
| `verificar-entrega` | El probador la aplicó al diseño. La validación del candidato queda pendiente. |
| `registrar-evidencia` | La sesión principal documentó rama, comandos, resultados y pendientes. |

| Control | Comando o fuente | Resultado | Estado |
|---|---|---|---|
| Base | `git --no-optional-locks status --short --branch`, `git rev-parse HEAD origin/main`, `git log -5 --first-parent --oneline` | Main y referencia remota local en 2b163c6. | Verificado |
| Rama | `git switch -c feat/reporte-inventario-filtro-tipo main` | Rama creada y activa. | Verificado |
| Contrato | Lectura de vista, cabecera, controlador, modelo, helper y DDL versionado | Enum, fechas, permiso y formato identificados sin consultar bases. | Verificado |
| Árbol final | `git --no-optional-locks status --short --branch --untracked-files=all`, `git diff --name-only HEAD` | Solo cuatro documentos nuevos y `dir`. | Verificado |
| Lint y HTTP | Comandos de [plan.md](plan.md) | Sin ejecución ni conexión a bases. | Pendiente |

La tabla anterior corresponde al cierre documental. El usuario autorizó la implementación en la misma rama.

## Implementación

El estado inicial confirmó `feat/reporte-inventario-filtro-tipo` en el SHA indicado arriba, con los cuatro documentos y `dir` como únicas entradas.

| Perfil ejecutado | Invocación | Resultado |
|---|---|---|
| `implementador-backend` | `/root/backend_inv` | Validación estricta, predicado preparado y protección CSV local al reporte. |
| `implementador-frontend` | `/root/frontend_inv` | Selector, formulario y enlaces. Integró la vista después de la entrega del bloque backend. |
| `probador` | `/root/test_inv` | INV-01 a INV-05, fixture, requisitos del runner y README. Incluyó regresión HTML y CSV de Productos. |
| `verificador` | `/root/review_inv` | Revisión independiente sin hallazgos bloqueantes. Confirmó permiso previo, SQL preparado, cabeceras, aislamiento y compatibilidad de la cabecera compartida. |

La sesión principal aplicó `verificar-entrega` y `registrar-evidencia`. El controlador conserva su delegación existente. La cabecera compartida activa el selector solo al recibir las opciones de Inventario. En los demás reportes las URLs decodificadas conservan los parámetros anteriores. El helper CSV compartido conserva su implementación. El prefijo adicional se aplica a campos textuales de Inventario antes de usar el mismo serializador.

| Control | Comando o pasos | Resultado |
|---|---|---|
| Lint PHP | `powershell -NoProfile -ExecutionPolicy Bypass -File tools/quality/lint-php.ps1` | PASS, PHP 8.2.12, 124 archivos, cero fallos. |
| Diferencias | `git diff --check` y `git diff --no-index --check` para los dos PHP nuevos | PASS, solo avisos LF a CRLF. |
| CSV local | Fragmento del bloque de exportación y helper real mediante `php` por stdin, con datos simulados y lectura `fgetcsv` | PASS, BOM, nueve columnas, decimales, separadores, comillas, texto multilínea y prefijo de fórmula. El primer intento falló al informar por `STDOUT` en modo stdin. Se corrigió el arnés temporal y se repitió con éxito. |
| HTTP Inventario | `php tests/tarea-6/run.php reporte-inventario-filtro-tipo --check-prerequisites` | SKIP INV-01 a INV-05, código 3. |
| HTTP Humo | `php tests/tarea-6/run.php humo --check-prerequisites` | SKIP HUMO-01 a HUMO-04, código 3. |

Las extensiones cURL, DOM, mbstring y PDO MySQL están instaladas. Faltan las variables `SV_TEST_*` de la instancia descartable. Se comprobó el árbol local con cambios y no se ejecutó HTTP ni se accedió a MySQL. Quedan pendientes ambas suites contra la copia atestada. El esquema y el índice de Git quedaron intactos. `dir` queda fuera del cambio. Sin commit, push ni PR.
