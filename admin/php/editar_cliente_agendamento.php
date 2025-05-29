<?php
session_start();

if (!isset($_SESSION['usuario_id']) || $_SESSION['nivel'] !== 'admin') {
    header("Location: ../../index.php");
    exit();
}

$id_agendamento = $_GET['id_agendamento'] ?? null;

if (!$id_agendamento) {
    $_SESSION['mensagem'] = "Agendamento inválido.";
    header("Location: listar_agendamentos.php");
    exit();
}

$pdo = new PDO("sqlite:../../banco/banco.db");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Buscar agendamento
$stmt = $pdo->prepare("SELECT * FROM agendamentos WHERE id = ?");
$stmt->execute([$id_agendamento]);
$agendamento = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$agendamento) {
    $_SESSION['mensagem'] = "Agendamento não encontrado.";
    header("Location: listar_agendamentos.php");
    exit();
}

// Buscar serviços
$servicos = $pdo->query("SELECT * FROM servicos")->fetchAll(PDO::FETCH_ASSOC);

// Buscar funcionários
$funcionarios = $pdo->query("SELECT * FROM usuarios WHERE funcao IS NOT NULL")->fetchAll(PDO::FETCH_ASSOC);


?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Agendamento</title>
    <link rel="stylesheet" href="../css/listar_agendamentos_style.css">

    

</head>
<body>
    <nav>
        <a href="../admin.php">
            <button>Gerencimento Estabelecimento</button>
        </a>
        <a href="listar_agendamentos.php">
            <button>VOLTAR</button>
        </a>
    </nav>
<div class="container">
<h2>Editar Agendamento</h2>
    <form method="post" action="salvar_edicao_agendamento.php">
       
        <input type="hidden" name="id_agendamento" value="<?= $agendamento['id'] ?>">

        <label>Serviço:</label>
        <select name="id_servico">
            <?php foreach ($servicos as $s): ?>
                <option value="<?= $s['id'] ?>" <?= $s['id'] == $agendamento['id_servico'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($s['nome']) ?>
                </option>
            <?php endforeach; ?>
        </select><br>

        <label>Funcionário:</label>
        <select name="id_funcionario">
            <?php foreach ($funcionarios as $f): ?>
                <option value="<?= $f['id'] ?>" <?= $f['id'] == $agendamento['id_funcionario'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($f['nome'] . " " . $f['sobrenome']) ?>
                </option>
            <?php endforeach; ?>
        </select><br>

        <label>Data:</label>
        <input type="date" name="data" value="<?= $agendamento['data'] ?>"><br>

        <label>Hora:</label>
        <input type="time" name="hora" value="<?= $agendamento['hora'] ?>"><br>

        <button type="submit">Salvar Alterações</button>
    </form>
   
</div>
</body>
</html>
