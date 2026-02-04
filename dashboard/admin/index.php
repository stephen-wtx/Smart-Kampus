<?php
session_start();
require_once '../../config/database.php';

if (!isset($_SESSION['user'])) {
    header('Location: /smartkampus/public/index.php');
    exit;
}

$user = $_SESSION['user'];

// Seleciona todas as reservas
$reservas = $conn->query("
    SELECT 
        id,
        docente_nome,
        curso,
        disciplina,
        turno,
        sala,
        dia_semana,
        data,
        hora_inicio,
        hora_fim,
        finalidade,
        estado
    FROM reservas
    ORDER BY data DESC, hora_inicio DESC
") or die("Erro na query: " . $conn->error);
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Admin - SMART KAMPUS</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Reset e base */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
        }

        body {
            background-color: #f8fafc;
            color: #334155;
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Header */
        .header {
            background: white;
            padding: 0 2rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1400px;
            margin: 0 auto;
            height: 70px;
        }

        .logo-container {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .logo-img {
            height: 100px;
            width: auto;
        }

        .logo-text {
            font-size: 1.5rem;
            font-weight: 600;
            color: #3b43ce;
            letter-spacing: -0.5px;
        }

        .nav-links {
            display: flex;
            gap: 1.5rem;
            align-items: center;
        }

        .nav-button {
            display: flex;
            align-items: center;
            gap: 8px;
            background: transparent;
            border: none;
            color: #64748b;
            font-size: 0.95rem;
            font-weight: 500;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
        }

        .nav-button:hover {
            background-color: #f1f5f9;
            color: #3b43ce;
        }

        .nav-button.logout {
            color: #ef4444;
        }

        .nav-button.logout:hover {
            background-color: #fee2e2;
        }

        /* Main content */
        .container {
            flex: 1;
            max-width: 1400px;
            margin: 2rem auto;
            padding: 0 2rem;
            width: 100%;
        }

        .page-title {
            font-size: 2rem;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 2rem;
            letter-spacing: -0.5px;
        }

        /* Botões de ação principais */
        .actions-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 1.5rem;
            margin-bottom: 3rem;
        }

        .action-button {
            display: flex;
            align-items: center;
            gap: 12px;
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 1.25rem 1.75rem;
            font-size: 1rem;
            font-weight: 500;
            color: #334155;
            cursor: pointer;
            transition: all 0.3s ease;
            flex: 1;
            min-width: 200px;
            max-width: 300px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .action-button:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
            border-color: #c7d2fe;
        }

        .action-button.gerir {
            border-left: 4px solid #3b43ce;
        }

        .action-button.reservas {
            border-left: 4px solid #10b981;
        }

        .action-button.calendario {
            border-left: 4px solid #8b5cf6;
        }

        .button-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            color: white;
        }

        .button-icon.gerir {
            background: linear-gradient(135deg, #3b43ce, #6366f1);
        }

        .button-icon.reservas {
            background: linear-gradient(135deg, #10b981, #34d399);
        }

        .button-icon.calendario {
            background: linear-gradient(135deg, #8b5cf6, #a78bfa);
        }

        /* Modais - mantendo a funcionalidade original */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(15, 23, 42, 0.7);
            backdrop-filter: blur(4px);
            z-index: 1000;
            align-items: center;
            justify-content: center;
            animation: fadeIn 0.3s ease;
            overflow-y: auto;
            padding: 1rem;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes slideUp {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .modal-content {
            background: white;
            border-radius: 16px;
            padding: 2rem;
            width: 100%;
            max-width: 1200px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            animation: slideUp 0.4s ease;
            max-height: 85vh;
            overflow-y: auto;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #f1f5f9;
        }

        .modal-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #0f172a;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .close-modal {
            background: none;
            border: none;
            font-size: 1.5rem;
            color: #94a3b8;
            cursor: pointer;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
        }

        .close-modal:hover {
            background: #f1f5f9;
            color: #64748b;
        }

        /* Botões dentro de modais */
        .modal-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            margin-top: 2rem;
        }

        .modal-btn {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 0.875rem 1.5rem;
            border-radius: 10px;
            border: 1px solid #e2e8f0;
            background: white;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
            font-size: 0.95rem;
            color: #334155;
        }

        .modal-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            border-color: #c7d2fe;
        }

        .modal-btn.primary {
            background: #3b43ce;
            color: white;
            border-color: #3b43ce;
        }

        .modal-btn.primary:hover {
            background: #2f36b5;
            border-color: #2f36b5;
        }

        .modal-btn.secondary {
            background: #f1f5f9;
            color: #334155;
        }

        .modal-btn.secondary:hover {
            background: #e2e8f0;
        }

        .modal-btn.cancel {
            background: #fee2e2;
            color: #dc2626;
            border-color: #fee2e2;
        }

        .modal-btn.cancel:hover {
            background: #fecaca;
            border-color: #fecaca;
        }

        /* Tabela de reservas */
        .table-container {
            overflow-x: auto;
            border-radius: 10px;
            border: 1px solid #e2e8f0;
            margin-top: 1.5rem;
        }

        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            min-width: 1000px;
        }

        thead {
            background: #f8fafc;
        }

        th {
            padding: 1rem;
            text-align: left;
            font-weight: 600;
            color: #475569;
            border-bottom: 1px solid #e2e8f0;
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        td {
            padding: 1rem;
            border-bottom: 1px solid #f1f5f9;
            color: #334155;
        }

        tr:last-child td {
            border-bottom: none;
        }

        tr:hover {
            background-color: #f8fafc;
        }

        /* Botões de ação na tabela - mantendo os forms originais */
        .action-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .btn {
            padding: 0.5rem 0.875rem;
            border-radius: 6px;
            border: none;
            font-weight: 500;
            font-size: 0.85rem;
            cursor: pointer;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .btn-approve {
            background: #d1fae5;
            color: #065f46;
        }

        .btn-approve:hover {
            background: #a7f3d0;
        }

        .btn-reject {
            background: #fee2e2;
            color: #991b1b;
        }

        .btn-reject:hover {
            background: #fecaca;
        }

        .btn-cancel {
            background: #fef3c7;
            color: #92400e;
        }

        .btn-cancel:hover {
            background: #fde68a;
        }

        .btn-delete {
            background: #f1f5f9;
            color: #475569;
        }

        .btn-delete:hover {
            background: #e2e8f0;
        }

        /* Status badges */
        .status-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .status-pendente {
            background: #fef3c7;
            color: #92400e;
        }

        .status-aprovada {
            background: #d1fae5;
            color: #065f46;
        }

        .status-rejeitada {
            background: #fee2e2;
            color: #991b1b;
        }

        /* Upload de calendário */
        .upload-container {
            border: 2px dashed #c7d2fe;
            border-radius: 12px;
            padding: 2rem;
            text-align: center;
            background: #f8fafc;
            margin-top: 1.5rem;
        }

        .upload-icon {
            font-size: 2.5rem;
            color: #3b43ce;
            margin-bottom: 1rem;
        }

        .upload-button {
            background: #3b43ce;
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 500;
            cursor: pointer;
            margin-top: 1rem;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .upload-button:hover {
            background: #2f36b5;
            transform: translateY(-2px);
        }

        /* Footer */
        .footer {
            background: #0f172a;
            color: #cbd5e1;
            text-align: center;
            padding: 1.5rem;
            margin-top: auto;
            font-size: 0.9rem;
        }

        /* Responsividade */
        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                height: auto;
                padding: 1rem 0;
            }
            
            .nav-links {
                margin-top: 1rem;
                flex-wrap: wrap;
                justify-content: center;
            }
            
            .container {
                padding: 0 1rem;
            }
            
            .actions-grid {
                flex-direction: column;
            }
            
            .action-button {
                max-width: 100%;
            }
            
            .modal-content {
                padding: 1.5rem;
                margin: 1rem;
            }
        }

        /* Estados vazios */
        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
            color: #64748b;
        }

        .empty-icon {
            font-size: 3rem;
            color: #cbd5e1;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>

<!-- Header -->
<header class="header">
    <div class="header-content">
        <div class="logo-container">
            <!-- Substitua pelo caminho da sua logo -->
            <img src="../../public/assets/imgs/smartkampus-logo.png" alt="Logo" class="logo-img">
            <span class="logo-text">SMART KAMPUS</span>
        </div>
        
        <div class="nav-links">
            <a href="../../public/index.php" class="nav-button">
                <i class="fas fa-home"></i>
                <span>Painel Principal</span>
            </a>
            <a href="../../public/logout.php" class="nav-button logout">
                <i class="fas fa-sign-out-alt"></i>
                <span>Sair</span>
            </a>
        </div>
    </div>
</header>

<!-- Conteúdo principal - PHP MANTIDO INTACTO -->
<main class="container">
    <h2 class="page-title">Painel do Administrador</h2>
    
    <div class="actions-grid">
        <button class="action-button gerir" onclick="abrirModalGerir()">
            <div class="button-icon gerir">
                <i class="fas fa-calendar-alt"></i>
            </div>
            <span>Gerir horários</span>
        </button>
        
        <button class="action-button reservas" onclick="abrirModalReservas()">
            <div class="button-icon reservas">
                <i class="fas fa-clock"></i>
            </div>
            <span>Gerir reservas</span>
        </button>
        
        <button class="action-button calendario" onclick="abrirModalCalendario()">
            <div class="button-icon calendario">
                <i class="fas fa-calendar-day"></i>
            </div>
            <span>Calendário Académico</span>
        </button>
    </div>

    <!-- MODAL 1: GERIR - PHP MANTIDO -->
    <div id="modalGerir" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">
                    <i class="fas fa-calendar-alt" style="color: #3b43ce;"></i>
                    Gerir Horários
                </h2>
                <button class="close-modal" onclick="fecharTodos()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div class="modal-buttons">
                <button class="modal-btn primary" onclick="abrirModalTipo('criar')">
                    <i class="fas fa-plus-circle"></i>
                    Criar horários
                </button>
                <button class="modal-btn secondary" onclick="abrirModalTipo('ver')">
                    <i class="fas fa-eye"></i>
                    Ver horários
                </button>
            </div>
        </div>
    </div>

    <!-- MODAL 2: TIPO - PHP MANTIDO -->
    <div id="modalTipo" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">
                    <i class="fas fa-list-alt" style="color: #3b43ce;"></i>
                    Tipo de horário
                </h2>
                <button class="close-modal" onclick="fecharTodos()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <h3>Tipo de horário</h3>
            
            <div class="modal-buttons">
                <button class="modal-btn primary" onclick="redirecionar('aula')">
                    <i class="fas fa-chalkboard-teacher"></i>
                    Aula
                </button>
                <button class="modal-btn primary" style="background: #10b981;" onclick="redirecionar('teste')">
                    <i class="fas fa-edit"></i>
                    Teste
                </button>
                <button class="modal-btn primary" style="background: #8b5cf6;" onclick="redirecionar('exame')">
                    <i class="fas fa-file-alt"></i>
                    Exame
                </button>
                <button class="modal-btn cancel" onclick="fecharTodos()">
                    <i class="fas fa-times"></i>
                    Fechar
                </button>
            </div>
        </div>
    </div>

    <!-- MODAL 3: GERIR RESERVAS - PHP MANTIDO INTACTO -->
    <div id="modalReservas" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">
                    <i class="fas fa-clock" style="color: #10b981;"></i>
                    Gerir Reservas
                </h2>
                <button class="close-modal" onclick="fecharTodos()">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <h3>Gerir Reservas</h3>

            <?php if ($reservas && $reservas->num_rows > 0): ?>
                <div class="table-container">
                    <table>
                        <tr>
                            <th>Docente</th>
                            <th>Curso</th>
                            <th>Sala</th>
                            <th>Disciplina</th>
                            <th>Turno</th>
                            <th>Dia</th>
                            <th>Data</th>
                            <th>Hora</th>
                            <th>Finalidade</th>
                            <th>Estado</th>
                            <th>Ação</th>
                        </tr>

                    <?php while ($r = $reservas->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($r['docente_nome']) ?></td>
                        <td><?= htmlspecialchars($r['curso']) ?></td>
                        <td><strong><?= htmlspecialchars($r['sala']) ?></strong></td>
                        <td><?= htmlspecialchars($r['disciplina']) ?></td>
                        <td><?= htmlspecialchars($r['turno']) ?></td>
                        <td><?= htmlspecialchars($r['dia_semana']) ?></td>
                        <td><?= date('d/m/Y', strtotime($r['data'])) ?></td>
                        <td><?= substr($r['hora_inicio'],0,5) ?> - <?= substr($r['hora_fim'],0,5) ?></td>
                        <td><?= htmlspecialchars($r['finalidade'] ?: '-') ?></td>
                        
                        <td>
                            <span class="status-badge status-<?= $r['estado'] ?>">
                                <?= ucfirst($r['estado']) ?>
                            </span>
                        </td>

                        <td class="action-buttons">
                            <?php if ($r['estado'] === 'pendente'): ?>
                                <form method="POST" action="reserva/gerir_reserva.php" style="display:inline;">
                                    <input type="hidden" name="id" value="<?= $r['id'] ?>">
                                    <input type="hidden" name="acao" value="aprovar">
                                    <button type="submit" class="btn btn-approve">
                                        <i class="fas fa-check"></i> Aprovar
                                    </button>
                                </form>

                                <form method="POST" action="reserva/gerir_reserva.php" style="display:inline;">
                                    <input type="hidden" name="id" value="<?= $r['id'] ?>">
                                    <input type="hidden" name="acao" value="rejeitar">
                                    <button type="submit" class="btn btn-reject">
                                        <i class="fas fa-times"></i> Rejeitar
                                    </button>
                                </form>

                            <?php elseif ($r['estado'] === 'aprovada'): ?>
                                <form method="POST" action="reserva/cancelar_reserva.php" style="display:inline;">
                                    <input type="hidden" name="id" value="<?= $r['id'] ?>">
                                    <button type="submit" class="btn btn-cancel" onclick="return confirm('Cancelar esta reserva?')">
                                        <i class="fas fa-ban"></i> Cancelar Reserva
                                    </button>
                                </form>

                            <?php elseif ($r['estado'] === 'rejeitada'): ?>
                                <form method="POST" action="reserva/excluir_reserva.php" style="display:inline;">
                                    <input type="hidden" name="id" value="<?= $r['id'] ?>">
                                    <button type="submit" class="btn btn-delete" onclick="return confirm('Deseja excluir esta reserva rejeitada?')">
                                        <i class="fas fa-trash"></i> Excluir
                                    </button>
                                </form>

                            <?php else: ?>
                                <?= strtoupper($r['estado']) ?>
                            <?php endif; ?>
                        </td>

                    </tr>
                    <?php endwhile; ?>
                    </table>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="far fa-calendar-times"></i>
                    </div>
                    <p>Sem reservas registadas.</p>
                </div>
            <?php endif; ?>

        </div>
    </div>

    <!-- MODAL 4: CALENDÁRIO ACADÉMICO - PHP MANTIDO INTACTO -->
    <div id="modalCalendario" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">
                    <i class="fas fa-calendar-day" style="color: #8b5cf6;"></i>
                    Calendário Académico
                </h2>
                <button class="close-modal" onclick="fecharTodos()">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <h3>Calendário Académico</h3>

            <?php
            $cal = $conn->query("SELECT * FROM calendario_academico ORDER BY data_publicacao DESC LIMIT 1");
            ?>

            <?php if ($cal && $cal->num_rows > 0): 
                $c = $cal->fetch_assoc();
            ?>

                <p><strong>Publicado em:</strong> <?= date('d/m/Y H:i', strtotime($c['data_publicacao'])) ?></p>

                <div class="upload-container">
                    <div class="upload-icon">
                        <i class="far fa-calendar-check"></i>
                    </div>
                    <p>Calendário académico disponível para download</p>
                    
                    <a href="/smartkampus/public/uploads/calendario/<?= $c['caminho'] ?>" target="_blank">
                        <button class="upload-button">
                            <i class="fas fa-external-link-alt"></i>
                            Ver Calendário
                        </button>
                    </a>
                </div>

                <div class="modal-buttons">
                    <form method="POST" action="calendario/exluir_calendario.php" 
                          onsubmit="return confirm('Deseja excluir o calendário académico?')">
                        <input type="hidden" name="id" value="<?= $c['id'] ?>">
                        <button type="submit" class="modal-btn cancel">
                            <i class="fas fa-trash"></i>
                            Excluir Calendário
                        </button>
                    </form>
                    
                    <button class="modal-btn secondary" onclick="fecharTodos()">
                        <i class="fas fa-times"></i>
                        Fechar
                    </button>
                </div>

            <?php else: ?>

                <div class="upload-container">
                    <div class="upload-icon">
                        <i class="fas fa-cloud-upload-alt"></i>
                    </div>
                    <p>Faça upload do calendário académico</p>
                    
                    <form method="POST" action="calendario/upload_calendario.php" enctype="multipart/form-data">
                        <input type="file" name="calendario_pdf" accept="application/pdf" required 
                               style="padding: 1rem; border: 1px solid #c7d2fe; border-radius: 8px; width: 100%; max-width: 400px;">
                        <br><br>
                        <button type="submit" class="upload-button">
                            <i class="fas fa-upload"></i>
                            📤 Publicar Calendário
                        </button>
                    </form>
                </div>

                <div class="modal-buttons">
                    <button class="modal-btn secondary" onclick="fecharTodos()">
                        <i class="fas fa-times"></i>
                        Fechar
                    </button>
                </div>

            <?php endif; ?>
        </div>
    </div>
</main>

<!-- Footer -->
<footer class="footer">
    <p>© 2026 SMART KAMPUS • Universidade Católica de Moçambique</p>
</footer>

<script>
let acao = null;

function abrirModalGerir() {
    document.getElementById('modalGerir').style.display = 'flex';
}

function abrirModalReservas() {
    document.getElementById('modalReservas').style.display = 'flex';
}

function abrirModalTipo(tipoAcao) {
    acao = tipoAcao;
    document.getElementById('modalGerir').style.display = 'none';
    document.getElementById('modalTipo').style.display = 'flex';
}

function abrirModalCalendario() {
    document.getElementById('modalCalendario').style.display = 'flex';
}

function fecharTodos() {
    document.getElementById('modalGerir').style.display = 'none';
    document.getElementById('modalTipo').style.display = 'none';
    document.getElementById('modalReservas').style.display = 'none';
    document.getElementById('modalCalendario').style.display = 'none';
}

function redirecionar(tipo) {
    if (acao === 'criar') {
        if (tipo === 'aula')   window.location = 'horarios/aulas/criar_horario.php';
        if (tipo === 'teste')  window.location = 'horarios/testes/criar_teste.php';
        if (tipo === 'exame')  window.location = 'horarios/exames/criar_exame.php';
    }

    if (acao === 'ver') {
        if (tipo === 'aula')   window.location = 'horarios/aulas/listar_horarios.php';
        if (tipo === 'teste')  window.location = 'horarios/testes/listar_testes.php';
        if (tipo === 'exame')  window.location = 'horarios/exames/listar_exames.php';
    }
}

// Fechar modal ao clicar fora
window.onclick = function(event) {
    const modals = document.querySelectorAll('.modal');
    modals.forEach(modal => {
        if (event.target == modal) {
            modal.style.display = 'none';
        }
    });
}
</script>

</body>
</html>
