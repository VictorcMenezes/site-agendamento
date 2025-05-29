<?php
session_start();
if(!isset($_SESSION['usuario_id']) || $_SESSION['nivel'] !== 'admin'){
    header('Location: ../../index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agendar para Cliente</title>
    <link rel="stylesheet" href="../css/agendar_cliente_style.css">
</head>
<body>
    <nav>
    <a href="../admin.php">
        <button>Gerencimento Estabelecimento</button>
    </a>
    </nav>
    <h1>Agendar Serviço Avulso</h1>
    <div class="container">
    <form method="post" action="buscar_cliente.php">
        <label>Email ou Telefone:</label>
        <input type="text" name="identificador" placeholder="Digite email ou telefone">
        <button type="submit">Buscar Cliente</button>
    </form>

    <?php if(isset($_SESSION['mensagem'])): ?>
        <p><?= $_SESSION['mensagem']; unset($_SESSION['mensagem']); ?></p>
    <?php endif; ?>
    </div>
</body>
</html>