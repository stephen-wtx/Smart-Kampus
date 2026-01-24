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
    <title>Dashboard</title>

    <style>
        body { font-family: Arial, sans-serif; margin: 30px; }
        img { border-radius: 50%; margin-bottom: 10px; }
        button { padding: 8px 14px; cursor: pointer; }

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
            max-width: 1400px;   /* ideal para FullHD */
            height: 90vh;        /* ocupa quase o ecrã todo */
            margin: 5vh auto;
            padding: 20px;
            overflow: auto;      /* scroll interno */
            border-radius: 6px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 900px; /* força scroll horizontal se precisar */
        }

        .modal-content {
            overflow-x: auto;
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

<button onclick="abrirModal()">Ver horários</button>

<div id="modalHorarios" class="modal">
    <div class="modal-content">

        <h3>Selecionar tipo de horário</h3>

        <button onclick="carregarHorarios('aula')">Aulas</button>
        <button onclick="carregarHorarios('teste')">Testes</button>
        <button onclick="carregarHorarios('exame')">Exames</button>

        <div id="resultado"></div>

        <br>
        <button onclick="fecharModal()">Fechar</button>
    </div>
</div>

<script>
function abrirModal() {
    document.getElementById('modalHorarios').style.display = 'block';
    document.getElementById('resultado').innerHTML = '';
}

function fecharModal() {
    document.getElementById('modalHorarios').style.display = 'none';
}

function carregarHorarios(tipo) {
    fetch('listar_horario_visual.php?tipo=' + tipo)
        .then(res => res.text())
        .then(html => {
            document.getElementById('resultado').innerHTML = html;
        });
}
</script>

</body>
</html>
