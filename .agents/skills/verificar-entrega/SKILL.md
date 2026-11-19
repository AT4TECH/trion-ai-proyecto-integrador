---
name: verificar-entrega
description: Valida un candidato de SistemaVentas contra su especificación y repite las comprobaciones si cambia.
---

1. Contrasta el diff con spec.md y revisa permisos, método, CSRF y transacciones según el alcance.
2. Ejecuta tools/quality/lint-php.ps1 y php tests/tarea-6/run.php <caso> en el entorno descartable.
3. Devuelve comandos, SHA, resultados por criterio y prerrequisitos faltantes.
