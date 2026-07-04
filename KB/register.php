<?php
require_once __DIR__ . '/functions.php';

if (currentUser()) {
    header('Location: dashboard.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama'] ?? '');
    $email = strtolower(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    $errors = [];
    if (strlen($nama) < 2) {
        $errors[] = 'Nama minimal 2 karakter.';
    }
    if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
        $errors[] = 'Format email tidak valid.';
    }
    if (strlen($password) < 6) {
        $errors[] = 'Password minimal 6 karakter.';
    }
    if ($password !== $confirm) {
        $errors[] = 'Konfirmasi password tidak sesuai.';
    }

    if (!$errors) {
        $pdo = connectDb();
        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = :email');
        $stmt->execute([':email' => $email]);
        if ($stmt->fetch()) {
            $errors[] = 'Email sudah terdaftar.';
        } else {
            $hashed = password_hash($password, PASSWORD_BCRYPT);
            $insert = $pdo->prepare('INSERT INTO users (nama, email, password_hash) VALUES (:nama, :email, :password_hash)');
            $insert->execute([':nama' => $nama, ':email' => $email, ':password_hash' => $hashed]);
            $_SESSION['user_id'] = (int) $pdo->lastInsertId();
            flash('success', 'Akun berhasil dibuat.');
            header('Location: dashboard.php');
            exit;
        }
    }

    foreach ($errors as $error) {
        flash('error', $error);
    }
}

include __DIR__ . '/partials/header.php';
?>

<div class="form-card">
    <h1 class="page-title">Buat Akun Baru</h1>
    <p class="page-sub">Daftar untuk menyimpan roadmap karier Anda.</p>
    <form method="post">
        <div class="form-group">
            <label>Nama Lengkap</label>
            <input type="text" name="nama" required>
        </div>
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" required>
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" required>
        </div>
        <div class="form-group">
            <label>Konfirmasi Password</label>
            <input type="password" name="confirm_password" required>
        </div>
        <div class="form-actions">
            <button class="btn btn-primary" type="submit">Daftar</button>
        </div>
    </form>
    <p class="muted" style="margin-top:16px;">Sudah punya akun? <a href="login.php" style="color:var(--accent);font-weight:700;">Masuk di sini</a>.</p>
</div>

<?php include __DIR__ . '/partials/footer.php'; ?>
