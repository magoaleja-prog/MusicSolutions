<?php

declare(strict_types=1);

/** @var array<int, string> $types */
$labels = [
    'snippet' => 'Snippet de codigo',
    'project' => 'Proyecto',
    'package' => 'Libreria / paquete',
    'tool' => 'Herramienta',
    'link' => 'Enlace',
];
$bodyClass = 'skin-code';
?>
<section class="container section section--narrow">
    <div class="page-head">
        <div>
            <p class="eyebrow">Comunidad</p>
            <h1>Publicar recurso</h1>
            <p class="muted">Comparte un fragmento de codigo, un enlace o adjunta un archivo.</p>
        </div>
        <div class="page-head__actions">
            <a class="btn btn-ghost" href="<?= url('programadores') ?>">&larr; Volver</a>
        </div>
    </div>

    <?php require base_path('resources/views/partials/errors.php'); ?>

    <form action="<?= url('programadores') ?>" method="post" enctype="multipart/form-data" class="form panel">
        <?= csrf_field() ?>

        <label class="field">
            <span>Titulo *</span>
            <input type="text" name="title" value="<?= e(old('title')) ?>" placeholder="Detector de acordes en tiempo real" required>
        </label>

        <div class="field-row">
            <label class="field">
                <span>Tipo *</span>
                <select name="type">
                    <?php foreach ($types as $type): ?>
                        <option value="<?= e($type) ?>" <?= old('type', 'snippet') === $type ? 'selected' : '' ?>><?= e($labels[$type] ?? ucfirst($type)) ?></option>
                    <?php endforeach; ?>
                </select>
            </label>

            <label class="field">
                <span>Lenguaje / tecnologia</span>
                <input type="text" name="language" value="<?= e(old('language')) ?>" placeholder="PHP, JavaScript, Python...">
            </label>
        </div>

        <label class="field">
            <span>Descripcion *</span>
            <textarea name="description" rows="3" placeholder="Explica que hace y como usarlo." required><?= e(old('description')) ?></textarea>
        </label>

        <label class="field">
            <span>Enlace (repositorio, demo, web)</span>
            <input type="url" name="url" value="<?= e(old('url')) ?>" placeholder="https://github.com/...">
        </label>

        <label class="field">
            <span>Fragmento de codigo</span>
            <textarea name="content" rows="8" class="code-input" placeholder="// Pega aqui tu codigo" spellcheck="false"><?= e(old('content')) ?></textarea>
        </label>

        <label class="field">
            <span>Adjuntar archivo (opcional)</span>
            <input type="file" name="file" accept=".zip,.tar,.gz,.pdf,.png,.jpg,.jpeg,.txt,.md,.json,.js,.ts,.php,.py,.java,.cs,.cpp,.c,.rb,.go,.rs,.sql,.yml,.yaml">
        </label>

        <p class="muted small">Debes aportar al menos un enlace, un fragmento de codigo o un archivo.</p>

        <button type="submit" class="btn btn-primary btn-lg">Publicar</button>
    </form>
</section>
