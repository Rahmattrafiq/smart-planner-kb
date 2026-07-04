# -*- coding: utf-8 -*-
import sqlite3
import json
import os

DB_PATH = os.path.join(os.path.dirname(__file__), "smart_planner.db")


def get_db():
    conn = sqlite3.connect(DB_PATH)
    conn.row_factory = sqlite3.Row
    return conn


def init_db():
    conn = get_db()
    cur = conn.cursor()
    cur.execute("""
        CREATE TABLE IF NOT EXISTS mahasiswa (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            nama TEXT NOT NULL,
            program_studi TEXT NOT NULL,
            semester INTEGER NOT NULL,
            career_key TEXT NOT NULL,
            skills TEXT NOT NULL,
            minat TEXT,
            roadmap_json TEXT NOT NULL,
            created_at TEXT DEFAULT CURRENT_TIMESTAMP
        )
    """)
    cur.execute("""
        CREATE TABLE IF NOT EXISTS progress (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            mahasiswa_id INTEGER NOT NULL,
            action_id TEXT NOT NULL,
            done INTEGER DEFAULT 0,
            UNIQUE(mahasiswa_id, action_id),
            FOREIGN KEY (mahasiswa_id) REFERENCES mahasiswa(id)
        )
    """)
    conn.commit()
    conn.close()


def save_mahasiswa(nama, program_studi, semester, career_key, skills, minat, roadmap):
    conn = get_db()
    cur = conn.cursor()
    cur.execute(
        """INSERT INTO mahasiswa (nama, program_studi, semester, career_key, skills, minat, roadmap_json)
           VALUES (?, ?, ?, ?, ?, ?, ?)""",
        (nama, program_studi, semester, career_key, json.dumps(skills), minat, json.dumps(roadmap)),
    )
    conn.commit()
    mahasiswa_id = cur.lastrowid
    conn.close()
    return mahasiswa_id


def get_mahasiswa(mahasiswa_id):
    conn = get_db()
    row = conn.execute("SELECT * FROM mahasiswa WHERE id = ?", (mahasiswa_id,)).fetchone()
    conn.close()
    if not row:
        return None
    data = dict(row)
    data["skills"] = json.loads(data["skills"])
    data["roadmap"] = json.loads(data["roadmap_json"])
    return data


def get_all_mahasiswa():
    conn = get_db()
    rows = conn.execute("SELECT * FROM mahasiswa ORDER BY created_at DESC").fetchall()
    conn.close()
    return [dict(r) for r in rows]


def toggle_progress(mahasiswa_id, action_id):
    conn = get_db()
    cur = conn.cursor()
    row = cur.execute(
        "SELECT done FROM progress WHERE mahasiswa_id = ? AND action_id = ?",
        (mahasiswa_id, action_id),
    ).fetchone()
    if row is None:
        cur.execute(
            "INSERT INTO progress (mahasiswa_id, action_id, done) VALUES (?, ?, 1)",
            (mahasiswa_id, action_id),
        )
        new_status = True
    else:
        new_status = not bool(row["done"])
        cur.execute(
            "UPDATE progress SET done = ? WHERE mahasiswa_id = ? AND action_id = ?",
            (int(new_status), mahasiswa_id, action_id),
        )
    conn.commit()
    conn.close()
    return new_status


def get_progress_map(mahasiswa_id):
    conn = get_db()
    rows = conn.execute(
        "SELECT action_id, done FROM progress WHERE mahasiswa_id = ?", (mahasiswa_id,)
    ).fetchall()
    conn.close()
    return {r["action_id"]: bool(r["done"]) for r in rows}
