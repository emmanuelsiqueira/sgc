<?php

$token = '7K8P1x8jsb4ZQ7KJb7xKzt26iaGMeK6au';
$numero = '5581983170608'; // No formato internacional, ex: 5511999999999
$mensagem = 'Olá! Esta é uma mensagem de teste via API do WhatsApp.';

$url = 'https://w-api.com/api/send';

$data = [
    'token' => $token,
    'to' => $numero,
    'message' => $mensagem
];

$options = [
    'http' => [
        'header'  => "Content-Type: application/json\r\n",
        'method'  => 'POST',
        'content' => json_encode($data)
    ]
];

$context  = stream_context_create($options);
$response = file_get_contents($url, false, $context);

if ($response === FALSE) {
    die('Erro ao enviar mensagem.');
}

echo "Resposta da API: " . $response;

?>