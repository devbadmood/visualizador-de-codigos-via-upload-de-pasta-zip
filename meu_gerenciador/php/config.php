<?php
$host = 'localhost';
$db = 'gerenciador';
$user = 'root';
$pass = ''; // ou sua senha do MySQL

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}
?>
