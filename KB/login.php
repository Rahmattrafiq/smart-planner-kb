<?php
require_once __DIR__ . '/functions.php';

if (currentUser()) {
    header('Location: dashboard.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $pdo = connectDb();
    $stmt = $pdo->prepare('SELECT * FROM users WHERE email = :email');
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user_id'] = $user['id'];
        flash('success', 'Login berhasil.');
        header('Location: dashboard.php');
        exit;
    }

    flash('error', 'Email atau password salah.');
}

include __DIR__ . '/partials/header.php';
?>

<div class="form-card">
    <h1 class="page-title">Masuk ke Akun</h1>
    <p class="page-sub">Masuk untuk melihat dan mengelola roadmap karier Anda.</p>
    <form method="post">
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" required>
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" required>
        </div>
        <div class="form-actions">
            <button class="btn btn-primary" type="submit">Masuk</button>
        </div>
    </form>
    <p class="muted" style="margin-top:16px;">Belum punya akun? <a href="register.php" style="color:var(--accent);font-weight:700;">Daftar di sini</a>.</p>
</div>

<?php include __DIR__ . '/partials/footer.php'; ?>
