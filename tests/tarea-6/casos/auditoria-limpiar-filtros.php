<?php
declare(strict_types=1);

const AUDIT_PATH = 'views/auditoria/index.php';
const AUDIT_URL = '/sistema_ventas/' . AUDIT_PATH;
const AUDIT_LOGIN_URL = '/sistema_ventas/public/login.php';

function auditCheck(bool $condition): void
{
    if (!$condition) {
        throw new RuntimeException('La vista de auditoría no cumple el contrato del caso.');
    }
}

function auditLogin(Http $http, string $username, string $password): void
{
    $preflight = $http->request(AUDIT_PATH);
    auditCheck($preflight['status'] === 302 && $preflight['location'] === AUDIT_LOGIN_URL);
    $login = $http->request('public/procesar_login.php', ['usuario' => $username, 'password' => $password]);
    auditCheck($login['status'] === 302 && $login['location'] === 'dashboard.php');
}

function auditPage(Http $http, string $path): DOMXPath
{
    $response = $http->request($path);
    auditCheck($response['status'] === 200
        && str_starts_with($response['headers']['content-type'][0] ?? '', 'text/html')
        && !isset($response['headers']['content-disposition']));
    $document = new DOMDocument();
    auditCheck($document->loadHTML($response['body'], LIBXML_NOERROR | LIBXML_NOWARNING));
    return new DOMXPath($document);
}

function auditLink(DOMXPath $page, string $label): string
{
    $nodes = $page->query('//a[contains(normalize-space(.), "' . $label . '")]/@href');
    auditCheck($nodes->length === 1);
    return $nodes->item(0)->nodeValue;
}

function auditReset(Http $http, DOMXPath $page): DOMXPath
{
    auditCheck(auditLink($page, 'Limpiar filtros') === AUDIT_URL);
    return auditPage($http, AUDIT_PATH);
}

function auditToday(): DateTimeImmutable
{
    return new DateTimeImmutable('today', new DateTimeZone('America/Bogota'));
}

function auditFilters(DOMXPath $page): array
{
    $id = $page->evaluate('string(//select[@name="usuario"]/option[normalize-space(.)="Auditor de prueba"]/@value)');
    auditCheck(ctype_digit($id));
    $yesterday = auditToday()->modify('-1 day')->format('Y-m-d');
    return [
        'desde' => $yesterday, 'hasta' => $yesterday, 'usuario' => $id,
        'modulo' => 'tarea6-auditoria', 'accion' => 'crear', 'q' => 'tarea6-coincidencia',
    ];
}

function auditFirstPage(DOMXPath $page): void
{
    auditCheck(str_contains($page->evaluate('string(//nav[@class="paginacion"])'), 'página 1 de'));
}

function auditDefaults(DOMXPath $page): void
{
    auditFirstPage($page);
    auditCheck($page->evaluate('string(//input[@name="desde"]/@value)') === auditToday()->modify('-7 days')->format('Y-m-d'));
    auditCheck($page->evaluate('string(//input[@name="hasta"]/@value)') === auditToday()->format('Y-m-d'));
    auditCheck($page->evaluate('string(//input[@name="q"]/@value)') === '');
    foreach (['usuario' => 'Todos', 'modulo' => 'Todos', 'accion' => 'Todas'] as $name => $label) {
        $select = '//select[@name="' . $name . '"]';
        auditCheck($page->evaluate('normalize-space(' . $select . '/option[1])') === $label);
        auditCheck($page->evaluate('count(' . $select . '/option[@selected and @value!=""])') === 0.0);
    }
}

function auditQuery(string $href, array $expected): string
{
    auditCheck(str_starts_with($href, '?'));
    parse_str(substr($href, 1), $query);
    auditCheck($query == $expected);
    return AUDIT_PATH . $href;
}

return [
    'AUD-01' => static function (Http $http, string $password): void {
        auditLogin($http, 'tarea6-auditor', $password);
        $direct = auditPage($http, AUDIT_PATH);
        $filters = auditFilters($direct);
        $first = auditPage($http, AUDIT_PATH . '?' . http_build_query($filters));
        $second = auditPage($http, auditQuery(auditLink($first, 'Siguiente'), $filters + ['pagina' => '2']));
        auditCheck(str_contains($second->evaluate('string(//nav[@class="paginacion"])'), 'página 2 de'));
        $reset = auditReset($http, $second);
        auditDefaults($reset);
        foreach (['string(//table/tbody)', 'string(//div[contains(@class, "stats-compactas")])'] as $path) {
            auditCheck($reset->evaluate($path) === $direct->evaluate($path));
        }
    },
    'AUD-02' => static function (Http $http): void {
        $empty = auditPage($http, AUDIT_PATH . '?q=tarea6-sin-coincidencias&pagina=9');
        auditCheck($empty->evaluate('count(//tbody//td[@class="empty"])') === 1.0);
        $reset = auditReset($http, $empty);
        auditDefaults($reset);
        auditDefaults(auditReset($http, $reset));
    },
    'AUD-03' => static function (Http $http): void {
        $clean = auditPage($http, AUDIT_PATH);
        auditCheck($clean->evaluate('count(//form[@class="filtros card" and @method="get"]//button[normalize-space(.)="Filtrar"])') === 1.0);
        auditCheck($clean->evaluate('count(//form[@class="filtros card"]//*[@name="pagina" or @name="exportar"])') === 0.0);
        $filters = auditFilters($clean);
        $first = auditPage($http, AUDIT_PATH . '?' . http_build_query($filters));
        auditFirstPage($first);
        auditCheck($first->evaluate('count(//tbody/tr)') === 50.0);
        $second = auditPage($http, auditQuery(auditLink($first, 'Siguiente'), $filters + ['pagina' => '2']));
        auditCheck($second->evaluate('count(//tbody/tr)') === 2.0);
        auditFirstPage(auditPage($http, auditQuery(auditLink($second, 'Anterior'), $filters + ['pagina' => '1'])));

        $export = auditQuery(auditLink($second, 'Exportar a Excel (CSV)'), $filters + ['exportar' => 'csv']);
        $response = $http->request($export);
        auditCheck($response['status'] === 200
            && str_starts_with($response['headers']['content-type'][0] ?? '', 'text/csv')
            && str_starts_with($response['headers']['content-disposition'][0] ?? '', 'attachment;'));
        $rows = preg_split('/\r?\n/', trim($response['body']));
        auditCheck(count($rows) === 53);
        $descriptions = [];
        foreach (array_slice($rows, 1) as $row) {
            $fields = str_getcsv($row, ';', '"', '');
            auditCheck(count($fields) === 8 && preg_match('/\Atarea6-coincidencia-\d+\z/', $fields[6]) === 1);
            $descriptions[] = $fields[6];
        }
        auditCheck(count(array_unique($descriptions)) === 52);
    },
    'AUD-04' => static function (Http $http, string $password): void {
        $vendedor = new Http();
        auditLogin($vendedor, 'tarea6-vendedor', $password);
        auditCheck($vendedor->request(AUDIT_PATH)['status'] === 403);
    },
];
