<?php
session_start();
require_once 'database.php';

// Processar compra de tênis
if (isset($_POST['comprar_tenis'])) {
    try {
        $id_produto = filter_input(INPUT_POST, 'id_produto', FILTER_SANITIZE_NUMBER_INT);
        $stmt = $conn->prepare("UPDATE Produtos SET status = 'indisponível' WHERE id_produto = ?");
        $stmt->execute([$id_produto]);
        $sucesso = "Compra realizada com sucesso!";
    } catch (Exception $e) {
        $erro = "Erro ao processar compra: " . $e->getMessage();
    }
}

// Buscar produtos do banco de dados
$stmt = $conn->query("SELECT p.*, c.nome as categoria_nome 
                     FROM Produtos p 
                     JOIN Categorias c ON p.id_categoria = c.id_categoria 
                     ORDER BY p.id_produto DESC");
$produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>SneakerHeads</title>
    <link rel="stylesheet" href="css/style.css" />
    <style>
      .product form {
        margin-top: 10px;
      }
      
      .btn-comprar {
        background-color: #af3d1a;
        color: white;
        border: none;
        padding: 8px 16px;
        border-radius: 4px;
        cursor: pointer;
        width: 100%;
        font-size: 14px;
      }

      .btn-comprar:hover {
        background-color: #8c3115;
      }

      .btn-comprar:disabled {
        background-color: #cccccc;
        cursor: not-allowed;
      }

      .success-message {
        background-color: #d4edda;
        color: #155724;
        padding: 15px;
        border-radius: 4px;
        margin: 20px auto;
        max-width: 600px;
        text-align: center;
      }

      .error-message {
        background-color: #f8d7da;
        color: #721c24;
        padding: 15px;
        border-radius: 4px;
        margin: 20px auto;
        max-width: 600px;
        text-align: center;
      }
    </style>
  </head>
  <body>
    <header>
      <div><img src="img/fotologo.png" alt="Logo" width="40" /></div>
      <nav>
        <a href="#">Sobre Nós</a>
        <a href="#">Favoritos</a>
        <a href="#">Perfil</a>
        <a href="#">Localização</a>
      </nav>
      <?php if (isset($_SESSION['usuario_id'])): ?>
        <div>
          <span>Olá, <?php echo htmlspecialchars($_SESSION['usuario_nome']); ?></span>
          <?php if ($_SESSION['tipo_usuario'] == 'Admin'): ?>
            <a href="admin/dashboard.php" style="margin-right: 10px; color: #af3d1a;">Admin</a>
          <?php endif; ?>
          <a href="logout.php" style="color: #af3d1a;">Sair</a>
        </div>
      <?php else: ?>
        <div><a href="login.php" style="text-decoration: none; color: rgb(175, 61, 26); font-weight: 700;">Login</a></div>
      <?php endif; ?>
    </header>

    <?php if (isset($sucesso)): ?>
      <div class="success-message"><?php echo $sucesso; ?></div>
    <?php endif; ?>
    
    <?php if (isset($erro)): ?>
      <div class="error-message"><?php echo $erro; ?></div>
    <?php endif; ?>

    <section class="banner">
      <img src="img/vans18.png" alt="VANS X 18 East" class="banner-img" />
    </section>

    <section class="gallery">
      <img src="img/img1.png" alt="img1" />
      <img src="img/img2.png" alt="img2" />
      <img src="img/img3.png" alt="img3" />
      <img src="img/img4.png" alt="img4" />
      <img src="img/img5.png" alt="img5" />
    </section>

    <section class="products">
      <?php foreach ($produtos as $produto): ?>
        <div class="product">
          <?php if ($produto['imagem_url']): ?>
            <img src="<?php echo htmlspecialchars($produto['imagem_url']); ?>" alt="<?php echo htmlspecialchars($produto['nome']); ?>" />
          <?php else: ?>
            <img src="img/no-image.png" alt="Imagem não disponível" />
          <?php endif; ?>
          <p>
            <?php echo htmlspecialchars($produto['nome']); ?><br />
            <?php echo htmlspecialchars($produto['categoria_nome']); ?>
          </p>
          <p class="price">R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?></p>
          
          <?php if ($produto['status'] === 'disponível'): ?>
            <form method="POST" action="" onsubmit="return confirm('Confirma a compra deste tênis?');">
              <input type="hidden" name="id_produto" value="<?php echo $produto['id_produto']; ?>">
              <button type="submit" name="comprar_tenis" class="btn-comprar">Comprar</button>
            </form>
          <?php else: ?>
            <button class="btn-comprar" disabled>Indisponível</button>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
    </section>

    <footer>
      <img src="img/fotologo.png" alt="Logo" width="40" /><br />
      © 2025 SneakerHeads. Todos os direitos reservados.<br />
      <a href="#">https://www.sneakerheads.com/...</a>
    </footer>
  </body>
</html>
