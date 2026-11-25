# Quickstart — Correos electrónicos únicos en el módulo de clientes

Validación funcional derivada de los criterios de aceptación de `specs/001-clientes-correo-unico/spec.md`.
Ejecutar sobre una copia descartable del sistema y una base de datos aislada (sin datos reales).

## Prerrequisitos
- Entorno PHP 8.2 + XAMPP (arquitectura MVC existente, `config/database.php`).
- Base de datos aislada con el esquema de `database.sql`.

## Escenarios de validación

### V-01 Registro con correo único (FR-001)
- Given: el correo ingresado es válido y no pertenece a ningún cliente.
- When: registrar un cliente desde el formulario de creación del módulo de clientes.
- Expected: el cliente se crea exitosamente.

### V-02 Duplicado al registrar (SC-001)
- Given: existe otro cliente con el mismo correo, independientemente de su estado activo o inactivo.
- When: intentar registrar un cliente con ese correo.
- Expected: se muestra un mensaje comprensible y no se crea el cliente.

### V-03 Edición conservando el correo propio (SC-002)
- Given: existe un cliente cuyo correo no pertenece a ningún otro cliente.
- When: editar el cliente sin modificar su correo.
- Expected: el cambio se guarda exitosamente y el correo se conserva.

### V-04 Edición con correo de otro cliente (SC-002)
- Given: existe otro cliente con el correo
- When: editar un cliente estableciendo ese correo
- Expected: se muestra un mensaje comprensible y el cambio no se guarda.

### V-05 Edición dejando el correo vacío (SC-002, SC-003)
- Given: cliente con correo asignado
- When: editar dejando el campo de correo vacío
- Expected: el cambio se guarda y el cliente queda sin correo asignado.

### V-06 Correo inválido al editar (SC-002)
- Given: cliente con correo válido
- When: editar ingresando un correo inválido
- Expected: se muestra el mensaje de validación de formato existente y el cambio no se guarda.

### V-07 Diferencias de mayúsculas y minúsculas (FR-001, FR-002, SC-001, SC-002)
- Given: existe un cliente con correo "cliente@ejemplo.com".
- When: intentar registrar o editar otro cliente con "CLIENTE@ejemplo.com" o "Cliente@ejemplo.com".
- Expected: se detecta el duplicado, se muestra un mensaje comprensible y el cambio no se guarda. La comparación ignora mayúsculas y minúsculas, pero no establece equivalencia entre caracteres con y sin tilde.

### V-08 Clientes inactivos (FR-002, SC-002)
- Given: existe otro cliente inactivo con el correo
- When: editar un cliente estableciendo ese correo
- Expected: se detecta duplicado y el cambio no se guarda.

### V-09 Registro sin correo (SC-003)
- Given: existen otros clientes sin correo
- When: registrar un cliente sin correo
- Expected: el cliente se crea sin correo asignado.

### V-10 Validación de formato en creación (FR-004)
- Given: formulario de creación
- When: ingresar un correo inválido y guardar
- Expected: se muestra el mensaje de validación de formato existente y el cliente no se crea.

### V-11 Conservar un correo duplicado preexistente (FR-006)
- Given: existen dos clientes con el mismo correo.
- When: editar uno de ellos conservando el correo duplicado.
- Expected: se muestra un mensaje comprensible y el cambio no se guarda.

### V-12 Corregir un correo duplicado preexistente (FR-006, SC-006)
- Given: existen dos clientes con el mismo correo.
- When: editar uno de ellos asignándole un correo válido y disponible.
- Expected: el cambio se guarda sin modificar los demás clientes.

### V-13 Eliminar el correo de un cliente con duplicado preexistente (FR-006, SC-006)
- Given: existen dos clientes con el mismo correo.
- When: editar uno de ellos dejando el campo de correo vacío.
- Expected: el cambio se guarda sin modificar los demás clientes.

### V-14 Solicitudes concurrentes con el mismo correo
- Given: un correo válido y disponible, y dos conexiones independientes a la base de datos aislada.
- When: ejecutar simultáneamente dos operaciones de creación o edición de clientes distintos con el mismo correo.
- Expected: solamente una operación guarda el correo; la otra se rechaza y no genera duplicados.

### V-15 Tiempo de espera del bloqueo
- Given: una conexión independiente mantiene adquirido el bloqueo compartido.
- When: otra solicitud intenta guardar un cliente y supera el tiempo de espera establecido.
- Expected: la operación se rechaza con un mensaje comprensible y no modifica los datos.

### V-16 Liberación del bloqueo ante errores
- Given: una operación adquiere el bloqueo y posteriormente presenta un error de validación o guardado.
- When: finaliza la operación.
- Expected: el bloqueo se libera y otra conexión puede adquirirlo.