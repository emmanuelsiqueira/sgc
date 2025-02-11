<?php
session_start();
require 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $senha = $_POST['senha'];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("E-mail inválido.");
    }

    // Verificar se o usuário existe
    $stmt = $pdo->prepare("SELECT usuario_id,usuario_email, usuario_senha FROM usuarios WHERE usuario_email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($senha, $user['usuario_senha'])) {
        // Regenerar a sessão para evitar fixação de sessão
        session_regenerate_id(true);
        
        $_SESSION['usuario_id'] = $user['usuario_id'];
        header("Location: dashboard.php");
        exit;
    } else {
        var_dump($senha);
        var_dump(password_verify($senha, $user['usuario_senha']));
        echo "E-mail ou senha inválidos.";
    }
}
?>