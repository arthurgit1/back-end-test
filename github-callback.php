<?php
session_start();

// Configurações do GitHub
$client_id = 'SEU_CLIENT_ID';
$client_secret = 'SEU_CLIENT_SECRET';
$redirect_uri = 'http://seu-dominio.com/github-callback.php';

if (isset($_GET['code'])) {
    $code = $_GET['code'];

    // Solicitar um access token
    $token_url = "https://github.com/login/oauth/access_token";
    $post_fields = http_build_query([
        'client_id' => $client_id,
        'client_secret' => $client_secret,
        'code' => $code,
        'redirect_uri' => $redirect_uri
    ]);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $token_url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json']);
    $response = curl_exec($ch);
    curl_close($ch);

    $token_data = json_decode($response, true);
    $access_token = $token_data['access_token'];

    // Solicitar dados do usuário
    $user_url = "https://api.github.com/user";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $user_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: token ' . $access_token,
        'User-Agent: CadastroApp'
    ]);
    $response = curl_exec($ch);
    curl_close($ch);

    $user_data = json_decode($response, true);

    // Conectar ao banco de dados
    $servername = "localhost";
    $db_username = "seu_usuario";
    $db_password = "sua_senha";
    $dbname = "user_database";

    $conn = new mysqli($servername, $db_username, $db_password, $dbname);
    if ($conn->connect_error) {
        die("Falha na conexão: " . $conn->connect_error);
    }

    // Verificar se o usuário já existe no banco de dados
    $username = $user_data['login'];
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 0) {
        // Inserir novo usuário
        $name = $user_data['name'] ?? $username;
        $profileImageUrl = $user_data['avatar_url'];
        $stmt = $conn->prepare("INSERT INTO users (username, name, profileImageUrl) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $name, $profileImageUrl);
        $stmt->execute();
    }

    $stmt->close();
    $conn->close();

    // Definir sessão do usuário
    $_SESSION['username'] = $username;
    header('Location: welcome.php');
    exit;
}
?>
