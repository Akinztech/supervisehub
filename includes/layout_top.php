<?php $user = current_user(); $flashes = pull_flashes(); ?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($pageTitle ?? $config['name']) ?> | <?= e($config['name']) ?></title>
<link rel="stylesheet" href="<?= e(url('assets/css/app.css')) ?>">
</head>
<body class="<?= $user ? 'app-shell' : 'public-shell' ?>">
<?php if ($user): ?>
<aside class="sidebar" id="sidebar">
  <div class="brand"><span class="brand-mark">SH</span><div><strong><?= e($config['name']) ?></strong><small>Project Supervision</small></div></div>
  <div class="profile-mini"><div class="avatar"><?= e(strtoupper(substr($user['full_name'],0,1))) ?></div><div><strong><?= e($user['full_name']) ?></strong><small><?= e(ucfirst($user['role'])) ?></small></div></div>
  <?php include __DIR__ . '/sidebar.php'; ?>
  <a class="logout-link" href="<?= e(url('index.php?page=logout')) ?>">Sign out</a>
</aside>
<main class="main-panel">
<header class="topbar"><button class="menu-button" type="button" onclick="document.getElementById('sidebar').classList.toggle('open')">☰</button><div><h1><?= e($pageTitle ?? 'Dashboard') ?></h1><p><?= e($pageSubtitle ?? $config['department']) ?></p></div><div class="topbar-actions"><span class="date-chip"><?= date('D, d M Y') ?></span></div></header>
<div class="content">
<?php else: ?>
<header class="public-nav"><a class="public-brand" href="<?= e(url()) ?>"><span class="brand-mark">SH</span><div><strong><?= e($config['name']) ?></strong><small><?= e($config['institution']) ?></small></div></a><nav><a href="<?= e(url()) ?>">Home</a><a href="<?= e(url('index.php?page=login')) ?>">Login</a><a class="button small" href="<?= e(url('index.php?page=register')) ?>">Create account</a></nav></header>
<main>
<?php endif; ?>
<?php foreach ($flashes as $f): ?><div class="flash <?= e($f['type']) ?>"><?= e($f['message']) ?></div><?php endforeach; ?>
