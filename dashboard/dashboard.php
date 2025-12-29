<?php
session_start();

if (!isset($_SESSION['user'])) {
    header('Location: /smartkampus/public/index.php');
    exit;
}

$user = $_SESSION['user'];
$role = $user['role'];
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Principal</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; }
        img { border-radius: 50%; }
        nav a { margin-right: 15px; }
    </style>
</head>
<body>

<h1>Bem-vindo, <?= htmlspecialchars($user['name']); ?>!</h1>
<p>Email: <?= htmlspecialchars($user['email']); ?></p>

<?php if (!empty($user['picture'])): ?>
    <img src="<?= htmlspecialchars($user['picture']); ?>" width="120">
<?php endif; ?>

<hr>

<nav>
    <a href="/smartkampus/dashboard/dashboard.php">Principal</a>

    <?php if ($role === 'docente'): ?>
        <a href="/smartkampus/dashboard/docente/index.php">Área do Docente</a>
    <?php endif; ?>

    <?php if ($role === 'admin'): ?>
        <a href="/smartkampus/dashboard/admin/index.php">Área do Admin</a>
    <?php endif; ?>

    <a href="/smartkampus/public/logout.php">Logout</a>
</nav>


</body>
</html>
