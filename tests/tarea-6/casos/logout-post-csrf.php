<?php
declare(strict_types=1);

const LOGOUT_PATH = 'public/logout.php';
const DASHBOARD_PATH = 'public/dashboard.php';
const LOGIN_LOCATION = '/sistema_ventas/public/login.php';

function comprobarLogout(bool $ok): void
{
    if (!$ok) {
        throw new RuntimeException('El cierre de sesión no cumple el contrato.');
    }
}

function contarCierres(): int
{
    require_once __DIR__ . '/../soporte/database.example.php';
    $db = (new Database())->getConnection();
    return (int) $db->query(
        "SELECT COUNT(*) FROM auditoria a JOIN usuarios u USING (id_usuario)
         WHERE u.usuario = 'tarea6-vendedor' AND a.modulo = 'sesion' AND a.accion = 'logout'"
    )->fetchColumn();
}

function iniciarSesionLogout(Http $http, string $password): array
{
    $preflight = $http->request(DASHBOARD_PATH);
    comprobarLogout($preflight['status'] === 200
        || ($preflight['status'] === 302 && $preflight['location'] === LOGIN_LOCATION));

    $response = $http->request('public/procesar_login.php', [
        'usuario' => 'tarea6-vendedor',
        'password' => $password,
    ]);
    comprobarLogout($response['status'] === 302 && $response['location'] === 'dashboard.php');
    $cookies = $response['headers']['set-cookie'] ?? [];
    comprobarLogout($cookies !== []);
    // El login puede emitir la cookie inicial y luego la regenerada.
    $cookie = explode(';', $cookies[array_key_last($cookies)], 2)[0];
    comprobarLogout((bool) preg_match('/\A[^=]+=[^=]+\z/', $cookie));
    $dashboard = $http->request(DASHBOARD_PATH);
    comprobarLogout($dashboard['status'] === 200);
    // Se comprueba el formulario del menú, incluido un control nativo de envío.
    $document = new DOMDocument();
    comprobarLogout($document->loadHTML($dashboard['body'], LIBXML_NOERROR | LIBXML_NOWARNING));
    $xpath = new DOMXPath($document);
    $form = $xpath->query('//form[@action="/sistema_ventas/public/logout.php" and translate(@method,"POST","post")="post"]');
    comprobarLogout($form->length === 1);
    $token = $xpath->query('.//input[@type="hidden" and @name="csrf"]/@value', $form->item(0));
    $button = $xpath->query('.//button[@type="submit" and not(@disabled) and contains(normalize-space(.),"Cerrar sesión")]', $form->item(0));
    comprobarLogout($token->length === 1 && $token->item(0)->nodeValue !== '' && $button->length === 1);
    return ['csrf' => $token->item(0)->nodeValue, 'cookie' => $cookie];
}

function comprobarRedireccionLogout(array $response): void
{
    comprobarLogout($response['status'] === 302 && $response['location'] === 'login.php');
}

function comprobarSesionDestruida(array $response, string $cookie): void
{
    comprobarRedireccionLogout($response);
    $name = explode('=', $cookie, 2)[0];
    $expiration = false;
    foreach ($response['headers']['set-cookie'] ?? [] as $header) {
        if (str_starts_with($header, $name . '=') && preg_match('/;\s*Max-Age=0(?:;|$)/i', $header)) {
            $expiration = true;
        }
    }
    comprobarLogout($expiration);
    // El cliente nuevo no conserva ninguna otra cookie que oculte el resultado.
    $oldSession = (new Http())->request(DASHBOARD_PATH, cookie: $cookie);
    comprobarLogout($oldSession['status'] === 302 && $oldSession['location'] === LOGIN_LOCATION);
}

return [
    'LOGOUT-01' => static function (Http $http, string $password): void {
        iniciarSesionLogout($http, $password);
        $before = contarCierres();
        $anonymous = new Http();
        foreach ([$http, $anonymous] as $client) {
            foreach (['GET', 'PUT'] as $method) {
                $response = $client->request(LOGOUT_PATH, method: $method);
                comprobarLogout($response['status'] === 405 && ($response['headers']['allow'] ?? []) === ['POST']);
            }
        }
        comprobarLogout($http->request(DASHBOARD_PATH)['status'] === 200 && contarCierres() === $before);
    },
    'LOGOUT-02' => static function (Http $http): void {
        $before = contarCierres();
        comprobarLogout($http->request(LOGOUT_PATH, [])['status'] === 400);
        comprobarLogout($http->request(DASHBOARD_PATH)['status'] === 200 && contarCierres() === $before);
    },
    'LOGOUT-03' => static function (Http $http, string $password): void {
        $other = new Http();
        $otherToken = iniciarSesionLogout($other, $password)['csrf'];
        $before = contarCierres();
        foreach (['incorrecto', $otherToken, ['malformado']] as $invalid) {
            comprobarLogout($http->request(LOGOUT_PATH, ['csrf' => $invalid])['status'] === 400);
            comprobarLogout($http->request(DASHBOARD_PATH)['status'] === 200 && contarCierres() === $before);
        }
    },
    'LOGOUT-04' => static function (Http $http, string $password): void {
        ['csrf' => $token, 'cookie' => $cookie] = iniciarSesionLogout($http, $password);
        $before = contarCierres();
        comprobarSesionDestruida($http->request(LOGOUT_PATH, ['csrf' => $token]), $cookie);
        comprobarLogout(contarCierres() === $before + 1);
        comprobarRedireccionLogout($http->request(LOGOUT_PATH, ['csrf' => $token]));
        comprobarLogout(contarCierres() === $before + 1);
    },
    'LOGOUT-04-F' => static function (Http $http, string $password): void {
        ['csrf' => $token, 'cookie' => $cookie] = iniciarSesionLogout($http, $password);
        $before = contarCierres();
        $response = $http->request(LOGOUT_PATH, ['csrf' => $token], failLogoutAudit: true);
        comprobarSesionDestruida($response, $cookie);
        comprobarLogout($response['body'] === '' && contarCierres() === $before);
    },
    'LOGOUT-05' => static function (): void {
        comprobarLogout((new Http())->request(DASHBOARD_PATH)['location'] === LOGIN_LOCATION);
        $before = contarCierres();
        foreach ([[], ['csrf' => 'incorrecto']] as $form) {
            comprobarRedireccionLogout((new Http())->request(LOGOUT_PATH, $form));
        }
        comprobarLogout(contarCierres() === $before);
    },
    'LOGOUT-REG' => static function (Http $http, string $password): void {
        iniciarSesionLogout($http, $password);
        comprobarLogout($http->request('views/auditoria/index.php')['status'] === 403);
    },
];
