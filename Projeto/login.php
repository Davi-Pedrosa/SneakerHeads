<?php
session_start();
require_once 'database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $senha = $_POST['senha'];

    try {
        $stmt = $conn->prepare("SELECT id_usuario, nome, tipo_usuario, senha_hash FROM Usuarios WHERE email = ?");
        $stmt->execute([$email]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario && password_verify($senha, $usuario['senha_hash'])) {
            $_SESSION['usuario_id'] = $usuario['id_usuario'];
            $_SESSION['usuario_nome'] = $usuario['nome'];
            $_SESSION['tipo_usuario'] = $usuario['tipo_usuario'];

            if ($usuario['tipo_usuario'] == 'Admin') {
                header("Location: admin/dashboard.php");
            } else {
                header("Location: index.php");
            }
            exit();
        } else {
            $erro = "Email ou senha inválidos";
        }
    } catch (PDOException $e) {
        $erro = "Erro ao fazer login: " . $e->getMessage();
    }
}

$base_path = '';
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SneakerHeadsss</title>
    <link rel="stylesheet" href="css/login.css">
    <link rel="stylesheet" href="css/header.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container">
        <div class="login-box">
            <h1>Bem-vindo!</h1>
            <p>Entre na sua conta para continuar.</p>

            <?php if (isset($erro)): ?>
                <div class="error-message"><?php echo $erro; ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <input type="email" name="email" placeholder="Seu email" required>
                <input type="password" name="senha" placeholder="Sua senha" required>

                <div class="options">
                    <label><input type="checkbox"> Lembrar-me</label>
                    <a href="#">Esqueceu a senha?</a>
                </div>

                <div class="buttons">
                    <button type="submit" class="login-btn">Login</button>
                    <button type="button" class="signup-btn" onclick="window.location.href='cadastro.php'">Cadastre-se</button>
                </div>
            </form>

            <div class="social">
                Siga-nos:
                <a href="#">Facebook</a> |
                <a href="#">Instagram</a> |
                <a href="#">Twitter</a>
            </div>
        </div>
    </div>
</body>
</html> 