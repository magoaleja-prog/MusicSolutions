<?php

declare(strict_types=1);
?>
<section class="container section section--narrow">
    <div class="page-head">
        <div>
            <p class="eyebrow">Biblioteca</p>
            <h1>Subir partitura</h1>
            <p class="muted">Formatos admitidos: PDF, MusicXML, MuseScore, MIDI e imagenes.</p>
        </div>
        <div class="page-head__actions">
            <a class="btn btn-ghost" href="<?= url('partituras') ?>">&larr; Volver</a>
        </div>
    </div>

    <?php require base_path('resources/views/partials/errors.php'); ?>

    <form action="<?= url('partituras') ?>" method="post" enctype="multipart/form-data" class="form panel">
        <?= csrf_field() ?>

        <label class="field">
            <span>Titulo *</span>
            <input type="text" name="title" value="<?= e(old('title')) ?>" placeholder="Sonata Claro de Luna" required>
        </label>

        <div class="field-row">
            <label class="field">
                <span>Compositor</span>
                <input type="text" name="composer" value="<?= e(old('composer')) ?>" placeholder="Ludwig van Beethoven">
            </label>

            <label class="field">
                <span>Instrumento</span>
                <input type="text" name="instrument" value="<?= e(old('instrument')) ?>" placeholder="Piano" list="instrument-list">
                <datalist id="instrument-list">
                    <option value="Piano"></option>
                    <option value="Guitarra"></option>
                    <option value="Violin"></option>
                    <option value="Bajo"></option>
                    <option value="Ukelele"></option>
                    <option value="Flauta"></option>
                    <option value="Saxofon"></option>
                </datalist>
            </label>
        </div>

        <label class="field">
            <span>Descripcion</span>
            <textarea name="description" rows="4" placeholder="Notas, dificultad, arreglos..."><?= e(old('description')) ?></textarea>
        </label>

        <label class="field">
            <span>Archivo *</span>
            <input type="file" name="file" accept=".pdf,.png,.jpg,.jpeg,.gif,.webp,.mus,.midi,.mid,.xml,.mscz,.mxl,.zip" required>
        </label>

        <label class="field">
            <span>Visibilidad</span>
            <select name="visibility">
                <option value="public" <?= old('visibility') === 'private' ? '' : 'selected' ?>>Publica (todos pueden verla)</option>
                <option value="private" <?= old('visibility') === 'private' ? 'selected' : '' ?>>Privada (solo yo)</option>
            </select>
        </label>

        <button type="submit" class="btn btn-primary btn-lg">Guardar partitura</button>
    </form>
</section>
