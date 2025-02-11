<?php

session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit;
}

session_start();

include("db.php");

date_default_timezone_set('America/Recife');

$email = filter_input(INPUT_POST, 'email', FILTER_DEFAULT);
$senha = filter_input(INPUT_POST, 'senha', FILTER_DEFAULT);
$senha_hash = MD5($senha);

$sql = "SELECT * FROM usuarios WHERE usuario_email = '$email' && usuario_senha = '$senha_hash'";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);
$num_result = mysqli_num_rows($result);

if ($num_result > 0) {
	
	$_SESSION['id'] = $row['usuario_id'];
	$_SESSION['nome'] = $row['usuario_nome'];
	$_SESSION['email'] = $row['usuario_email'];
	$_SESSION['status'] = $row['usuario_status'];

	echo "<script>location='dashboard.php';</script>";

} else {
	
	echo "<script>alert('Usuário ou senha incorreto.'); location='index.php';</script>";

}