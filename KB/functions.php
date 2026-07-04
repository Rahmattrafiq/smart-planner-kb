<?php
require_once __DIR__ . '/config.php';

function flash(string $type, string $message): void {
    if (!isset($_SESSION['flash'])) {
        $_SESSION['flash'] = [];
    }
    $_SESSION['flash'][] = ['type' => $type, 'message' => $message];
}

function getFlashes(): array {
    $items = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $items;
}

function currentUser(): ?array {
    if (empty($_SESSION['user_id'])) {
        return null;
    }

    $pdo = connectDb();
    $stmt = $pdo->prepare('SELECT id, nama, email FROM users WHERE id = :id');
    $stmt->execute([':id' => $_SESSION['user_id']]);
    return $stmt->fetch() ?: null;
}

function requireLogin(): void {
    if (!currentUser()) {
        flash('error', 'Silakan login terlebih dahulu.');
        header('Location: login.php');
        exit;
    }
}

function getCareerOptions(): array {
    return [
        'software_engineer' => [
            'label' => 'Software Engineer',
            'description' => 'Bangun aplikasi, produk digital, dan sistem berbasis tim teknis.',
        ],
        'data_scientist' => [
            'label' => 'Data Scientist',
            'description' => 'Analisis data, machine learning, dan insight bisnis untuk keputusan.',
        ],
        'uiux_designer' => [
            'label' => 'UI/UX Designer',
            'description' => 'Merancang pengalaman pengguna yang nyaman dan menarik.',
        ],
    ];
}

function buildRoadmap(string $careerKey, int $semester, array $skills): array {
    $careers = getCareerOptions();
    $career = $careers[$careerKey] ?? $careers['software_engineer'];

    $baseActions = [
        1 => [
            ['name' => 'Pelajari dasar programming', 'category' => 'skill', 'weeks' => 3, 'resources' => ['Roadmap belajar coding']],
            ['name' => 'Bangun project mini', 'category' => 'portfolio', 'weeks' => 2, 'resources' => ['GitHub']],
        ],
        2 => [
            ['name' => 'Perkuat skill teknis utama', 'category' => 'skill', 'weeks' => 3, 'resources' => ['Course online']],
            ['name' => 'Ikuti challenge coding', 'category' => 'portfolio', 'weeks' => 2, 'resources' => ['LeetCode / HackerRank']],
        ],
        3 => [
            ['name' => 'Sertifikasi relevan', 'category' => 'certification', 'weeks' => 2, 'resources' => ['Platform sertifikasi']],
            ['name' => 'Ajukan magang / internship', 'category' => 'magang', 'weeks' => 2, 'resources' => ['LinkedIn']],
        ],
        4 => [
            ['name' => 'Siapkan portofolio final', 'category' => 'portfolio', 'weeks' => 2, 'resources' => ['Behance / GitHub']],
            ['name' => 'Networking dan interview', 'category' => 'career', 'weeks' => 2, 'resources' => ['LinkedIn / komunitas']],
        ],
    ];

    $semesters = [];
    for ($phase = 1; $phase <= 4; $phase++) {
        $phaseActions = $baseActions[$phase] ?? [];
        if ($phaseActions === []) {
            continue;
        }

        $semesters[] = [
            'semester_no' => $semester + ($phase - 1),
            'phase' => $phase,
            'phase_label' => match ($phase) {
                1 => 'Fondasi',
                2 => 'Penguatan',
                3 => 'Eksposur',
                default => 'Siap Karier',
            },
            'focus_tag' => match ($phase) {
                1 => 'Belajar Dasar',
                2 => 'Skill Utama',
                3 => 'Sertifikasi & Magang',
                default => 'Portofolio & Karier',
            },
            'actions' => array_map(function ($action) use ($skills) {
                $action['skill_hint'] = count($skills) > 0 ? 'Disesuaikan dari skill: ' . implode(', ', $skills) : 'Pilih skill yang ingin diperdalam';
                return $action;
            }, $phaseActions),
        ];
    }

    return [
        'career_key' => $careerKey,
        'career_label' => $career['label'],
        'career_description' => $career['description'],
        'skill_gap' => $skills,
        'semesters' => $semesters,
        'total_actions' => array_sum(array_map('count', $baseActions)),
    ];
}

function saveRoadmap(array $data): int {
    $pdo = connectDb();
    $stmt = $pdo->prepare('INSERT INTO roadmaps (user_id, nama, program_studi, semester, career_key, skills, minat, roadmap_json) VALUES (:user_id, :nama, :program_studi, :semester, :career_key, :skills, :minat, :roadmap_json)');
    $stmt->execute([
        ':user_id' => $data['user_id'],
        ':nama' => $data['nama'],
        ':program_studi' => $data['program_studi'],
        ':semester' => $data['semester'],
        ':career_key' => $data['career_key'],
        ':skills' => json_encode($data['skills']),
        ':minat' => $data['minat'],
        ':roadmap_json' => json_encode($data['roadmap']),
    ]);
    return (int) $pdo->lastInsertId();
}

function getRoadmapsForUser(int $userId): array {
    $pdo = connectDb();
    $stmt = $pdo->prepare('SELECT * FROM roadmaps WHERE user_id = :user_id ORDER BY created_at DESC');
    $stmt->execute([':user_id' => $userId]);
    return $stmt->fetchAll();
}

function getRoadmapById(int $id, int $userId): ?array {
    $pdo = connectDb();
    $stmt = $pdo->prepare('SELECT * FROM roadmaps WHERE id = :id AND user_id = :user_id');
    $stmt->execute([':id' => $id, ':user_id' => $userId]);
    $roadmap = $stmt->fetch();
    if (!$roadmap) {
        return null;
    }
    $roadmap['skills'] = json_decode($roadmap['skills'], true) ?: [];
    $roadmap['roadmap'] = json_decode($roadmap['roadmap_json'], true) ?: [];
    return $roadmap;
}

function toggleProgress(int $roadmapId, string $actionId): void {
    $pdo = connectDb();
    $stmt = $pdo->prepare('SELECT done FROM progress WHERE roadmap_id = :roadmap_id AND action_id = :action_id');
    $stmt->execute([':roadmap_id' => $roadmapId, ':action_id' => $actionId]);
    $row = $stmt->fetch();

    if ($row) {
        $newValue = (int) !$row['done'];
        $pdo->prepare('UPDATE progress SET done = :done WHERE roadmap_id = :roadmap_id AND action_id = :action_id')->execute([
            ':done' => $newValue,
            ':roadmap_id' => $roadmapId,
            ':action_id' => $actionId,
        ]);
    } else {
        $pdo->prepare('INSERT INTO progress (roadmap_id, action_id, done) VALUES (:roadmap_id, :action_id, 1)')->execute([
            ':roadmap_id' => $roadmapId,
            ':action_id' => $actionId,
        ]);
    }
}

function getProgressMap(int $roadmapId): array {
    $pdo = connectDb();
    $stmt = $pdo->prepare('SELECT action_id, done FROM progress WHERE roadmap_id = :roadmap_id');
    $stmt->execute([':roadmap_id' => $roadmapId]);
    $rows = $stmt->fetchAll();
    $result = [];
    foreach ($rows as $row) {
        $result[$row['action_id']] = (bool) $row['done'];
    }
    return $result;
}
