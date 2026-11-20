# Especificación — Limpiar filtros de auditoría

## Comportamiento actual y fuentes

La [vista de auditoría](../../../../views/auditoria/index.php#L7) exige `auditoria.ver` y usa un formulario GET con `desde`, `hasta`, `usuario`, `modulo`, `accion` y `q` (texto en la descripción). `pagina` controla la paginación y `exportar=csv` activa la descarga. El formulario «Filtrar» inicia una búsqueda en página 1 y el paginador conserva los filtros activos. Hoy la vista carece de un control de limpieza.

El [controlador](../../../../controllers/AuditoriaController.php) usa 50 registros por página, limita la página al intervalo disponible y adopta la página 1 si se omite el parámetro o no hay resultados. Sin filtros en la URL aplica desde hoy menos siete días hasta hoy, según la zona America/Bogota configurada por la aplicación, con usuario, módulo, acción y texto sin restricciones adicionales. El [modelo](../../../../models/Auditoria.php) combina los filtros y aplica el mismo alcance al listado y al resumen.

## Objetivo, actores y alcance

Cualquier usuario con `auditoria.ver` puede usar «Limpiar filtros» junto a «Filtrar». Limpiar significa volver al estado inicial del módulo, incluido el rango predeterminado de fechas. El alcance funcional es la vista existente. Se mantienen las consultas, los permisos, el tamaño de página y la exportación.

## Criterios de aceptación

- **CA-01.** Con filtros activos, al activar «Limpiar filtros» se abre por GET la ruta base de auditoría sin cadena de consulta. Desde y Hasta recuperan sus fechas predeterminadas, los selectores muestran Todos/Todas y el texto queda vacío. El listado y el resumen coinciden con una visita directa a la ruta base con los mismos datos.
- **CA-02.** Desde una página posterior, una búsqueda sin coincidencias o la vista inicial, al limpiar se muestra la primera página del listado por defecto. El destino omite `pagina` y `exportar`. Repetir la acción conserva el mismo estado inicial y no descarga un CSV.
- **CA-03.** Después de limpiar, «Filtrar» aplica los criterios nuevos mediante GET y empieza en la página 1. Los enlaces Anterior y Siguiente conservan los filtros activos.
- **CA-04.** La exportación CSV sigue usando los filtros activos. La ruta base conserva el control de acceso: un visitante se redirige al ingreso y un usuario sin `auditoria.ver` recibe 403.
