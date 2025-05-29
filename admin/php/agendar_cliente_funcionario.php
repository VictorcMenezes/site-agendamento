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

//Verificar se o foi endiado por POST

$id_servico = $_POST['id_servico'] ?? null;
if (!$id_servico){
    $_SESSION['mensagem'] = "Selecione um serviço.";
    header("Location: agendar_cliente_servico.php");
    exit();
}

try{
    //Conecta ao banco de dados
    $pdo = new PDO("sqlite:../../banco/banco.db");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 1. Busca o serviço
    $stmt = $pdo->prepare("SELECT * FROM servicos WHERE id = ?");
    $stmt->execute([$id_servico]);
    $servico = $stmt->fetch(PDO::FETCH_ASSOC);

    if(!$servico){
        $_SESSION['mensagem'] = "Serviço inválido.";
        header("Location: agendar_cliente.php");
        exit();
    }

    $funcao = $servico['funcao'] ?? null;

   // 2. Buscar todos os usuários com essa função
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE funcao = ?");
    $stmt->execute([$funcao]);
    $funcionarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($funcionarios) === 0) {
        $_SESSION['mensagem'] = "Nenhum funcionário disponível para a função '$funcao'.";
        header("Location: agendar_cliente.php");
        exit();
    }

} catch (PDOException $e) {
    $_SESSION['mensagem'] = "Erro no banco: " . $e->getMessage();
    header("Location: agendar_cliente.php");
    exit();
}

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Escolher Funcionário</title>
    <link rel="stylesheet" href="../css/agendar_cliente_style.css">
</head>
<body>

    <h2>Agendar para: <?= htmlspecialchars($cliente['nome']) ?> <?= htmlspecialchars($cliente['sobrenome']) ?></h2>
    <div class="container">
    <h3>Serviço: <?= htmlspecialchars($servico['nome']) ?> (<?= $servico['duracao'] ?> min)</h3>

    <form action="agendar_cliente_calendario.php" method="post">
        <input type="hidden" name="id_servico" value="<?= $servico['id'] ?>">

        <label for="id_funcionario">Escolha o profissional:</label>
        <select name="id_funcionario" id="id_funcionario" required>
            <option value="">Selecione</option>
            <?php foreach ($funcionarios as $f): ?>
                <option value="<?= $f['id'] ?>"><?= htmlspecialchars($f['nome'] . " " . $f['sobrenome']) ?></option>
            <?php endforeach; ?>
        </select>

        <button type="submit">Continuar</button>
    </form>
    </div>
</body>
</html>