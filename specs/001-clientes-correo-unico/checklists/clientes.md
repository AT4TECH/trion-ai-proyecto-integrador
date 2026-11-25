# Checklist: Calidad de requisitos — Módulo de clientes

**Purpose**: Validar la calidad de los requisitos escritos para la funcionalidad de correos electrónicos únicos en el módulo de clientes

**Created**: 2026-11-25

**Feature**: specs/001-clientes-correo-unico/spec.md

**Ownership**: Este checklist es un artefacto de revisión propiedad del revisor; `[x]` significa que el criterio de calidad de los requisitos está satisfecho, no que el trabajo de implementación esté completo.

## Completitud de los requisitos

- [x] CHK001 - Are functional requirements for duplicate email prevention present for both register and edit? [Completeness, Spec §FR-001, Spec §FR-002]
- [x] CHK002 - Is the requirement that the email field remains optional (creation and editing) documented? [Completeness, Spec §FR-003]
- [x] CHK003 - Is the requirement to preserve the existing email format validation documented? [Completeness, Spec §FR-004]
- [x] CHK004 - Is the requirement to display a comprehensible message and avoid saving on duplicate documented? [Completeness, Spec §FR-005]
- [x] CHK005 - Are requirements for pre-existing duplicates and conflict handling (correct to available or empty email, without modifying existing records) defined? [Completeness, Spec §FR-006]
- [x] CHK006 - Are requirements for keeping the client own email during edit and rejecting another client email defined? [Completeness, Spec §FR-002]

## Claridad y especificidad

- [x] CHK007 - Is the email comparison requirement quantified as case-insensitive, explicitly excluding tilde and accent equivalence? [Clarity, Spec §FR-001]
- [x] CHK008 - Are ownership boundaries (another client) and exclusion of the editing client own id explicitly defined? [Clarity, Spec §FR-002]
- [x] CHK009 - Is the block behavior for both active and inactive owners explicitly stated? [Clarity, Spec §FR-002]
- [x] CHK010 - Are boundary conditions defined (empty email on edit, invalid email input)? [Clarity, Spec §Edge Cases]

## Consistencia

- [x] CHK011 - Are register and edit requirements consistent, with no requirement allowing saving another client email? [Consistency, Spec §FR-001]
- [x] CHK012 - Are requirements aligned with the documented behaviors in User Stories 1-7? [Consistency, Spec §User Stories]

## Cobertura de escenarios

- [x] CHK013 - Are acceptance scenarios defined for creation with an available or duplicate email, editing with an available email, keeping one's own email, rejecting another client's email, optional empty emails, invalid formats, case-insensitive comparison, accent distinctions, and active or inactive email owners? [Coverage, spec.md, quickstart.md]
- [x] CHK014 - Are requirements and acceptance scenarios defined for pre-existing duplicates, concurrent creation and editing, lock acquisition timeout, rejection when the lock cannot be acquired, and lock release after validation or persistence errors? [Coverage, spec.md, plan.md, research.md, quickstart.md]

## Calidad de criterios de aceptación

- [x] CHK015 - Are success criteria measurable and technology-agnostic (no implementation details)? [Measurability, Spec §Success Criteria]
- [x] CHK016 - Are SC-001 to SC-007 defined for all documented functional requirements, including concurrency protection? [Measurability, Spec §Success Criteria]

## Trazabilidad

- [x] CHK017 - Are all functional requirements (FR-001 to FR-007) traceable to user stories or edge cases, acceptance scenarios, success criteria, and planned verification scenarios? Is concurrency protection consistently documented across the specification and planning artifacts? [Traceability, spec.md, plan.md, research.md, quickstart.md]

## Conservación del entorno existente

- [x] CHK018 - Are requirements and technical decisions defined to preserve the existing PHP 8.2, MySQL/MariaDB, MVC and PDO architecture, existing validations, security controls, audit logging, controller return contracts and database schema? [Completeness, spec.md, plan.md, research.md, constitution.md]

## Notas

- Mark items `[x]` only after a reviewer confirms the requirements-quality criterion is satisfied.
- `checklists/requirements.md` remains the built-in lifecycle checklist; this file is the domain-specific requirements-quality checklist.
- Items are numbered sequentially (CHK001..CHK018).
