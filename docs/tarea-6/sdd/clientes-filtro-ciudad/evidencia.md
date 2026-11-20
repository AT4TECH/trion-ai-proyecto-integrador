# Evidencia — Análisis, especificación y planificación

- Rama `feat/clientes-filtro-ciudad`. HEAD `458e13f05ac21f81628d0ee402c94b45c1cf2bb8`.
- Fecha de la sesión 2026-11-19. Entorno Git y PowerShell en Windows. Versión del cliente Codex sin verificar.
- El historial local registra las fusiones de los PR #1, #2 y #3. La nueva funcionalidad queda sin commit, push ni PR.
- Árbol inicial con `dir` como único archivo sin seguimiento. Los cuatro documentos quedan sin preparar. No se accedió a bases de datos ni se cambió el esquema.

## Delegaciones ejecutadas

| Perfil | Invocación | Resultado |
|---|---|---|
| `analista-requisitos` | `/root/analisis_clientes` | Analizó las rutas y el DDL versionado, confirmó la columna y detectó el límite de 500 sin paginación. Propuso criterios y Factor Ámbar. |
| `probador` | `/root/pruebas_clientes` | Propuso cinco casos positivos, negativos y de regresión, con requisitos de datos y aislamiento. |

## Skills aplicadas

| Skill | Resultado |
|---|---|
| `brujula-semantica` | Aplicada por el analista y la sesión principal. Definió coincidencia por valor completo, restablecimiento y alcance de la paginación nueva. |
| `planificar-sdd` | Aplicada por la sesión principal después de redactar la especificación. Produjo el plan de 5/9 y las tareas trazadas. |
| `verificar-entrega` | Aplicada por el probador al diseño. Validación funcional pendiente. |
| `registrar-evidencia` | Aplicada por la sesión principal para registrar rama, comandos, delegaciones y pendientes. |

| Control | Comando o pasos | Resultado | Estado |
|---|---|---|---|
| Base Git | `git --no-optional-locks status --short --branch --untracked-files=all`, `git rev-parse HEAD`, `git log --first-parent -4 --oneline` | Base main en 458e13f, solo `dir` sin seguimiento. | Verificado |
| Rama | `git switch -c feat/clientes-filtro-ciudad main` | Rama creada y activada. | Verificado |
| Campo | `rg -n 'ciudad|CREATE TABLE .*clientes' tests/tarea-6/fixtures/esquema.sql views/clientes/_form.php` y lectura del modelo y controlador | Columna versionada `clientes.ciudad VARCHAR(100) NULL`, usada por la aplicación. | Verificado |
| Alcance final | `git --no-optional-locks status --short --branch --untracked-files=all`, `git diff --name-only HEAD` | Solo cuatro documentos nuevos y `dir`. | Verificado |
| Lint y HTTP | Comandos previstos en [plan.md](plan.md) | Sin ejecución en la fase documental. | Pendiente |

Los controles anteriores corresponden a la fase documental. El usuario autorizó la implementación en esta misma rama.

## Implementación

Antes de editar, `git --no-optional-locks status --short --branch --untracked-files=all` y `git rev-parse HEAD` confirmaron la rama y el SHA indicados arriba. Los cuatro documentos y `dir` eran las únicas entradas. El candidato consiste en cambios locales sobre ese HEAD.

| Perfil | Invocación | Resultado |
|---|---|---|
| `implementador-backend` | `/root/backend_clientes` | Implementó filtros compartidos, conteo y consulta paginada. Lint individual y diff PASS. |
| `implementador-frontend` | `/root/frontend_clientes` | Implementó el campo Ciudad, validación GET y navegación con filtros. Lint individual y diff PASS. |
| `probador` | `/root/test_ciudad` | Implementó CLI-01 a CLI-05, fixture aislado, guardas del runner y documentación. Añadió el 403 para POST de un lector sin permiso de escritura. Lint individual PASS y prerrequisitos SKIP. |
| `verificador` | `/root/review_ciudad` | Revisó seguridad, rendimiento, compatibilidad y regresiones. Sin hallazgos bloqueantes en la revisión estática. |

La sesión principal aplicó `verificar-entrega` y `registrar-evidencia` para revisar el candidato y registrar los controles.

| Control | Comando | Resultado |
|---|---|---|
| Sintaxis PHP | `powershell -NoProfile -ExecutionPolicy Bypass -File tools/quality/lint-php.ps1` | PASS. PHP 8.2.12, 120 archivos, 0 fallos, código 0. |
| Diferencias | `git diff --check` | PASS, código 0. Avisos LF a CRLF en los tres PHP del módulo. |
| CA-01 a CA-05 | `php tests/tarea-6/run.php clientes-filtro-ciudad --check-prerequisites` | SKIP CLI-01 a CLI-05, código 3. |
| Humo | `php tests/tarea-6/run.php humo --check-prerequisites` | SKIP HUMO-01 a HUMO-04, código 3. |

Las extensiones cURL, DOM, mbstring y PDO MySQL están presentes. Faltan las variables `SV_TEST_*` de la instancia descartable. Se comprobó el candidato local sin ejecutar HTTP, sembrar el fixture ni conectar a MySQL. Quedan pendientes ambas suites en la copia aislada.

La revisión confirmó los predicados preparados compartidos, el orden estable, los parámetros de página enteros y el agregado de ventas limitado a la página seleccionada. El conteo y la ordenación se resuelven en MySQL. El plan de ejecución y el rendimiento real siguen sin medir. Las guardas POST y CSRF se comprobaron por revisión del diff.

El esquema y el índice de Git quedaron intactos. `dir` sigue fuera del cambio. Sin commit, push ni PR.
