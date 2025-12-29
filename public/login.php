<?php
require_once __DIR__ . '/../config/google.php';

// Se já estiver autenticado
if (isset($_SESSION['user'])) {
    header('Location: dashboard.php');
    exit;
}

$loginUrl = $client->createAuthUrl();
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Login Institucional</title>
</head>
<body>

<h2>Login Institucional UCM</h2>

<a href="<?= htmlspecialchars($loginUrl); ?>">
    <button>Entrar com conta institucional</button>
</a>

</body>
</html>
