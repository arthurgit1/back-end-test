<?php
// Conexão com o banco de dados
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "teste-bw";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Função para validar dados alfanuméricos
function isAlphanumeric($str) {
    return ctype_alnum($str);
}

// Função para validar apenas letras
function isAlpha($str) {
    return ctype_alpha($str);
}

// Função para validar URL
function isValidUrl($url) {
    return filter_var($url, FILTER_VALIDATE_URL) !== false;
}

// Função para validar tamanho dos campos
function isValidLength($str, $min, $max) {
    $len = strlen($str);
    return $len >= $min && $len <= $max;
}

// Função para verificar unicidade no banco de dados
function isUnique($conn, $field, $value) {
    $stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE $field = ?");
    $stmt->bind_param("s", $value);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();
    return $count == 0;
}

// Obtendo dados do formulário (exemplo usando POST)
$username = $_POST['username'];
$password = $_POST['password'];
$name = $_POST['name'];
$lastName = $_POST['lastName'] ?? null;
$profileImageUrl = $_POST['profileImageUrl'] ?? null;
$bio = $_POST['bio'] ?? null;
$gender = $_POST['gender'] ?? 'Not Specified';

// Validando os dados
if (!isValidLength($username, 5, 30) || !isAlphanumeric($username) || !isUnique($conn, 'username', $username)) {
    die("Username inválido ou já existe.");
}

if (!isValidLength($password, 5, 15) || !isUnique($conn, 'password', $password)) {
    die("Password inválido ou já existe.");
}

if (!isValidLength($name, 3, 30) || !isAlpha($name)) {
    die("Nome inválido.");
}

if ($lastName && (!isValidLength($lastName, 3, 30) || !isAlpha($lastName))) {
    die("Sobrenome inválido.");
}

if ($profileUrl && !isValidUrl($profileUrl)) {
    die("URL de perfil inválido.");
}

if ($bio && (!isValidLength($bio, 3, 30) || !isAlpha($bio))) {
    die("Bio inválida.");
}

$validGenders = ['Male', 'Female', 'Not Specified'];
if (!in_array($gender, $validGenders)) {
    $gender = 'Not Specified';
}

// Inserindo os dados no banco de dados
$stmt = $conn->prepare("INSERT INTO users (username, password, name, lastName, profileImageUrl, bio, gender) VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sssssss", $username, $password, $name, $lastName, $profileImageUrl, $bio, $gender);

if ($stmt->execute()) {
    echo "Usuário cadastrado com sucesso!";
} else {
    echo "Erro ao cadastrar usuário: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
