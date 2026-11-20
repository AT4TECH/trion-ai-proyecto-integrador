<?php
declare(strict_types=1);

final class Database
{
    private ?PDO $connection = null;
    private static bool $verifiedRequest = false;

    public function getConnection(): PDO
    {
        if (self::$verifiedRequest && ($_SERVER['SCRIPT_NAME'] ?? '') === '/sistema_ventas/public/logout.php'
            && ($_SERVER['HTTP_X_SISTEMAVENTAS_TEST_LOGOUT_AUDIT'] ?? '') === ($_SERVER['HTTP_X_SISTEMAVENTAS_CHALLENGE'] ?? '')
            && isset($_SERVER['HTTP_X_SISTEMAVENTAS_TEST_LOGOUT_AUDIT'])) {
            throw new RuntimeException('Fallo de auditoría simulado por el arnés.');
        }
        if ($this->connection !== null) {
            return $this->connection;
        }
        $port = filter_var(getenv('SV_TEST_DB_PORT'), FILTER_VALIDATE_INT, [
            'options' => ['min_range' => 1024, 'max_range' => 65535],
        ]);
        $user = getenv('SV_TEST_DB_USER');
        $password = getenv('SV_TEST_DB_PASSWORD');
        if (getenv('SV_TEST_ISOLATED') !== '1' || !$port || $port === 3306
            || !$user || !$password) {
            throw new RuntimeException('Configuración descartable incompleta.');
        }
        $marker = getenv('SV_TEST_DB_MARKER');
        if (!is_string($marker) || !preg_match('/\A[a-f0-9]{64}\z/', $marker)) {
            throw new RuntimeException('Falta el marcador de la instancia descartable.');
        }
        $db = new PDO(
            "mysql:host=127.0.0.1;port={$port};dbname=sistema_ventas_tarea6_test;charset=utf8mb4",
            $user,
            $password,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_MULTI_STATEMENTS => false,
            ]
        );
        if ($db->query('SELECT DATABASE()')->fetchColumn() !== 'sistema_ventas_tarea6_test') {
            throw new RuntimeException('El esquema activo no es el de pruebas.');
        }
        if ($db->query('SELECT CURRENT_ROLE()')->fetchColumn() !== 'NONE'
            || (int) $db->query('SELECT @@GLOBAL.partial_revokes')->fetchColumn() !== 0) {
            throw new RuntimeException('No se pueden comprobar los permisos efectivos de la cuenta.');
        }
        $grants = $db->query('SHOW GRANTS FOR CURRENT_USER')->fetchAll(PDO::FETCH_COLUMN);
        $account = '(?:`[^`]+`|\x27[^\x27]+\x27)@(?:`[^`]+`|\x27[^\x27]+\x27)\z';
        $scope = '`sistema\\_ventas\\_tarea6\\_test`.*';
        $prefix = '/\AGRANT ([A-Z, ]+) ON ' . preg_quote($scope, '/') . ' TO ';
        $scoped = false;
        foreach ($grants as $grant) {
            if (preg_match('/\AGRANT USAGE ON \*\.\* TO ' . $account . '/i', $grant)) {
                continue;
            }
            if (!preg_match($prefix . $account . '/i', $grant, $matches)) {
                throw new RuntimeException('La cuenta tiene permisos fuera del esquema descartable o no verificables.');
            }
            $privileges = array_map('trim', explode(',', strtoupper($matches[1])));
            if (array_diff($privileges, ['SELECT', 'INSERT', 'UPDATE', 'CREATE', 'REFERENCES'])) {
                throw new RuntimeException('La cuenta tiene privilegios adicionales.');
            }
            $scoped = true;
        }
        if (!$scoped) {
            throw new RuntimeException('No se pudo verificar el alcance de los permisos.');
        }
        $actual = $db->query(
            'SELECT marker FROM sistema_ventas_tarea6_test.arnes_instancia WHERE id = 1'
        )->fetchColumn();
        if (!is_string($actual) || !hash_equals($marker, $actual)) {
            throw new RuntimeException('El marcador de la base no coincide.');
        }
        return $this->connection = $db;
    }

    public static function attest(string $challenge): void
    {
        (new self())->getConnection();
        self::$verifiedRequest = true;
        header('X-SistemaVentas-Fixture: ' . hash_hmac('sha256', $challenge, getenv('SV_TEST_DB_MARKER')));
    }
}

if (PHP_SAPI !== 'cli') {
    try {
        $challenge = $_SERVER['HTTP_X_SISTEMAVENTAS_CHALLENGE'] ?? '';
        if (PHP_SAPI !== 'cli-server' || !preg_match('/\A[a-f0-9]{64}\z/', $challenge)) {
            throw new RuntimeException('Solicitud fuera del arnés.');
        }
        // Se ejecuta al cargar la configuración, antes de procesar el formulario.
        Database::attest($challenge);
    } catch (Throwable) {
        http_response_code(503);
        exit('Instancia de pruebas no verificada.');
    }
}
