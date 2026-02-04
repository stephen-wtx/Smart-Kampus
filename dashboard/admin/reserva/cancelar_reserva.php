<?php
session_start();
require_once __DIR__ . '/../../../config/database.php';

if (!isset($_POST['id'])) {
    die("ID inválido");
}

$id = (int) $_POST['id'];

/* Buscar sala da reserva */
$stmt = $conn->prepare("SELECT sala FROM reservas WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result()->fetch_assoc();

if (!$res) {
    die("Reserva não encontrada");
}

$sala = $res['sala'];

/* Apagar reserva */
$del = $conn->prepare("DELETE FROM reservas WHERE id = ?");
$del->bind_param("i", $id);
$del->execute();

/* Libertar sala */
$upd = $conn->prepare("UPDATE salas SET estado = 'livre' WHERE nome = ?");
$upd->bind_param("s", $sala);
$upd->execute();

/* Volta */
header("Location: ../index.php");
exit;
