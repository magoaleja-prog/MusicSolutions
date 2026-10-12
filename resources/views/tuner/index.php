<?php

declare(strict_types=1);

/** @var array<string, array{label:string, notes:array<int,string>}> $instruments */
$pageScripts = ['js/pitch-detection.js', 'js/tuner.js'];
$bodyClass = 'skin-music tuner-body';
?>
<section class="container section">
    <div class="page-head">
        <div>
            <p class="eyebrow">Afinador</p>
            <h1>Afinador en tiempo real</h1>
            <p class="muted">Elige tu instrumento, pulsa el microfono y toca una cuerda. La nota se detecta al instante.</p>
        </div>
    </div>

    <div class="tuner" data-tuner data-notes='<?= e(json_encode($instruments, JSON_UNESCAPED_UNICODE)) ?>'>
        <div class="tuner__gauge">
            <div class="tuner__note" data-tuner-note>--</div>
            <div class="tuner__meta">
                <span data-tuner-frequency>0.0 Hz</span>
                <span data-tuner-cents>0 cents</span>
            </div>

            <div class="meter" data-tuner-meter>
                <span class="meter__target" data-tuner-target></span>
                <span class="meter__needle" data-tuner-needle></span>
            </div>
            <div class="meter__scale">
                <span>-50</span><span>-25</span><span>0</span><span>+25</span><span>+50</span>
            </div>

            <p class="tuner__status" data-tuner-status>Pulsa "Escuchar" para empezar.</p>
        </div>

        <div class="tuner__controls">
            <label class="field">
                <span>Instrumento</span>
                <select data-tuner-instrument>
                    <?php foreach ($instruments as $key => $instrument): ?>
                        <option value="<?= e($key) ?>"><?= e($instrument['label']) ?></option>
                    <?php endforeach; ?>
                </select>
            </label>

            <button type="button" class="btn btn-primary btn-block btn-lg" data-tuner-toggle>🎤 Escuchar</button>

            <div class="tuner__strings" data-tuner-strings aria-label="Notas del instrumento (toca para escuchar la referencia)"></div>

            <p class="tuner__hint">
                <span class="eq" aria-hidden="true"><i></i><i></i><i></i><i></i><i></i><i></i><i></i></span>
                Pulsa una cuerda y se escuchara la nota correcta: afina de oido.
            </p>

            <label class="field field--inline">
                <input type="checkbox" data-tuner-a4 value="442">
                <span>Calibrar La a 442 Hz (por defecto 440 Hz)</span>
            </label>
        </div>
    </div>

    <p class="muted small center">
        Consejo: toca una cuerda a la vez y deja que el sonido se mantenga. Funciona mejor en un lugar silencioso.
    </p>
</section>
