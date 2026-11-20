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
    $db->exec("INSERT INTO roles (nombre) VALUES ('Consulta de clientes de prueba')");
    $role = (int) $db->lastInsertId();
    $db->exec("INSERT INTO permisos (nombre) VALUES ('clientes.ver')");
    $permission = (int) $db->lastInsertId();
    $db->prepare('INSERT INTO rol_permisos (id_rol, id_permiso) VALUES (?, ?)')->execute([$role, $permission]);
    $db->prepare("INSERT INTO usuarios (nombre, usuario, password, rol_id)
        VALUES ('Consulta de clientes de prueba', 'tarea6-clientes', ?, ?)")->execute([
        password_hash($password, PASSWORD_DEFAULT), $role,
    ]);
    $user = (int) $db->lastInsertId();

    $insert = $db->prepare('INSERT INTO clientes (documento, nombre, telefono, email, ciudad, estado)
        VALUES (?, ?, ?, ?, ?, ?)');
    for ($i = 1; $i <= 52; $i++) {
        $insert->execute([sprintf('CLI-P-%02d', $i), 'Tarea6 Grupo', null, null, 'Bogotá', $i <= 51 ? 1 : 0]);
    }
    $insert->execute(['CLI-OTRA', 'Tarea6 Grupo', null, null, 'Bogotá Norte', 1]);
    $insert->execute(['CLI-NULA', 'Tarea6 Grupo', null, null, null, 1]);
    $insert->execute(['CLI-VACIA', 'Tarea6 Grupo', null, null, '', 1]);
    $insert->execute(['CLI-COMPRA', 'Tarea6 Compras', '6010000001', 'tarea6-compras@example.test', 'Bogotá', 1]);
    $customer = (int) $db->lastInsertId();
    $insert->execute(['CLI-LARGA', 'Tarea6 Ciudad larga', null, null, str_repeat('Á', 100), 1]);
    $sale = $db->prepare("INSERT INTO ventas (numero_factura, id_cliente, id_usuario, fecha_venta, total, estado)
        VALUES (?, ?, ?, ?, ?, ?)");
    $sale->execute(['CLI-VENTA-1', $customer, $user, '2020-01-02 12:00:00', 125, 'pagada']);
    $sale->execute(['CLI-VENTA-2', $customer, $user, '2020-01-03 12:00:00', 999, 'anulada']);
    $db->commit();
    fwrite(STDOUT, "Fixture de clientes creado.\n");
} catch (Throwable) {
    if (isset($db) && $db->inTransaction()) {
        $db->rollBack();
    }
    fwrite(STDERR, "No se pudo crear el fixture de clientes en la instancia descartable.\n");
    exit(1);
}
