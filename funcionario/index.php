<?php
require_once "php/verifica_funcionario.php";

try {
    $pdo = new PDO("sqlite:../banco/banco.db");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $id_funcionario = $_SESSION['usuario_id'];

     // Buscar agendamentos futuros do funcionário
    $stmt = $pdo->prepare("
        SELECT a.id, a.data, a.hora, s.nome AS servico, u.nome AS cliente
        FROM agendamentos a
        JOIN servicos s ON a.id_servico = s.id
        JOIN usuarios u ON a.id_usuario = u.id
        WHERE a.id_funcionario = ? AND a.data >= date('now')
        ORDER BY a.data, a.hora
    ");
    $stmt->execute([$id_funcionario]);
    $agendamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "Erro: " . $e->getMessage();
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Painel do Funcionário</title>
    <link rel="stylesheet" href="css/index_style.css">
</head>
<body>
    <main class="main-wrapper">
         <header>
            <h1>Bem-vindo, <?= $_SESSION['nome'] ?>!</h1>
        </header>
        <nav class="nav-bar">
        <a href="../login-register/php/logout.php" onclick="return confirm('Tem certeza que deseja sair?');">
            <button class="btn"><i class="fas fa-sign-out-alt"></i> Sair</button>
        </a>
        <a href="./php/agendamentos.php">
            <button>Gerenciamento de Agendamentos</button>
        </a>
    </nav>
     <!-- Conteúdo principal -->
    <div class="container">
        <div class="card">
        <h2>Seus Agendamentos Futuros</h2>

        <?php if (empty($agendamentos)): ?>
            <p>Você não possui agendamentos futuros.</p>
        <?php else: ?>
            <?php foreach ($agendamentos as $a): ?>
                <div class="card-agendamento">
                    <strong>Data:</strong> <?= date("d/m/Y", strtotime($a['data'])) ?> às <?= $a['hora'] ?><br>
                    <strong>Serviço:</strong> <?= htmlspecialchars($a['servico']) ?><br>
                    <strong>Cliente:</strong> <?= htmlspecialchars($a['cliente']) ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
     </div>
    </div>
    <footer class="footer">
        <p>Desenvolvido por <strong>Victor Menezes</strong> &copy; <?= date("Y") ?></p>
    </footer>
    </main>
</body>
</html>