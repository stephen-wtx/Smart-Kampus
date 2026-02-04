<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();

require_once __DIR__ . '/../config/google.php';
require_once __DIR__ . '/../config/database.php';

// 1. Verifica se o código OAuth chegou
if (!isset($_GET['code'])) {
    exit('Código OAuth não recebido');
}

// 2. Troca o code pelo token
$token = $client->fetchAccessTokenWithAuthCode($_GET['code']);

if (isset($token['error'])) {
    exit('Erro OAuth: ' . $token['error']);
}

$client->setAccessToken($token);

// 3. Obtem informações do utilizador
$oauth = new Google_Service_Oauth2($client);
$userInfo = $oauth->userinfo->get();

$email = $userInfo->email;

// 4. Valida domínio institucional
if (!preg_match('/@ucm\.ac\.mz$/', $email)) {
    // Página estilizada para email não institucional COM MESMO DESIGN DO LOGIN
    ?>
    <!DOCTYPE html>
    <html lang="pt" class="scroll-smooth">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Acesso Negado - SMART KAMPUS</title>

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        
        <!-- Google Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

        <link rel="icon" type="image/x-icon" href="/smartkampus/public/assets/imgs/smartkampus-logo.png">

        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }

            body {
                font-family: 'Inter', system-ui, -apple-system, sans-serif;
                min-height: 100vh;
                background: #f8fafc;
            }

            /* Animações */
            @keyframes fadeIn {
                from { opacity: 0; }
                to { opacity: 1; }
            }

            @keyframes fadeInUp {
                from { opacity: 0; transform: translateY(30px); }
                to { opacity: 1; transform: translateY(0); }
            }

            .animate-fade-in {
                animation: fadeIn 0.6s ease-out;
            }

            .animate-fade-in-up {
                animation: fadeInUp 0.6s ease-out;
            }

            /* Background igual ao login */
            .bg-overlay {
                position: fixed;
                inset: 0;
                z-index: -20;
                background: url('/smartkampus/public/assets/imgs/bg.jpeg') center/cover no-repeat;
            }

            .bg-blur {
                position: fixed;
                inset: 0;
                z-index: -10;
                background: rgba(0, 0, 0, 0.3);
                backdrop-filter: blur(8px);
            }

            /* Container principal */
            .min-h-screen {
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 20px;
            }

            /* Card de acesso negado - DIMENSIONAMENTO RESPONSIVO */
            .access-card {
                width: 100%;
                background: white;
                border-radius: 24px;
                padding: 30px 25px;
                box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
                text-align: center;
                animation: fadeInUp 0.6s ease-out;
                
                /* Mobile primeiro - card mais compacto */
                max-width: 90%;
                min-height: auto;
            }

            /* Logo */
            .logo-container {
                margin-bottom: 20px;
            }

            .logo-img {
                height: 90px;
                width: auto;
                margin: 0 auto 10px;
                display: block;
            }

            .logo-text {
                font-family: 'Bebas Neue', sans-serif;
                font-size: 2.5rem;
                color: #1e40af;
                letter-spacing: 1px;
                line-height: 1;
            }

            /* Ícone de alerta */
            .icon-alert {
                width: 70px;
                height: 70px;
                background: linear-gradient(135deg, #f87171, #ef4444);
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                margin: 0 auto 20px;
                color: white;
                font-size: 2rem;
                box-shadow: 0 10px 25px rgba(239, 68, 68, 0.25);
            }

            /* Títulos */
            .title {
                font-family: 'Inter', sans-serif;
                font-size: 1.6rem;
                font-weight: 700;
                color: #1e293b;
                margin-bottom: 12px;
            }

            .subtitle {
                font-size: 0.9rem;
                color: #64748b;
                line-height: 1.5;
                margin-bottom: 20px;
            }

            /* Email display */
            .email-display {
                background: #f8fafc;
                border: 2px solid #e2e8f0;
                border-radius: 16px;
                padding: 16px;
                margin: 20px 0;
                text-align: center;
            }

            .email-label {
                font-size: 0.85rem;
                color: #64748b;
                font-weight: 500;
                margin-bottom: 6px;
                display: block;
            }

            .email-value {
                font-size: 1rem;
                font-weight: 600;
                color: #1e293b;
                word-break: break-all;
                font-family: 'Inter', monospace;
            }

            /* Domínio requerido */
            .domain-box {
                background: #fef3c7;
                border: 2px solid #fbbf24;
                border-radius: 16px;
                padding: 16px;
                margin: 20px 0;
                text-align: left;
            }

            .domain-title {
                font-weight: 600;
                color: #92400e;
                margin-bottom: 10px;
                display: flex;
                align-items: center;
                gap: 8px;
                font-size: 0.95rem;
            }

            .domain-list {
                list-style: none;
                padding: 0;
            }

            .domain-list li {
                padding: 5px 0;
                color: #78350f;
                display: flex;
                align-items: center;
                gap: 8px;
                font-size: 0.9rem;
            }

            .domain-list li i {
                color: #16a34a;
            }

            /* Botão */
            .btn-container {
                margin-top: 25px;
            }

            .btn {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 10px;
                padding: 12px 24px;
                border-radius: 12px;
                font-family: 'Inter', sans-serif;
                font-weight: 600;
                font-size: 0.95rem;
                transition: all 0.3s ease;
                text-decoration: none;
                border: none;
                cursor: pointer;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            }

            .btn-primary {
                background: #1e40af;
                color: white;
            }

            .btn-primary:hover {
                background: #1e3a8a;
                transform: translateY(-3px);
                box-shadow: 0 6px 12px rgba(30, 64, 175, 0.25);
            }

            /* Footer igual ao login */
            .footer {
                position: fixed;
                bottom: 20px;
                left: 0;
                right: 0;
                text-align: center;
            }

            .footer-text {
                font-size: 0.75rem;
                font-weight: 500;
                color: rgba(255, 255, 255, 0.9);
                text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
            }

            /* RESPONSIVIDADE */

            /* Tablets - Card médio */
            @media (min-width: 640px) {
                .access-card {
                    max-width: 500px;
                    padding: 35px 30px;
                    min-height: 550px;
                }

                .logo-img {
                    height: 100px;
                }

                .logo-text {
                    font-size: 2.8rem;
                }

                .icon-alert {
                    width: 80px;
                    height: 80px;
                    font-size: 2.2rem;
                    margin-bottom: 25px;
                }

                .title {
                    font-size: 1.8rem;
                    margin-bottom: 15px;
                }

                .subtitle {
                    font-size: 1rem;
                    margin-bottom: 25px;
                    line-height: 1.6;
                }

                .email-display {
                    padding: 18px;
                    margin: 25px 0;
                }

                .email-label {
                    font-size: 0.9rem;
                }

                .email-value {
                    font-size: 1.1rem;
                }

                .domain-box {
                    padding: 18px;
                    margin: 25px 0;
                }

                .domain-title {
                    font-size: 1rem;
                }

                .domain-list li {
                    font-size: 0.95rem;
                }

                .btn {
                    padding: 14px 28px;
                    font-size: 1rem;
                }

                .footer-text {
                    font-size: 0.8rem;
                }
            }

            /* Desktop - Card mais longo */
            @media (min-width: 1024px) {
                .access-card {
                    max-width: 550px;
                    padding: 40px 35px;
                    min-height: 600px;
                }

                .logo-container {
                    margin-bottom: 30px;
                }

                .logo-img {
                    height: 120px;
                }

                .logo-text {
                    font-size: 3.2rem;
                }

                .icon-alert {
                    width: 90px;
                    height: 90px;
                    font-size: 2.5rem;
                    margin-bottom: 30px;
                }

                .title {
                    font-size: 2rem;
                    margin-bottom: 18px;
                }

                .subtitle {
                    font-size: 1.1rem;
                    margin-bottom: 30px;
                    line-height: 1.7;
                }

                .email-display {
                    padding: 22px;
                    margin: 30px 0;
                }

                .email-label {
                    font-size: 0.95rem;
                    margin-bottom: 8px;
                }

                .email-value {
                    font-size: 1.2rem;
                }

                .domain-box {
                    padding: 22px;
                    margin: 30px 0;
                }

                .domain-title {
                    font-size: 1.1rem;
                    margin-bottom: 12px;
                }

                .domain-list li {
                    font-size: 1rem;
                    padding: 6px 0;
                }

                .btn-container {
                    margin-top: 35px;
                }

                .btn {
                    padding: 16px 32px;
                    font-size: 1.05rem;
                    gap: 12px;
                }

                .footer-text {
                    font-size: 0.85rem;
                }
            }

            /* Desktop grande - Card ainda mais longo */
            @media (min-width: 1280px) {
                .access-card {
                    max-width: 600px;
                    padding: 45px 40px;
                    min-height: 650px;
                }

                .logo-img {
                    height: 130px;
                }

                .logo-text {
                    font-size: 3.5rem;
                }

                .title {
                    font-size: 2.2rem;
                }

                .subtitle {
                    font-size: 1.15rem;
                }

                .email-display {
                    padding: 25px;
                }

                .email-value {
                    font-size: 1.25rem;
                }
            }

            /* Mobile muito pequeno */
            @media (max-width: 375px) {
                .access-card {
                    padding: 25px 20px;
                    border-radius: 20px;
                    max-width: 95%;
                }

                .logo-img {
                    height: 80px;
                }

                .logo-text {
                    font-size: 2.2rem;
                }

                .icon-alert {
                    width: 65px;
                    height: 65px;
                    font-size: 1.8rem;
                }

                .title {
                    font-size: 1.5rem;
                }

                .btn {
                    padding: 11px 22px;
                    font-size: 0.9rem;
                }
            }
        </style>
    </head>
    <body>
        <!-- Background igual ao login -->
        <div class="bg-overlay"></div>
        <div class="bg-blur"></div>

        <!-- Container principal -->
        <div class="min-h-screen">
            <div class="access-card">
                <!-- Logo -->
                <div class="logo-container">
                    <img src="/smartkampus/public/assets/imgs/smartkampus-logo.png" 
                         alt="SMART KAMPUS Logo" 
                         class="logo-img">
                    <div class="logo-text">SMART KAMPUS</div>
                </div>

                <!-- Ícone de alerta -->
                <div class="icon-alert">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>

                <!-- Títulos -->
                <h1 class="title">Acesso Restrito</h1>
                <p class="subtitle">
                    O SMART KAMPUS é exclusivo para membros da<br>
                    Universidade Católica de Moçambique.
                </p>

                <!-- Email detectado -->
                <div class="email-display">
                    <span class="email-label">Email detectado:</span>
                    <span class="email-value"><?php echo htmlspecialchars($email); ?></span>
                </div>

                <!-- Domínio requerido -->
                <div class="domain-box">
                    <div class="domain-title">
                        <i class="fas fa-university"></i>
                        Domínio autorizado
                    </div>
                    <ul class="domain-list">
                        <li><i class="fas fa-check-circle"></i> @ucm.ac.mz</li>
                        <li><i class="fas fa-info-circle"></i> Apenas para comunidade académica UCM</li>
                    </ul>
                </div>

                <!-- Botão voltar -->
                <div class="btn-container">
                    <a href="/smartkampus/public/index.php" class="btn btn-primary">
                        <i class="fas fa-arrow-left"></i>
                        Voltar ao Login
                    </a>
                </div>
            </div>
        </div>

        <!-- Footer igual ao login -->
        <footer class="footer">
            <p class="footer-text">
                © <?php echo date('Y'); ?> SMART KAMPUS • Universidade Católica de Moçambique
            </p>
        </footer>
    </body>
    </html>
    <?php
    session_destroy();
    exit();
}

// ... (resto do código permanece igual)
// 5. Procura utilizador na BD
$stmt = $conn->prepare("SELECT id, role FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // Primeiro login → auto-registo
    $role = 'estudante';

    $stmt = $conn->prepare("
        INSERT INTO users (oauth_provider, oauth_uid, name, email, role, picture)
        VALUES ('google', ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param(
        "sssss",
        $userInfo->id,
        $userInfo->name,
        $email,
        $role,
        $userInfo->picture
    );
    $stmt->execute();
    $userId = $stmt->insert_id;
} else {
    // Utilizador existente
    $user = $result->fetch_assoc();
    $userId = $user['id'];
    $role   = $user['role'];

    // Atualiza last_login
    $stmt = $conn->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
}

// 6. Cria sessão segura
session_regenerate_id(true);

$_SESSION['user'] = [
    'id'    => $userId,
    'name'  => $userInfo->name,
    'email' => $email,
    'role'  => $role,
    'picture' => $userInfo->picture
];

// 7. Redireciona para dashboard principal (todos acessam)
header('Location: /smartkampus/dashboard/dashboard.php');
exit;
