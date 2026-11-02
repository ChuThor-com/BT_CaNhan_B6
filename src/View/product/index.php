<section>
    <h1>Danh sách sản phẩm</h1>
    <p class="muted">
        Demo MVC: Controller nhận request → Repository truy vấn MySQL → View hiển thị HTML.
    </p>

    <form method="get" action="index.php" class="filters">
        <input type="hidden" name="route" value="products">

        <label>
            Tìm kiếm
            <input
                id="live-search"
                type="search"
                name="q"
                value="<?= e($keyword) ?>"
                placeholder="Nhập tên hoặc mã sản phẩm"
                autocomplete="off"
            >
        </label>

        <label>
            Danh mục
            <select name="category_id">
                <option value="">-- Tất cả --</option>
                <?php foreach ($categories as $category): ?>
                    <option
                        value="<?= (int) $category['id'] ?>"
                        <?= $categoryId === (int) $category['id'] ? 'selected' : '' ?>
                    >
                        <?= e($category['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>

        <button type="submit">Lọc</button>
    </form>

    <p class="hint">
        Bài 8: gõ vào ô tìm kiếm để gọi JSON API bằng Fetch mà không tải lại trang.
    </p>

    <div id="live-status" class="muted"></div>
    <div id="live-results"></div>

    <table>
        <thead>
        <tr>
            <th>ID</th>
            <th>Mã</th>
            <th>Tên</th>
            <th>Danh mục</th>
            <th>Giá</th>
            <th>Tồn kho</th>
            <th>Trạng thái</th>
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
                <td><?= e($product['status']) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</section>
