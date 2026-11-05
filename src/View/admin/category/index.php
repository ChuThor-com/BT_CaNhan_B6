<section>
    <div class="page-head">
        <h1>Quản trị Danh mục</h1>
        <a class="button" href="<?= e(url('admin/categories/create')) ?>">+ Thêm danh mục</a>
    </div>

    <div class="card live-panel" id="cat-live">
        <h2>Tìm nhanh (Fetch API)</h2>
        <p class="muted">Gọi <code>?route=api/categories</code> bằng fetch(), không tải lại trang.</p>

        <div class="live-controls">
            <label>
                Tên danh mục
                <input id="cat-q" type="search" maxlength="100" placeholder="Nhập tên danh mục" autocomplete="off">
            </label>
            <label>
                Trạng thái
                <select id="cat-status">
                    <option value="">-- Tất cả --</option>
                    <option value="active">active</option>
                    <option value="inactive">inactive</option>
                </select>
            </label>
        </div>

        <div id="cat-msg" class="live-msg muted" role="status" aria-live="polite"></div>
        <button type="button" id="cat-retry" class="secondary-btn" hidden>Thử lại</button>
        <div id="cat-results" aria-busy="false"></div>
    </div>

    <h2>Danh sách đầy đủ</h2>
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
<script src="assets/category-live.js" defer></script>
