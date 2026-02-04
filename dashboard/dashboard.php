<?php
session_start();
require_once __DIR__ . '/../config/database.php';

if (!isset($_SESSION['user'])) {
    header('Location: /smartkampus/public/index.php');
    exit;
}

$user = $_SESSION['user'];
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Principal - SMART KAMPUS</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Reset e base - Estilo consistente */
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
            height: 100px;
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
            gap: 1rem;
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
        }

        /* Welcome card */
        .welcome-card {
            background: white;
            border-radius: 16px;
            padding: 2.5rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            border: 1px solid #e2e8f0;
            margin-bottom: 3rem;
            display: flex;
            align-items: center;
            gap: 2rem;
        }

        .user-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            overflow: hidden;
            border: 4px solid #e2e8f0;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .user-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .avatar-placeholder {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: linear-gradient(135deg, #3b43ce, #6366f1);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 3rem;
            box-shadow: 0 4px 12px rgba(59, 67, 206, 0.2);
        }

        .welcome-text h1 {
            font-size: 2.5rem;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 0.5rem;
            background: linear-gradient(135deg, #3b43ce, #6366f1);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .welcome-text p {
            color: #64748b;
            font-size: 1.1rem;
            margin-bottom: 0.5rem;
        }

        .user-email {
            color: #3b43ce;
            font-weight: 500;
            background: #f1f5f9;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            display: inline-block;
            margin-top: 0.5rem;
        }

        /* Dashboard Grid */
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }

        .dashboard-card {
            background: white;
            border-radius: 16px;
            padding: 2rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            border: 1px solid #e2e8f0;
            transition: all 0.3s ease;
            cursor: pointer;
            border-left: 6px solid transparent;
        }

        .dashboard-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }

        .dashboard-card.horarios {
            border-left-color: #3b43ce;
        }

        .dashboard-card.salas-livres {
            border-left-color: #10b981;
        }

        .dashboard-card.salas-ocupadas {
            border-left-color: #f59e0b;
        }

        .dashboard-card.calendario {
            border-left-color: #8b5cf6;
        }

        .card-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .card-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.75rem;
            color: white;
        }

        .card-icon.horarios {
            background: linear-gradient(135deg, #3b43ce, #6366f1);
        }

        .card-icon.salas-livres {
            background: linear-gradient(135deg, #10b981, #34d399);
        }

        .card-icon.salas-ocupadas {
            background: linear-gradient(135deg, #f59e0b, #fbbf24);
        }

        .card-icon.calendario {
            background: linear-gradient(135deg, #8b5cf6, #a78bfa);
        }

        .card-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #0f172a;
            margin-bottom: 0.25rem;
        }

        .card-description {
            color: #64748b;
            font-size: 0.95rem;
            margin-bottom: 1.5rem;
        }

        .card-stats {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #3b43ce;
            font-weight: 600;
            font-size: 0.9rem;
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
            border-radius: 20px;
            padding: 2.5rem;
            width: 100%;
            max-width: 1400px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
            animation: slideUp 0.4s ease;
            max-height: 85vh;
            overflow-y: auto;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid #f1f5f9;
        }

        .modal-title {
            font-size: 1.75rem;
            font-weight: 600;
            color: #0f172a;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .close-modal {
            background: none;
            border: none;
            font-size: 1.5rem;
            color: #94a3b8;
            cursor: pointer;
            width: 40px;
            height: 40px;
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

        /* Formulário de filtros */
        .filters-container {
            background: #f8fafc;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }

        .filters-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            align-items: end;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .form-label {
            font-weight: 500;
            color: #475569;
            font-size: 0.9rem;
        }

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
            border-color: #3b43ce;
            box-shadow: 0 0 0 3px rgba(59, 67, 206, 0.1);
        }

        /* Botões */
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
            height: fit-content;
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


        /* Calendário */
        .calendar-container {
            background: #f8fafc;
            border-radius: 12px;
            padding: 2rem;
            margin-top: 1.5rem;
        }

        .calendar-info {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #e2e8f0;
        }

        .calendar-preview {
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            overflow: hidden;
            margin-top: 1.5rem;
        }

        .calendar-preview iframe {
            width: 100%;
            height: 500px;
            border: none;
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
            
            .welcome-card {
                flex-direction: column;
                text-align: center;
                padding: 1.5rem;
            }
            
            .dashboard-grid {
                grid-template-columns: 1fr;
            }
            
            .modal-content {
                padding: 1.5rem;
                margin: 1rem;
            }
            
            .filters-grid {
                grid-template-columns: 1fr;
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
            <a href="dashboard.php" class="nav-button">
                <i class="fas fa-home"></i>
                <span>Painel principal</span>
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
                <img src="<?= htmlspecialchars($user['picture']) ?>" alt="Foto do usuário">
            </div>
        <?php else: ?>
            <div class="avatar-placeholder">
                <i class="fas fa-user"></i>
            </div>
        <?php endif; ?>
        
        <div class="welcome-text">
            <h1>Bem-vindo, <?= htmlspecialchars($user['name']); ?>!</h1>
            <div class="user-email">
                <i class="fas fa-envelope"></i>
                <?= htmlspecialchars($user['email']); ?>
            </div>
            <p>Acesse informações acadêmicas e gerencie sua experiência no campus.</p>
        </div>
    </div>

    <!-- Dashboard Grid -->
    <div class="dashboard-grid">
        <!-- Horários -->
        <div class="dashboard-card horarios" onclick="abrirModalHorarios()">
            <div class="card-header">
                <div class="card-icon horarios">
                    <i class="fas fa-clock"></i>
                </div>
                <div>
                    <h3 class="card-title">Ver Horários</h3>
                    <div class="card-stats">
                        <i class="fas fa-search"></i>
                        Consultar horários de aulas, testes e exames
                    </div>
                </div>
            </div>
            <p class="card-description">
                Consulte os horários acadêmicos filtrados por curso, ano, semestre e turno.
            </p>
        </div>

        <!-- Salas Livres -->
        <div class="dashboard-card salas-livres" onclick="abrirModalSalas('livres')">
            <div class="card-header">
                <div class="card-icon salas-livres">
                    <i class="fas fa-door-open"></i>
                </div>
                <div>
                    <h3 class="card-title">Salas Livres</h3>
                    <div class="card-stats">
                        <i class="fas fa-check-circle"></i>
                        Disponíveis para uso imediato
                    </div>
                </div>
            </div>
            <p class="card-description">
                Visualize quais salas estão disponíveis no momento atual para reservas.
            </p>
        </div>

        <!-- Salas Ocupadas -->
        <div class="dashboard-card salas-ocupadas" onclick="abrirModalSalas('ocupadas')">
            <div class="card-header">
                <div class="card-icon salas-ocupadas">
                    <i class="fas fa-door-closed"></i>
                </div>
                <div>
                    <h3 class="card-title">Salas Ocupadas</h3>
                    <div class="card-stats">
                        <i class="fas fa-exclamation-circle"></i>
                        Em uso no momento
                    </div>
                </div>
            </div>
            <p class="card-description">
                Consulte as salas que estão atualmente em uso ou reservadas.
            </p>
        </div>

        <!-- Calendário Académico -->
        <div class="dashboard-card calendario" onclick="abrirModalCalendario()">
            <div class="card-header">
                <div class="card-icon calendario">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <div>
                    <h3 class="card-title">Calendário Académico</h3>
                    <div class="card-stats">
                        <i class="fas fa-calendar-check"></i>
                        Datas importantes do semestre
                    </div>
                </div>
            </div>
            <p class="card-description">
                Acesse o calendário oficial com todas as datas acadêmicas importantes.
            </p>
        </div>
    </div>
</main>

<!-- ================= MODAL SALAS ================= -->
<div id="modalSalas" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="modal-title" id="tituloSalas">
                <i class="fas fa-door-open"></i>
                Salas
            </h2>
            <button class="close-modal" onclick="fecharModalSalas()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div id="resultadoSalas" class="table-container"></div>
        <div style="margin-top: 2rem;">
            <button onclick="fecharModalSalas()" class="btn btn-secondary">
                <i class="fas fa-times"></i>
                Fechar
            </button>
        </div>
    </div>
</div>

<!-- ================= MODAL HORÁRIOS ================= -->
<div id="modalHorarios" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="modal-title">
                <i class="fas fa-clock"></i>
                Consultar Horários
            </h2>
            <button class="close-modal" onclick="fecharModalHorarios()">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="filters-container">
            <div class="filters-grid">
                <div class="form-group">
                    <label class="form-label">Tipo de Horário</label>
                    <select id="tipoHorario" class="form-select" onchange="mostrarFiltros()">
                        <option value="">-- Selecione o tipo --</option>
                        <option value="aula">Aulas</option>
                        <option value="teste">Testes</option>
                        <option value="exame">Exames</option>
                    </select>
                </div>

                <div id="filtros" style="display:none; grid-column: span 3;">
                    <div class="filters-grid">
                        <div class="form-group">
                            <label class="form-label">Curso</label>
                            <select id="curso" class="form-select">
                                <option value="">Todos os cursos</option>
                                <option>Administração Pública</option>
                                <option>Contabilidade & Auditoria</option>
                                <option>Direito</option>
                                <option>Economia e Gestão</option>
                                <option>Gestão de Recursos Humanos</option>
                                <option>Meio Ambiente</option>
                                <option>Tecnologia de Informação</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Ano</label>
                            <select id="ano" class="form-select">
                                <option value="">Todos os anos</option>
                                <option>1</option>
                                <option>2</option>
                                <option>3</option>
                                <option>4</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Semestre</label>
                            <select id="semestre" class="form-select">
                                <option value="">Ambos semestres</option>
                                <option>I</option>
                                <option>II</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Turno</label>
                            <select id="turno" class="form-select">
                                <option value="">Todos os turnos</option>
                                <option>Diurno</option>
                                <option>Noturno</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <button onclick="buscarHorarios()" class="btn btn-primary">
                                <i class="fas fa-search"></i>
                                Buscar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="resultadoHorarios" class="table-container"></div>
    </div>
</div>

<!-- ================= MODAL CALENDÁRIO ACADÉMICO ================= -->
<div id="modalCalendario" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="modal-title">
                <i class="fas fa-calendar-alt"></i>
                Calendário Académico
            </h2>
            <button class="close-modal" onclick="fecharModalCalendario()">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <?php
        // Seleciona o último calendário publicado
        $cal = $conn->query("
            SELECT * FROM calendario_academico
            ORDER BY data_publicacao DESC
            LIMIT 1
        ");
        ?>

        <?php if ($cal && $cal->num_rows > 0): 
            $c = $cal->fetch_assoc();
        ?>
            <div class="calendar-info">
                <div>
                    <h3 style="color: #0f172a; margin-bottom: 0.5rem;">Calendário Académico Atual</h3>
                    <p style="color: #64748b;">
                        <i class="fas fa-calendar-check"></i>
                        Publicado em: <?= date('d/m/Y H:i', strtotime($c['data_publicacao'])) ?>
                    </p>
                </div>
            </div>

            <div class="calendar-preview">
                <iframe src="/smartkampus/public/uploads/calendario/<?= $c['caminho'] ?>"></iframe>
            </div>

            <div style="margin-top: 2rem; display: flex; gap: 1rem;">
                <a href="/smartkampus/public/uploads/calendario/<?= $c['caminho'] ?>" download class="btn btn-primary">
                    <i class="fas fa-download"></i>
                    Baixar Calendário
                </a>
                <button onclick="fecharModalCalendario()" class="btn btn-secondary">
                    <i class="fas fa-times"></i>
                    Fechar
                </button>
            </div>

        <?php else: ?>
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="far fa-calendar-times"></i>
                </div>
                <h3>Nenhum calendário disponível</h3>
                <p>Não existe nenhum calendário académico publicado ainda.</p>
                <button onclick="fecharModalCalendario()" class="btn btn-secondary" style="margin-top: 1rem;">
                    <i class="fas fa-times"></i>
                    Fechar
                </button>
            </div>
        <?php endif; ?>
        
    </div>

</div>


<!-- Footer -->
<footer class="footer">
    <p>© 2026 SMART KAMPUS • Universidade Católica de Moçambique</p>
</footer>

<script>
// ================= MODAL HORÁRIOS =================
function abrirModalHorarios() {
    document.getElementById('modalHorarios').style.display = 'flex';
    document.getElementById('resultadoHorarios').innerHTML = '';
    document.body.style.overflow = 'hidden';
}

function fecharModalHorarios() {
    document.getElementById('modalHorarios').style.display = 'none';
    document.body.style.overflow = 'auto';
}

// ================= MODAL SALAS =================
function abrirModalSalas(tipo) {
    document.getElementById('modalSalas').style.display = 'flex';
    const titulo = document.getElementById('tituloSalas');
    const icon = titulo.querySelector('i');
    
    if (tipo === 'livres') {
        titulo.innerHTML = '<i class="fas fa-door-open"></i> Salas Livres Agora';
        icon.className = 'fas fa-door-open';
    } else {
        titulo.innerHTML = '<i class="fas fa-door-closed"></i> Salas Ocupadas Agora';
        icon.className = 'fas fa-door-closed';
    }
    
    document.body.style.overflow = 'hidden';

    fetch('salas_status.php?tipo=' + tipo)
        .then(res => res.text())
        .then(html => {
            document.getElementById('resultadoSalas').innerHTML = html;
        });
}

function fecharModalSalas() {
    document.getElementById('modalSalas').style.display = 'none';
    document.body.style.overflow = 'auto';
}

// ================= MODAL FILTRO =================
function mostrarFiltros() {
    document.getElementById('filtros').style.display = 'grid';
    document.getElementById('resultadoHorarios').innerHTML = '';
}

function buscarHorarios() {
    const tipo = document.getElementById('tipoHorario').value;
    const curso = document.getElementById('curso').value;
    const ano = document.getElementById('ano').value;
    const semestre = document.getElementById('semestre').value;
    const turno = document.getElementById('turno').value;

    const params = new URLSearchParams({
        tipo, curso, ano, semestre, turno
    });

    fetch('listar_horario_visual.php?' + params.toString())
        .then(res => res.text())
        .then(html => {
            document.getElementById('resultadoHorarios').innerHTML = html;
        });
}

// ================= MODAL CALENDÁRIO =================
function abrirModalCalendario() {
    document.getElementById('modalCalendario').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function fecharModalCalendario() {
    document.getElementById('modalCalendario').style.display = 'none';
    document.body.style.overflow = 'auto';
}

// Fechar modais ao clicar fora
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
