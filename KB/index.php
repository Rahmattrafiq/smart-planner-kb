<?php
require_once __DIR__ . '/functions.php';
$currentUser = currentUser();
include __DIR__ . '/partials/header.php';
?>

<div class="hero-card">
    <div>
        <p class="eyebrow">XAMPP Ready</p>
        <h1>Smart Planner Karier Mahasiswa</h1>
        <p>Rancang roadmap karier Anda dengan alur login, pendaftaran, dan roadmap yang tersimpan secara lokal menggunakan SQLite.</p>
        <div class="actions">
            <?php if ($currentUser): ?>
                <a class="btn btn-primary" href="dashboard.php">Buat Roadmap</a>
            <?php else: ?>
                <a class="btn btn-primary" href="register.php">Daftar Sekarang</a>
                <a class="btn btn-secondary" href="login.php">Masuk</a>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="card-grid">
    <div class="info-card">
        <h3>Login & Register</h3>
        <p>Pengguna bisa mendaftar, masuk, dan melihat roadmap masing-masing.</p>
    </div>
    <div class="info-card">
        <h3>Roadmap Dinamis</h3>
        <p>Form pembuatan roadmap otomatis menyiapkan rencana per fase.</p>
    </div>
    <div class="info-card">
        <h3>Siap Dipasang di XAMPP</h3>
        <p>Folder ini bisa langsung disalin ke htdocs dan dibuka di browser.</p>
    </div>
</div>

<?php include __DIR__ . '/partials/footer.php'; ?>
