<?php

declare(strict_types=1);

/** @var array<int, array<string, mixed>> $scores */
/** @var bool $mineOnly */
?>
<section class="container section">
    <div class="page-head">
        <div>
            <p class="eyebrow">Biblioteca</p>
            <h1>Partituras</h1>
            <p class="muted">Guarda tus partituras y explora lo que comparte la comunidad.</p>
        </div>
        <div class="page-head__actions">
            <a class="btn btn-primary" href="<?= url('partituras/nueva') ?>">+ Subir partitura</a>
        </div>
    </div>

    <div class="tabs">
        <a class="<?= $mineOnly ? '' : 'is-active' ?>" href="<?= url('partituras') ?>">Todas</a>
        <a class="<?= $mineOnly ? 'is-active' : '' ?>" href="<?= url('partituras?mine=1') ?>">Mis partituras</a>
    </div>

    <?php if ($scores === []): ?>
        <div class="empty-state">
            <span aria-hidden="true">🎼</span>
            <h2>No hay partituras todavia</h2>
            <p>Sube tu primer documento para empezar tu biblioteca.</p>
            <a class="btn btn-primary" href="<?= url('partituras/nueva') ?>">Subir partitura</a>
        </div>
    <?php else: ?>
        <div class="grid grid--3">
            <?php foreach ($scores as $score): ?>
                <article class="score-card">
                    <header>
                        <span class="badge badge--<?= $score['visibility'] === 'public' ? 'public' : 'private' ?>">
                            <?= $score['visibility'] === 'public' ? 'Publica' : 'Privada' ?>
                        </span>
                        <span class="score-card__ext"><?= e(strtoupper((string) pathinfo((string) $score['original_name'], PATHINFO_EXTENSION))) ?></span>
                    </header>
                    <h3><a href="<?= url('partituras/' . $score['id']) ?>"><?= e($score['title']) ?></a></h3>
                    <p class="muted small">
                        <?= e($score['composer'] ?: 'Compositor desconocido') ?>
                        <?= $score['instrument'] ? ' · ' . e($score['instrument']) : '' ?>
                    </p>
                    <footer>
                        <span class="muted small"><?= e($score['author_name'] ?? 'Anonimo') ?></span>
                        <span class="muted small"><?= e(date('d/m/Y', strtotime((string) $score['created_at']))) ?></span>
                    </footer>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>
