<?php
session_start();
require_once __DIR__ . '/../../config/database.php';

if (!isset($_SESSION['user'])) {
    header('Location: /smartkampus/public/index.php');
    exit;
}

$user = $_SESSION['user'];


// Verifica se já existe reserva ativa
$check = $conn->prepare("
    SELECT id, estado
    FROM reservas
    WHERE docente_id = ?
      AND estado IN ('pendente', 'aprovada')
    LIMIT 1
");
$check->bind_param("i", $user['id']);
$check->execute();
$reservaAtiva = $check->get_result()->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel do Docente - SMART KAMPUS</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Reset e base - Mesmo estilo do admin */
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

        main.container {
            flex: 1;
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

        /* Conteúdo principal */
        .container {
            max-width: 1400px;
            margin: 2rem auto;
            padding: 0 2rem;
            width: 100%;
        }

        /* Welcome card */
        .welcome-card {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            border: 1px solid #e2e8f0;
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .user-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            overflow: hidden;
            border: 3px solid #e2e8f0;
        }

        .user-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .avatar-placeholder {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, #3b43ce, #6366f1);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 2rem;
        }

        .welcome-text h1 {
            font-size: 1.75rem;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 0.5rem;
        }

        .welcome-text p {
            color: #64748b;
            font-size: 1rem;
        }

        /* Cards de ação */
        .actions-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 1.5rem;
            margin-bottom: 3rem;
        }

        .actions-grid p {
            font-size: 0.8rem;
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
            max-width: 320px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .action-button:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
            border-color: #c7d2fe;
        }

        .action-button.solicitar {
            border-left: 4px solid #3b43ce;
        }

        .action-button.minhas {
            border-left: 4px solid #10b981;
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

        .button-icon.solicitar {
            background: linear-gradient(135deg, #3b43ce, #6366f1);
        }

        .button-icon.minhas {
            background: linear-gradient(135deg, #10b981, #34d399);
        }

        /* Modais */
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
            max-width: 900px;
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

        /* Formulário */
        .form-container {
            background: #f8fafc;
            border-radius: 12px;
            padding: 1.5rem;
            margin-top: 1.5rem;
        }

        .form-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        .form-table tr {
            margin-bottom: 1rem;
        }

        .form-table td {
            padding: 0.75rem 0;
        }

        .form-table td:first-child {
            font-weight: 500;
            color: #475569;
            width: 150px;
        }

        .form-input, .form-select {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            font-size: 0.95rem;
            color: #334155;
            background: white;
            transition: all 0.2s ease;
        }

        .form-input:focus, .form-select:focus {
            outline: none;
            border-color: #3b43ce;
            box-shadow: 0 0 0 3px rgba(59, 67, 206, 0.1);
        }

        .form-input:hover, .form-select:hover {
            border-color: #cbd5e1;
        }

        /* Botões */
        .form-actions {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 500;
            font-size: 0.95rem;
            cursor: pointer;
            transition: all 0.2s ease;
            border: 1px solid transparent;
            text-decoration: none;
        }

        .btn-primary {
            background: #3b43ce;
            color: white;
            border-color: #3b43ce;
        }

        .btn-primary:hover {
            background: #2f36b5;
            border-color: #2f36b5;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(59, 67, 206, 0.2);
        }

        .btn-secondary {
            background: #f1f5f9;
            color: #334155;
            border-color: #e2e8f0;
        }

        .btn-secondary:hover {
            background: #e2e8f0;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        .btn-success {
            background: #10b981;
            color: white;
            border-color: #10b981;
        }

        .btn-success:hover {
            background: #059669;
            border-color: #059669;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);
        }

        .btn-warning {
            background: #f59e0b;
            color: white;
            border-color: #f59e0b;
        }

        .btn-warning:hover {
            background: #d97706;
            border-color: #d97706;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.2);
        }

        .btn-error {
            background: #ef4444;
            color: white;
            border-color: #ef4444;
        }

        .btn-error:hover {
            background: #dc2626;
            border-color: #dc2626;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.2);
        }

        /* Alertas */
        .alert {
            padding: 1rem 1.25rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            animation: slideDown 0.3s ease;
        }

        @keyframes slideDown {
            from { transform: translateY(-10px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .alert-warning {
            background: #fef3c7;
            border: 1px solid #fde68a;
            color: #92400e;
        }

        /* Tabela de reservas */
        .table-container {
            overflow-x: auto;
            border-radius: 10px;
            border: 1px solid #e2e8f0;
            margin-top: 1.5rem;
        }

        .data-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            min-width: 1000px;
        }

        .data-table thead {
            background: #f8fafc;
        }

        .data-table th {
            padding: 1rem;
            text-align: left;
            font-weight: 600;
            color: #475569;
            border-bottom: 1px solid #e2e8f0;
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .data-table td {
            padding: 1rem;
            border-bottom: 1px solid #f1f5f9;
            color: #334155;
        }

        .data-table tr:last-child td {
            border-bottom: none;
        }

        .data-table tr:hover {
            background-color: #f8fafc;
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

        /* Action buttons */
        .action-buttons {
            display: flex;
            gap: 0.5rem;
        }

        /* Empty state */
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

        /* Footer */
        .footer {
            background: #0f172a;
            color: #cbd5e1;
            text-align: center;
            padding: 1.5rem;
            font-size: 0.9rem;
            margin-top: auto;
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
            
            .welcome-card {
                flex-direction: column;
                text-align: center;
                padding: 1.5rem;
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
            
            .form-table td:first-child {
                width: auto;
                display: block;
                margin-bottom: 0.25rem;
            }
            
            .form-table td {
                display: block;
                padding: 0.25rem 0;
            }
        }
    </style>
</head>
<body>

<!-- Header -->
<header class="header">
    <div class="header-content">
        <div class="logo-container">
            <img src="/smartkampus/public/assets/imgs/smartkampus-logo.png" alt="Logo" class="logo-img">
            <span class="logo-text">SMART KAMPUS</span>
        </div>
        
        <div class="nav-links">
            <a href="/smartkampus/dashboard/dashboard.php" class="nav-button">
                <i class="fas fa-home"></i>
                <span>Painel Principal</span>
            </a>
            <a href="/smartkampus/public/logout.php" class="nav-button logout">
                <i class="fas fa-sign-out-alt"></i>
                <span>Sair</span>
            </a>
        </div>
    </div>
</header>

<!-- Conteúdo principal -->
<main class="container">
    <!-- Welcome card -->
    <div class="welcome-card">
        <?php if (!empty($user['picture'])): ?>
            <div class="user-avatar">
                <img src="<?= htmlspecialchars($user['picture']) ?>" alt="Foto do docente">
            </div>
        <?php else: ?>
            <div class="avatar-placeholder">
                <i class="fas fa-user"></i>
            </div>
        <?php endif; ?>
        
        <div class="welcome-text">
            <h1>Painel do Docente</h1>
            <p>Bem-vindo, <strong><?= htmlspecialchars($user['name']) ?></strong></p>
            <p>Gerencie suas reservas de salas de forma rápida e eficiente.</p>
        </div>
    </div>

    <!-- Cards de ação -->
    <div class="actions-grid">
        <button class="action-button solicitar" onclick="abrirModal('modalSolicitar')">
            <div class="button-icon solicitar">
                <i class="fas fa-calendar-plus"></i>
            </div>
            <div>
                <strong>Solicitar Reserva</strong>
                <p>Solicite uma nova reserva de sala</p>
            </div>
        </button>
        
        <button class="action-button minhas" onclick="abrirModal('modalMinhas')">
            <div class="button-icon minhas">
                <i class="fas fa-list-alt"></i>
            </div>
            <div>
                <strong>Minhas Reservas</strong>
                <p>Consulte e gerencie suas reservas</p>
            </div>
        </button>
    </div>

    <!-- ================= MODAL SOLICITAR RESERVA ================= -->
    <div id="modalSolicitar" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">
                    <i class="fas fa-calendar-plus" style="color: #3b43ce;"></i>
                    Solicitar Reserva
                </h2>
                <button class="close-modal" onclick="fecharModal('modalSolicitar')">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <?php if ($reservaAtiva): ?>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <div>
                        <strong>Reserva ativa encontrada</strong>
                        <p>Você já possui uma reserva <span class="status-badge status-<?= $reservaAtiva['estado'] ?>"><?= strtoupper($reservaAtiva['estado']) ?></span>.<br>
                        Para cancelar, acesse <strong>Minhas Reservas</strong>.</p>
                    </div>
                </div>
                
                <div class="form-actions">
                    <button onclick="abrirModal('modalMinhas')" class="btn btn-primary">
                        <i class="fas fa-list-alt"></i>
                        Ver Minhas Reservas
                    </button>
                    <button onclick="fecharModal('modalSolicitar')" class="btn btn-secondary">
                        <i class="fas fa-times"></i>
                        Fechar
                    </button>
                </div>
            <?php else: ?>
                <div class="form-container">
                    <form method="POST" action="solicitar_reserva.php">
                        <table class="form-table">
                            <tr>
                                <td>Curso</td>
                                <td>
                                    <select name="curso" class="form-select" required>
                                        <option value="">-- Selecionar Curso --</option>
                                        <option value="Administração Pública">Administração Pública</option>
                                        <option value="Contabilidade & Auditoria">Contabilidade & Auditoria</option>
                                        <option value="Direito">Direito</option>
                                        <option value="Economia e Gestão">Economia e Gestão</option>
                                        <option value="Gestão de Recursos Humanos">Gestão de Recursos Humanos</option>
                                        <option value="Meio Ambiente">Meio Ambiente</option>
                                        <option value="Tecnologia de Informação">Tecnologia de Informação</option>
                                    </select>
                                </td>
                            </tr>

                            <tr>
                                <td>Disciplina</td>
                                <td>
                                    <input type="text" name="disciplina" class="form-input" placeholder="Nome da disciplina" required>
                                </td>
                            </tr>

                            <tr>
                                <td>Turno</td>
                                <td>
                                    <select name="turno" class="form-select" required>
                                        <option value="Manhã">Manhã</option>
                                        <option value="Tarde">Tarde</option>
                                        <option value="Noite">Noite</option>
                                    </select>
                                </td>
                            </tr>

                            <tr>
                                <td>Sala</td>
                                <td>
                                    <select name="sala" class="form-select" required>
                                        <?php
                                        $salas = $conn->query("
                                            SELECT nome 
                                            FROM salas 
                                            WHERE estado = 'livre'
                                            ORDER BY nome
                                        ");
                                        while ($s = $salas->fetch_assoc()):
                                        ?>
                                            <option value="<?= htmlspecialchars($s['nome']) ?>">
                                                <?= htmlspecialchars($s['nome']) ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </td>
                            </tr>

                            <tr>
                                <td>Dia da Semana</td>
                                <td>
                                    <select name="dia_semana" class="form-select" required>
                                        <option value="Segunda-feira">Segunda-feira</option>
                                        <option value="Terça-feira">Terça-feira</option>
                                        <option value="Quarta-feira">Quarta-feira</option>
                                        <option value="Quinta-feira">Quinta-feira</option>
                                        <option value="Sexta-feira">Sexta-feira</option>
                                        <option value="Sábado">Sábado</option>
                                    </select>
                                </td>
                            </tr>

                            <tr>
                                <td>Data</td>
                                <td><input type="date" name="data" class="form-input" required></td>
                            </tr>

                            <tr>
                                <td>Hora início</td>
                                <td><input type="time" name="hora_inicio" class="form-input" required></td>
                            </tr>

                            <tr>
                                <td>Hora fim</td>
                                <td><input type="time" name="hora_fim" class="form-input" required></td>
                            </tr>

                            <tr>
                                <td>Finalidade (opcional)</td>
                                <td><input type="text" name="finalidade" class="form-input" placeholder="Ex: Aula prática, Reunião..."></td>
                            </tr>
                        </table>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-paper-plane"></i>
                                Solicitar Reserva
                            </button>
                            <button type="button" onclick="fecharModal('modalSolicitar')" class="btn btn-secondary">
                                <i class="fas fa-times"></i>
                                Cancelar
                            </button>
                        </div>
                    </form>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- ================= MODAL MINHAS RESERVAS ================= -->
    <div id="modalMinhas" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">
                    <i class="fas fa-list-alt" style="color: #10b981;"></i>
                    Minhas Reservas
                </h2>
                <button class="close-modal" onclick="fecharModal('modalMinhas')">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <?php
            $list = $conn->prepare("
                SELECT id, curso, disciplina, turno, sala, dia_semana, data, hora_inicio, hora_fim, finalidade, estado
                FROM reservas
                WHERE docente_id = ?
                ORDER BY criado_em DESC
            ");

            $list->bind_param("i", $user['id']);
            $list->execute();
            $result = $list->get_result();
            ?>

            <?php if ($result->num_rows === 0): ?>
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="far fa-calendar-times"></i>
                    </div>
                    <h3>Nenhuma reserva encontrada</h3>
                    <p>Você ainda não possui reservas cadastradas.</p>
                    <button onclick="abrirModal('modalSolicitar')" class="btn btn-primary" style="margin-top: 1rem;">
                        <i class="fas fa-calendar-plus"></i>
                        Solicitar Primeira Reserva
                    </button>
                </div>
            <?php else: ?>
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Curso</th>
                                <th>Disciplina</th>
                                <th>Sala</th>
                                <th>Turno</th>
                                <th>Dia</th>
                                <th>Data</th>
                                <th>Hora</th>
                                <th>Finalidade</th>
                                <th>Estado</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($r = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?= htmlspecialchars($r['curso']) ?></td>
                                    <td><?= htmlspecialchars($r['disciplina']) ?></td>
                                    <td><strong><?= htmlspecialchars($r['sala']) ?></strong></td>
                                    <td><?= htmlspecialchars($r['turno']) ?></td>
                                    <td><?= htmlspecialchars($r['dia_semana']) ?></td>
                                    <td><?= date('d/m/Y', strtotime($r['data'])) ?></td>
                                    <td><?= substr($r['hora_inicio'],0,5) ?> - <?= substr($r['hora_fim'],0,5) ?></td>
                                    <td><?= $r['finalidade'] ?: '-' ?></td>
                                    <td>
                                        <span class="status-badge status-<?= $r['estado'] ?>">
                                            <?= strtoupper($r['estado']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <?php if ($r['estado'] === 'aprovada' || $r['estado'] === 'pendente'): ?>
                                                <form method="POST" action="cancelar_reserva.php" style="display:inline;">
                                                    <input type="hidden" name="id" value="<?= $r['id'] ?>">
                                                    <button type="submit" class="btn btn-warning btn-small" 
                                                            onclick="return confirm('Tem certeza que deseja cancelar esta reserva?')">
                                                        <i class="fas fa-ban"></i>
                                                        Cancelar
                                                    </button>
                                                </form>
                                            <?php elseif ($r['estado'] === 'rejeitada'): ?>
                                                <form method="POST" action="excluir_reserva.php" style="display:inline;">
                                                    <input type="hidden" name="id" value="<?= $r['id'] ?>">
                                                    <button type="submit" class="btn btn-error btn-small"
                                                            onclick="return confirm('Deseja excluir esta reserva rejeitada?')">
                                                        <i class="fas fa-trash"></i>
                                                        Excluir
                                                    </button>
                                                </form>
                                            <?php else: ?>
                                                -
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>

                <div class="form-actions">
                    <button onclick="fecharModal('modalMinhas')" class="btn btn-secondary">
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

<!-- JS -->
<script>
function abrirModal(id) {
    document.getElementById(id).style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function fecharModal(id) {
    document.getElementById(id).style.display = 'none';
    document.body.style.overflow = 'auto';
}

// Fechar modal ao clicar fora
window.onclick = function(event) {
    const modals = document.querySelectorAll('.modal');
    modals.forEach(modal => {
        if (event.target == modal) {
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
        }
    });
}
</script>

</body>
</html>
