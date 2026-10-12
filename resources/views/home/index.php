<?php

declare(strict_types=1);

/**
 * Landing: portada publica.
 *
 * @var array{scores:int, resources:int, songs:int} $stats
 * @var array<int, array<string, mixed>> $latestSongs
 */
$bodyClass = 'skin-music page-home';
$ticker = true;
?>
<section class="hero">
    <div class="container hero__inner">
        <p class="eyebrow">Afinador · Partituras · Musica · Codigo</p>
        <h1 class="hero__title">
            Tu mundo musical<br>
            a todo <span class="gradient-text">volumen</span>
        </h1>
        <p class="hero__lead">
            Afina tu instrumento al instante, <strong>escucha musica</strong>, guarda y comparte tus partituras
            y entra en la zona de programadores. Una plataforma para musicos, sin vueltas.
        </p>

        <div class="hero__actions">
            <a class="btn btn-primary btn-lg" href="<?= url('register') ?>">Crear cuenta gratis</a>
            <a class="btn btn-ghost btn-lg" href="<?= url('login') ?>">Iniciar sesion</a>
        </div>

        <dl class="hero__stats">
            <div><dt><?= number_format($stats['scores']) ?></dt><dd>Partituras guardadas</dd></div>
            <div><dt><?= number_format($stats['resources']) ?></dt><dd>Recursos de codigo</dd></div>
            <div><dt><?= number_format($stats['songs']) ?></dt><dd>Canciones escuchables</dd></div>
        </dl>
    </div>
</section>

<section class="container section">
    <div class="section__head">
        <div>
            <p class="eyebrow">Soundcheck</p>
            <h2>Ahora suena</h2>
            <p class="muted">Dale al play y escucha el catalogo. Sin cuenta, sin registro.</p>
        </div>
        <span class="eq" aria-hidden="true"><i></i><i></i><i></i><i></i><i></i><i></i><i></i></span>
    </div>

    <?php if ($latestSongs === []): ?>
        <p class="empty">El catalogo se llenara pronto. Mientras tanto, <a href="<?= url('register') ?>">crea tu cuenta</a>.</p>
    <?php else: ?>
        <ul class="track-list">
            <?php foreach ($latestSongs as $song): ?>
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
</section>

<section class="container section">
    <div class="section__head">
        <h2>Cuatro herramientas, una plataforma</h2>
        <p>Disenada para acompanarte del ensayo al deploy.</p>
    </div>

    <div class="grid grid--4">
        <article class="feature-card">
            <span class="feature-card__icon" aria-hidden="true">🎚️</span>
            <h3>Afinador en tiempo real</h3>
            <p>Usa el microfono y detecta la nota exacta. Ademas suena la nota de referencia para afinar de oido.</p>
            <a href="<?= url('afinador') ?>">Abrir afinador &rarr;</a>
        </article>

        <article class="feature-card">
            <span class="feature-card__icon" aria-hidden="true">🎼</span>
            <h3>Tu biblioteca de partituras</h3>
            <p>Sube PDF, MusicXML, MuseScore o imagenes, organizalas por instrumento y compartelas.</p>
            <a href="<?= url('partituras') ?>">Ver partituras &rarr;</a>
        </article>

        <article class="feature-card">
            <span class="feature-card__icon" aria-hidden="true">🔎</span>
            <h3>Busca y escucha musica</h3>
            <p>Encuentra canciones por titulo o artista y reproducelas al instante con el reproductor.</p>
            <a href="<?= url('buscar') ?>">Buscar ahora &rarr;</a>
        </article>

        <article class="feature-card">
            <span class="feature-card__icon" aria-hidden="true">🧩</span>
            <h3>Consola de programadores</h3>
            <p>Prueba codigo en una consola integrada, detecta errores y comparte recursos con la comunidad dev.</p>
            <a href="<?= url('programadores/playground') ?>">Abrir consola &rarr;</a>
        </article>
    </div>
</section>

<section class="container section">
    <div class="cta-panel">
        <div>
            <h2>Listo para empezar?</h2>
            <p>Crea tu cuenta en segundos. Tus datos de acceso se guardan de forma segura.</p>
        </div>
        <a class="btn btn-primary btn-lg" href="<?= url('register') ?>">Crear mi cuenta</a>
    </div>
</section>