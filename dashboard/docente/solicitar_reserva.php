<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once __DIR__ . '/../../config/database.php';

if (!isset($_SESSION['user'])) {
    die("Erro: sessão inválida.");
}

$user = $_SESSION['user'];

$sala        = $_POST['sala'] ?? null;
$dia_semana  = $_POST['dia_semana'] ?? null;
$data        = $_POST['data'] ?? null;
$hora_inicio = $_POST['hora_inicio'] ?? null;
$hora_fim    = $_POST['hora_fim'] ?? null;
$curso       = $_POST['curso'] ?? null;
$disciplina  = $_POST['disciplina'] ?? null;
$turno       = $_POST['turno'] ?? null;
$finalidade  = $_POST['finalidade'] ?? null;

/* ================= VALIDAÇÕES ================= */

// Campos obrigatórios
if (
    empty($sala) || empty($dia_semana) || empty($data) ||
    empty($hora_inicio) || empty($hora_fim) ||
    empty($curso) || empty($disciplina) || empty($turno)
) {
    die("Erro: todos os campos obrigatórios devem ser preenchidos.");
}

// Data válida
$hoje = date('Y-m-d');
if ($data < $hoje) {
    die("Erro: não é possível reservar para datas passadas.");
}

// Hora lógica
if ($hora_fim <= $hora_inicio) {
    die("Erro: hora fim deve ser maior que a hora início.");
}

// Curso válido
$cursosValidos = [
    'Administração Pública',
    'Contabilidade & Auditoria',
    'Direito',
    'Economia e Gestão',
    'Gestão de Recursos Humanos',
    'Meio Ambiente',
    'Tecnologia de Informação'
];

if (!in_array($curso, $cursosValidos)) {
    die("Erro: curso inválido.");
}

// Disciplina válida
$disciplina = trim($disciplina);

if (!preg_match('/^[A-Za-zÀ-ÿ\s]+(\s[I,V,X]+)?$/', $disciplina)) {
    die("Erro: nome de disciplina inválido.");
}


// Turno coerente
switch ($turno) {
    case 'Manhã':
        if ($hora_inicio < '08:00' || $hora_fim > '12:00') {
            die("Erro: horário fora do turno da manhã.");
        }
        break;

    case 'Tarde':
        if ($hora_inicio < '13:00' || $hora_fim > '18:00') {
            die("Erro: horário fora do turno da tarde.");
        }
        break;

    case 'Noite':
        if ($hora_inicio < '18:30' || $hora_fim > '22:30') {
            die("Erro: horário fora do turno da noite.");
        }
        break;

    default:
        die("Erro: turno inválido.");
}

/* ================= VALIDAÇÕES EXISTENTES ================= */

// Já possui reserva
$check = $conn->prepare("SELECT 1 FROM reservas WHERE docente_id = ?");
$check->bind_param("i", $user['id']);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    die("Erro: já possui uma reserva.");
}

// Conflitos de sala
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

// Inserção
$insert = $conn->prepare("
    INSERT INTO reservas
    (docente_id, docente_nome, sala, dia_semana, data, hora_inicio, hora_fim, curso, disciplina, turno, finalidade)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
");

$insert->bind_param(
    "issssssssss",
    $user['id'],
    $user['name'],
    $sala,
    $dia_semana,
    $data,
    $hora_inicio,
    $hora_fim,
    $curso,
    $disciplina,
    $turno,
    $finalidade
);

$insert->execute();

header("Location: index.php");
exit;
