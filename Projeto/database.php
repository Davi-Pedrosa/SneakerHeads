<?php

$host = "localhost";    
$dbname = "sneaker_head"; 
$user = "postgres";      
$password = "root"; 
$port = "5432";        

try {
    // Criando a string de conexão PDO
    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname;";
    
    // Estabelecendo a conexão usando PDO
    $conn = new PDO($dsn, $user, $password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    
    // Atualiza a senha do administrador para um hash válido
    $senha_admin = "pedro1234";
    $senha_hash = password_hash($senha_admin, PASSWORD_DEFAULT);
    
    $stmt = $conn->prepare("UPDATE Usuarios SET senha_hash = ? WHERE email = 'pedroadm@gmail.com' AND tipo_usuario = 'Admin'");
    $stmt->execute([$senha_hash]);
    
} catch (PDOException $e) {

    die("Erro na conexão com o banco de dados: " . $e->getMessage());
}

?> 