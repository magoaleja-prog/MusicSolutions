<?php

declare(strict_types=1);

use App\Core\Session;

$success = Session::pull('success');
$error = Session::pull('error');
?>
<?php if ($success): ?>
    <div class="container">
        <div class="flash flash--success" role="status" data-flash>
            <span><?= e($success) ?></span>
            <button type="button" class="flash__close" data-flash-close aria-label="Cerrar">&times;</button>
        </div>
    </div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="container">
        <div class="flash flash--error" role="alert" data-flash>
            <span><?= e($error) ?></span>
            <button type="button" class="flash__close" data-flash-close aria-label="Cerrar">&times;</button>
        </div>
    </div>
<?php endif; ?>
