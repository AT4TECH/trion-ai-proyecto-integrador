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

    public function request(string $path, ?array $form = null): array
    {
        $headers = [];
        $challenge = bin2hex(random_bytes(32));
        curl_setopt($this->handle, CURLOPT_HTTPHEADER, ['X-SistemaVentas-Challenge: ' . $challenge]);
        curl_setopt($this->handle, CURLOPT_URL, 'http://127.0.0.1:8765/sistema_ventas/' . $path);
        curl_setopt($this->handle, CURLOPT_HEADERFUNCTION, static function ($handle, string $line) use (&$headers): int {
            if (str_contains($line, ':')) {
                [$name, $value] = explode(':', $line, 2);
                $headers[strtolower($name)] = trim($value);
            }
            return strlen($line);
        });
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
        if (!hash_equals($expected, $headers['x-sistemaventas-fixture'] ?? '')) {
            throw new RuntimeException('El servidor no se identificó como copia descartable.');
        }
        return [
            'status' => curl_getinfo($this->handle, CURLINFO_RESPONSE_CODE),
            'location' => $headers['location'] ?? '',
            'body' => $body,
        ];
    }
}
