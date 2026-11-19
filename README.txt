SistemaVentas

Aplicación PHP/MySQL para XAMPP. Sirve el proyecto bajo /sistema_ventas y usa
http://localhost/sistema_ventas/public/ como entrada local. La configuración de
base de datos y los volcados del entorno se gestionan fuera de Git. Solicita la
configuración y el esquema aprobados para tu instalación.

Para instalar la conexión local, crea config/database.php en la raíz del
proyecto, dentro de config/. Debe declarar la clase Database y ofrecer
Database::getConnection(): PDO con una conexión MySQL al esquema autorizado.
Obtén los parámetros de acceso por el canal aprobado y conserva ese archivo
fuera de Git. El ejemplo tests/tarea-6/soporte/database.example.php se instala
como config/database.php solo en la copia descartable, servida en
http://127.0.0.1:8765/sistema_ventas/public/. La guía de pruebas explica el
marcador, la cuenta restringida y la preparación de esa copia.

Módulos
- Dashboard de ventas, caja y devoluciones del día.
- Punto de venta, listado, detalle, anulación y factura (ticket).
- Caja con apertura, ingresos, egresos y cierre con cuadre.
- Devoluciones parciales o totales con reembolso y reintegro al inventario.
- Productos, categorías, inventario (kardex, entradas, salidas y ajustes),
  clientes, reportes, usuarios, roles, configuración y auditoría.

Los permisos determinan las acciones disponibles por rol. Los cambios de
permisos se sincronizan en las rutas protegidas sin iniciar sesión de nuevo.

Tarea 6 · Diplomado en Ingeniería de Software con IA y Sistemas Agénticos · Grupo 2

- AGENTS.md contiene las instrucciones permanentes y el flujo SDD.
- docs/tarea-6/matriz-halcon.md vincula los roles de Proyecto Albatros con Codex.
- docs/tarea-6/factor-ambar.md y docs/tarea-6/plantillas/ guían cada ciclo.
- tests/tarea-6/README.md explica cómo ejecutar lint y el caso HTTP.
- docs/tarea-6/evidencia-arnes.md registra las verificaciones del arnés.

Cada funcionalidad tendrá una rama y un PR propios. Sus artefactos SDD y
resultados se agregarán con el cambio correspondiente.
