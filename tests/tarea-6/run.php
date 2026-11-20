<?php
declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require __DIR__ . '/soporte/Http.php';

$name = $argv[1] ?? '';
$checkOnly = ($argv[2] ?? '') === '--check-prerequisites';
if (!preg_match('/\A[a-z0-9]+(?:-[a-z0-9]+)*\z/', $name)
    || count($argv) > 3 || (isset($argv[2]) && !$checkOnly)
    || !is_file(__DIR__ . "/casos/{$name}.php")) {
    fwrite(STDERR, "Uso: php tests/tarea-6/run.php <caso> [--check-prerequisites].\n");
    exit(2);
}
$password = getenv('SV_TEST_LOGIN_PASSWORD');
$marker = getenv('SV_TEST_DB_MARKER');
$assertions = require __DIR__ . "/casos/{$name}.php";
$missing = [];
if (!extension_loaded('curl') || getenv('SV_TEST_ISOLATED') !== '1'
    || $password === false || strlen($password) < 12 || $marker === false
    || !preg_match('/\A[a-f0-9]{64}\z/', $marker)) {
    $missing[] = 'Comprueba cURL, SV_TEST_ISOLATED, SV_TEST_LOGIN_PASSWORD y SV_TEST_DB_MARKER.';
}
if ($name === 'logout-post-csrf' && (!extension_loaded('dom') || !extension_loaded('pdo_mysql') || !extension_loaded('mbstring')
    || !filter_var(getenv('SV_TEST_DB_PORT'), FILTER_VALIDATE_INT, [
        'options' => ['min_range' => 1024, 'max_range' => 65535],
    ]) || (int) getenv('SV_TEST_DB_PORT') === 3306 || !getenv('SV_TEST_DB_USER') || !getenv('SV_TEST_DB_PASSWORD'))) {
    $missing[] = 'Para cierre de sesión, comprueba DOM, PDO MySQL, mbstring, puerto aislado y cuenta descartable.';
}
if ($checkOnly || $missing) {
    $reason = $missing ? implode(' ', $missing) : 'Modo de prerrequisitos locales, sin solicitudes HTTP.';
    foreach (array_keys($assertions) as $id) {
        fwrite(STDOUT, "SKIP {$id}: {$reason}\n");
    }
    exit($missing ? 3 : 0);
}

$http = null;
$failed = null;
foreach ($assertions as $id => $assertion) {
    if ($failed !== null) {
        fwrite(STDOUT, "SKIP {$id}: bloqueada por {$failed}.\n");
        continue;
    }
    try {
        $http ??= new Http();
        $assertion($http, $password);
        fwrite(STDOUT, "PASS {$id}\n");
    } catch (Throwable) {
        fwrite(STDERR, "FAIL {$id}: respuesta, aislamiento o aserción incorrectos.\n");
        $failed = $id;
    }
}
exit($failed === null ? 0 : 1);
