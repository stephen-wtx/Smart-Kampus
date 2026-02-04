<?php

session_start();
require_once __DIR__ . "/../../../../config/database.php";
if (!isset($_SESSION['user'])) {
    header('Location: /smartkampus/public/index.php');
    exit;
}
$user = $_SESSION['user'];

// Validação mínima
if (empty($_GET['id'])) {
    die("ID do exame não fornecido.");
}

$id = (int) $_GET['id'];

// Deleta do banco
$stmt = $conn->prepare("DELETE FROM exames WHERE id = ?");
if (!$stmt) {
    die("Erro ao preparar a query: " . $conn->error);
}
$stmt->bind_param("i", $id);
$stmt->execute();

// Mensagem flash
$_SESSION['sucesso'] = "Exame excluído com sucesso!";

// Redireciona de volta à lista
header("Location: listar_exames.php");
exit;
?>
