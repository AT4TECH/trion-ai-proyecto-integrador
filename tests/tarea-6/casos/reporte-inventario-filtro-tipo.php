<?php
declare(strict_types=1);

const INV_REPORT_PATH = 'views/reportes/inventario.php';
const INV_REPORT_DATES = ['desde' => '2022-07-07', 'hasta' => '2022-07-08'];
const INV_REPORT_TYPES = ['entrada', 'salida', 'ajuste', 'devolucion'];
const INV_REPORT_HEADERS = ['Fecha', 'Código', 'Producto', 'Tipo', 'Stock anterior', 'Stock nuevo', 'Motivo', 'Referencia', 'Usuario'];

function invReportCheck(bool $condition): void
{
    if (!$condition) {
        throw new RuntimeException('El reporte de inventario no cumple el contrato del caso.');
    }
}

function invReportLogin(Http $http, string $user, string $password): void
{
    $preflight = $http->request(INV_REPORT_PATH . '?exportar=csv');
    invReportCheck($preflight['status'] === 302 && $preflight['location'] === '/sistema_ventas/public/login.php');
    invReportCheck(!isset($preflight['headers']['content-disposition']));
    $login = $http->request('public/procesar_login.php', ['usuario' => $user, 'password' => $password]);
    invReportCheck($login['status'] === 302 && $login['location'] === 'dashboard.php');
}

function invReportCsv(Http $http, array $query = []): array
{
    $result = $http->request(INV_REPORT_PATH . '?' . http_build_query($query + INV_REPORT_DATES + ['exportar' => 'csv']));
    invReportCheck($result['status'] === 200 && $result['headers']['content-type'] === ['text/csv; charset=utf-8']);
    $dates = [$query['desde'] ?? INV_REPORT_DATES['desde'], $query['hasta'] ?? INV_REPORT_DATES['hasta']];
    sort($dates);
    invReportCheck($result['headers']['content-disposition'] === ['attachment; filename="movimientos_inventario_' . implode('_a_', $dates) . '.csv"']);
    invReportCheck(str_starts_with($result['body'], "\xEF\xBB\xBF"));
    $stream = fopen('php://temp', 'w+b');
    fwrite($stream, substr($result['body'], 3));
    rewind($stream);
    $rows = [];
    try {
        invReportCheck(fgetcsv($stream, 0, ';', '"', '') === INV_REPORT_HEADERS);
        while (($row = fgetcsv($stream, 0, ';', '"', '')) !== false) {
            invReportCheck(count($row) === 9);
            $rows[] = $row;
        }
    } finally {
        fclose($stream);
    }
    return $rows;
}

function invReportPage(Http $http, string $path, array $query = []): DOMXPath
{
    $result = $http->request($path . '?' . http_build_query($query + INV_REPORT_DATES));
    invReportCheck($result['status'] === 200 && !isset($result['headers']['content-disposition']));
    $doc = new DOMDocument();
    invReportCheck($doc->loadHTML($result['body'], LIBXML_NOERROR | LIBXML_NOWARNING));
    return new DOMXPath($doc);
}

function invReportLinkQuery(DOMNode $link): array
{
    parse_str((string) parse_url($link->nodeValue, PHP_URL_QUERY), $query);
    return $query;
}

return [
    'INV-01' => static function (Http $http, string $password): void {
        invReportLogin($http, 'tarea6-inventario-reporte', $password);
        $rows = invReportCsv($http);
        invReportCheck(array_column($rows, 0) === [
            '2022-07-07 00:00:00', '2022-07-07 14:30:00', '2022-07-08 01:00:00', '2022-07-08 23:59:59',
        ]);
        invReportCheck(array_column($rows, 3) === INV_REPORT_TYPES);
        invReportCheck(invReportCsv($http, ['tipo' => '']) === $rows);
        invReportCheck(invReportCsv($http, ['desde' => '2022-07-08', 'hasta' => '2022-07-07']) === $rows);
    },
    'INV-02' => static function (Http $http): void {
        foreach (INV_REPORT_TYPES as $type) {
            invReportCheck(array_column(invReportCsv($http, ['tipo' => $type]), 3) === [$type]);
        }
        invReportCheck(invReportCsv($http, ['tipo' => 'entrada', 'desde' => '2022-07-08']) === []);
        $page = invReportPage($http, INV_REPORT_PATH, ['tipo' => 'entrada']);
        invReportCheck($page->evaluate('string(//form[@method="get"]//select[@name="tipo"]/option[@selected]/@value)') === 'entrada');
        foreach (INV_REPORT_DATES as $key => $date) {
            invReportCheck($page->evaluate('string(//form[@method="get"][.//select[@name="tipo"]]//input[@name="' . $key . '"]/@value)') === $date);
        }
        $shortcuts = $page->query('//div[contains(@class,"atajos")]//a/@href');
        invReportCheck($shortcuts->length > 0);
        foreach ($shortcuts as $link) {
            $query = invReportLinkQuery($link);
            invReportCheck(($query['tipo'] ?? '') === 'entrada' && isset($query['desde'], $query['hasta']));
        }
        $links = $page->query('//a[contains(@href,"exportar=csv")]/@href');
        invReportCheck($links->length === 1 && invReportLinkQuery($links->item(0)) == INV_REPORT_DATES + [
            'tipo' => 'entrada', 'exportar' => 'csv',
        ]);
    },
    'INV-03' => static function (Http $http): void {
        foreach (['no-existe', 'ENTRADA', ' entrada', ['entrada']] as $value) {
            $result = $http->request(INV_REPORT_PATH . '?' . http_build_query(INV_REPORT_DATES + [
                'exportar' => 'csv', 'tipo' => $value,
            ]));
            invReportCheck($result['status'] === 400 && !isset($result['headers']['content-disposition']));
            invReportCheck(!str_starts_with($result['headers']['content-type'][0] ?? '', 'text/csv'));
            invReportCheck(!str_contains($result['body'], INV_REPORT_HEADERS[0] . ';'));
            if (is_string($value)) {
                invReportCheck(!str_contains($result['body'], $value));
            }
        }
    },
    'INV-04' => static function (Http $http): void {
        $rows = invReportCsv($http);
        invReportCheck($rows[0][2] === 'Artículo de inventario');
        invReportCheck($rows[0][4] === "'-1,25" && $rows[0][5] === '1,5');
        invReportCheck($rows[0][6] === "Recepción; \"revisión\"\nsegunda línea");
        invReportCheck($rows[0][7] === "' \t=1+1");
        invReportCheck($rows[1][6] === "'=1+1");
    },
    'INV-05' => static function (Http $http, string $password): void {
        $seller = new Http();
        invReportLogin($seller, 'tarea6-vendedor', $password);
        $denied = $seller->request(INV_REPORT_PATH . '?exportar=csv&tipo=ajuste');
        invReportCheck($denied['status'] === 403 && !isset($denied['headers']['content-disposition']));
        invReportCheck($seller->request(INV_REPORT_PATH)['status'] === 403);
        invReportCheck($seller->request(INV_REPORT_PATH . '?exportar=csv&tipo=incorrecto')['status'] === 403);
        invReportCheck(count(invReportCsv($http, ['tipo' => 'ajuste'])) === 1);

        $base = invReportPage($http, INV_REPORT_PATH);
        $filtered = invReportPage($http, INV_REPORT_PATH, ['tipo' => 'ajuste']);
        foreach (['string(//div[@class="stats"])', 'string(//div[@class="reporte-2col"])'] as $expression) {
            $contents = trim($base->evaluate($expression));
            invReportCheck($contents !== '' && $contents === trim($filtered->evaluate($expression)));
        }
        $kardex = $filtered->query('//a[contains(@href,"../inventario/index.php")]/@href');
        invReportCheck($kardex->length === 1 && invReportLinkQuery($kardex->item(0)) == INV_REPORT_DATES);
        $tab = $filtered->query('//nav[@class="tabs"]/a[starts-with(@href,"productos.php")]/@href');
        invReportCheck($tab->length === 1 && invReportLinkQuery($tab->item(0)) == INV_REPORT_DATES);

        $otherPath = 'views/reportes/' . $tab->item(0)->nodeValue;
        $otherPage = invReportPage($http, 'views/reportes/productos.php', ['tipo' => 'ajuste']);
        invReportCheck($otherPage->evaluate('count(//select[@name="tipo"])') === 0.0);
        $otherLinks = $otherPage->query('//a[contains(@href,"exportar=csv")]/@href');
        invReportCheck($otherLinks->length === 1 && invReportLinkQuery($otherLinks->item(0)) == INV_REPORT_DATES + [
            'exportar' => 'csv',
        ]);
        $other = $http->request($otherPath . '&exportar=csv');
        invReportCheck($other['status'] === 200 && str_starts_with($other['headers']['content-disposition'][0] ?? '', 'attachment; filename="productos_'));
        invReportCheck($other['body'] === $http->request($otherPath . '&exportar=csv&tipo=ajuste')['body']);
    },
];
