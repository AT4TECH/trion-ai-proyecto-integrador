SistemaVentas — Proyecto Integrador · Tarea 6

Diplomado en Ingeniería de Software con IA y Sistemas Agénticos · Grupo 2

Arnés certificado por el profesor (código FET-ARN-42)

> Esta frase se incluye como requisito textual del enunciado; no constituye una certificación independiente.

 1. Descripción del proyecto

SistemaVentas es una aplicación web desarrollada en PHP y MySQL para gestionar ventas, caja, devoluciones, productos, categorías, inventario, clientes, reportes, usuarios, roles, configuración y auditoría.

El proyecto integrador aplica un arnés de desarrollo asistido por IA para implementar y verificar cinco funcionalidades mediante desarrollo guiado por especificaciones (SDD).

 2. Arquitectura

- `public/`: entrada a la aplicación, autenticación y dashboard.
- `views/`: interfaces, formularios y endpoints.
- `controllers/`: coordinación de operaciones.
- `models/`: acceso a datos mediante PDO.
- `helpers/`: autorización, CSRF y utilidades.
- `assets/`: CSS y JavaScript.
- `config/`: configuración local y de aplicación.

Las credenciales y los volcados reales de base de datos permanecen fuera del repositorio.

 3. Componentes del arnés

 Memoria e instrucciones

`AGENTS.md` establece la arquitectura, convenciones, seguridad, límites de modificación, flujo SDD y controles previos a la entrega.

 Skills

El arnés documenta cuatro procedimientos:

- brujula-semantica**: transforma criterios de aceptación en casos de prueba verificables.
- planificar-sdd**: organiza las decisiones técnicas y las tareas.
- verificar-entrega**: comprueba el candidato mediante controles y pruebas.
- registrar-evidencia**: conserva los resultados y la trazabilidad del trabajo.

 Matriz Halcón

La relación entre roles y skills está documentada en `docs/tarea-6/matriz-halcon.md`.

 Factor Ámbar

La complejidad se evalúa en una escala de 1 a 9, siguiendo `docs/tarea-6/factor-ambar.md`. Cada plan debe registrar su puntuación específica.

 4. Sistema de agentes

El arnés contempla los roles de orquestador, analista de requisitos, probador, implementadores de backend y frontend, verificador, revisor de arquitectura y cronista.

Los perfiles configurados se encuentran en `.codex/agents/`, y `AGENTS.md` describe las responsabilidades adicionales del orquestador y la coordinación.

**Importante:** la existencia de un perfil no debe confundirse con evidencia de ejecución. Las intervenciones reales deben respaldarse con registros verificables.
 5. Flujo SDD

1. Analizar el comportamiento actual.
2. Redactar la especificación y sus criterios de aceptación.
3. Elaborar el plan y registrar el Factor Ámbar.
4. Dividir el trabajo en tareas.
5. Implementar en una rama específica.
6. Ejecutar las pruebas y controles de calidad.
7. Registrar resultados y revisar el pull request.
8. Fusionar los cambios aprobados en `main`.

## 6. Cinco funcionalidades desarrolladas

| Funcionalidad | Pull request |
|---|---|
| Cierre de sesión seguro mediante POST y CSRF | #2 |
| Restablecimiento de filtros de auditoría | #3 |
| Filtro de clientes por ciudad | #4 |
| Filtro de stock bajo por categoría | #5 |
| Filtro del reporte de inventario por tipo de movimiento | #6 |

Los cinco pull requests aparecen fusionados en la rama principal.

 7. Aseguramiento de la calidad

 Mecanismo 1: análisis de sintaxis PHP

El script `tools/quality/lint-php.ps1` comprueba los archivos PHP del proyecto. Se registró una ejecución con 124 archivos aprobados y cero fallos.

 Mecanismo 2: pruebas HTTP automatizadas

El runner `tests/tarea-6/run.php` verifica los criterios de aceptación sobre una copia descartable de SistemaVentas y una base de datos aislada.

La evidencia consolidada registra **29 aserciones PASS, 0 FAIL y 0 SKIP**, incluyendo cuatro pruebas generales de humo.

La preparación del entorno y los casos están documentados en `tests/tarea-6/README.md`.

 Coeficiente Murciélago

Pendiente de completar con la valoración justificada de cada mecanismo.** No se asigna una cifra sin el criterio y la evidencia correspondientes.

 8. Evidencia y trazabilidad

- `AGENTS.md`: memoria y reglas.
- `docs/tarea-6/matriz-halcon.md`: relación entre agentes y skills.
- `docs/tarea-6/factor-ambar.md`: complejidad.
- `docs/tarea-6/evidencia-arnes.md`: verificaciones preparatorias.
- `tests/tarea-6/README.md`: guía de pruebas.
- `evidencia-completa-tarea6.txt`: resultados HTTP.
- Pull requests #1–#6: integración del arnés y funcionalidades.

## 9. Pendientes de verificación documental

Antes de la entrega, confirmar y completar:

- Las diferencias del sistema de agentes respecto al diseño del taller.
- La evidencia de ejecución real de los agentes.
- La puntuación Factor Ámbar de cada plan.
- El Coeficiente Murciélago de cada mecanismo de calidad.
- La frase «Spec revisada por el profesor» en las descripciones de los cinco PR.
- La ubicación de las especificaciones, planes y tareas de cada funcionalidad.

## 10. Ejecución local

La aplicación se sirve desde XAMPP en:

`http://localhost/sistema_ventas/public/`

La configuración de la base de datos se instala localmente en `config/database.php` y no debe publicarse en Git.

Las pruebas automatizadas utilizan un entorno descartable separado de los datos reales.
