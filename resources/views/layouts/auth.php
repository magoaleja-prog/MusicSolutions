<?php

declare(strict_types=1);

/**
 * Minimal layout for authentication screens.
 */
$title = $title ?? 'Acceso';
$bodyClass = $bodyClass ?? 'auth-body';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title) ?> · MusicSolutions</title>
    <link rel="icon" type="image/svg+xml" href="<?= asset('img/logo.svg') ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('css/app.css') ?>">
</head>
<body class="<?= e($bodyClass) ?>">
    <div class="aurora" aria-hidden="true">
        <span class="aurora__blob aurora__blob--one"></span>
        <span class="aurora__blob aurora__blob--two"></span>
    </div>

    <div class="auth-shell">
        <a class="brand brand--center" href="<?= url('/') ?>">
            <img src="<?= asset('img/logo.svg') ?>" alt="" width="40" height="40">
            <span>Music<b>Solutions</b></span>
        </a>

        <?php require base_path('resources/views/partials/flash.php'); ?>

        <?= $content ?>
    </div>

    <script src="<?= asset('js/app.js') ?>" defer></script>
</body>
</html>
