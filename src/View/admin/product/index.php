<section>
    <div class="page-head">
        <div>
            <h1>Quản trị sản phẩm</h1>
            <p class="muted">Route này được bảo vệ bằng role = admin ở server.</p>
        </div>

        <a class="button" href="<?= e(url('admin/products/create')) ?>">+ Thêm sản phẩm</a>
    </div>

    <table>
        <thead>
        <tr>
            <th>ID</th>
            <th>Mã</th>
            <th>Tên</th>
            <th>Danh mục</th>
            <th>Giá</th>
            <th>Tồn kho</th>
            <th>Thao tác</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($products as $product): ?>
            <tr>
                <td><?= (int) $product['id'] ?></td>
                <td><?= e($product['code']) ?></td>
                <td><?= e($product['name']) ?></td>
                <td><?= e($product['category_name']) ?></td>
                <td><?= number_format((float) $product['price'], 0, ',', '.') ?> đ</td>
                <td><?= (int) $product['stock'] ?></td>
                <td>
                    <a href="<?= e(url('admin/products/edit', ['id' => $product['id']])) ?>">Sửa</a>

                    <form
                        class="inline"
                        method="post"
                        action="<?= e(url('admin/products/delete')) ?>"
                        onsubmit="return confirm('Xóa sản phẩm này?')"
                    >
                        <?= csrf_field() ?>
                        <input type="hidden" name="id" value="<?= (int) $product['id'] ?>">
                        <button type="submit" class="link-danger">Xóa</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</section>
