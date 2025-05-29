<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../../login.php");
    exit();
}

// Obtém os dados do formulário
$id_usuario = $_SESSION['usuario_id'];
$id_funcionario = $_POST['id_funcionario'] ?? null;
$id_servico = $_POST['id_servico'] ?? null;
$data = $_POST['data'] ?? null;
$hora = $_POST['hora'] ?? null;

if (!$id_usuario || !$id_funcionario || !$id_servico || !$data || !$hora) {
    $_SESSION['mensagem'] = "❌ Dados incompletos para agendar.";
    header("Location: agendar.php");
    exit();
}

try{
    $pdo = new PDO("sqlite:../../banco/banco.db");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Verifica se já existe agendamento nesse horário
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM agendamentos WHERE data = ? AND hora= ? AND id_funcionario = ?");
    $stmt->execute([$data, $hora, $id_funcionario]);
    $ja_existe = $stmt->fetchColumn();

    if ($ja_existe) {
        $_SESSION['mensagem'] = "⚠️ Já existe um agendamento nesse horário.";
        header("Location: agendar.php");
        exit();
    }

    $stmt = $pdo->prepare("INSERT INTO agendamentos (id_usuario, id_funcionario, id_servico, data, hora) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$id_usuario, $id_funcionario, $id_servico, $data, $hora]);

    $_SESSION['mensagem'] = "✅ Agendamento realizado com sucesso!";
} catch (PDOException $e) {
    $_SESSION['mensagem'] = "❌ Erro ao agendar: " . $e->getMessage();
}
header("Location: agendar.php");
exit();