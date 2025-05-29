<?php
    session_start();
    //verifica se o usario está logado é admin
    if(!isset($_SESSION['usuario_id']) || $_SESSION['nivel'] !== 'admin'){
        // Redireciona para a página inicial (ou página de login)
        header('Location: ../../index.php');
        exit();
    }
?>
    <h2>Cadastro Rápido de Cliente</h2>
    <form method="post" action="salvar_cliente.php">
        <label>Nome:</label>
        <input type="text" name="nome" required>
        <label>Sobrenome:</label>
        <input type="text" name="sobrenome" required>
        <label>Email:</label>
        <input type="email" name="email" required>
        <label>Telefone:</label>
        <input type="text" name="contato" required>
        <label>Senha (opcional):</label>
        <input type="password" name="senha">
        <button type="submit">Cadastrar e Continuar</button>
    </form>