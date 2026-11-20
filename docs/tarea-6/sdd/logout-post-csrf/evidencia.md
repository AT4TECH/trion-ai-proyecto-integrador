# Evidencia — Especificación y planificación

- Fecha de la sesión: 2026-11-19. Rama: `feat/logout-post-csrf`. HEAD: `3ffe03baf9f401acf15092c7647d95f84d771c2e`.
- Entorno observado: PowerShell 5.1.19041.6456 y Git 2.56.0.windows.2. Sesión Codex por API con colaboración; versión de cliente sin verificar.
- Árbol inicial en `main` con `dir` como único elemento sin seguimiento. La rama conserva el mismo HEAD y los documentos son cambios locales sin preparar. `dir` permanece fuera del cambio. Esta fase no produjo un candidato funcional.
- PR #1 corresponde al arnés y su fusión es el HEAD de partida. El ciclo funcional queda sin commit, push ni PR.

## Agentes y skills

Los perfiles `analista-requisitos`, `probador`, `implementador-backend`, `implementador-frontend` y `verificador` están configurados en `.codex/agents/` y detectados en el catálogo de `collaboration.spawn_agent`. En esta fase se invocaron los siguientes.

| Perfil | Evidencia de invocación | Resultado |
|---|---|---|
| `analista-requisitos` | `/root/spec_logout` | Ejecutó la revisión en lectura y devolvió un borrador con fuentes, alcance, orden de validación y CA-01 a CA-05. Incorporado en [spec.md](spec.md). |
| `probador` | `/root/pruebas_logout` | Invocado antes del reinicio de la sesión. Resultado no recuperado. |
| `probador` | `/root/pruebas_logout_recuperacion` | Ejecutó el diseño de pruebas en lectura y devolvió cobertura positiva, negativa y de regresión, con límites del arnés. Incorporado en [plan.md](plan.md) y [tasks.md](tasks.md). |

Los demás perfiles quedaron detectados. Las cuatro skills están configuradas en `.agents/skills/` y detectadas en el catálogo de la sesión.

| Skill | Invocación y resultado |
|---|---|
| `brujula-semantica` | Seleccionada por la sesión principal y aplicada por el analista al leer las rutas, la plantilla y AGENTS.md. Quedó definido que el POST anónimo redirige sin exigir token porque carece de operación de cierre autenticado. |
| `planificar-sdd` | Aplicada por la sesión principal sobre la especificación. Produjo trazado de capas, Factor Ámbar 8/9 y tareas vinculadas con criterios y casos. |
| `verificar-entrega` | Aplicada por el probador al diseño de cobertura. La validación de un candidato queda pendiente. |
| `registrar-evidencia` | Aplicada por la sesión principal para registrar comandos, SHA, delegaciones y pendientes. |

## Controles

| Control o CA | Comando o pasos | Esperado | Observado | Estado |
|---|---|---|---|---|
| Base | `git --no-optional-locks status --short --branch`, `git rev-parse HEAD`, `git log -1 --oneline` | `main` en la fusión del PR #1, solo `dir` sin seguimiento | Coincidió con lo esperado | Verificado |
| Rama | `git switch -c feat/logout-post-csrf main` | Rama activa desde el mismo HEAD | Primer intento sin permiso sobre `.git/index.lock`. La ejecución con permisos elevados creó y activó la rama | Verificado |
| Alcance documental | `git --no-optional-locks status --short --branch --untracked-files=all`, `git diff --check`, `git diff --cached --check` | Solo documentos nuevos y `dir`, sin cambios rastreados o preparados | Estado revisado y ambos `diff --check` sin salida. Los documentos nuevos se revisaron directamente | Verificado |
| Sintaxis PHP | `powershell -NoProfile -File tools/quality/lint-php.ps1` | Lint del candidato | Sin ejecutar en la fase documental | Pendiente |
| CA-01 a CA-05 | `php tests/tarea-6/run.php logout-post-csrf` | Casos de método, CSRF, sesión, cookie y auditoría | Solo propuesta. Sin solicitudes HTTP ni conexión a MySQL | Pendiente |
| Regresión | `php tests/tarea-6/run.php humo` | HUMO-01 a HUMO-04 | Sin ejecutar | Pendiente |

El usuario autorizó la implementación en la misma rama. Los controles de arriba corresponden al cierre de planificación.

## Implementación

Antes de editar, `git --no-optional-locks status --short --branch --untracked-files=all` confirmó `feat/logout-post-csrf` con los cuatro documentos SDD y el elemento preexistente `dir` como únicas entradas. El candidato son los cambios locales sobre el HEAD indicado arriba.

| Perfil | Invocación | Resultado |
|---|---|---|
| `implementador-backend` | `/root/backend_logout` | Implementó CA-01 a CA-05 en `public/logout.php` y comprobó la sintaxis del archivo. |
| `implementador-frontend` | `/root/frontend_logout` | Implementó el formulario POST y su foco de teclado para CA-04. Comprobó la sintaxis de la vista. |
| `probador` | `/root/test_logout` | Implementó siete casos y el soporte para atestar respuestas tempranas, inspeccionar cabeceras y comprobar auditoría en la copia. Lint individual PASS y prerrequisitos SKIP. |
| `verificador` | `/root/review_logout` | Aplicó `verificar-entrega` y los criterios de revisor-arquitectura. Detectó un hallazgo medio en la suite y verificó su corrección. Revisión estática final sin bloqueantes. |

La sesión principal aplicó `verificar-entrega` para contrastar el candidato con los criterios y `registrar-evidencia` para documentar comandos y resultados.

### Controles del candidato

| Control | Comando | Resultado |
|---|---|---|
| Sintaxis PHP | `powershell -NoProfile -File tools/quality/lint-php.ps1` | La política local impidió iniciar el script. |
| Sintaxis PHP | `powershell -NoProfile -ExecutionPolicy Bypass -File tools/quality/lint-php.ps1` | PASS. PHP 8.2.12, 116 archivos, cero fallos, código 0. La opción aplicó solo al proceso. |
| Diferencias | `git diff --check` y `git diff --cached --check` | PASS. Código 0. Git emitió avisos de conversión de finales de línea. |
| CA-01 a CA-05 y regresión | `php tests/tarea-6/run.php logout-post-csrf --check-prerequisites` | SKIP en LOGOUT-01, 02, 03, 04, 04-F, 05 y REG. Código 3. |
| Humo | `php tests/tarea-6/run.php humo --check-prerequisites` | SKIP en HUMO-01 a HUMO-04. Código 3. |

Las extensiones cURL, DOM, mbstring y PDO MySQL están presentes. Faltan las variables `SV_TEST_*` de una instancia descartable. Los chequeos se ejecutaron sobre el árbol local con cambios, sin probar una copia por HTTP ni conectar a MySQL.

La revisión detectó que algunos clientes nuevos enviaban la contraseña sintética antes de comprobar la atestación. La suite ahora exige un GET atestado antes de cada POST de inicio de sesión. El verificador confirmó la corrección y se repitieron los controles del candidato.

Quedan pendientes las dos suites contra la copia aislada, la comprobación del log técnico de LOGOUT-04-F y la revisión de foco y apariencia en navegador. El índice permanece sin cambios y `dir` fuera del candidato. No se creó commit ni PR y no se hizo push.
