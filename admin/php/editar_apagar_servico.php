<?php
    session_start();
    //verifica se o usario está logado é admin
    if(!isset($_SESSION['usuario_id']) || $_SESSION['nivel'] !== 'admin'){
        // Redireciona para a página inicial (ou página de login)
        header('Location: ../../index.php');
        exit();
    }

    try{
        $pdo = new PDO("sqlite:../../banco/banco.db");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $servico = null;
        $mensagem = "";        

        // Verifica se o formulário foi enviado
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['buscar'])) {
            // Obtém os dados do formulário
            $nome = $_POST['nome'] ?? '';

            // Consulta ao banco para buscar o servico
            $stmt = $pdo->prepare("SELECT * FROM servicos WHERE nome = :nome");
            $stmt->execute(['nome' => $nome]);
            $_SESSION['servico'] = $stmt->fetch(PDO::FETCH_ASSOC);
            //se o servico não for encontrado
            if (!$_SESSION['servico']) {
                $_SESSION['mensagem'] = "⚠️Serviço não encontrado.";
            }       
              // redireciona para a tela com os dados
             header('Location: gerenciar_servico.php');
            exit(); 
        }

        if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['salvar'])) {
            $id = $_POST['id'];
            $duracao = intval($_POST['duracao']);
            $valor = floatval($_POST['valor']);

            if($duracao > 0 && $valor > 0) {
                // Inicia uma transação para evitar o erro "database is locked"
                $pdo->beginTransaction();
                $stmt = $pdo->prepare("UPDATE servicos SET duracao = :duracao, valor = :valor WHERE id = :id");
                $stmt->execute([
                    'duracao' => $duracao,
                    'valor' => $valor,
                    'id' => $id
                ]);
                // Confirma a transação
                $pdo->commit();
                $_SESSION['mensagem'] = "✅Serviço atualizado com sucesso!";
            } else {
                $_SESSION['mensagem'] = "❌ Duração e valor devem ser maiores que zero." . $e->getMessage();
            }

           // Fecha a conexão antes do redirecionamento
            $pdo = null; 
           header('Location: gerenciar_servico.php');
           exit();
        }

        if($_SERVER['REQUEST_METHOD']=== 'POST' && isset($_POST['excluir'])){
            $id = $_POST['id'];
            // Inicia uma transação para evitar o erro "database is locked"
            $pdo->beginTransaction();
            $stmt = $pdo->prepare("DELETE FROM servicos WHERE id = :id");
            $stmt->execute(['id' => $id]);
            // Confirma a transação
            $pdo->commit();
            $_SESSION['mensagem'] = "✅Serviço excluído com sucesso!";   
        }else{
            $_SESSION['mensagem'] = "❌Serviço não encontrado.";
        }

    } catch(PDOException $e) {
        // Se der erro, desfaz a transação e mostra o erro
        if($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        $_SESSION['mensagem'] = "Erro ao conectar ao banco de dados: " . $e->getMessage();
    }finally{
        //fehca a concexão com o banco
        $pdo = null;
        // Redireciona de volta
        header('Location: gerenciar_servico.php');
        exit();
}
    ?>