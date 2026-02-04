<?php
session_start();
require_once __DIR__ . '/../../../../config/database.php';
if (!isset($_SESSION['user'])) {
    header('Location: /smartkampus/public/index.php');
    exit;
}
$user = $_SESSION['user'];

$result = $conn->query("
    SELECT *
    FROM horarios
    ORDER BY dia_semana, hora_inicio
");
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Editar Horários</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; }
        table { border-collapse: collapse; width: 100%; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background: #f4f4f4; }
        .msg { margin-top: 20px; color: #555; }
        a.btn {
            padding: 6px 10px;
            background: #2563eb;
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
            font-size: 14px;
        }
        a.btn:hover {
            background: #1d4ed8;
        }
    </style>
</head>
<body>

<nav>
    <a href="/smartkampus/dashboard/admin/index.php">← Voltar</a>
    |
    <a href="/smartkampus/dashboard/dashboard.php">Dashboard Principal</a>
</nav>

<h2>Editar Horários</h2>

<?php if ($result->num_rows === 0): ?>
    <p class="msg">Sem horários disponíveis agora.</p>
<?php else: ?>

<table>
    <tr>
        <th>Dia</th>
        <th>Curso</th>
        <th>Ano</th>
        <th>Semestre</th>
        <th>Disciplina</th>
        <th>Turno</th>
        <th>Sala</th>
        <th>Horas</th>
        <th>Ação</th>
    </tr>

    <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['dia_semana']) ?></td>
            <td><?= htmlspecialchars($row['curso']) ?></td>
            <td><?= htmlspecialchars($row['ano']) ?></td>
            <td><?= htmlspecialchars($row['semestre']) ?></td>
            <td><?= htmlspecialchars($row['disciplina']) ?></td>
            <td><?= htmlspecialchars($row['turno']) ?></td>
            <td><?= htmlspecialchars($row['sala']) ?></td>
            <td>
                <?= htmlspecialchars(date('H:i', strtotime($row['hora_inicio']))) ?>
                –
                <?= htmlspecialchars(date('H:i', strtotime($row['hora_fim']))) ?>

            </td>
            <td>
                <a class="btn" href="editar_horario.php?id=<?= $row['id'] ?>">
                    Editar
                </a>
            </td>
        </tr>
    <?php endwhile; ?>

</table>

<?php endif; ?>

</body>
</html>
