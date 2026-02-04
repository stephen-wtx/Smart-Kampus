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

// ID do exame
$id = $_GET['id'] ?? null;
if (!$id) { die("Exame inválido."); }

// Buscar exame
$stmt = $conn->prepare("SELECT * FROM exames WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$exame = $stmt->get_result()->fetch_assoc();
if (!$exame) { die("Exame não encontrado."); }

// Configurações escaláveis
$diasSemana = ['Segunda-feira','Terça-feira','Quarta-feira','Quinta-feira','Sexta-feira','Sábado'];
$cursos = ['Administração Pública','Contabilidade & Auditoria','Direito','Economia e Gestão','Gestão de Recursos Humanos','Meio Ambiente','Tecnologia de Informação'];
$anos = ['1º','2º','3º','4º'];
$semestres = ['I','II'];
$turnos = ['Diurno','Noturno'];
$salas = [
    'Nelson Mandela 1','Nelson Mandela 2','Nkwame Nkrumah','Martin Luther King','Santo Agostinho','Dom Jaime Gonsalves',
    'Josefina Bakhita 1','Josefina Bakhita 2','Blase Pascal','Sala de Informática','Laboratório de SIG',
    'Cipriano Parite 1','Cipriano Parite 2','Laboratório de Línguas','São Tomás de Aquino','Roberto Busa',
    'Rosário Policarpo Nápica','Beato Newman','Francisco de Assis','São Francisco de Vitória','Max Planck'
];
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Exame - SMART KAMPUS</title>
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
            color: #8b5cf6;
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

        .alert-error {
            background: #fee2e2;
            border: 1px solid #fecaca;
            color: #991b1b;
        }

        .alert i {
            font-size: 1.25rem;
        }

        /* Formulário */
        .form-container {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            border: 1px solid #e2e8f0;
            margin-bottom: 2rem;
        }

        .form-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #0f172a;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .form-title i {
            color: #8b5cf6;
        }

        /* Tabela do formulário */
        .form-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid #e2e8f0;
            margin-bottom: 1.5rem;
        }

        .form-table th {
            background: #f8fafc;
            padding: 1rem;
            text-align: center;
            font-weight: 600;
            color: #475569;
            border-bottom: 1px solid #e2e8f0;
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-table td {
            padding: 0.75rem;
            border-bottom: 1px solid #f1f5f9;
            background: white;
        }

        .form-table tr:last-child td {
            border-bottom: none;
        }

        /* Campos do formulário */
        .form-select, .form-input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            font-size: 0.95rem;
            color: #334155;
            background: white;
            transition: all 0.2s ease;
        }

        .form-select:focus, .form-input:focus {
            outline: none;
            border-color: #8b5cf6;
            box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.1);
        }

        .form-select:hover, .form-input:hover {
            border-color: #cbd5e1;
        }

        .form-input[type="date"] {
            min-height: 46px;
        }

        /* Informações do exame */
        .exame-info {
            background: #faf5ff;
            border: 1px solid #f3e8ff;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .exame-icon {
            width: 48px;
            height: 48px;
            background: #8b5cf6;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.25rem;
        }

        .exame-details h3 {
            color: #0f172a;
            margin-bottom: 0.25rem;
        }

        .exame-details p {
            color: #64748b;
            font-size: 0.9rem;
        }

        /* Botões */
        .form-actions {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
            flex-wrap: wrap;
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
            background: #8b5cf6;
            color: white;
            border-color: #8b5cf6;
        }

        .btn-primary:hover {
            background: #7c3aed;
            border-color: #7c3aed;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(139, 92, 246, 0.2);
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
            background: #8b5cf6;
            color: white;
            border-color: #8b5cf6;
        }

        .btn-success:hover {
            background: #7c3aed;
            border-color: #7c3aed;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(139, 92, 246, 0.2);
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
        @media (max-width: 1024px) {
            .form-table {
                display: block;
                overflow-x: auto;
            }
            
            .form-table th, .form-table td {
                min-width: 120px;
            }
        }

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
            
            .form-container {
                padding: 1.5rem;
            }
            
            .form-table th, .form-table td {
                padding: 0.5rem;
                font-size: 0.85rem;
            }
            
            .form-actions {
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
            <a href="../../index.php" class="nav-button">
                <i class="fas fa-home"></i>
                <span>Admin Inicial</span>
            </a>
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
        <a href="../../../../index.php">Início</a>
        <span class="separator">/</span>
        <a href="../../index.php">Admin</a>
        <span class="separator">/</span>
        <a href="../index.php">Exames</a>
        <span class="separator">/</span>
        <a href="listar_exames.php">Listar Exames</a>
        <span class="separator">/</span>
        <span>Editar Exame #<?= $id ?></span>
    </div>

    <!-- Cabeçalho da página -->
    <div class="page-header">
        <h1 class="page-title">
            <i class="fas fa-edit"></i>
            Editar Exame
        </h1>
    </div>

    <!-- Mensagens de erro -->
    <?php if (!empty($_SESSION['erro'])): ?>
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i>
            <?= htmlspecialchars($_SESSION['erro']); ?>
        </div>
        <?php unset($_SESSION['erro']); ?>
    <?php endif; ?>

    <!-- Informações do exame -->
    <div class="exame-info">
        <div class="exame-icon">
            <i class="fas fa-file-contract"></i>
        </div>
        <div class="exame-details">
            <h3><?= htmlspecialchars($exame['disciplina']) ?> - <?= htmlspecialchars($exame['curso']) ?></h3>
            <p>Data: <?= date('d/m/Y', strtotime($exame['data'])) ?> • Sala: <?= htmlspecialchars($exame['sala']) ?> • Horário: <?= substr($exame['hora_inicio'], 0, 5) ?> - <?= substr($exame['hora_fim'], 0, 5) ?></p>
        </div>
    </div>

    <!-- Formulário de edição -->
    <div class="form-container">
        <h2 class="form-title">
            <i class="fas fa-pencil-alt"></i>
            Modificar Dados do Exame
        </h2>

        <form method="POST" action="atualizar_exame.php">
            <input type="hidden" name="id" value="<?= $exame['id']; ?>">

            <table class="form-table">
                <thead>
                    <tr>
                        <th>Dia</th>
                        <th>Data</th>
                        <th>Curso</th>
                        <th>Ano</th>
                        <th>Semestre</th>
                        <th>Disciplina</th>
                        <th>Turno</th>
                        <th>Sala</th>
                        <th>Início</th>
                        <th>Fim</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <!-- Dia da Semana -->
                        <td>
                            <select name="dia_semana" class="form-select">
                                <?php foreach ($diasSemana as $d): ?>
                                    <option value="<?= $d ?>" <?= $exame['dia_semana']===$d?'selected':'' ?>>
                                        <?= $d ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </td>

                        <!-- Data -->
                        <td>
                            <input type="date" name="data" class="form-input" 
                                   value="<?= htmlspecialchars($exame['data']); ?>" required>
                        </td>

                        <!-- Curso -->
                        <td>
                            <select name="curso" class="form-select">
                                <?php foreach ($cursos as $c): ?>
                                    <option value="<?= $c ?>" <?= $exame['curso']===$c?'selected':'' ?>>
                                        <?= $c ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </td>

                        <!-- Ano -->
                        <td>
                            <select name="ano" class="form-select">
                                <?php foreach ($anos as $a): ?>
                                    <option value="<?= $a ?>" <?= $exame['ano']===$a?'selected':'' ?>>
                                        <?= $a ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </td>

                        <!-- Semestre -->
                        <td>
                            <select name="semestre" class="form-select">
                                <?php foreach ($semestres as $s): ?>
                                    <option value="<?= $s ?>" <?= $exame['semestre']===$s?'selected':'' ?>>
                                        <?= $s ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </td>

                        <!-- Disciplina -->
                        <td>
                            <input type="text" name="disciplina" class="form-input" 
                                   value="<?= htmlspecialchars($exame['disciplina']); ?>" required>
                        </td>

                        <!-- Turno -->
                        <td>
                            <select name="turno" class="form-select">
                                <?php foreach ($turnos as $t): ?>
                                    <option value="<?= $t ?>" <?= $exame['turno']===$t?'selected':'' ?>>
                                        <?= $t ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </td>

                        <!-- Sala -->
                        <td>
                            <select name="sala" class="form-select">
                                <?php foreach ($salas as $sala): ?>
                                    <option value="<?= $sala ?>" <?= $exame['sala']===$sala?'selected':'' ?>>
                                        <?= $sala ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </td>

                        <!-- Hora Início -->
                        <td>
                            <input type="time" name="hora_inicio" class="form-input"
                                   value="<?= htmlspecialchars($exame['hora_inicio']); ?>" required>
                        </td>

                        <!-- Hora Fim -->
                        <td>
                            <input type="time" name="hora_fim" class="form-input"
                                   value="<?= htmlspecialchars($exame['hora_fim']); ?>" required>
                        </td>
                    </tr>
                </tbody>
            </table>

            <div class="form-actions">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save"></i>
                    Atualizar Exame
                </button>
                
                <a href="listar_exames.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i>
                    Voltar à Lista
                </a>
                
                <a href="../index.php" class="btn btn-secondary">
                    <i class="fas fa-times"></i>
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</main>

<!-- Footer -->
<footer class="footer">
    <p>© 2026 SMART KAMPUS • Universidade Católica de Moçambique</p>
</footer>

</body>
</html>