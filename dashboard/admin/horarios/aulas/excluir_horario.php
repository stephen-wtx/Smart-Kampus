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
    die("ID do horário não fornecido.");
}

$id = (int) $_GET['id'];

// Deleta do banco
$stmt = $conn->prepare("DELETE FROM horarios WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

// Mensagem flash
$_SESSION['sucesso'] = "Horário excluído com sucesso!";

// Redireciona de volta à lista
header("Location: listar_horarios.php");
exit;
