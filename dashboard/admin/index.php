<?php
session_start();
require_once '../../config/database.php';

// Seleciona todas as reservas
$reservas = $conn->query("
    SELECT 
        id,
        docente_nome,
        curso,
        disciplina,
        turno,
        sala,
        dia_semana,
        data,
        hora_inicio,
        hora_fim,
        finalidade,
        estado
    FROM reservas
    ORDER BY data DESC, hora_inicio DESC
") or die("Erro na query: " . $conn->error);
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin</title>
    <style>
        .modal {
            display: none;
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(0,0,0,0.4);
            overflow:auto;
        }
        .modal-content {
            background: #fff;
            padding: 20px;
            margin: 20px auto;
            width: 90%;
            max-width: 1200px;
        }
        table { border-collapse: collapse; width: 100%; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        button { margin: 2px; padding: 5px 10px; cursor: pointer; }
        .acoes button { margin-right: 5px; }
    </style>
</head>
<body>

<h2>Dashboard do Administrador</h2>

<button onclick="abrirModalGerir()">Gerir horários</button>
<button onclick="abrirModalReservas()">Gerir reservas</button>
<button onclick="abrirModalCalendario()">📅 Calendário Académico</button>

<!-- MODAL 1: GERIR -->
<div id="modalGerir" class="modal">
    <div class="modal-content">
        <button onclick="abrirModalTipo('criar')">Criar horários</button>
        <button onclick="abrirModalTipo('ver')">Ver horários</button>
        <br><br>
        <button onclick="fecharTodos()">Fechar</button>
    </div>
</div>

<!-- MODAL 2: TIPO -->
<div id="modalTipo" class="modal">
    <div class="modal-content">
        <h3>Tipo de horário</h3>
        <button onclick="redirecionar('aula')">Aula</button>
        <button onclick="redirecionar('teste')">Teste</button>
        <button onclick="redirecionar('exame')">Exame</button>
        <br><br>
        <button onclick="fecharTodos()">Fechar</button>
    </div>
</div>

<!-- MODAL 3: GERIR RESERVAS -->
<div id="modalReservas" class="modal">
    <div class="modal-content">
        <h3>Gerir Reservas</h3>

        <?php if ($reservas && $reservas->num_rows > 0): ?>
            <table>
                <tr>
                    <th>Docente</th>
                    <th>Curso</th>
                    <th>Sala</th>
                    <th>Disciplina</th>
                    <th>Turno</th>
                    <th>Dia</th>
                    <th>Data</th>
                    <th>Hora</th>
                    <th>Finalidade</th>
                    <th>Ação</th>
                </tr>

            <?php while ($r = $reservas->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($r['docente_nome']) ?></td>
                <td><?= htmlspecialchars($r['curso']) ?></td>
                <td><?= htmlspecialchars($r['sala']) ?></td>
                <td><?= htmlspecialchars($r['disciplina']) ?></td>
                <td><?= htmlspecialchars($r['turno']) ?></td>
                <td><?= htmlspecialchars($r['dia_semana']) ?></td>
                <td><?= date('d/m/Y', strtotime($r['data'])) ?></td>
                <td><?= substr($r['hora_inicio'],0,5) ?> - <?= substr($r['hora_fim'],0,5) ?></td>
                <td><?= htmlspecialchars($r['finalidade'] ?: '-') ?></td>

                <td class="acoes">
                    <?php if ($r['estado'] === 'pendente'): ?>
                        <form method="POST" action="gerir_reserva.php" style="display:inline;">
                            <input type="hidden" name="id" value="<?= $r['id'] ?>">
                            <input type="hidden" name="acao" value="aprovar">
                            <button type="submit">Aprovar</button>
                        </form>

                        <form method="POST" action="gerir_reserva.php" style="display:inline;">
                            <input type="hidden" name="id" value="<?= $r['id'] ?>">
                            <input type="hidden" name="acao" value="rejeitar">
                            <button type="submit">Rejeitar</button>
                        </form>

                    <?php elseif ($r['estado'] === 'aprovada'): ?>
                        <form method="POST" action="cancelar_reserva.php" style="display:inline;">
                            <input type="hidden" name="id" value="<?= $r['id'] ?>">
                            <button type="submit" onclick="return confirm('Cancelar esta reserva?')">Cancelar Reserva</button>
                        </form>

                    <?php elseif ($r['estado'] === 'rejeitada'): ?>
                        <form method="POST" action="excluir_reserva.php" style="display:inline;">
                            <input type="hidden" name="id" value="<?= $r['id'] ?>">
                            <button type="submit" onclick="return confirm('Deseja excluir esta reserva rejeitada?')">Excluir</button>
                        </form>

                    <?php else: ?>
                        <?= strtoupper($r['estado']) ?>
                    <?php endif; ?>
                </td>

            </tr>
            <?php endwhile; ?>
            </table>
        <?php else: ?>
            <p>Sem reservas registadas.</p>
        <?php endif; ?>

        <br><br>
        <button onclick="fecharTodos()">Fechar</button>
    </div>
</div>

<!-- MODAL 4: CALENDÁRIO ACADÉMICO -->
<div id="modalCalendario" class="modal">
    <div class="modal-content">
        <h3>Calendário Académico</h3>

        <?php
        $cal = $conn->query("SELECT * FROM calendario_academico ORDER BY data_publicacao DESC LIMIT 1");
        ?>

        <?php if ($cal && $cal->num_rows > 0): 
            $c = $cal->fetch_assoc();
        ?>

            <p><strong>Publicado em:</strong> <?= date('d/m/Y H:i', strtotime($c['data_publicacao'])) ?></p>

            <a href="/smartkampus/public/uploads/calendario/<?= $c['caminho'] ?>" target="_blank">
                <button>📄 Ver Calendário</button>
            </a>

            <form method="POST" action="exluir_calendario.php" 
                  onsubmit="return confirm('Deseja excluir o calendário académico?')">
                <input type="hidden" name="id" value="<?= $c['id'] ?>">
                <button type="submit" style="background:#c0392b;color:#fff;">❌ Excluir Calendário</button>
            </form>

        <?php else: ?>

            <form method="POST" action="upload_calendario.php" enctype="multipart/form-data">
                <input type="file" name="calendario_pdf" accept="application/pdf" required>
                <br><br>
                <button type="submit">📤 Publicar Calendário</button>
            </form>

        <?php endif; ?>

        <br>
        <button onclick="fecharTodos()">Fechar</button>
    </div>
</div>

<script>
let acao = null;

function abrirModalGerir() {
    document.getElementById('modalGerir').style.display = 'block';
}

function abrirModalReservas() {
    document.getElementById('modalReservas').style.display = 'block';
}

function abrirModalTipo(tipoAcao) {
    acao = tipoAcao;
    document.getElementById('modalGerir').style.display = 'none';
    document.getElementById('modalTipo').style.display = 'block';
}

function abrirModalCalendario() {
    document.getElementById('modalCalendario').style.display = 'block';
}

function fecharTodos() {
    document.getElementById('modalGerir').style.display = 'none';
    document.getElementById('modalTipo').style.display = 'none';
    document.getElementById('modalReservas').style.display = 'none';
    document.getElementById('modalCalendario').style.display = 'none';
}

function redirecionar(tipo) {
    if (acao === 'criar') {
        if (tipo === 'aula')   window.location = 'criar_horario.php';
        if (tipo === 'teste')  window.location = 'criar_teste.php';
        if (tipo === 'exame')  window.location = 'criar_exame.php';
    }

    if (acao === 'ver') {
        if (tipo === 'aula')   window.location = 'listar_horarios.php';
        if (tipo === 'teste')  window.location = 'listar_testes.php';
        if (tipo === 'exame')  window.location = 'listar_exames.php';
    }
}
</script>

</body>
</html>
