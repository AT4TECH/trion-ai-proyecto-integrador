# Evidencia — Análisis, especificación y planificación

- Rama `feat/auditoria-limpiar-filtros`. HEAD `36f16b4ce3288642bf8fca7180ed6de1baa9fc8e`, fusión del PR #2 según el historial local.
- Fecha de la sesión 2026-11-19. Entorno PowerShell y Git en Windows. Versión del cliente Codex sin verificar.
- El cambio local consta de estos cuatro documentos sin preparar. `dir` era el único elemento sin seguimiento al inicio y queda fuera del cambio. Sin commit, push ni PR para esta funcionalidad.

## Agentes y skills

| Perfil | Invocación real | Resultado |
|---|---|---|
| `analista-requisitos` | `/root/analisis_auditoria` | Revisó vista, controlador, modelo y permiso. Identificó parámetros y valores predeterminados y propuso criterios y Factor Ámbar. |
| `probador` | `/root/pruebas_auditoria` | Diseñó cuatro casos trazados y detalló los datos sintéticos y el aislamiento requeridos. |

Los perfiles invocados figuran en `.codex/agents/` y fueron seleccionados en el catálogo de colaboración. `implementador-backend`, `implementador-frontend` y `verificador` quedaron detectados y sin invocar en esta fase.

| Skill | Uso real |
|---|---|
| `brujula-semantica` | Seleccionada por la sesión principal y aplicada por el analista. Delimitó el reset al estado inicial, incluido el rango por defecto. |
| `planificar-sdd` | Aplicada por la sesión principal. Produjo el plan con Factor Ámbar 3/9 y las tareas trazadas. |
| `verificar-entrega` | Aplicada por el probador solo al diseño de casos. La verificación de un candidato queda pendiente. |
| `registrar-evidencia` | Aplicada por la sesión principal para registrar resultados y pendientes. |

| Control | Comando o pasos | Resultado | Estado |
|---|---|---|---|
| Git inicial | `git --no-optional-locks status --short --branch --untracked-files=all`, `git log -1 --oneline main`, `git rev-parse main` | Main en 36f16b4 con solo `dir` sin seguimiento. | Verificado |
| Rama | `git switch -c feat/auditoria-limpiar-filtros main` | Rama creada y activada desde main. | Verificado |
| Alcance final | `git --no-optional-locks status --short --branch --untracked-files=all`, `git diff --name-only HEAD` | Solo los cuatro documentos nuevos y `dir`. Sin cambios en archivos rastreados. | Verificado |
| Lint y HTTP | Comandos previstos en [plan.md](plan.md) | Sin candidato funcional. No se ejecutaron pruebas ni se accedió a una base de datos. | Pendiente |

La tabla anterior corresponde al cierre de la fase documental. El usuario autorizó después la implementación en la misma rama.

## Implementación

Antes de editar, `git --no-optional-locks status --short --branch --untracked-files=all` y `git rev-parse HEAD` confirmaron la rama y el SHA indicados arriba. Los cuatro documentos y el archivo preexistente `dir` eran las únicas entradas. El candidato consiste en cambios locales sobre ese HEAD.

| Perfil | Invocación real | Entrega |
|---|---|---|
| `implementador-frontend` | `/root/frontend_auditoria` | Añadió el enlace escapado a la ruta base junto a «Filtrar». Lint individual y comprobación del diff PASS. |
| `probador` | `/root/test_auditoria` | Implementó AUD-01 a AUD-04, fixture sintético, guardas del runner y guía de ejecución. Lint individual PASS y prerrequisitos SKIP. |
| `verificador` | `/root/review_auditoria` | Revisión independiente sin hallazgos bloqueantes. Confirmó la ruta escapada, el permiso, los enlaces existentes y las comprobaciones de aislamiento. |

La sesión principal aplicó `verificar-entrega` y `registrar-evidencia` para contrastar el candidato con la especificación, ejecutar los controles seguros y registrar sus resultados.

| Control | Comando | Resultado |
|---|---|---|
| Sintaxis PHP | `powershell -NoProfile -ExecutionPolicy Bypass -File tools/quality/lint-php.ps1` | PASS. PHP 8.2.12, 118 archivos, 0 fallos, código 0. |
| Diferencias | `git diff --check` | PASS, código 0. Aviso de conversión LF a CRLF de la vista. |
| CA-01 a CA-04 | `php tests/tarea-6/run.php auditoria-limpiar-filtros --check-prerequisites` | SKIP AUD-01 a AUD-04, código 3. |
| Humo | `php tests/tarea-6/run.php humo --check-prerequisites` | SKIP HUMO-01 a HUMO-04, código 3. |

Las extensiones requeridas están presentes. Faltan las variables `SV_TEST_*` de la instancia descartable. Se comprobó el árbol local con cambios, sin ejecutar HTTP, sembrar el fixture ni conectar a MySQL. Queda pendiente correr ambas suites en la copia aislada. El fixture de auditoría y la suite deben ejecutarse el mismo día según America/Bogota, como indica el README.
