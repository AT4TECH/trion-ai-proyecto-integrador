<?php
declare(strict_types=1);

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    header('Allow: POST');
    http_response_code(405);
    exit;
}

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../helpers/funciones.php';

if (!isset($_SESSION['id_usuario'])) {
    redirect('login.php');
}

if (isset($_POST['csrf']) && !is_string($_POST['csrf'])) {
    http_response_code(400);
    exit;
}
verificarCsrf();

try {
    require_once __DIR__ . '/../config/database.php';
    require_once __DIR__ . '/../models/Auditoria.php';
    $auditoria = new Auditoria((new Database())->getConnection());
    $auditoria->registrar('sesion', 'logout', 'usuarios', (int) $_SESSION['id_usuario'], 'Cierre de sesión');
} catch (Throwable $e) {
    error_log('Auditoría logout: ' . $e->getMessage());
}

if (ini_get('session.use_cookies')) {
    $cookie = session_get_cookie_params();
    unset($cookie['lifetime']);
    $cookie['expires'] = time() - 3600;
    setcookie(session_name(), '', $cookie);
}

$_SESSION = [];
session_destroy();
redirect('login.php');
