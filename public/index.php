<?php
session_start();
require_once __DIR__ . '/../config/google.php';

// Se já estiver autenticado
if (isset($_SESSION['user'])) {
    header('Location: dashboard.php');
    exit;
}

$loginUrl = $client->createAuthUrl();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>SmartKampus | Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Google Fonts - Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --text-dark: #1f2937;
            --text-light: #6b7280;
            --border: #e5e7eb;
            --radius: 16px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, sans-serif;
        }

        body {
            min-height: 100vh;
            background:
                linear-gradient(rgba(0,0,0,0.45), rgba(0,0,0,0.45)),
                url("assets/bg.jpeg") no-repeat center center / cover;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        /* Container principal */
        .login-container {
            width: 100%;
            max-width: 900px;
            background: #ffffff;
            border-radius: var(--radius);
            display: grid;
            grid-template-columns: 1fr 1fr;
            overflow: hidden;
            box-shadow: 0 30px 70px rgba(0,0,0,0.35);
        }

        /* Sidebar */
        .login-brand {
            padding: 48px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            border-right: 1px solid var(--border);
        }

        .login-brand img {
            width: 290px;
            margin-bottom: 10px;
        }

        .login-brand h1 {
            font-size: 28px;
            color: var(--text-dark);
            margin-bottom: 8px;
            font-weight: 600;
        }

        .login-brand p {
            font-size: 15px;
            color: var(--text-light);
            max-width: 320px;
        }

        /* Área de login */
        .login-form {
            padding: 56px 48px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .login-form h2 {
            font-size: 24px;
            color: var(--text-dark);
            margin-bottom: 8px;
        }

        .login-form p {
            font-size: 14px;
            color: var(--text-light);
            margin-bottom: 32px;
            text-align: center;
            max-width: 320px;
        }

        /* Botão Google */
        .google-btn {
            width: 100%;
            max-width: 340px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            padding: 14px 16px;
            border-radius: 10px;
            border: 1px solid var(--border);
            background: #ffffff;
            color: #111827;
            font-size: 15px;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .google-btn img {
            width: 20px;
            height: 20px;
        }

        .google-btn:hover {
            background: #f9fafb;
            transform: translateY(-1px);
        }

        /* Mobile */
        @media (max-width: 768px) {
            .login-container {
                grid-template-columns: 1fr;
            }

            .login-brand {
                border-right: none;
                padding: 10px 14px 12px;
            }

            .login-form {
                padding: 24px;
            }
        }
    </style>
</head>
<body>

<main class="login-container">

    <!-- Sidebar / Branding -->
    <section class="login-brand">
        <img src="assets/smartkampus-logo.png" alt="Logo SmartKampus">
        <h1>Smart Kampus</h1>
        <p>Sistema de Gestão de Salas e Horários</p>
    </section>

    <!-- Login -->
    <section class="login-form">
        <h2>Bem-vindo</h2>
        <p>Utilize o seu e-mail institucional para aceder ao sistema.</p>

        <a class="google-btn" href="<?= htmlspecialchars($loginUrl); ?>">
            <img src="https://developers.google.com/identity/images/g-logo.png" alt="">
            Entrar com Google
        </a>
    </section>

</main>

</body>
</html>
