<style>
    /* ESTILOS PARA TABELA DE HORÁRIOS */
    .table-container {
        overflow-x: auto;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    }
    
    .data-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }
    
    /* TABELA PARA AULAS (8 colunas) */
    .data-table-aulas {
        min-width: 1400px;
    }
    
    /* TABELA PARA TESTES/EXAMES (10 colunas - precisa de mais espaço) */
    .data-table-testes-exames {
        min-width: 1600px;
    }
    
    .data-table thead {
        background: #f8fafc;
    }
    
    .data-table th {
        padding: 1.5rem 1rem; /* MAIS ESPAÇAMENTO VERTICAL */
        text-align: left;
        font-weight: 600;
        color: #475569;
        border-bottom: 1px solid #e2e8f0;
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        white-space: nowrap;
    }
    
    .data-table td {
        padding: 1.25rem 1rem; /* MAIS ESPAÇAMENTO HORIZONTAL */
        border-bottom: 1px solid #f1f5f9;
        color: #334155;
        vertical-align: top;
        line-height: 1.5;
    }
    
    /* ESPAÇAMENTO MAIOR ENTRE CÉLULAS */
    .data-table th, .data-table td {
        padding-left: 1.5rem;
        padding-right: 1.5rem;
    }
    
    /* LARGURAS ESPECÍFICAS PARA TESTES/EXAMES (10 colunas) */
    .data-table-testes-exames th:nth-child(1),
    .data-table-testes-exames td:nth-child(1) { /* CURSO */
        min-width: 180px;
        width: 180px;
    }
    
    .data-table-testes-exames th:nth-child(2),
    .data-table-testes-exames td:nth-child(2) { /* DISCIPLINA */
        min-width: 200px;
        width: 200px;
    }
    
    .data-table-testes-exames th:nth-child(3),
    .data-table-testes-exames td:nth-child(3) { /* ANO */
        min-width: 70px;
        width: 70px;
        text-align: center;
    }
    
    .data-table-testes-exames th:nth-child(4),
    .data-table-testes-exames td:nth-child(4) { /* SEMESTRE */
        min-width: 90px;
        width: 90px;
        text-align: center;
    }
    
    .data-table-testes-exames th:nth-child(5),
    .data-table-testes-exames td:nth-child(5) { /* TURNO */
        min-width: 100px;
        width: 100px;
        text-align: center;
    }
    
    .data-table-testes-exames th:nth-child(6),
    .data-table-testes-exames td:nth-child(6) { /* SALA */
        min-width: 140px;
        width: 140px;
    }
    
    .data-table-testes-exames th:nth-child(7),
    .data-table-testes-exames td:nth-child(7) { /* DIA */
        min-width: 130px;
        width: 130px;
    }
    
    .data-table-testes-exames th:nth-child(8),
    .data-table-testes-exames td:nth-child(8) { /* DATA */
        min-width: 100px;
        width: 100px;
        text-align: center;
    }
    
    .data-table-testes-exames th:nth-child(9),
    .data-table-testes-exames td:nth-child(9) { /* HORA */
        min-width: 130px;
        width: 130px;
        text-align: center;
    }
    
    .data-table-testes-exames th:nth-child(10),
    .data-table-testes-exames td:nth-child(10) { /* DURAÇÃO */
        min-width: 90px;
        width: 90px;
        text-align: center;
    }
    
    /* LARGURAS PARA AULAS (8 colunas) */
    .data-table-aulas th:nth-child(1), .data-table-aulas td:nth-child(1) { /* DIA */
        min-width: 140px;
        width: 140px;
    }
    
    .data-table-aulas th:nth-child(2), .data-table-aulas td:nth-child(2) { /* CURSO */
        min-width: 180px;
        width: 180px;
    }
    
    .data-table-aulas th:nth-child(3), .data-table-aulas td:nth-child(3) { /* ANO */
        min-width: 70px;
        width: 70px;
        text-align: center;
    }
    
    .data-table-aulas th:nth-child(4), .data-table-aulas td:nth-child(4) { /* SEMESTRE */
        min-width: 90px;
        width: 90px;
        text-align: center;
    }
    
    .data-table-aulas th:nth-child(5), .data-table-aulas td:nth-child(5) { /* DISCIPLINA */
        min-width: 220px;
        width: 220px;
    }
    
    .data-table-aulas th:nth-child(6), .data-table-aulas td:nth-child(6) { /* TURNO */
        min-width: 100px;
        width: 100px;
        text-align: center;
    }
    
    .data-table-aulas th:nth-child(7), .data-table-aulas td:nth-child(7) { /* SALA */
        min-width: 150px;
        width: 150px;
    }
    
    .data-table-aulas th:nth-child(8), .data-table-aulas td:nth-child(8) { /* HORÁRIO */
        min-width: 130px;
        width: 130px;
        text-align: center;
    }
    
    .data-table tr:last-child td {
        border-bottom: none;
    }
    
    .data-table tr:hover {
        background-color: #f8fafc;
    }
    
    /* ESTILO PARA TEXTO */
    .no-wrap {
        white-space: nowrap;
    }
    
    .text-wrap {
        word-wrap: break-word;
        word-break: break-word;
        white-space: normal;
        line-height: 1.4;
    }
    
    /* ALINHAMENTO */
    .text-center {
        text-align: center;
    }
    
    .text-left {
        text-align: left;
    }
    
    /* ESPAÇAMENTO EXTRA PARA TABELAS COM MUITAS COLUNAS */
    .wide-table {
        min-width: 1600px;
    }
</style>


<?php
session_start();
require_once __DIR__ . '/../config/database.php';

if (!isset($_SESSION['user'])) {
    header('Location: /smartkampus/public/index.php');
    exit;
}

$user = $_SESSION['user'];


/* =========================
   RECEBER PARÂMETROS
   ========================= */
$tipo     = $_GET['tipo'] ?? 'aula';
$curso    = $_GET['curso'] ?? '';
$ano      = $_GET['ano'] ?? '';
$semestre = $_GET['semestre'] ?? '';
$turno    = $_GET['turno'] ?? '';

/* =========================
   MONTAR FILTROS DINÂMICOS
   ========================= */
$filtros = [];

if (!empty($curso)) {
    $curso = $conn->real_escape_string($curso);
    $filtros[] = "curso = '$curso'";
}

if (!empty($ano)) {
    $ano = (int)$ano;
    $filtros[] = "ano = $ano";
}

if (!empty($semestre)) {
    $semestre = $conn->real_escape_string($semestre);
    $filtros[] = "semestre = '$semestre'";
}

if (!empty($turno)) {
    $turno = $conn->real_escape_string($turno);
    $filtros[] = "turno = '$turno'";
}

$where = $filtros ? 'WHERE ' . implode(' AND ', $filtros) : '';

/* =========================
   TESTES E EXAMES
   ========================= */
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
        $where
        ORDER BY data, hora_inicio
    ";

    $result = $conn->query($sql);

    if (!$result || $result->num_rows === 0) {
        echo "<p><strong>Sem horário disponível ainda.</strong></p>";
        exit;
    }
    ?>
    
    <!-- ADICIONE A DIV E A CLASSE: -->
<div class="table-container">
    <table class="data-table data-table-testes-exames wide-table">
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
                    <td class="text-wrap"><?= htmlspecialchars($row['curso']) ?></td>
                    <td class="text-wrap"><?= htmlspecialchars($row['disciplina']) ?></td>
                    <td class="no-wrap text-center"><?= (int)$row['ano'] ?>º</td>
                    <td class="no-wrap text-center"><?= htmlspecialchars($row['semestre']) ?></td>
                    <td class="no-wrap text-center"><?= htmlspecialchars($row['turno']) ?></td>
                    <td class="no-wrap"><?= htmlspecialchars($row['sala']) ?></td>
                    <td class="no-wrap"><?= htmlspecialchars($row['dia_semana']) ?></td>
                    <td class="no-wrap text-center"><?= date('d/m/Y', strtotime($row['data'])) ?></td>
                    <td class="no-wrap text-center">
                        <?= substr($row['hora_inicio'], 0, 5) ?> - <?= substr($row['hora_fim'], 0, 5) ?>
                    </td>
                    <td class="no-wrap text-center">
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
</div>

        <?php
    $queryString = http_build_query([
        'tipo'     => $tipo,
        'curso'    => $curso ?? '',
        'ano'      => $ano ?? '',
        'semestre' => $semestre ?? '',
        'turno'    => $turno ?? ''
    ]);
    ?>

<a href="baixar_horario_pdf.php?<?= $queryString ?>" target="_blank" style="text-decoration: none;">
    <button class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 8px;">
        <i class="fas fa-download"></i>
        Baixar Horário (PDF)
    </button>
</a>


<?php

    exit;
}

/* =========================
   AULAS (CÓDIGO ORIGINAL + FILTROS)
   ========================= */

$sql = "
    SELECT 
        dia_semana,
        curso,
        ano,
        semestre,
        disciplina,
        turno,
        sala,
        hora_inicio,
        hora_fim
    FROM horarios
    $where
    ORDER BY FIELD(dia_semana,'Segunda','Terça','Quarta','Quinta','Sexta','Sábado'),
             hora_inicio
";

$result = $conn->query($sql);

if (!$result || $result->num_rows === 0) {
    echo "<p><strong>Sem horário disponível ainda.</strong></p>";
    exit;
}
?>

<!-- ADICIONE A DIV E A CLASSE: -->
<div class="table-container">
    <table class="data-table data-table-aulas">
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
                    <td class="no-wrap"><?= htmlspecialchars($row['dia_semana']) ?></td>
                    <td class="text-wrap"><?= htmlspecialchars($row['curso']) ?></td>
                    <td class="no-wrap text-center"><?= (int)$row['ano'] ?>º</td>
                    <td class="no-wrap text-center"><?= htmlspecialchars($row['semestre']) ?></td>
                    <td class="text-wrap"><?= htmlspecialchars($row['disciplina']) ?></td>
                    <td class="no-wrap text-center"><?= htmlspecialchars($row['turno']) ?></td>
                    <td class="no-wrap"><?= htmlspecialchars($row['sala']) ?></td>
                    <td class="no-wrap text-center"><?= substr($row['hora_inicio'], 0, 5) ?> - <?= substr($row['hora_fim'], 0, 5) ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php
$queryString = http_build_query([
    'tipo'     => $tipo,
    'curso'    => $curso ?? '',
    'ano'      => $ano ?? '',
    'semestre' => $semestre ?? '',
    'turno'    => $turno ?? ''
]);
?>

<a href="baixar_horario_pdf.php?<?= $queryString ?>" target="_blank" style="text-decoration: none;">
    <button class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 8px;">
        <i class="fas fa-download"></i>
        Baixar Horário (PDF)
    </button>
</a>

