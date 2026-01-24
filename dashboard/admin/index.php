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
        }

        .modal-content {
            background: #fff;
            padding: 20px;
            width: 300px;
            margin: 100px auto;
            text-align: center;
        }

        button {
            margin: 5px;
        }
    </style>
</head>
<body>

    <h2>Dashboard do Administrador</h2>

    <button onclick="abrirModalGerir()">Gerir horários</button>

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

    <script>
        let acao = null; // criar ou ver

        function abrirModalGerir() {
            document.getElementById('modalGerir').style.display = 'block';
        }

        function abrirModalTipo(tipoAcao) {
            acao = tipoAcao;
            document.getElementById('modalGerir').style.display = 'none';
            document.getElementById('modalTipo').style.display = 'block';
        }

        function fecharTodos() {
            document.getElementById('modalGerir').style.display = 'none';
            document.getElementById('modalTipo').style.display = 'none';
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
