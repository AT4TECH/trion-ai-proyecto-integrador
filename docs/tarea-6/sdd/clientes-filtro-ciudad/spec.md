# Especificación — Filtrar clientes por ciudad

## Comportamiento actual y fuentes

La [vista de clientes](../../../../views/clientes/index.php#L6) exige `clientes.ver`, muestra la ciudad en la tabla y recibe `q` y `estado` por GET. `q` busca por nombre, documento, teléfono o correo. El estado es Activos (`1`) por defecto, Inactivos (`0`) o Todos (vacío explícito).

El [controlador](../../../../controllers/ClienteController.php#L19) delega el listado al [modelo](../../../../models/Cliente.php#L12). La consulta combina los filtros, agrega el resumen de compras y devuelve hasta 500 filas. El listado actual carece de paginación y no procesa `pagina`.

El [DDL versionado del fixture](../../../../tests/tarea-6/fixtures/esquema.sql#L46) declara `clientes.ciudad VARCHAR(100) NULL`. El [formulario](../../../../views/clientes/_form.php#L15), la [normalización de escritura](../../../../controllers/ClienteController.php#L68) y las operaciones de alta y edición del [modelo](../../../../models/Cliente.php#L35) confirman que la aplicación usa ese campo. La comprobación se hizo en el repositorio.

## Objetivo, actores y alcance

Un usuario con `clientes.ver` puede filtrar el listado por ciudad. La conservación del filtro al navegar requiere incorporar paginación al listado. La propuesta incluye esa paginación para los resultados de Clientes. El selector de clientes de Ventas, las operaciones de escritura y el esquema quedan fuera del cambio.

## Criterios de aceptación

- **CA-01.** El filtro «Ciudad» usa el parámetro GET `ciudad`. Un texto escalar se recorta a ambos extremos y se limita a 100 caracteres. El valor se conserva en el control y se compara por igualdad del nombre completo con la colación de la columna. Una ciudad activa excluye filas con ciudad nula o vacía. Un valor ausente, vacío, compuesto solo por espacios o enviado como arreglo equivale a no filtrar por ciudad.
- **CA-02.** Ciudad, búsqueda `q` y estado se combinan mediante AND. Se mantienen los cuatro campos de búsqueda y los estados actuales, incluido el valor explícito vacío para Todos. El total refleja los clientes que cumplen todos los criterios y las filas muestran sus resúmenes de compras habituales. Cero coincidencias muestra «No hay clientes con estos filtros».
- **CA-03.** Si los resultados ocupan varias páginas, los enlaces Anterior y Siguiente conservan `q`, `estado` y `ciudad` activos. El conteo y las filas aplican los mismos filtros. Sin `pagina`, o si el valor es inválido o menor que 1, se abre la página 1. Una página numérica superior al total abre la última disponible. Un resultado vacío queda en página 1, sin enlaces de navegación.
- **CA-04.** Al enviar «Filtrar» desde cualquier página, la búsqueda comienza en página 1. Vaciar Ciudad y enviar conserva `q` y `estado` y recupera resultados de todas las ciudades. Abrir la ruta base sin consulta vuelve a Activos, con búsqueda y ciudad vacías y página 1.
- **CA-05.** La ruta conserva `clientes.ver`: sin sesión redirige al ingreso y con sesión sin permiso responde 403. Las operaciones de escritura conservan sus comprobaciones actuales de permiso y CSRF.
