<?php

declare(strict_types=1);

/** @var array{scores:int, resources:int, songs:int} $stats */
?>
<section class="hero">
    <div class="container hero__inner">
        <p class="eyebrow">Afinador · Partituras · Musica · Codigo</p>
        <h1 class="hero__title">
            Todo tu mundo musical<br>
            en <span class="gradient-text">un solo lugar</span>
        </h1>
        <p class="hero__lead">
            Afina tu instrumento, guarda y comparte tus partituras, descubre nueva musica y
            conecta con una comunidad de programadores. Entra con tu cuenta y empieza.
        </p>

        <div class="hero__actions">
            <a class="btn btn-primary btn-lg" href="<?= url('register') ?>">Crear cuenta gratis</a>
            <a class="btn btn-ghost btn-lg" href="<?= url('login') ?>">Iniciar sesion</a>
        </div>

        <dl class="hero__stats">
            <div><dt><?= number_format($stats['scores']) ?></dt><dd>Partituras guardadas</dd></div>
            <div><dt><?= number_format($stats['resources']) ?></dt><dd>Recursos de codigo</dd></div>
            <div><dt><?= number_format($stats['songs']) ?></dt><dd>Canciones en catalogo</dd></div>
        </dl>
    </div>
</section>

<section class="container section">
    <div class="section__head">
        <h2>Cuatro herramientas, una plataforma</h2>
        <p>Disenada para acompanarte desde el ensayo hasta el deploy.</p>
    </div>

    <div class="grid grid--4">
        <article class="feature-card">
            <span class="feature-card__icon" aria-hidden="true">🎚️</span>
            <h3>Afinador en tiempo real</h3>
            <p>Usa el microfono y detecta la nota exacta con presets para guitarra, bajo, ukelele, violin y mas.</p>
            <a href="<?= url('afinador') ?>">Abrir afinador &rarr;</a>
        </article>

        <article class="feature-card">
            <span class="feature-card__icon" aria-hidden="true">🎼</span>
            <h3>Tu biblioteca de partituras</h3>
            <p>Sube PDF, MusicXML, MuseScore o imagenes, organizalas por instrumento y compartelas con la comunidad.</p>
            <a href="<?= url('partituras') ?>">Ver partituras &rarr;</a>
        </article>

        <article class="feature-card">
            <span class="feature-card__icon" aria-hidden="true">🔎</span>
            <h3>Busca musica</h3>
            <p>Encuentra canciones por titulo, artista o genero combinando tu catalogo con resultados publicos.</p>
            <a href="<?= url('buscar') ?>">Buscar ahora &rarr;</a>
        </article>

        <article class="feature-card">
            <span class="feature-card__icon" aria-hidden="true">🧩</span>
            <h3>Zona de programadores</h3>
            <p>Comparte snippets, librerias, herramientas y proyectos con quien construye software para musicos.</p>
            <a href="<?= url('programadores') ?>">Entrar como dev &rarr;</a>
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
