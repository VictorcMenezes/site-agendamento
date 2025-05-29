<?php
    session_start();

    // Verifica se o usuário está logado
    if (!isset($_SESSION['usuario_id'])) {
        header('Location: ../../login.php');
        exit();
    }
    

    $pdo = new PDO("sqlite:../../banco/banco.db");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    
    $servicos = $pdo->query("SELECT * FROM servicos")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agendar Serviço</title>
</head>
<body>
    <nav>
        <?php if(isset($_SESSION['nome'])): ?>
            <p>Olá, <?php echo $_SESSION['nome'];?></p>
            <a href="./php/logout.php" onclick="return confirm('Tem certeza que deseja sair?');">
                <button>Sair</button>
            </a>
            <?php endif; ?>
            <a href="../../index.php">
                <button>Voltar</button>
            </a>
    </nav>

    <h1>Agendar Serviço</h1>

        <?php if(!empty($mensagem)): ?>
            <p style="color:red;"<?php echo htmlspecialchars($mensagem); ?>></p>
        <?php endif; ?>        
            <form method="post" action="agendamento.php">
                <label for="servico">Serviço:</label>
                <select name="servico" id="servico">
                    <?php foreach($servicos as $s): ?>
                        <option value="<?=$s['id']?>"><?= htmlspecialchars($s['nome'])?></option>
                        <?php endforeach; ?>
                </select>
                <button type="submit">Escolher Serviço</button>
            </form>

            <form method="post" action="calendario.php">
                <input type="hidden" name="id_servico" value="<?php $id_servico ?>">
                <label for="funcionario">Escolha o Profissional:</label>
                <select name="id_funcionario">
                    <?php foreach($funcionarios as $f): ?>
                        <option value="<?=$f['id']?>"><?= $f['nome'] . " " . $f['sobrenome']?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit">Escolher Profissional</button>
            </form>
            <br><br>
            <label>Serviço:</label>
            <select name="servico" onchange="carregarFuncionarios(this.value)">
                <?php foreach($servicos as $s): ?>
                    <option value="<?= $s['id'] ?>"><?htmlspecialchars($s['nome']) ?></option>
                <?php endforeach; ?>
            </select>

            <div id="container_funcionario" style="display:none;">
                <label>Profissional:</label>
                <select id="funcionarios" oncharge="carregarCalendario()"></select>
            </div>

            <div id="calendario"></div>



                <!-- <label for="servico">Serviços Disponiveis:</label>
                <select name="servico" id="servico">
                    <option value="">Selecione</option>
                    <?php foreach($servicos as $s): ?>
                        <option value="<?php echo htmlspecialchars($s['id'])?>">
                            <?php echo htmlspecialchars($s['nome']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <br><br>
                <button type="submit">Agendar</button> -->
            </form>
<script src="../js/agendar.js"></script>
</body>
</html>