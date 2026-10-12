<?php

declare(strict_types=1);

use App\Core\Auth;

/**
 * Main layout for the public site and the authenticated application.
 *
 * Expected variables (all optional): $title, $content, $pageScripts, $bodyClass.
 */
$title = $title ?? 'Plataforma para musicos';
$pageScripts = $pageScripts ?? [];
$bodyClass = $bodyClass ?? '';
$currentUser = Auth::check() ? Auth::user() : null;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title) ?> · MusicSolutions</title>
    <meta name="description" content="MusicSolutions: afinador online, biblioteca de partituras, buscador de musica y espacio para programadores.">
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
        <span class="aurora__blob aurora__blob--three"></span>
    </div>

    <?php require base_path('resources/views/partials/nav.php'); ?>

    <main class="page">
        <?php require base_path('resources/views/partials/flash.php'); ?>
        <?= $content ?>
    </main>

    <?php require base_path('resources/views/partials/footer.php'); ?>

    <script src="<?= asset('js/app.js') ?>" defer></script>
    <?php foreach ($pageScripts as $script): ?>
        <script src="<?= asset($script) ?>" defer></script>
    <?php endforeach; ?>
</body>
</html>
