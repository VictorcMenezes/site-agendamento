<?php
session_start();

// Verifica se o admin está logado
if (!isset($_SESSION['usuario_id']) || $_SESSION['nivel'] !== 'admin') {
    header('Location: ../../index.php');
    exit();
}

// Verifica se o cliente está salvo na sessão
$cliente = $_SESSION['cliente'] ?? null;
if(!$cliente){
    $_SESSION['mensagem'] = "Cliente não encontrado.";
    header("Location: agendar_cliente.php");
    exit();
}

//Conecta ao banco de dados
$pdo = new PDO("sqlite:../../banco/banco.db");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Busca os serviços disponíveis
$stmt = $pdo->query("SELECT * FROM servicos");
$servicos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Agendar Serviço - Escolher Serviço</title>
    <link rel="stylesheet" href="../css/agendar_cliente_style.css">
</head>
<body>
    <h2>Agendar para: <?= htmlspecialchars($cliente['nome']) . ' ' . htmlspecialchars($cliente['sobrenome']) ?> </h2>
    <div class="container">
        <form method="post" action="agendar_cliente_funcionario.php">
            <label for="servico">Escolha o serviço:</label>
            <select name="id_servico" required>
                <option value="">Selecione</option>
                <?php foreach ($servicos as $s): ?>
                    <option value="<?= $s['id'] ?>">
                        <?= htmlspecialchars($s['nome']) ?> (<?= $s['duracao'] ?>min - R$ <?= number_format($s['valor'], 2, ',', '.') ?>)
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit">Continuar</button>
        </form>
    </div>
</body>
</html>