<?php
session_start();
$nome = $_POST['nome'] ?? '';
$sobrenome = $_POST['sobrenome'] ?? '';
$email = $_POST['email'] ?? '';
$contato = $_POST['contato'] ?? '';
$senha = $_POST['senha'] ?? '';

if (!$nome || !$sobrenome || (!$email && !$contato)) {
    $_SESSION['mensagem'] = "⚠️ Preencha todos os campos obrigatórios.";
    header("Location: cadastro_rapido.php");
    exit();
}

try {
    $pdo = new PDO("sqlite:../../banco/banco.db");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "INSERT INTO usuarios (nome, sobrenome, email, contato, senha, nivel) VALUES (?, ?, ?, ?, ?, 'cliente')";
    $stmt = $pdo->prepare($sql);
    $senhaCriptografada = $senha ? passord_hash($senha, PASSWORD_DEFAULT) : '' ;
    $stmt->execute([$nome, $sobrenome, $email, $contato, $senhaCriptografada]);


    $id_cliente = $pdo->lastInsertId();
    $_SESSION['cliente'] = [
        'id' => $id_cliente,
        'nome' => $nome,
        'sobrenome' => $sobrenome,
    ];

    header("Location: escolher_servico.php");
    exit();
}catch(PDOException $e) {
    $_SESSION['mensagem'] = "Erro ao cadastrar cliente: " . $e->getMessage();
    header("Location: cadastro_rapido.php");
    exit();
}