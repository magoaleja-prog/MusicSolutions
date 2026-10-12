<?php

declare(strict_types=1);

/**
 * Consola de pruebas y detector de errores de la zona de programadores.
 *
 * @var array<int, string> $languages
 */
$pageScripts = ['js/playground.js'];
$bodyClass = 'skin-code';
?>
<section class="container section">
    <div class="page-head">
        <div>
            <p class="eyebrow">Dev console</p>
            <h1>Playground</h1>
            <p class="muted">Prueba codigo y detecta errores de sintaxis. JavaScript se ejecuta en una zona aislada.</p>
        </div>
        <div class="page-head__actions">
            <a class="btn btn-ghost" href="<?= url('programadores') ?>">&larr; Galeria</a>
            <a class="btn btn-primary" href="<?= url('programadores/nuevo') ?>">+ Publicar recurso</a>
        </div>
    </div>

    <div class="playground">
        <div class="playground__left">
            <label class="field">
                <span>Lenguaje</span>
                <select data-pg-language>
                    <?php foreach ($languages as $language): ?>
                        <option value="<?= e($language) ?>" <?= $language === 'javascript' ? 'selected' : '' ?>>
                            <?= e(['php' => 'PHP', 'javascript' => 'JavaScript', 'python' => 'Python', 'html' => 'HTML / CSS'][$language] ?? ucfirst($language)) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>

            <label class="field" style="margin-top:14px">
                <span>Codigo</span>
                <textarea class="code-editor" data-pg-editor spellcheck="false" aria-label="Editor de codigo del playground"></textarea>
            </label>

            <div class="playground__actions">
                <button type="button" class="btn btn-ghost btn-sm" data-pg-example>💡 Cargar ejemplo</button>
                <button type="button" class="btn btn-primary" data-pg-lint>🔍 Detectar errores</button>
                <button type="button" class="btn btn-ghost" data-pg-run>▶ Ejecutar (JS / HTML)</button>
            </div>
        </div>

        <div class="playground__right">
            <div class="term">
                <div class="term__bar"><i></i> <span data-pg-console-label>Consola</span></div>
                <div class="lint-output">
                    <div class="lint-output__head">
                        <span data-pg-status>Lista</span>
                        <button type="button" class="btn btn-ghost btn-sm" data-pg-clear>Limpiar</button>
                    </div>
                    <div class="lint-output__body" data-pg-output aria-live="polite">
                        Escribe codigo y pulsa "Detectar errores" o "Ejecutar".
                    </div>
                </div>
            </div>
            <iframe class="preview" data-pg-preview hidden title="Previsualizacion o zona aislada de ejecucion"></iframe>
        </div>
    </div>
</section>