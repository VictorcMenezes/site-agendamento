<?php
session_start();

// Verifica se o usuário é admin
if (!isset($_SESSION['usuario_id']) || $_SESSION['nivel'] !== 'admin') {
    header("Location: ../../index.php");
    exit();
}
$id_agendamento = $_POST['id_agendamento'] ?? null;
$id_usuario = $_SESSION['usuario_id'];

if (!$id_agendamento) {
    $_SESSION['mensagem'] = "❌ Agendamento inválido.";
    header('Location: listar_agendamentos.php');
    exit();
}

try{
   $pdo = new PDO("sqlite:../../banco/banco.db");
   $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 

   //apagar independete do id do agendameto
   $stmt = $pdo->prepare("DELETE FROM agendamentos WHERE id = ?");
    $stmt->execute([$id_agendamento]);

   if($stmt->rowCount() > 0) {
    $_SESSION['mensagem'] = "✅ Agendamento cancelado com sucesso.";
   }else{
    $_SESSION['mensagem'] = "⚠️ Agendamento não encontrado.";
   }

}catch(PDOException $e) {
    $_SESSION['mensagem'] = "❌ Erro ao cancelar: " . $e->getMessage();
}

header('Location: listar_agendamentos.php');
exit();