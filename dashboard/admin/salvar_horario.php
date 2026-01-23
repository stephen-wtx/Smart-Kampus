<?php
session_start();
require_once __DIR__ . "/../../config/database.php";

// Recebe dados do POST
$dia_semana = $_POST['dia_semana'];
$sala = $_POST['sala'];
$hora_inicio = $_POST['hora_inicio']; // formato 24h: HH:MM
$hora_fim = $_POST['hora_fim'];

// Ajusta hora_fim para validar intervalos
$hora_inicio_dt = new DateTime($hora_inicio);
$hora_fim_dt = new DateTime($hora_fim);

// Consulta horários já existentes na mesma sala e dia
$stmt = $conn->prepare("
    SELECT * FROM horarios 
    WHERE sala = ? AND dia_semana = ?
");
$stmt->bind_param("ss", $sala, $dia_semana);
$stmt->execute();
$result = $stmt->get_result();

// Verifica conflitos
$conflito = false;
while ($row = $result->fetch_assoc()) {
    $exist_inicio = new DateTime($row['hora_inicio']);
    $exist_fim = new DateTime($row['hora_fim']);

    // Adiciona 1 minuto de folga para o fim da aula existente
    $exist_fim->modify('+1 minute');

    // Se houver sobreposição
    if ($hora_inicio_dt < $exist_fim && $hora_fim_dt > $exist_inicio) {
        $conflito = true;
        break;
    }
}

if ($conflito) {
    $_SESSION['erro'] = "Horário inválido! Essa sala já está ocupada nesse período.";
    header("Location: index.php");
    exit;
}

// Se não houver conflito, insere normalmente
$stmt = $conn->prepare("
    INSERT INTO horarios
    (dia_semana, curso, ano, semestre, disciplina, turno, sala, hora_inicio, hora_fim, criado_por)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
");

$stmt->bind_param(
    "ssissssssi",
    $_POST['dia_semana'],
    $_POST['curso'],
    $_POST['ano'],
    $_POST['semestre'],
    $_POST['disciplina'],
    $_POST['turno'],
    $_POST['sala'],
    $_POST['hora_inicio'],
    $_POST['hora_fim'],
    $_SESSION['user']['id']
);

$stmt->execute();
$_SESSION['sucesso'] = "Horário criado com sucesso!";
header("Location: index.php");
exit;
