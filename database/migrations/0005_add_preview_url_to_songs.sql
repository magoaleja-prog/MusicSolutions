-- Preview de audio (URL publica) para escuchar las canciones del catalogo.
ALTER TABLE songs
    ADD COLUMN preview_url VARCHAR(500) NULL DEFAULT NULL AFTER genre;