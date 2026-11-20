# Plan — Cierre de sesión seguro

## Rutas, capas y archivos

- `public/logout.php` valida primero el método, redirige al visitante y valida el CSRF del usuario autenticado antes de auditar o cerrar. Reutiliza los helpers de autenticación y CSRF. Un valor de formulario no escalar debe seguir el contrato 400.
- `views/layouts/header.php` presenta un formulario POST hacia la ruta existente, con `csrfCampo()` y un botón accesible. Ajustar `assets/css/style.css` si el botón lo requiere para conservar la apariencia y el enfoque del menú.
- `tests/tarea-6/casos/logout-post-csrf.php` incorpora los casos al runner. Ampliar el soporte HTTP únicamente donde falten capacidades para comprobar el contrato.

## Datos, permisos y riesgos

Cualquier rol autenticado puede cerrar su sesión actual. La única escritura MySQL del flujo de producto es el evento de auditoría existente, después de las validaciones y antes de perder la identidad. Conservar su registro técnico ante fallos sin exponer detalles internos en HTTP. Expirar la cookie con el nombre, ruta y dominio de sesión activos y destruir el estado del servidor. Las solicitudes rechazadas deben conservar el acceso y el conteo de eventos. El POST anónimo navega al ingreso sin escribir un evento.

## Factor Ámbar

| Dimensión | Puntos | Justificación |
|---|---:|---|
| Superficie | 1 | Vista, endpoint y helpers en un único flujo. |
| Datos | 2 | Escritura de auditoría y modificación del estado de sesión. |
| Estado y permisos | 2 | Se combinan método, identidad, token y estado posterior al cierre. |
| Verificación | 3 | Integra inicio de sesión, envío, auditoría, expiración y acceso posterior. |
| **Total** | **8/9** | Requiere preparación, casos de error, consistencia de datos y revisión independiente. |

[Rúbrica Factor Ámbar](../../factor-ambar.md) · [Matriz Halcón](../../matriz-halcon.md)

## Controles, casos HTTP y preparación del entorno

Tras autorizar la implementación, preparar la copia de la aplicación y la instancia independiente en `sistema_ventas_tarea6_test`, con usuario sintético, marcador y comprobaciones de aislamiento según el [README del arnés](../../../../tests/tarea-6/README.md). Medir el delta de auditoría del usuario sintético antes y después de cada caso. Verificar que el fallo controlado de auditoría se limita a la copia y permite distinguir un fallo de escritura de un fallo de aislamiento. Mantener respuestas, identificadores y tokens fuera de la evidencia.

El cliente actual cubre GET y POST, estado, cuerpo y redirección. Añadir al soporte de pruebas los métodos y cabeceras requeridos, incluida la conservación de múltiples `Set-Cookie`. Usar clientes independientes y mantener el identificador anterior en memoria para verificar la invalidación. Si las respuestas tempranas se producen antes de cargar la configuración de base, emitir el HMAC desde el arranque exclusivo de la copia descartable. Conservar la exigencia de atestación del runner y las comprobaciones de esquema, permisos y marcador.

Ejecutar el lint desde PowerShell y los casos `logout-post-csrf` y `humo` con `php tests/tarea-6/run.php <caso>`. El verificador revisará los criterios y los resultados contra el mismo candidato. Repetir los controles si cambia el candidato y registrar HEAD, condición de la copia y salidas sanitizadas.
