<?php

declare(strict_types=1);

/** @var array<int, array<string, mixed>> $resources */
/** @var string $activeType */
/** @var array<int, string> $types */
$labels = [
    'snippet' => 'Snippet',
    'project' => 'Proyecto',
    'package' => 'Libreria',
    'tool' => 'Herramienta',
    'link' => 'Enlace',
];
?>
<section class="container section">
    <div class="page-head">
        <div>
            <p class="eyebrow">Comunidad</p>
            <h1>Zona de programadores</h1>
            <p class="muted">Snippets, librerias, herramientas y proyectos hechos por y para musicos.</p>
        </div>
        <div class="page-head__actions">
            <a class="btn btn-primary" href="<?= url('programadores/nuevo') ?>">+ Publicar recurso</a>
        </div>
    </div>

    <div class="tabs">
        <a class="<?= $activeType === '' ? 'is-active' : '' ?>" href="<?= url('programadores') ?>">Todos</a>
        <?php foreach ($types as $type): ?>
            <a class="<?= $activeType === $type ? 'is-active' : '' ?>" href="<?= url('programadores?type=' . $type) ?>"><?= e($labels[$type] ?? ucfirst($type)) ?></a>
        <?php endforeach; ?>
    </div>

    <?php if ($resources === []): ?>
        <div class="empty-state">
            <span aria-hidden="true">🧩</span>
            <h2>Aun no hay recursos aqui</h2>
            <p>Comparte el primero y ayuda a la comunidad.</p>
            <a class="btn btn-primary" href="<?= url('programadores/nuevo') ?>">Publicar recurso</a>
        </div>
    <?php else: ?>
        <div class="grid grid--3">
            <?php foreach ($resources as $resource): ?>
                <article class="dev-card">
                    <header>
                        <span class="badge badge--type"><?= e($labels[$resource['type']] ?? ucfirst((string) $resource['type'])) ?></span>
                        <?php if (!empty($resource['language'])): ?>
                            <span class="tag"><?= e($resource['language']) ?></span>
                        <?php endif; ?>
                    </header>
                    <h3><a href="<?= url('programadores/' . $resource['id']) ?>"><?= e($resource['title']) ?></a></h3>
                    <p class="muted small"><?= e(mb_strimwidth((string) $resource['description'], 0, 140, '...')) ?></p>
                    <footer>
                        <span class="muted small"><?= e($resource['author_name'] ?? 'Anonimo') ?></span>
                        <span class="muted small"><?= e(date('d/m/Y', strtotime((string) $resource['created_at']))) ?></span>
                    </footer>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>
