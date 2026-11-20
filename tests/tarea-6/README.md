# Verificación del arnés

Desde la raíz, ejecuta `powershell -NoProfile -File tools/quality/lint-php.ps1` o llama al script desde PowerShell. Usa PHP 8.1 o posterior con cURL, PDO MySQL y mbstring para las pruebas HTTP.

Si la política local bloquea el archivo y tienes autorización para ejecutarlo, usa `powershell -NoProfile -ExecutionPolicy Bypass -File tools/quality/lint-php.ps1`. La opción aplica al proceso de lint.

## Preparar una instancia descartable

1. Inicia una instancia independiente de MySQL 8 en 127.0.0.1, en un puerto propio como 3307, con un directorio de datos nuevo o un contenedor descartable. Mantén `partial_revokes=OFF` para que los escapes del alcance sean comprobables. Desde la cuenta administradora de esa instancia nueva, crea **sistema_ventas_tarea6_test** con juego de caracteres utf8mb4.
2. En esa instancia nueva, crea `sistema_ventas_tarea6_test.arnes_instancia` con columnas `id TINYINT PRIMARY KEY` y `marker CHAR(64) NOT NULL`. Inserta la fila de ID 1 con un marcador aleatorio de 32 bytes codificado en hexadecimal. Pásalo de forma privada como `SV_TEST_DB_MARKER` al servidor y al runner. No reutilices el marcador de otra instancia.
3. Usa una cuenta nueva cuyos únicos privilegios efectivos sean `SELECT, INSERT, UPDATE, CREATE, REFERENCES` sobre el patrón de esquema `` `sistema\_ventas\_tarea6\_test`.* `` (los guiones bajos escapados son literales). Usa un nombre de cuenta local sin roles ni GRANT OPTION. El arnés aborta ante un formato de privilegios que no pueda reconocer o un permiso fuera del alcance permitido.
4. Define localmente `SV_TEST_ISOLATED=1`, `SV_TEST_DB_PORT`, `SV_TEST_DB_USER`, `SV_TEST_DB_PASSWORD`, `SV_TEST_DB_MARKER` y `SV_TEST_LOGIN_PASSWORD`. Introduce los valores privados sin imprimirlos ni guardarlos en el historial. Usa una contraseña sintética de al menos 12 caracteres para el login. Desde la raíz ejecuta `php tests/tarea-6/fixtures/importar.php` y, si termina con salida 0, `php tests/tarea-6/fixtures/crear-usuario.php`. El importador exige que la única tabla inicial sea la del marcador y se detiene en el primer error. El DDL puede quedar aplicado parcialmente; si falla, prepara una instancia nueva. El segundo comando guarda el hash del usuario sintético.
5. En una ubicación nueva fuera del repositorio, crea una carpeta `sistema_ventas`. Copia allí `assets/css/`, `assets/js/`, `controllers/`, `models/`, `views/`, `public/`, `helpers/` y los archivos `config/config.php` y `config/constantes.php`, manteniendo sus rutas. Copia **`tests/tarea-6/soporte/database.example.php`** como `config/database.php` solamente en esa copia. Usa el código del candidato que deseas probar.
6. Desde el mismo entorno de variables, inicia `php -d display_errors=0 -S 127.0.0.1:8765 -t <directorio-padre-de-sistema_ventas>`. Conserva ese servidor bajo tu control durante toda la ejecución. El puerto es fijo para este runner.
7. Desde el repositorio ejecuta `php tests/tarea-6/run.php humo` con `SV_TEST_ISOLATED=1`, el mismo marcador y la contraseña sintética. Cada respuesta debe acreditar un desafío nuevo con un HMAC calculado tras verificar esquema, permisos y marcador. El GET inicial debe cumplir esa verificación antes de enviar el login. La configuración de la copia vuelve a verificar el aislamiento al procesar el POST.

## Resultados

- **HUMO-01** · El dashboard redirige al visitante al login.
- **HUMO-02** · El vendedor inicia sesión y recibe la redirección al dashboard.
- **HUMO-03** · El dashboard muestra el nombre sintético de empresa y cero ventas del día.
- **HUMO-04** · El vendedor autenticado recibe HTTP 403 al abrir auditoría.

El runner devuelve 0 si todas las aserciones pasan, 1 ante un fallo, 2 ante uso incorrecto y 3 si faltan prerrequisitos locales. Reporta `PASS`, `FAIL` o `SKIP` por aserción; tras un fallo, las restantes se omiten. Conserva solo resultados sanitizados. Detén el servidor local al terminar.

`php tests/tarea-6/run.php humo --check-prerequisites` revisa solo la extensión y las variables locales. Omite todas las aserciones y devuelve 0 si están presentes o 3 si faltan. No realiza HTTP ni comprueba MySQL.

## Cierre de sesión

Para `logout-post-csrf`, habilita también DOM y arranca la copia descartable con su configuración de pruebas precargada para atestar incluso las respuestas tempranas de `logout.php`. Usa rutas absolutas a la copia en ambos argumentos.

```powershell
php -d display_errors=0 -d "auto_prepend_file=<directorio-padre>/sistema_ventas/config/database.php" -S 127.0.0.1:8765 -t "<directorio-padre>"
php tests/tarea-6/run.php logout-post-csrf
```

El caso comprueba método, CSRF, sesión, cookie, auditoría y acceso posterior. Lee los conteos únicamente después de verificar la conexión descartable y usa el usuario sintético del humo. Evita ejecutar otras suites o acciones sobre el mismo usuario al medir el delta. El caso LOGOUT-04-F solicita un fallo controlado de acceso a auditoría en la copia después de atestar. Confirma el mensaje técnico en el log del servidor de pruebas y conserva únicamente el resultado sanitizado. Ejecuta también el caso `humo` contra la misma copia.

Para ampliar la suite agrega un archivo `casos/<id>.php` que devuelva un mapa ordenado de identificadores y funciones, como `casos/humo.php`. El runner identifica cada criterio en la salida sin volcar respuestas, cookies ni parámetros de acceso.
