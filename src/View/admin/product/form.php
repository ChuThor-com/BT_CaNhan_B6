<?php
$isEdit = is_array($product);
?>
<section class="card">
    <h1><?= $isEdit ? 'Sửa sản phẩm' : 'Thêm sản phẩm' ?></h1>

    <form method="post" action="<?= e(url($action)) ?>">
        <?= csrf_field() ?>

        <?php if ($isEdit): ?>
            <input type="hidden" name="id" value="<?= (int) $product['id'] ?>">
        <?php endif; ?>

        <label>
            Danh mục
            <select name="category_id" required>
                <?php foreach ($categories as $category): ?>
                    <option
                        value="<?= (int) $category['id'] ?>"
                        <?= $isEdit && (int) $product['category_id'] === (int) $category['id'] ? 'selected' : '' ?>
                    >
                        <?= e($category['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>

        <label>
            Mã sản phẩm
            <input
                type="text"
                name="code"
                required
                value="<?= $isEdit ? e($product['code']) : '' ?>"
            >
        </label>

        <label>
            Tên sản phẩm
            <input
                type="text"
                name="name"
                required
                value="<?= $isEdit ? e($product['name']) : '' ?>"
            >
        </label>

        <label>
            Giá
            <input
                type="number"
                name="price"
                min="0"
                step="1000"
                required
                value="<?= $isEdit ? e((string) $product['price']) : '0' ?>"
            >
        </label>

        <label>
            Tồn kho
            <input
                type="number"
                name="stock"
                min="0"
                required
                value="<?= $isEdit ? (int) $product['stock'] : 0 ?>"
            >
        </label>

        <label>
            Trạng thái
            <select name="status">
                <option value="active" <?= $isEdit && $product['status'] === 'active' ? 'selected' : '' ?>>
                    active
                </option>
                <option value="inactive" <?= $isEdit && $product['status'] === 'inactive' ? 'selected' : '' ?>>
                    inactive
                </option>
            </select>
        </label>

        <button type="submit">Lưu</button>
        <a class="button secondary" href="<?= e(url('admin/products')) ?>">Hủy</a>
    </form>
</section>
