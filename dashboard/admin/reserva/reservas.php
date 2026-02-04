<?php
session_start();
require_once __DIR__ . '/../../config/database.php';

if (!isset($_SESSION['admin'])) {
    header('Location: /../../public/index.php');
    exit;
}

$reservas = $conn->query("
    SELECT r.*, u.name AS docente
    FROM reservas_sala r
    JOIN users u ON u.id = r.docente_id
    WHERE r.estado = 'pendente'
    ORDER BY r.criado_em ASC
");
?>

<h1>Reservas Pendentes</h1>

<?php if ($reservas->num_rows === 0): ?>
    <p>Sem reservas pendentes.</p>
<?php else: ?>
<table border="1" cellpadding="6">
    <tr>
        <th>Docente</th>
        <th>Sala</th>
        <th>Data</th>
        <th>Hora</th>
        <th>Finalidade</th>
        <th>Ações</th>
    </tr>

    <?php while ($r = $reservas->fetch_assoc()): ?>
    <tr>
        <td><?= htmlspecialchars($r['docente']) ?></td>
        <td><?= $r['sala'] ?></td>
        <td><?= date('d/m/Y', strtotime($r['data'])) ?></td>
        <td><?= substr($r['hora_inicio'],0,5) ?> - <?= substr($r['hora_fim'],0,5) ?></td>
        <td><?= $r['finalidade'] ?: '-' ?></td>
        <td>
            <form method="POST" action="processar_reserva.php" style="display:inline;">
                <input type="hidden" name="id" value="<?= $r['id'] ?>">
                <input type="hidden" name="acao" value="aprovar">
                <button>Aprovar</button>
            </form>

            <form method="POST" action="processar_reserva.php" style="display:inline;">
                <input type="hidden" name="id" value="<?= $r['id'] ?>">
                <input type="hidden" name="acao" value="rejeitar">
                <button>Rejeitar</button>
            </form>
        </td>
    </tr>
    <?php endwhile; ?>
</table>
<?php endif; ?>
