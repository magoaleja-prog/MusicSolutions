<?php

declare(strict_types=1);

/**
 * Auth layout (login / register): centrado y enfocado.
 *
 * Variables: $title, $content.
 */
$title = $title ?? 'Acceso · MusicSolutions';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title) ?></title>
    <meta name="description" content="Accede a MusicSolutions para afinar, guardar partituras y compartir codigo.">
    <link rel="icon" type="image/svg+xml" href="<?= asset('img/logo.svg') ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="<?= asset('css/app.css') ?>">
</head>
<body class="skin-music">
    <a class="skip-link" href="#contenido">Saltar al contenido</a>

    <main class="auth-shell" id="contenido">
        <?php require base_path('resources/views/partials/flash.php'); ?>
        <?= $content ?>
    </main>

    <script src="<?= asset('js/app.js') ?>" defer></script>
    <?php foreach ($pageScripts ?? [] as $script): ?>
        <script src="<?= asset($script) ?>" defer></script>
    <?php endforeach; ?>
</body>
</html>