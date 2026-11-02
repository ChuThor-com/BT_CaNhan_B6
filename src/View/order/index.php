<section>
    <h1>Đơn hàng của tôi</h1>
    <p class="muted">
        Bài 7 - Ownership: repository luôn lọc theo user_id lấy từ session.
    </p>

    <table>
        <thead>
        <tr>
            <th>ID</th>
            <th>Ngày tạo</th>
            <th>Trạng thái</th>
            <th>Tổng tiền</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($orders as $order): ?>
            <tr>
                <td><?= (int) $order['id'] ?></td>
                <td><?= e($order['created_at']) ?></td>
                <td><?= e($order['status']) ?></td>
                <td><?= number_format((float) $order['total_amount'], 0, ',', '.') ?> đ</td>
                <td>
                    <a href="<?= e(url('my-orders/show', ['id' => $order['id']])) ?>">Chi tiết</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</section>
