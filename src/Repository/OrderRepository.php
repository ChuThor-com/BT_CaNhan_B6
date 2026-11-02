<?php
declare(strict_types=1);

namespace App\Repository;

use PDO;

final class OrderRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    public function findByUser(int $userId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, total_amount, status, created_at
             FROM orders
             WHERE user_id = :user_id
             ORDER BY created_at DESC'
        );

        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll();
    }

    public function findOwned(int $orderId, int $userId): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, user_id, total_amount, status, created_at
             FROM orders
             WHERE id = :id
               AND user_id = :user_id'
        );

        $stmt->execute([
            'id' => $orderId,
            'user_id' => $userId,
        ]);

        return $stmt->fetch() ?: null;
    }

    public function items(int $orderId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT oi.quantity, oi.unit_price,
                    oi.quantity * oi.unit_price AS line_total,
                    p.code, p.name
             FROM order_items oi
             JOIN products p ON p.id = oi.product_id
             WHERE oi.order_id = :order_id
             ORDER BY oi.id'
        );

        $stmt->execute(['order_id' => $orderId]);
        return $stmt->fetchAll();
    }
}
