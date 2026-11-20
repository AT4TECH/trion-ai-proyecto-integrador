# Especificación — Filtrar el CSV de inventario por tipo de movimiento

## Comportamiento actual y fuentes

[Inventario](../../../../views/reportes/inventario.php) sirve HTML y, con `exportar=csv`, descarga movimientos ordenados por fecha. El [inicio compartido](../../../../views/reportes/_inicio.php) exige `reportes.ver` y prepara el formulario, las pestañas, los atajos y la exportación. El [controlador](../../../../controllers/ReporteController.php) interpreta `desde` y `hasta`, usa del primero del mes a hoy por defecto e intercambia fechas invertidas. [Reporte::movimientosDetalle](../../../../models/Reporte.php#L96) aplica el rango inclusivo por día y une `movimientos_inventario` con `productos` y `usuarios`. La vista carece de paginación. El enlace al kardex conserva las fechas y exige `inventario.ver`.

El [esquema versionado](../../../../tests/tarea-6/fixtures/esquema.sql#L254) confirma `movimientos_inventario.tipo ENUM('entrada','salida','ajuste','devolucion')`, `fecha_movimiento`, `stock_anterior`, `stock_nuevo`, `motivo` y `referencia`, y las claves `id_producto` e `id_usuario`. El [helper CSV](../../../../helpers/reportes.php#L60) serializa con `fputcsv` y antepone un apóstrofo a cadenas que comienzan con indicadores de fórmula.

## Objetivo y alcance

Una persona con `reportes.ver` puede elegir «Tipo de movimiento (CSV)». El filtro afecta la descarga del detalle. Los indicadores, la valoración, el resumen de movimientos, el kardex y los demás reportes conservan su comportamiento actual.

## Criterios de aceptación

- **CA-01.** Con `exportar=csv` y `tipo` ausente o vacío, se exportan todos los movimientos del rango efectivo, con el orden por fecha existente. Elegir «Todos» equivale a quitar el filtro de tipo.
- **CA-02.** Cada uno de los cuatro tipos admitidos devuelve únicamente movimientos de ese tipo dentro del mismo rango. Un tipo válido sin coincidencias genera un CSV con sus encabezados y sin filas de datos.
- **CA-03.** Después de comprobar el permiso, cualquier valor distinto de los cuatro literales o la cadena vacía, incluidos arreglos y valores malformados, devuelve HTTP 400 con un mensaje genérico, sin reflejar la entrada ni iniciar una descarga. El tipo válido se enlaza como parámetro y se combina con el intervalo mediante AND.
- **CA-04.** En Inventario, el formulario, los atajos de fechas y el enlace CSV conservan el tipo elegido. Cambiar el tipo conserva las fechas. Las pestañas de otros reportes y el enlace al kardex conservan las fechas según su contrato actual. La vista y la descarga mantienen `reportes.ver`, incluida la redirección al ingreso y el 403 para una sesión sin permiso.
- **CA-05.** El CSV conserva el nombre `movimientos_inventario_<desde>_a_<hasta>.csv`, la disposición de descarga, UTF-8 con BOM, separador punto y coma, formato decimal vigente y los nueve encabezados en orden: Fecha, Código, Producto, Tipo, Stock anterior, Stock nuevo, Motivo, Referencia y Usuario. Los campos con separadores, comillas o saltos de línea se recuperan completos al parsear el CSV. Los campos textuales susceptibles de interpretarse como fórmulas se exportan como texto, incluidos los casos en que espacios o controles iniciales preceden el indicador. Esta protección se limita al CSV de Inventario y conserva el tratamiento actual de las columnas de stock.
