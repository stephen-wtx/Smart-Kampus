<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
require_once __DIR__ . "/../../../../config/database.php";

if (!isset($_SESSION['user'])) {
    header('Location: /smartkampus/public/index.php');
    exit;
}
$user = $_SESSION['user'];

// Buscar horários
$sql = "SELECT * FROM horarios ORDER BY dia_semana, hora_inicio";
$resultado = $conn->query($sql);
$total = $resultado->num_rows;
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listar Horários - SMART KAMPUS</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Reset e base - Mesmo estilo do dashboard */
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
            height: 40px;
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

        /* Cabeçalho da página */
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .page-title {
            font-size: 1.75rem;
            font-weight: 700;
            color: #0f172a;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .page-title i {
            color: #3b43ce;
        }

        /* Contador */
        .counter-badge {
            background: #3b43ce;
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.875rem;
            font-weight: 500;
        }

        /* Breadcrumb */
        .breadcrumb {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
            color: #64748b;
        }

        .breadcrumb a {
            color: #3b43ce;
            text-decoration: none;
            transition: color 0.2s ease;
        }

        .breadcrumb a:hover {
            color: #2f36b5;
            text-decoration: underline;
        }

        .breadcrumb .separator {
            color: #cbd5e1;
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

        .alert-success {
            background: #d1fae5;
            border: 1px solid #a7f3d0;
            color: #065f46;
        }

        .alert-error {
            background: #fee2e2;
            border: 1px solid #fecaca;
            color: #991b1b;
        }

        .alert-info {
            background: #e0f2fe;
            border: 1px solid #bae6fd;
            color: #075985;
        }

        .alert i {
            font-size: 1.25rem;
        }

        /* Tabela */
        .table-container {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            border: 1px solid #e2e8f0;
            margin-bottom: 2rem;
            overflow-x: auto;
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

        /* Estado vazio */
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

        /* Badges para dados */
        .data-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            background: #f1f5f9;
            color: #475569;
        }

        .badge-curso {
            background: #e0f2fe;
            color: #0369a1;
        }

        .badge-disciplina {
            background: #f0f9ff;
            color: #0c4a6e;
        }

        .badge-turno {
            background: #fef3c7;
            color: #92400e;
        }

        .badge-sala {
            background: #dcfce7;
            color: #166534;
        }

        .badge-dia {
            background: #f3e8ff;
            color: #7c3aed;
        }

        /* Botões de ação */
        .action-buttons {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            font-weight: 500;
            font-size: 0.85rem;
            cursor: pointer;
            transition: all 0.2s ease;
            border: none;
            text-decoration: none;
        }

        .btn-editar {
            background: #10b981;
            color: white;
        }

        .btn-editar:hover {
            background: #059669;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);
        }

        .btn-excluir {
            background: #ef4444;
            color: white;
        }

        .btn-excluir:hover {
            background: #dc2626;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.2);
        }

        .btn-primary {
            background: #3b43ce;
            color: white;
        }

        .btn-primary:hover {
            background: #2f36b5;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(59, 67, 206, 0.2);
        }

        .btn-secondary {
            background: #f1f5f9;
            color: #334155;
            border: 1px solid #e2e8f0;
        }

        .btn-secondary:hover {
            background: #e2e8f0;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        /* Ações da página */
        .page-actions {
            display: flex;
            gap: 1rem;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
        }

        /* Footer */
        .footer {
            background: #0f172a;
            color: #cbd5e1;
            text-align: center;
            padding: 1.5rem;
            margin-top: 3rem;
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
            
            .page-header {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .table-container {
                padding: 1rem;
            }
            
            .page-actions {
                flex-direction: column;
            }
            
            .btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>

<!-- Header -->
<header class="header">
    <div class="header-content">
        <div class="logo-container">
            <img src="../../../../public/assets/imgs/smartkampus-logo.png" alt="Logo" class="logo-img">
            <span class="logo-text">SMART KAMPUS</span>
        </div>
        
        <div class="nav-links">
            <a href="/smartkampus/dashboard/dashboard.php" class="nav-button">
                <i class="fas fa-dashboard"></i>
                <span>Dashboard Principal</span>
            </a>
            <a href="/smartkampus/public/logout.php" class="nav-button logout">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </a>
        </div>
    </div>
</header>

<!-- Conteúdo principal -->
<main class="container">
    <!-- Breadcrumb -->
    <div class="breadcrumb">
        <a href="../../index.php">Início</a>
        <span class="separator">/</span>
        <span>Listar Horários</span>
    </div>

    <!-- Cabeçalho da página -->
    <div class="page-header">
        <h1 class="page-title">
            <i class="fas fa-calendar-alt"></i>
            Horários Criados
        </h1>
        <div class="counter-badge">
            <i class="fas fa-clock"></i>
            <?= $total ?> horário(s)
        </div>
    </div>

    <!-- Ações da página -->
    <div class="page-actions">
        <a href="criar_horario.php" class="btn btn-primary">
            <i class="fas fa-plus-circle"></i>
            Criar Novo Horário
        </a>
        <a href="../../index.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i>
            Voltar ao Dashboard
        </a>
    </div>

    <!-- Mensagens de feedback -->
    <?php if (!empty($_SESSION['sucesso'])): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            <?= htmlspecialchars($_SESSION['sucesso']); ?>
        </div>
        <?php unset($_SESSION['sucesso']); ?>
    <?php endif; ?>

    <?php if (!empty($_SESSION['erro'])): ?>
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i>
            <?= htmlspecialchars($_SESSION['erro']); ?>
        </div>
        <?php unset($_SESSION['erro']); ?>
    <?php endif; ?>

    <?php if ($total === 0): ?>
        <div class="empty-state">
            <div class="empty-icon">
                <i class="far fa-calendar-times"></i>
            </div>
            <h3>Nenhum horário cadastrado</h3>
            <p>Não há horários disponíveis no momento.</p>
            <a href="criar_horario.php" class="btn btn-primary" style="margin-top: 1rem;">
                <i class="fas fa-plus-circle"></i>
                Criar Primeiro Horário
            </a>
        </div>
    <?php else: ?>
        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Curso</th>
                        <th>Disciplina</th>
                        <th>Ano</th>
                        <th>Semestre</th>
                        <th>Turno</th>
                        <th>Sala</th>
                        <th>Dia</th>
                        <th>Hora</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($linha = $resultado->fetch_assoc()): ?>
                    <tr>
                        <td>
                            <span class="data-badge badge-curso">
                                <?= htmlspecialchars($linha['curso']); ?>
                            </span>
                        </td>
                        <td>
                            <span class="data-badge badge-disciplina">
                                <i class="fas fa-book"></i>
                                <?= htmlspecialchars($linha['disciplina']); ?>
                            </span>
                        </td>
                        <td>
                            <span class="data-badge">
                                <?= htmlspecialchars($linha['ano']); ?>
                            </span>
                        </td>
                        <td>
                            <span class="data-badge">
                                <?= htmlspecialchars($linha['semestre']); ?>
                            </span>
                        </td>
                        <td>
                            <span class="data-badge badge-turno">
                                <i class="fas fa-sun"></i>
                                <?= htmlspecialchars($linha['turno']); ?>
                            </span>
                        </td>
                        <td>
                            <span class="data-badge badge-sala">
                                <i class="fas fa-door-open"></i>
                                <?= htmlspecialchars($linha['sala']); ?>
                            </span>
                        </td>
                        <td>
                            <span class="data-badge badge-dia">
                                <i class="fas fa-calendar-day"></i>
                                <?= htmlspecialchars($linha['dia_semana']); ?>
                            </span>
                        </td>
                        <td>
                            <span class="data-badge" style="background: #e0e7ff; color: #3730a3;">
                                <i class="fas fa-clock"></i>
                                <?= htmlspecialchars(date('H:i', strtotime($linha['hora_inicio']))); ?>
                                -
                                <?= htmlspecialchars(date('H:i', strtotime($linha['hora_fim']))); ?>
                            </span>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <a href="editar_horario.php?id=<?= $linha['id']; ?>" class="btn btn-editar">
                                    <i class="fas fa-edit"></i>
                                    Editar
                                </a>
                                <a href="excluir_horario.php?id=<?= $linha['id']; ?>" 
                                   class="btn btn-excluir"
                                   onclick="return confirm('Tem certeza que deseja excluir este horário?');">
                                    <i class="fas fa-trash"></i>
                                    Excluir
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <!-- Estatísticas -->
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i>
            Exibindo <strong><?= $total ?></strong> horário(s) cadastrado(s) no sistema.
        </div>
    <?php endif; ?>
</main>

<!-- Footer -->
<footer class="footer">
    <p>© 2026 SMART KAMPUS • Universidade Católica de Moçambique</p>
</footer>

</body>
</html>