<?php

declare(strict_types=1);

use App\Core\App;

define('MUSICSOLUTIONS_START', microtime(true));

require dirname(__DIR__) . '/bootstrap/autoload.php';

$app = new App(dirname(__DIR__));
$app->boot();
$app->run();
