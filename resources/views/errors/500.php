<?php

declare(strict_types=1);

/** @var int $status */
/** @var string $message */
/** @var string $trace */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>500 · MusicSolutions</title>
    <link rel="stylesheet" href="<?= asset('css/app.css') ?>">
</head>
<body class="error-body">
    <div class="error-card error-card--wide">
        <span class="error-card__code">500</span>
        <h1>Algo salio mal</h1>
        <p class="muted"><?= e($message !== '' ? $message : 'Ha ocurrido un error inesperado.') ?></p>
        <?= $trace ?? '' ?>
        <a class="btn btn-primary" href="<?= url('/') ?>">Volver al inicio</a>
    </div>
</body>
</html>
