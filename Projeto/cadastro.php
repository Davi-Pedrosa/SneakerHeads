<?php
session_start();
require_once 'database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $senha = $_POST['senha'];
    $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
    $endereco = filter_input(INPUT_POST, 'endereco', FILTER_SANITIZE_STRING);
    $cpf = filter_input(INPUT_POST, 'cpf', FILTER_SANITIZE_STRING);

    try {
        $conn->beginTransaction();

        // Verifica se o email já existe
        $stmt = $conn->prepare("SELECT id_usuario FROM Usuarios WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            throw new Exception("Este email já está cadastrado");
        }

        // Extrai informações do endereço 
        $partes_endereco = explode(',', $endereco);
        $rua = trim($partes_endereco[0] ?? '');
        $numero = trim($partes_endereco[1] ?? '');
        $bairro = trim($partes_endereco[2] ?? '');

        // Insere o usuário
        $stmt = $conn->prepare("INSERT INTO Usuarios (nome, email, tipo_usuario, senha_hash) VALUES (?, ?, 'Cliente', ?)");
        $stmt->execute([$nome, $email, $senha_hash]);
        $id_usuario = $conn->lastInsertId();

        // Insere o endereço
        $stmt = $conn->prepare("INSERT INTO Enderecos (rua, bairro, numero, id_usuario) VALUES (?, ?, ?, ?)");
        $stmt->execute([$rua, $bairro, $numero, $id_usuario]);

        $conn->commit();
        header("Location: login.php?cadastro=sucesso");
        exit();

    } catch (Exception $e) {
        $conn->rollBack();
        $erro = "Erro ao cadastrar: " . $e->getMessage();
    }
}

$base_path = '';
?>
<!DOCTYPE html>
<html lang="pt">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Cadastro - SneakerHeadsss</title>
    <link rel="stylesheet" href="css/cadastro.css" />
    <link rel="stylesheet" href="css/header.css" />
  </head>
  <body>
    <?php include 'includes/header.php'; ?>

    <div class="container">
      <h1>Crie sua conta</h1>
      <p>Faça parte da nossa comunidade sneaker!</p>

      <?php if (isset($erro)): ?>
          <div class="error-message"><?php echo $erro; ?></div>
      <?php endif; ?>

      <form method="POST" action="">
        <input type="text" name="nome" placeholder="Nome completo" required />
        <input type="email" name="email" placeholder="Seu e-mail" required />
        <input
          type="password"
          name="senha"
          placeholder="Senha (mínimo 8 caracteres)"
          required
        />
        <input type="text" name="endereco" placeholder="Endereço completo (Rua, Número, Bairro)" required />
        <input type="text" name="cpf" placeholder="CPF (apenas números)" required />

        <button type="submit">Cadastrar</button>
      </form>

      <div class="login">Já tem uma conta? <a href="login.php">Entrar</a></div>
    </div>
  </body>
</html> 