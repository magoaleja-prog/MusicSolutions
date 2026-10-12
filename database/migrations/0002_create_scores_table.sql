-- Partituras subidas por los usuarios.
CREATE TABLE IF NOT EXISTS scores (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    title VARCHAR(150) NOT NULL,
    composer VARCHAR(120) NOT NULL DEFAULT '',
    instrument VARCHAR(80) NOT NULL DEFAULT '',
    description TEXT NULL,
    visibility ENUM('public', 'private') NOT NULL DEFAULT 'public',
    file_path VARCHAR(255) NOT NULL,
    original_name VARCHAR(255) NOT NULL,
    mime VARCHAR(120) NOT NULL DEFAULT 'application/octet-stream',
    size INT UNSIGNED NOT NULL DEFAULT 0,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    KEY scores_user_id_index (user_id),
    CONSTRAINT scores_user_id_foreign FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
