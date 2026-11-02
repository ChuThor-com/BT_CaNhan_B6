<section class="card narrow">
    <h1>Thêm người dùng</h1>

    <p class="hint">
        Mật khẩu người dùng nhập vào chỉ tồn tại trong request hiện tại.
        Server dùng <code>password_hash()</code> trước khi INSERT vào CSDL.
    </p>

    <form method="post" action="<?= e(url('admin/users/store')) ?>">
        <?= csrf_field() ?>

        <label>
            Họ tên
            <input type="text" name="full_name" required maxlength="150">
        </label>

        <label>
            Email
            <input type="email" name="email" required maxlength="150">
        </label>

        <label>
            Mật khẩu
            <input type="password" name="password" required minlength="8" autocomplete="new-password">
        </label>

        <label>
            Role
            <select name="role">
                <option value="customer">customer</option>
                <option value="admin">admin</option>
            </select>
        </label>

        <label>
            Trạng thái
            <select name="status">
                <option value="active">active</option>
                <option value="locked">locked</option>
            </select>
        </label>

        <button type="submit">Tạo người dùng</button>
        <a class="button secondary" href="<?= e(url('admin/users')) ?>">Hủy</a>
    </form>
</section>
