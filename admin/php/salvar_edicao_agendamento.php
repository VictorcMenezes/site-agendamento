<?php
session_start();

if (!isset($_SESSION['usuario_id']) || $_SESSION['nivel'] !== 'admin') {
    header("Location: ../../index.php");
    exit();
}

$id = $_POST['id_agendamento'] ?? null;
$id_servico = $_POST['id_servico'] ?? null;
$id_funcionario = $_POST['id_funcionario'] ?? null;
$data = $_POST['data'] ?? null;
$hora = $_POST['hora'] ?? null;

if (!$id || !$id_servico || !$id_funcionario || !$data || !$hora) {
    $_SESSION['mensagem'] = "Preencha todos os campos!";
    header("Location: listar_agendamentos.php");
    exit();
}

try {
    $pdo = new PDO("sqlite:../../banco/banco.db");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("UPDATE agendamentos SET id_servico = ?, id_funcionario = ?, data = ?, hora = ? WHERE id = ?");
    $stmt->execute([$id_servico, $id_funcionario, $data, $hora, $id]);

    $_SESSION['mensagem'] = "✅ Agendamento atualizado com sucesso!";
} catch (PDOException $e) {
    $_SESSION['mensagem'] = "❌ Erro ao atualizar: " . $e->getMessage();
}

header("Location: listar_agendamentos.php");
exit();
