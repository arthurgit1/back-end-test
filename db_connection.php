<?php
$servername = "localhost";
$db_username = "";
$db_password = "";
$dbname = "db_test";

// Criar conexão
$conn = new mysqli($servername, $db_username, $db_password, $dbname);

// Verificar a conexão
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}
?>
