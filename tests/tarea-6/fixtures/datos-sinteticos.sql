INSERT INTO sistema_ventas_tarea6_test.roles (id_rol, nombre) VALUES (2, 'Vendedor de prueba');
INSERT INTO sistema_ventas_tarea6_test.permisos (id_permiso, nombre)
VALUES (1, 'dashboard.ver'), (2, 'ventas.ver');
INSERT INTO sistema_ventas_tarea6_test.rol_permisos (id_rol, id_permiso) VALUES (2, 1), (2, 2);
INSERT INTO sistema_ventas_tarea6_test.configuracion (clave, valor)
VALUES ('nombre_empresa', 'Comercio Sintético Tarea 6'), ('permisos_version', 'fixture-1');
