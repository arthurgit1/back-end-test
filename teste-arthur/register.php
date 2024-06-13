<?php
session_start();
include 'db_connection.php';

// Função para validar se uma string contém apenas letras
function apenas_letras($string) {
    return preg_match("/^[a-zA-Zá-úÁ-Ú ]*$/", $string);
}

// Obter dados do formulário
$username = trim($_POST['username']);
$password = trim($_POST['password']);
$name = trim($_POST['name']);
$lastName = trim($_POST['lastName'] ?? '');
$profileUrl = trim($_POST['profileUrl'] ?? '');
$bio = trim($_POST['bio'] ?? '');
$gender = trim($_POST['gender'] ?? 'Not Specified');

// Validação dos dados
$errors = [];

if (empty($username) || !preg_match("/^[a-zA-Z0-9]{5,30}$/", $username)) {
    $errors[] = "Username deve ter entre 5 e 30 caracteres alfanuméricos.";
}

if (empty($password) || strlen($password) < 5 || strlen($password) > 15) {
    $errors[] = "Password deve ter entre 5 e 15 caracteres.";
}

if (empty($name) || strlen($name) < 3 || strlen($name) > 30 || !apenas_letras($name)) {
    $errors[] = "Nome deve ter entre 3 e 30 caracteres e conter apenas letras.";
}

if (!empty($lastName) && (strlen($lastName) < 3 || strlen($lastName) > 30 || !apenas_letras($lastName))) {
    $errors[] = "Sobrenome deve ter entre 3 e 30 caracteres e conter apenas letras.";
}

if (!empty($bio) && (strlen($bio) < 3 || strlen($bio) > 30 || !apenas_letras($bio))) {
    $errors[] = "Bio deve ter entre 3 e 30 caracteres e conter apenas letras.";
}

if (!empty($profileUrl) && !filter_var($profileUrl, FILTER_VALIDATE_URL)) {
    $errors[] = "URL do Linkedin.";
}

if (!in_array($gender, ['Male', 'Female', 'Not Specified'])) {
    $errors[] = "Gênero inválido.";
}

// Verificar se o username já existe
$stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    $errors[] = "Username já existe.";
}

$stmt->close();

if (empty($errors)) {
    // Inserir novo usuário
    $stmt = $conn->prepare("INSERT INTO users (username, password, name, lastName, profileUrl, bio, gender) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);
    $stmt->bind_param("sssssss", $username, $hashed_password, $name, $lastName, $profileUrl, $bio, $gender);
    
    if ($stmt->execute()) {
        $_SESSION['username'] = $username;
        header('Location: welcome.php');
        exit;
    } else {
        $errors[] = "Erro ao registrar usuário: " . $stmt->error;
    }
    
    $stmt->close();
}

$conn->close();

if (!empty($errors)) {
    $_SESSION['errors'] = $errors;
    header('Location: index.php');
    exit;
}
?>
