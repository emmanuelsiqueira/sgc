<?php

//$host = 'ec2-54-227-63-115.compute-1.amazonaws.com';
$host = 'localhost';
$db = 'sgc';
//$user = 'nerdghost';
$user = 'root';
//$pass = 'Apropos742@#$';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro ao conectar ao banco de dados: " . $e->getMessage());
}
?>