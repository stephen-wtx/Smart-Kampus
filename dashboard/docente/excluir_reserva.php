<?php
session_start();
require_once __DIR__ . '/../../config/database.php';

if (!isset($_POST['id'])) {
    die("Reserva inválida.");
}

$id = intval($_POST['id']);

// Deleta a reserva
$stmt = $conn->prepare("DELETE FROM reservas WHERE id = ? AND estado = 'rejeitada'");
$stmt->bind_param("i", $id);
$stmt->execute();

header("Location: index.php");
exit;
