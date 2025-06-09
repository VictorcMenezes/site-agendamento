<?php
require_once 'enviar_whatsapp.php';

$numero = '5581985420223'; // número com DDI e DDD, sem "+"
$mensagem = "🚀 Teste de envio via UltraMsg - projeto Victor!";

$response = enviarWhatsApp($numero, $mensagem);

echo "<h3>Resposta da API:</h3>";
var_dump($response);
