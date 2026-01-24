<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
require_once __DIR__ . "/../../config/database.php";

// ID do exame
$id = $_GET['id'] ?? null;
if (!$id) { die("Exame inválido."); }

// Buscar exame
$stmt = $conn->prepare("SELECT * FROM exames WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$exame = $stmt->get_result()->fetch_assoc();
if (!$exame) { die("Exame não encontrado."); }

// Configurações escaláveis
$diasSemana = ['Segunda-feira','Terça-feira','Quarta-feira','Quinta-feira','Sexta-feira','Sábado'];
$cursos = ['Administração Pública','Contabilidade & Auditoria','Direito','Economia e Gestão','Gestão de Recursos Humanos','Meio Ambiente','Tecnologia de Informação'];
$anos = ['1º','2º','3º','4º'];
$semestres = ['I','II'];
$turnos = ['Diurno','Noturno'];
$salas = [
    'Nelson Mandela 1','Nelson Mandela 2','Nkwame Nkrumah','Martin Luther King','Santo Agostinho','Dom Jaime Gonsalves',
    'Josefina Bakhita 1','Josefina Bakhita 2','Blase Pascal','Sala de Informática','Laboratório de SIG',
    'Cipriano Parite 1','Cipriano Parite 2','Laboratório de Línguas','São Tomás de Aquino','Roberto Busa',
    'Rosário Policarpo Nápica','Beato Newman','Francisco de Assis','São Francisco de Vitória','Max Planck'
];
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Editar Exame</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; }
        table { width:100%; border-collapse: collapse; }
        th, td { border:1px solid #ccc; padding:8px; }
        th { background:#f4f4f4; }
        select, input[type="time"], input[type="text"], input[type="date"] { width:100%; padding:6px; }
        button { margin-top:15px; padding:10px 20px; }
    </style>
</head>

<?php if (!empty($_SESSION['erro'])): ?>
<div style="background:#ffe6e6; border:1px solid #f00; padding:12px; margin-bottom:20px; color:#900;">
    <?= htmlspecialchars($_SESSION['erro']); ?>
</div>
<?php unset($_SESSION['erro']); ?>
<?php endif; ?>

<body>
<nav>
    <a href="listar_exames.php">← Voltar à lista</a> |
    <a href="/smartkampus/dashboard/dashboard.php">Dashboard</a>
</nav>

<h2>Editar Exame</h2>

<form method="POST" action="atualizar_exame.php">
    <input type="hidden" name="id" value="<?= $exame['id']; ?>">
    <table>
        <tr>
            <th>Dia</th>
            <th>Data</th>
            <th>Curso</th>
            <th>Ano</th>
            <th>Semestre</th>
            <th>Disciplina</th>
            <th>Turno</th>
            <th>Sala</th>
            <th>Início</th>
            <th>Fim</th>
        </tr>
        <tr>
            <td>
                <select name="dia_semana">
                    <?php foreach ($diasSemana as $d): ?>
                        <option value="<?= $d ?>" <?= $exame['dia_semana']===$d?'selected':'' ?>><?= $d ?></option>
                    <?php endforeach; ?>
                </select>
            </td>

            <td>
                <input type="date" name="data" value="<?= htmlspecialchars($exame['data']); ?>" required>
            </td>

            <td>
                <select name="curso">
                    <?php foreach ($cursos as $c): ?>
                        <option value="<?= $c ?>" <?= $exame['curso']===$c?'selected':'' ?>><?= $c ?></option>
                    <?php endforeach; ?>
                </select>
            </td>

            <td>
                <select name="ano">
                    <?php foreach ($anos as $a): ?>
                        <option value="<?= $a ?>" <?= $exame['ano']===$a?'selected':'' ?>><?= $a ?></option>
                    <?php endforeach; ?>
                </select>
            </td>

            <td>
                <select name="semestre">
                    <?php foreach ($semestres as $s): ?>
                        <option value="<?= $s ?>" <?= $exame['semestre']===$s?'selected':'' ?>><?= $s ?></option>
                    <?php endforeach; ?>
                </select>
            </td>

            <td>
                <input type="text" name="disciplina" value="<?= htmlspecialchars($exame['disciplina']); ?>" required>
            </td>

            <td>
                <select name="turno">
                    <?php foreach ($turnos as $t): ?>
                        <option value="<?= $t ?>" <?= $exame['turno']===$t?'selected':'' ?>><?= $t ?></option>
                    <?php endforeach; ?>
                </select>
            </td>

            <td>
                <select name="sala">
                    <?php foreach ($salas as $sala): ?>
                        <option value="<?= $sala ?>" <?= $exame['sala']===$sala?'selected':'' ?>><?= $sala ?></option>
                    <?php endforeach; ?>
                </select>
            </td>

            <td>
                <input type="time" name="hora_inicio" value="<?= $exame['hora_inicio']; ?>" required>
            </td>

            <td>
                <input type="time" name="hora_fim" value="<?= $exame['hora_fim']; ?>" required>
            </td>
        </tr>
    </table>

    <button type="submit">Atualizar Exame</button>
</form>

</body>
</html>
