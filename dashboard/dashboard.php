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
<button onclick="abrirModalCalendario()">📅 Ver Calendário Académico</button>


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
        <h3>Selecionar horário</h3>

        <label>Tipo:</label>
        <select id="tipoHorario" onchange="mostrarFiltros()">
            <option value="">-- Selecione --</option>
            <option value="aula">Aulas</option>
            <option value="teste">Testes</option>
            <option value="exame">Exames</option>
        </select>

        <div id="filtros" style="display:none; margin-top:15px;">
            <label>Curso:</label>
            <select id="curso">
                <option value="">Todos</option>
                <option>Administração Pública</option>
                <option>Contabilidade & Auditoria</option>
                <option>Direito</option>
                <option>Economia e Gestão</option>
                <option>Gestão de Recursos Humanos</option>
                <option>Meio Ambiente</option>
                <option>Tecnologia de Informação</option>
            </select>

            <label>Ano:</label>
            <select id="ano">
                <option value="">Todos</option>
                <option>1</option>
                <option>2</option>
                <option>3</option>
                <option>4</option>
            </select>

            <label>Semestre:</label>
            <select id="semestre">
                <option value="">Todos</option>
                <option>I</option>
                <option>II</option>
            </select>

            <label>Turno:</label>
            <select id="turno">
                <option value="">Todos</option>
                <option>Diurno</option>
                <option>Noturno</option>
            </select>

            <br><br>
            <button onclick="buscarHorarios()">Buscar</button>
        </div>

        <div id="resultadoHorarios" style="margin-top:15px;"></div>

        <br>
        <button onclick="fecharModalHorarios()">Fechar</button>
    </div>
</div>


<!-- ================= MODAL CALENDÁRIO ACADÉMICO ================= -->
<div id="modalCalendario" class="modal">
    <div class="modal-content">
        <h3>Calendário Académico</h3>

        <?php
        // Seleciona o último calendário publicado
        $cal = $conn->query("
            SELECT * FROM calendario_academico
            ORDER BY data_publicacao DESC
            LIMIT 1
        ");
        ?>

        <?php if ($cal && $cal->num_rows > 0): 
            $c = $cal->fetch_assoc();
        ?>
            <p><strong>Publicado em:</strong> <?= date('d/m/Y H:i', strtotime($c['data_publicacao'])) ?></p>

            <!-- Visualização do PDF -->
            <iframe src="/smartkampus/public/uploads/calendario/<?= $c['caminho'] ?>" 
                    style="width:100%; height:70vh;" frameborder="0"></iframe>

            <!-- Botão para download -->
            <br><br>
            <a href="/smartkampus/public/uploads/calendario/<?= $c['caminho'] ?>" download>
                <button>⬇️ Baixar Calendário</button>
            </a>

        <?php else: ?>
            <p>Não existe nenhum calendário académico publicado ainda.</p>
        <?php endif; ?>

        <br>
        <button onclick="fecharModalCalendario()">Fechar</button>
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

// ================= MODAL FILTRO =================
function mostrarFiltros() {
    document.getElementById('filtros').style.display = 'block';
    document.getElementById('resultadoHorarios').innerHTML = '';
}

function buscarHorarios() {
    const tipo = document.getElementById('tipoHorario').value;
    const curso = document.getElementById('curso').value;
    const ano = document.getElementById('ano').value;
    const semestre = document.getElementById('semestre').value;
    const turno = document.getElementById('turno').value;

    const params = new URLSearchParams({
        tipo, curso, ano, semestre, turno
    });

    fetch('listar_horario_visual.php?' + params.toString())
        .then(res => res.text())
        .then(html => {
            document.getElementById('resultadoHorarios').innerHTML = html;
        });
}

// ================= MODAL CALENDÁRIO =================
function abrirModalCalendario() {
    document.getElementById('modalCalendario').style.display = 'block';
}

function fecharModalCalendario() {
    document.getElementById('modalCalendario').style.display = 'none';
}


</script>

</body>
</html>
