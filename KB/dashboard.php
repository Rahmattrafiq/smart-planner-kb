<?php
require_once __DIR__ . '/functions.php';
requireLogin();

$user = currentUser();
$roadmaps = getRoadmapsForUser((int) $user['id']);
include __DIR__ . '/partials/header.php';
?>

<div class="roadmap-header">
    <div class="profile-card">
        <div class="avatar"><?= strtoupper(substr($user['nama'], 0, 1)) ?></div>
        <div>
            <div class="profile-name">Halo, <?= htmlspecialchars($user['nama']) ?> 👋</div>
            <div class="profile-meta">Buat roadmap karier baru atau lanjutkan roadmap yang sebelumnya kamu simpan.</div>
        </div>
    </div>
    <div class="gap-card">
        <div class="gap-title">Mulai langkah berikutnya</div>
        <div class="gap-desc">Rancang target karier, cek skill yang sudah dimiliki, dan susun rencana belajar per semester.</div>
        <div class="actions">
            <a class="btn btn-primary" href="new_roadmap.php">Buat Roadmap Baru</a>
        </div>
    </div>
</div>

<div class="section-title">Roadmap Anda</div>
<?php if ($roadmaps): ?>
    <div class="roadmap-list">
        <?php foreach ($roadmaps as $roadmap): ?>
            <div class="timeline-card">
                <div>
                    <div class="tl-sem"><?= htmlspecialchars($roadmap['nama']) ?></div>
                    <div class="tl-phase">Program Studi: <?= htmlspecialchars($roadmap['program_studi']) ?> • Semester <?= (int) $roadmap['semester'] ?></div>
                    <div class="muted">Target Karier: <?= htmlspecialchars(json_decode($roadmap['roadmap_json'], true)['career_label'] ?? '-') ?></div>
                </div>
                <div class="actions">
                    <a class="btn btn-secondary" href="view_roadmap.php?id=<?= (int) $roadmap['id'] ?>">Lihat Detail</a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <div class="empty-state">Belum ada roadmap yang tersimpan. <a href="new_roadmap.php">Buat roadmap sekarang</a></div>
<?php endif; ?>

<?php include __DIR__ . '/partials/footer.php'; ?>
