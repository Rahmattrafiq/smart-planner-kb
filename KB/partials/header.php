<?php $authUser = currentUser(); ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= APP_NAME ?></title>
    <link rel="stylesheet" href="static/css/style.css">
</head>
<body>
<div class="app-shell">
    <aside class="sidebar">
        <div class="brand">
            <div class="brand-icon" aria-hidden>
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none">
                    <path d="M12 2L2 7l10 5 10-5-10-5z" stroke="white" stroke-width="1.6" stroke-linejoin="round"/>
                    <path d="M2 17l10 5 10-5M2 12l10 5 10-5" stroke="white" stroke-width="1.6" stroke-linejoin="round"/>
                </svg>
            </div>
            <div class="brand-text">
                <div class="brand-title">Smart Planner</div>
                <div class="brand-sub">Dream Career</div>
            </div>
        </div>

        <nav class="nav">
            <a href="index.php" class="nav-item <?= basename($_SERVER['PHP_SELF']) === 'index.php' ? 'active' : '' ?>">
                <span class="nav-ic">⌂</span> Beranda
            </a>
            <?php if ($authUser): ?>
                <a href="dashboard.php" class="nav-item <?= basename($_SERVER['PHP_SELF']) === 'dashboard.php' ? 'active' : '' ?>">
                    <span class="nav-ic">➕</span> Roadmap Saya
                </a>
                <a href="new_roadmap.php" class="nav-item <?= basename($_SERVER['PHP_SELF']) === 'new_roadmap.php' ? 'active' : '' ?>">
                    <span class="nav-ic">✦</span> Buat Roadmap
                </a>
            <?php endif; ?>
            <?php if (!$authUser): ?>
                <a href="login.php" class="nav-item <?= basename($_SERVER['PHP_SELF']) === 'login.php' ? 'active' : '' ?>">
                    <span class="nav-ic">🔐</span> Masuk
                </a>
                <a href="register.php" class="nav-item <?= basename($_SERVER['PHP_SELF']) === 'register.php' ? 'active' : '' ?>">
                    <span class="nav-ic">📝</span> Daftar
                </a>
            <?php endif; ?>
        </nav>

        <div class="sidebar-footer">
            <?php if ($authUser): ?>
                <div class="profile-pill">Halo, <?= htmlspecialchars($authUser['nama']) ?></div>
                <a href="logout.php" class="nav-item nav-item-muted">
                    <span class="nav-ic">↩</span> Keluar
                </a>
            <?php else: ?>
                <a href="login.php" class="nav-item nav-item-muted">
                    <span class="nav-ic">🔑</span> Masuk
                </a>
            <?php endif; ?>
        </div>
    </aside>

    <main class="content">
        <?php foreach (getFlashes() as $flash): ?>
            <div class="flash <?= htmlspecialchars($flash['type']) ?>"><?= htmlspecialchars($flash['message']) ?></div>
        <?php endforeach; ?>
