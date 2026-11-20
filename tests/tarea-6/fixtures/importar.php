<?php
declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require __DIR__ . '/../soporte/database.example.php';

try {
    $statements = [];
    foreach (['esquema.sql' => 'CREATE TABLE', 'datos-sinteticos.sql' => 'INSERT INTO'] as $file => $verb) {
        $sql = file_get_contents(__DIR__ . '/' . $file);
        if ($sql === false) {
            throw new RuntimeException('Fixture ilegible.');
        }
        $sql = preg_replace('/^--[^\r\n]*/m', '', $sql);
        foreach (array_filter(array_map('trim', explode(';', $sql))) as $statement) {
            if (!preg_match('/\A' . $verb . ' sistema_ventas_tarea6_test\.[a-z_]+\s*\(/', $statement)) {
                throw new RuntimeException('Destino o instrucción fuera del contrato del fixture.');
            }
            $statements[] = $statement;
        }
    }

    $db = (new Database())->getConnection();
    $tables = $db->query('SHOW TABLES FROM sistema_ventas_tarea6_test')->fetchAll(PDO::FETCH_COLUMN);
    if ($tables !== ['arnes_instancia']) {
        throw new RuntimeException('El esquema debe contener únicamente el marcador.');
    }
    foreach ($statements as $statement) {
        $db->exec($statement);
    }
    fwrite(STDOUT, "Fixtures cargados en el esquema descartable.\n");
} catch (Throwable) {
    fwrite(STDERR, "Importación detenida. Comprueba el marcador, los permisos y el esquema vacío.\n");
    exit(1);
}
