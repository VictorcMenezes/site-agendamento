<?php
session_start();
//conecta com o banco de dados
$pdo = new PDO("sqlite:../../banco/banco.db");

function redirecionarPorNivel($nivel) {
    $nivel = strtolower(trim($nivel));
    if ($nivel === 'admin') {
        header('Location: ../../admin/admin.php');
    } elseif ($nivel === 'funcionario') {
        header('Location: ../../funcionario/index.php');
    } else {
        header('Location: ../../index.php');
    }
    exit();
}

// Verifica se o usuário está logado
if (isset($_SESSION['usuario_id'])) {
    redirecionarPorNivel($_SESSION['nivel']);
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
            
            redirecionarPorNivel($_SESSION['nivel']);
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
    <title>Login | Acesso ao Sistema</title>
    <link rel="stylesheet" href="../css/login.css">
</head>
<body>
    <main class="main-wrapper">
        <nav class="nav-bar">
            <a href="../../index.php"><button>Home</button></a>
        </nav>

        <header>
            <h1>Bem-vindo! Faça seu login</h1>
        </header>

        <div class="container">
            <?php if (!empty($erro)): ?>
                <div class="erro"><?= htmlspecialchars($erro) ?></div>
            <?php endif; ?>

            <form action="login.php" method="POST">
                <div class="form-group">
                    <input type="text" name="usuario" id="usuario" placeholder="Email ou DDD+Telefone" required value="<?= htmlspecialchars($usuario ?? '') ?>">
                    <input type="password" name="senha" id="senha" placeholder="Senha" required>
                    <input type="submit" value="Entrar">
                </div>
            </form>

            <div class="cadastro-link">
                <a href="register.php">Não tem cadastro? <strong>Crie uma conta</strong></a>
            </div>
        </div>

        <br><br>

        <footer class="footer">
            <p>Desenvolvido por <strong>Victor Menezes</strong> &copy; <?= date("Y") ?></p>
        </footer>
    </main>
</body>
</html>