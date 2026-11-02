<section>
    <h1>Chi tiết đơn #<?= (int) $order['id'] ?></h1>
    <p>Trạng thái: <strong><?= e($order['status']) ?></strong></p>
    <p>Tạo lúc: <?= e($order['created_at']) ?></p>

    <table>
        <thead>
        <tr>
            <th>Mã SP</th>
            <th>Tên SP</th>
            <th>Số lượng</th>
            <th>Đơn giá</th>
            <th>Thành tiền</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($items as $item): ?>
            <tr>
                <td><?= e($item['code']) ?></td>
                <td><?= e($item['name']) ?></td>
                <td><?= (int) $item['quantity'] ?></td>
                <td><?= number_format((float) $item['unit_price'], 0, ',', '.') ?> đ</td>
                <td><?= number_format((float) $item['line_total'], 0, ',', '.') ?> đ</td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</section>
