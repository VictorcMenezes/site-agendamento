<?php
    session_start();

    //verificar se os dados foram enviados corretamente

    $id_servico = $_POST['id_servico'] ?? $_GET['id_servico'] ?? null;
    $id_funcionario = $_POST['id_funcionario'] ?? $_GET['id_funcionario'] ?? null;
    $mes = $_POST['mes'] ?? $_GET['mes'] ?? date('m');
    $ano = $_POST['ano'] ?? $_GET['ano'] ?? date('Y');

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
    $dias_semana = ['domingo', 'segunda', 'terça', 'quarta', 'quinta', 'sexta', 'sábado'];
    $nome_meses = ['janeiro','fevereiro','março','abril','maio','junho','julho','agosto','setembro','outubro','novembro','dezembro'];

// Calcula o primeiro e último dia do mês selecionado
$primeiro_dia = new DateTime("$ano-$mes-01");
$ultimo_dia = clone $primeiro_dia;
$ultimo_dia->modify('last day of this month');

// Botões para trocar mês (copiando para não modificar o original)
$mes_anterior = (clone $primeiro_dia)->modify('-1 month');
$prox_mes = (clone $primeiro_dia)->modify('+1 month');

// URLs
$prox_mes_url = "?mes={$prox_mes->format('m')}&ano={$prox_mes->format('Y')}&id_servico=$id_servico&id_funcionario=$id_funcionario";
$anterior_url = "?mes={$mes_anterior->format('m')}&ano={$mes_anterior->format('Y')}&id_servico=$id_servico&id_funcionario=$id_funcionario";

echo "<h2>";
echo "<a href='$anterior_url' class='seta'>&larr;</a> ";
echo ucfirst($nome_meses[intval($mes)-1]) . " de $ano";
echo " <a href='$prox_mes_url' class='seta'>&rarr;</a>";
echo "</h2>";

echo "<div class='calendario-grid'>";

$data = clone $primeiro_dia;
$data->modify('+1 month'); // para o loop funcionar corretamente
$data_fim = new DateTime("$ano-$mes-{$ultimo_dia->format('d')}");

while ($primeiro_dia <= $data_fim) {
    $nome_dia = $dias_semana[$primeiro_dia->format('w')];
    $data_formatada = $primeiro_dia->format('d');
    $nome_dia_uc = ucfirst($nome_dia);

    if (isset($dias_abertos[$nome_dia])) {
        echo "<form method='post' action='horarios.php' class='dia-form'>";
        echo "<input type='hidden' name='data' value='" . $primeiro_dia->format('Y-m-d') . "'>";
        echo "<input type='hidden' name='id_servico' value='$id_servico'>";
        echo "<input type='hidden' name='id_funcionario' value='$id_funcionario'>";
        echo "<button type='submit' class='dia-botao disponivel'>";
        echo "<strong>$data_formatada</strong><br><small>$nome_dia_uc</small>";
        echo "</button>";
        echo "</form>";
    } else {
        echo "<button class='dia-botao fechado' onclick='alert(\"Dia fechado para agendamento\")'>";
        echo "<strong>$data_formatada</strong><br><small>$nome_dia_uc</small>";
        echo "</button>";
    }

    $primeiro_dia->modify('+1 day');
}

echo "</div>";
?>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../css/calendario_style.css">

    