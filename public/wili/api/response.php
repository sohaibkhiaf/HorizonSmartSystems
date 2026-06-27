<?php
declare(strict_types=1);

function json_response(array $payload, int $status = 200): never
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store');
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function ok(mixed $data = null): never
{
    json_response(['ok' => true, 'data' => $data]);
}

function fail(string $error, int $status = 400, mixed $details = null): never
{
    $payload = ['ok' => false, 'error' => $error];
    if ($details !== null) $payload['details'] = $details;
    json_response($payload, $status);
}

function read_json_body(): array
{
    $raw = file_get_contents('php://input') ?: '';
    if ($raw === '') return [];
    $data = json_decode($raw, true);
    return is_array($data) ? $data : [];
}

