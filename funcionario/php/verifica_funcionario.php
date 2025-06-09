<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['usuario_id']) || $_SESSION['nivel'] !== 'funcionario') {
    header('Location: ../login-register/php/login.php');
    exit();
}
