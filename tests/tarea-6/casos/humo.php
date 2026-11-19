<?php
declare(strict_types=1);

return [
    'HUMO-01' => static function (Http $http): void {
        $response = $http->request('public/dashboard.php');
        if ($response['status'] !== 302 || $response['location'] !== '/sistema_ventas/public/login.php') {
            throw new RuntimeException('El dashboard no redirigió al visitante.');
        }
    },
    'HUMO-02' => static function (Http $http, string $password): void {
        $response = $http->request('public/procesar_login.php', [
            'usuario' => 'tarea6-vendedor',
            'password' => $password,
        ]);
        if ($response['status'] !== 302 || $response['location'] !== 'dashboard.php') {
            throw new RuntimeException('El login no redirigió al dashboard.');
        }
    },
    'HUMO-03' => static function (Http $http): void {
        $response = $http->request('public/dashboard.php');
        if ($response['status'] !== 200
            || !str_contains($response['body'], 'Comercio Sintético Tarea 6')
            || !str_contains($response['body'], '0 ventas hoy')) {
            throw new RuntimeException('El dashboard no mostró los datos esperados del fixture.');
        }
    },
    'HUMO-04' => static function (Http $http): void {
        if ($http->request('views/auditoria/index.php')['status'] !== 403) {
            throw new RuntimeException('Auditoría no rechazó al vendedor.');
        }
    },
];
