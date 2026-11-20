# SistemaVentas · Tarea 6 · Grupo 2

## Arquitectura y límites

Aplicación PHP/MySQL con MVC distribuido. public/ inicia sesión y sirve el dashboard; views/ sirve páginas, procesa formularios y contiene endpoints JSON. controllers/ coordina operaciones, models/ usa PDO, helpers/ comparte autorización, CSRF, formateo y respuestas, y assets/ reúne CSS y JavaScript. config/config.php define la URL base, zona horaria y sesión. config/database.php es local e ignorado. El volcado local también es ignorado.

Conserva rutas, capas y comportamiento de los módulos de ventas, inventario, caja, clientes, facturación, devoluciones, reportes y administración. Usa los helpers existentes y mantén el alcance de cada cambio.

## Seguridad y datos

- Aplica permisos en el servidor, validación de método y CSRF en operaciones que escriben. Usa consultas preparadas y escapa la salida en el contexto que corresponda.
- Conserva límites transaccionales y bloqueos de inventario, ventas y caja. Registra fallos técnicos sin divulgar detalles internos en HTTP.
- Mantén credenciales, configuración local y volcados reales fuera de Git y de la evidencia. Las pruebas HTTP usan la copia y la base descartables descritas en tests/tarea-6/README.md.
- Solicita autorización antes de push, apertura de PR o fusión.

## Ciclo SDD

Trabaja en una rama por funcionalidad. Aplica brújula-semántica y deriva spec.md, plan.md y tasks.md en ese orden. Puntúa Factor Ámbar antes de implementar. Vincula cada tarea y prueba con un criterio de aceptación. Usa las plantillas y la Matriz Halcón en docs/tarea-6/.

La sesión principal coordina como orquestador-ventas. Cuando el usuario o las instrucciones aplicables autoricen delegación, asigna tareas concretas a los perfiles de .codex/agents/ y delimita los archivos de escritura. Si dos perfiles necesitan el mismo archivo, asigna un propietario a la vez. Verificador aplica también los criterios de revisor-arquitectura. Cronista-lunar registra resultados con registrar-evidencia.

## Controles antes de entregar

1. Ejecuta tools/quality/lint-php.ps1 desde PowerShell.
2. Ejecuta php tests/tarea-6/run.php <caso> contra la instancia descartable.
3. Registra comandos, resultados y SHA. En trabajo sin commit registra HEAD y que se probó una copia con cambios locales. Distingue prueba pendiente de prueba fallida y perfil configurado de perfil detectado o ejecutado.
4. Repite los controles si cambia el candidato y vincula la evidencia y los criterios en el PR.
