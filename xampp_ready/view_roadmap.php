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

<div class="roadmap-card">
    <h2><?= htmlspecialchars($roadmap['nama']) ?></h2>
    <p class="muted">Program Studi: <?= htmlspecialchars($roadmap['program_studi']) ?> | Semester: <?= (int) $roadmap['semester'] ?></p>
    <p><strong>Target Karier:</strong> <?= htmlspecialchars($roadmap['roadmap']['career_label'] ?? '-') ?></p>

    <?php foreach (($roadmap['roadmap']['semesters'] ?? []) as $semester): ?>
        <div class="roadmap-item" style="margin-top: 16px;">
            <h3>Semester <?= (int) $semester['semester_no'] ?> - <?= htmlspecialchars($semester['phase_label']) ?></h3>
            <p class="muted">Fokus: <?= htmlspecialchars($semester['focus_tag']) ?></p>
            <ul>
                <?php foreach ($semester['actions'] as $action): ?>
                    <li>
                        <form method="post" style="display:inline;">
                            <input type="hidden" name="action_id" value="<?= htmlspecialchars($action['name']) ?>">
                            <label style="display:inline;">
                                <input type="checkbox" name="done" value="1" onclick="this.form.submit()" <?= (!empty($progressMap[$action['name']]) ? 'checked' : '') ?>>
                                <?= htmlspecialchars($action['name']) ?>
                            </label>
                        </form>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endforeach; ?>
</div>

<?php include __DIR__ . '/partials/footer.php'; ?>
