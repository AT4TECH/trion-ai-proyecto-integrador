# Evidencia — Análisis, especificación y planificación

- Rama `feat/stock-bajo-categoria`. HEAD `c268bee50362a9b44dcc72cf1c3161a07bd22f90`.
- Git y PowerShell en Windows. Versión del cliente Codex sin verificar.
- El historial local muestra las fusiones de los PR #1 a #4. El nuevo ciclo queda sin commit, push ni PR.
- El único archivo sin seguimiento al iniciar era `dir`. Queda fuera del cambio.

| Perfil ejecutado | Invocación | Resultado |
|---|---|---|
| `analista-requisitos` | `/root/analisis_stock` | Analizó vista, controlador, modelos, DDL, permisos y enlaces. Confirmó que el filtro preparado ya existe y propuso criterios. |
| `probador` | `/root/pruebas_stock` | Propuso casos positivos, negativos y de regresión y la preparación aislada. |

| Skill aplicada | Resultado |
|---|---|
| `brujula-semantica` | La sesión principal y el analista delimitaron el filtro a la tabla, con tarjetas globales y categorías activas. |
| `planificar-sdd` | La sesión principal trazó el plan y las tareas después de la especificación. Factor Ámbar 3/9. |
| `verificar-entrega` | El probador la aplicó al diseño de casos. La verificación de un candidato queda pendiente. |
| `registrar-evidencia` | La sesión principal registró los resultados y pendientes. |

| Control | Comando o fuente | Resultado | Estado |
|---|---|---|---|
| Base | `git --no-optional-locks status --short --branch`, `git rev-parse HEAD`, `git log -4 --first-parent --oneline` | Main en c268bee con solo `dir` sin seguimiento. | Verificado |
| Rama | `git switch -c feat/stock-bajo-categoria main` | Rama creada y activa. | Verificado |
| Campo y alcance | Lectura de la vista, controlador, modelos y DDL versionados | FK confirmada. Sin paginación ni exportación en Stock bajo. | Verificado |
| Árbol final | `git --no-optional-locks status --short --branch --untracked-files=all`, `git diff --name-only HEAD` | Cuatro documentos nuevos y `dir`. | Verificado |
| Lint y HTTP | Comandos de [plan.md](plan.md) | Sin ejecución ni conexión a bases de datos. | Pendiente |

La tabla anterior corresponde al cierre documental. El usuario autorizó la implementación en esta misma rama.

## Implementación

Antes de editar, `git --no-optional-locks status --short --branch --untracked-files=all` y `git rev-parse HEAD` confirmaron `feat/stock-bajo-categoria` en el SHA indicado. Los cuatro documentos SDD y `dir` eran las únicas entradas.

| Perfil | Invocación | Entrega |
|---|---|---|
| `implementador-frontend` | `/root/frontend_stock` | Implementó el selector GET, validación y mensaje vacío en la vista. Lint individual PASS. |
| `probador` | `/root/test_stock` | Implementó STK-01 a STK-04, fixture sintético, guardas del runner y README. Lint individual PASS y prerrequisitos SKIP. |
| `verificador` | `/root/review_stock` | Revisión independiente sin hallazgos bloqueantes. Confirmó permisos, filtro preparado, escape HTML, enlaces, resumen global y aislamiento del fixture. |

La sesión principal aplicó `verificar-entrega` y `registrar-evidencia` para contrastar el candidato con la especificación y documentar los controles.

| Control | Comando | Resultado |
|---|---|---|
| Sintaxis PHP | `powershell -NoProfile -ExecutionPolicy Bypass -File tools/quality/lint-php.ps1` | PASS. PHP 8.2.12, 122 archivos, cero fallos, código 0. |
| Diferencias | `git diff --check` | PASS, código 0. Aviso de conversión LF a CRLF en la vista. |
| CA-01 a CA-04 | `php tests/tarea-6/run.php stock-bajo-categoria --check-prerequisites` | SKIP STK-01 a STK-04, código 3. |
| Humo | `php tests/tarea-6/run.php humo --check-prerequisites` | SKIP HUMO-01 a HUMO-04, código 3. |

Las extensiones cURL, DOM, mbstring y PDO MySQL están presentes. Faltan las variables `SV_TEST_*` de la instancia descartable. Se comprobó el candidato local con cambios, sin realizar HTTP, cargar el fixture ni conectar a la base. Queda pendiente ejecutar ambas suites contra la copia atestada.

El esquema y el índice de Git quedaron intactos. `dir` queda fuera del cambio. Sin commit, push ni PR.
