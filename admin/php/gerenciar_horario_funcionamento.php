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
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciamento Horarios de Funcionamento</title>
    <link rel="stylesheet" href="../css/gerenciar_horario_funcionamento_style.css">
</head>
<body>
    <nav>
        <a href="../admin.php">
            <button>Gerencimento Estabelecimento</button>
        </a>
    </nav>
    <?php
    if (isset($_SESSION['mensagem'])) {
    $msg = $_SESSION['mensagem'];
    $cor = (str_starts_with($msg, '❌')) ? 'red' : 'green';
    echo "<p style='color:$cor'>$msg</p>";
    unset($_SESSION['mensagem']);
    }
    ?>
    <h1>Gerenciamento Horarios de Funcionamento</h1>
    <div class="container">
        <div class="cadastro">
            <h2>Cadastra Horario Abertura e Fechamento da semana</h2>
            <div class="linha-dia">
                <form action="cadastrar_horario_funcionamento.php" method="post">
                  <input type="hidden" name="dia" value="segunda">
                  <label class="dia-label">Segunda:</label>
                  <span>Abertura:</span>
                  <input type="time" name="hora_inicio">
                  <span>Fechamento:</span>
                  <input type="time" name="hora_fim">
                  <button class="salvar" type="submit" name="acao" value="salvar">Salvar</button>
                  <button class="fechar" type="submit" name="acao" value="fechar">Fechar</button>
                </form>
            </div>
            <br>
            <div class="linha-dia">
              <form action="cadastrar_horario_funcionamento.php" method="post">
                <input type="hidden" name="dia" value="terça">
                <label class="dia-label">Terça:</label>
                <span>Abertura:</span>
                <input type="time" name="hora_inicio">
                <span>Fechamento:</span>
                <input type="time" name="hora_fim">
                <button type="submit" name="acao" value="salvar">Salvar</button>
                <button class="fechar"type="submit" name="acao" value="fechar">Fechar</button>
              </form>
            </div>
              <br>
            <div class="linha-dia">
              <form action="cadastrar_horario_funcionamento.php" method="post">
                <input type="hidden" name="dia" value="quarta">
                <label class="dia-label">Quarta:</label>
                <span>Abertura:</span>
                <input type="time" name="hora_inicio">
                <span>Fechamento:</span>
                <input type="time" name="hora_fim">
                <button type="submit" name="acao" value="salvar">Salvar</button>
                <button class="fechar"type="submit" name="acao" value="fechar">Fechar</button>
              </form>
            </div>
              <br>
            <div class="linha-dia">
              <form action="cadastrar_horario_funcionamento.php" method="post">
                <input type="hidden" name="dia" value="quinta">
                <label class="dia-label">Quinta:</label>
                <span>Abertura:</span>
                <input type="time" name="hora_inicio">
                <span>Fechamento:</span>
                <input type="time" name="hora_fim">
                <button type="submit" name="acao" value="salvar">Salvar</button>
                <button class="fechar"type="submit" name="acao" value="fechar">Fechar</button>
              </form>
            </div>
              <br>
            <div class="linha-dia">
              <form action="cadastrar_horario_funcionamento.php" method="post">
                <input type="hidden" name="dia" value="sexta">
                <label class="dia-label">Sexta:</label>
                <span>Abertura:</span>
                <input type="time" name="hora_inicio">
                <span>Fechamento:</span>
                <input type="time" name="hora_fim">
                <button type="submit" name="acao" value="salvar">Salvar</button>
                <button class="fechar"type="submit" name="acao" value="fechar">Fechar</button>
              </form>
            </div>
              <br>
            <div class="linha-dia">
              <form action="cadastrar_horario_funcionamento.php" method="post">
                <input type="hidden" name="dia" value="sábado">
                <label class="dia-label">Sábado:</label>  
                <span>Abertura:</span>
                <input type="time" name="hora_inicio">
                <span>Fechamento:</span>
                <input type="time" name="hora_fim">
                <button type="submit" name="acao" value="salvar">Salvar</button>
                <button class="fechar"type="submit" name="acao" value="fechar">Fechar</button>
              </form>
            </div>
              <br>
            <div class="linha-dia">
              <form action="cadastrar_horario_funcionamento.php" method="post">
                <input type="hidden" name="dia" value="domingo">
                <label class="dia-label">Domingo:</label>
                <span>Abertura:</span>
                <input type="time" name="hora_inicio">
                <span>Fechamento:</span>
                <input type="time" name="hora_fim">
                <button type="submit" name="acao" value="salvar">Salvar</button>
                <button class="fechar"type="submit" name="acao" value="fechar">Fechar</button>
              </form>
            </div>          

        </div>
    </div>

    
</body>
</html>
