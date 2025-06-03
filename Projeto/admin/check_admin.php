<?php
session_start();

// Verifica se o usuário está logado e é administrador
if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] !== 'Admin') {
    header("Location: ../login.php");
    exit();
}
?> 