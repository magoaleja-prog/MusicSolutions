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

// --- Sincronizar el catalogo con musica real (iTunes, si hay red) -------
// Reemplaza los temas de demostracion (SoundHelix, audio que no coincide con
// el titulo) por canciones reales con su preview y portada. Es repetible:
// solo inserta lo que falta y no toca nada mas.

$catalogueTerms = [
    'flamenco', 'rock', 'pop', 'jazz', 'clasica', 'electronica', 'reggaeton',
    'latin', 'indie', 'metal', 'blues', 'salsa', 'hip hop', 'folk', 'soul',
    'reggae', 'banda sonora', 'chill', 'guitarra', 'piano', 'opera', 'bach',
    'beethoven', 'mozart', 'tango', 'bolero', 'cumbia', 'merengue',
    'bossa nova', 'rap', 'techno', 'house', 'rumba', 'country', 'funk', 'disco',
];

$tracks = [];

foreach ($catalogueTerms as $term) {
    $payload = fetch_json('https://itunes.apple.com/search?' . http_build_query([
        'term' => $term,
        'entity' => 'song',
        'media' => 'music',
        'country' => 'es',
        'limit' => 12,
    ]));

    foreach (($payload['results'] ?? []) as $track) {
        if (empty($track['previewUrl'])) {
            continue;
        }

        $key = strtolower(trim(($track['trackName'] ?? '') . '|' . ($track['artistName'] ?? '')));

        if ($key === '' || isset($tracks[$key])) {
            continue;
        }

        $artwork = (string) ($track['artworkUrl100'] ?? '');
        // Sube la resolucion de la portada (artworkUrl100 -> 300x300).
        $artwork = str_replace('100x100', '300x300', $artwork);

        $tracks[$key] = [
            'title' => trim((string) ($track['trackName'] ?? 'Sin titulo')),
            'artist' => trim((string) ($track['artistName'] ?? 'Artista desconocido')),
            'album' => trim((string) ($track['collectionName'] ?? '')),
            'genre' => trim((string) ($track['primaryGenreName'] ?? '')),
            'year' => isset($track['releaseDate']) ? (int) substr((string) $track['releaseDate'], 0, 4) : 0,
            'preview' => (string) $track['previewUrl'],
            'artwork' => $artwork,
        ];
    }
}

if ($tracks === []) {
    echo "  · Sin conexion con iTunes ahora mismo: se mantiene el catalogo de demostracion.\n";
} else {
    // Quita los temas demo de SoundHelix (sus nombres no correspondian al audio).
    $pdo->exec("DELETE FROM songs WHERE preview_url LIKE '%soundhelix.com%'");

    $existing = $pdo->query('SELECT preview_url FROM songs')->fetchAll(PDO::FETCH_COLUMN);

    $insert = $pdo->prepare(
        'INSERT INTO songs (title, artist, album, genre, year, preview_url, artwork_url)
         VALUES (:title, :artist, :album, :genre, :year, :preview_url, :artwork_url)'
    );

    $added = 0;

    foreach ($tracks as $track) {
        if (in_array($track['preview'], $existing, true)) {
            continue;
        }

        $insert->execute([
            'title' => $track['title'],
            'artist' => $track['artist'],
            'album' => $track['album'],
            'genre' => $track['genre'],
            'year' => $track['year'] ?: null,
            'preview_url' => $track['preview'],
            'artwork_url' => $track['artwork'] ?: null,
        ]);

        $added++;
    }

    $total = (int) $pdo->query('SELECT COUNT(*) FROM songs')->fetchColumn();
    echo "  ✓ Catalogo real desde iTunes: {$added} nuevos, {$total} temas en total (con preview y portada).\n";
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

/**
 * GET + JSON para la sincronizacion del catalogo (curl o stream context).
 */
function fetch_json(string $url): ?array
{
    if (function_exists('curl_init')) {
        $handle = curl_init($url);
        curl_setopt_array($handle, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 6,
            CURLOPT_CONNECTTIMEOUT => 4,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_USERAGENT => 'MusicSolutions/1.0',
        ]);

        $response = curl_exec($handle);
        $status = (int) curl_getinfo($handle, CURLINFO_HTTP_CODE);
        curl_close($handle);

        $json = ($status >= 200 && $status < 300 && is_string($response)) ? $response : null;
    } else {
        $json = @file_get_contents($url, false, stream_context_create([
            'http' => [
                'timeout' => 6,
                'method' => 'GET',
                'header' => "User-Agent: MusicSolutions/1.0\r\n",
            ],
        ]));
    }

    if (!is_string($json)) {
        return null;
    }

    $decoded = json_decode($json, true);

    return is_array($decoded) ? $decoded : null;
}

echo "\nListo. Entra con demo@musicsolutions.test / password\n";
