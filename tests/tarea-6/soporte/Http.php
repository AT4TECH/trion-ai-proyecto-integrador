<?php
declare(strict_types=1);

final class Http
{
    private CurlHandle $handle;

    public function __construct()
    {
        $handle = curl_init();
        if ($handle === false) {
            throw new RuntimeException('No se pudo iniciar cURL.');
        }
        $this->handle = $handle;
        curl_setopt_array($handle, [
            CURLOPT_COOKIEFILE => '',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_CONNECTTIMEOUT => 3,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_PROXY => '',
        ]);
    }

    public function request(
        string $path,
        ?array $form = null,
        ?string $method = null,
        ?string $cookie = null,
        bool $failLogoutAudit = false
    ): array
    {
        $headers = [];
        $challenge = bin2hex(random_bytes(32));
        $requestHeaders = ['X-SistemaVentas-Challenge: ' . $challenge];
        if ($failLogoutAudit) {
            $requestHeaders[] = 'X-SistemaVentas-Test-Logout-Audit: ' . $challenge;
        }
        curl_setopt($this->handle, CURLOPT_HTTPHEADER, $requestHeaders);
        curl_setopt($this->handle, CURLOPT_COOKIE, $cookie);
        curl_setopt($this->handle, CURLOPT_URL, 'http://127.0.0.1:8765/sistema_ventas/' . $path);
        curl_setopt($this->handle, CURLOPT_HEADERFUNCTION, static function ($handle, string $line) use (&$headers): int {
            if (str_contains($line, ':')) {
                [$name, $value] = explode(':', $line, 2);
                $headers[strtolower($name)][] = trim($value);
            }
            return strlen($line);
        });
        curl_setopt($this->handle, CURLOPT_CUSTOMREQUEST, $method);
        if ($form === null) {
            curl_setopt($this->handle, CURLOPT_HTTPGET, true);
        } else {
            curl_setopt($this->handle, CURLOPT_POST, true);
            curl_setopt($this->handle, CURLOPT_POSTFIELDS, http_build_query($form));
        }

        $body = curl_exec($this->handle);
        if ($body === false) {
            throw new RuntimeException('Conexión fallida con la copia HTTP descartable.');
        }
        $expected = hash_hmac('sha256', $challenge, getenv('SV_TEST_DB_MARKER'));
        $attestations = $headers['x-sistemaventas-fixture'] ?? [];
        if (count($attestations) !== 1 || !hash_equals($expected, $attestations[0])) {
            throw new RuntimeException('El servidor no se identificó como copia descartable.');
        }
        return [
            'status' => curl_getinfo($this->handle, CURLINFO_RESPONSE_CODE),
            'location' => $headers['location'][0] ?? '',
            'body' => $body,
            'headers' => $headers,
        ];
    }
}
