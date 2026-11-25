<!-- SYNC IMPACT REPORT
- Version change: N/A -> 1.0.0 (first ratified version; the scaffold carried no version)
- Modified principles: I (reformulada), II (ampliada: consultas preparadas y parametrizadas
  cuando intervengan datos variables; escape de salidas según contexto), III (conservada),
  IV (actualizada: rutas specs/<funcionalidad>), V (actualizada: validación de sintaxis PHP
  + pruebas funcionales derivadas de criterios de aceptación, entorno aislado)
- Added sections: "Restricciones y Estándares de la Stack", "Flujo de Calidad y Trabajo", Governance rules
- Removed sections: none
- Follow-up TODOs: Ratificación inicial autorizada por el usuario el 2026-11-24.
-->

# SistemaVentas Constitution

## Core Principles

### I. Conservación de la arquitectura PHP 8.2 — MySQL — MVC — PDO

SistemaVentas DEBE permanecer una aplicación PHP 8.2 con MySQL/MariaDB, arquitectura MVC y
acceso a datos mediante PDO. Los niveles y prácticas declarativas observados DEBEN conservarse:
`declare(strict_types=1)`, tipado de propiedades y PDO con `ATTR_EMULATE_PREPARES => false`.
Los directorios `public/`, `controllers/`, `models/`, `views/`, `helpers/`, `config/` y `assets/`
DEBEN conservar su separación de responsabilidades; conservar las rutas, capas y
comportamientos existentes que no formen parte de la funcionalidad especificada.

### II. Seguridad: autorización, validación del lado del servidor, CSRF, consultas preparadas y parametrizadas, escape de salidas según contexto

Toda operación de escritura DEBE requerir autorización del lado del servidor
(helpers/permisos.php: tienePermiso, requirePermission; ROL_ADMINISTRADOR) y validación del
lado del servidor con patrones de validación observados (ejemplo: UsuarioController::validar).
Los formularios POST DEBEN estar protegidos contra CSRF según el entorno observado
(verificarCsrf()/csrfCampo() con hash_equals, helpers/funciones.php). Todas las consultas que
involucren datos variables DEBEN ser preparadas con PDO, pasando los parámetros como variables
vinculadas (nombrados o posicionales según convenga), sin interpolación de variables en la
sentencia. Toda salida de datos DEBE escaparse según su contexto (según el entorno observado,
con e() para texto HTML: htmlspecialchars, ENT_QUOTES, UTF-8). Las credenciales y los datos
reales DEBEN permanecer fuera de Git (.gitignore).

### III. Cambios mínimos y compatibilidad con el comportamiento existente

Los cambios DEBEN limitarse al alcance de la funcionalidad especificada. No debe romperse ni
modificarse comportamientos existentes fuera de ese alcance. Las transacciones existentes
(beginTransaction en los controladores que las usan) DEBEN conservarse.

### IV. Desarrollo dirigido por especificaciones con trazabilidad

Toda funcionalidad DEBE desarrollarse con SDD: especificación con criterios de aceptación,
planificación, tareas y evidencia real. Cada funcionalidad DEBE mantener sus artefactos en su
propio directorio de specs nativo, bajo specs/<funcionalidad>/ (estructura nativa de Spec Kit
1.4.4). DEBE existir trazabilidad entre requisitos, planificación, tareas y pruebas.

### V. Verificación mediante pruebas reales en un entorno seguro

Las verificaciones DEBEN ejecutarse sobre un entorno aislado: copia del sistema y base de
datos sintética o descartable, sin datos reales. Validación de sintaxis PHP obligatoria; se
requieren pruebas funcionales derivadas de los criterios de aceptación de cada nueva
especificación, ejecutadas en el entorno aislado. Los resultados registrados DEBEN ser
resultados reales y verificables. Un resultado solo es válido si una prueba lo demuestra
realmente.

## Restricciones y Estándares de la Stack

- **Stack técnico:** PHP 8.2, MySQL/MariaDB compatible con XAMPP; arquitectura MVC; PDO con
  consultas preparadas.
- **Protección de datos:** config/database.php, *.env, claves y credenciales DEBEN permanecer
  fuera de Git (.gitignore); datos reales DEBEN usarse solo fuera de los entornos de prueba.
- **Validación y pruebas:** validación de sintaxis PHP obligatoria; pruebas funcionales
  derivadas de los criterios de aceptación de cada especificación, ejecutadas en un entorno
  aislado. Resultados registrados únicamente reales.
- **Convenciones:** timezone America/Bogota (config/config.php); permisos de rol con
  ROL_ADMINISTRADOR; transacciones mediante PDO beginTransaction.

## Flujo de Calidad y Trabajo

- **Flujo SDD:** analizar el comportamiento actual; redactar especificación y criterios de
  aceptación; planificar; dividir en tareas; implementar; ejecutar pruebas; registrar resultados
  reales (specs/<funcionalidad>/).
- **Revisión:** los artefactos generados DEBEN revisarse antes de avanzar a la siguiente fase;
  el diff debe coincidir con la especificación.
- **Gates de calidad:** validación de sintaxis PHP sin fallos; pruebas funcionales derivadas
  de los criterios de aceptación, ejecutadas en entorno aislado; registrar únicamente
  resultados reales.
- **Rama de trabajo:** desarrollo exclusivo en recuperacion/triana; sin fusiones, publicación
  ni pull requests sin autorización del usuario.

## Governance

- **Procedimiento de enmienda:** las modificaciones a esta constitución DEBEN documentarse
  (Sync Impact Report: old→new, principios añadidos o eliminados, secciones añadidas o
  eliminadas) antes de aplicarse.
- **Versionado:** SEMVER — MAJOR: cambios incompatibles de gobernanza o redefiniciones de
  principios; MINOR: nuevos principios o secciones añadidos; PATCH: aclaraciones y retoques
  sin efecto semántico.
- **Cumplimiento:** la planificación y los pull requests DEBEN verificar cada funcionalidad
  contra los principios vigentes y la versión de la constitución en uso.

**Version**: 1.0.0 | **Ratified**: 2026-11-24 | **Last Amended**: 2026-11-24
