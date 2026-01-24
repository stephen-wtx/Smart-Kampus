<?php
require_once __DIR__ . '/../config/database.php';

$tipo = $_GET['tipo'] ?? 'aula';

if ($tipo === 'teste' || $tipo === 'exame') {

    $tabela = $tipo === 'teste' ? 'testes' : 'exames';

    $sql = "
        SELECT 
            curso,
            disciplina,
            ano,
            semestre,
            turno,
            sala,
            dia_semana,
            data,
            hora_inicio,
            hora_fim
        FROM $tabela
        ORDER BY data, hora_inicio
    ";

    $result = $conn->query($sql);

    if ($result->num_rows === 0) {
        echo "<p><strong>Sem horários disponíveis.</strong></p>";
        exit;
    }
    ?>

    <table>
        <thead>
            <tr>
                <th>Curso</th>
                <th>Disciplina</th>
                <th>Ano</th>
                <th>Semestre</th>
                <th>Turno</th>
                <th>Sala</th>
                <th>Dia</th>
                <th>Data</th>
                <th>Hora</th>
                <th>Duração</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['curso']) ?></td>
                    <td><?= htmlspecialchars($row['disciplina']) ?></td>
                    <td><?= (int)$row['ano'] ?>º</td>
                    <td><?= htmlspecialchars($row['semestre']) ?></td>
                    <td><?= htmlspecialchars($row['turno']) ?></td>
                    <td><?= htmlspecialchars($row['sala']) ?></td>
                    <td><?= htmlspecialchars($row['dia_semana']) ?></td>
                    <td><?= date('d/m/Y', strtotime($row['data'])) ?></td>
                    <td>
                        <?= substr($row['hora_inicio'], 0, 5) ?> - <?= substr($row['hora_fim'], 0, 5) ?>
                    </td>
                    <td>
                        <?php
                            $inicio = strtotime($row['hora_inicio']);
                            $fim = strtotime($row['hora_fim']);
                            $duracaoHoras = ($fim - $inicio) / 3600;
                            echo intval($duracaoHoras) . ' h';
                        ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

<?php
    exit;
}

/* =========================
   AULAS (mantém como está)
   ========================= */

$sql = "
    SELECT dia_semana, curso, ano, semestre, disciplina, turno, sala, hora_inicio, hora_fim
    FROM horarios
    ORDER BY FIELD(dia_semana,'Segunda','Terça','Quarta','Quinta','Sexta','Sábado'), hora_inicio
";

$result = $conn->query($sql);

if ($result->num_rows === 0) {
    echo "<p><strong>Sem horários disponíveis.</strong></p>";
    exit;
}
?>

<table>
    <thead>
        <tr>
            <th>Dia</th>
            <th>Curso</th>
            <th>Ano</th>
            <th>Semestre</th>
            <th>Disciplina</th>
            <th>Turno</th>
            <th>Sala</th>
            <th>Horário</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['dia_semana']) ?></td>
                <td><?= htmlspecialchars($row['curso']) ?></td>
                <td><?= (int)$row['ano'] ?>º</td>
                <td><?= htmlspecialchars($row['semestre']) ?></td>
                <td><?= htmlspecialchars($row['disciplina']) ?></td>
                <td><?= htmlspecialchars($row['turno']) ?></td>
                <td><?= htmlspecialchars($row['sala']) ?></td>
                <td><?= substr($row['hora_inicio'],0,5) ?> - <?= substr($row['hora_fim'],0,5) ?></td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>