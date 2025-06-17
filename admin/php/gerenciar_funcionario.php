<?php
    session_start();
    //verifica se o usario está logado é admin
    if(!isset($_SESSION['usuario_id']) || $_SESSION['nivel'] !== 'admin'){
        // Redireciona para a página inicial (ou página de login)
        header('Location: ../../index.php');
        exit();
    }

    $funcao=$_SESSION['funcao']?? null;
    $mensagem = $_SESSION['mensagem']?? "";
    $usuario = null;// <- Adiciona esta linha para evitar o warning
    //limpa os dados da sessão apos carregar
    unset($_SESSION['funcao'], $_SESSION['mensagem']);

    try{
        $pdo = new PDO("sqlite:../../banco/banco.db");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        //carrega fuções cadastradas
        $stmt = $pdo->query("SELECT * FROM funcao");
        $funcoes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        //Verifica se foi feito busca por usuario
        if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["buscar"])) {
            $usuarioInput = trim($_POST["Usuario"]);

            $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ? or contato = ?");
            $stmt->execute([$usuarioInput, $usuarioInput]);
            $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if(count($usuarios) === 0){
                $_SESSION['mensagem'] = "❌ Usuário não encontrado.";
                header("Location: gerenciar_funcionario.php");
                exit();
            }
            $usuario = $usuarios[0];// Pegamos o primeiro resultado
        }
    }catch(PDOException $e){
        $_SESSION['mensagem'] ="Erro com o banco de dados: " . $e->getMessage();
        header("Location: gerenciar_funcionario.php");
        exit();
    }
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciamento Funcionario e Função</title>
    <link rel="stylesheet" href="../css/gerenciar_funcionario_style.css">
</head>
<body>

<main class="main-wrapper">    
        <header>
            <h1>Gerenciamento Funcionario e Função</h1>
        </header>
    <nav class="nav-bar">
        <a href="../admin.php">
            <button>VOLTAR</button>
        </a>
    </nav>    
     <?php if ($mensagem): ?>
                <p><?= $mensagem ?></p>
        <?php endif; ?>
    <div class = "container">
    <div class="cadastro">
        <div class="cadastro-funcao">
            <h2>Cadastrar Função</h2>
            <form action="cadastrar_funcao_trabalho.php" method="post">
                <label for="funcao">Função:</label>
                <input type="text" id="funcao" name="funcao" placeholder="Função Trabalho" required>
                <button type="submit">Cadastrar</button>
            </form>
        </div>

        <div class="cadastro-funcionario">
            <h2>Cadastrar Funcionario</h2>
            <form method="post">
                <label for="Usuario">Email ou Telefone:</label>
                <input type="text" name="Usuario" placeholder="Email ou Contato" required>
                <button type="submit" name="buscar">Buscar</button>
            </form>
        </div>
        <?php if ($usuario): ?>
            <h3>Funcionario Encontrado: <?= htmlspecialchars($usuario['nome']) ?> <?= htmlspecialchars($usuario['sobrenome']) ?></h3>
            <form action="cadastar_funcionario.php" method="post">
                <input type="hidden" name="id" value="<?= $usuario['id'] ?>">
                <label for="funcao">Selecionar Função</label>
                <select name="funcao" id="funcao" required>
                    <option value="">Selecione</option>
                    <?php foreach ($funcoes as $f): ?>
                        <option value="<?= htmlspecialchars($f['nome'])?>" <?= $usuario['funcao'] === $f['nome'] ? 'select' : ''?>>
                            <?= htmlspecialchars($f['nome']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit">Salvar Função</button>
            </form> 
        <?php endif; ?>
    </div>
    
</div>
  <footer class="footer">
    <p>Desenvolvido por <strong>Victor Menezes</strong> &copy; <?= date("Y") ?></p>
</footer>
</main>
</body>
</html>