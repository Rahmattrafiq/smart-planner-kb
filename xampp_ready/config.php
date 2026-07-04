<?php
session_start();

if (!defined('APP_ROOT')) {
    define('APP_ROOT', __DIR__);
}

define('DB_PATH', APP_ROOT . '/data/smart_planner.sqlite');

define('APP_NAME', 'Smart Planner XAMPP');

function connectDb(): PDO {
    if (!is_dir(dirname(DB_PATH))) {
        mkdir(dirname(DB_PATH), 0777, true);
    }

    $pdo = new PDO('sqlite:' . DB_PATH);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    return $pdo;
}

function initDb(): void {
    $pdo = connectDb();

    $pdo->exec("""
        CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            nama TEXT NOT NULL,
            email TEXT NOT NULL UNIQUE,
            password_hash TEXT NOT NULL,
            created_at TEXT DEFAULT CURRENT_TIMESTAMP
        )
    """);

    $pdo->exec("""
        CREATE TABLE IF NOT EXISTS roadmaps (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER NOT NULL,
            nama TEXT NOT NULL,
            program_studi TEXT NOT NULL,
            semester INTEGER NOT NULL,
            career_key TEXT NOT NULL,
            skills TEXT NOT NULL,
            minat TEXT,
            roadmap_json TEXT NOT NULL,
            created_at TEXT DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY(user_id) REFERENCES users(id)
        )
    """);

    $pdo->exec("""
        CREATE TABLE IF NOT EXISTS progress (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            roadmap_id INTEGER NOT NULL,
            action_id TEXT NOT NULL,
            done INTEGER NOT NULL DEFAULT 0,
            UNIQUE(roadmap_id, action_id),
            FOREIGN KEY(roadmap_id) REFERENCES roadmaps(id)
        )
    """);
}

initDb();
