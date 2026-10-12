-- Recursos compartidos en la zona de programadores.
CREATE TABLE IF NOT EXISTS dev_resources (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    title VARCHAR(150) NOT NULL,
    description TEXT NOT NULL,
    type ENUM('snippet', 'project', 'package', 'tool', 'link') NOT NULL DEFAULT 'snippet',
    language VARCHAR(40) NOT NULL DEFAULT '',
    url VARCHAR(255) NOT NULL DEFAULT '',
    content MEDIUMTEXT NULL,
    file_path VARCHAR(255) NULL,
    original_name VARCHAR(255) NULL,
    mime VARCHAR(120) NULL,
    size INT UNSIGNED NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    KEY dev_resources_user_id_index (user_id),
    KEY dev_resources_type_index (type),
    CONSTRAINT dev_resources_user_id_foreign FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
