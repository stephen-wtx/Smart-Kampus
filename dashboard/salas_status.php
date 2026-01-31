<?php
require_once __DIR__ . '/../config/database.php';

// Fuso horário de Moçambique
date_default_timezone_set('Africa/Maputo');

$hoje = date('Y-m-d');
$horaAtual = date('H:i:s');

// Dia da semana em português
$dias = [
    'Sunday'    => 'Domingo',
    'Monday'    => 'Segunda-feira',
    'Tuesday'   => 'Terça-feira',
    'Wednesday' => 'Quarta-feira',
    'Thursday'  => 'Quinta-feira',
    'Friday'    => 'Sexta-feira',
    'Saturday'  => 'Sábado',
];
$diaSemana = $dias[date('l')];

// Receber tipo de consulta (livres ou ocupadas)
$tipo = $_GET['tipo'] ?? 'livres';

// ==========================
// 1. Buscar todas as salas da tabela salas
// ==========================
$salas = [];
$resSalas = $conn->query("SELECT nome, estado FROM salas");

while ($s = $resSalas->fetch_assoc()) {
    $salas[$s['nome']] = $s['estado']; // pode ser 'livre' ou outro
}

// ==========================
// 2. Marcar salas ocupadas com base em horários, testes e exames
// ==========================
$sqlOcupadas = "
    SELECT DISTINCT sala FROM (
        SELECT sala
        FROM horarios
        WHERE dia_semana = ?
          AND hora_inicio <= ?
          AND hora_fim > ?
        UNION ALL
        SELECT sala
        FROM testes
        WHERE data = ?
          AND hora_inicio <= ?
          AND hora_fim > ?
        UNION ALL
        SELECT sala
        FROM exames
        WHERE data = ?
          AND hora_inicio <= ?
          AND hora_fim > ?
    ) t
";

$stmt = $conn->prepare($sqlOcupadas);
$stmt->bind_param(
    "sssssssss",
    $diaSemana, $horaAtual, $horaAtual,
    $hoje, $horaAtual, $horaAtual,
    $hoje, $horaAtual, $horaAtual
);
$stmt->execute();
$result = $stmt->get_result();

while ($r = $result->fetch_assoc()) {
    $salas[$r['sala']] = 'ocupada';
}

// ==========================
// 3. Separar listas
// ==========================
$livres = [];
$ocupadas = [];

foreach ($salas as $nome => $estado) {
    if ($estado === 'ocupada') {
        $ocupadas[] = $nome;
    } else {
        $livres[] = $nome;
    }
}

// ==========================
// 4. Output apenas conforme o tipo solicitado
// ==========================
if ($tipo === 'livres') {
    echo "<p><strong>Salas Livres:</strong> " . count($livres) . "</p>";
    if (count($livres) > 0) {
        echo "<ul>";
        foreach ($livres as $sala) {
            echo "<li style='color:green'>🟢 " . htmlspecialchars($sala) . "</li>";
        }
        echo "</ul>";
    } else {
        echo "<p>Não existem salas livres neste momento.</p>";
    }
} else { // ocupadas
    echo "<p><strong>Salas Ocupadas:</strong> " . count($ocupadas) . "</p>";
    if (count($ocupadas) > 0) {
        echo "<ul>";
        foreach ($ocupadas as $sala) {
            echo "<li style='color:red'>🔴 " . htmlspecialchars($sala) . "</li>";
        }
        echo "</ul>";
    } else {
        echo "<p>Não existem salas ocupadas neste momento.</p>";
    }
}
