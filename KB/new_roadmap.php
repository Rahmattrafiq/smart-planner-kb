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
    <h2>Buat Roadmap</h2>
    <form method="post">
        <label>Nama Mahasiswa</label>
        <input type="text" name="nama" required>

        <label>Program Studi</label>
        <input type="text" name="program_studi" placeholder="Contoh: Ilmu Komputer" required>

        <label>Semester Saat Ini</label>
        <select name="semester" required>
            <?php for ($i = 1; $i <= 8; $i++): ?>
                <option value="<?= $i ?>">Semester <?= $i ?></option>
            <?php endfor; ?>
        </select>

        <label>Target Karier</label>
        <select name="career_key" required>
            <option value="">Pilih target karier</option>
            <?php foreach ($careers as $key => $career): ?>
                <option value="<?= htmlspecialchars($key) ?>"><?= htmlspecialchars($career['label']) ?></option>
            <?php endforeach; ?>
        </select>

        <label>Skill yang Sudah Dimiliki</label>
        <div>
            <?php foreach (['Python', 'Java', 'JavaScript', 'SQL', 'Git', 'UI/UX'] as $skill): ?>
                <label><input type="checkbox" name="skills[]" value="<?= htmlspecialchars($skill) ?>"> <?= htmlspecialchars($skill) ?></label>
            <?php endforeach; ?>
        </div>

        <label>Minat / Bidang yang Disukai</label>
        <input type="text" name="minat" placeholder="Contoh: Web, Data, AI">

        <button class="btn btn-primary" type="submit">Buat Roadmap</button>
    </form>
</div>

<?php include __DIR__ . '/partials/footer.php'; ?>
