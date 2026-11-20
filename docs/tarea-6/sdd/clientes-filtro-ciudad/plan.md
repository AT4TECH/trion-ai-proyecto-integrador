# Plan — Filtrar clientes por ciudad

## Rutas, capas y archivos

- `views/clientes/index.php` recibe y normaliza Ciudad y página, muestra el control escapado y genera enlaces GET. Preservar `estado=` cuando represente Todos. El formulario omite `pagina` para comenzar una búsqueda nueva.
- `controllers/ClienteController.php` coordina conteo y página del listado. Propuesta de 50 filas por página y acceso a todas las coincidencias de la vista, reemplazando su límite fijo de 500. Conservar el contrato de métodos existentes que puedan tener otros consumidores.
- `models/Cliente.php` comparte las condiciones preparadas entre el conteo de clientes y la consulta paginada. Contar sin multiplicar clientes por ventas. Mantener agregados y orden actual por estado, cliente general y nombre, con `id_cliente` como desempate para páginas estables.
- `views/clientes/_form.php`, `tests/tarea-6/fixtures/esquema.sql` y `helpers/permisos.php` son referencias del campo y la autorización.

## Datos, permisos y riesgos

Usar la columna existente `clientes.ciudad` y enlazar el parámetro como valor de consulta. Conservar la colación, el permiso `clientes.ver` y la validación de permiso y CSRF de las acciones POST. Limitar y enlazar los enteros de paginación. Mantener `listarActivos()` de Ventas fuera del alcance.

## Factor Ámbar

| Dimensión | Puntos | Justificación |
|---|---:|---|
| Superficie | 1 | Vista, controlador y modelo en un módulo. |
| Datos | 1 | Lectura filtrada, conteo y resumen de compras. |
| Estado y permisos | 0 | Se reutiliza la autorización vigente. |
| Verificación | 3 | Integración de filtros, conteo, navegación en ambos sentidos y restablecimiento. |
| **Total** | **5/9** | Combinar filtros y cubrir páginas límite, parámetros vacíos y regresiones. |

## Controles y preparación

Tras autorización, usar una copia y una instancia descartables conforme al [README](../../../../tests/tarea-6/README.md). Preparar un usuario sintético con `clientes.ver`, conservar el vendedor de humo sin ese permiso y generar más de 50 coincidencias, con estados distintos, ciudad nula o vacía, nombres empatados, ciudades similares y compras de control. Usar los fixtures existentes como base. Conservar la atestación antes del login y en cada respuesta.

Ejecutar `powershell -NoProfile -File tools/quality/lint-php.ps1`, `php tests/tarea-6/run.php clientes-filtro-ciudad` tras incorporar el caso y `php tests/tarea-6/run.php humo`. Registrar SHA, estado del candidato y resultados sanitizados. Repetir controles si cambia el candidato.
