<?php

declare(strict_types=1);

/** @var int $status */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>404 · MusicSolutions</title>
    <link rel="stylesheet" href="<?= asset('css/app.css') ?>">
</head>
<body class="error-body">
    <div class="error-card">
        <span class="error-card__code">404</span>
        <h1>Pagina no encontrada</h1>
        <p class="muted">La pagina que buscas no existe o fue movida.</p>
        <a class="btn btn-primary" href="<?= url('/') ?>">Volver al inicio</a>
    </div>
</body>
</html>
