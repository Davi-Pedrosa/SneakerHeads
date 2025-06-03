<?php
require_once 'check_admin.php';
require_once '../database.php';

// Buscar categorias
$stmt = $conn->query("SELECT id_categoria, nome FROM Categorias");
$categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Processar exclusão de tênis
if (isset($_POST['excluir_tenis'])) {
    try {
        $id_produto = filter_input(INPUT_POST, 'id_produto', FILTER_SANITIZE_NUMBER_INT);
        $stmt = $conn->prepare("DELETE FROM Produtos WHERE id_produto = ?");
        $stmt->execute([$id_produto]);
        $sucesso = "Tênis excluído com sucesso!";
    } catch (Exception $e) {
        $erro = "Erro ao excluir tênis: " . $e->getMessage();
    }
}

// Processar atualização de tênis
if (isset($_POST['atualizar_tenis'])) {
    try {
        $id_produto = filter_input(INPUT_POST, 'id_produto', FILTER_SANITIZE_NUMBER_INT);
        $nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_STRING);
        $preco = filter_input(INPUT_POST, 'preco', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $categoria = filter_input(INPUT_POST, 'categoria', FILTER_SANITIZE_NUMBER_INT);
        $status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_STRING);

        $stmt = $conn->prepare("UPDATE Produtos SET nome = ?, preco = ?, status = ?, id_categoria = ? WHERE id_produto = ?");
        $stmt->execute([$nome, $preco, $status, $categoria, $id_produto]);
        $sucesso = "Tênis atualizado com sucesso!";
    } catch (Exception $e) {
        $erro = "Erro ao atualizar tênis: " . $e->getMessage();
    }
}

// Processar o formulário de cadastro de tênis
if (isset($_POST['cadastrar_tenis'])) {
    try {
        $nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_STRING);
        $preco = filter_input(INPUT_POST, 'preco', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $categoria = filter_input(INPUT_POST, 'categoria', FILTER_SANITIZE_NUMBER_INT);
        $status = 'disponível';

        // Processar upload da imagem
        if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] == 0) {
            $upload_dir = "../uploads/";
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            $file_extension = pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION);
            $new_filename = uniqid() . '.' . $file_extension;
            $upload_path = $upload_dir . $new_filename;

            if (move_uploaded_file($_FILES['imagem']['tmp_name'], $upload_path)) {
                $imagem_url = 'uploads/' . $new_filename;
            } else {
                throw new Exception("Erro ao fazer upload da imagem");
            }
        } else {
            $imagem_url = null;
        }

        // Inserir o produto
        $stmt = $conn->prepare("INSERT INTO Produtos (nome, preco, status, imagem_url, id_categoria) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$nome, $preco, $status, $imagem_url, $categoria]);

        $sucesso = "Tênis cadastrado com sucesso!";
    } catch (Exception $e) {
        $erro = "Erro ao cadastrar tênis: " . $e->getMessage();
    }
}

// Buscar produtos cadastrados
$stmt = $conn->query("SELECT p.*, c.nome as categoria_nome FROM Produtos p 
                     JOIN Categorias c ON p.id_categoria = c.id_categoria 
                     ORDER BY p.id_produto DESC");
$produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);

$base_path = '../';
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - SneakerHeads</title>
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="../css/header.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="container">
        <div class="card">
            <h4>Cadastrar Novo Tênis</h4>
            
            <?php if (isset($sucesso)): ?>
                <div class="success-message"><?php echo $sucesso; ?></div>
            <?php endif; ?>
            
            <?php if (isset($erro)): ?>
                <div class="error-message"><?php echo $erro; ?></div>
            <?php endif; ?>

            <form method="POST" action="" enctype="multipart/form-data" class="compact-form">
                <div class="form-row">
                    <div class="form-group">
                        <label for="nome">Nome do Tênis</label>
                        <input type="text" id="nome" name="nome" required>
                    </div>
                    <div class="form-group">
                        <label for="categoria">Categoria</label>
                        <select id="categoria" name="categoria" required>
                            <?php foreach ($categorias as $categoria): ?>
                                <option value="<?php echo $categoria['id_categoria']; ?>">
                                    <?php echo htmlspecialchars($categoria['nome']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="preco">Preço</label>
                        <input type="number" step="0.01" id="preco" name="preco" required>
                    </div>
                    <div class="form-group">
                        <label for="imagem">Imagem</label>
                        <input type="file" id="imagem" name="imagem" accept="image/*" required>
                    </div>
                </div>

                <button type="submit" name="cadastrar_tenis">Cadastrar Tênis</button>
            </form>
        </div>

        <div class="card">
            <h4>Tênis Cadastrados</h4>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Categoria</th>
                            <th>Preço</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($produtos as $produto): ?>
                            <tr>
                                <td><?php echo $produto['id_produto']; ?></td>
                                <td><?php echo htmlspecialchars($produto['nome']); ?></td>
                                <td><?php echo htmlspecialchars($produto['categoria_nome']); ?></td>
                                <td>R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?></td>
                                <td><?php echo htmlspecialchars($produto['status']); ?></td>
                                <td class="actions">
                                    <button onclick="editarTenis(<?php echo htmlspecialchars(json_encode($produto)); ?>)" class="btn-edit">Editar</button>
                                    <form method="POST" action="" style="display: inline;">
                                        <input type="hidden" name="id_produto" value="<?php echo $produto['id_produto']; ?>">
                                        <button type="submit" name="excluir_tenis" class="btn-delete" onclick="return confirm('Tem certeza que deseja excluir este tênis?')">Excluir</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal de Edição -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h4>Editar Tênis</h4>
            <form method="POST" action="">
                <input type="hidden" id="edit_id_produto" name="id_produto">
                <div class="form-group">
                    <label for="edit_nome">Nome</label>
                    <input type="text" id="edit_nome" name="nome" required>
                </div>
                <div class="form-group">
                    <label for="edit_categoria">Categoria</label>
                    <select id="edit_categoria" name="categoria" required>
                        <?php foreach ($categorias as $categoria): ?>
                            <option value="<?php echo $categoria['id_categoria']; ?>">
                                <?php echo htmlspecialchars($categoria['nome']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="edit_preco">Preço</label>
                    <input type="number" step="0.01" id="edit_preco" name="preco" required>
                </div>
                <div class="form-group">
                    <label for="edit_status">Status</label>
                    <select id="edit_status" name="status" required>
                        <option value="disponível">Disponível</option>
                        <option value="indisponível">Indisponível</option>
                    </select>
                </div>
                <button type="submit" name="atualizar_tenis">Atualizar</button>
            </form>
        </div>
    </div>

    <script>
        // Modal
        var modal = document.getElementById("editModal");
        var span = document.getElementsByClassName("close")[0];

        function editarTenis(produto) {
            modal.style.display = "block";
            document.getElementById("edit_id_produto").value = produto.id_produto;
            document.getElementById("edit_nome").value = produto.nome;
            document.getElementById("edit_categoria").value = produto.id_categoria;
            document.getElementById("edit_preco").value = produto.preco;
            document.getElementById("edit_status").value = produto.status;
        }

        span.onclick = function() {
            modal.style.display = "none";
        }

        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>
</body>
</html> 