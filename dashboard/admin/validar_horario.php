<?php
// validar_horario.php

function validarHorario(
    $conn,
    $dia_semana,
    $curso,
    $ano,
    $semestre,
    $turno,
    $sala,
    $hora_inicio,
    $hora_fim,
    $id_ignore = null
) {
    $inicio = new DateTime($hora_inicio);
    $fim    = new DateTime($hora_fim);

    /* 1️⃣ Hora início < hora fim */
    if ($inicio >= $fim) {
        return "O horário de início deve ser menor que o horário de fim.";
    }

    /* 2️⃣ Validação por turno */
    $inicioMin = ((int)$inicio->format('H')) * 60 + (int)$inicio->format('i');
    $fimMin    = ((int)$fim->format('H')) * 60 + (int)$fim->format('i');

    if ($turno === 'Diurno') {
        if ($inicioMin < 420 || $fimMin > 720) { // 07:00 – 12:00
            return "Horários diurnos devem estar entre 07:00 e 12:00.";
        }
    }

    if ($turno === 'Noturno') {
        if ($inicioMin < 720 || $fimMin > 1260) { // 12:00 – 21:00
            return "Horários noturnos devem estar entre 12:00 e 21:00.";
        }
    }

    /* 3️⃣ Conflito de SALA */
    if ($id_ignore) {
        $stmt = $conn->prepare("
            SELECT hora_inicio, hora_fim FROM horarios
            WHERE sala = ? AND dia_semana = ? AND id != ?
        ");
        $stmt->bind_param("ssi", $sala, $dia_semana, $id_ignore);
    } else {
        $stmt = $conn->prepare("
            SELECT hora_inicio, hora_fim FROM horarios
            WHERE sala = ? AND dia_semana = ?
        ");
        $stmt->bind_param("ss", $sala, $dia_semana);
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

    /* 4️⃣ Conflito de CURSO + ANO + SEMESTRE 🔥 */
    if ($id_ignore) {
        $stmt = $conn->prepare("
            SELECT hora_inicio, hora_fim FROM horarios
            WHERE dia_semana = ?
              AND curso = ?
              AND ano = ?
              AND semestre = ?
              AND id != ?
        ");
        $stmt->bind_param("ssisi", $dia_semana, $curso, $ano, $semestre, $id_ignore);
    } else {
        $stmt = $conn->prepare("
            SELECT hora_inicio, hora_fim FROM horarios
            WHERE dia_semana = ?
              AND curso = ?
              AND ano = ?
              AND semestre = ?
        ");
        $stmt->bind_param("ssis", $dia_semana, $curso, $ano, $semestre);
    }

    $stmt->execute();
    $res = $stmt->get_result();

    while ($row = $res->fetch_assoc()) {
        $eIni = new DateTime($row['hora_inicio']);
        $eFim = new DateTime($row['hora_fim']);
        $eFim->modify('+1 minute');

        if ($inicio < $eFim && $fim > $eIni) {
            return "Conflito académico: o curso {$curso} ({$ano} {$semestre}) já tem aula nesse horário.";
        }
    }

    return null; // ✅ Tudo OK
}
