<!-- <?php
session_start();

if (!isset($_SESSION['user'])) {
    header('Location: /smartkampus/public/index.php');
    exit;
}

$user = $_SESSION['user'];
$role = $user['role'];
?>
 -->

 <?php
session_start();
require_once __DIR__ . '/../config/database.php';

if (!isset($_SESSION['user'])) {
    header('Location: /smartkampus/public/index.php');
    exit;
}

$user = $_SESSION['user'];
$role = $user['role'];

$result = $conn->query("
    SELECT 
        dia_semana,
        curso,
        ano,
        semestre,
        disciplina,
        turno,
        sala,
        hora_inicio,
        hora_fim
    FROM horarios
    ORDER BY 
        FIELD(dia_semana,'Segunda','Terça','Quarta','Quinta','Sexta','Sábado'),
        hora_inicio
");
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

<h2>Horários Disponíveis</h2>

<table border="1" cellpadding="8" cellspacing="0" width="100%">
    <thead>
        <tr>
            <th>Dia</th>
            <th>Curso</th>
            <th>Ano</th>
            <th>Semestre</th>
            <th>Disciplina</th>
            <th>Turno</th>
            <th>Sala</th>
            <th>Horário</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['dia_semana']) ?></td>
                    <td><?= htmlspecialchars($row['curso']) ?></td>
                    <td><?= (int)$row['ano'] ?>º</td>
                    <td><?= htmlspecialchars($row['semestre']) ?></td>
                    <td><?= htmlspecialchars($row['disciplina']) ?></td>
                    <td><?= htmlspecialchars($row['turno']) ?></td>
                    <td><?= htmlspecialchars($row['sala']) ?></td>
                    <td>
                        <?= substr($row['hora_inicio'], 0, 5) ?>
                        -
                        <?= substr($row['hora_fim'], 0, 5) ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="8" style="text-align:center;">
                    Nenhum horário registado.
                </td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>



</body>
</html>
