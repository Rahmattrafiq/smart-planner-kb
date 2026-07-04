<?php
require_once __DIR__ . '/functions.php';

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
    <h2>Buat Akun</h2>
    <p>Daftar untuk menyimpan roadmap Anda.</p>
    <form method="post">
        <label>Nama Lengkap</label>
        <input type="text" name="nama" required>

        <label>Email</label>
        <input type="email" name="email" required>

        <label>Password</label>
        <input type="password" name="password" required>

        <label>Konfirmasi Password</label>
        <input type="password" name="confirm_password" required>

        <button class="btn btn-primary" type="submit">Daftar</button>
    </form>
    <p class="muted">Sudah punya akun? <a href="login.php">Masuk di sini</a>.</p>
</div>

<?php include __DIR__ . '/partials/footer.php'; ?>
