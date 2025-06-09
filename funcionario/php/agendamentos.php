<?php
// Verifica se está logado e se é funcionário
require_once(__DIR__ . '/verifica_funcionario.php');

$id_funcionario = $_SESSION['usuario_id'];

try {
    $pdo = new PDO("sqlite:" . __DIR__ . "/../../banco/banco.db");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {     
    echo "Erro ao conectar ao banco: " . $e->getMessage();
    exit();
}

// Filtro: dia, semana, mes, pendentes
$filtro = $_GET['filtro'] ?? 'dia';

switch ($filtro) {
    case 'semana':
        $query = "
            SELECT a.*, s.nome AS servico, u.nome AS cliente
            FROM agendamentos a
            JOIN servicos s ON a.id_servico = s.id
            JOIN usuarios u ON a.id_usuario = u.id
            WHERE a.id_funcionario = ? AND strftime('%W', a.data) = strftime('%W', 'now')
            ORDER BY a.data, a.hora
        ";
        break;
    case 'mes':
        $query = "
            SELECT a.*, s.nome AS servico, u.nome AS cliente
            FROM agendamentos a
            JOIN servicos s ON a.id_servico = s.id
            JOIN usuarios u ON a.id_usuario = u.id
            WHERE a.id_funcionario = ? AND strftime('%m', a.data) = strftime('%m', 'now')
            ORDER BY a.data, a.hora
        ";
        break;
    case 'pendentes':
        $query = "
            SELECT a.*, s.nome AS servico, u.nome AS cliente
            FROM agendamentos a
            JOIN servicos s ON a.id_servico = s.id
            JOIN usuarios u ON a.id_usuario = u.id
            WHERE a.id_funcionario = ? AND a.status = 'pendente'
            ORDER BY a.data, a.hora
        ";
        break;
    default: // dia
        $query = "
            SELECT a.*, s.nome AS servico, u.nome AS cliente
            FROM agendamentos a
            JOIN servicos s ON a.id_servico = s.id
            JOIN usuarios u ON a.id_usuario = u.id
            WHERE a.id_funcionario = ? AND a.data = date('now')
            ORDER BY a.data, a.hora
        ";
        break;
}
$stmt = $pdo->prepare($query);
$stmt->execute([$id_funcionario]);
$agendamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Agendamentos do Funcionário</title>
    <link rel="stylesheet" href="../css/agendamentos_style.css"> <!-- Pode ajustar se tiver outro -->
</head>
<body>
   <main class="main-wrapper">
    <header>
        <h1>Olá, <?= htmlspecialchars($_SESSION['nome']) ?>!</h1>
        <h2>Seus Agendamentos - <em><?= ucfirst($filtro) ?></em></h2>
    </header>

    <nav class="nav-bar">
        <a href="../index.php"><button class="btn-voltar">Voltar</button></a>
        <div class="filtros">
            <a href="?filtro=dia"><button>Hoje</button></a>
            <a href="?filtro=semana"><button>Semana</button></a>
            <a href="?filtro=mes"><button>Mês</button></a>
            <a href="?filtro=pendentes"><button>Pendentes</button></a>
        </div>
    </nav>

    <section class="mensagens">
        <?php if (!empty($_SESSION['msg_sucesso'])): ?>
            <p class="sucesso"><?= $_SESSION['msg_sucesso'] ?></p>
            <?php unset($_SESSION['msg_sucesso']); ?>
        <?php endif; ?>

        <?php if (!empty($_SESSION['msg_erro'])): ?>
            <p class="erro"><?= $_SESSION['msg_erro'] ?></p>
            <?php unset($_SESSION['msg_erro']); ?>
        <?php endif; ?>
    </section>

    <section class="agendamentos">
        <?php if (empty($agendamentos)): ?>
            <p>Sem agendamentos para o filtro selecionado.</p>
        <?php else: ?>
            <?php foreach ($agendamentos as $a): ?>
                <div class="agendamento-card">
                    <p><strong><?= date('d/m/Y', strtotime($a['data'])) ?> às <?= htmlspecialchars($a['hora']) ?></strong></p>
                    <p><strong>Cliente:</strong> <?= htmlspecialchars($a['cliente']) ?></p>
                    <p><strong>Serviço:</strong> <?= htmlspecialchars($a['servico']) ?></p>

                    <?php if ($filtro === 'pendentes'): ?>
                        <div class="botoes-resposta">
                            <form action="responder_agendamento.php" method="post">
                                <input type="hidden" name="id_agendamento" value="<?= $a['id'] ?>">
                                <input type="hidden" name="resposta" value="1">
                                <button type="submit" class="btn-confirmar">Confirmar</button>
                            </form>
                            <form action="responder_agendamento.php" method="post">
                                <input type="hidden" name="id_agendamento" value="<?= $a['id'] ?>">
                                <input type="hidden" name="resposta" value="0">
                                <button type="submit" class="btn-recusar">Recusar</button>
                            </form>
                        </div>
                    <?php else: ?>
                        <p><strong>Status:</strong> 
                            <?= $a['confirmado'] === '1' ? '✅ Confirmado' : ($a['confirmado'] === '0' ? '❌ Recusado' : '⏳ Pendente') ?>
                        </p>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </section>

    <footer class="footer">
        <p>Desenvolvido por <strong>Victor Menezes</strong> &copy; <?= date("Y") ?></p>
    </footer>
</main>
</body>
