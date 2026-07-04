<?php
session_start();

if (!defined('APP_ROOT')) {
    define('APP_ROOT', __DIR__);
}

require_once __DIR__ . '/db.php';

define('APP_NAME', 'Smart Planner XAMPP');

function initDb(): void {
    $pdo = connectDb();

    $pdo->exec(<<<'SQL'
        CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nama VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL UNIQUE,
            password_hash VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    SQL
    );

    $pdo->exec(<<<'SQL'
        CREATE TABLE IF NOT EXISTS roadmaps (
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
            CONSTRAINT fk_roadmaps_user FOREIGN KEY (user_id) REFERENCES users(id)
                ON DELETE CASCADE ON UPDATE CASCADE,
            INDEX idx_roadmaps_user_id (user_id),
            INDEX idx_roadmaps_created_at (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    SQL
    );

    $pdo->exec(<<<'SQL'
        CREATE TABLE IF NOT EXISTS progress (
            id INT AUTO_INCREMENT PRIMARY KEY,
            roadmap_id INT NOT NULL,
            action_id VARCHAR(255) NOT NULL,
            done TINYINT(1) NOT NULL DEFAULT 0,
            UNIQUE KEY uq_progress_roadmap_action (roadmap_id, action_id),
            CONSTRAINT fk_progress_roadmap FOREIGN KEY (roadmap_id) REFERENCES roadmaps(id)
                ON DELETE CASCADE ON UPDATE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    SQL
    );
}

initDb();
