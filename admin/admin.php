<?php
    session_start();
    //verifica se o usario está logado é admin
    if(!isset($_SESSION['usuario_id']) || $_SESSION['nivel'] !== 'admin'){
        // Redireciona para a página inicial (ou página de login)
        header('Location: ../login-register/php/login.php');
        exit();
    }
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerencimento Estabelecimento</title>
    <link rel="stylesheet" href="./css/admin_style.css">
</head>
<body>
    <main class="main-wrapper">
        <header>
            <h1>Bem-vindo, <?= $_SESSION['nome'] ?>!</h1>
        </header>
    <nav class="nav-bar">
        <?php if(isset($_SESSION['nome'])): ?>
        <a href="../login-register/php/logout.php" onclick="return confirm('Tem certeza que deseja sair?');">
            <button class="btn"><i class="fas fa-sign-out-alt"></i> Sair</button>
        </a>
        <?php else: ?>
            <a href="../login-register/php/login.php">
            <button class="btn"><i class="fas fa-sign-in-alt"></i> Login</button>
        </a>
        <?php endif; ?>
        <a href="../admin/php/gerenciar_servico.php">
            <button>Gerenciamento Serviços</button>
        </a>
        <a href="../admin/php/gerenciar_horario_funcionamento.php">
            <button>Gerenciamento Horarios de Funcionamento</button>
        </a>
        <a href="../admin/php/gerenciar_funcionario.php">
            <button>Gerenciamento de Funcionário</button>
        </a>
        <a href="../admin/php/listar_agendamentos.php">
            <button>Lista de Agendamentos</button>
        </a>
         <a href="../admin/php/agendar_cliente.php">
            <button>Agendar Serviço Avulso</button>
        </a>
    </nav>
    <div class="conteudo">
    <h1>Gerencimento Estabelecimento</h1>
    </div>
      <footer class="footer">
    <p>Desenvolvido por <strong>Victor Menezes</strong> &copy; <?= date("Y") ?></p>
</footer>
    </main>
   
</body>
</html>