<?php
session_start();

// Verifica se o admin está logado
if (!isset($_SESSION['usuario_id']) || $_SESSION['nivel'] !== 'admin') {
    header('Location: ../../index.php');
    exit();
}

// Verifica se o cliente está salvo na sessão
$cliente = $_SESSION['cliente'] ?? null;
if (!$cliente) {
    $_SESSION['mensagem'] = "Cliente não encontrado.";
    header("Location: agendar_cliente.php");
    exit();
}

// Obtém os dados do formulário
$id_usuario = $_SESSION['cliente']['id'];
$id_funcionario = $_POST['id_funcionario'] ?? null;
$id_servico = $_POST['id_servico'] ?? null;
$data = $_POST['data'] ?? null;
$hora = $_POST['hora'] ?? null;

if (!$id_usuario || !$id_funcionario || !$id_servico || !$data || !$hora) {
    $_SESSION['mensagem'] = "❌ Dados incompletos para agendar.";
    header("Location: agendar_cliente.php");
    exit();
}

try {
    $pdo = new PDO("sqlite:../../banco/banco.db");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Verifica se já existe agendamento nesse horário
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM agendamentos WHERE data = ? AND hora = ? AND id_funcionario = ?");
    $stmt->execute([$data, $hora, $id_funcionario]);
    $ja_existe = $stmt->fetchColumn();

    if ($ja_existe) {
        $_SESSION['mensagem'] = "⚠️ Já existe um agendamento nesse horário.";
        header("Location: agendar_cliente.php");
        exit();
    }

    // Realiza o agendamento
    $stmt = $pdo->prepare("INSERT INTO agendamentos (id_usuario, id_funcionario, id_servico, data, hora) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$id_usuario, $id_funcionario, $id_servico, $data, $hora]);

    // Agora que o agendamento foi salvo, buscamos os dados para enviar WhatsApp
    $stmt = $pdo->prepare("
        SELECT 
            cliente.nome AS cliente_nome, cliente.contato AS cliente_contato,
            func.nome AS funcionario_nome, func.contato AS funcionario_contato,
            s.nome AS servico_nome
        FROM usuarios AS cliente
        JOIN usuarios AS func ON func.id = ?
        JOIN servicos AS s ON s.id = ?
        WHERE cliente.id = ?
    ");
    $stmt->execute([$id_funcionario, $id_servico, $id_usuario]);

    $dados = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($dados) {
        require_once 'enviar_whatsapp.php';

        // Mensagens
        $msg_cliente = "Olá {$dados['cliente_nome']}, seu agendamento para {$dados['servico_nome']} foi confirmado para o dia $data às $hora.";
        $msg_funcionario = "Você tem um novo agendamento: {$dados['cliente_nome']} marcou {$dados['servico_nome']} com você em $data às $hora.";
        // $msg_dono = "Novo agendamento: {$dados['cliente_nome']} com {$dados['funcionario_nome']} para {$dados['servico_nome']} em $data às $hora.";

        // Formata os contatos (remove caracteres não numéricos e adiciona o DDI)
        $cliente_contato = '+55' . preg_replace('/\D/', '', $dados['cliente_contato']);
        $funcionario_contato = '+55' . preg_replace('/\D/', '', $dados['funcionario_contato']);
        // $dono_contato = '+5581985420223'; // Substitua por seu número com DDD, por exemplo: +5581989094854

        // Envia WhatsApp
        enviarWhatsApp($cliente_contato, $msg_cliente);
        enviarWhatsApp($funcionario_contato, $msg_funcionario);
        enviarWhatsApp($dono_contato, $msg_dono);

    }

    $_SESSION['mensagem'] = "✅ Agendamento realizado com sucesso!";
} catch (PDOException $e) {
    $_SESSION['mensagem'] = "❌ Erro ao agendar: " . $e->getMessage();
}

header("Location: agendar_cliente.php");
exit();
