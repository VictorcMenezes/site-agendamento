<?php
    session_start();
    //verifica se o usario está logado é admin
    if(!isset($_SESSION['usuario_id']) || $_SESSION['nivel'] !== 'admin'){
        // Redireciona para a página inicial (ou página de login)
        header('Location: ../../index.php');
        exit();
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerencimento Estabelecimento</title>
    <link rel="stylesheet" href="./css/admin_style.css">
</head>
<body>
    <nav>
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
    <h1>Gerencimento Estabelecimento</h1>
</body>
</html>