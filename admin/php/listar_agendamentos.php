<?php
session_start();

// Verifica se o usuário é admin
if (!isset($_SESSION['usuario_id']) || $_SESSION['nivel'] !== 'admin') {
    header("Location: ../../index.php");
    exit();
}


try {
    $pdo = new PDO("sqlite:../../banco/banco.db");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Buscar agendamentos futuros com JOIN nas tabelas
    $stmt = $pdo->query("
     SELECT 
        a.id, 
        a.data, 
        a.hora,
        s.nome AS servico,
        COALESCE(c.nome, '') || ' ' || COALESCE(c.sobrenome, '') AS cliente,
        COALESCE(f.nome, '') || ' ' || COALESCE(f.sobrenome, '') AS funcionario
    FROM agendamentos a
    JOIN usuarios c ON a.id_usuario = c.id
    JOIN usuarios f ON a.id_funcionario = f.id
    JOIN servicos s ON a.id_servico = s.id
    WHERE a.data >= date('now')
    ORDER BY a.data, a.hora
    ");
    $agendamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erro ao buscar agendamentos: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agendamentos</title>
    <link rel="stylesheet" href="../css/listar_agendamentos_style.css">
</head>
<body>
    <nav>
        <a href="../admin.php">
            <button>Gerencimento Estabelecimento</button>
        </a>
    </nav>
    <div class="container">
    <h1>Lista de Agendamentos</h1>
     <?php if (empty($agendamentos)): ?>
        <p>Nenhum agendamento futuro encontrado.</p>
    <?php else: ?>

        <?php if (isset($_SESSION['mensagem'])): ?>
            <p style="color: green;"><?= $_SESSION['mensagem'] ?></p>
            <?php unset($_SESSION['mensagem']); ?>
        <?php endif; ?>
        <table border="1" cellpadding="5">
            <thead>
                <tr>
                    <th>Data</th>
                    <th>Hora</th>
                    <th>Cliente</th>
                    <th>Funcionário</th>
                    <th>Serviço</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($agendamentos as $ag): ?>
                    <tr>
                        <td><?= htmlspecialchars($ag['data']) ?></td>
                        <td><?= htmlspecialchars($ag['hora']) ?></td>
                        <td><?= htmlspecialchars($ag['cliente']) ?></td>
                        <td><?= htmlspecialchars($ag['funcionario']) ?></td>
                        <td><?= htmlspecialchars($ag['servico']) ?></td>
                       <td>
                            <form action="cancelar_cliente_agendamento.php" method="post" onsubmit="return confirm('Cancelar este agendamento?');">
                                <input type="hidden" name="id_agendamento" value="<?= $ag['id'] ?>">
                                <button type="submit">Cancelar</button>
                            </form>
                        <form action="editar_cliente_agendamento.php" method="get" style="display:inline;">
                            <input type="hidden" name="id_agendamento" value="<?= $ag['id'] ?>">
                            <button type="submit">Editar</button>
                        </form>
                        </td>
                    </tr>
                    
                <?php endforeach; ?>
            </tbody>
        </table>
        
    <?php endif; ?>
    </div>

</body>
</html>