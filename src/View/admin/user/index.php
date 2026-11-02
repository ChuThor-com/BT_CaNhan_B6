<section>
    <div class="page-head">
        <div>
            <h1>Quản trị người dùng</h1>
            <p class="muted">Danh sách không hiển thị password_hash. Có thể mở phpMyAdmin để quan sát giá trị hash trong bảng users.</p>
        </div>

        <a class="button" href="<?= e(url('admin/users/create')) ?>">+ Thêm người dùng</a>
    </div>

    <table>
        <thead>
        <tr>
            <th>ID</th>
            <th>Họ tên</th>
            <th>Email</th>
            <th>Role</th>
            <th>Trạng thái</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($users as $user): ?>
            <tr>
                <td><?= (int) $user['id'] ?></td>
                <td><?= e($user['full_name']) ?></td>
                <td><?= e($user['email']) ?></td>
                <td><?= e($user['role']) ?></td>
                <td><?= e($user['status']) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</section>
