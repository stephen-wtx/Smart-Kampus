<?php
require_once "conexao.php";

if (!isset($_GET['id'])) {
    header("Location: editar_horario.php");
    exit;
}

$id = intval($_GET['id']);

$sql = "SELECT * FROM horarios WHERE id = $id";
$resultado = mysqli_query($conexao, $sql);

if (mysqli_num_rows($resultado) == 0) {
    echo "Horário não encontrado.";
    exit;
}

$horario = mysqli_fetch_assoc($resultado);
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Editar Horário</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f8;
            padding: 20px;
        }

        form {
            background: #fff;
            padding: 20px;
            max-width: 500px;
            border-radius: 6px;
        }

        label {
            display: block;
            margin-top: 12px;
            font-weight: bold;
        }

        input, select {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
        }

        button {
            margin-top: 20px;
            padding: 10px;
            width: 100%;
            background: #2b6cb0;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        button:hover {
            background: #2c5282;
        }

        a {
            display: inline-block;
            margin-top: 15px;
            text-decoration: none;
            color: #2b6cb0;
        }
    </style>
</head>
<body>

<h2>Editar Horário</h2>

<form method="POST" action="atualizar_horario.php">

    <input type="hidden" name="id" value="<?= $horario['id']; ?>">

    <label>Curso</label>
    <input type="text" name="curso" value="<?= $horario['curso']; ?>" required>

    <label>Disciplina</label>
    <input type="text" name="disciplina" value="<?= $horario['disciplina']; ?>" required>

    <label>Ano</label>
    <input type="text" name="ano" value="<?= $horario['ano']; ?>" required>

    <label>Sala</label>
    <input type="text" name="sala" value="<?= $horario['sala']; ?>" required>

    <label>Dia da Semana</label>
    <select name="dia_semana" required>
        <?php
        $dias = ["Segunda", "Terça", "Quarta", "Quinta", "Sexta", "Sábado"];
        foreach ($dias as $dia) {
            $selected = ($horario['dia_semana'] == $dia) ? "selected" : "";
            echo "<option value='$dia' $selected>$dia</option>";
        }
        ?>
    </select>

    <label>Hora Início</label>
    <input type="time" name="hora_inicio" value="<?= $horario['hora_inicio']; ?>" required>

    <label>Hora Fim</label>
    <input type="time" name="hora_fim" value="<?= $horario['hora_fim']; ?>" required>

    <button type="submit">Salvar Alterações</button>
</form>

<a href="editar_horario.php">← Voltar</a>

</body>
</html>
