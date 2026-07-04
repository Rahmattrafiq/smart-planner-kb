<?php
require_once __DIR__ . '/functions.php';
requireLogin();

$user = currentUser();
$roadmapId = (int) ($_GET['id'] ?? 0);
$roadmap = getRoadmapById($roadmapId, (int) $user['id']);
if (!$roadmap) {
    flash('error', 'Roadmap tidak ditemukan.');
    header('Location: dashboard.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $actionId = $_POST['action_id'] ?? '';
    toggleProgress($roadmapId, $actionId);
    header('Location: view_roadmap.php?id=' . $roadmapId);
    exit;
}

$progressMap = getProgressMap($roadmapId);
include __DIR__ . '/partials/header.php';
?>

<div class="roadmap-header">
    <div class="profile-card">
        <div class="avatar">R</div>
        <div>
            <div class="profile-name"><?= htmlspecialchars($roadmap['nama']) ?></div>
            <div class="profile-meta">Program Studi: <?= htmlspecialchars($roadmap['program_studi']) ?> • Semester <?= (int) $roadmap['semester'] ?></div>
        </div>
    </div>
    <div class="gap-card">
        <div class="gap-title">Target Karier</div>
        <div class="gap-desc"><?= htmlspecialchars($roadmap['roadmap']['career_label'] ?? '-') ?></div>
        <div class="gap-tags">
            <span class="gap-tag"><?= htmlspecialchars($roadmap['roadmap']['career_description'] ?? '-') ?></span>
        </div>
    </div>
</div>

<div class="section-title">Timeline Roadmap</div>
<div class="roadmap-list">
    <?php foreach (($roadmap['roadmap']['semesters'] ?? []) as $semester): ?>
        <div class="timeline-card <?= ((int) $semester['semester_no'] === (int) $roadmap['semester']) ? 'now' : '' ?>">
            <div>
                <div class="tl-sem">Semester <?= (int) $semester['semester_no'] ?> - <?= htmlspecialchars($semester['phase_label']) ?> <?= ((int) $semester['semester_no'] === (int) $roadmap['semester']) ? '<span class="now-tag">Sekarang</span>' : '' ?></div>
                <div class="tl-phase">Fokus: <?= htmlspecialchars($semester['focus_tag']) ?></div>
                <ul class="tl-list">
                    <?php foreach ($semester['actions'] as $action): ?>
                        <li>
                            <form method="post" style="display:inline;">
                                <input type="hidden" name="action_id" value="<?= htmlspecialchars($action['name']) ?>">
                                <label style="display:inline-flex;align-items:center;gap:8px;">
                                    <input type="checkbox" name="done" value="1" onclick="this.form.submit()" <?= (!empty($progressMap[$action['name']]) ? 'checked' : '') ?>>
                                    <?= htmlspecialchars($action['name']) ?>
                                </label>
                            </form>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <span class="focus-badge focus-skill"><?= htmlspecialchars($semester['focus_tag']) ?></span>
        </div>
    <?php endforeach; ?>
</div>

<?php include __DIR__ . '/partials/footer.php'; ?>
