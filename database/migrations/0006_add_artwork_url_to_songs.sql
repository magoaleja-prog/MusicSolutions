-- Portada del tema en el catalogo local (cargada desde iTunes al sembrar).
ALTER TABLE songs
    ADD COLUMN artwork_url VARCHAR(500) NULL DEFAULT NULL AFTER preview_url;