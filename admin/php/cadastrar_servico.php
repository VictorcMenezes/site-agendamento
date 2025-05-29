<?php
    session_start();
    //verifica se o usario está logado é admin
    if(!isset($_SESSION['usuario_id']) || $_SESSION['nivel'] !== 'admin'){
        // Redireciona para a página inicial (ou página de login)
        header('Location: ../../index.php');
        exit();
    }
    $mensagem = '';

    try{
        $pdo = new PDO("sqlite:../../banco/banco.db");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Só executa se o formulário foi enviado via POST
        if($_SERVER["REQUEST_METHOD"] == "POST"){
            // Verifica se todos os campos estão presentes
            if(isset($_POST["nome"]) && isset($_POST["duracao"]) && isset($_POST["valor"]) && isset($_POST["funcao"])){
                $nome = $_POST["nome"];
                $duracao = $_POST["duracao"];
                $valor = $_POST["valor"];
                $funcao = $_POST['funcao'];
                if($valor < 0){
                    echo "❌ Valor dever ser maior que 0";
                }else{

                    $stmt = $pdo->prepare("SELECT * FROM servicos WHERE nome = ?");
                    $stmt->execute([$nome]);
                    $existe = $stmt->fetchColumn(); // sem FETCH_ASSOC
                    if ($existe !== false) {
                         echo "⚠️ Serviço já cadastrado";
                    }else{

                    $stmt = $pdo->prepare("INSERT INTO servicos (nome, duracao, valor, funcao) VALUES (?, ?, ?, ?)");
                    $stmt->execute([$nome, $duracao, $valor, $funcao]);
                    
                    if ($stmt->rowCount() > 0) {
                        $mensagem = "✅ Serviço cadastrado com sucesso!";
                        echo "<p id='mensagem-sucesso'>$mensagem</p>";
                        echo "<script src='../js/cadastrar_servico.js'></script>";
                        exit();
                    } else {
                        $mensagem = "❌ Erro ao inserir serviço.";
                    }
                }
                }
            }
        } else {
            // Caso falte algum campo
            $mensagem = "⚠️Preencha todos os campos";
        }

    }catch (PDOException $e) {
    $mensagem = "Erro ao conectar/inserir: " . $e->getMessage();
    }

    // Exibe a mensagem final, se houver
    if (!empty($mensagem)) {
        echo "<p>$mensagem</p>";
    }