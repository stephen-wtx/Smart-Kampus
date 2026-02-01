<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../libs/fpdf/fpdf.php';

// =========================
// RECEBER FILTROS
// =========================
$tipo     = $_GET['tipo'] ?? 'aula';
$curso    = $_GET['curso'] ?? '';
$ano      = $_GET['ano'] ?? '';
$semestre = $_GET['semestre'] ?? '';
$turno    = $_GET['turno'] ?? '';

// =========================
// MONTAR FILTROS
// =========================
$filtros = [];

if ($curso !== '')    $filtros[] = "curso = '" . $conn->real_escape_string($curso) . "'";
if ($ano !== '')      $filtros[] = "ano = " . (int)$ano;
if ($semestre !== '') $filtros[] = "semestre = '" . $conn->real_escape_string($semestre) . "'";
if ($turno !== '')    $filtros[] = "turno = '" . $conn->real_escape_string($turno) . "'";

$where = $filtros ? 'WHERE ' . implode(' AND ', $filtros) : '';

// =========================
// DEFINIR TABELA
// =========================
if ($tipo === 'teste') {
    $tabela = 'testes';
} elseif ($tipo === 'exame') {
    $tabela = 'exames';
} else {
    $tabela = 'horarios';
}

// =========================
// QUERY
// =========================
$sql = "
    SELECT *
    FROM $tabela
    $where
    ORDER BY hora_inicio
";

$result = $conn->query($sql);

if (!$result || $result->num_rows === 0) {
    die('Sem dados para gerar o PDF.');
}

// =========================
// CRIAR PDF
// =========================
$pdf = new FPDF('L', 'mm', 'A4');
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 14);

// 🔧 TÍTULO (UTF-8 CORRIGIDO)
$pdf->Cell(
    0,
    10,
    utf8_decode('Horário - ' . strtoupper($tipo)),
    0,
    1,
    'C'
);

$pdf->Ln(5);

$pdf->SetFont('Arial', 'B', 10);

// 🔧 CABEÇALHOS (UTF-8 CORRIGIDO)
$headers = ['Curso','Ano','Semestre','Disciplina','Turno','Sala','Dia','Hora'];
$widths  = [60,15,25,60,30,25,30,30];

foreach ($headers as $i => $h) {
    $pdf->Cell($widths[$i], 8, utf8_decode($h), 1, 0, 'C');
}
$pdf->Ln();

// =========================
// DADOS
// =========================
$pdf->SetFont('Arial', '', 9);

while ($row = $result->fetch_assoc()) {

    $pdf->Cell(60, 8, utf8_decode($row['curso']), 1);
    $pdf->Cell(15, 8, $row['ano'] . 'º', 1);
    $pdf->Cell(25, 8, utf8_decode($row['semestre']), 1);
    $pdf->Cell(60, 8, utf8_decode($row['disciplina']), 1);
    $pdf->Cell(30, 8, utf8_decode($row['turno']), 1);
    $pdf->Cell(25, 8, utf8_decode($row['sala']), 1);
    $pdf->Cell(30, 8, utf8_decode($row['dia_semana'] ?? '-'), 1);
    $pdf->Cell(
        30,
        8,
        substr($row['hora_inicio'],0,5) . ' - ' . substr($row['hora_fim'],0,5),
        1
    );

    $pdf->Ln();
}

// =========================
// OUTPUT
// =========================
$pdf->Output('I', 'horario.pdf');
