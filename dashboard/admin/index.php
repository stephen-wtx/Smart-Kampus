<?php
session_start();



// session_start();
// require_once __DIR__ . '/../../config/database.php';

/*
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: /smartkampus/dashboard/dashboard.php');
    exit;
}
*/

// CONFIGURAÇÕES ESCALÁVEIS
$diasSemana = [
    'Segunda-feira',
    'Terça-feira',
    'Quarta-feira',
    'Quinta-feira',
    'Sexta-feira',
    'Sábado'
];

$cursos = [
    'Administração Pública',
    'Contabilidade & Auditoria',
    'Direito',
    'Economia e Gestão',
    'Gestão de Recursos Humanos',
    'Meio Ambiente',
    'Tecnologia de Informação'
];

$anos = ['1º', '2º', '3º', '4º'];

$semestres = ['I', 'II'];

$turnos = ['Diurno', 'Noturno'];

$salas = [
    'Nelson Mandela 1',
    'Nelson Mandela 2',
    'Nkwame Nkrumah',
    'Martin Luther King',
    'Santo Agostinho',
    'Dom Jaime Gonsalves',
    'Josefina Bakhita 1',
    'Josefina Bakhita 2',
    'Blase Pascal',
    'Sala de Informática',
    'Laboratório de SIG',
    'Cipriano Parite 1',
    'Cipriano Parite 2',
    'Laboratório de Línguas',
    'São Tomás de Aquino',
    'Roberto Busa',
    'Rosário Policarpo Nápica',
    'Beato Newman',
    'Francisco de Assis',
    'São Francisco de Vitória',
    'Max Planck'
];
?>

<!DOCTYPE html>
<html lang="pt-pt">
<head>
    <meta charset="UTF-8">
    <title>Criar Horário</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ccc; padding: 8px; }
        th { background: #f4f4f4; }
        select, input[type="time"], input[type="text"] {
            width: 100%;
            padding: 6px;
        }
        button {
            margin-top: 15px;
            padding: 10px 20px;
            cursor: pointer;
        }
    </style>
</head>

<?php if (!empty($_SESSION['sucesso'])): ?>
    <div style="
        background:#e6fffa;
        border:1px solid #38b2ac;
        padding:12px 16px;
        margin-bottom:20px;
        color:#065f46;
        border-radius:6px;
    ">
        <?= htmlspecialchars($_SESSION['sucesso']); ?>
    </div>
    <?php unset($_SESSION['sucesso']); ?>
<?php endif; ?>

<body>
    <nav>
    <a href="/smartkampus/dashboard/dashboard.php">Ir ao Dashboard Principal</a>
    |
    <a href="/smartkampus/public/logout.php">Logout</a>
</nav>


<h2>Criar Horário</h2>

<!-- MENSAGEM DE ERRO -->
<?php if (!empty($_SESSION['erro'])): ?>
    <div style="background:#ffe6e6; border:1px solid #f00; padding:12px; margin-bottom:20px; color:#900;">
        <?= htmlspecialchars($_SESSION['erro']); ?>
    </div>
    <?php unset($_SESSION['erro']); ?>
<?php endif; ?>


<form method="POST" action="salvar_horario.php">

<table>
    <tr>
        <th>Dia da Semana</th>
        <th>Curso</th>
        <th>Ano</th>
        <th>Semestre</th>
        <th>Disciplina</th>
        <th>Turno</th>
        <th>Sala</th>
        <th>Hora Início</th>
        <th>Hora Fim</th>
    </tr>

    <tr>
        <td>
            <select name="dia_semana" required>
                <?php foreach ($diasSemana as $dia): ?>
                    <option value="<?= $dia ?>"><?= $dia ?></option>
                <?php endforeach; ?>
            </select>
        </td>

        <td>
            <select name="curso" required>
                <?php foreach ($cursos as $curso): ?>
                    <option value="<?= $curso ?>"><?= $curso ?></option>
                <?php endforeach; ?>
            </select>
        </td>

        <td>
            <select name="ano" required>
                <?php foreach ($anos as $ano): ?>
                    <option value="<?= $ano ?>"><?= $ano ?></option>
                <?php endforeach; ?>
            </select>
        </td>

        <td>
            <select name="semestre" required>
                <?php foreach ($semestres as $sem): ?>
                    <option value="<?= $sem ?>"><?= $sem ?></option>
                <?php endforeach; ?>
            </select>
        </td>

        <td>
            <input type="text" name="disciplina" placeholder="Ex: Matemática I" required>
        </td>

        <td>
            <select name="turno" required>
                <?php foreach ($turnos as $turno): ?>
                    <option value="<?= $turno ?>"><?= $turno ?></option>
                <?php endforeach; ?>
            </select>
        </td>

        <td>
            <select name="sala" required>
                <?php foreach ($salas as $sala): ?>
                    <option value="<?= $sala ?>"><?= $sala ?></option>
                <?php endforeach; ?>
            </select>
        </td>

        <td>
            <input type="time" name="hora_inicio" required>
        </td>

        <td>
            <input type="time" name="hora_fim" required>
        </td>
    </tr>
</table>

<button type="submit">Salvar</button>

</form>

<p>
    <a href="listar_horarios.php">✏️ Ver horários</a>
</p>



</body>
</html>
