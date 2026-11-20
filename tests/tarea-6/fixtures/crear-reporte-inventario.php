<?php
declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require __DIR__ . '/../soporte/database.example.php';

try {
    $password = getenv('SV_TEST_LOGIN_PASSWORD');
    if ($password === false || strlen($password) < 12) {
        throw new RuntimeException('Falta la contraseña sintética.');
    }

    $db = (new Database())->getConnection();
    $db->beginTransaction();
    $db->exec("INSERT INTO roles (nombre) VALUES ('Reporte de inventario de prueba')");
    $role = (int) $db->lastInsertId();
    $insertPermission = $db->prepare('INSERT INTO permisos (nombre) VALUES (?) ON DUPLICATE KEY UPDATE nombre = nombre');
    $findPermission = $db->prepare('SELECT id_permiso FROM permisos WHERE nombre = ?');
    $grant = $db->prepare('INSERT INTO rol_permisos (id_rol, id_permiso) VALUES (?, ?)');
    foreach (['reportes.ver', 'inventario.ver'] as $permission) {
        $insertPermission->execute([$permission]);
        $findPermission->execute([$permission]);
        $grant->execute([$role, $findPermission->fetchColumn()]);
    }
    $db->prepare("INSERT INTO usuarios (nombre, usuario, password, rol_id)
        VALUES ('Lector inventario', 'tarea6-inventario-reporte', ?, ?)")->execute([
            password_hash($password, PASSWORD_DEFAULT), $role,
        ]);
    $user = (int) $db->lastInsertId();
    $db->exec("INSERT INTO categorias (nombre) VALUES ('Reporte de inventario de prueba')");
    $category = (int) $db->lastInsertId();
    $db->prepare("INSERT INTO productos (id_categoria, codigo, nombre, stock, precio_compra, precio_venta)
        VALUES (?, 'T6-INV-CSV', ?, 4, 2, 7)")->execute([$category, 'Artículo de inventario']);
    $product = (int) $db->lastInsertId();

    $insert = $db->prepare('INSERT INTO movimientos_inventario
        (id_producto, id_usuario, tipo, cantidad, stock_anterior, stock_nuevo, motivo, referencia, fecha_movimiento)
        VALUES (?, ?, ?, 1, ?, ?, ?, ?, ?)');
    foreach ([
        ['entrada', '0', '1', 'Fuera del rango inicial', null, '2022-07-06 23:59:59'],
        ['entrada', '-1.25', '1.5', "Recepción; \"revisión\"\nsegunda línea", " \t=1+1", '2022-07-07 00:00:00'],
        ['salida', '1.5', '0.5', '=1+1', 'REF-INV-02', '2022-07-07 14:30:00'],
        ['ajuste', '0.5', '3.5', 'Ajuste de prueba', 'REF-INV-03', '2022-07-08 01:00:00'],
        ['devolucion', '3.5', '4', 'Devolución de prueba', 'REF-INV-04', '2022-07-08 23:59:59'],
        ['devolucion', '4', '5', 'Fuera del rango final', null, '2022-07-09 00:00:00'],
    ] as $row) {
        $insert->execute([$product, $user, ...$row]);
    }
    $db->commit();
    fwrite(STDOUT, "Fixture del reporte de inventario creado.\n");
} catch (Throwable) {
    if (isset($db) && $db->inTransaction()) {
        $db->rollBack();
    }
    fwrite(STDERR, "No se pudo crear el fixture en la instancia descartable.\n");
    exit(1);
}
