<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../../login.php');
    exit();
}

$id_agendamento = $_POST['id_agendamento'] ?? null;
$id_usuario = $_SESSION['usuario_id'];

if (!$id_agendamento) {
    $_SESSION['mensagem'] = "❌ Agendamento inválido.";
    header('Location: agendar.php');
    exit();
}

try{
   $pdo = new PDO("sqlite:../../banco/banco.db");
   $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 

   //garante que o agendamento pertence ao usuario antes de excluir
   $stmt = $pdo->prepare("DELETE FROM agendamentos WHERE id = ? AND id_usuario = ?");
   $stmt->execute([$id_agendamento, $id_usuario]);

   if($stmt->rowCount() > 0) {
    $_SESSION['mensagem'] = "✅ Agendamento cancelado com sucesso.";
   }else{
    $_SESSION['mensagem'] = "⚠️ Agendamento não encontrado.";
   }

}catch(PDOException $e) {
    $_SESSION['mensagem'] = "❌ Erro ao cancelar: " . $e->getMessage();
}

header('Location: agendar.php');
exit();