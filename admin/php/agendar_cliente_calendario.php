<?php
session_start();

// Verifica se o admin está logado
if (!isset($_SESSION['usuario_id']) || $_SESSION['nivel'] !== 'admin') {
    header('Location: ../../index.php');
    exit();
}

// Verifica se o cliente está salvo na sessão
$cliente = $_SESSION['cliente'] ?? null;
if(!$cliente){
    $_SESSION['mensagem'] = "Cliente não encontrado.";
    header("Location: agendar_cliente.php");
    exit();
}


 //verificar se os dados foram enviados corretamente

    $id_servico = $_POST['id_servico'] ?? null;
    $id_funcionario = $_POST['id_funcionario'] ?? null;

    if(!$id_servico || !$id_funcionario){
        echo "Erro: Dados inválidos";
        exit;
    }

    $pdo = new PDO("sqlite:../../banco/banco.db");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    //Consultar os dias da semana em que o estabelecimento está aberto
    $stmt = $pdo->query("SELECT * FROM horario_funcionamento WHERE fechado = 0");
    $horario_abertos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Organiza os dias disponíveis em array: ex: ['segunda' => ['08:00', '18:00']]
    $dias_abertos = [];
    foreach ($horario_abertos as $linha) {
        $dias_abertos[strtolower($linha['dia'])] = $linha;
    }

    // Cria os nomes dos dias fixos
    $dias_semana = ['segunda', 'terça', 'quarta', 'quinta', 'sexta', 'sábado', 'domingo'];

    //gerar os proximos 30 dias
    $hoje = new Datetime();
    echo "<h2>Escolha um dia disponível</h2>";

    //Mostrar  dias futuros que coincidem com os dias abertos
    for($i = 0; $i <30; $i++){
        $data = clone $hoje;
        $data->modify("+$i day");
        
        // Pega o nome do dia em português (segunda, terça, etc.)
        $nome_dia = $dias_semana[$data->format('N') - 1]; 

        if(isset($dias_abertos[$nome_dia])){
        echo "<form method='post' action='agendar_cliente_horarios.php' style='display:inline-block; margin: 5px;'>";
        echo "<input type='hidden' name='data' value='" . $data->format('Y-m-d') . "'>";
        echo "<input type='hidden' name='id_servico' value='$id_servico'>";
        echo "<input type='hidden' name='id_funcionario' value='$id_funcionario'>";
        echo "<button type='submit'>" . $data->format('d/m/Y') . " (" . ucfirst($nome_dia) . ")</button>";
        echo "</form>";
        }
   

        
    }
?>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="../css/agendar_cliente_style.css">