<?php
session_start();

// Simula login do admin
$_SESSION['usuario_id'] = 1;
$_SESSION['nivel'] = 'admin';

// Simula cliente logado
$_SESSION['cliente'] = [
    'id' => 2,
    'nome' => 'victor',
    'contato' => '5581985420223'
];
?>

<form action="agendar_cliente_confirmar.php?debug=1" method="POST">
    <input type="hidden" name="id_funcionario" value="3">
    <input type="hidden" name="id_servico" value="1">
    <input type="hidden" name="data" value="2025-06-06">
    <input type="hidden" name="hora" value="14:00">
    <button type="submit">Testar Envio WhatsApp (modo debug)</button>
</form>
