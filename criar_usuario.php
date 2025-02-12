<?php
require 'db.php';

$email = 'admin@email.com';
$senha = password_hash('admin', PASSWORD_DEFAULT);

$stmt = $pdo->prepare("INSERT INTO usuarios (usuario_email, usuario_senha) VALUES (?, ?)");
$stmt->execute([$email, $senha]);

echo "Usuário criado com sucesso!";
?>