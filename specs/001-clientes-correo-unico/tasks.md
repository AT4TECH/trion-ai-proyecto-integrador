# Tasks: Correos electrónicos únicos en el módulo de clientes

**Input**: Design documents from `specs/001-clientes-correo-unico/`

**Prerequisites**: plan.md, spec.md, data-model.md, research.md, quickstart.md y constitución vigente.

---

## Phase 1: Setup (Shared Infrastructure)

**Purpose**: Preparar un entorno seguro de implementación y pruebas.

- [ ] T001 Configurar una copia descartable del sistema con una base de datos aislada, sin datos reales, utilizando `config/database.php` y `database.sql`. Inspeccionar el esquema antes de importarlo y verificar que la conexión apunta exclusivamente a la base de datos de pruebas. No modificar la base de datos original ni registrar credenciales en Git.
- [ ] T002 Ejecutar `php -l` sobre `models/Cliente.php` y `controllers/ClienteController.php` para establecer una referencia inicial de sintaxis.

---

## Phase 2: Foundational (Blocking Prerequisites)

**Purpose**: Implementar los componentes compartidos necesarios para las historias de usuario.

- [ ] T003 Implementar `Cliente::existeEmail(string $email, int $excluirId = 0): bool` en `models/Cliente.php`, utilizando una consulta PDO preparada con `CAST(LOWER(email) AS BINARY) = CAST(LOWER(:email) AS BINARY)`, exclusión mediante `id_cliente <> :id`, `LIMIT 1` y sin filtrar por estado (DR-01, DR-02, DR-04, FR-001, FR-002).
- [ ] T004 Implementar la protección de concurrencia en `controllers/ClienteController.php` para creación y edición mediante GET_LOCK y RELEASE_LOCK. Utilizar un nombre de bloqueo estable, compartido y específico de la base de datos, de máximo 64 caracteres, con espera de 5 segundos. Adquirir el bloqueo antes de validar y mantenerlo hasta finalizar el guardado, utilizando la misma conexión PDO. Continuar únicamente si GET_LOCK devuelve 1; en caso contrario, rechazar la operación con un mensaje comprensible y sin modificar datos. Liberar únicamente el bloqueo adquirido mediante `finally`, incluso ante errores de validación o persistencia. Evitar transacciones con lecturas anteriores a la adquisición del bloqueo (FR-007, research.md).

**Checkpoint**: Componentes compartidos disponibles para implementar la validación de correos.

---

## Phase 3: User Story 1 - Registrar cliente evitando duplicado de correo (Priority: P1)

**Goal**: Impedir el registro de clientes con correos pertenecientes a otros clientes.

**Independent Test**: Registrar un cliente con correo disponible y rechazar el registro con correo duplicado.

- [ ] T005 [US1] Incorporar una única validación compartida en `controllers/ClienteController.php`, dentro de `validar()`, utilizando `existeEmail()` con el identificador recibido por el método: 0 para creación y el identificador actual para edición. Consultar duplicados únicamente cuando el correo no sea nulo y tenga formato válido. Conservar las validaciones existentes y utilizar el mensaje "El correo electrónico ya pertenece a otro cliente." (DR-05, FR-001, FR-002, FR-003, FR-004, FR-005).
- [ ] T006 [US1] Ejecutar los escenarios V-01 y V-02 de `quickstart.md` en la base de datos aislada, verificando registro exitoso con correo disponible y rechazo de duplicados pertenecientes a clientes activos e inactivos (FR-001, FR-005, SC-001, SC-005).

---

## Phase 4: User Story 2 - Editar conservando el correo propio (Priority: P2)

**Goal**: Permitir conservar el correo propio cuando no pertenece a otro cliente.

**Independent Test**: Editar un cliente sin cambiar su correo y comprobar que se guarda correctamente.

- [ ] T007 [US2] Verificar en `controllers/ClienteController.php` que la edición utiliza la validación compartida de T005 con el identificador del cliente actual y que conserva los contratos de retorno existentes. Corregir la integración si es necesario, sin duplicar la lógica de validación (FR-002).
- [ ] T008 [US2] Ejecutar los escenarios V-03 y V-17 de `quickstart.md` en la base de datos aislada, verificando conservación del correo propio y edición con un correo disponible (FR-002).

---

## Phase 5: User Story 3 - Editar con correo perteneciente a otro cliente (Priority: P3)

**Goal**: Rechazar la asignación de correos pertenecientes a otros clientes.

**Independent Test**: Intentar editar un cliente utilizando el correo de otro y comprobar que sus datos no cambian.

- [ ] T009 [US3] Verificar en `controllers/ClienteController.php` que la validación compartida rechaza correos pertenecientes a otros clientes y conserva el mensaje de error y los contratos de retorno existentes (FR-002, FR-005).
- [ ] T010 [US3] Ejecutar los escenarios V-04 y V-08 de `quickstart.md`, verificando el rechazo de correos pertenecientes a clientes activos e inactivos (FR-002, FR-005, SC-002, SC-005).

---

## Phase 6: User Story 4 - Editar dejando el correo vacío (Priority: P4)

**Goal**: Conservar el carácter opcional del correo electrónico.

**Independent Test**: Editar un cliente dejando el correo vacío y comprobar que se guarda correctamente.

- [ ] T011 [US4] Ejecutar los escenarios V-05 y V-09 de `quickstart.md`, verificando que se permite crear y editar clientes sin correo, incluso cuando otros clientes tienen correo nulo (FR-003, SC-003).
- [ ] T012 [US4] Ejecutar el escenario V-13 de `quickstart.md`, verificando que se puede resolver un duplicado preexistente dejando el correo vacío (FR-006, SC-006).

---

## Phase 7: User Story 5 - Correo inválido en edición (Priority: P5)

**Goal**: Conservar la validación existente del formato del correo.

**Independent Test**: Intentar guardar un correo inválido y comprobar que se conserva el mensaje de formato existente.

- [ ] T013 [US5] Verificar en `controllers/ClienteController.php` que se conserva la validación mediante `filter_var` y que no se consulta duplicidad para correos nulos o inválidos (FR-003, FR-004).
- [ ] T014 [US5] Ejecutar los escenarios V-06 y V-10 de `quickstart.md`, verificando el rechazo de correos inválidos durante creación y edición (FR-004, SC-004).

---

## Phase 8: User Story 6 - Diferencias de mayúsculas y minúsculas (Priority: P6)

**Goal**: Detectar duplicados sin distinguir mayúsculas y minúsculas, sin aplicar equivalencia de tildes.

**Independent Test**: Intentar registrar o editar un cliente con variantes de mayúsculas y minúsculas de un correo existente.

- [ ] T015 [US6] Verificar en `models/Cliente.php` que la comparación implementada en T003 no depende de una intercalación que ignore las tildes (FR-001, FR-002).
- [ ] T016 [US6] Ejecutar los escenarios V-07 y V-18 de `quickstart.md`, comprobando duplicidad por diferencias de mayúsculas y minúsculas en creación y edición, y distinción de tildes mediante una prueba directa de comparación independiente de la validación de formato (FR-001, FR-002, SC-001, SC-002).

---

## Phase 9: User Story 7 - Duplicados preexistentes (Priority: P7)

**Goal**: Gestionar duplicados preexistentes sin modificar automáticamente otros registros.

**Independent Test**: Intentar editar un cliente cuyo correo ya está duplicado y comprobar que puede resolver el conflicto.

- [ ] T017 [US7] Verificar en `controllers/ClienteController.php` que se rechaza el guardado mientras persiste el conflicto y que se permite resolverlo mediante un correo disponible o vacío, sin modificar ni eliminar automáticamente otros registros (FR-006, SC-006).
- [ ] T018 [US7] Ejecutar los escenarios V-11, V-12 y V-13 de `quickstart.md`, verificando rechazo del conflicto, corrección con correo disponible y eliminación voluntaria del correo del cliente editado (FR-006, SC-006). Reutilizar la evidencia de V-13 obtenida en T012 si sigue siendo válida.

---

## Phase 10: Polish & Cross-Cutting Concerns

- [ ] T019 Ejecutar los escenarios V-14, V-15 y V-16 de `quickstart.md` con conexiones independientes a la base de datos aislada. Verificar concurrencia creación-creación, creación-edición y edición-edición, rechazo seguro al superar los 5 segundos de espera y liberación del bloqueo después de operaciones exitosas, errores de validación y errores de persistencia (FR-007, SC-007).
- [ ] T020 Ejecutar el escenario V-19 de `quickstart.md`, verificando conservación de las validaciones existentes, seguridad, protección CSRF, auditoría, contratos de retorno y esquema de base de datos.
- [ ] T021 Ejecutar nuevamente `php -l` sobre `models/Cliente.php`, `controllers/ClienteController.php` y cualquier archivo PHP nuevo o modificado. Registrar en `quickstart.md` los resultados reales de V-01 a V-19, sus evidencias y la trazabilidad con FR-001 a FR-007 y SC-001 a SC-007. Revisar los cambios mediante Git y confirmar que no se incorporan credenciales, datos reales ni archivos temporales.

---

## Dependencies & Execution Order

### Phase Dependencies

- Setup debe completarse antes de Foundational.
- Foundational debe completarse antes de implementar la validación compartida.
- T003 debe completarse antes de T005.
- T004 debe completarse antes de ejecutar pruebas funcionales de creación y edición.
- T005 debe completarse antes de las verificaciones de US1 a US7.
- T007 debe completarse antes de las verificaciones de edición.
- La fase final depende de la implementación y verificación de las historias de usuario.

### User Story Dependencies

- US1 establece la validación compartida.
- US2 verifica su integración en edición.
- US3 a US7 verifican comportamientos adicionales de los componentes compartidos.
- Cada historia dispone de escenarios de aceptación verificables, aunque comparte componentes con otras historias.

### Parallel Opportunities

- T003 y T004 afectan archivos diferentes y pueden desarrollarse en paralelo si se mantiene el contrato acordado.
- Las pruebas independientes pueden ejecutarse en paralelo únicamente cuando sus datos y conexiones están aislados y no interfieren entre sí.
- Las modificaciones sobre un mismo archivo deben coordinarse y ejecutarse secuencialmente.

### MVP Scope

US1 y US2 constituyen el incremento inicial de validación compartida para creación y edición. No se considera terminada la funcionalidad hasta completar todas las historias, las pruebas concurrentes y las verificaciones de regresión.

---

## Implementation Strategy

1. Preparar el entorno aislado y establecer la referencia inicial de sintaxis.
2. Implementar la consulta de duplicidad y la protección de concurrencia.
3. Incorporar la validación compartida y verificar su integración en creación y edición.
4. Ejecutar las verificaciones correspondientes a US1–US7.
5. Ejecutar las pruebas concurrentes, de regresión y sintaxis final.
6. Registrar evidencias reales y revisar los cambios antes de considerar terminada la funcionalidad.

---

## Notes

- Todas las tareas comienzan sin marcar.
- Marcar una tarea como completada únicamente después de ejecutarla y verificar su resultado.
- Los escenarios V-01 a V-19 están definidos en `specs/001-clientes-correo-unico/quickstart.md`.
- No inventar resultados de pruebas ni marcar tareas pendientes como completadas.
- No modificar el esquema de base de datos ni corregir automáticamente duplicados históricos.
- La protección de concurrencia cubre las operaciones que utilizan el mecanismo de bloqueo compartido, no las escrituras externas que lo omiten.
- Mantener la arquitectura MVC, PDO, las validaciones existentes, la seguridad y la auditoría.
- Trabajar exclusivamente en la rama `recuperacion/triana`.
