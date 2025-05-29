<?php
session_start(); // Ativa sessão para armazenar mensagens
$mensagem = $_SESSION['mensagem'] ?? "";
unset($_SESSION['mensagem']);
try{
    $pdo = new PDO("sqlite:../../banco/banco.db");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Só executa se o formulário foi enviado via POST
    if($_SERVER["REQUEST_METHOD"] == "POST"){
        // Verifica se todos os campos estão presentes
        if(isset($_POST["nome"]) && isset($_POST["sobrenome"]) && isset($_POST["email"]) && isset($_POST["contato"]) && isset($_POST["senha"]) && isset($_POST["confirma-senha"])){
        $nome = $_POST["nome"];
        $sobrenome = $_POST["sobrenome"];
        $email = $_POST["email"];
        $contato = $_POST["contato"];
        $senha = $_POST["senha"];        
        $confirmaSenha = $_POST["confirma-senha"];
         
        if ($senha !== $confirmaSenha){
            echo "❌ Senhas não iguais";
        }else{
            
            $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ? OR contato = ?");
            $stmt->execute([$email, $contato]);
            $exite = $stmt->fetchColumn(PDO::FETCH_ASSOC);
            if ($exite > 0){
                echo "⚠️ Email ou contato já cadastrado";
            }else{
            // Hash da senha (mais seguro)
            $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

            $stmt = $pdo->prepare("INSERT INTO usuarios (nome, sobrenome, email, contato, senha) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$nome, $sobrenome, $email, $contato, $senhaHash]);
        
            if ($stmt->rowCount() > 0) {
            $mensagem = "✅Usuário cadastrado com sucesso!";
            echo "<p id='mensagem-sucesso'>$mensagem</p>";
            echo "<script src='../js/register.js'></script>";
            exit();

            } else {
            $mensagem = "❌ Erro ao inserir usuário.";
            }
        }
    } 
    }else{
        // Caso falte algum campo
        $mensagem = "⚠️Preencha todos os campos";
    }
}

}catch (PDOException $e) {
    $mensagem = "Erro ao conectar/inserir: " . $e->getMessage();
}


?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar</title>
</head>
    <header>
        <nav>
        <a href="../../index.php">
            <button>Home</button>
        </a>
        </nav>
    </header>  

<body>
    <div class="container">
    <h1>Cadastrar</h1>
        <?php if (!empty($mensagem)): ?>
            <p style="color: green;"><?php echo $mensagem; ?></p>
        <?php endif; ?>
    <form action="register.php" method="post">
        <div class="form-group">
            <input type="text" name="nome" id="nome" placeholder="Nome" required value="<?= isset($nome) ? htmlspecialchars($nome) : '' ?>">
            <input type="text" name="sobrenome" id="sobrenome" placeholder="Sobrenome"required value="<?= isset($sobrenome) ? htmlspecialchars($sobrenome) : '' ?>">
            <input type="email" name="email" id="email" placeholder="Email"required value="<?= isset($email) ? htmlspecialchars($email) : '' ?>">
            <input type="number" name="contato" id="contato" placeholder="Contato"required value="<?= isset($contato) ? htmlspecialchars($contato) : '' ?>">
            <input type="password" name="senha" id="senha" placeholder="Senha"required>
            <input type="password" name="confirma-senha" id="confirmaSenha" placeholder="Confirmar Senha"required>
            <small id="mensagemSenha"></small>
            <button type="submit">Cadastrar</button>
    </div>
    <script src="register.js"></script>
</body>
</html>