<?php
session_start();
require_once __DIR__ . '/../../config/database.php';

if (!isset($_SESSION['admin'])) {
    header('Location: /smartkampus/public/index.php');
    exit;
}

$id   = $_POST['id'];
$acao = $_POST['acao'];

if ($acao === 'rejeitar') {
    $stmt = $conn->prepare("UPDATE reservas_sala SET estado='rejeitada' WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    header("Location: reservas.php");
    exit;
}

// ================= APROVAR =================

// Buscar reserva
$res = $conn->prepare("
    SELECT sala, hora_inicio, hora_fim
    FROM reservas_sala
    WHERE id = ?
");
$res->bind_param("i", $id);
$res->execute();
$reserva = $res->get_result()->fetch_assoc();

// Verificar conflito
$conflito = $conn->prepare("
    SELECT 1 FROM (
        SELECT sala, hora_inicio, hora_fim FROM horarios
        UNION ALL
        SELECT sala, hora_inicio, hora_fim FROM testes
        UNION ALL
        SELECT sala, hora_inicio, hora_fim FROM exames
        UNION ALL
        SELECT sala, hora_inicio, hora_fim FROM reservas_sala WHERE estado='aprovada'
    ) t
    WHERE sala = ?
    AND NOT (hora_fim <= ? OR hora_inicio >= ?)
");

$conflito->bind_param(
    "sss",
    $reserva['sala'],
    $reserva['hora_inicio'],
    $reserva['hora_fim']
);
$conflito->execute();
$conflito->store_result();

if ($conflito->num_rows > 0) {
    die("Conflito detectado. Não é possível aprovar.");
}

// Aprovar
$aprovar = $conn->prepare("UPDATE reservas_sala SET estado='aprovada' WHERE id=?");
$aprovar->bind_param("i", $id);
$aprovar->execute();

header("Location: reservas.php");
exit;
