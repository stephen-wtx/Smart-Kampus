<?php
// validar_horario.php

function validarHorario(
    $conn,
    $tipo,
    $dia_semana,
    $data,
    $curso,
    $ano,
    $semestre,
    $turno,
    $sala,
    $hora_inicio,
    $hora_fim,
    $id_ignore = null
) {
    // Determinar tabela
    if ($tipo === 'aula') $tabela = 'horarios';
    elseif ($tipo === 'teste') $tabela = 'testes';
    elseif ($tipo === 'exame') $tabela = 'exames';
    else return "Tipo inválido: $tipo";

    // Validar horário
    $inicio = new DateTime($hora_inicio);
    $fim    = new DateTime($hora_fim);
    if ($inicio >= $fim) return "O horário de início deve ser menor que o horário de fim.";

    // Validar turno
    $inicioMin = ((int)$inicio->format('H'))*60 + (int)$inicio->format('i');
    $fimMin    = ((int)$fim->format('H'))*60 + (int)$fim->format('i');
    if ($turno === 'Diurno' && ($inicioMin < 420 || $fimMin > 720)) {
        return "Horários diurnos devem estar entre 07:00 e 12:00.";
    }
    if ($turno === 'Noturno' && ($inicioMin < 720 || $fimMin > 1260)) {
        return "Horários noturnos devem estar entre 12:00 e 21:00.";
    }

    // Campo de dia/data
    $campoDia = ($tipo === 'aula') ? 'dia_semana' : 'data';
    $valorDia = ($tipo === 'aula') ? $dia_semana : $data;

    // 1️⃣ Conflito de SALA
    if ($id_ignore) {
        $stmt = $conn->prepare(
            "SELECT hora_inicio, hora_fim FROM $tabela 
             WHERE sala = ? AND $campoDia = ? AND id != ?"
        );
        if (!$stmt) return "Erro SQL sala: ".$conn->error;
        $stmt->bind_param("ssi", $sala, $valorDia, $id_ignore);
    } else {
        $stmt = $conn->prepare(
            "SELECT hora_inicio, hora_fim FROM $tabela 
             WHERE sala = ? AND $campoDia = ?"
        );
        if (!$stmt) return "Erro SQL sala: ".$conn->error;
        $stmt->bind_param("ss", $sala, $valorDia);
    }

    $stmt->execute();
    $res = $stmt->get_result();
    while ($row = $res->fetch_assoc()) {
        $eIni = new DateTime($row['hora_inicio']);
        $eFim = new DateTime($row['hora_fim']);
        $eFim->modify('+1 minute');
        if ($inicio < $eFim && $fim > $eIni) {
            return "Conflito de sala: esta sala já está ocupada nesse horário.";
        }
    }

    // 2️⃣ Conflito acadêmico
    if ($id_ignore) {
        $stmt = $conn->prepare(
            "SELECT hora_inicio, hora_fim FROM $tabela 
             WHERE $campoDia = ? AND curso = ? AND ano = ? AND semestre = ? AND id != ?"
        );
        if (!$stmt) return "Erro SQL acadêmico: ".$conn->error;
        $stmt->bind_param("ssssi", $valorDia, $curso, $ano, $semestre, $id_ignore);
    } else {
        $stmt = $conn->prepare(
            "SELECT hora_inicio, hora_fim FROM $tabela 
             WHERE $campoDia = ? AND curso = ? AND ano = ? AND semestre = ?"
        );
        if (!$stmt) return "Erro SQL acadêmico: ".$conn->error;
        $stmt->bind_param("ssss", $valorDia, $curso, $ano, $semestre);
    }

    $stmt->execute();
    $res = $stmt->get_result();
    while ($row = $res->fetch_assoc()) {
        $eIni = new DateTime($row['hora_inicio']);
        $eFim = new DateTime($row['hora_fim']);
        $eFim->modify('+1 minute');
        if ($inicio < $eFim && $fim > $eIni) {
            return "Conflito académico: o curso {$curso} ({$ano} {$semestre}) já tem outro evento nesse horário.";
        }
    }

    return null; // Tudo ok
}
?>
