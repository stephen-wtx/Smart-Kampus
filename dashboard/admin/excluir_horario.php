<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
require_once __DIR__ . "/../../config/database.php";

// Proteção de role (opcional)
// if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
//     http_response_code(403);
//     exit('Acesso negado');
// }

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
