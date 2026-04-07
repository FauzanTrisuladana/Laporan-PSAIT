<?php

declare(strict_types=1);

// $API_BASE_URL = 'http://103.139.192.46/a1/6/new/';
$API_BASE_URL = 'http://10.33.35.96/new/';

function api_base_url(): string
{
    global $API_BASE_URL;

    return rtrim((string) $API_BASE_URL, "/") . "/";
}

function normalize_resource(?string $value): string
{
    $r = strtolower(trim((string) $value));
    return in_array($r, ['koperasi', 'anggota'], true) ? $r : '';
}

function resource_id_key(string $resource): string
{
    return $resource === 'koperasi' ? 'id_koperasi' : 'id_anggota';
}

function api_build_url(string $resource, ?int $id = null): string
{
    $resource = normalize_resource($resource);
    if ($resource === '') {
        return api_base_url();
    }

    $query = ['tabel' => $resource];

    if ($id !== null) {
        $query[resource_id_key($resource)] = (string) $id;
    }

    return api_base_url() . '?' . http_build_query($query);
}

function h(string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function api_get_json(string $url): array
{
    $context = stream_context_create([
        'http' => [
            'method' => 'GET',
            'timeout' => 10,
            'header' => "Accept: application/json\r\nUser-Agent: fe_new/1.0\r\n",
        ],
    ]);

    $json = @file_get_contents($url, false, $context);
    if ($json === false || trim($json) === '') {
        return [
            'status' => 0,
            'message' => 'Gagal mengakses API. Pastikan API aktif & URL di config.php benar.',
        ];
    }

    $decoded = json_decode($json, true);
    if (!is_array($decoded)) {
        return [
            'status' => 0,
            'message' => 'Response API bukan JSON.',
            'raw' => $json,
        ];
    }

    return $decoded;
}

function api_request(string $method, string $url, ?array $body = null): array
{
    $ch = curl_init($url);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);

    $headers = [
        'Accept: application/json',
    ];

    if ($body !== null) {
        $payload = json_encode($body);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        $headers[] = 'Content-Type: application/json';
    }

    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $response = curl_exec($ch);
    $curlError = curl_error($ch);
    $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);

    curl_close($ch);

    if ($response === false) {
        return [
            'status' => 0,
            'message' => 'Request gagal: ' . ($curlError !== '' ? $curlError : 'unknown error'),
            '_http_code' => $httpCode,
        ];
    }

    $decoded = json_decode($response, true);
    if (!is_array($decoded)) {
        return [
            'status' => 0,
            'message' => 'Response API bukan JSON.',
            'raw' => $response,
            '_http_code' => $httpCode,
        ];
    }

    $decoded['_http_code'] = $httpCode;
    return $decoded;
}

function swal_and_redirect(string $title, string $text, string $icon, string $redirectTo): void
{
    echo "<script>\n";
    echo "Swal.fire({";
    echo "title: " . json_encode($title) . ",";
    echo "text: " . json_encode($text) . ",";
    echo "icon: " . json_encode($icon) . ",";
    echo "confirmButtonText: 'OK'";
    echo "}).then((result) => { if (result.isConfirmed) { window.location.href = " . json_encode($redirectTo) . "; } });\n";
    echo "</script>\n";
}
