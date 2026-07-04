-- Skrip database MySQL untuk proyek KB
-- Sesuai dengan struktur yang dipakai oleh aplikasi PHP di folder KB

CREATE DATABASE IF NOT EXISTS smart_planner_kb
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE smart_planner_kb;

DROP TABLE IF EXISTS progress;
DROP TABLE IF EXISTS roadmaps;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE roadmaps (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    nama VARCHAR(255) NOT NULL,
    program_studi VARCHAR(255) NOT NULL,
    semester TINYINT NOT NULL,
    career_key VARCHAR(100) NOT NULL,
    skills JSON NOT NULL,
    minat VARCHAR(255) DEFAULT NULL,
    roadmap_json LONGTEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_roadmaps_user
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    INDEX idx_roadmaps_user_id (user_id),
    INDEX idx_roadmaps_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE progress (
    id INT AUTO_INCREMENT PRIMARY KEY,
    roadmap_id INT NOT NULL,
    action_id VARCHAR(255) NOT NULL,
    done TINYINT(1) NOT NULL DEFAULT 0,
    UNIQUE KEY uq_progress_roadmap_action (roadmap_id, action_id),
    CONSTRAINT fk_progress_roadmap
        FOREIGN KEY (roadmap_id) REFERENCES roadmaps(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Contoh data awal (opsional)
-- INSERT INTO users (nama, email, password_hash) VALUES
-- ('Admin', 'admin@example.com', '$2y$10$examplehash');
