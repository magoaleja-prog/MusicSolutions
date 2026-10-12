<?php

declare(strict_types=1);

/** @var array<string, mixed> $score */
/** @var bool $isOwner */
$extension = strtolower((string) pathinfo((string) $score['original_name'], PATHINFO_EXTENSION));
$isPdf = $extension === 'pdf';
$isImage = in_array($extension, ['png', 'jpg', 'jpeg', 'gif', 'webp'], true);
?>
<section class="container section">
    <div class="page-head">
        <div>
            <p class="eyebrow"><?= e($score['instrument'] ?: 'Partitura') ?></p>
            <h1><?= e($score['title']) ?></h1>
            <p class="muted">
                <?= e($score['composer'] ?: 'Compositor desconocido') ?>
                · Subida por <?= e($score['author_name'] ?? 'Anonimo') ?>
                · <?= e(date('d/m/Y', strtotime((string) $score['created_at']))) ?>
            </p>
        </div>
        <div class="page-head__actions">
            <a class="btn btn-ghost" href="<?= url('partituras') ?>">&larr; Volver</a>
            <a class="btn btn-primary" href="<?= url('partituras/' . $score['id'] . '/descargar') ?>">Descargar</a>
            <?php if ($isOwner): ?>
                <form action="<?= url('partituras/' . $score['id'] . '/eliminar') ?>" method="post" class="inline-form" onsubmit="return confirm('Eliminar esta partitura?');">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-danger">Eliminar</button>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <?php if (trim((string) $score['description']) !== ''): ?>
        <p class="panel panel--soft"><?= nl2br(e($score['description'])) ?></p>
    <?php endif; ?>

    <?php if ($isPdf): ?>
        <div class="viewer">
            <iframe src="<?= url('partituras/' . $score['id'] . '/descargar') ?>#view=FitH" title="Vista previa de la partitura"></iframe>
        </div>
    <?php elseif ($isImage): ?>
        <div class="viewer viewer--image">
            <img src="<?= url('partituras/' . $score['id'] . '/descargar') ?>" alt="Vista previa de <?= e($score['title']) ?>">
        </div>
    <?php else: ?>
        <div class="empty-state">
            <span aria-hidden="true">📄</span>
            <h2>Vista previa no disponible</h2>
            <p>Este formato (<?= e(strtoupper($extension)) ?>) no se puede previsualizar. Descarga el archivo para abrirlo.</p>
        </div>
    <?php endif; ?>
</section>
