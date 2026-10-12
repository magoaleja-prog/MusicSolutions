<?php

declare(strict_types=1);

namespace App\Support;

/**
 * Searches the public iTunes catalogue so users can discover music.
 * No API key required. Failures degrade gracefully to an empty result set.
 */
final class MusicSearch
{
    public static function itunes(string $term, int $limit = 12): array
    {
        $term = trim($term);

        if ($term === '' || !(bool) config('app.music_search_external', true)) {
            return [];
        }

        $url = 'https://itunes.apple.com/search?' . http_build_query([
            'term' => $term,
            'entity' => 'song',
            'limit' => max(1, min(25, $limit)),
            'media' => 'music',
        ]);

        $payload = self::get($url);

        if ($payload === null) {
            return [];
        }

        $decoded = json_decode($payload, true);

        if (!is_array($decoded['results'] ?? null)) {
            return [];
        }

        return array_map(static function (array $track): array {
            return [
                'title' => $track['trackName'] ?? 'Sin titulo',
                'artist' => $track['artistName'] ?? 'Artista desconocido',
                'album' => $track['collectionName'] ?? '',
                'genre' => $track['primaryGenreName'] ?? '',
                'year' => isset($track['releaseDate']) ? substr((string) $track['releaseDate'], 0, 4) : '',
                'artwork' => $track['artworkUrl100'] ?? '',
                'preview' => $track['previewUrl'] ?? '',
                'url' => $track['trackViewUrl'] ?? '',
            ];
        }, $decoded['results']);
    }

    private static function get(string $url): ?string
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

            return ($status >= 200 && $status < 300 && is_string($response)) ? $response : null;
        }

        $context = stream_context_create([
            'http' => [
                'timeout' => 6,
                'method' => 'GET',
                'header' => "User-Agent: MusicSolutions/1.0\r\n",
            ],
        ]);

        $response = @file_get_contents($url, false, $context);

        return is_string($response) ? $response : null;
    }
}
