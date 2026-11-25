# Research — Correos electrónicos únicos en el módulo de clientes

Investigación derivada de los criterios de aceptación de `specs/001-clientes-correo-unico/spec.md`.

## 1. Comportamiento actual (inspección realizada)

- `models/Cliente.php`:
  - `Cliente::crear()` ejecuta un INSERT preparado sin comprobación previa de duplicación.
  - `Cliente::actualizar()` ejecuta un UPDATE preparado sin comprobación de duplicación.
  - `Cliente::existeDocumento()` es el patrón observable de consulta preparada con parámetros vinculados y exclusión del identificador (`id_cliente<>:id`).
- `controllers/ClienteController.php::validar()`:
  - Valida formato de correo con `filter_var($d['email'], FILTER_VALIDATE_EMAIL)`; el correo es opcional (`null` cuando está vacío).
  - Valida documento con `existeDocumento()` (excluye el id actual).
- `database.sql` (esquema):
  - Tabla `clientes`: `email VARCHAR(100) NULL`; sin restricción UNIQUE sobre email ni índices adicionales.
  - Collation de la base de datos: `utf8mb4_unicode_ci`.
- Vistas `views/clientes/crear.php`, `editar.php` y `_form.php`: flujo actual de POST con `verificarCsrf()` y validación del controlador antes de guardar.

## 2. Decisiones técnicas

| Decisión | Fundamento |
|---|---|
| Nuevo método `Cliente::existeEmail(string $email, int $excluirId = 0): bool` | Mantiene el patrón de `existeDocumento()` mediante consultas preparadas y parámetros vinculados. |
| Comparación `CAST(LOWER(c.email) AS BINARY) = CAST(LOWER(:email) AS BINARY)` | Convierte ambos correos a minúsculas y realiza una comparación binaria para evitar la equivalencia entre caracteres con y sin tilde. Su comportamiento deberá verificarse en el motor de base de datos utilizado. |
| Exclusión del propio cliente: `id_cliente <> :id` | Permite conservar el correo propio cuando no pertenece a otro cliente. |
| Sin filtro de estado en la consulta | La prevención aplica a clientes activos e inactivos. |
| Validación en `ClienteController::validar()` | Centraliza la validación del lado del servidor y conserva la validación de formato existente. |
| Mensaje: "El correo electrónico ya pertenece a otro cliente." | Mensaje comprensible que se agrega al conjunto de errores de validación. |
| No se añade restricción UNIQUE ni tablas nuevas | Propuesta de cambios mínimos que requiere evaluar el riesgo de concurrencia descrito en el apartado 4. |

## 3. Pruebas funcionales en entorno aislado

Se definen escenarios de validación en `quickstart.md` que ejecutan sobre una copia descartable del sistema y una base de datos aislada sin datos reales.


## 4. Condiciones de concurrencia

La validación previa al INSERT o UPDATE presenta un riesgo
de duplicación ante solicitudes simultáneas.

Se utilizará un bloqueo de aplicación compartido para las
operaciones de creación y edición de clientes mediante
GET_LOCK() y RELEASE_LOCK(), utilizando la misma conexión PDO.

El bloqueo deberá adquirirse antes de validar y mantenerse
hasta finalizar el guardado. Si no se obtiene el bloqueo,
la operación deberá rechazarse con un mensaje comprensible.

La liberación se realizará mediante finally, incluso cuando
ocurran errores.

Se comprobó la disponibilidad de ambas funciones en el
servidor MariaDB 10.4.32, obteniendo el valor 1 tanto en
la adquisición como en la liberación del bloqueo.

La eficacia de esta estrategia deberá verificarse mediante
pruebas concurrentes con conexiones independientes.

No se garantiza la unicidad frente a escrituras externas
que no utilicen este mecanismo de bloqueo.
