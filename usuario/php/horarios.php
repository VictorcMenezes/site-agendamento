<?php
session_start();

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

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Horários Disponíveis</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../css/agendar_style.css">
</head>
<body>
    <div class="container">
        <h2>Horários disponíveis para <?= date('d/m/Y', strtotime($data)) ?></h2>

        <?php if (empty($horarios_disponiveis)): ?>
            <p>Nenhum horário disponível.</p>
        <?php else: ?>
            <div class="botoes-horarios">
                <?php foreach ($horarios_disponiveis as $hora): ?>
                    <form method="post" action="confirmar_agendamento.php">
                        <input type="hidden" name="data" value="<?= $data ?>">
                        <input type="hidden" name="hora" value="<?= $hora ?>">
                        <input type="hidden" name="id_servico" value="<?= $id_servico ?>">
                        <input type="hidden" name="id_funcionario" value="<?= $id_funcionario ?>">
                        <button type="submit"><?= $hora ?></button>
                    </form>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>