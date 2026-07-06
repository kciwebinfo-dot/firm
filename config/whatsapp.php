<?php
require_once __DIR__ . '/helpers.php';

function sendWhatsAppTemplate($mobile, $templateName, $parameters = [])
{
    $token = firm('wa_meta_token');
    $phoneId = firm('wa_phone_id');
    $version = firm('wa_api_version', 'v25.0') ?: 'v25.0';

    if (!$token || !$phoneId) {
        return ['success' => false, 'error' => 'WhatsApp API settings are missing.'];
    }

    if (!function_exists('curl_init')) {
        $decoded = ['success' => false, 'error' => 'PHP cURL extension is not enabled.'];
        $line = date('Y-m-d H:i:s') . ' | ' . clean_mobile($mobile) . ' | ' . $templateName . ' | ' . json_encode($decoded) . PHP_EOL;
        @file_put_contents(__DIR__ . '/../whatsapp_log.txt', $line, FILE_APPEND);
        return $decoded;
    }

    $components = [[
        'type' => 'body',
        'parameters' => array_map(function ($value) {
            return ['type' => 'text', 'text' => (string)$value];
        }, $parameters),
    ]];

    $payload = [
        'messaging_product' => 'whatsapp',
        'to' => clean_mobile($mobile),
        'type' => 'template',
        'template' => [
            'name' => $templateName,
            'language' => ['code' => 'en'],
            'components' => $components,
        ],
    ];

    try {
        $ch = curl_init("https://graph.facebook.com/{$version}/{$phoneId}/messages");
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $token,
                'Content-Type: application/json',
            ],
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_TIMEOUT => 30,
        ]);
        $response = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);
    } catch (Throwable $e) {
        $response = false;
        $error = $e->getMessage();
    }

    $decoded = $response ? json_decode($response, true) : ['success' => false, 'error' => $error];
    $line = date('Y-m-d H:i:s') . ' | ' . clean_mobile($mobile) . ' | ' . $templateName . ' | ' . json_encode($decoded) . PHP_EOL;
    @file_put_contents(__DIR__ . '/../whatsapp_log.txt', $line, FILE_APPEND);
    return $decoded;
}
