<?php

declare(strict_types=1);

use App\Core\Auth;

/** @var array<int, array<string, mixed>> $scores */
/** @var int $scoreCount */
/** @var int $resourceCount */
/** @var array<int, array<string, mixed>> $latestResources */
$user = Auth::user();
?>
<section class="container section">
    <div class="page-head">
        <div>
            <p class="eyebrow">Panel</p>
            <h1>Hola, <?= e(explode(' ', (string) $user['name'])[0]) ?> 👋</h1>
            <p class="muted">Este es tu punto de partida. Que quieres hacer hoy?</p>
        </div>
        <div class="page-head__metrics">
            <div class="metric"><strong><?= number_format($scoreCount) ?></strong><span>Partituras</span></div>
            <div class="metric"><strong><?= number_format($resourceCount) ?></strong><span>Recursos dev</span></div>
        </div>
    </div>

    <div class="grid grid--4 quick-actions">
        <a class="quick-action" href="<?= url('afinador') ?>">
            <span aria-hidden="true">🎚️</span>
            <strong>Afinar</strong>
            <small>Escucha y afina al instante</small>
        </a>
        <a class="quick-action" href="<?= url('partituras/nueva') ?>">
            <span aria-hidden="true">🎼</span>
            <strong>Subir partitura</strong>
            <small>Anade un nuevo documento</small>
        </a>
        <a class="quick-action" href="<?= url('buscar') ?>">
            <span aria-hidden="true">🔎</span>
            <strong>Buscar musica</strong>
            <small>Encuentra tu proxima cancion</small>
        </a>
        <a class="quick-action" href="<?= url('programadores/nuevo') ?>">
            <span aria-hidden="true">🧩</span>
            <strong>Publicar recurso</strong>
            <small>Comparte codigo o un enlace</small>
        </a>
    </div>
</section>

<section class="container section">
    <div class="split">
        <div class="panel">
            <div class="panel__head">
                <h2>Mis ultimas partituras</h2>
                <a href="<?= url('partituras?mine=1') ?>">Ver todas</a>
            </div>

            <?php if ($scores === []): ?>
                <p class="empty">Aun no has subido partituras. <a href="<?= url('partituras/nueva') ?>">Sube la primera</a>.</p>
            <?php else: ?>
                <ul class="stack-list">
                    <?php foreach ($scores as $score): ?>
                        <li>
                            <a href="<?= url('partituras/' . $score['id']) ?>">
                                <strong><?= e($score['title']) ?></strong>
                                <small><?= e($score['composer'] ?: 'Compositor desconocido') ?><?= $score['instrument'] ? ' · ' . e($score['instrument']) : '' ?></small>
                            </a>
                            <span class="badge badge--<?= $score['visibility'] === 'public' ? 'public' : 'private' ?>">
                                <?= $score['visibility'] === 'public' ? 'Publica' : 'Privada' ?>
                            </span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>

        <div class="panel">
            <div class="panel__head">
                <h2>De la comunidad dev</h2>
                <a href="<?= url('programadores') ?>">Explorar</a>
            </div>

            <?php if ($latestResources === []): ?>
                <p class="empty">Todavia no hay recursos. <a href="<?= url('programadores/nuevo') ?>">Se el primero</a>.</p>
            <?php else: ?>
                <ul class="stack-list">
                    <?php foreach ($latestResources as $resource): ?>
                        <li>
                            <a href="<?= url('programadores/' . $resource['id']) ?>">
                                <strong><?= e($resource['title']) ?></strong>
                                <small><?= e($resource['author_name'] ?? 'Anonimo') ?><?= $resource['language'] ? ' · ' . e($resource['language']) : '' ?></small>
                            </a>
                            <span class="badge"><?= e(ucfirst((string) $resource['type'])) ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</section>
