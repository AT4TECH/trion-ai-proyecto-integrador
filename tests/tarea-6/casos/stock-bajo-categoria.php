<?php
declare(strict_types=1);

const STOCK_PATH = 'views/productos/stock.php';

function stockCheck(bool $condition): void
{
    if (!$condition) {
        throw new RuntimeException('La vista de stock bajo no cumple el contrato del caso.');
    }
}

function stockLogin(Http $http, string $user, string $password): void
{
    $preflight = $http->request(STOCK_PATH);
    stockCheck($preflight['status'] === 302 && $preflight['location'] === '/sistema_ventas/public/login.php');
    $login = $http->request('public/procesar_login.php', ['usuario' => $user, 'password' => $password]);
    stockCheck($login['status'] === 302 && $login['location'] === 'dashboard.php');
}

function stockPage(Http $http, array $query = []): DOMXPath
{
    $result = $http->request(STOCK_PATH . ($query ? '?' . http_build_query($query) : ''));
    stockCheck($result['status'] === 200
        && str_starts_with($result['headers']['content-type'][0] ?? '', 'text/html')
        && !isset($result['headers']['content-disposition']));
    $document = new DOMDocument();
    stockCheck($document->loadHTML($result['body'], LIBXML_NOERROR | LIBXML_NOWARNING));
    return new DOMXPath($document);
}

function stockCategory(DOMXPath $page, string $label): string
{
    $id = $page->evaluate('string(//select[@name="categoria"]/option[normalize-space(.)="' . $label . '"]/@value)');
    stockCheck(ctype_digit($id) && $id !== '0');
    return $id;
}

function stockSelected(DOMXPath $page): string
{
    return $page->evaluate('normalize-space(//select[@name="categoria"]/option[@selected])');
}

function stockCodes(DOMXPath $page): array
{
    $codes = [];
    foreach ($page->query('//table/tbody/tr[td[2]]/td[1]') as $cell) {
        $codes[] = trim($cell->textContent);
    }
    return $codes;
}

function stockStats(DOMXPath $page): array
{
    $values = [];
    foreach ($page->query('//strong[@class="stat-value"]') as $node) {
        $values[] = trim($node->textContent);
    }
    stockCheck(count($values) >= 3);
    return $values;
}

return [
    'STK-01' => static function (Http $http, string $password): void {
        stockLogin($http, 'tarea6-stock-lector', $password);
        $base = stockPage($http);
        stockCheck($base->evaluate('count(//form[translate(@method,"GET","get")="get"]//select[@name="categoria"])') === 1.0);
        $category = stockCategory($base, 'STK A <control>');
        $filtered = stockPage($http, ['categoria' => $category]);
        stockCheck(stockSelected($filtered) === 'STK A <control>');
        stockCheck(stockCodes($filtered) === ['STK-01', 'STK-02', 'STK-03']);
        stockCheck($filtered->evaluate('count(//select[@name="categoria"]//option/*)') === 0.0);
    },
    'STK-02' => static function (Http $http): void {
        $base = stockPage($http);
        $all = stockPage($http, ['categoria' => '']);
        stockCheck(stockSelected($all) === 'Todas');
        $codes = stockCodes($base);
        $expected = ['STK-01', 'STK-07', 'STK-02', 'STK-03', 'STK-06'];
        stockCheck(array_values(array_intersect($codes, $expected)) === $expected);
        stockCheck(!array_intersect($codes, ['STK-04', 'STK-05']));
        stockCheck($codes === stockCodes($all));
        stockCheck(stockStats($base) === stockStats($all));
        $filtered = stockPage($http, ['categoria' => stockCategory($base, 'STK A <control>')]);
        stockCheck(stockStats($base) === stockStats($filtered));
    },
    'STK-03' => static function (Http $http): void {
        require_once __DIR__ . '/../soporte/database.example.php';
        $db = (new Database())->getConnection();
        $inactive = $db->query("SELECT id_categoria FROM categorias WHERE nombre = 'STK Inactiva' AND estado = 0")->fetchColumn();
        stockCheck($inactive !== false);
        $unknown = (int) $db->query('SELECT MAX(id_categoria) FROM categorias')->fetchColumn() + 1;
        $base = stockPage($http);
        stockCheck($base->evaluate('count(//select[@name="categoria"]/option[normalize-space(.)="STK Inactiva"])') === 0.0);
        foreach (['texto', '0', '-1', '1.5', ['1'], str_repeat('9', 40), (string) $inactive, (string) $unknown] as $value) {
            $page = stockPage($http, ['categoria' => $value]);
            stockCheck(stockSelected($page) === 'Todas' && stockCodes($page) === stockCodes($base));
        }
        $page = stockPage($http, ['categoria' => stockCategory($base, 'STK Sin coincidencias')]);
        stockCheck(stockSelected($page) === 'STK Sin coincidencias' && stockCodes($page) === []);
        $empty = $page->evaluate('normalize-space(//table/tbody/tr/td[@class="empty"])');
        stockCheck(str_contains(mb_strtolower($empty), 'categoría'));
    },
    'STK-04' => static function (Http $http, string $password): void {
        $seller = new Http();
        stockLogin($seller, 'tarea6-vendedor', $password);
        stockCheck($seller->request(STOCK_PATH)['status'] === 403);

        $reader = stockPage($http);
        stockCheck($reader->evaluate('count(//table//a[normalize-space(.)="Editar"])') === 0.0);
        stockCheck($reader->evaluate('count(//span[normalize-space(.)="Inventario al costo"])') === 0.0);
        stockCheck($reader->evaluate('string(//a[normalize-space(.)="Ver todos los productos"]/@href)') === 'index.php');
        stockCheck($reader->evaluate('count(//nav[@class="paginacion"] | //a[contains(@href,"exportar=")])') === 0.0);

        $editor = new Http();
        stockLogin($editor, 'tarea6-stock-editor', $password);
        $page = stockPage($editor, ['categoria' => stockCategory($reader, 'STK A <control>')]);
        stockCheck($page->evaluate('count(//span[normalize-space(.)="Inventario al costo"])') === 1.0);
        stockCheck(stockStats($page) === stockStats(stockPage($editor)));
        $links = $page->query('//table//a[normalize-space(.)="Editar"]/@href');
        stockCheck($links->length === 3);
        foreach ($links as $link) {
            stockCheck(preg_match('/\Aeditar\.php\?id=[1-9][0-9]*\z/', $link->nodeValue) === 1);
        }
    },
];
