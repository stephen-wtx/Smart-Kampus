<?php
session_start();
require_once __DIR__ . "/../../../../config/database.php";
require_once __DIR__ . "/../validacao/validar_horario.php"; // função centralizada de validação
if (!isset($_SESSION['user'])) {
    header('Location: /smartkampus/public/index.php');
    exit;
}
$user = $_SESSION['user'];

// Validação mínima
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['id'])) {
    die("Requisição inválida.");
}

// Captura dados
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

// 1️⃣ Validação: horário início < fim e conflito de sala

$erro = validarHorario(
    $conn,
    'aula',        // tipo
    $dia_semana,   // dia_semana
    null,          // data
    $curso,
    $ano,
    $semestre,
    $turno,
    $sala,
    $hora_inicio,
    $hora_fim,
    $id            
);


if ($erro) {
    $_SESSION['erro'] = $erro;
    header("Location: editar_horario.php?id=$id");
    exit;
}

// 2️⃣ Atualiza o horário no banco
$stmt = $conn->prepare("
    UPDATE horarios SET
        dia_semana  = ?,
        curso       = ?,
        ano         = ?,
        semestre    = ?,
        disciplina  = ?,
        turno       = ?,
        sala        = ?,
        hora_inicio = ?,
        hora_fim    = ?
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

// 3️⃣ Mensagem de sucesso
$_SESSION['sucesso'] = "Horário atualizado com sucesso!";
header("Location: listar_horarios.php");
exit;
?>

