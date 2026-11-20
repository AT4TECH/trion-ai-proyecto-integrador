# Especificación — Cierre de sesión seguro

## Comportamiento actual y fuentes

El [menú](../../../../views/layouts/header.php#L31) enlaza por GET al [endpoint](../../../../public/logout.php#L1). El endpoint intenta auditar si hay identidad autenticada, registra los fallos de auditoría, vacía y destruye la sesión y redirige a `login.php`. Actualmente acepta cualquier método, carece de validación CSRF y omite la expiración explícita de la cookie.

[AGENTS.md](../../../../AGENTS.md) exige validación de método y CSRF en operaciones de escritura. El proyecto ya define la identidad por `id_usuario` en [auth.php](../../../../helpers/auth.php#L33) y el campo `csrf` y su respuesta 400 en [funciones.php](../../../../helpers/funciones.php#L11).

## Objetivo, términos y roles

Cualquier usuario autenticado puede cerrar su sesión actual desde «Cerrar sesión» mediante POST con el token CSRF de esa sesión. Una sesión sin identidad autenticada incluye al visitante, la sesión caducada y la sesión ya cerrada. El endpoint evalúa método, identidad y CSRF en ese orden.

## Alcance y exclusiones

El ciclo abarca el control del menú, la ruta existente, la sesión actual, su cookie, el evento de auditoría y la redirección al ingreso. Quedan fuera el cierre de otras sesiones, los cambios de permisos por rol y los cambios generales de autenticación.

## Criterios de aceptación

- **CA-01.** Dada una solicitud GET u otro método distinto de POST, con o sin sesión activa, el endpoint responde `405` con `Allow: POST`. El intento no registra un cierre y una sesión activa conserva su acceso.
- **CA-02.** Dado un usuario autenticado, cuando envía un POST sin campo `csrf`, el endpoint responde `400`. La sesión conserva su acceso y el intento no registra un cierre.
- **CA-03.** Dado un usuario autenticado, cuando envía un POST con un token incorrecto, de otra sesión o con formato inválido, el endpoint responde `400`. La sesión conserva su acceso y el intento no registra un cierre.
- **CA-04.** Dado un usuario autenticado, cuando activa «Cerrar sesión», el menú envía un POST con el token de su sesión. Ante un POST autenticado válido, el endpoint registra un evento de cierre para el usuario, destruye su sesión, expira su cookie y redirige a `login.php`. La cookie anterior no permite acceder a una ruta protegida. Si falla la auditoría, el servidor registra el fallo técnico y completa el cierre y la misma redirección.
- **CA-05.** Dada una solicitud POST sin identidad autenticada, incluso una repetición tras el cierre, el endpoint redirige a `login.php` sin exigir token CSRF ni registrar un evento de cierre. Esta rama solo atiende la navegación al ingreso.
