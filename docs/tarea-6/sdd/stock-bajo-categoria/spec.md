# Especificación — Filtrar stock bajo por categoría

## Comportamiento actual y fuentes

La [vista](../../../../views/productos/stock.php) protege el acceso con `productos.ver`, muestra tarjetas globales y lista productos activos con `stock <= stock_minimo`. Solo `productos.editar` habilita el costo y los enlaces Editar. La lista se ordena por nombre entre activos y tiene un límite de 500. La pantalla carece de filtros GET, paginación y exportación.

El [controlador](../../../../controllers/ProductoController.php) ya ofrece el listado filtrado y las categorías activas. El [modelo Producto](../../../../models/Producto.php) implementa el filtro preparado por ID y el [modelo Categoría](../../../../models/Categoria.php) ofrece las opciones activas ordenadas por nombre. El [DDL versionado](../../../../tests/tarea-6/fixtures/esquema.sql#L62) confirma la relación `productos.id_categoria` → `categorias.id_categoria`, las columnas `estado` de ambas tablas y `productos.stock` y `productos.stock_minimo` como `DECIMAL(12,3)`.

## Alcance

El selector aplica a la tabla «Productos por reabastecer». Las tarjetas conservan su alcance global. Las opciones reutilizan las categorías activas del catálogo. El catálogo y el reporte de inventario siguen abriendo Stock bajo sin parámetros. «Ver todos los productos» y «Editar» conservan sus destinos y el enlace de edición conserva su ID.

## Criterios de aceptación

- **CA-01.** Una categoría activa seleccionada mediante GET `categoria` permanece seleccionada. La tabla muestra solo productos activos de esa categoría cuyo stock está en o por debajo del mínimo, incluidos los agotados que cumplan esa regla.
- **CA-02.** Al abrir la ruta base, omitir el parámetro, enviarlo vacío o elegir «Todas», se obtiene el listado anterior, con su orden y límite. Esto incluye productos activos en categorías inactivas si cumplen la regla de stock bajo. Las tarjetas muestran los mismos totales globales con y sin filtro.
- **CA-03.** Un valor que no sea un ID entero positivo seleccionable, incluso arreglo, ID inexistente o inactivo, o valor fuera de rango, se trata como «Todas» sin error HTTP. Si una categoría activa válida carece de coincidencias, la selección se conserva y se muestra un mensaje vacío referido a esa categoría.
- **CA-04.** Se mantiene `productos.ver` en el servidor. Un visitante se redirige al ingreso y un usuario sin el permiso recibe 403. El costo y Editar siguen reservados a `productos.editar`. Los enlaces existentes conservan sus destinos y la pantalla sigue sin paginación ni exportación.
