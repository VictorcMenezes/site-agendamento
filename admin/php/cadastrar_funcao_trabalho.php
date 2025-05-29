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
        if($_SERVER["REQUEST_METHOD"] === "POST"){
            //Verifica se todos os campos estão presentes
            if(isset($_POST["funcao"])){
               // $funcao = $_POST["funcao"];

                $funcao = trim($_POST["funcao"]);
                    if ($funcao === "") {
                        $mensagem = "❌ O campo função não pode estar vazio.";
                    }else{

                        $stmt = $pdo->prepare("SELECT * FROM funcao WHERE nome = ?");
                        $stmt->execute([$funcao]);
                        $existe = $stmt->fetch(PDO::FETCH_ASSOC);
                        
                        if ($existe) {
                            $_SESSION['mensagem'] = "⚠️ Função já cadastrada";

                        }else{

                        $stmt = $pdo->prepare("INSERT INTO funcao (nome) VALUES (?)");
                        $stmt->execute([$funcao]);

                        if ($stmt->rowCount() > 0) {
                            $_SESSION['mensagem'] = "✅ Função de trabalho cadastrada com sucesso!";
                        }else{
                            $mensagem = "❌ Erro ao inserir Função de trabalho.";
                        }
                    }
                }
            }else{
            //Caso falte algum campo
            $_SESSION['mensagem'] = "⚠️Preencha a função de trabalho";
        }
        } else {
        $_SESSION['mensagem'] = "⚠️ Acesso inválido.";
        }
    } catch(PDOException $e){
        $mensagem = "Erro na conexão com o banco de dados: " . $e->getMessage();
    }
// Redireciona de volta à tela de gerenciamento
header("Location: gerenciar_funcionario.php");
exit();