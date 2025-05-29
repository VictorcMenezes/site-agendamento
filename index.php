<?php session_start(); ?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>pagina inicial</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="./usuario/css/style.css">
</head>        
<body>
     <!-- Cabeçalho -->
      <main class="main-wrapper">
  <header>
    <h1>Bem-vindo ao Salão de Beleza</h1>
  </header>
    <nav class="nav-bar">
        <?php if(isset($_SESSION['nome'])): ?>
        <p>Olá, <?php echo $_SESSION['nome']; ?></p>
        <a href="./login-register/php/logout.php" onclick="return confirm('Tem certeza que deseja sair?');">
            <button class="btn"><i class="fas fa-sign-out-alt"></i> Sair</button>
        </a>
        <?php else: ?>
        <a href="./login-register/php/login.php">
            <button class="btn"><i class="fas fa-sign-in-alt"></i> Login</button>
        </a>
        <?php endif; ?>
        <a href="./usuario/php/agendar.php">
            <button class="btn"><i class="fas fa-calendar-check"></i> Agendar</button>
        </a>
        </nav>

    <!-- Conteúdo principal -->
    <div class="container">
        <div class="card">
        <h2>Agende seu horário com facilidade</h2>
        <p>Escolha o serviço, selecione o profissional e veja os horários disponíveis!</p>
        <a href="./usuario/php/agendar.php">
            <button class="btn">Começar Agendamento</button>
        </a>
        </div>
    </div>
    <br><br>
     <footer class="footer">
    <p>Desenvolvido por <strong>Victor Menezes</strong> &copy; <?= date("Y") ?></p>
</footer>
    </main>
   
</body>
</html>