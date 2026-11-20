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
    $db->exec("INSERT INTO roles (nombre) VALUES ('Auditor de prueba')");
    $role = (int) $db->lastInsertId();
    $db->exec("INSERT INTO permisos (nombre) VALUES ('auditoria.ver')");
    $permission = (int) $db->lastInsertId();
    $db->prepare('INSERT INTO rol_permisos (id_rol, id_permiso) VALUES (?, ?)')->execute([$role, $permission]);
    $db->prepare("INSERT INTO usuarios (nombre, usuario, password, rol_id)
        VALUES ('Auditor de prueba', 'tarea6-auditor', ?, ?)")->execute([
        password_hash($password, PASSWORD_DEFAULT), $role,
    ]);
    $user = (int) $db->lastInsertId();

    $today = new DateTimeImmutable('today', new DateTimeZone('America/Bogota'));
    $yesterday = $today->modify('-1 day')->format('Y-m-d');
    $insert = $db->prepare("INSERT INTO auditoria (id_usuario, modulo, accion, descripcion, fecha)
        VALUES (?, 'tarea6-auditoria', 'crear', ?, ?)");
    for ($i = 1; $i <= 52; $i++) {
        $insert->execute([$user, "tarea6-coincidencia-$i", "$yesterday 12:00:00"]);
    }
    $insert->execute([$user, 'tarea6-otro-registro', "$yesterday 13:00:00"]);
    $insert->execute([$user, 'tarea6-coincidencia-fuera-de-rango', $today->modify('-10 days')->format('Y-m-d') . ' 12:00:00']);
    $db->commit();
    fwrite(STDOUT, "Fixture de auditoría creado.\n");
} catch (Throwable) {
    if (isset($db) && $db->inTransaction()) {
        $db->rollBack();
    }
    fwrite(STDERR, "No se pudo crear el fixture de auditoría en la instancia descartable.\n");
    exit(1);
}
