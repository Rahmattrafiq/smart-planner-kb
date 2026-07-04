<?php
require_once __DIR__ . '/functions.php';

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
    <h2>Login</h2>
    <p>Masuk untuk melihat dan mengelola roadmap Anda.</p>
    <form method="post">
        <label>Email</label>
        <input type="email" name="email" required>

        <label>Password</label>
        <input type="password" name="password" required>

        <button class="btn btn-primary" type="submit">Masuk</button>
    </form>
    <p class="muted">Belum punya akun? <a href="register.php">Daftar di sini</a>.</p>
</div>

<?php include __DIR__ . '/partials/footer.php'; ?>
