<?php
    session_start();
    //verifica se o usario está logado é admin
    if(!isset($_SESSION['usuario_id']) || $_SESSION['nivel'] !== 'admin'){
        // Redireciona para a página inicial (ou página de login)
        header('Location: ../../index.php');
        exit();
    }
    
    $identificador = trim($_POST['identificador'] ??'');

    if ($identificador === '') {
        $_SESSION['mensagem'] = "⚠️ Informe um email ou telefone.";
        header('Location: agendar_cliente.php');
        exit();
    }

    try {
        $pdo = new PDO("sqlite:../../banco/banco.db");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ? OR contato = ?");
        $stmt->execute([$identificador, $identificador]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario){
            $_SESSION['cliente'] = $usuario;
            header("Location: agendar_cliente_servico.php");
            exit();
        } else {
        $_SESSION['mensagem'] = "❌ Cliente não encontrado. Deseja cadastrá-lo?";
        $_SESSION['proposta_cadastro'] = $identificador;
        header("Location: cadastro_rapido.php");
        exit();
        }
    } catch(PDOException $e) {
    $_SESSION['mensagem'] = "Erro: " . $e->getMessage();
    header("Location: agendar_cliente.php");
    exit();
}