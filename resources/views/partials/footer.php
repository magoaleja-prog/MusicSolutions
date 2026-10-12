<?php

declare(strict_types=1);
?>
<footer class="site-footer">
    <div class="container site-footer__inner">
        <p>&copy; <?= date('Y') ?> MusicSolutions · Hecho para musicos y programadores.</p>
        <ul class="site-footer__links">
            <li><a href="<?= url('afinador') ?>">Afinador</a></li>
            <li><a href="<?= url('partituras') ?>">Partituras</a></li>
            <li><a href="<?= url('buscar') ?>">Buscar musica</a></li>
            <li><a href="<?= url('programadores') ?>">Programadores</a></li>
        </ul>
    </div>
</footer>
