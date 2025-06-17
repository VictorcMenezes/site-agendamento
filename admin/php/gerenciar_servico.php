<?php
    session_start();
    //verifica se o usario está logado é admin
    if(!isset($_SESSION['usuario_id']) || $_SESSION['nivel'] !== 'admin'){
        // Redireciona para a página inicial (ou página de login)
        header('Location: ../../index.php');
        exit();
    }

    $servico=$_SESSION['servico']?? null;
    $mensagem = $_SESSION['mensagem']?? "";
    //limpa os dados da sessão apos carregar
    unset($_SESSION['servico'], $_SESSION['mensagem']);

    try {
    $pdo = new PDO("sqlite:../../banco/banco.db");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Carregar funções cadastradas
    $stmt = $pdo->query("SELECT nome FROM funcao");
    $funcoes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $mensagem = "Erro ao carregar funções: " . $e->getMessage();
    $funcoes = [];
}

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciamento Serviços</title>
    <link rel="stylesheet" href="../css/gerenciar_servico_style.css">
</head>
<body>

<main class="main-wrapper">
    <div class="container">
    <header>
        <h1>Gerenciamento Serviços</h1>
    </header>
    <nav>
    <a href="../admin.php">    
        <button>VOLTAR</button>
    </a>
    </nav>
    
    
        <div class="cadastro">
            <h2>Cadastrar Serviço</h2>
            <form action="cadastrar_servico.php" method="post">
                <label for="nome">Nome do Serviço:</label>
                <input type="text" id="nome" name="nome" required>

                <label for="duracao">Duração (em minutos):</label>
                <input type="number" id="duracao" name="duracao" required>

                <label for="valor">Valor:</label>
                <input type="number" id="valor" name="valor" step="0.01" required>

                <label for="funcao">Função relacionada:</label>
                <select id="funcao" name="funcao" required>
                    <option value="">Selecione</option>
                        <?php foreach ($funcoes as $f): ?>
                            <option value="<?= htmlspecialchars($f['nome']) ?>"><?= htmlspecialchars($f['nome']) ?></option>
                        <?php endforeach; ?>
            </select>

            <button type="submit">Cadastrar</button>
        </form>

        </div>
        <div class="gerenciar">
            <h2>Editar ou Apagar</h2>
            <form action="editar_apagar_servico.php" method="post">
                <label for="nome">Nome do Serviço:</label>
                <input type="text" id="nome" name="nome" required>
                <button type="submit" name="buscar">Pesquisar</button>
            </form>
            <?php if ($mensagem): ?>
                <p><?= $mensagem ?></p>
            <?php endif; ?>

            <?php if ($servico): ?>
                <form  action="editar_apagar_servico.php" method="post">
                    <input type="hidden" name="id" value="<?= $servico['id'] ?>">
                    
                    <p><strong?>Nome:</strong> <?= htmlspecialchars($servico['nome'])?></p>

                    <label for="duracao">Duração(minutos):</label>
                    <input type="number" id="duracao" name="duracao" value="<?=$servico['duracao']?>" required>

                    <label for="valor">Valor R$:</label>
                    <input type="number" id="valor" step="0.01" name="valor" value="<?=$servico['valor']?>" required>
                    <button type="submit" name="salvar">Salvar</button>
                </form>

                <form action="editar_apagar_servico.php" method="post" onsubmit="return confirm('Tem certeza que deseja excluir este serviço?')">
                    <input type="hidden" name="id" value="<?= $servico['id'] ?>">
                    <button type="submit" name="excluir" style="background-color:red;color:white;">Excluir</button>
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