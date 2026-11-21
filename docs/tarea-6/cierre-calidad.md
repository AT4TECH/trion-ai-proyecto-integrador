# Cierre de calidad — Proyecto Integrador Tarea 6

**Proyecto:** SistemaVentas  
**Grupo:** 2  
**Rama de entrega:** `main`  
**Candidato documentado:** `f8ea095277a1341bbf257c17124a5666b425ed37`

## 1. Propósito

Consolidar los mecanismos de aseguramiento de calidad utilizados en las cinco funcionalidades SDD y distinguir las verificaciones iniciales de las ejecuciones posteriores.

El proyecto utiliza dos mecanismos principales:

1. Análisis de sintaxis de archivos PHP.
2. Pruebas HTTP automatizadas basadas en criterios de aceptación.

 2. Mecanismo 1: análisis de sintaxis PHP

Herramienta `tools/quality/lint-php.ps1`

Comando

```powershell
powershell -NoProfile -ExecutionPolicy Bypass -File tools/quality/lint-php.ps1
```

Resultado registrado en la evidencia de implementación

- PHP 8.2.12.
- 124 archivos PHP revisados.
- Cero fallos de sintaxis.

Alcance detección de errores de sintaxis PHP en los archivos revisados.

Límite este mecanismo no demuestra que las operaciones de negocio funcionen correctamente ni sustituye las pruebas de integración.

 3. Mecanismo 2: pruebas HTTP automatizadas

Herramienta `tests/tarea-6/run.php`

Las suites utilizan una copia descartable de la aplicación, datos sintéticos y una instancia aislada de base de datos. La preparación está documentada en `tests/tarea-6/README.md`.

### Resultados consolidados

| Suite | Casos PASS |
|---|---:|
| Humo general | 4 |
| Cierre de sesión POST + CSRF | 7 |
| Limpiar filtros de auditoría | 4 |
| Clientes por ciudad | 5 |
| Stock bajo por categoría | 4 |
| Reporte de inventario por tipo | 5 |
| Total | 29  |

**Resultado 29 PASS, 0 FAIL, 0 SKIP en el registro consolidado.

Fuente [`evidencia-completa-tarea6.txt`](../../evidencia-completa-tarea6.txt).

Alcance verificación automatizada de criterios funcionales, validaciones, permisos, límites y regresiones definidos en las suites.

Límite los resultados corresponden al entorno aislado registrado. No equivalen a una prueba de despliegue público ni a una certificación de seguridad completa.

 4. Trazabilidad de las cinco funcionalidades

| Funcionalidad | Factor Ámbar | Pruebas HTTP | PR |
|---|---:|---:|---|
| Cierre de sesión seguro | 8/9 | 7 PASS | [#2](https://github.com/AT4TECH/trion-ai-proyecto-integrador/pull/2) |
| Auditoría: limpiar filtros | 3/9 | 4 PASS | [#3](https://github.com/AT4TECH/trion-ai-proyecto-integrador/pull/3) |
| Clientes: filtro por ciudad | 5/9 | 5 PASS | [#4](https://github.com/AT4TECH/trion-ai-proyecto-integrador/pull/4) |
| Stock bajo por categoría | 3/9 | 4 PASS | [#5](https://github.com/AT4TECH/trion-ai-proyecto-integrador/pull/5) |
| Inventario: reporte por tipo | 5/9 | 5 PASS | [#6](https://github.com/AT4TECH/trion-ai-proyecto-integrador/pull/6) |

Las especificaciones, planes, tareas y evidencias se encuentran en `docs/tarea-6/sdd/`, dentro de la carpeta de cada funcionalidad.

 5. Evolución de los resultados

Los documentos `evidencia.md` y `tasks.md` de algunas funcionalidades registran inicialmente `SKIP` para las pruebas HTTP. Esos registros corresponden a una etapa en la que faltaban los prerrequisitos del entorno descartable.

Posteriormente se preparó el entorno de pruebas y se incorporó el registro consolidado de 29 resultados `PASS` mediante el commit:

[`f8ea095`](https://github.com/AT4TECH/trion-ai-proyecto-integrador/commit/f8ea095277a1341bbf257c17124a5666b425ed37).

Los registros iniciales no se eliminan porque documentan la evolución real del trabajo.

 6. Coeficiente Murciélago

El enunciado exige reportar un **Coeficiente Murciélago por mecanismo de calidad**. No define en el documento una fórmula numérica obligatoria.

Para evitar presentar una puntuación arbitraria como resultado experimental, se propone registrar una valoración cualitativa basada en evidencia:

| Mecanismo | Confianza respaldada por evidencia | Limitación |
|---|---|---|
| Lint PHP | Alta para ausencia de errores de sintaxis en los archivos revisados | No mide corrección funcional |
| Pruebas HTTP | Alta para los 29 escenarios automatizados que pasaron | No demuestra cobertura total ni ausencia de defectos |

Coeficiente Murciélago propuesto:** confianza **alta dentro del alcance comprobado**, sin equivaler a una garantía general del software.

Esta interpretación debe explicarse como criterio adoptado por el equipo, no como una fórmula suministrada por el profesor.

 7. Evidencia del sistema de agentes

Los registros por funcionalidad describen intervenciones de los perfiles de análisis, implementación, pruebas y verificación, con identificadores de invocación y resultados.

La evidencia preparatoria distingue correctamente entre perfiles configurados, detectados y ejecutados. Las ejecuciones registradas posteriormente deben consultarse en los documentos `evidencia.md` de cada funcionalidad.

 8. Conclusión

Las cinco funcionalidades fueron integradas mediante pull requests fusionados en `main`. El proyecto conserva especificaciones, planes, tareas, evidencia de agentes y dos mecanismos de calidad.

La evidencia automatizada consolidada registra 29 pruebas HTTP aprobadas y una verificación de sintaxis de 124 archivos PHP sin fallos.

Las verificaciones respaldan los escenarios y archivos efectivamente comprobados, sin afirmar cobertura total ni ausencia absoluta de defectos.
