<?php

declare(strict_types=1);

use App\Core\Auth;

$user = Auth::check() ? Auth::user() : null;
$links = [
    ['href' => url('dashboard'), 'label' => 'Inicio', 'path' => '/dashboard'],
    ['href' => url('afinador'), 'label' => 'Afinador', 'path' => '/afinador'],
    ['href' => url('partituras'), 'label' => 'Partituras', 'path' => '/partituras'],
    ['href' => url('buscar'), 'label' => 'Buscar musica', 'path' => '/buscar'],
    ['href' => url('programadores'), 'label' => 'Programadores', 'path' => '/programadores'],
];
?>
<header class="topbar">
    <div class="container topbar__inner">
        <a class="brand" href="<?= url('/') ?>">
            <img src="<?= asset('img/logo.svg') ?>" alt="" width="34" height="34">
            <span>Music<b>Solutions</b></span>
        </a>

        <button class="nav-toggle" type="button" aria-expanded="false" aria-controls="main-nav" data-nav-toggle>
            <span class="sr-only">Abrir menu</span>
            <span></span><span></span><span></span>
        </button>

        <nav class="main-nav" id="main-nav" data-nav>
            <?php if ($user !== null): ?>
                <?php foreach ($links as $link): ?>
                    <a class="<?= active($link['path']) ?>" href="<?= e($link['href']) ?>"><?= e($link['label']) ?></a>
                <?php endforeach; ?>

                <div class="user-chip">
                    <span class="user-chip__avatar"><?= e(mb_strtoupper(mb_substr((string) $user['name'], 0, 1))) ?></span>
                    <span class="user-chip__name"><?= e($user['name']) ?></span>
                    <form action="<?= url('logout') ?>" method="post" class="inline-form">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-ghost btn-sm">Salir</button>
                    </form>
                </div>
            <?php else: ?>
                <a href="<?= url('login') ?>">Iniciar sesion</a>
                <a class="btn btn-primary btn-sm" href="<?= url('register') ?>">Crear cuenta</a>
            <?php endif; ?>
        </nav>
    </div>
</header>
