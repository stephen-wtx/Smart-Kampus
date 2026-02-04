<?php
session_start();
require_once __DIR__ . "/../../../../config/database.php";
require_once __DIR__ . "/../validacao/validar_horario.php";

if (!isset($_SESSION['user'])) {
    header('Location: /smartkampus/public/index.php');
    exit;
}
$user = $_SESSION['user'];

// Log para depuração
error_log("=== SALVAR EXAME ===");
error_log("Dados POST: " . print_r($_POST, true));

// Verificar dados POST
if (empty($_POST)) {
    $_SESSION['erro'] = "Nenhum dado recebido!";
    header("Location: criar_exame.php");
    exit;
}

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
    error_log("Data inválida: $data");
    header("Location: criar_exame.php");
    exit;
}

// 1️⃣ Validação geral
$erro = validarHorario(
    $conn,
    'exame',
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
    error_log("Erro na validação: $erro");
    header("Location: criar_exame.php");
    exit;
}

try {
    // 2️⃣ Cálculo da duração
    $hora_inicio_dt = new DateTime($hora_inicio);
    $hora_fim_dt    = new DateTime($hora_fim);
    $duracao        = intval(($hora_fim_dt->getTimestamp() - $hora_inicio_dt->getTimestamp()) / 60);
    
    error_log("Duração calculada: $duracao minutos");
    
    // 3️⃣ Inserção no banco (SEM criado_por)
    $sql = "INSERT INTO exames (dia_semana, data, curso, ano, semestre, disciplina, turno, sala, hora_inicio, hora_fim, duracao) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    error_log("SQL: $sql");
    
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        error_log("Erro ao preparar statement: " . $conn->error);
        throw new Exception("Erro ao preparar consulta: " . $conn->error);
    }
    
    // Bind parameters (11 parâmetros agora, sem o criado_por)
    $stmt->bind_param(
        "ssssssssssi",  // Note: 'i' no final para duracao (inteiro)
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
        $duracao
    );
    
    if (!$stmt->execute()) {
        error_log("Erro na execução: " . $stmt->error);
        throw new Exception("Erro ao salvar no banco de dados: " . $stmt->error);
    }
    
    $linhas_afetadas = $stmt->affected_rows;
    $ultimo_id = $stmt->insert_id;
    error_log("Linhas afetadas: $linhas_afetadas, ID inserido: $ultimo_id");
    
    $stmt->close();
    
    // 4️⃣ Sucesso
    $_SESSION['sucesso'] = "Exame criado com sucesso!";
    error_log("=== EXAME SALVO COM SUCESSO ===");
    
} catch (Exception $e) {
    error_log("Exception: " . $e->getMessage());
    $_SESSION['erro'] = "Erro ao salvar exame: " . $e->getMessage();
}

header("Location: criar_exame.php");
exit;
?>