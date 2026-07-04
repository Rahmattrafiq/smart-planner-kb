<?php
require_once __DIR__ . '/functions.php';
requireLogin();

$user = currentUser();
$roadmaps = getRoadmapsForUser((int) $user['id']);
include __DIR__ . '/partials/header.php';
?>

<div class="hero-card">
    <div>
        <p class="eyebrow">Dashboard</p>
        <h1>Halo, <?= htmlspecialchars($user['nama']) ?> 👋</h1>
        <p>Buat roadmap karier baru atau lihat roadmap yang pernah Anda simpan.</p>
        <div class="actions">
            <a class="btn btn-primary" href="new_roadmap.php">Buat Roadmap Baru</a>
        </div>
    </div>
</div>

<div class="roadmap-card">
    <h3>Roadmap Anda</h3>
    <?php if ($roadmaps): ?>
        <div class="roadmap-list">
            <?php foreach ($roadmaps as $roadmap): ?>
                <div class="roadmap-item">
                    <strong><?= htmlspecialchars($roadmap['nama']) ?></strong><br>
                    <span class="muted">Program Studi: <?= htmlspecialchars($roadmap['program_studi']) ?> | Semester: <?= (int) $roadmap['semester'] ?></span><br>
                    <span class="muted">Target: <?= htmlspecialchars(json_decode($roadmap['roadmap_json'], true)['career_label'] ?? '-') ?></span>
                    <div class="actions">
                        <a class="btn btn-secondary" href="view_roadmap.php?id=<?= (int) $roadmap['id'] ?>">Lihat Detail</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p class="muted">Belum ada roadmap yang tersimpan.</p>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/partials/footer.php'; ?>
