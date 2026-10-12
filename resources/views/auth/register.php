<?php

declare(strict_types=1);
?>
<section class="auth-card">
    <h1>Crear cuenta</h1>
    <p class="muted">Unete a MusicSolutions en menos de un minuto.</p>

    <?php require base_path('resources/views/partials/errors.php'); ?>

    <form action="<?= url('register') ?>" method="post" class="form">
        <?= csrf_field() ?>

        <label class="field">
            <span>Nombre completo</span>
            <input type="text" name="name" value="<?= e(old('name')) ?>" placeholder="Maria Perez" required autocomplete="name">
        </label>

        <label class="field">
            <span>Nombre de usuario</span>
            <input type="text" name="username" value="<?= e(old('username')) ?>" placeholder="mariaperez" required autocomplete="username">
        </label>

        <label class="field">
            <span>Correo electronico</span>
            <input type="email" name="email" value="<?= e(old('email')) ?>" placeholder="tu@correo.com" required autocomplete="email">
        </label>

        <div class="field-row">
            <label class="field">
                <span>Contrasena</span>
                <input type="password" name="password" placeholder="Minimo 6 caracteres" required autocomplete="new-password">
            </label>

            <label class="field">
                <span>Repite la contrasena</span>
                <input type="password" name="password_confirmation" placeholder="Repite tu contrasena" required autocomplete="new-password">
            </label>
        </div>

        <button type="submit" class="btn btn-primary btn-block btn-lg">Crear mi cuenta</button>
    </form>

    <p class="auth-card__foot">
        Ya tienes cuenta? <a href="<?= url('login') ?>">Inicia sesion</a>
    </p>
</section>
