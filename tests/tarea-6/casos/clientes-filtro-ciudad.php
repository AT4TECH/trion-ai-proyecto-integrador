<?php
declare(strict_types=1);

const CITY_CLIENTS_PATH = 'views/clientes/index.php';
const CITY_CLIENTS_LOGIN = '/sistema_ventas/public/login.php';

function cityClientsCheck(bool $condition): void
{
    if (!$condition) {
        throw new RuntimeException('El listado de clientes no cumple el criterio.');
    }
}

function cityClientsLogin(Http $http, string $username, string $password): void
{
    $initial = $http->request(CITY_CLIENTS_PATH);
    cityClientsCheck($initial['status'] === 302 && $initial['location'] === CITY_CLIENTS_LOGIN);
    $response = $http->request('public/procesar_login.php', ['usuario' => $username, 'password' => $password]);
    cityClientsCheck($response['status'] === 302 && $response['location'] === 'dashboard.php');
}

function cityClientsPage(Http $http, array $filters = []): DOMXPath
{
    $path = CITY_CLIENTS_PATH;
    if ($filters) {
        $path .= '?' . http_build_query($filters);
    }
    $response = $http->request($path);
    cityClientsCheck($response['status'] === 200);
    $document = new DOMDocument();
    cityClientsCheck($document->loadHTML($response['body'], LIBXML_NOERROR | LIBXML_NOWARNING));
    return new DOMXPath($document);
}

function cityClientsDocuments(DOMXPath $page): array
{
    $documents = [];
    foreach ($page->query('//table/tbody/tr/td[2]') as $cell) {
        $documents[] = trim($cell->textContent);
    }
    return $documents;
}

function cityClientsNavigate(Http $http, DOMXPath $page, string $label, array $expected): DOMXPath
{
    $links = $page->query('//nav//a[contains(normalize-space(.), "' . $label . '")]/@href');
    cityClientsCheck($links->length === 1);
    $query = parse_url($links->item(0)->nodeValue, PHP_URL_QUERY);
    cityClientsCheck(is_string($query));
    parse_str($query, $params);
    cityClientsCheck($params == $expected);
    return cityClientsPage($http, $params);
}

return [
    'CLI-01' => static function (Http $http, string $password): void {
        cityClientsLogin($http, 'tarea6-clientes', $password);
        $page = cityClientsPage($http, ['ciudad' => '  Bogotá  ', 'q' => 'Tarea6 Grupo', 'estado' => '']);
        cityClientsCheck($page->evaluate('string(//input[@name="ciudad"]/@value)') === 'Bogotá');
        cityClientsCheck(cityClientsDocuments($page) === array_map(
            static fn (int $n): string => sprintf('CLI-P-%02d', $n),
            range(1, 50)
        ));
        $longCity = str_repeat('Á', 100);
        $long = cityClientsPage($http, ['ciudad' => $longCity . 'X']);
        cityClientsCheck($long->evaluate('string(//input[@name="ciudad"]/@value)') === $longCity);
        cityClientsCheck(cityClientsDocuments($long) === ['CLI-LARGA']);
        $prefix = cityClientsPage($http, ['ciudad' => 'Bogot']);
        cityClientsCheck(cityClientsDocuments($prefix) === []);
    },
    'CLI-02' => static function (Http $http): void {
        $group = ['ciudad' => 'Bogotá', 'q' => 'Tarea6 Grupo'];
        foreach (['1' => 51, '0' => 1, '' => 52] as $state => $total) {
            $page = cityClientsPage($http, $group + ['estado' => (string) $state]);
            cityClientsCheck($page->evaluate('normalize-space(//p[@class="resumen-filtro"])') === "$total clientes");
            if ($total === 1) {
                cityClientsCheck(cityClientsDocuments($page) === ['CLI-P-52']);
            }
        }
        $empty = cityClientsPage($http, ['ciudad' => 'Ciudad sin coincidencias']);
        cityClientsCheck($empty->evaluate('count(//tbody//td[@class="empty"])') === 1.0);
        cityClientsCheck($empty->evaluate('count(//nav[@class="paginacion"]//a)') === 0.0);
        foreach (['Tarea6 Compras', 'CLI-COMPRA', '6010000001', 'tarea6-compras@example.test'] as $search) {
            cityClientsCheck(cityClientsDocuments(cityClientsPage($http, [
                'ciudad' => 'Bogotá', 'q' => $search,
            ])) === ['CLI-COMPRA']);
        }
    },
    'CLI-03' => static function (Http $http): void {
        $filters = ['ciudad' => 'Bogotá', 'q' => 'Tarea6 Grupo', 'estado' => ''];
        $first = cityClientsPage($http, $filters);
        $firstDocuments = cityClientsDocuments($first);
        $second = cityClientsNavigate($http, $first, 'Siguiente', $filters + ['pagina' => '2']);
        $secondDocuments = cityClientsDocuments($second);
        cityClientsCheck($secondDocuments === ['CLI-P-51', 'CLI-P-52']);
        cityClientsCheck(count($firstDocuments) === 50 && !array_intersect($firstDocuments, $secondDocuments));
        cityClientsCheck(cityClientsDocuments(cityClientsNavigate($http, $second, 'Anterior', $filters + [
            'pagina' => '1',
        ])) === $firstDocuments);
        foreach (['0', '-1', '2.5', 'abc', ['2']] as $invalid) {
            cityClientsCheck(cityClientsDocuments(cityClientsPage($http, $filters + ['pagina' => $invalid])) === $firstDocuments);
        }
        foreach (['999', str_repeat('9', 40)] as $outOfRange) {
            cityClientsCheck(cityClientsDocuments(cityClientsPage($http, $filters + ['pagina' => $outOfRange])) === $secondDocuments);
        }
    },
    'CLI-04' => static function (Http $http): void {
        $filters = ['q' => 'CLI-VACIA', 'estado' => ''];
        $page = cityClientsPage($http, ['ciudad' => 'Bogotá', 'pagina' => '2']);
        $form = '//form[.//input[@name="ciudad"] and translate(@method,"GET","get")="get"]';
        cityClientsCheck($page->evaluate('count(' . $form . ')') === 1.0);
        cityClientsCheck($page->evaluate('count(' . $form . '//*[@name="pagina"])') === 0.0);
        foreach ([[], ['ciudad' => ''], ['ciudad' => '  '], ['ciudad' => ['Bogotá']]] as $city) {
            $reset = cityClientsPage($http, $filters + $city);
            cityClientsCheck(cityClientsDocuments($reset) === ['CLI-VACIA']);
            cityClientsCheck($reset->evaluate('string(//input[@name="ciudad"]/@value)') === '');
            cityClientsCheck($reset->evaluate('string(//input[@name="q"]/@value)') === $filters['q']);
            cityClientsCheck($reset->evaluate('count(//select[@name="estado"]/option[@value="" and @selected])') === 1.0);
        }
        $base = cityClientsPage($http);
        cityClientsCheck($base->evaluate('string(//input[@name="q"]/@value)') === '');
        cityClientsCheck($base->evaluate('string(//input[@name="ciudad"]/@value)') === '');
        cityClientsCheck($base->evaluate('count(//select[@name="estado"]/option[@value="1" and @selected])') === 1.0);
        cityClientsCheck(!in_array('CLI-P-52', cityClientsDocuments($base), true));
    },
    'CLI-05' => static function (Http $http, string $password): void {
        $summary = cityClientsPage($http, ['q' => 'CLI-COMPRA']);
        cityClientsCheck(cityClientsDocuments($summary) === ['CLI-COMPRA']);
        cityClientsCheck(str_starts_with($summary->evaluate('normalize-space(//tbody/tr/td[5])'), '1'));
        cityClientsCheck(str_contains($summary->evaluate('normalize-space(//tbody/tr/td[5])'), '125'));
        cityClientsCheck($summary->evaluate('normalize-space(//tbody/tr/td[6])') === '02/01/2020');
        $withoutPurchases = cityClientsPage($http, ['q' => 'CLI-NULA']);
        cityClientsCheck(cityClientsDocuments($withoutPurchases) === ['CLI-NULA']);
        cityClientsCheck($withoutPurchases->evaluate('normalize-space(//tbody/tr/td[5])') === '0');
        cityClientsCheck($http->request(CITY_CLIENTS_PATH, [])['status'] === 403);
        $seller = new Http();
        cityClientsLogin($seller, 'tarea6-vendedor', $password);
        cityClientsCheck($seller->request(CITY_CLIENTS_PATH)['status'] === 403);
    },
];
