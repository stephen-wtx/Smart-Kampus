<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once __DIR__ . '/../../config/database.php';

if (!isset($_SESSION['user'])) {
    header('Location: /smartkampus/public/index.php');
    exit;
}

$user = $_SESSION['user'];

// Verifica se já existe reserva
$check = $conn->prepare("
    SELECT id FROM reservas
    WHERE docente_id = ?
");
$check->bind_param("i", $user['id']);
$check->execute();
$reservaExistente = $check->get_result()->num_rows > 0;
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Painel do Docente</title>
</head>
<body>

<h1>Painel do Docente</h1>
<p>Bem-vindo, <?= htmlspecialchars($user['name']) ?></p>

<?php if (!empty($user['picture'])): ?>
    <img src="<?= htmlspecialchars($user['picture']) ?>" width="80">
<?php endif; ?>

<br><br>

<a href="/smartkampus/dashboard/dashboard.php">Dashboard Principal</a> |
<a href="/smartkampus/public/logout.php">Logout</a>

<hr>

<button onclick="abrirModal('modalSolicitar')">Solicitar Reserva</button>
<button onclick="abrirModal('modalMinhas')">Minhas Reservas</button>

<!-- ================= MODAL SOLICITAR (FULLSCREEN) ================= -->
<div id="modalSolicitar" style="
    display:none;
    position:fixed;
    top:0; left:0;
    width:100%;
    height:100%;
    background:#fff;
    overflow:auto;
">

    <h2>Solicitar Reserva</h2>
    <button onclick="fecharModal('modalSolicitar')">Voltar</button>
    <hr>

    <?php if ($reservaExistente): ?>
        <p style="color:red;">
            Erro! Já possui uma reserva (pendente, aprovada ou rejeitada).
        </p>
    <?php else: ?>
        <form method="POST" action="solicitar_reserva.php">
            <table cellpadding="6">
                <tr>
                    <td>Sala</td>
                    <td>
                        <select name="sala" required>
                            <?php
                            $salas = $conn->query("
                                SELECT nome 
                                FROM salas 
                                WHERE estado = 'livre'
                                ORDER BY nome
                            ");

                            while ($s = $salas->fetch_assoc()):
                            ?>
                                <option value="<?= htmlspecialchars($s['nome']) ?>">
                                    <?= htmlspecialchars($s['nome']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>

                    </td>
                </tr>

                <tr>
                    <td>Dia</td>
                    <td>
                        <select name="dia_semana" required>
                            <option>Segunda-feira</option>
                            <option>Terça-feira</option>
                            <option>Quarta-feira</option>
                            <option>Quinta-feira</option>
                            <option>Sexta-feira</option>
                            <option>Sábado</option>
                        </select>
                    </td>
                </tr>

                <tr>
                    <td>Data</td>
                    <td><input type="date" name="data" required></td>
                </tr>

                <tr>
                    <td>Hora início</td>
                    <td><input type="time" name="hora_inicio" required></td>
                </tr>

                <tr>
                    <td>Hora fim</td>
                    <td><input type="time" name="hora_fim" required></td>
                </tr>

                <tr>
                    <td>Finalidade</td>
                    <td><input type="text" name="finalidade"></td>
                </tr>

                <tr>
                    <td colspan="2">
                        <button type="submit">Solicitar Reserva</button>
                    </td>
                </tr>
            </table>
        </form>
    <?php endif; ?>
</div>

<!-- ================= MODAL MINHAS RESERVAS (FULLSCREEN) ================= -->
<div id="modalMinhas" style="
    display:none;
    position:fixed;
    top:0; left:0;
    width:100%;
    height:100%;
    background:#fff;
    overflow:auto;
">

    <h2>Minhas Reservas</h2>
    <button onclick="fecharModal('modalMinhas')">Voltar</button>
    <hr>

    <?php
    $list = $conn->prepare("
        SELECT sala, dia_semana, data, hora_inicio, hora_fim, finalidade, estado
        FROM reservas
        WHERE docente_id = ?
        ORDER BY criado_em DESC
    ");
    $list->bind_param("i", $user['id']);
    $list->execute();
    $result = $list->get_result();
    ?>

    <?php if ($result->num_rows === 0): ?>
        <p>Não possui reservas.</p>
    <?php else: ?>
        <table border="1" cellpadding="6">
            <tr>
                <th>Sala</th>
                <th>Dia</th>
                <th>Data</th>
                <th>Hora</th>
                <th>Finalidade</th>
                <th>Estado</th>
            </tr>

            <?php while ($r = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($r['sala']) ?></td>
                    <td><?= htmlspecialchars($r['dia_semana']) ?></td>
                    <td><?= date('d/m/Y', strtotime($r['data'])) ?></td>
                    <td><?= substr($r['hora_inicio'],0,5) ?> - <?= substr($r['hora_fim'],0,5) ?></td>
                    <td><?= $r['finalidade'] ?: '-' ?></td>
                    <td><strong><?= strtoupper($r['estado']) ?></strong></td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php endif; ?>
</div>

<!-- JS -->
<script>
function abrirModal(id) {
    document.getElementById(id).style.display = 'block';
}

function fecharModal(id) {
    document.getElementById(id).style.display = 'none';
}
</script>

</body>
</html>
