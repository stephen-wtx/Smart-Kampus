<?php
session_start();
require_once '../../config/database.php';
if (!isset($_SESSION['user'])) {
    header('Location: /smartkampus/public/index.php');
    exit;
}

if (!isset($_FILES['calendario_pdf'])) {
    die('Nenhum ficheiro enviado.');
}

$pdf = $_FILES['calendario_pdf'];

if ($pdf['type'] !== 'application/pdf') {
    die('Apenas PDF permitido.');
}

$dir = '../../public/uploads/calendario/';
if (!is_dir($dir)) mkdir($dir, 0777, true);

$nomeFinal = time() . '_' . basename($pdf['name']);
$caminhoCompleto = $dir . $nomeFinal;

if (!move_uploaded_file($pdf['tmp_name'], $caminhoCompleto)) {
    die('Falha ao mover o arquivo.');
}

// Apaga calendário antigo
$conn->query("DELETE FROM calendario_academico");

$stmt = $conn->prepare("INSERT INTO calendario_academico (nome_ficheiro, caminho) VALUES (?, ?)");
$stmt->bind_param('ss', $pdf['name'], $nomeFinal);
$stmt->execute();

header('Location: index.php');
exit;
