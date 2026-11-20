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

    $insertPermission = $db->prepare('INSERT INTO permisos (nombre) VALUES (?)');
    $permissions = [];
    foreach (['productos.ver', 'productos.editar'] as $permission) {
        $insertPermission->execute([$permission]);
        $permissions[$permission] = (int) $db->lastInsertId();
    }
    $insertRole = $db->prepare('INSERT INTO roles (nombre) VALUES (?)');
    $grant = $db->prepare('INSERT INTO rol_permisos (id_rol, id_permiso) VALUES (?, ?)');
    $insertUser = $db->prepare('INSERT INTO usuarios (nombre, usuario, password, rol_id) VALUES (?, ?, ?, ?)');
    foreach (['lector' => ['productos.ver'], 'editor' => ['productos.ver', 'productos.editar']] as $actor => $grants) {
        $name = 'Stock de prueba ' . $actor;
        $insertRole->execute([$name]);
        $role = (int) $db->lastInsertId();
        foreach ($grants as $permission) {
            $grant->execute([$role, $permissions[$permission]]);
        }
        $insertUser->execute([$name, 'tarea6-stock-' . $actor, password_hash($password, PASSWORD_DEFAULT), $role]);
    }

    $insertCategory = $db->prepare('INSERT INTO categorias (nombre, estado) VALUES (?, ?)');
    $categories = [];
    foreach (['STK A <control>' => 1, 'STK B' => 1, 'STK Sin coincidencias' => 1, 'STK Inactiva' => 0] as $name => $active) {
        $insertCategory->execute([$name, $active]);
        $categories[$name] = (int) $db->lastInsertId();
    }
    $insertProduct = $db->prepare(
        'INSERT INTO productos (id_categoria, codigo, nombre, stock, stock_minimo, estado, precio_compra)
         VALUES (?, ?, ?, ?, ?, ?, 5)'
    );
    foreach ([
        ['STK A <control>', 'STK-01', 'STK Agotado', 0, 4, 1],
        ['STK A <control>', 'STK-02', 'STK Debajo', 2, 4, 1],
        ['STK A <control>', 'STK-03', 'STK Igual', 4, 4, 1],
        ['STK A <control>', 'STK-04', 'STK Suficiente', 5, 4, 1],
        ['STK A <control>', 'STK-05', 'STK Producto inactivo', 1, 4, 0],
        ['STK B', 'STK-06', 'STK Otra categoría', 1, 4, 1],
        ['STK Inactiva', 'STK-07', 'STK Categoría inactiva', 1, 4, 1],
    ] as [$category, $code, $name, $stock, $minimum, $active]) {
        $insertProduct->execute([$categories[$category], $code, $name, $stock, $minimum, $active]);
    }
    $db->commit();
    fwrite(STDOUT, "Fixture de stock bajo creado.\n");
} catch (Throwable) {
    if (isset($db) && $db->inTransaction()) {
        $db->rollBack();
    }
    fwrite(STDERR, "No se pudo crear el fixture de stock bajo en la instancia descartable.\n");
    exit(1);
}
