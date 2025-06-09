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

echo "<table class='calendario-tabela'>";
echo "<thead><tr>";
foreach ($dias_semana as $nome_dia) {
    echo "<th>" . ucfirst($nome_dia) . "</th>";
}
echo "</tr></thead>";
echo "<tbody>";


$data = clone $primeiro_dia;
$data->modify('+1 month'); // para o loop funcionar corretamente
$data_fim = new DateTime("$ano-$mes-{$ultimo_dia->format('d')}");
$hoje = new DateTime(); // data atual zerada para evitar problemas com horas
$hoje->setTime(0, 0);

$hoje = new DateTime();
$hoje->setTime(0, 0);

$dia_cursor = clone $primeiro_dia;
$dia_cursor->modify('first day of this month');

// Descobre em que dia da semana começa (0 = domingo)
$inicio_semana = $dia_cursor->format('w');

echo "<tr>";

// Preenche os dias vazios antes do primeiro dia do mês
for ($i = 0; $i < $inicio_semana; $i++) {
    echo "<td></td>";
}

while ($dia_cursor <= $data_fim) {
    $nome_dia = strtolower($dias_semana[$dia_cursor->format('w')]);

    // Verifica se precisa abrir nova linha (domingo)
    if ($dia_cursor->format('w') == 0 && $dia_cursor != $primeiro_dia) {
        echo "</tr><tr>";
    }

    $data_formatada = $dia_cursor->format('d');

    // Verifica se o dia é anterior a hoje
    if ($dia_cursor < $hoje || !isset($dias_abertos[$nome_dia])) {
        echo "<td><button class='dia-botao fechado' onclick='alert(\"Indisponível\")'>";
        echo "$data_formatada";
        echo "</button></td>";
    } else {
        echo "<td>";
        echo "<form method='post' action='horarios.php' class='dia-form'>";
        echo "<input type='hidden' name='data' value='" . $dia_cursor->format('Y-m-d') . "'>";
        echo "<input type='hidden' name='id_servico' value='$id_servico'>";
        echo "<input type='hidden' name='id_funcionario' value='$id_funcionario'>";
        echo "<button type='submit' class='dia-botao disponivel'>$data_formatada</button>";
        echo "</form>";
        echo "</td>";
    }

    $dia_cursor->modify('+1 day');
}

// Preenche os dias restantes da última semana com células vazias
$dia_semana_fim = $dia_cursor->format('w');
if ($dia_semana_fim > 0) {
    for ($i = $dia_semana_fim; $i < 7; $i++) {
        echo "<td></td>";
    }
}

echo "</tr>";
echo "</tbody></table>";

?>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../css/calendario_style.css">

    