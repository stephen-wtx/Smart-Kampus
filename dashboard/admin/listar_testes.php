<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
require_once __DIR__ . "/../../config/database.php";

/*
// Proteção (ativar depois)
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: /smartkampus/dashboard/dashboard.php');
    exit;
}
*/

// Buscar testes
$sql = "SELECT * FROM testes ORDER BY data, hora_inicio";
$resultado = $conn->query($sql);
$total = $resultado->num_rows;
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Listar Testes</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 30px;
            background: #f4f6f8;
        }

        h2 { margin-bottom: 20px; }

        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }

        th {
            background: #2b6cb0;
            color: #fff;
        }

        a {
            text-decoration: none;
            font-size: 14px;
            color: #fff;
            padding: 6px 12px;
            border-radius: 4px;
        }

        .btn-editar { background: #38a169; }
        .btn-editar:hover { background: #2f855a; }

        .btn-excluir { background: #e53e3e; }
        .btn-excluir:hover { background: #c53030; }

        .mensagem {
            background: #fff3cd;
            padding: 15px;
            border: 1px solid #ffeeba;
            color: #856404;
            border-radius: 5px;
            max-width: 400px;
        }

        nav { margin-bottom: 20px; }
        nav a { margin-right: 15px; color: #2b6cb0; }
    </style>
</head>

<?php if (!empty($_SESSION['sucesso'])): ?>
<div style="background:#e6fffa;border:1px solid #38b2ac;padding:12px;margin-bottom:20px;color:#065f46;border-radius:6px;">
    <?= htmlspecialchars($_SESSION['sucesso']); ?>
</div>
<?php unset($_SESSION['sucesso']); endif; ?>

<?php if (!empty($_SESSION['erro'])): ?>
<div style="background:#ffe6e6;border:1px solid #f00;padding:12px;margin-bottom:20px;color:#900;border-radius:6px;">
    <?= htmlspecialchars($_SESSION['erro']); ?>
</div>
<?php unset($_SESSION['erro']); endif; ?>

<body>

<nav>
    <a href="/smartkampus/dashboard/admin/index.php">Página Inicial</a>
    <a href="/smartkampus/dashboard/dashboard.php">Dashboard Principal</a>
    <a href="/smartkampus/public/logout.php">Logout</a>
</nav>

<h2>Testes Criados</h2>

<?php if ($total === 0): ?>

<div class="mensagem">
    Sem testes cadastrados
</div>

<?php else: ?>

<table>
<thead>
<tr>
    <th>Curso</th>
    <th>Disciplina</th>
    <th>Ano</th>
    <th>Semestre</th>
    <th>Turno</th>
    <th>Sala</th>
    <th>Dia</th>
    <th>Data</th>
    <th>Hora</th>
    <th>Duração</th>
    <th>Ação</th>
</tr>
</thead>

<tbody>
<?php while ($linha = $resultado->fetch_assoc()): ?>
<tr>
    <td><?= htmlspecialchars($linha['curso']); ?></td>
    <td><?= htmlspecialchars($linha['disciplina']); ?></td>
    <td><?= htmlspecialchars($linha['ano']); ?></td>
    <td><?= htmlspecialchars($linha['semestre']); ?></td>
    <td><?= htmlspecialchars($linha['turno']); ?></td>
    <td><?= htmlspecialchars($linha['sala']); ?></td>
    <td><?= htmlspecialchars($linha['dia_semana']); ?></td>
    <td>
    <?= !empty($linha['data']) 
        ? date('d-m-Y', strtotime($linha['data'])) 
        : '-' ?>
    </td>

    <td>
        <?= date('H:i', strtotime($linha['hora_inicio'])); ?> -
        <?= date('H:i', strtotime($linha['hora_fim'])); ?>
    </td>
    <td><?= htmlspecialchars($linha['duracao']); ?></td>
    <td>
        <a class="btn-editar" href="editar_teste.php?id=<?= $linha['id']; ?>">Editar</a>
        <a class="btn-excluir"
           href="excluir_teste.php?id=<?= $linha['id']; ?>"
           onclick="return confirm('Tem certeza que deseja excluir este teste?');">
           Excluir
        </a>
    </td>
</tr>
<?php endwhile; ?>
</tbody>
</table>

<?php endif; ?>

</body>
</html>
