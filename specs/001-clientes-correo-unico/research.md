# Research — Correos electrónicos únicos en el módulo de clientes

Investigación derivada de los requisitos y criterios de aceptación de `specs/001-clientes-correo-unico/spec.md`.

## 1. Comportamiento actual (inspección realizada)

- `models/Cliente.php`:
  - `Cliente::crear()` ejecuta un INSERT preparado sin comprobación previa de duplicación de correo.
  - `Cliente::actualizar()` ejecuta un UPDATE preparado sin comprobación de duplicación de correo.
  - `Cliente::existeDocumento()` es el patrón observable de consulta preparada con parámetros vinculados y exclusión del identificador (`id_cliente<>:id`).
- `controllers/ClienteController.php::validar()`:
  - Valida formato de correo con `filter_var($d['email'], FILTER_VALIDATE_EMAIL)`; el correo es opcional (`null` cuando está vacío).
  - Valida documento con `existeDocumento()` (excluye el id actual).
- `database.sql` (esquema):
  - Tabla `clientes`: `email VARCHAR(100) NULL`, sin restricción UNIQUE ni índice específico sobre email.
  - La tabla conserva sus índices existentes sobre nombre y estado.
- Se conservarán el flujo de formularios POST, la protección CSRF y la validación del controlador antes de guardar.

## 2. Decisiones técnicas

| Decisión | Fundamento |
|---|---|
| Nuevo método `Cliente::existeEmail(string $email, int $excluirId = 0): bool` | Mantiene el patrón de `existeDocumento()` mediante consultas preparadas y parámetros vinculados. |
| Comparación `CAST(LOWER(c.email) AS BINARY) = CAST(LOWER(:email) AS BINARY)` | Convierte ambos correos a minúsculas y realiza una comparación binaria para evitar la equivalencia entre caracteres con y sin tilde. Su comportamiento deberá verificarse en el motor utilizado. |
| Exclusión del propio cliente: `id_cliente <> :id` | Permite conservar el correo propio cuando no pertenece a otro cliente. |
| Sin filtro de estado en la consulta | La prevención aplica a clientes activos e inactivos. |
| Validación en `ClienteController::validar()` | Centraliza la validación del lado del servidor y conserva la validación de formato existente. |
| Mensaje: "El correo electrónico ya pertenece a otro cliente." | Mensaje comprensible que se agrega al conjunto de errores de validación. |
| No se añade restricción UNIQUE ni tablas nuevas | Conserva el esquema y los registros preexistentes. El riesgo de concurrencia se aborda mediante el mecanismo descrito en el apartado 4. |
| Conservación de contratos existentes | Se mantienen los formatos de retorno, las validaciones y el registro de auditoría del controlador. |

## 3. Pruebas funcionales y concurrentes en entorno aislado

Se definen 19 escenarios de validación en `quickstart.md`, que deberán ejecutarse sobre una copia descartable del sistema y una base de datos aislada sin datos reales.

Los escenarios incluyen pruebas funcionales, comparación de correos, conservación del comportamiento existente y pruebas concurrentes con conexiones independientes.

Los resultados deberán documentarse después de ejecutar las pruebas.

## 4. Condiciones de concurrencia

La validación previa al INSERT o UPDATE presenta un riesgo de duplicación ante solicitudes simultáneas.

Se utilizará un bloqueo de aplicación compartido para las operaciones de creación y edición de clientes mediante GET_LOCK() y RELEASE_LOCK(), utilizando la misma conexión PDO.

El mecanismo deberá cumplir las siguientes condiciones:

- Utilizar un nombre de bloqueo estable y compartido entre creación y edición, específico para la base de datos utilizada y con una longitud máxima de 64 caracteres.
- Adquirir el bloqueo antes de realizar la validación y mantenerlo hasta finalizar el guardado.
- Establecer un tiempo máximo de espera de 5 segundos para adquirir el bloqueo.
- Considerar adquirido el bloqueo únicamente cuando GET_LOCK() devuelva 1.
- Rechazar la operación con un mensaje comprensible y sin modificar los datos cuando no se pueda adquirir el bloqueo.
- Liberar únicamente los bloqueos adquiridos por la operación mediante finally, incluso cuando ocurran errores.
- Evitar iniciar una transacción con una lectura previa que pueda impedir observar los cambios confirmados por otra operación.

Se comprobó la disponibilidad de GET_LOCK() y RELEASE_LOCK() en el servidor MariaDB 10.4.32 mediante una prueba directa, obteniendo el valor 1 tanto en la adquisición como en la liberación del bloqueo.

Esta comprobación confirma la disponibilidad de las funciones, pero no demuestra todavía la eficacia de la implementación.

La protección deberá verificarse mediante pruebas concurrentes con conexiones independientes, incluyendo creación-creación, creación-edición y edición-edición, tiempo de espera y liberación del bloqueo.

No se garantiza la unicidad frente a escrituras externas que no utilicen este mecanismo de bloqueo.
