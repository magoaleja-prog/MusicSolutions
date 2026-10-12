<?php

declare(strict_types=1);
?>
<section class="auth-card">
    <h1>Iniciar sesion</h1>
    <p class="muted">Entra para afinar, guardar partituras y mucho mas.</p>

    <?php require base_path('resources/views/partials/errors.php'); ?>

    <form action="<?= url('login') ?>" method="post" class="form">
        <?= csrf_field() ?>

        <label class="field">
            <span>Correo electronico</span>
            <input type="email" name="email" value="<?= e(old('email')) ?>" placeholder="tu@correo.com" required autofocus autocomplete="email">
        </label>

        <label class="field">
            <span>Contrasena</span>
            <input type="password" name="password" placeholder="••••••••" required autocomplete="current-password">
        </label>

        <button type="submit" class="btn btn-primary btn-block btn-lg">Entrar</button>
    </form>

    <p class="auth-card__foot">
        No tienes cuenta? <a href="<?= url('register') ?>">Registrate gratis</a>
    </p>
</section>
