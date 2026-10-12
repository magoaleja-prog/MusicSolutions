<?php

declare(strict_types=1);

/**
 * Inserta datos de ejemplo: usuario demo, catalogo de canciones y recursos dev.
 *
 *   php bin/seed.php
 */

use App\Core\Env;

require dirname(__DIR__) . '/bootstrap/autoload.php';

Env::load(base_path('.env'));

$config = config('database');

$dsn = sprintf('%s:host=%s;port=%s;dbname=%s;charset=%s', $config['driver'], $config['host'], $config['port'], $config['database'], $config['charset']);

try {
    $pdo = new PDO($dsn, $config['username'], $config['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $exception) {
    fwrite(STDERR, "✗ No se pudo conectar con la base de datos. Ejecuta antes: php bin/migrate.php\n");
    fwrite(STDERR, '  Detalle: ' . $exception->getMessage() . "\n");
    exit(1);
}

// --- Usuario demo ----------------------------------------------------------
$email = 'demo@musicsolutions.test';

$existing = $pdo->prepare('SELECT id FROM users WHERE email = :email LIMIT 1');
$existing->execute(['email' => $email]);
$userId = $existing->fetchColumn();

if ($userId === false) {
    $insert = $pdo->prepare(
        'INSERT INTO users (name, username, email, password_hash, role)
         VALUES (:name, :username, :email, :password_hash, :role)'
    );
    $insert->execute([
        'name' => 'Usuario Demo',
        'username' => 'demo',
        'email' => $email,
        'password_hash' => password_hash('password', PASSWORD_DEFAULT),
        'role' => 'admin',
    ]);

    $userId = (int) $pdo->lastInsertId();
    echo "  ✓ Usuario demo creado (demo@musicsolutions.test / password)\n";
} else {
    $userId = (int) $userId;
    echo "  · El usuario demo ya existe, se reutiliza.\n";
}

// --- Catalogo de canciones (con preview de audio real) ---------------------
$songCount = (int) $pdo->query('SELECT COUNT(*) FROM songs')->fetchColumn();

// Cada cancion apunta a un preview MP3 publico (SoundHelix, sin API key).
$songs = [
    ['Claro de Luna', 'Ludwig van Beethoven', 'Sonatas', 'Clasica', 1801, 1],
    ['Canon en Re mayor', 'Johann Pachelbel', 'Obras barrocas', 'Clasica', 1680, 2],
    ['Asturias (Leyenda)', 'Isaac Albeniz', 'Suite Espanola', 'Clasica', 1892, 3],
    ['Entre dos aguas', 'Paco de Lucia', 'Fuente y caudal', 'Flamenco', 1973, 4],
    ['Spain', 'Chick Corea', 'Light as a Feather', 'Jazz', 1973, 5],
    ['Take Five', 'Dave Brubeck', 'Time Out', 'Jazz', 1959, 6],
    ['So What', 'Miles Davis', 'Kind of Blue', 'Jazz', 1959, 7],
    ['Autumn Leaves', 'Joseph Kosma', 'Standards', 'Jazz', 1945, 8],
    ['Hotel California', 'Eagles', 'Hotel California', 'Rock', 1976, 9],
    ['Stairway to Heaven', 'Led Zeppelin', 'Led Zeppelin IV', 'Rock', 1971, 10],
    ['Bohemian Rhapsody', 'Queen', 'A Night at the Opera', 'Rock', 1975, 11],
    ['Wonderwall', 'Oasis', '(What\'s the Story) Morning Glory?', 'Rock', 1995, 12],
    ['Blackbird', 'The Beatles', 'The White Album', 'Rock', 1968, 13],
    ['Shape of You', 'Ed Sheeran', 'Divide', 'Pop', 2017, 14],
    ['Someone Like You', 'Adele', '21', 'Pop', 2011, 15],
    ['Billie Jean', 'Michael Jackson', 'Thriller', 'Pop', 1982, 16],
];

$audioBase = 'https://www.soundhelix.com/examples/mp3/SoundHelix-Song-';

if ($songCount === 0) {
    $insert = $pdo->prepare(
        'INSERT INTO songs (title, artist, album, genre, year, preview_url)
         VALUES (:title, :artist, :album, :genre, :year, :preview_url)'
    );

    foreach ($songs as $song) {
        $insert->execute([
            'title' => $song[0],
            'artist' => $song[1],
            'album' => $song[2],
            'genre' => $song[3],
            'year' => $song[4],
            'preview_url' => $audioBase . $song[5] . '.mp3',
        ]);
    }

    echo '  ✓ Catalogo sembrado con ' . count($songs) . " canciones.\n";
} else {
    // Bases de datos existentes: completar los previews que falten.
    try {
        $patch = $pdo->prepare(
            'UPDATE songs SET preview_url = CONCAT(:base, ((id % 16) + 1), ".mp3")
             WHERE preview_url IS NULL OR preview_url = :empty'
        );
        $patch->execute(['base' => $audioBase, 'empty' => '']);
        echo "  ✓ Previews de audio completados en el catalogo existente.\n";
    } catch (PDOException $exception) {
        echo "  · Los previews ya estan asignados.\n";
    }
}

// --- Recursos de programadores --------------------------------------------
if ((int) $pdo->query('SELECT COUNT(*) FROM dev_resources')->fetchColumn() === 0) {
    $resources = [
        [
            'title' => 'Detector de acordes con Web Audio API',
            'description' => 'Snippet en JavaScript que analiza el microfono y estima el acorde dominante en tiempo real.',
            'type' => 'snippet',
            'language' => 'JavaScript',
            'url' => '',
            'content' => "const analyser = ctx.createAnalyser();\nanalyser.fftSize = 4096;\nconst data = new Float32Array(analyser.fftSize);",
        ],
        [
            'title' => 'Utilidades de teoria musical',
            'description' => 'Libreria con conversiones entre notas, frecuencias y numeros MIDI.',
            'type' => 'package',
            'language' => 'TypeScript',
            'url' => 'https://www.npmjs.com/',
            'content' => null,
        ],
        [
            'title' => 'Plantilla MVC en PHP',
            'description' => 'Estructura base de un proyecto PHP sin framework, muy util para empezar rapido.',
            'type' => 'project',
            'language' => 'PHP',
            'url' => 'https://github.com/',
            'content' => null,
        ],
    ];

    $insert = $pdo->prepare(
        'INSERT INTO dev_resources (user_id, title, description, type, language, url, content)
         VALUES (:user_id, :title, :description, :type, :language, :url, :content)'
    );

    foreach ($resources as $resource) {
        $insert->execute([
            'user_id' => $userId,
            'title' => $resource['title'],
            'description' => $resource['description'],
            'type' => $resource['type'],
            'language' => $resource['language'],
            'url' => $resource['url'],
            'content' => $resource['content'],
        ]);
    }

    echo '  ✓ Recursos de programadores sembrados: ' . count($resources) . ".\n";
} else {
    echo "  · Ya existen recursos de programadores.\n";
}

echo "\nListo. Entra con demo@musicsolutions.test / password\n";
