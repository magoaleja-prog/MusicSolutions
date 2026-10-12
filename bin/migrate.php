<?php

declare(strict_types=1);

/**
 * Crea la base de datos `solutions` y ejecuta las migraciones pendientes.
 *
 *   php bin/migrate.php
 */

use App\Core\Env;

require dirname(__DIR__) . '/bootstrap/autoload.php';

Env::load(base_path('.env'));

$config = config('database');

$dsn = sprintf('%s:host=%s;port=%s;charset=%s', $config['driver'], $config['host'], $config['port'], $config['charset']);

try {
    $pdo = new PDO($dsn, $config['username'], $config['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $exception) {
    fwrite(STDERR, "✗ No se pudo conectar con MySQL/MariaDB.\n");
    fwrite(STDERR, "  Arranca MySQL desde el panel de XAMPP y vuelve a intentarlo.\n");
    fwrite(STDERR, '  Detalle: ' . $exception->getMessage() . "\n");
    exit(1);
}

$database = $config['database'];
$pdo->exec("CREATE DATABASE IF NOT EXISTS `{$database}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
$pdo->exec("USE `{$database}`");

$pdo->exec(
    'CREATE TABLE IF NOT EXISTS migrations (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        migration VARCHAR(190) NOT NULL,
        executed_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY migrations_migration_unique (migration)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
);

$executed = $pdo->query('SELECT migration FROM migrations')->fetchAll(PDO::FETCH_COLUMN);

$files = glob(base_path('database/migrations/*.sql')) ?: [];
sort($files);

$applied = 0;

foreach ($files as $file) {
    $name = basename($file);

    if (in_array($name, $executed, true)) {
        continue;
    }

    $sql = file_get_contents($file);

    if ($sql === false || trim($sql) === '') {
        continue;
    }

    $pdo->exec($sql);

    $statement = $pdo->prepare('INSERT INTO migrations (migration) VALUES (:migration)');
    $statement->execute(['migration' => $name]);

    echo "  ✓ Migracion aplicada: {$name}\n";
    $applied++;
}

echo "\nBase de datos \"{$database}\" lista. Migraciones nuevas: {$applied}.\n";
