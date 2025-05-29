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
// Validação
$data = $_POST['data'] ?? null;
$id_servico = $_POST['id_servico'] ?? null;
$id_funcionario = $_POST['id_funcionario'] ?? null;

if (!$data || !$id_servico || !$id_funcionario) {
    echo "Dados inválidos.";
    exit;
}


$pdo = new PDO("sqlite:../../banco/banco.db");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

//1.buscar a duração do serviço
$stmt = $pdo->prepare("SELECT duracao FROM servicos WHERE id = ?");
$stmt->execute([$id_servico]);
$duracao = (int) $stmt->fetchColumn();

if($duracao <= 0){
    echo "Erro: Duração do serviço inválida.";
    exit;
}

//2.Buscar o horario de funcionamento do dia da semana
$dias_semana =['domingo','segunda','terça','quarta','quinta','sexta','sábado'];
$nome_dia = $dias_semana[date('w', strtotime($data))];

$stmt = $pdo->prepare("SELECT hora_inicio, hora_fim FROM horario_funcionamento WHERE dia = ? AND fechado = 0");
$stmt->execute([$nome_dia]);
$horario = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$horario){
    echo "Erro: Horário de funcionamento inválido.";
    exit;
}

// 3. Buscar agendamentos existentes do funcionário nesse dia
$stmt = $pdo->prepare("SELECT hora FROM agendamentos WHERE data = ? AND id_funcionario = ?");
$stmt->execute([$data, $id_funcionario]);
$horarios_ocupados = $stmt->fetchAll(PDO::FETCH_COLUMN);

//4. gerar todos os horarios possiveis
$inicio = new DateTime($horario['hora_inicio']);
$fim = new DateTime($horario['hora_fim']);

// intervalo baseado na duração do serviço
$intervalo = new DateInterval('PT' . $duracao . 'M');
$horarios_disponiveis = [];

while($inicio < $fim){
    $hora_str = $inicio->format('H:i');

    // Só adiciona se não estiver ocupado
    if(!in_array($hora_str, $horarios_ocupados)){
        $horarios_disponiveis[] = $hora_str;
    }

    $inicio->add($intervalo);
}

// 5. Exibir os horários
echo "<h2>Horários disponíveis para " . date('d/m/Y', strtotime($data)) . "</h2>";
if (empty($horarios_disponiveis)){
    echo "<p>Nenhum horário disponível.</p>";
}else {
    foreach ($horarios_disponiveis as $hora) {
        echo "<form method='post' action='agendar_cliente_confirmar.php' style='display:inline-block; margin: 5px;'>";
        echo "<input type='hidden' name='data' value='$data'>";
        echo "<input type='hidden' name='hora' value='$hora'>";
        echo "<input type='hidden' name='id_servico' value='$id_servico'>";
        echo "<input type='hidden' name='id_funcionario' value='$id_funcionario'>";
        echo "<button type='submit'>$hora</button>";
        echo "</form>";
    }
}
?>
<link rel="stylesheet" href="../css/agendar_cliente_style.css">