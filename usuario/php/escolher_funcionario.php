<?php
session_start();

//verificar se está logado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../../login.php');
    exit();
}

$pdo = new PDO("sqlite:../../banco/banco.db");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$servico_id =$_POST['servico_id'] ?? null;

if (!$servico_id){
    $_SESSION['mensagem'] = "Selecione um serviço";
    header('Location: agendar.php');
    exit();
}

//Buscar serviço
$stmt = $pdo->prepare("SELECT * FROM servicos WHERE id = ?");
$stmt->execute([$servico_id]);
$servico = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$servico) {
    echo "Serviço não encontrado!";
    exit();
}

$funcao = $servico['funcao'] ?? '';

$stmt = $pdo->prepare("SELECT id, nome, sobrenome  FROM usuarios WHERE funcao = ?");
$stmt->execute([$funcao]);
$funcionarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../css/escolher_funcionario_style.css">
    <div class="container">
         <nav>
        <a href="../../index.php">
            <button>VOLTAR</button>
        </a>
    </nav>
<h2>Profissional disponíveis para: <?= htmlspecialchars($servico['nome']) ?></h2>


<form method="post" action="calendario.php">
    <input type="hidden" name="id_servico" value="<?= $servico['id'] ?>">
    <label for="id_funcionario">Escolha um profissional:</label>
    <select name="id_funcionario" required>
        <?php foreach ($funcionarios as $f): ?>
            <option value="<?=$f['id']?>">
                <?= htmlspecialchars($f['nome'] . ' ' . $f['sobrenome']) ?>
            </option>
        <?php endforeach; ?>
    </select>
    <button type="submit">Ver dias disponíveis</button>
</form>
</div>