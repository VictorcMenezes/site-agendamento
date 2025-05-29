<?php
session_start();
//conecta com o banco de dados
$pdo = new PDO("sqlite:../../banco/banco.db");

// Verifica se o usuário está logado
if (isset($_SESSION['usuario_id'])) {
    header('Location: ../../index.php');
    exit();
}
// Verifica se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtém os dados do formulário
    $usuario = $_POST['usuario'] ?? null;
    $senha = $_POST['senha']?? null;

    if(!$usuario || !$senha){
        echo "⚠️ Preencha todos os campos";
        exit;
    }
    // Consulta ao banco para buscar o usuário
    $stmt = $pdo->prepare("SELECT id, nome, senha, nivel FROM usuarios WHERE email = :usuario OR contato = :usuario" );
    $stmt->execute(['usuario' => $usuario]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if($user && password_verify($senha, $user['senha'])){
            // Login bem-sucedido: armazena dados na sessão
            $_SESSION['usuario_id'] = $user['id'];
            $_SESSION['nome'] = $user['nome'];
            $_SESSION['nivel'] = $user['nivel'];
            
            header('Location: ../../index.php');
            exit();
        } else {
             echo "⚠️ Usuário ou senha incorretos!";
    exit;
}

}
?>


<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login/Cadastar</title>
</head>
    <header>
        <nav>
        <?php if (isset($_SESSION['usuario_id'])): ?>
    <a href="../php/logout.php" onclick="return confirm('Tem certeza que deseja sair?');">
    <button>sair</button>
</a>
<?php endif; ?>
        <a href="../../index.php">
            <button>Home</button>
        </a>
        </nav>
    </header>  
    <div class="container">
        <h1>Login/Acessar Conta</h1>
        <form action="login.php" method="POST">
            <div class="form-group">
                <input type="text" name="usuario" id="usuario" placeholder="Email ou DDD+Telefone" required value="<?= isset($usuario) ? htmlspecialchars($usuario) : '' ?>">
                <input type="password" name="senha" id="senha" placeholder="Senha" required>
                <input type="submit" value="Entrar">
                <a href="register.php">
                    <p>Ainda nao tem, cadastro? Clique aqui!</p>
                </a>
            </div>
        </form>
    </div>

<body>
    
</body>
</html>