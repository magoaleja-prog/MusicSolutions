<?php

declare(strict_types=1);

/** @var string $term */
/** @var array<int, array<string, mixed>> $local */
/** @var array<int, array<string, mixed>> $external */
/** @var array<int, array<string, mixed>> $scores */
/** @var array<int, array<string, mixed>> $resources */
$pageScripts = ['js/search.js'];
$bodyClass = 'skin-music';
?>
<section class="container section">
    <div class="page-head">
        <div>
            <p class="eyebrow">Explorar</p>
            <h1>Buscar musica</h1>
            <p class="muted">Busca por titulo, artista, genero, partitura o recurso de programador.</p>
        </div>
    </div>

    <form class="search-bar" data-search-form action="<?= url('buscar') ?>" method="get" role="search">
        <input type="search" name="q" value="<?= e($term) ?>" placeholder="Ej. guitarra flamenca, Beethoven, jazz..." autocomplete="off" data-search-input autofocus>
        <button type="submit" class="btn btn-primary">Buscar</button>
    </form>

    <div class="search-results" data-search-results>
        <section class="result-block">
            <h2>🎧 Catalogo local</h2>
            <div data-result-local>
                <?php if ($local === []): ?>
                    <p class="empty">Sin resultados en tu catalogo.</p>
                <?php else: ?>
                    <ul class="track-list">
                        <?php foreach ($local as $song): ?>
                            <li class="track">
                                <?php if (!empty($song['artwork_url'])): ?>
                                    <img src="<?= e($song['artwork_url']) ?>" alt="" width="48" height="48" loading="lazy">
                                <?php endif; ?>
                                <?php if (!empty($song['preview_url'])): ?>
                                    <?php $payload = ['url' => $song['preview_url'], 'title' => $song['title'], 'artist' => $song['artist'], 'art' => $song['artwork_url'] ?: '🎵']; ?>
                                    <button type="button" class="play-btn" data-play="<?= e(json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)) ?>" aria-label="Reproducir <?= e($song['title']) ?>">▶</button>
                                <?php endif; ?>
                                <div>
                                    <strong><?= e($song['title']) ?></strong>
                                    <small><?= e($song['artist']) ?><?= $song['album'] ? ' · ' . e($song['album']) : '' ?></small>
                                </div>
                                <?php if (!empty($song['genre'])): ?><span class="badge"><?= e($song['genre']) ?></span><?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </section>

        <section class="result-block" data-block-external <?= $term === '' ? 'hidden' : '' ?>>
            <h2>🌍 Resultados publicos</h2>
            <div data-result-external>
                <?php if ($term !== '' && $external === []): ?>
                    <p class="empty">Sin resultados externos.</p>
                <?php endif; ?>
                <ul class="track-list track-list--rich">
                    <?php foreach ($external as $track): ?>
                        <li class="track">
                            <?php if (!empty($track['artwork'])): ?>
                                <img src="<?= e($track['artwork']) ?>" alt="" width="48" height="48" loading="lazy">
                            <?php endif; ?>
                            <div>
                                <strong><?= e($track['title']) ?></strong>
                                <small><?= e($track['artist']) ?><?= $track['album'] ? ' · ' . e($track['album']) : '' ?><?= $track['year'] ? ' (' . e($track['year']) . ')' : '' ?></small>
                            </div>
                            <?php if (!empty($track['preview'])): ?>
                                <?php $payload = ['url' => $track['preview'], 'title' => $track['title'], 'artist' => $track['artist'], 'art' => '🎵']; ?>
                                <button type="button" class="play-btn" data-play="<?= e(json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)) ?>" aria-label="Reproducir preview de <?= e($track['title']) ?>">▶</button>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </section>

        <section class="result-block">
            <h2>🎼 Partituras relacionadas</h2>
            <div data-result-scores>
                <?php if ($scores === []): ?>
                    <p class="empty">Sin partituras para esta busqueda.</p>
                <?php else: ?>
                    <ul class="stack-list">
                        <?php foreach ($scores as $score): ?>
                            <li>
                                <a href="<?= url('partituras/' . $score['id']) ?>">
                                    <strong><?= e($score['title']) ?></strong>
                                    <small><?= e($score['composer'] ?: 'Compositor desconocido') ?></small>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </section>

        <section class="result-block">
            <h2>🧩 Recursos de programadores</h2>
            <div data-result-resources>
                <?php if ($resources === []): ?>
                    <p class="empty">Sin recursos para esta busqueda.</p>
                <?php else: ?>
                    <ul class="stack-list">
                        <?php foreach ($resources as $resource): ?>
                            <li>
                                <a href="<?= url('programadores/' . $resource['id']) ?>">
                                    <strong><?= e($resource['title']) ?></strong>
                                    <small><?= e(ucfirst((string) $resource['type'])) ?><?= $resource['language'] ? ' · ' . e($resource['language']) : '' ?></small>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </section>
    </div>
</section>
