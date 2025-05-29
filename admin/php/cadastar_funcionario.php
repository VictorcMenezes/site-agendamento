<?php
    session_start();
    if(!isset($_SESSION['usuario_id']) || $_SESSION['nivel'] !== 'admin'){
        // Redireciona para a página inicial (ou página de login)
        header('Location: ../../index.php');
        exit();
    }

    if ($_SERVER["REQUEST_METHOD"] === "POST"){
        $id = $_POST["id"] ?? null;
        $funcao = $_POST["funcao"] ?? null;

        if(!$id || !$funcao){
            $_SESSION['mensagem'] = "❌ Preencha todos os campos.";
            header("Location: cadastrar_funcionario.php");
            exit();
        }
    }

    try{
        $pdo = new PDO("sqlite:../../banco/banco.db");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $pdo->prepare("UPDATE usuarios SET funcao = ? WHERE id = ?");
        $stmt->execute([$funcao, $id]);

        if($stmt->rowCount() > 0){
            $_SESSION['mensagem'] = "✅ Função alterada com sucesso.";
        } else {
            $_SESSION['mensagem'] = "⚠️ Nenhuma alteração feita.";
        }
    } catch (PDOException $e){
        $_SESSION['mensagem'] = "Erro com o banco de dados: " . $e->getMessage();
    }

    header("Location: gerenciar_funcionario.php");
    exit();