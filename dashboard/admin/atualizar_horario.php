<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
require_once __DIR__ . "/../../config/database.php";

// (proteção de role pode ser ativada depois)
// if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
//     http_response_code(403);
//     exit('Acesso negado');
// }

// Validação mínima
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['id'])) {
    die("Requisição inválida.");
}

// Captura dos dados
$id           = (int) $_POST['id'];
$dia_semana   = $_POST['dia_semana'];
$curso        = $_POST['curso'];
$ano          = $_POST['ano'];
$semestre     = $_POST['semestre'];
$disciplina   = $_POST['disciplina'];
$turno        = $_POST['turno'];
$sala         = $_POST['sala'];
$hora_inicio  = $_POST['hora_inicio'];
$hora_fim     = $_POST['hora_fim'];

// UPDATE no banco
$stmt = $conn->prepare("
    UPDATE horarios SET
        dia_semana   = ?,
        curso        = ?,
        ano          = ?,
        semestre     = ?,
        disciplina   = ?,
        turno        = ?,
        sala         = ?,
        hora_inicio  = ?,
        hora_fim     = ?
    WHERE id = ?
");

$stmt->bind_param(
    "ssissssssi",
    $dia_semana,
    $curso,
    $ano,
    $semestre,
    $disciplina,
    $turno,
    $sala,
    $hora_inicio,
    $hora_fim,
    $id
);

$stmt->execute();

// Mensagem flash
$_SESSION['sucesso'] = "Horário atualizado com sucesso!";

// Redireciona de volta à lista
header("Location: listar_horarios.php");
exit;
