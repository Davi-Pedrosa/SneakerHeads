<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<header>
    <div><img src="<?php echo $base_path; ?>img/fotologo.png" alt="Logo" width="40" /></div>
    <nav>
        <a href="<?php echo $base_path; ?>index.php">Home</a>
        <a href="#">Sobre Nós</a>
        <a href="#">Favoritos</a>
        <a href="#">Perfil</a>
        <a href="#">Localização</a>
    </nav>
    <div class="user-area">
        <?php if (isset($_SESSION['usuario_id'])): ?>
            <div class="user-info">
                <span class="welcome-text">Olá, <strong><?php echo htmlspecialchars($_SESSION['usuario_nome']); ?></strong></span>
                <?php if ($_SESSION['tipo_usuario'] == 'Admin'): ?>
                    <a href="<?php echo $base_path; ?>admin/dashboard.php" class="admin-link">Admin</a>
                <?php endif; ?>
                <a href="<?php echo $base_path; ?>logout.php" class="logout-link">Sair</a>
            </div>
        <?php else: ?>
            <a href="<?php echo $base_path; ?>login.php" class="login-link">Login</a>
        <?php endif; ?>
    </div>
</header> 