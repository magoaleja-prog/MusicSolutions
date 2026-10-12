<?php

declare(strict_types=1);

use App\Core\Auth;

/**
 * Main layout for the public site and the authenticated application.
 *
 * Expected variables (all optional): $title, $content, $pageScripts, $bodyClass, $ticker.
 */
$title = $title ?? 'Plataforma para musicos';
$pageScripts = $pageScripts ?? [];
$bodyClass = $bodyClass ?? 'skin-music';
$ticker = $ticker ?? false;
$currentUser = Auth::check() ? Auth::user() : null;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title) ?> · MusicSolutions</title>
    <meta name="description" content="MusicSolutions: afinador online, biblioteca de partituras, buscador de musica con audio y una consola de programacion accesible.">
    <meta name="csrf-token" content="<?= e(csrf_token()) ?>">
    <link rel="icon" type="image/svg+xml" href="<?= asset('img/logo.svg') ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="<?= asset('css/app.css') ?>">
</head>
<body class="<?= e($bodyClass) ?>">
    <a class="skip-link" href="#contenido">Saltar al contenido</a>

    <div class="reading-guide" aria-hidden="true"></div>

    <?php require base_path('resources/views/partials/nav.php'); ?>

    <?php if ($ticker): ?>
        <div class="ticker" aria-hidden="true">
            <div class="ticker__track">
                <span>Music</span> Solutions <span>Afinador</span> Partituras <span>Buscar</span> Codigo <span>Comunidad</span> Rock <span>Jazz</span> Dev <span>Musica</span> Programacion <span>Ensayo</span> Deploy <span>Music</span> Solutions <span>Afinador</span> Partituras <span>Buscar</span> Codigo <span>Comunidad</span> Rock <span>Jazz</span> Dev <span>Musica</span> Programacion <span>Ensayo</span> Deploy
            </div>
        </div>
    <?php endif; ?>

    <main class="page" id="contenido">
        <?php require base_path('resources/views/partials/flash.php'); ?>
        <?= $content ?>
    </main>

    <?php require base_path('resources/views/partials/footer.php'); ?>

    <!-- Reproductor de musica global -->
    <div class="player" data-player aria-hidden="true">
        <div class="container player__inner">
            <div class="player__track">
                <div class="player__art" data-player-art aria-hidden="true">🎵</div>
                <div class="player__info">
                    <span class="player__title" data-player-title>Cargando...</span>
                    <span class="player__artist" data-player-artist></span>
                </div>
            </div>
            <div class="player__center">
                <div class="player__progress">
                    <span data-player-time>0:00</span>
                    <div class="player__bar" data-player-bar role="slider" aria-label="Progreso" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0" tabindex="0">
                        <div class="player__bar-fill" data-player-fill></div>
                    </div>
                    <span data-player-duration>0:00</span>
                </div>
            </div>
            <div class="player__controls">
                <input type="range" class="player__volume" data-player-volume min="0" max="1" step="0.05" value="1" aria-label="Volumen">
                <button type="button" class="player__btn player__btn--main" data-player-toggle aria-label="Reproducir o pausar">▶</button>
                <button type="button" class="player__btn" data-player-close aria-label="Cerrar reproductor">✕</button>
            </div>
        </div>
    </div>

    <!-- Accesibilidad -->
    <button type="button" class="a11y-fab" data-a11y-toggle aria-expanded="false" aria-controls="a11y-panel">
        <span aria-hidden="true">♿</span> Accesibilidad
    </button>

    <aside class="a11y-panel" id="a11y-panel" hidden aria-label="Ajustes de accesibilidad">
        <div class="a11y-panel__head">
            <h2>Accesibilidad</h2>
            <button type="button" data-a11y-close aria-label="Cerrar panel de accesibilidad">✕</button>
        </div>
        <label class="a11y-option">
            <span>Alto contraste</span>
            <input type="checkbox" data-a11y="contrast" role="switch">
        </label>
        <label class="a11y-option">
            <span>Texto grande</span>
            <input type="checkbox" data-a11y="large" role="switch">
        </label>
        <label class="a11y-option">
            <span>Sin animaciones</span>
            <input type="checkbox" data-a11y="nomotion" role="switch">
        </label>
        <label class="a11y-option">
            <span>Guia de lectura</span>
            <input type="checkbox" data-a11y="lineguide" role="switch">
        </label>
        <p class="muted small" style="margin:10px 0 0">Estas preferencias se guardan en tu dispositivo.</p>
    </aside>

    <script src="<?= asset('js/app.js') ?>" defer></script>
    <?php foreach ($pageScripts as $script): ?>
        <script src="<?= asset($script) ?>" defer></script>
    <?php endforeach; ?>
</body>
</html>