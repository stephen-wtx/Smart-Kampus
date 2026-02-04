<?php
session_start();
require_once __DIR__ . '/../config/google.php';

if (isset($_SESSION['user'])) {
    header('Location: /smartkampus/dashboard/dashboard.php');
    exit;
}

$loginUrl = $client->createAuthUrl();
?>
<!DOCTYPE html>
<html lang="pt" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SMART KAMPUS | Login</title>

    <link rel="stylesheet" href="/smartkampus/public/assets/css/app.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <link rel="icon" type="image/x-icon" href="/smartkampus/public/assets/imgs/smartkampus-logo.png">
</head>

<body class="min-h-screen font-body bg-gray-50">


    <!-- Background para TODAS as telas -->
    <div class="fixed inset-0 -z-10 bg-[url('/smartkampus/public/assets/imgs/bg.jpeg')] bg-cover bg-center bg-no-repeat"></div>
    <div class="fixed inset-0 -z-10 bg-black/30 md:bg-black/40 backdrop-blur-sm"></div>


    <!-- Login Container -->
    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="animate-fade-in grid grid-cols-1 md:grid-cols-2 w-full max-w-4xl bg-white rounded-2xl shadow-xl overflow-hidden">

            <!-- Brand (sempre visível) -->
            <div class="flex flex-col items-center mt-10 justify-center p-8 md:p-10 text-center md:border-r border-gray-200">
                <img
                src="/smartkampus/public/assets/imgs/smartkampus-logo.png"
                alt="SMART KAMPUS"
                class="w-44 sm:w-52 md:w-80 mb-6"
                >


                <h1 class="font-heading text-4xl md:text-5xl tracking-wide text-primary mb-3">
                    SMART KAMPUS
                </h1>

                <p class="text-darktext text-sm md:text-base max-w-xs">
                    Sistema de gestão de salas e horários
                </p>

                <!-- Botão (mobile fica logo abaixo do slogan) -->
                <div class="w-full flex justify-center mt-8 md:hidden">
                    <a href="<?= htmlspecialchars($loginUrl); ?>"
                       class="btn btn-google transition-transform duration-300 hover:-translate-y-1"
                       aria-label="Entrar com email institucional da UCM">
                        <span class="font-semibold text-sm">
                            Entrar com email da UCM
                        </span>
                    </a>
                </div>
            </div>

            <!-- Login (APENAS md+) -->
            <div class="hidden md:flex mt-16 flex-col items-center justify-center p-10 text-center">
                <div class="mt-14">
                    <h2 class="text-3xl font-semibold text-gray-800 mb-3">
                        Boas-vindas!
                    </h2>

                    <p class="text-sm text-gray-600">
                        Use o seu email institucional para aceder ao sistema
                    </p>
                </div>

            <!-- Google Login Button -->
            <div class="mt-20 md:mt-24">
                <a href="<?= htmlspecialchars($loginUrl); ?>" 
                class="btn btn-google transition-transform duration-300 hover:-translate-y-1"
                aria-label="Entrar com email institucional da UCM">
                    <span class="font-semibold text-sm md:text-base">
                        Entrar com email da UCM
                    </span>
                </a>
            </div>


                <p class="text-xs text-gray-500 mt-4">
                    Apenas para comunidade académica da UCM.
                </p>
            </div>

        </div>
    </div>

    <!-- Footer -->
    <footer class="fixed bottom-4 left-0 right-0 text-center">
        <p class="text-xs font-medium text-white/90">
            © <?= date('Y'); ?> SMART KAMPUS • Universidade Católica de Moçambique
        </p>
    </footer>



</body>
</html>
