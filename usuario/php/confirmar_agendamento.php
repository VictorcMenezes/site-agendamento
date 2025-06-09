<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../../login.php");
    exit();
}

// Obtém os dados do formulário
$id_usuario = $_SESSION['usuario_id'];
$id_funcionario = $_POST['id_funcionario'] ?? null;
$id_servico = $_POST['id_servico'] ?? null;
$data = $_POST['data'] ?? null;
$hora = $_POST['hora'] ?? null;

if (!trim($id_usuario) || !trim($id_funcionario) || !trim($id_servico) || !trim($data) || !trim($hora)) {
    $_SESSION['mensagem'] = "❌ Dados incompletos para agendar.";
    header("Location: agendar.php");
    exit();
}

try {
    $pdo = new PDO("sqlite:../../banco/banco.db");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Verifica se já existe agendamento nesse horário
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM agendamentos WHERE data = ? AND hora= ? AND id_funcionario = ?");
    $stmt->execute([$data, $hora, $id_funcionario]);
    $ja_existe = $stmt->fetchColumn();

    if ($ja_existe) {
        $_SESSION['mensagem'] = "⚠️ Já existe um agendamento nesse horário.";
        header("Location: agendar.php");
        exit();
    }

    // Salva o agendamento
    $stmt = $pdo->prepare("INSERT INTO agendamentos (id_usuario, id_funcionario, id_servico, data, hora, status) VALUES (?, ?, ?, ?, ?, 'pendente')");
    $stmt->execute([$id_usuario, $id_funcionario, $id_servico, $data, $hora]);

    // Busca dados do agendamento para envio de mensagem
 $stmt = $pdo->prepare("
    SELECT 
        cliente.nome AS cliente_nome, cliente.contato AS cliente_contato,
        func.nome AS funcionario_nome, func.contato AS funcionario_contato,
        s.nome AS servico_nome
    FROM usuarios AS cliente, usuarios AS func, servicos AS s
    WHERE cliente.id = ? AND func.id = ? AND s.id = ?
");
$stmt->execute([$id_usuario, $id_funcionario, $id_servico]);
    $dados = $stmt->fetch(PDO::FETCH_ASSOC);
    $data_formatada = date('d/m/Y', strtotime($data));

    if ($dados) {
        require_once '../../admin/php/enviar_whatsapp.php';
        //mensagem cliente
        $msg_cliente = "Olá {$dados['cliente_nome']} tudo bem? \nSeu agendamento para {$dados['servico_nome']} para o dia $data_formatada  às $hora. está esperando confirmação por {$dados['funcionario_nome']}. ";
        $id_agendamento = $pdo->lastInsertId();
        //mensagem funcionario
        $link_confirmar = "http://localhost/site-agendamento/funcionario/php/responder_agendamento.php?id=$id_agendamento&acao=confirmado";
        $link_recusar = "http://localhost/site-agendamento/funcionario/php/responder_agendamento.php?id=$id_agendamento&acao=recusado";

        $msg_funcionario = "Você tem um novo agendamento pendente!\nCliente: {$dados['cliente_nome']}\nServiço: {$dados['servico_nome']}\nDia: $data_formatada às $hora\n\nConfirmar: $link_confirmar\nRecusar: $link_recusar";

        // $msg_dono = "Novo agendamento: {$dados['cliente_nome']} com {$dados['funcionario_nome']} para {$dados['servico_nome']} em $data_formatada  às $hora.";

        $cliente_contato = '+55' . preg_replace('/\D/', '', $dados['cliente_contato']);
        $funcionario_contato = '+55' . preg_replace('/\D/', '', $dados['funcionario_contato']);
       

        // 🔒 Log para depuração
        file_put_contents("log_envio.txt", date("Y-m-d H:i:s") . " | Telefones formatados: Cliente = $cliente_contato | Funcionário = $funcionario_contato\n", FILE_APPEND);
            if ($cliente_contato === $funcionario_contato) {
        file_put_contents("log_envio.txt", "⚠️ ALERTA: Cliente e funcionário possuem o mesmo número: $cliente_contato\n", FILE_APPEND);
    }
        // Envia WhatsApp
        if ($cliente_contato !== $funcionario_contato) {
            enviarWhatsApp($cliente_contato, $msg_cliente);
            enviarWhatsApp($funcionario_contato, $msg_funcionario);
        } else {
            enviarWhatsApp($cliente_contato, $msg_cliente . "\n\n*Mensagem também enviada como notificação para o profissional.*");
        }
       
    }

    $_SESSION['mensagem'] = "✅ Agendamento realizado com sucesso!";
} catch (PDOException $e) {
    $_SESSION['mensagem'] = "❌ Erro ao agendar: " . $e->getMessage();
}

header("Location: agendar.php");
exit();
