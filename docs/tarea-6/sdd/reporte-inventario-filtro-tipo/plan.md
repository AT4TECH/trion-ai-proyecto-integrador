# Plan — Filtrar el CSV de inventario por tipo de movimiento

## Rutas, capas y archivos

- `views/reportes/inventario.php` valida `tipo` mediante lista cerrada después de la autorización y antes de emitir cabeceras. Usa la misma lista para opciones y etiquetas, y pasa el tipo normalizado al detalle.
- `views/reportes/_inicio.php` puede admitir una opción explícita de Inventario en `cabeceraReporte()`, con valor predeterminado que conserva los demás consumidores. Incorporar el selector y propagar el valor mediante `http_build_query` y escape HTML solo en enlaces y controles de Inventario.
- `controllers/ReporteController.php` ya antepone las fechas mediante `__call`. Reutilizar ese contrato. `models/Reporte.php` añade un parámetro opcional a `movimientosDetalle()` y un predicado SQL fijo `m.tipo = :tipo` cuando hay selección. Mantener los joins y el intervalo.
- Reutilizar `helpers/reportes.php` para cabeceras y serialización. Aplicar el refuerzo antifórmulas a los campos textuales de esta descarga antes de serializar, preservando el comportamiento de las demás exportaciones.

## Datos, permisos y riesgos

Mantener la autorización antes de validar o exportar. El tipo solo puede ser un literal de la lista permitida y se enlaza como dato. Mantener `fputcsv` para el escape estructural y neutralizar indicadores de fórmula al inicio o tras espacios y controles iniciales de campos textuales. Evitar duplicar el prefijo de protección del helper. Conservar la conversión existente de las columnas numéricas.

## Factor Ámbar

| Dimensión | Puntos | Justificación |
|---|---:|---|
| Superficie | 1 | Vista, cabecera reutilizada y modelo de un reporte. |
| Datos | 1 | Lectura filtrada y serialización CSV. |
| Estado y permisos | 0 | Se conservan los permisos vigentes. |
| Verificación | 3 | Integra formulario, fechas, exportación, decodificación CSV y controles de seguridad. |
| **Total** | **5/9** | Requiere combinaciones y límites de fechas, tipo, autorización y contenido CSV. |

## Controles y preparación

Usar la [copia descartable](../../../../tests/tarea-6/README.md) con usuarios sintéticos con y sin `reportes.ver`, movimientos de los cuatro tipos dentro y fuera del rango y campos que ejerciten el escape. Atestar antes del login y cada respuesta. Tras implementar, ejecutar el lint PHP, `php tests/tarea-6/run.php reporte-inventario-filtro-tipo` y `php tests/tarea-6/run.php humo` en el entorno aislado. Registrar SHA y resultados sanitizados.
