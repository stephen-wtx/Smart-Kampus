<?php
session_start();
require_once __DIR__ . '/../config/database.php';

if (!isset($_SESSION['user'])) {
    header('Location: /smartkampus/public/index.php');
    exit;
}

$user = $_SESSION['user'];
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title>Dashboard</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; }
        img { border-radius: 50%; margin-bottom: 10px; }
        button { padding: 8px 14px; cursor: pointer; margin: 3px; }

        .modal {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.6);
            z-index: 999;
        }

        .modal-content {
            background: #fff;
            width: 95%;
            max-width: 1400px;
            height: 90vh;
            margin: 5vh auto;
            padding: 20px;
            overflow: auto;
            border-radius: 6px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 900px;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 6px;
        }
    </style>
</head>
<body>

<h1>Bem-vindo, <?= htmlspecialchars($user['name']); ?>!</h1>
<p>Email: <?= htmlspecialchars($user['email']); ?></p>
<?php if (!empty($user['picture'])): ?>
    <img src="<?= htmlspecialchars($user['picture']); ?>" width="120">
<?php endif; ?>

<hr>

<a href="/smartkampus/public/logout.php">
    <button>Logout</button>
</a>

<br><br>

<!-- Botões principais -->
<button onclick="abrirModalHorarios()">Ver Horários</button>
<button onclick="abrirModalSalas('livres')">Ver Salas Livres</button>
<button onclick="abrirModalSalas('ocupadas')">Ver Salas Ocupadas</button>

<!-- ================= MODAL SALAS ================= -->
<div id="modalSalas" class="modal">
    <div class="modal-content">
        <h3 id="tituloSalas"></h3>
        <div id="resultadoSalas"></div>
        <br>
        <button onclick="fecharModalSalas()">Fechar</button>
    </div>
</div>

<!-- ================= MODAL HORÁRIOS ================= -->
<div id="modalHorarios" class="modal">
    <div class="modal-content">
        <h3>Selecionar tipo de horário</h3>
        <button onclick="carregarHorarios('aula')">Aulas</button>
        <button onclick="carregarHorarios('teste')">Testes</button>
        <button onclick="carregarHorarios('exame')">Exames</button>
        <div id="resultadoHorarios"></div>
        <br>
        <button onclick="fecharModalHorarios()">Fechar</button>
    </div>
</div>

<script>
// ================= MODAL HORÁRIOS =================
function abrirModalHorarios() {
    document.getElementById('modalHorarios').style.display = 'block';
    document.getElementById('resultadoHorarios').innerHTML = '';
}

function fecharModalHorarios() {
    document.getElementById('modalHorarios').style.display = 'none';
}

function carregarHorarios(tipo) {
    fetch('listar_horario_visual.php?tipo=' + tipo)
        .then(res => res.text())
        .then(html => {
            document.getElementById('resultadoHorarios').innerHTML = html;
        });
}

// ================= MODAL SALAS =================
function abrirModalSalas(tipo) {
    document.getElementById('modalSalas').style.display = 'block';
    document.getElementById('tituloSalas').innerText =
        tipo === 'livres' ? 'Salas Livres Agora' : 'Salas Ocupadas Agora';

    fetch('salas_status.php?tipo=' + tipo)
        .then(res => res.text())
        .then(html => {
            document.getElementById('resultadoSalas').innerHTML = html;
        });
}

function fecharModalSalas() {
    document.getElementById('modalSalas').style.display = 'none';
}
</script>

</body>
</html>
