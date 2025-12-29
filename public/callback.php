<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();

require_once __DIR__ . '/../config/google.php';
require_once __DIR__ . '/../config/database.php';

// 1. Verifica se o código OAuth chegou
if (!isset($_GET['code'])) {
    exit('Código OAuth não recebido');
}

// 2. Troca o code pelo token
$token = $client->fetchAccessTokenWithAuthCode($_GET['code']);

if (isset($token['error'])) {
    exit('Erro OAuth: ' . $token['error']);
}

$client->setAccessToken($token);

// 3. Obtem informações do utilizador
$oauth = new Google_Service_Oauth2($client);
$userInfo = $oauth->userinfo->get();

$email = $userInfo->email;

// 4. Valida domínio institucional
if (!preg_match('/@ucm\.ac\.mz$/', $email)) {
    session_destroy();
    exit('Acesso negado. Use email institucional @ucm.ac.mz');
}

// 5. Procura utilizador na BD
$stmt = $conn->prepare("SELECT id, role FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // Primeiro login → auto-registo
    $role = 'estudante';

    $stmt = $conn->prepare("
        INSERT INTO users (oauth_provider, oauth_uid, name, email, role, picture)
        VALUES ('google', ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param(
        "sssss",
        $userInfo->id,
        $userInfo->name,
        $email,
        $role,
        $userInfo->picture
    );
    $stmt->execute();
    $userId = $stmt->insert_id;
} else {
    // Utilizador existente
    $user = $result->fetch_assoc();
    $userId = $user['id'];
    $role   = $user['role'];

    // Atualiza last_login
    $stmt = $conn->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
}

// 6. Cria sessão segura
session_regenerate_id(true);

$_SESSION['user'] = [
    'id'    => $userId,
    'name'  => $userInfo->name,
    'email' => $email,
    'role'  => $role,
    'picture' => $userInfo->picture
];

// 7. Redireciona para dashboard principal (todos acessam)
header('Location: /smartkampus/dashboard/dashboard.php');
exit;
