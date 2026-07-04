<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= APP_NAME ?></title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; background: #f5f7fb; color: #14213d; }
        .container { max-width: 980px; margin: 0 auto; padding: 24px; }
        .topbar { display: flex; justify-content: space-between; align-items: center; padding: 16px 0 24px; }
        .brand { font-weight: 700; font-size: 20px; }
        .nav a { margin-left: 12px; color: #2c6df5; text-decoration: none; font-weight: 600; }
        .hero-card, .form-card, .info-card, .roadmap-card { background: white; border-radius: 16px; padding: 24px; box-shadow: 0 8px 24px rgba(0,0,0,0.06); }
        .hero-card { display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; }
        .eyebrow { text-transform: uppercase; color: #2c6df5; font-size: 12px; font-weight: 700; letter-spacing: 0.08em; }
        h1, h2, h3 { margin-top: 0; }
        .actions { margin-top: 16px; }
        .btn { display: inline-block; padding: 10px 16px; border-radius: 10px; text-decoration: none; font-weight: 600; margin-right: 8px; }
        .btn-primary { background: #2c6df5; color: white; }
        .btn-secondary { background: #edf3ff; color: #2c6df5; }
        .card-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; }
        .form-card { max-width: 560px; margin: 20px auto; }
        label { display: block; margin: 10px 0 6px; font-weight: 600; }
        input, select, textarea { width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #dbe3f0; margin-bottom: 8px; }
        .muted { color: #6b7594; font-size: 14px; }
        .flash { padding: 10px 12px; border-radius: 10px; margin-bottom: 12px; font-weight: 600; }
        .flash.success { background: #e8f8ee; color: #166534; }
        .flash.error { background: #fdecea; color: #b42318; }
        .roadmap-list { display: grid; gap: 12px; }
        .roadmap-item { border: 1px solid #e6e9f2; border-radius: 12px; padding: 14px; }
        @media (max-width: 700px) { .card-grid { grid-template-columns: 1fr; } .hero-card { flex-direction: column; align-items:flex-start; } }
    </style>
</head>
<body>
<div class="container">
    <div class="topbar">
        <div class="brand">Smart Planner</div>
        <div class="nav">
            <a href="index.php">Beranda</a>
            <?php if (currentUser()): ?>
                <a href="dashboard.php">Dashboard</a>
                <a href="logout.php">Keluar</a>
            <?php else: ?>
                <a href="login.php">Login</a>
                <a href="register.php">Daftar</a>
            <?php endif; ?>
        </div>
    </div>

    <?php foreach (getFlashes() as $flash): ?>
        <div class="flash <?= htmlspecialchars($flash['type']) ?>"><?= htmlspecialchars($flash['message']) ?></div>
    <?php endforeach; ?>
