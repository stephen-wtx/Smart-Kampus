<?php
session_start();

// Validação de role
// if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
//     http_response_code(403);
//     exit('Acesso negado');
// }

$user = $_SESSION['user'];
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Painel do Admin</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; }
        nav a { margin-right: 15px; }
    </style>
</head>
<body>

<h1>Painel do Admin</h1>
<p>Bem-vindo, <?= htmlspecialchars($user['name']); ?>!</p>

<nav>
    <a href="/smartkampus/dashboard/dashboard.php">Dashboard Principal</a>
    <a href="/smartkampus/public/logout.php">Logout</a>
</nav>

<hr>

<p>Funcionalidades administrativas irão aparecer aqui.</p>

</body>
</html>
