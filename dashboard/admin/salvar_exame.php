<?php
session_start();
require_once __DIR__ . "/../../config/database.php";
require_once __DIR__ . "/validar_horario.php"; // validações centralizadas

// Recebe dados do POST
$dia_semana  = $_POST['dia_semana'];
$data        = $_POST['data'];
$curso       = $_POST['curso'];
$ano         = $_POST['ano'];
$semestre    = $_POST['semestre'];
$disciplina  = $_POST['disciplina'];
$turno       = $_POST['turno'];
$sala        = $_POST['sala'];
$hora_inicio = $_POST['hora_inicio'];
$hora_fim    = $_POST['hora_fim'];

// 0️⃣ Validação da data
if (!DateTime::createFromFormat('Y-m-d', $data)) {
    $_SESSION['erro'] = "Data inválida!";
    header("Location: criar_exame.php");
    exit;
}

// 1️⃣ Validação geral (horas, turno, conflitos)
$erro = validarHorario(
    $conn,
    'exame',       // tipo do evento
    $dia_semana,
    $data,
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
    header("Location: criar_exame.php");
    exit;
}

// 2️⃣ Cálculo da duração (em minutos)
$hora_inicio_dt = new DateTime($hora_inicio);
$hora_fim_dt    = new DateTime($hora_fim);
$duracao        = intval(($hora_fim_dt->getTimestamp() - $hora_inicio_dt->getTimestamp()) / 60);

// 3️⃣ Inserção no banco
$stmt = $conn->prepare("
    INSERT INTO exames
    (dia_semana, data, curso, ano, semestre, disciplina, turno, sala, hora_inicio, hora_fim, duracao, criado_por)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
");

$stmt->bind_param(
    "sssssssssssi",
    $dia_semana,
    $data,
    $curso,
    $ano,
    $semestre,
    $disciplina,
    $turno,
    $sala,
    $hora_inicio,
    $hora_fim,
    $duracao,
    $_SESSION['user']['id']
);

$stmt->execute();

// 4️⃣ Sucesso
$_SESSION['sucesso'] = "Exame criado com sucesso!";
header("Location: criar_exame.php");
exit;
?>
