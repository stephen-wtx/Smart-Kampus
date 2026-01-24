<?php
// Ativa exibição completa de erros
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


session_start();
require_once __DIR__ . "/../../config/database.php";
require_once __DIR__ . "/validar_horario.php"; // função centralizada

// Recebe dados do POST
$dia_semana  = $_POST['dia_semana'];
$curso       = $_POST['curso'];
$ano         = $_POST['ano'];
$semestre    = $_POST['semestre'];
$disciplina  = $_POST['disciplina'];
$turno       = $_POST['turno'];
$sala        = $_POST['sala'];
$hora_inicio = $_POST['hora_inicio'];
$hora_fim    = $_POST['hora_fim'];

// 1️⃣ Valida horário (início < fim e conflito de sala)
$erro = validarHorario(
    $conn,
    'aula',        // tipo
    $dia_semana,   // dia_semana
    null,          // data não se aplica
    $curso,
    $ano,
    $semestre,
    $turno,
    $sala,
    $hora_inicio,
    $hora_fim
);


if ($erro) {
    $_SESSION['erro'] = $erro;
    header("Location: criar_horario.php");
    exit;
}


// 2️⃣ Inserção no banco
$stmt = $conn->prepare("
    INSERT INTO horarios
    (dia_semana, curso, ano, semestre, disciplina, turno, sala, hora_inicio, hora_fim, criado_por)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
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
    $_SESSION['user']['id']
);

$stmt->execute();

// 3️⃣ Mensagem de sucesso
$_SESSION['sucesso'] = "Horário criado com sucesso!";
header("Location: criar_horario.php");
exit;
?>
