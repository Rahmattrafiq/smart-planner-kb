<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= APP_NAME ?></title>
    <style>
        :root{--blue:#0b4ad3;--nav-bg:#072047;--muted:#6b7594}
        *{box-sizing:border-box}
        body{font-family:Inter, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial; margin:0; background:#f6f8fb; color:#0f1724}
        .app{display:flex; min-height:100vh}
        .sidebar{width:220px; background:var(--nav-bg); color:#fff; padding:24px 16px; display:flex; flex-direction:column; gap:18px}
        .brand{font-weight:800; font-size:18px; display:flex; align-items:center; gap:10px}
        .brand .logo{width:36px; height:36px; background:linear-gradient(135deg,var(--blue),#3b82f6); border-radius:8px}
        .navlinks{display:flex; flex-direction:column; gap:6px; margin-top:8px}
        .navlinks a{color:rgba(255,255,255,0.9); text-decoration:none; padding:10px 12px; border-radius:8px; font-weight:600}
        .navlinks a.active, .navlinks a:hover{background:rgba(255,255,255,0.06)}
        .side-footer{margin-top:auto; font-size:13px; color:rgba(255,255,255,0.7)}
        .main{flex:1; padding:28px}
        .container{max-width:1100px; margin:0 auto}
        .hero-card, .form-card, .info-card, .roadmap-card{background:#fff;border-radius:14px;padding:22px;box-shadow:0 10px 30px rgba(15,23,36,0.06)}
        .hero-card{display:flex;align-items:center;justify-content:space-between;gap:18px;margin-bottom:20px}
        .eyebrow{text-transform:uppercase;color:var(--blue);font-size:12px;font-weight:800}
        h1{margin:6px 0 6px;font-size:28px}
        .card-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:16px}
        .btn{display:inline-block;padding:10px 16px;border-radius:10px;text-decoration:none;font-weight:700}
        .btn-primary{background:var(--blue);color:#fff}
        .btn-secondary{background:#eef4ff;color:var(--blue)}
        label{display:block;margin:8px 0 6px;font-weight:700}
        input, select, textarea{width:100%;padding:10px;border-radius:8px;border:1px solid #e6eefb;margin-bottom:8px}
        .muted{color:var(--muted);font-size:14px}
        .flash{padding:10px 12px;border-radius:10px;margin-bottom:12px;font-weight:700}
        .flash.success{background:#e8f8ee;color:#166534}
        .flash.error{background:#fdecea;color:#b42318}
        .roadmap-list{display:grid;gap:12px}
        .roadmap-item{border:1px solid #eef2ff;border-radius:12px;padding:14px}
        @media (max-width:900px){.sidebar{display:none}.card-grid{grid-template-columns:1fr}}
    </style>
</head>
<body>
<div class="app">
    <aside class="sidebar">
        <div class="brand">
            <div class="logo" aria-hidden></div>
            <div>
                <div>Smart Planner</div>
                <div style="font-size:12px;opacity:.85">Dream Career</div>
            </div>
        </div>

        <nav class="navlinks">
            <a href="index.php" class="<?= basename($_SERVER['PHP_SELF']) === 'index.php' ? 'active' : '' ?>">Beranda</a>
            <a href="dashboard.php" class="<?= basename($_SERVER['PHP_SELF']) === 'dashboard.php' ? 'active' : '' ?>">Roadmap Saya</a>
            <a href="profile.php" class="<?= basename($_SERVER['PHP_SELF']) === 'profile.php' ? 'active' : '' ?>">Profil Saya</a>
            <a href="panduan.php" class="<?= basename($_SERVER['PHP_SELF']) === 'panduan.php' ? 'active' : '' ?>">Panduan</a>
            <a href="about.php" class="<?= basename($_SERVER['PHP_SELF']) === 'about.php' ? 'active' : '' ?>">Tentang</a>
        </nav>

        <div class="side-footer">
            <?php if (currentUser()): ?>
                <div><?= htmlspecialchars(currentUser()['nama']) ?></div>
                <div><a href="logout.php" style="color:inherit;text-decoration:none">Keluar</a></div>
            <?php else: ?>
                <div><a href="login.php" style="color:inherit;text-decoration:none">Login</a> • <a href="register.php" style="color:inherit;text-decoration:none">Daftar</a></div>
            <?php endif; ?>
        </div>
    </aside>

    <main class="main">
        <div class="container">
            <?php foreach (getFlashes() as $flash): ?>
                <div class="flash <?= htmlspecialchars($flash['type']) ?>"><?= htmlspecialchars($flash['message']) ?></div>
            <?php endforeach; ?>
