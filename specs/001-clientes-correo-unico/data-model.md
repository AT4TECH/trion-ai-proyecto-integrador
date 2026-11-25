# Data Model — Correos electrónicos únicos en el módulo de clientes

Derivado de la especificación `specs/001-clientes-correo-unico/spec.md` y del esquema existente (`database.sql`, tabla `clientes`).

## Entidades

### Cliente
- id_cliente: INT UNSIGNED, AUTO_INCREMENT, PRIMARY KEY.
- documento: VARCHAR(30), NULL, UNIQUE.
- nombre: VARCHAR(100), NOT NULL.
- telefono: VARCHAR(30), NULL.
- email: VARCHAR(100), NULL, sin restricción UNIQUE.
- direccion: VARCHAR(200), NULL.
- ciudad: VARCHAR(100), NULL.
- estado: TINYINT(1), NOT NULL, DEFAULT 1.
- fecha_creacion: TIMESTAMP, NOT NULL, DEFAULT CURRENT_TIMESTAMP.
- fecha_actualizacion: TIMESTAMP, NOT NULL, DEFAULT CURRENT_TIMESTAMP, ON UPDATE CURRENT_TIMESTAMP.
- Índices existentes: idx_cliente_nombre (nombre) e idx_cliente_estado (estado).
- Motor de almacenamiento: InnoDB.

## Reglas de unicidad de correos (nuevas)

- DR-01: un correo no debe estar asignado a otro cliente, sin distinguir mayúsculas ni minúsculas y sin equivalencia de tildes.
- DR-02: al editar, puede conservarse el correo propio únicamente si ningún otro cliente tiene asignado ese correo.
- DR-03: no se modifican ni eliminan automáticamente registros con correos duplicados preexistentes.
- DR-04: la validación aplica a clientes activos e inactivos.
- DR-05: el correo es opcional y se permiten múltiples clientes con valor NULL.
- DR-06: los duplicados preexistentes pueden corregirse asignando un correo válido y disponible o dejando el campo vacío.

## Relaciones

La funcionalidad no requiere crear, modificar ni eliminar
relaciones entre tablas del esquema existente.
