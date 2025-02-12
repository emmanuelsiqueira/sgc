<?php

session_start();

require 'db.php';

date_default_timezone_set('America/Recife');

$ip = $_SERVER['REMOTE_ADDR'];
$limite_tentativas = 3; // Número máximo de tentativas antes do bloqueio
$tempo_bloqueio = 15 * 60; // 15 minutos de bloqueio

// Verificar tentativas de login
$stmt = $pdo->prepare("SELECT tentativas, ultima_tentativa FROM logins_falhos WHERE ip = ?");
$stmt->execute([$ip]);
$registro = $stmt->fetch();

if ($registro) {
    $tentativas = $registro['tentativas'];
    $ultima_tentativa = strtotime($registro['ultima_tentativa']);

    if ($tentativas >= $limite_tentativas && (time() - $ultima_tentativa) < $tempo_bloqueio) {
		echo "<script>alert('Tentativas excedidas de login. Seu acesso está temporariamente bloqueado. Tente novamente mais tarde.'); location='index.php'; </script>";
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $senha = $_POST['senha'];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('E-mail inválido.'); location='index.php'; </script>";
    }

    // Buscar usuário no banco de dados
    $stmt = $pdo->prepare("SELECT usuario_id, usuario_senha, usuario_status FROM usuarios WHERE usuario_email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($senha, $user['usuario_senha'])) {
        // Limpar registros de tentativas falhas
        $stmt = $pdo->prepare("DELETE FROM logins_falhos WHERE ip = ?");
        $stmt->execute([$ip]);

        // Regenerar sessão para evitar fixação
        session_regenerate_id(true);
        $_SESSION['usuario_id'] = $user['usuario_id'];
        $_SESSION['usuario_status'] = $user['usuario_status'];

        header("Location: dashboard.php");
        exit;
    } else {
        // Atualizar tentativas de login
        if ($registro) {
            $stmt = $pdo->prepare("UPDATE logins_falhos SET tentativas = tentativas + 1, ultima_tentativa = NOW() WHERE ip = ?");
        } else {
            $stmt = $pdo->prepare("INSERT INTO logins_falhos (ip, tentativas) VALUES (?, 1)");
        }
        $stmt->execute([$ip]);
		echo "<script>alert('E-mail ou senha inválidos'); location='index.php'; </script>";
    }
}
?>
