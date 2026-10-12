-- Catalogo local de canciones usado por el buscador.
CREATE TABLE IF NOT EXISTS songs (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(180) NOT NULL,
    artist VARCHAR(160) NOT NULL DEFAULT '',
    album VARCHAR(160) NOT NULL DEFAULT '',
    genre VARCHAR(80) NOT NULL DEFAULT '',
    year SMALLINT UNSIGNED NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    KEY songs_title_index (title),
    KEY songs_artist_index (artist)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
