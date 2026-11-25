# Quickstart — Correos electrónicos únicos en el módulo de clientes

Validación funcional y concurrente derivada de los requisitos y criterios de aceptación de `specs/001-clientes-correo-unico/spec.md`.

Ejecutar sobre una copia descartable del sistema y una base de datos aislada (sin datos reales).

Los escenarios siguientes corresponden a pruebas planificadas, pendientes de ejecución.

## Prerrequisitos

- Entorno PHP 8.2 + XAMPP (arquitectura MVC existente, `config/database.php`).
- Base de datos aislada con el esquema de `database.sql`.
- Verificar que la configuración apunta exclusivamente a la base de datos de pruebas.
- Conexiones independientes para las pruebas concurrentes.
- Validación de sintaxis PHP de los archivos modificados.

## Escenarios de validación

### V-01 Registro con correo único (FR-001, SC-001)
- Given: el correo ingresado es válido y no pertenece a ningún cliente.
- When: registrar un cliente desde el formulario de creación.
- Expected: el cliente se crea exitosamente.

### V-02 Duplicado al registrar (FR-001, FR-005, SC-001, SC-005)
- Given: existe otro cliente con el mismo correo.
- When: intentar registrar un cliente con ese correo, realizando una prueba con propietario activo y otra con propietario inactivo.
- Expected: se muestra un mensaje comprensible y no se crea el cliente en ninguno de los casos.

### V-03 Edición conservando el correo propio (FR-002, SC-002)
- Given: existe un cliente cuyo correo no pertenece a ningún otro cliente.
- When: editar el cliente sin modificar su correo.
- Expected: el cambio se guarda exitosamente y el correo se conserva.

### V-04 Edición con correo de otro cliente (FR-002, FR-005, SC-002, SC-005)
- Given: existe otro cliente activo con el correo.
- When: editar un cliente estableciendo ese correo.
- Expected: se muestra un mensaje comprensible y el cambio no se guarda.

### V-05 Edición dejando el correo vacío (FR-003, SC-003)
- Given: un cliente con correo asignado y otros clientes sin correo.
- When: editar dejando el campo de correo vacío.
- Expected: el cambio se guarda y el cliente queda sin correo asignado.

### V-06 Correo inválido al editar (FR-004, SC-004)
- Given: un cliente con correo válido.
- When: editar ingresando un correo inválido.
- Expected: se muestra el mensaje de validación de formato existente y el cambio no se guarda.

### V-07 Diferencias de mayúsculas y minúsculas (FR-001, FR-002, SC-001, SC-002)
- Given: existe un cliente con correo "cliente@ejemplo.com".
- When: intentar registrar y editar otro cliente utilizando variantes en mayúsculas y minúsculas, incluyendo "CLIENTE@EJEMPLO.COM".
- Expected: se detecta el duplicado, se muestra un mensaje comprensible y el cambio no se guarda en ninguna operación.

### V-08 Clientes inactivos (FR-002, SC-002)
- Given: existe otro cliente inactivo con el correo.
- When: editar un cliente estableciendo ese correo.
- Expected: se detecta el duplicado y el cambio no se guarda.

### V-09 Registro sin correo (FR-003, SC-003)
- Given: existen otros clientes sin correo.
- When: registrar un cliente sin correo.
- Expected: el cliente se crea sin correo asignado.

### V-10 Validación de formato en creación (FR-004, SC-004)
- Given: formulario de creación.
- When: ingresar un correo inválido y guardar.
- Expected: se muestra el mensaje de validación de formato existente y el cliente no se crea.

### V-11 Conservar un correo duplicado preexistente (FR-006, SC-006)
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

### V-14 Solicitudes concurrentes con el mismo correo (FR-007, SC-007)
- Given: un correo válido y disponible, y dos conexiones independientes a la base de datos aislada.
- When: ejecutar solicitudes simultáneas sobre clientes distintos, probando las combinaciones creación-creación, creación-edición y edición-edición.
- Expected: solamente una operación guarda el correo en cada prueba; la otra se rechaza y no se generan duplicados.

### V-15 Tiempo de espera del bloqueo (FR-007, SC-007)
- Given: una conexión independiente mantiene adquirido el bloqueo compartido.
- When: otra solicitud intenta guardar un cliente y no obtiene el bloqueo dentro del tiempo establecido de 5 segundos.
- Expected: la operación se rechaza con un mensaje comprensible y no modifica los datos.

### V-16 Liberación del bloqueo (FR-007, SC-007)
- Given: una operación adquiere el bloqueo compartido.
- When: finaliza exitosamente, presenta un error de validación o presenta un error de persistencia. Probar cada caso por separado.
- Expected: el bloqueo se libera y otra conexión puede adquirirlo.

### V-17 Edición con correo válido y disponible (FR-002, FR-006, SC-006)
- Given: existe un cliente sin conflictos de correo.
- When: editarlo asignándole un correo válido que no pertenece a otro cliente.
- Expected: el cambio se guarda exitosamente.

### V-18 Comparación de caracteres con y sin tilde (FR-001, FR-002)
- Given: dos valores de correo que difieren únicamente por la presencia de una tilde.
- When: verificar directamente el mecanismo de comparación de correos en la base de datos aislada.
- Expected: los valores se consideran diferentes.
- Nota: esta prueba verifica la comparación de valores y no modifica ni sustituye la validación de formato existente.

### V-19 Conservación del comportamiento existente
- Given: el módulo de clientes con sus validaciones, controles de seguridad y auditoría existentes.
- When: ejecutar operaciones válidas e inválidas de creación y edición.
- Expected: se conservan las validaciones existentes, la protección CSRF, los formatos de retorno y el registro de auditoría de las operaciones exitosas.

## Registro de resultados

Durante la implementación se documentará para cada escenario:

- Identificador de la prueba.
- Datos utilizados.
- Resultado esperado.
- Resultado obtenido.
- Estado: aprobado o fallido.
- Evidencia y observaciones.

No se considerará aprobada ninguna prueba sin haberla ejecutado.
## Registro de resultados reales (T021)

Fecha: 2026-11-25 04:04:54
Entorno: PHP 8.2.12 (cli), MariaDB 10.4.32, base de datos aislada \sistema_ventas_iso\ (importada desde \database.sql\). Verificado: la base original \sistema_ventas\ sin modificaciones (recuento 1 antes y 1 después).

Verificación de sintaxis (T002/T021): \models/Cliente.php\ — "No syntax errors detected"; \controllers/ClienteController.php\ — "No syntax errors detected".

Escenarios ejecutados contra la base aislada (resultados reportados por el ejecutor, con cobertura parcial):

- V-01: PASS — Creación de cliente con correo válido y disponible (crear ok: [2,[]]).
- V-02: PASS — Rechazo de duplicado al registrar (activo).
- V-02b: PASS — Rechazo de duplicado al registrar (propietario inactivo).
- V-03: PASS — Edición conservando el correo propio ({"r":[],"email":"v01@ejemplo.com"}).
- V-04: PASS — Rechazo de correo de otro cliente en edición; V-04b: PASS — sin cambio: "v03b@ejemplo.com".
- V-05: PASS — Edición dejando el correo vacío ({"r":[],"email":null}).
- V-06: PASS — Rechazo de correo inválido en edición.
- V-07: PASS — Duplicado detectado por diferencia de caja.
- V-08: PASS — Rechazo a propietario inactivo.
- V-09: PASS — Registro sin correo (sin correo ok: [4,[]]).
- V-10: PASS — Rechazo de correo inválido en creación.
- V-11: PASS — Rechazo conservar duplicado preexistente.
- V-12: PASS — Corrección a correo disponible ([]).
- V-13: PASS — Dejar vacío un correo con duplicado preexistente ([]).
- V-17: PASS — Edición con correo válido y disponible ([]).
- V-18: PASS — Correo con tilde se considera distinto.
- V-18-b: PASS — Diferencia de caja detectada como duplicado.
- V-14-setup: PASS — Adquisición del bloqueo por la conexión A (creación/creación).
- V-15: PASS — Rechazo al intentar crear mientras A tiene el bloqueo.
- V-14b: PASS — No se creó mientras el bloqueo estaba adquirido.
- V-16-a: PASS — Liberación manual por la conexión A.
- V-14-d: PASS — Creación exitosa tras liberar el bloqueo (creación/creación).
- V-14-setup2: PASS — Adquisición del bloqueo (creación/edición).
- V-14-c: PASS — Rechazo al intentar editar mientras A tiene el bloqueo.
- V-14-e: PASS — Edición exitosa tras liberar el bloqueo (creación/edición).
- V-14-setup3: PASS — Adquisición del bloqueo (edición/edición).
- V-14-f: PASS — Rechazo al intentar editar (edición/edición).
- V-14-g: PASS — Edición exitosa tras liberar el bloqueo (edición/edición).
- V-16b-pre: PASS — Error de validación esperado ([0,["El nombre es obligatorio..."]]).
- V-16-c: PASS — Liberación tras error de validación.
- V-16-d: PASS — Excepción de persistencia simulada ("Simulado error de persistencia").
- V-16-e: PASS — Liberación tras error de persistencia.
- V-19: PASS — Auditoría conservada (before 4, after 5).
- ISO_ISOLATION: PASS — Base original sin modificaciones (before=1 after=1).

RESUMEN: PASS=35 FAIL=0 TOTAL=35

## Revisión humana de los resultados

- El ejecutor reportó 35 comprobaciones aprobadas y ninguna fallida.
- Se detectó y corrigió manualmente el BOM UTF-8 introducido durante la implementación.
- Los tres archivos PHP nuevos o modificados superaron la validación de sintaxis mediante php -l.
- V-14: se comprobó la contención del bloqueo, pero quedan pendientes las operaciones simultáneas reales.
- V-16: se comprobó la liberación ante una excepción simulada de persistencia. Queda pendiente verificarla desde una conexión independiente.
- V-19: se comprobó el registro de auditoría en creación. Quedan pendientes las verificaciones integrales de seguridad y regresión.
- Las tareas T019, T020 y T021 permanecen abiertas hasta completar sus verificaciones.
- El recuento de clientes de la base original permaneció igual antes y después de las pruebas. Esta comprobación no demuestra por sí sola la integridad de todos sus datos.

Los resultados parciales no constituyen una aprobación integral de todos los escenarios de aceptación.
