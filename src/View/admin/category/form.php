<?php $isEdit = is_array($category); ?>
<section class="card">
    <h1><?= $isEdit ? 'Sửa danh mục' : 'Thêm danh mục' ?></h1>

    <form method="post" action="<?= e(url($isEdit ? 'admin/categories/update' : 'admin/categories/store')) ?>">
        <?= csrf_field() ?>
        
        <?php if ($isEdit): ?>
            <input type="hidden" name="id" value="<?= (int) $category['id'] ?>">
        <?php endif; ?>

        <label>
            Tên danh mục
            <input type="text" name="name" required value="<?= $isEdit ? e($category['name']) : '' ?>">
        </label>

        <label>
            Trạng thái
            <select name="status">
                <option value="active" <?= $isEdit && $category['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                <option value="inactive" <?= $isEdit && $category['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option>
            </select>
        </label>

        <button type="submit">Lưu</button>
        <a class="button secondary" href="<?= e(url('admin/categories')) ?>">Hủy</a>
    </form>
</section>