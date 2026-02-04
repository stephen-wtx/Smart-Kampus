<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
require_once __DIR__ . "/../../../../config/database.php";
if (!isset($_SESSION['user'])) {
    header('Location: /smartkampus/public/index.php');
    exit;
}


// Validação mínima
if (empty($_GET['id'])) {
    die("ID do teste não fornecido.");
}

$id = (int) $_GET['id'];

// Deleta do banco
$stmt = $conn->prepare("DELETE FROM testes WHERE id = ?");
if (!$stmt) {
    die("Erro ao preparar a query: " . $conn->error);
}
$stmt->bind_param("i", $id);
$stmt->execute();

// Mensagem flash
$_SESSION['sucesso'] = "Teste excluído com sucesso!";

// Redireciona de volta à lista
header("Location: listar_testes.php");
exit;
?>
