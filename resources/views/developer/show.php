<?php

declare(strict_types=1);

/** @var array<string, mixed> $resource */
/** @var bool $isOwner */
$labels = [
    'snippet' => 'Snippet',
    'project' => 'Proyecto',
    'package' => 'Libreria',
    'tool' => 'Herramienta',
    'link' => 'Enlace',
];
$bodyClass = 'skin-code';
?>
<section class="container section section--narrow">
    <div class="page-head">
        <div>
            <p class="eyebrow"><?= e($labels[$resource['type']] ?? ucfirst((string) $resource['type'])) ?></p>
            <h1><?= e($resource['title']) ?></h1>
            <p class="muted">
                Por <?= e($resource['author_name'] ?? 'Anonimo') ?>
                · <?= e(date('d/m/Y', strtotime((string) $resource['created_at']))) ?>
                <?= $resource['language'] ? ' · ' . e($resource['language']) : '' ?>
            </p>
        </div>
        <div class="page-head__actions">
            <a class="btn btn-ghost" href="<?= url('programadores') ?>">&larr; Volver</a>
            <?php if ($isOwner): ?>
                <form action="<?= url('programadores/' . $resource['id'] . '/eliminar') ?>" method="post" class="inline-form" onsubmit="return confirm('Eliminar este recurso?');">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-danger">Eliminar</button>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <p class="panel panel--soft"><?= nl2br(e($resource['description'])) ?></p>

    <?php if (!empty($resource['url'])): ?>
        <p><a class="btn btn-ghost" href="<?= e($resource['url']) ?>" target="_blank" rel="noopener noreferrer">🔗 Abrir enlace externo</a></p>
    <?php endif; ?>

    <?php if (!empty($resource['content'])): ?>
        <div class="code-block">
            <div class="code-block__bar">
                <span><?= e($resource['language'] ?: 'codigo') ?></span>
                <button type="button" class="btn btn-ghost btn-sm" data-copy-target="#resource-code">Copiar</button>
            </div>
            <pre><code id="resource-code"><?= e($resource['content']) ?></code></pre>
        </div>
    <?php endif; ?>

    <?php if (!empty($resource['file_path'])): ?>
        <p>
            <a class="btn btn-primary" href="<?= url('programadores/' . $resource['id'] . '/descargar') ?>">
                ⬇︎ Descargar <?= e($resource['original_name'] ?? 'archivo') ?>
            </a>
        </p>
    <?php endif; ?>
</section>
