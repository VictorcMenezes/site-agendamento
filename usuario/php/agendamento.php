<?php
    session_start();
    //verificar se o usario esta logado
    if (!isset($_SESSION['usuario_id'])) {
        header('Location: ../../login.php');
        exit();
    }

    if($_SERVER['REQUEST_METHOD'] ==='POST'){
        $id_usuario = $_SESSION['usuario_id'];
        $id_servico = $_POST['id_servico'] ?? null;

        if(empty($id_servico)){
            $_SESSION['mensagem'] = "Selecione um serviço para agendar.";
            header("Location: agendar.php");
            exit();
        }

        try{
            $pdo = new PDO("sqlite:../../banco/banco.db");
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            //buscar funcionario que fazem esse serviço
            $sql = "SELECT f.id, f.nome, f.sobrenome
                    FROM funcionarios f
                    JOIN servicos_funcionarios fs ON f.id = fs.id_funcionario
                    WHERE fs.id_servico = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id_servico]);
            $funcionarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

            //inserir o agendamento
            $stmt = $pdo-> prepare("INSERT INTO agendamentos (id_usuario, id_servico) VALUES (?, ?, datetime('now'))");
            $stmt->execute([$id_usuario, $id_servico]);

            $_SESSION['mensagem'] = "Agendamento realizado com sucesso!";
        } catch (PDOException $e) {
            $_SESSION['mensagem'] = "Erro ao agendar serviço: " . $e->getMessage();
        }

        header("Location: agendar.php");
        exit();
    }else{
        $_SESSION['mensagem'] = "Erro ao agendar serviço.";
        header("Location: agendar.php");
        exit();
    }