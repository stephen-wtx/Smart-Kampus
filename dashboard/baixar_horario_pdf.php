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
// DEFINIR TABELA E COLUNAS
// =========================
if ($tipo === 'teste') {
    $tabela = 'testes';
    $colunas = ['Curso', 'Disciplina', 'Ano', 'Semestre', 'Turno', 'Sala', 'Dia', 'Data', 'Hora', 'Duração'];
} elseif ($tipo === 'exame') {
    $tabela = 'exames';
    $colunas = ['Curso', 'Disciplina', 'Ano', 'Semestre', 'Turno', 'Sala', 'Dia', 'Data', 'Hora', 'Duração'];
} else {
    $tabela = 'horarios';
    $colunas = ['Dia', 'Curso', 'Ano', 'Semestre', 'Disciplina', 'Turno', 'Sala', 'Horário'];
}

// =========================
// QUERY COM ORDER BY ADEQUADO
// =========================
if ($tabela === 'horarios') {
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
        FROM $tabela
        $where
        ORDER BY FIELD(dia_semana,'Segunda-feira','Terça-feira','Quarta-feira','Quinta-feira','Sexta-feira','Sábado'),
                 hora_inicio,
                 curso,
                 ano
    ";
} else {
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
}

$result = $conn->query($sql);

if (!$result || $result->num_rows === 0) {
    die('Sem dados para gerar o PDF.');
}

// =========================
// CRIAR PDF COM LAYOUT MELHOR
// =========================
$pdf = new FPDF('L', 'mm', 'A4'); // Landscape
$pdf->AddPage();
$pdf->SetFont('Arial', '', 10);

// CORES (RGB)
$azulEscuro = array(59, 67, 206);   // #3b43ce
$azulClaro  = array(248, 250, 252); // #f8fafc
$cinzaClaro = array(241, 245, 249); // #f1f5f9
$cinzaTexto = array(71, 85, 105);   // #475569
$preto      = array(15, 23, 42);    // #0f172a
$branco     = array(255, 255, 255);

// =========================
// CABEÇALHO DO PDF
// =========================
$pdf->SetFillColor($azulEscuro[0], $azulEscuro[1], $azulEscuro[2]);
$pdf->SetTextColor($branco[0], $branco[1], $branco[2]);
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 15, utf8_decode('SMART KAMPUS'), 0, 1, 'C', true);

$pdf->SetFont('Arial', 'B', 14);
$pdf->SetTextColor($preto[0], $preto[1], $preto[2]);
$pdf->Cell(0, 10, utf8_decode('HORÁRIO ACADÊMICO - ' . strtoupper($tipo)), 0, 1, 'C');

// Informações dos filtros
$pdf->SetFont('Arial', '', 10);
$pdf->SetTextColor($cinzaTexto[0], $cinzaTexto[1], $cinzaTexto[2]);

$filtrosTexto = 'Curso: ' . ($curso ?: 'Todos') . ' | ';
$filtrosTexto .= 'Ano: ' . ($ano ?: 'Todos') . ' | ';
$filtrosTexto .= 'Semestre: ' . ($semestre ?: 'Ambos') . ' | ';
$filtrosTexto .= 'Turno: ' . ($turno ?: 'Todos') . ' | ';
$filtrosTexto .= 'Gerado em: ' . date('d/m/Y H:i');

$pdf->Cell(0, 8, utf8_decode($filtrosTexto), 0, 1, 'C');
$pdf->Ln(5);

// =========================
// DEFINIÇÃO DAS COLUNAS (LARGURAS)
// =========================
if ($tipo === 'teste' || $tipo === 'exame') {
    // Para testes/exames (10 colunas)
    $widths = [40, 50, 15, 20, 20, 30, 25, 25, 30, 20]; // Total: 275mm
    $aligns = ['L', 'L', 'C', 'C', 'C', 'L', 'L', 'C', 'C', 'C'];
} else {
    // Para aulas (8 colunas)
    $widths = [25, 50, 15, 20, 55, 20, 30, 25]; // Total: 240mm
    $aligns = ['L', 'L', 'C', 'C', 'L', 'C', 'L', 'C'];
}

// =========================
// CABEÇALHO DA TABELA
// =========================
$pdf->SetFillColor($azulClaro[0], $azulClaro[1], $azulClaro[2]);
$pdf->SetTextColor($cinzaTexto[0], $cinzaTexto[1], $cinzaTexto[2]);
$pdf->SetFont('Arial', 'B', 9);
$pdf->SetDrawColor(226, 232, 240); // #e2e8f0

// Desenhar cabeçalho
foreach ($colunas as $i => $coluna) {
    $pdf->Cell($widths[$i], 10, utf8_decode($coluna), 1, 0, 'C', true);
}
$pdf->Ln();

// =========================
// DADOS DA TABELA
// =========================
$pdf->SetFont('Arial', '', 9);
$pdf->SetTextColor($preto[0], $preto[1], $preto[2]);
$pdf->SetFillColor($branco[0], $branco[1], $branco[2]);

$fill = false; // Para zebrar as linhas
$rowHeight = 8;

while ($row = $result->fetch_assoc()) {
    // Alternar cor de fundo
    if ($fill) {
        $pdf->SetFillColor($cinzaClaro[0], $cinzaClaro[1], $cinzaClaro[2]);
    } else {
        $pdf->SetFillColor($branco[0], $branco[1], $branco[2]);
    }
    
    // Determinar altura da linha (para texto que pode quebrar)
    $maxHeight = $rowHeight;
    
    // Calcular altura necessária para cada célula
    $cellHeights = [];
    foreach ($colunas as $i => $coluna) {
        $text = '';
        
        switch ($coluna) {
            case 'Dia':
                $text = $row['dia_semana'] ?? '-';
                break;
            case 'Curso':
                $text = $row['curso'];
                break;
            case 'Ano':
                $text = $row['ano'] . 'º';
                break;
            case 'Semestre':
                $text = $row['semestre'];
                break;
            case 'Disciplina':
                $text = $row['disciplina'];
                break;
            case 'Turno':
                $text = $row['turno'];
                break;
            case 'Sala':
                $text = $row['sala'];
                break;
            case 'Horário':
                $text = substr($row['hora_inicio'], 0, 5) . ' - ' . substr($row['hora_fim'], 0, 5);
                break;
            case 'Data':
                $text = date('d/m/Y', strtotime($row['data']));
                break;
            case 'Hora':
                $text = substr($row['hora_inicio'], 0, 5) . ' - ' . substr($row['hora_fim'], 0, 5);
                break;
            case 'Duração':
                $inicio = strtotime($row['hora_inicio']);
                $fim = strtotime($row['hora_fim']);
                $duracaoHoras = ($fim - $inicio) / 3600;
                $text = intval($duracaoHoras) . ' h';
                break;
        }
        
        // Calcular quantas linhas o texto vai ocupar
        $textWidth = $pdf->GetStringWidth(utf8_decode($text));
        $lines = ceil($textWidth / ($widths[$i] - 2)); // -2 para margem
        $cellHeights[$i] = max($rowHeight, $lines * $rowHeight);
        $maxHeight = max($maxHeight, $cellHeights[$i]);
    }
    
    // Desenhar células
    $x = $pdf->GetX();
    $y = $pdf->GetY();
    
    // Primeiro desenhar o fundo da linha inteira
    $pdf->SetDrawColor(226, 232, 240); // Borda cinza claro
    $pdf->Rect($x, $y, array_sum($widths), $maxHeight, 'FD'); // Fill and Draw
    
    // Agora colocar o texto em cada célula
    $pdf->SetDrawColor(226, 232, 240);
    foreach ($colunas as $i => $coluna) {
        $text = '';
        
        switch ($coluna) {
            case 'Dia':
                $text = $row['dia_semana'] ?? '-';
                break;
            case 'Curso':
                $text = $row['curso'];
                break;
            case 'Ano':
                $text = $row['ano'] . 'º';
                break;
            case 'Semestre':
                $text = $row['semestre'];
                break;
            case 'Disciplina':
                $text = $row['disciplina'];
                break;
            case 'Turno':
                $text = $row['turno'];
                break;
            case 'Sala':
                $text = $row['sala'];
                break;
            case 'Horário':
                $text = substr($row['hora_inicio'], 0, 5) . ' - ' . substr($row['hora_fim'], 0, 5);
                break;
            case 'Data':
                $text = date('d/m/Y', strtotime($row['data']));
                break;
            case 'Hora':
                $text = substr($row['hora_inicio'], 0, 5) . ' - ' . substr($row['hora_fim'], 0, 5);
                break;
            case 'Duração':
                $inicio = strtotime($row['hora_inicio']);
                $fim = strtotime($row['hora_fim']);
                $duracaoHoras = ($fim - $inicio) / 3600;
                $text = intval($duracaoHoras) . ' h';
                break;
        }
        
        // Centralizar verticalmente
        $textY = $y + (($maxHeight - $rowHeight) / 2);
        
        $pdf->SetXY($x, $textY);
        $pdf->Cell($widths[$i], $rowHeight, utf8_decode($text), 0, 0, $aligns[$i]);
        
        // Desenhar borda vertical
        if ($i < count($colunas) - 1) {
            $pdf->Line($x + $widths[$i], $y, $x + $widths[$i], $y + $maxHeight);
        }
        
        $x += $widths[$i];
    }
    
    $pdf->SetXY(10, $y + $maxHeight);
    $fill = !$fill;
}

// =========================
// RODAPÉ
// =========================
$pdf->Ln(10);
$pdf->SetFont('Arial', 'I', 8);
$pdf->SetTextColor($cinzaTexto[0], $cinzaTexto[1], $cinzaTexto[2]);
$pdf->Cell(0, 6, utf8_decode('Documento gerado automaticamente pelo sistema SMART KAMPUS - Universidade Católica de Moçambique'), 0, 1, 'C');
$pdf->Cell(0, 6, utf8_decode('© ' . date('Y') . ' - Todos os direitos reservados'), 0, 1, 'C');

// =========================
// OUTPUT
// =========================
$pdf->Output('I', 'horario_' . $tipo . '_' . date('Y-m-d') . '.pdf');