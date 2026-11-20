# Evidencia del arnés preparatorio

Rama `chore/arnes-tarea-6`. Se comprobó que HEAD, `main`, `origin/main` y el resultado de `git fetch origin main` apuntaban a `3200224841231ba1cc4e574d954e872c97a96845` antes de crear la rama. Las comprobaciones se ejecutaron sobre el árbol de trabajo con cambios locales sin commit.

| Comprobación ejecutada | Resultado observado |
|---|---|
| `codex.cmd --version` en el diagnóstico | `codex-cli 0.170.0`. La consulta anterior de funciones del perfil falló al resolver el directorio de inicio. |
| Análisis de TOML con Python 3.12 y `tomllib` | Cinco perfiles válidos sintácticamente. Se comprobaron los campos `name`, `description` y `developer_instructions`. |
| Comprobación local de metadatos de skills | Cuatro bloques con `name` y `description`, nombre consistente con carpeta, cuerpo presente y YAML de presentación conforme a la estructura esperada. La comprobación fue estructural. |
| Verificación estática de los fixtures SQL | 21 declaraciones `CREATE TABLE` y ausencia de declaraciones `DROP`, `TRUNCATE`, `USE` y `ALTER`. No se importaron los fixtures. |
| `powershell -NoProfile -ExecutionPolicy Bypass -File tools/quality/lint-php.ps1` | PHP 8.2.12, 114 archivos revisados, 0 fallos, salida 0. La invocación directa previa quedó bloqueada por la política local. |
| `php tests/tarea-6/run.php` | Mostró el uso del runner y devolvió 2. |
| `php tests/tarea-6/run.php humo` | Devolvió 2 en la comprobación de prerrequisitos. No se realizó ninguna solicitud HTTP. |
| `git diff --check` | Salida 0 en los cambios de archivos versionados. |

## Perfiles de Codex

| Perfil | Configurado | Detectado en una sesión | Ejecutado |
|---|---|---|---|
| analista-requisitos | Sí | Pendiente | Pendiente |
| probador | Sí | Pendiente | Pendiente |
| implementador-backend | Sí | Pendiente | Pendiente |
| implementador-frontend | Sí | Pendiente | Pendiente |
| verificador | Sí | Pendiente | Pendiente |

También queda pendiente comprobar la carga del AGENTS.md del repositorio y la detección e invocación de sus cuatro skills en una sesión nueva. Para registrar un perfil como detectado, conserva una referencia observable de su disponibilidad en Codex. Para registrar ejecución, conserva la tarea delegada y el resultado propio del subagente.

## Requisitos pendientes del caso de humo

Proveer una instancia MySQL 8 separada en loopback, crear el esquema descartable y su marcador único, y configurar una cuenta restringida. Importar los fixtures con el importador y provisionar el usuario sintético. Configurar las variables y servir la copia en el puerto 8765 con la configuración de ejemplo. Seguir [las instrucciones de pruebas](../../tests/tarea-6/README.md) y conservar el código de salida y los resultados de las cuatro aserciones.

## Validación posterior a las correcciones de auditoría

| Comprobación ejecutada | Resultado observado |
|---|---|
| Lint con PHP 8.2.12 | 115 archivos y 0 fallos. |
| Análisis de formatos | Cinco TOML con tomllib y comprobación estructural de los metadatos de cuatro skills. |
| Revisión estática de SQL y rutas de escritura | 21 tablas y sus referencias calificadas, cuatro INSERT dirigidos al esquema descartable y verificación de conexión previa a las escrituras. |
| `php tests/tarea-6/run.php humo --check-prerequisites` | Cuatro SKIP, salida 3 por prerrequisitos locales incompletos. No hubo solicitudes HTTP ni conexiones a MySQL. |

El reconocimiento de los permisos efectivos de MySQL, el rechazo de un marcador incorrecto y el desafío HTTP siguen pendientes de prueba con la instancia descartable. Los resultados anteriores de código 2 corresponden a la versión inicial del runner.
