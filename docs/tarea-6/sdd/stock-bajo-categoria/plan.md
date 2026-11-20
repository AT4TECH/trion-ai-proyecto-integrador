# Plan — Filtrar stock bajo por categoría

## Rutas, capas y archivos

Añadir en `views/productos/stock.php` un selector GET `categoria` y la opción «Todas». Reutilizar `ProductoController::categorias()`, `listar()` y `resumenStock()`. `models/Producto.php` ya combina categoría, estado y stock bajo en una consulta preparada. `models/Categoria.php` es la fuente de opciones activas. El cambio funcional previsto cabe en la vista.

## Datos, permisos y riesgos

Validar el valor escalar como ID positivo dentro de rango y comprobar que pertenezca a las opciones activas antes de pasarlo al listado. Escapar los nombres de las categorías. Conservar las reglas de stock y los permisos vigentes, e indicar que las tarjetas tienen alcance global. Mantener el mensaje vacío general y usar texto contextual si hay una categoría seleccionada.

## Factor Ámbar

| Dimensión | Puntos | Justificación |
|---|---:|---|
| Superficie | 0 | Cambio en una vista que reutiliza las API existentes. |
| Datos | 1 | Lectura filtrada de productos y categorías. |
| Estado y permisos | 0 | Se conservan los controles actuales. |
| Verificación | 2 | Hay combinaciones de stock, estado, selección y permisos. |
| **Total** | **3/9** | Caso directo y regresiones sobre selección, umbral y acceso. |

## Controles y preparación

Usar exclusivamente la copia y la instancia descartables del [README](../../../../tests/tarea-6/README.md). Preparar categorías activas e inactiva, productos activos e inactivos y casos de stock por debajo, igual y por encima del mínimo. Preparar lectores sintéticos con y sin permisos de edición y un usuario sin permiso de lectura. Conservar la atestación previa al login y por respuesta.

Tras implementar, ejecutar `powershell -NoProfile -File tools/quality/lint-php.ps1`, `php tests/tarea-6/run.php stock-bajo-categoria` tras crear el caso y `php tests/tarea-6/run.php humo`. Registrar SHA y resultados sanitizados.
