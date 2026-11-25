# SistemaVentas

## Arquitectura

Aplicación PHP/MySQL con arquitectura MVC existente. Mantener la separación de responsabilidades entre controladores, modelos, vistas, helpers y recursos estáticos.

Conservar las rutas, capas y comportamientos existentes que no formen parte de la funcionalidad especificada.

## Seguridad y datos

- Aplicar autorización y validación en el servidor.
- Mantener la protección CSRF en operaciones de escritura.
- Utilizar consultas preparadas con PDO.
- Escapar las salidas según su contexto.
- Conservar los límites transaccionales existentes.
- Mantener credenciales, configuraciones locales y datos reales fuera de Git.

## Flujo de desarrollo

- Trabajar en la rama recuperacion/triana.
- Utilizar el flujo oficial de GitHub Spec Kit instalado en el proyecto.
- Mantener cada funcionalidad en su propio directorio de especificación.
- Revisar los artefactos generados antes de avanzar a la siguiente fase.
- Limitar los cambios al alcance de cada especificación.
- Ejecutar las pruebas definidas y registrar únicamente resultados reales.
- No utilizar los flujos, perfiles ni instrucciones particulares de entregas académicas anteriores.
- No crear ramas adicionales, realizar fusiones, publicar cambios ni abrir pull requests sin autorización del usuario.