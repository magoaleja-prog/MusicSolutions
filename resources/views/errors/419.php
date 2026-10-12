<?php

declare(strict_types=1);

/** @var int $status */
/** @var string $message */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>419 · MusicSolutions</title>
    <link rel="stylesheet" href="<?= asset('css/app.css') ?>">
</head>
<body class="error-body">
    <div class="error-card">
        <span class="error-card__code">419</span>
        <h1>Sesion expirada</h1>
        <p class="muted"><?= e($message !== '' ? $message : 'Por seguridad, vuelve atras y envia el formulario de nuevo.') ?></p>
        <a class="btn btn-primary" href="<?= url('/') ?>">Volver al inicio</a>
    </div>
</body>
</html>
