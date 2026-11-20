# Tareas — Cierre de sesión seguro

| ID | CA | Acción | Archivos | Prueba | Estado |
|---|---|---|---|---|---|
| T-01 | CA-01 a CA-05 | Validar método, identidad y CSRF. Conservar auditoría, invalidar sesión y expirar la cookie tras validar. | `public/logout.php` | LOGOUT-01 a LOGOUT-05 | Implementado. HTTP SKIP |
| T-02 | CA-04 | Cambiar el enlace a formulario POST con CSRF y botón accesible. | `views/layouts/header.php`, `assets/css/style.css` | LOGOUT-04 y revisión de teclado | Implementado. HTTP SKIP y revisión en navegador pendiente |
| T-03 | CA-01 a CA-05 | Preparar cliente y casos. Conservar la atestación y medir el delta de auditoría. | `tests/tarea-6/casos/logout-post-csrf.php` y soporte del arnés | Matriz de pruebas inferior | Suite implementada. Copia descartable pendiente |
| T-04 | CA-01 a CA-05 | Ejecutar lint, suite funcional y humo. Someter el candidato a revisión independiente y registrar evidencia. | `evidencia.md` | Lint, LOGOUT y HUMO | Revisión estática realizada. HTTP SKIP por prerrequisitos |

## Casos automatizados y comprobaciones manuales

| Caso | CA | Tipo | Verificación esperada |
|---|---|---|---|
| LOGOUT-01 | CA-01 | Negativa | GET y PUT con y sin sesión reciben 405 y `Allow: POST`. La sesión activa conserva acceso y el delta de auditoría es cero. |
| LOGOUT-02 | CA-02 | Negativa | POST autenticado sin campo recibe 400. El dashboard sigue accesible y el delta es cero. |
| LOGOUT-03 | CA-03 | Negativa | POST con valor incorrecto, de otra sesión o no escalar recibe 400. El dashboard sigue accesible y el delta es cero. |
| LOGOUT-04 | CA-04 | Positiva | Verificar acción, método, campo oculto y botón de envío. Enviar el token de esa sesión. Comprobar redirección, expiración de cookie, un evento del usuario sintético y pérdida de acceso con el identificador anterior. Verificación manual del foco y apariencia pendiente. |
| LOGOUT-04-F | CA-04 | Negativa | Simular un fallo de acceso a auditoría solo en la copia. Comprobar cierre, redirección, delta cero y ausencia de detalles internos en HTTP. Verificación manual del log pendiente. |
| LOGOUT-05 | CA-05 | Regresión | POST de cliente nuevo y repetición tras el cierre, con y sin token, redirigen al login con delta cero. |
| LOGOUT-REG | CA-04, CA-05 | Regresión | Un nuevo login después del cierre recupera el acceso y conserva el 403 del vendedor en auditoría. |
| HUMO-01 a HUMO-04 | CA-04, CA-05 | Regresión | Verificar acceso anónimo, autenticación, dashboard y permiso de auditoría. |

Los siete casos LOGOUT y cuatro HUMO quedaron en SKIP por falta de configuración descartable. Ver [evidencia.md](evidencia.md).
