<section>
    <div class="page-head">
        <h1>Quản trị Danh mục</h1>
        <a class="button" href="<?= e(url('admin/categories/create')) ?>">+ Thêm danh mục</a>
    </div>

    <table>
        <thead>
        <tr>
            <th>ID</th>
            <th>Tên danh mục</th>
            <th>Trạng thái</th>
            <th>Thao tác</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($categories as $cat): ?>
            <tr>
                <td><?= (int) $cat['id'] ?></td>
                <td><?= e($cat['name']) ?></td>
                <td><?= e($cat['status']) ?></td>
                <td>
                    <a href="<?= e(url('admin/categories/edit', ['id' => $cat['id']])) ?>">Sửa</a>
                    <form class="inline" method="post" action="<?= e(url('admin/categories/delete')) ?>" onsubmit="return confirm('Xóa danh mục này?')">
                        <?= csrf_field() ?>
                        <input type="hidden" name="id" value="<?= (int) $cat['id'] ?>">
                        <button type="submit" class="link-danger">Xóa</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</section>