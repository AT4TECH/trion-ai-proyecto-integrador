# Feature Specification: Correos electrónicos únicos en el módulo de clientes

**Feature Branch**: `recuperacion/triana`

**Created**: 2026-11-24

**Status**: Draft

**Input**: User description: "Crear una funcionalidad para prevenir correos electrónicos duplicados en el módulo de clientes de SistemaVentas, tanto al registrar como al editar clientes. El sistema debe impedir utilizar un correo que pertenezca a otro cliente, permitir conservar el correo propio durante la edición, mantener el correo como campo opcional y conservar la validación de formato existente. Cuando exista un duplicado, debe mostrar un mensaje comprensible y evitar guardar el cambio. Inspecciona el comportamiento actual del módulo y el esquema de datos para fundamentar los requisitos. Identifica las ambigüedades sobre mayúsculas, minúsculas, clientes inactivos y duplicados preexistentes, sin tomar decisiones de negocio no autorizadas. Utiliza la constitución vigente y las plantillas y scripts oficiales de Spec Kit. Genera la especificación en un directorio independiente bajo specs/, con nombre corto clientes-correo-unico."

## User Scenarios & Testing *(mandatory)*

### User Story 1 - Registrar cliente evitando duplicado de correo (Priority: P1)

El usuario intenta crear un cliente cuando ya existe otro cliente con el mismo correo (comparando sin distinguir mayúsculas y minúsculas).

**Why this priority**: Detectar el correo duplicado al momento de registro evita duplicados desde el origen.

**Independent Test**: Crear un cliente usando un correo ya asignado a otro cliente; el sistema debe rechazar el guardado y mostrar un mensaje comprensible.

**Acceptance Scenarios**:

1. **Given** que existe un cliente con correo "cliente@ejemplo.com", **When** se intenta registrar un nuevo cliente con "CLIENTE@ejemplo.com", **Then** se muestra un mensaje comprensible y no se crea el nuevo cliente.

### User Story 2 - Editar conservando el correo propio (Priority: P2)

El usuario edita un cliente y conserva su correo actual.

**Why this priority**: La edición común debe poder mantener el correo propio del cliente.

**Independent Test**: Editar un cliente cuyo correo no pertenece a otro cliente; el cambio se guarda y el correo se conserva.

**Acceptance Scenarios**:

1. **Given** un cliente que tiene un correo asignado, **When** se edita conservando ese correo, **Then** el cambio se guarda exitosamente y el correo se conserva.
2. **Given** un cliente que tiene un correo asignado, **When** se edita sin modificar el campo de correo, **Then** el cambio se guarda exitosamente y el correo se conserva.

### User Story 3 - Editar con correo perteneciente a otro cliente (Priority: P3)

El usuario intenta editar un cliente para que su correo pertenezca a otro cliente.

**Why this priority**: Un correo perteneciente a otro cliente no puede usarse.

**Independent Test**: Editar un cliente usando un correo que ya pertenece a otro cliente; el cambio no se guarda y se muestra un mensaje comprensible.

**Acceptance Scenarios**:

1. **Given** que existe otro cliente con correo "a@ejemplo.com", **When** se edita un cliente asignándole ese correo, **Then** se muestra un mensaje comprensible y el cambio no se guarda (el correo del cliente se conserva).
2. **Given** que existe otro cliente inactivo con correo "b@ejemplo.com", **When** se edita un cliente asignándole ese correo, **Then** se muestra un mensaje comprensible y el cambio no se guarda.

### User Story 4 - Editar dejando el correo vacío (Priority: P4)

El usuario edita un cliente y deja el campo de correo vacío.

**Why this priority**: El correo es opcional, así que dejarlo vacío debe estar permitido.

**Independent Test**: Editar un cliente dejando el correo vacío; el cambio se guarda y el cliente queda sin correo asignado.

**Acceptance Scenarios**:

1. **Given** un cliente con correo asignado, **When** se edita dejando el campo de correo vacío, **Then** el cambio se guarda exitosamente y el cliente queda sin correo asignado.

### User Story 5 - Correo inválido en edición (Priority: P5)

El usuario ingresa un correo inválido al editar.

**Why this priority**: La validación de formato existente debe aplicarse también al editar.

**Independent Test**: Editar un cliente ingresando un correo inválido; el cambio no se guarda y se muestra el mensaje de validación de formato existente.

**Acceptance Scenarios**:

1. **Given** un cliente con correo válido, **When** se edita ingresando un correo inválido, **Then** se muestra el mensaje de validación de formato existente y el cambio no se guarda.

### User Story 6 - Diferencias de mayúsculas y minúsculas (Priority: P6)

El usuario ingresa un correo que difiere de uno ya asignado a otro cliente solo en mayúsculas o minúsculas.

**Why this priority**: La comparación se hace sin distinguir mayúsculas y minúsculas.

**Independent Test**: Ingresar un correo que difiere en tilde o cambio de caja de uno ya asignado a otro cliente; el sistema lo trata como duplicado.

**Acceptance Scenarios**:

1. **Given** que existe un cliente con correo "cliente@ejemplo.com", **When** se intenta registrar un nuevo cliente con "Cliente@ejemplo.com" o "CLIENTE@EJEMPLO.COM", **Then** se trata como duplicado: se muestra un mensaje comprensible y el guardado no se realiza.

### User Story 7 - Duplicados preexistentes en la BD (Priority: P7)

El sistema detecta duplicados ya existentes en la base de datos durante una edición.

**Why this priority**: Durante la edición el usuario puede corregir el correo a uno disponible o dejarlo vacío sin que el sistema modifique ni elimine registros preexistentes.

**Independent Test**: Editar un cliente en una base de datos que ya contiene correos duplicados; el sistema permite corregir el correo a uno disponible o dejarlo vacío sin modificar ni eliminar registros.

**Acceptance Scenarios**:

1. **Given** que existen clientes con correos duplicados en la BD, **When** se edita uno de ellos, **Then** el usuario puede ingresar un correo disponible o dejarlo vacío, y el sistema no modifica ni elimina automáticamente registros con correos preexistentes.

### Edge Cases

- ¿Qué ocurre si el correo está vacío durante la edición de un cliente? (Se guarda sin correo asignado; el sistema no lo considera duplicado.)
- ¿Qué ocurre si el cliente que se edita es el propietario del correo? (El correo se conserva y el cambio se guarda.)
- ¿Qué ocurre si ya existen clientes con correos duplicados en la base de datos? (El sistema no los modifica ni elimina; durante la edición se permite al usuario corregir el correo a uno disponible o dejarlo vacío.)
- ¿Qué ocurre si el correo ingresado es inválido durante el registro o la edición? (El mensaje de validación de formato existente se muestra y el cambio no se guarda.)
- ¿Qué ocurre si las diferencias de mayúsculas y minúsculas hacen que dos correos sean iguales? (El sistema los trata como duplicados, comparándolos sin distinguir tilde y cambio de caja.)

## Requirements *(mandatory)*

### Functional Requirements

- **FR-001**: System MUST prevent registering a new client whose email already belongs to another client, comparing emails case-insensitively (ignoring uppercase and lowercase differences).
- **FR-002**: System MUST prevent using an email that belongs to another client when editing a client, while allowing the client to keep its own email.
- **FR-003**: System MUST allow leaving the email field empty when creating and editing clients (optional field).
- **FR-004**: System MUST preserve the existing email format validation.
- **FR-005**: System MUST display a comprehensible message and avoid saving the change when a duplicate is detected.
- **FR-006**: System MUST allow, when editing, correcting the email to one not used by another client or leaving it empty, without automatically modifying or deleting preexisting records with duplicate emails.

### Key Entities *(include if feature involves data)*

- **Cliente**: entidad de registro de clientes; incluye datos como id, documento, nombre, teléfono, correo electrónico, dirección, ciudad y estado (activo/inactivo).

## Success Criteria *(mandatory)*

### Measurable Outcomes

- **SC-001**: El registro de un cliente se rechaza con un mensaje comprensible cuando su correo ya pertenece a otro cliente (comparación sin distinguir mayúsculas/minúsculas); no se crea el cliente.
- **SC-002**: Al editar un cliente, si el correo ingresado pertenece a otro cliente, el cambio no se guarda y se muestra un mensaje comprensible; si el cliente conserva su correo propio, el cambio se guarda exitosamente.
- **SC-003**: El campo de correo permite quedar vacío tanto en la creación como en la edición de clientes.
- **SC-004**: La validación de formato existente sigue aplicándose a los correos ingresados (correo inválido: el cambio no se guarda).
- **SC-005**: Al detectarse un duplicado, se muestra un mensaje comprensible y el cambio no se guarda.
- **SC-006**: Al editar, el usuario puede ingresar un correo disponible (no perteneciente a otro cliente) o dejar el campo vacío; el sistema no modifica ni elimina automáticamente registros con correos duplicados preexistentes.

## Assumptions

- El módulo de clientes es el módulo de gestión de clientes actual del sistema.
- El correo electrónico es un campo opcional en el esquema de datos actual.
- La comparación de correos ignora tilde y cambio de caja, según la decisión de comparación sin distinguir mayúsculas y minúsculas.

