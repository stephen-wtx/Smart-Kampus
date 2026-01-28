<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


session_start();
require_once __DIR__ . '/../../config/database.php';

$user = $_SESSION['user'];

$sala        = $_POST['sala'];
$dia_semana  = $_POST['dia_semana'];
$data        = $_POST['data'];
$hora_inicio = $_POST['hora_inicio'];
$hora_fim    = $_POST['hora_fim'];
$finalidade  = $_POST['finalidade'] ?? null;

// Verifica se já existe reserva
$check = $conn->prepare("
    SELECT 1 FROM reservas WHERE docente_id = ?
");
$check->bind_param("i", $user['id']);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    die("Erro: já possui uma reserva.");
}

// Verificar conflitos
$sqlConflito = "
    SELECT 1 FROM (
        SELECT sala, hora_inicio, hora_fim FROM horarios
        UNION ALL
        SELECT sala, hora_inicio, hora_fim FROM testes
        UNION ALL
        SELECT sala, hora_inicio, hora_fim FROM exames
        UNION ALL
        SELECT sala, hora_inicio, hora_fim FROM reservas
        WHERE estado IN ('pendente','aprovada')
    ) t
    WHERE sala = ?
    AND NOT (hora_fim <= ? OR hora_inicio >= ?)
";

$stmt = $conn->prepare($sqlConflito);
$stmt->bind_param("sss", $sala, $hora_inicio, $hora_fim);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    die("Erro: sala ocupada neste horário.");
}

// Inserir reserva
$insert = $conn->prepare("
    INSERT INTO reservas_sala
    (docente_id, docente_nome, sala, dia_semana, data, hora_inicio, hora_fim, finalidade)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
");

$insert->bind_param(
    "isssssss",
    $user['id'],
    $user['name'],
    $sala,
    $dia_semana,
    $data,
    $hora_inicio,
    $hora_fim,
    $finalidade
);

$insert->execute();

header("Location: index.php");
exit;
