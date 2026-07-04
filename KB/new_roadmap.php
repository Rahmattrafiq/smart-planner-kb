<?php
require_once __DIR__ . '/functions.php';
requireLogin();

$user = currentUser();
$careers = getCareerOptions();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama'] ?? '');
    $programStudi = trim($_POST['program_studi'] ?? '');
    $semester = (int) ($_POST['semester'] ?? 0);
    $careerKey = $_POST['career_key'] ?? '';
    $skills = $_POST['skills'] ?? [];
    $minat = trim($_POST['minat'] ?? '');

    if (strlen($nama) < 2) {
        flash('error', 'Nama minimal 2 karakter.');
    } elseif ($semester < 1 || $semester > 8) {
        flash('error', 'Semester harus antara 1 dan 8.');
    } elseif (!isset($careers[$careerKey])) {
        flash('error', 'Target karier belum dipilih.');
    } elseif (!$skills) {
        flash('error', 'Pilih minimal satu skill.');
    } else {
        $roadmap = buildRoadmap($careerKey, $semester, $skills);
        $roadmapId = saveRoadmap([
            'user_id' => (int) $user['id'],
            'nama' => $nama,
            'program_studi' => $programStudi,
            'semester' => $semester,
            'career_key' => $careerKey,
            'skills' => $skills,
            'minat' => $minat,
            'roadmap' => $roadmap,
        ]);

        flash('success', 'Roadmap berhasil dibuat.');
        header('Location: view_roadmap.php?id=' . $roadmapId);
        exit;
    }
}

include __DIR__ . '/partials/header.php';
?>

<div class="form-card">
    <h1 class="page-title">Buat Roadmap Karier</h1>
    <p class="page-sub">Lengkapi informasi berikut untuk mendapatkan roadmap yang sesuai dengan tujuan kariermu.</p>
    <form method="post">
        <div class="form-row">
            <div class="form-group">
                <label>Nama Mahasiswa</label>
                <input type="text" name="nama" required>
            </div>
            <div class="form-group">
                <label>Program Studi</label>
                <input type="text" name="program_studi" placeholder="Contoh: Ilmu Komputer" required>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Semester Saat Ini</label>
                <select name="semester" required>
                    <option value="">Pilih semester</option>
                    <?php for ($i = 1; $i <= 8; $i++): ?>
                        <option value="<?= $i ?>">Semester <?= $i ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Target Karier</label>
                <select name="career_key" required>
                    <option value="">Pilih target karier</option>
                    <?php foreach ($careers as $key => $career): ?>
                        <option value="<?= htmlspecialchars($key) ?>"><?= htmlspecialchars($career['label']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="form-group" style="margin-bottom:18px;">
            <label>Skill yang Sudah Dimiliki</label>
            <p style="font-size:12.5px;color:#6b7594;margin:-4px 0 10px;">Pilih skill yang sudah kamu kuasai</p>
            <div class="checkbox-grid">
                <?php foreach (['Python', 'Java', 'JavaScript', 'SQL', 'Git', 'UI/UX'] as $skill): ?>
                    <label class="checkbox-chip">
                        <input type="checkbox" name="skills[]" value="<?= htmlspecialchars($skill) ?>"> <?= htmlspecialchars($skill) ?>
                    </label>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="form-group">
            <label>Minat / Bidang yang Disukai (Opsional)</label>
            <input type="text" name="minat" placeholder="Contoh: Web, Data, AI">
        </div>
        <div class="form-actions">
            <button class="btn btn-primary" type="submit">Buat Roadmap →</button>
        </div>
    </form>
</div>

<script>
document.querySelectorAll('.checkbox-chip input').forEach(function (cb) {
    cb.addEventListener('change', function () {
        this.closest('.checkbox-chip').classList.toggle('checked', this.checked);
    });
});
</script>

<?php include __DIR__ . '/partials/footer.php'; ?>
