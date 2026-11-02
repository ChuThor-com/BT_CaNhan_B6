<section class="card narrow">
    <h1>Đăng nhập</h1>

    <form method="post" action="<?= e(url('login')) ?>">
        <?= csrf_field() ?>

        <label>
            Email
            <input type="email" name="email" required autocomplete="username">
        </label>

        <label>
            Mật khẩu
            <input type="password" name="password" required autocomplete="current-password">
        </label>

        <button type="submit">Đăng nhập</button>
    </form>

    <hr>

    <p><strong>Tài khoản demo</strong></p>
    <p>Admin: admin@example.com / Admin@123</p>
    <p>Customer: customer@example.com / Customer@123</p>
</section>
