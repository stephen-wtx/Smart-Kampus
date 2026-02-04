<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../../../config/database.php';

if (!isset($_POST['id'], $_POST['acao'])) {
    die('Dados inválidos');
}

$id = (int) $_POST['id'];
$acao = $_POST['acao'];

if ($acao === 'aprovar') {
    $estado = 'aprovada';
} elseif ($acao === 'rejeitar') {
    $estado = 'rejeitada';
} else {
    die('Ação inválida');
}

$stmt = $conn->prepare("UPDATE reservas SET estado = ? WHERE id = ?");
if (!$stmt) {
    die('Erro na query: ' . $conn->error);
}

$stmt->bind_param("si", $estado, $id);

if (!$stmt->execute()) {
    die('Erro ao executar: ' . $stmt->error);
}

header('Location: ../index.php');
exit;
