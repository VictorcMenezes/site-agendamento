<?php
session_start();

// Verifica se o funcionário está logado
require_once(__DIR__ . '/verifica_funcionario.php');

//Recebe dados via POST
$id_agendamento = $_POST['id_agendamento'] ?? null;
$resposta = $_POST['resposta'] ?? null;

$acao = $resposta === '1' ? 'confirmado' : ($resposta === '0' ? 'recusado' : null);

if (!$id_agendamento || !$acao) {
    $_SESSION['msg_erro'] = "❌ Parâmetros inválidos.";
    header('Location: ../../funcionario/php/agendamentos.php?filtro=pendentes');
    exit();
}

try {
    $pdo = new PDO("sqlite:" . __DIR__ . "/../../banco/banco.db");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Atualiza o status no banco
    $stmt = $pdo->prepare("UPDATE agendamentos SET status = ?, respondido_em = datetime('now') WHERE id = ?");
    $stmt->execute([$acao, $id_agendamento]);

    // Busca dados do agendamento
    $stmt = $pdo->prepare("
        SELECT
            a.data, a.hora,
            c.nome AS cliente_nome, c.contato AS cliente_contato,
            s.nome AS servico_nome
        FROM agendamentos AS a
        JOIN usuarios c ON a.id_usuario = c.id
        JOIN servicos s ON a.id_servico = s.id
        WHERE a.id = ?
    ");
    $stmt->execute([$id_agendamento]);
    $dados = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($dados) {
        require_once(__DIR__ . '/../../admin/php/enviar_whatsapp.php');
        $cliente_contato = '+55' . preg_replace('/\D/', '', $dados['cliente_contato']);
        $data_formatada = date('d/m/Y', strtotime($dados['data']));

        if ($acao === 'confirmado') {
            $mensagem = "Olá {$dados['cliente_nome']}, seu agendamento para {$dados['servico_nome']} em {$data_formatada} às {$dados['hora']} foi confirmado!";
        } else {
            $mensagem = "Olá {$dados['cliente_nome']}, o profissional não pôde confirmar seu agendamento em {$data_formatada} às {$dados['hora']}. Por favor, reagende acessando: https://seudominio.com/usuario/php/agendar.php";
        }

        enviarWhatsApp($cliente_contato, $mensagem);
    }

    //Redireciona com mensagem de sucesso
    $_SESSION['msg_sucesso'] = "✅ Ação registrada com sucesso.";
    header('Location: agendamentos.php?filtro=pendentes');
    exit();

} catch (PDOException $e) {
    $_SESSION['msg_erro'] = "❌ Erro no banco de dados: " . $e->getMessage();
    header('Location: agendamentos.php?filtro=pendentes');
    exit();
}
?>
