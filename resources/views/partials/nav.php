<?php

declare(strict_types=1);

use App\Core\Auth;

$user = Auth::check() ? Auth::user() : null;
$links = [
    ['href' => url('dashboard'), 'label' => 'Inicio', 'path' => '/dashboard', 'icon' => '<path d="M3 10.5 12 3l9 7.5"/><path d="M5 9.5V21h5v-6h4v6h5V9.5"/>'],
    ['href' => url('afinador'), 'label' => 'Afinador', 'path' => '/afinador', 'icon' => '<path d="M9 18V6l10-3v12"/><circle cx="7" cy="18" r="2"/><circle cx="17" cy="15" r="2"/>'],
    ['href' => url('partituras'), 'label' => 'Partituras', 'path' => '/partituras', 'icon' => '<path d="M4 20V4a1 1 0 0 1 1-1h10l5 5v12a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1Z"/><path d="M14 3v5h5"/><path d="M8 12h6M8 16h6"/>'],
    ['href' => url('buscar'), 'label' => 'Buscar musica', 'path' => '/buscar', 'icon' => '<circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/>'],
    ['href' => url('programadores'), 'label' => 'Programadores', 'path' => '/programadores', 'icon' => '<path d="m8 6-6 6 6 6"/><path d="m16 6 6 6-6 6"/><path d="m13.5 4-3 16"/>'],
];
?>
<header class="topbar">
    <div class="container topbar__inner">
        <a class="brand" href="<?= url('/') ?>">
            <img src="<?= asset('img/logo.svg') ?>" alt="" width="36" height="36">
            <span>Music<b>Solutions</b></span>
        </a>

        <button class="nav-toggle" type="button" aria-expanded="false" aria-controls="main-nav" data-nav-toggle>
            <span class="sr-only">Abrir menu de navegacion</span>
            <span></span><span></span><span></span>
        </button>

        <nav class="main-nav" id="main-nav" data-nav aria-label="Navegacion principal">
            <?php if ($user !== null): ?>
                <?php foreach ($links as $link): ?>
                    <a class="<?= active($link['path']) ?>" href="<?= e($link['href']) ?>">
                        <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><?= $link['icon'] ?></svg>
                        <span><?= e($link['label']) ?></span>
                    </a>
                <?php endforeach; ?>

                <div class="user-chip">
                    <span class="user-chip__avatar" aria-hidden="true"><?= e(mb_strtoupper(mb_substr((string) $user['name'], 0, 1))) ?></span>
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