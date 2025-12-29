<?php
session_start();

// Validação de role
// if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'docente') {
//     http_response_code(403);
//     exit('Acesso negado');
// }

$user = $_SESSION['user'];
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Painel do Docente</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; }
        nav a { margin-right: 15px; }
    </style>
</head>
<body>

<h1>Painel do Docente</h1>
<p>Bem-vindo, <?= htmlspecialchars($user['name']); ?>!</p>

<nav>
    <a href="/smartkampus/dashboard/dashboard.php">Dashboard Principal</a>
    <a href="/smartkampus/public/logout.php">Logout</a>
</nav>

<hr>

<p>Funcionalidades específicas do docente irão aparecer aqui.</p>

</body>
</html>
