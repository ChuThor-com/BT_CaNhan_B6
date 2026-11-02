<?php
use App\Core\Auth;

$success = flash('success');
$error = flash('error');
?>
<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MiniShop MVC Demo</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<header class="topbar">
    <strong>MiniShop MVC Demo</strong>
    <nav>
        <a href="<?= e(url('products')) ?>">Sản phẩm</a>

        <?php if (Auth::role() === 'customer'): ?>
            <a href="<?= e(url('my-orders')) ?>">Đơn của tôi</a>
        <?php endif; ?>

        <?php if (Auth::role() === 'admin'): ?>
            <a href="<?= e(url('admin/products')) ?>">Quản trị sản phẩm</a>
            <a href="<?= e(url('admin/categories')) ?>">Quản lý danh mục</a> 
            <a href="<?= e(url('admin/users')) ?>">Người dùng</a>
           
        <?php endif; ?>

        <?php if (Auth::check()): ?>
            <span>Xin chào, <?= e(Auth::name()) ?> (<?= e(Auth::role()) ?>)</span>
            <form class="inline" method="post" action="<?= e(url('logout')) ?>">
                <?= csrf_field() ?>
                <button type="submit">Đăng xuất</button>
            </form>
        <?php else: ?>
            <a href="<?= e(url('login')) ?>">Đăng nhập</a>
        <?php endif; ?>
    </nav>
</header>

<main class="container">
    <?php if ($success): ?>
        <div class="alert success"><?= e($success) ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert error"><?= e($error) ?></div>
    <?php endif; ?>
