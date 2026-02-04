<?php
require_once __DIR__ . '/../../../config/database.php';

if (!isset($_GET['id'])) {
    header('Location: ../index.php');
    exit;
}

$id = intval($_GET['id']);

$stmt = $conn->prepare("UPDATE reservas SET estado = 'aprovada' WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

header('Location: ../index.php');
exit;
