<?php
declare(strict_types=1);

namespace App\Repository;

use PDO;

final class CategoryRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    public function all(): array
    {
        return $this->pdo->query('SELECT id, name, status FROM categories ORDER BY id DESC')->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT id, name, status FROM categories WHERE id = :id');
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function create(string $name, string $status): void
    {
        $stmt = $this->pdo->prepare('INSERT INTO categories(name, status) VALUES(:name, :status)');
        $stmt->execute(['name' => $name, 'status' => $status]);
    }

    public function update(int $id, string $name, string $status): void
    {
        $stmt = $this->pdo->prepare('UPDATE categories SET name = :name, status = :status WHERE id = :id');
        $stmt->execute(['id' => $id, 'name' => $name, 'status' => $status]);
    }

    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM categories WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }

    /**
     * Dùng cho JSON endpoint: tìm theo tên, lọc theo trạng thái, kèm số sản phẩm.
     */
    public function search(string $keyword = '', string $status = ''): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT c.id, c.name, c.status, COUNT(p.id) AS product_count
             FROM categories c
             LEFT JOIN products p ON p.category_id = c.id
             WHERE (:keyword_empty = 1 OR c.name LIKE :keyword)
               AND (:status_empty = 1 OR c.status = :status)
             GROUP BY c.id, c.name, c.status
             ORDER BY c.id DESC
             LIMIT 50'
        );

        $stmt->bindValue(':keyword_empty', $keyword === '' ? 1 : 0, PDO::PARAM_INT);
        $stmt->bindValue(':keyword', '%' . $keyword . '%', PDO::PARAM_STR);
        $stmt->bindValue(':status_empty', $status === '' ? 1 : 0, PDO::PARAM_INT);
        $stmt->bindValue(':status', $status, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchAll();
    }
}
