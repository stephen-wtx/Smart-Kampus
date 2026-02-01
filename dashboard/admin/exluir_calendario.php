<?php
session_start();
require_once '../../config/database.php';

$id = (int)($_POST['id'] ?? 0);

$res = $conn->query("SELECT * FROM calendario_academico WHERE id = $id");

if ($res && $res->num_rows > 0) {
    $c = $res->fetch_assoc();
    @unlink('../../public/uploads/calendario/' . $c['caminho']);
    $conn->query("DELETE FROM calendario_academico WHERE id = $id");
}

header('Location: index.php');
exit;
