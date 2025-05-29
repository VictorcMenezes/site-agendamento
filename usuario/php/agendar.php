<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../../login-register/php/login.php');
    exit();
}

try{
    $pdo = new PDO("sqlite:../../banco/banco.db");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $servicos = $pdo->query("SELECT * FROM servicos")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Erro ao conectar ao banco de dados: " . $e->getMessage();
    exit();
}

$id_usuario = $_SESSION['usuario_id'];

// Buscar agendamentos futuros
$stmt = $pdo->prepare("
    SELECT a.id, a.data, a.hora, s.nome AS servico, u.nome AS profissional
    FROM agendamentos a
    JOIN servicos s ON a.id_servico = s.id
    JOIN usuarios u ON a.id_funcionario = u.id
    WHERE a.id_usuario = ? AND a.data >= date('now')
    ORDER BY a.data, a.hora
");
$stmt->execute([$id_usuario]);
$agendamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../css/agendar_style.css">
    <title>Escolher Serviço</title>
</head>
<body>
    <div class="agendamento-container">
    <nav>
        <a href="../../index.php">
            <button>VOLTAR</button>
        </a>
    </nav>

    <div class = "container-formulario">
        <h1>Agendamento - Etapa 1</h1>

        <?php if (!empty($_SESSION['mensagem'])): ?>
            <p class="mensagem-sucesso"><?= $_SESSION['mensagem'] ?></p>
            <?php unset($_SESSION['mensagem']); ?>
        <?php endif; ?>

        <form action="escolher_funcionario.php" method="post" class="formulario">
            <label for="servico">Escolher Serviço:</label>
            <select name="servico_id" id="servico" required>
                <option value="">Selecione</option>
                <?php foreach ($servicos as $s): ?>
                    <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['nome'])?></option>
                <?php endforeach; ?> 
            </select>
            <br><br>
            <button type="submit">Próximo</button>
        </form>
    </div>
    <div class = "container-agendamentos">
        <h2>Seus Agendamentos Futuros</h2>
            <?php if (empty($agendamentos)): ?>
                <p>Você ainda não possui agendamentos.</p>
            <?php else: ?>
                <ul>
                    <?php foreach ($agendamentos as $a): ?>
                        <div class="agendamento-item card">
                            <?php
                                $data_br = DateTime::createFromFormat('Y-m-d', $a['data'])->format('d/m/Y');
                            ?>
                                <p><strong><?= $data_br ?> às <?= htmlspecialchars($a['hora']) ?></strong></p>
                            <p>Serviço: <?= htmlspecialchars($a['servico']) ?></p>
                            <p>Profissional: <strong><?= htmlspecialchars($a['profissional']) ?></strong></p>
                            <form action="cancelar_agendamento.php" method="post">
                                <input type="hidden" name="id_agendamento" value="<?= $a['id'] ?>">
                                <button type="submit" class="btn btn-danger" onclick="return confirm('Cancelar este agendamento?');">
                                    <i class="fas fa-times-circle"></i> Cancelar
                                </button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
    </div>
    
    <footer class="footer">
    <p>Desenvolvido por <strong>Victor Menezes</strong> &copy; <?= date("Y") ?></p>
</footer>
</div>
</body>
</html>