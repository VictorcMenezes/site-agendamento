<?php
function limparNumero($numero) {
    return preg_replace('/[^0-9]/', '', $numero); // remove tudo que não for número
}

function enviarWhatsApp($numero, $mensagem) {
    $numero = limparNumero($numero);
    if (strlen($numero) < 12) {
        file_put_contents("log_envio.txt", date("Y-m-d H:i:s") . " | Número inválido: $numero\n", FILE_APPEND);
        return false;
    }

    $instanceId = 'instance122645';
    $token = 'pchexz6z9hki4s6o';

    $url = "https://api.ultramsg.com/{$instanceId}/messages/chat";

    $data = [
        "token" => $token,
        "to" => $numero,
        "body" => $mensagem
    ];

    $json = json_encode($data);

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    $error = curl_error($ch);
    curl_close($ch);

    file_put_contents("log_envio.txt", date("Y-m-d H:i:s") . " | Enviado para $numero | Mensagem: $mensagem | Resposta: $response | Erro: $error\n", FILE_APPEND);

    if ($error) {
        return "Erro cURL: " . $error;
    }

    return $response;
}
