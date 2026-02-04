<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

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
$data         = $_POST['data']; // nova coluna obrigatória
$curso        = $_POST['curso'];
$ano          = $_POST['ano'];
$semestre     = $_POST['semestre'];
$disciplina   = $_POST['disciplina'];
$turno        = $_POST['turno'];
$sala         = $_POST['sala'];
$hora_inicio  = $_POST['hora_inicio'];
$hora_fim     = $_POST['hora_fim'];

// 0️⃣ Validação da data
if (!DateTime::createFromFormat('Y-m-d', $data)) {
    $_SESSION['erro'] = "Data inválida!";
    header("Location: editar_exame.php?id=$id");
    exit;
}

// 1️⃣ Validação: horário início < fim, turno, conflitos
$erro = validarHorario(
    $conn,
    'exame',      // tipo do evento
    $dia_semana,
    $data,
    $curso,
    $ano,
    $semestre,
    $turno,
    $sala,
    $hora_inicio,
    $hora_fim,
    $id // Ignora o próprio registro
);

if ($erro) {
    $_SESSION['erro'] = $erro;
    header("Location: editar_exame.php?id=$id");
    exit;
}

// 2️⃣ Cálculo da duração
$hora_inicio_dt = new DateTime($hora_inicio);
$hora_fim_dt    = new DateTime($hora_fim);
$duracao        = intval(($hora_fim_dt->getTimestamp() - $hora_inicio_dt->getTimestamp()) / 60);

// 3️⃣ Atualiza o exame no banco
$stmt = $conn->prepare("
    UPDATE exames SET
        dia_semana  = ?,
        data        = ?,
        curso       = ?,
        ano         = ?,
        semestre    = ?,
        disciplina  = ?,
        turno       = ?,
        sala        = ?,
        hora_inicio = ?,
        hora_fim    = ?,
        duracao     = ?
    WHERE id = ?
");

$stmt->bind_param(
    "sssssssssssi", // ✅ semestre agora como string
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
    $id
);

$stmt->execute();

// 4️⃣ Mensagem de sucesso
$_SESSION['sucesso'] = "Exame atualizado com sucesso!";
header("Location: listar_exames.php");
exit;
?>
