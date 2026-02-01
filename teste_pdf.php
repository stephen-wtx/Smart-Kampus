<?php
require_once __DIR__ . '/libs/fpdf/fpdf.php';

$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial','B',16);
$pdf->Cell(40,10,'FPDF a funcionar!');
$pdf->Output();
