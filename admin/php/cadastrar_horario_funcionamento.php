<?php
    session_start();
    //verifica se o usario está logado é admin
    if(!isset($_SESSION['usuario_id']) || $_SESSION['nivel'] !== 'admin'){
        // Redireciona para a página inicial (ou página de login)
        header('Location: ../../index.php');
        exit();
    }
    $mensagem = $_SESSION['mensagem'] ?? "";
    unset($_SESSION['mensagem']);

    try{
        $pdo = new PDO("sqlite:../../banco/banco.db");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        if($_SERVER["REQUEST_METHOD"] === "POST") {
            //Recebe os dados enviados para um dia
            $dia = $_POST['dia'] ?? null;
            $acao = $_POST['acao'] ?? 'salvar';// "salvar" ou "fechar"
            if($dia){
                if($acao === 'fechar'){                
                    // Verifica se o dia já existe no banco
                    $stmt = $pdo->prepare("SELECT id FROM horario_funcionamento WHERE dia = ?");
                    $stmt->execute([$dia]);
                    $existe = $stmt->fetch();

                    if ($existe){
                        $stmt = $pdo->prepare("UPDATE horario_funcionamento SET fechado = 1 WHERE dia = ?");
                        $stmt->execute([$dia]);
                    }else{
                        $stmt = $pdo->prepare("INSERT INTO horario_funcionamento (dia, hora_inicio, hora_fim, fechado) VALUES (?,'', '', 1)");
                        $stmt->execute([$dia]);
                    }

                    $_SESSION['mensagem'] = "🔒 $dia marcado como fechado!";
                
                }else{
                    //salvar ou atualizar
                    $hora_inicio = $_POST['hora_inicio'] ?? null;
                    $hora_fim = $_POST['hora_fim'] ?? null;

                    if($hora_inicio && $hora_fim){
                        //verifica se já existe
                        $stmt = $pdo->prepare("SELECT id FROM horario_funcionamento WHERE dia = ?");
                        $stmt->execute([$dia]);
                        $existe = $stmt->fetch();

                        if($existe){
                            $stmt = $pdo->prepare("UPDATE horario_funcionamento SET hora_inicio = ?, hora_fim = ? WHERE dia = ?");
                            $stmt->execute([$hora_inicio, $hora_fim, $dia]);
                            $_SESSION['mensagem'] = "🔄 $dia atualizado com sucesso!";
                        }else{
                            $stmt = $pdo->prepare("INSERT INTO horario_funcionamento (dia, hora_inicio, hora_fim) VALUES (?, ?, ?)");
                            $stmt->execute([$dia, $hora_inicio, $hora_fim]);
                            $_SESSION['mensagem'] = "✅ Horário de $dia cadastrado!";
                        }
                    }else{
                        $_SESSION['mensagem'] = "❌ Informe os horários de início e fim.";
                    }
                }
            }
        }

    } catch (PDOException $e) {
        $_SESSION['mensagem'] = "❌ Erro no banco de dados: " . $e->getMessage();
    }            

    // Redireciona de volta para a tela de gerenciamento
    header("Location: gerenciar_horario_funcionamento.php");
    exit();
    