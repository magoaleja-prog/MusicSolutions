<?php

declare(strict_types=1);

$formErrors = errors();
?>
<?php if ($formErrors !== []): ?>
    <ul class="form-errors">
        <?php foreach ($formErrors as $message): ?>
            <li><?= e($message) ?></li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>
