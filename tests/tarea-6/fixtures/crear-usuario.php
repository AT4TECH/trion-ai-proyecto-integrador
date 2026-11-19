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
    $insert = $db->prepare(
        "INSERT INTO sistema_ventas_tarea6_test.usuarios (nombre, usuario, password, rol_id)
         VALUES ('Vendedor de prueba', 'tarea6-vendedor', :password, 2)"
    );
    $insert->execute(['password' => password_hash($password, PASSWORD_DEFAULT)]);
    fwrite(STDOUT, "Usuario sintético creado.\n");
} catch (Throwable) {
    fwrite(STDERR, "No se pudo crear el usuario sintético. Comprueba la instancia aislada, los fixtures y las variables.\n");
    exit(1);
}
