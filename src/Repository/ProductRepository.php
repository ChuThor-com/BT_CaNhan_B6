<?php
declare(strict_types=1);

namespace App\Repository;

use PDO;

final class ProductRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    public function findAll(string $keyword = '', ?int $categoryId = null): array
    {
        $sql = '
            SELECT p.id, p.code, p.name, p.price, p.stock, p.status,
                   c.name AS category_name
            FROM products p
            JOIN categories c ON c.id = p.category_id
            WHERE (
                    :keyword_empty = 1
                    OR p.name LIKE :keyword_name
                    OR p.code LIKE :keyword_code
                  )
              AND (
                    :category_filter = 0
                    OR p.category_id = :category_id
                  )
            ORDER BY p.id DESC
        ';

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':keyword_empty', $keyword === '' ? 1 : 0, PDO::PARAM_INT);
        $stmt->bindValue(':keyword_name', '%' . $keyword . '%', PDO::PARAM_STR);
        $stmt->bindValue(':keyword_code', '%' . $keyword . '%', PDO::PARAM_STR);
        $stmt->bindValue(':category_filter', $categoryId === null ? 0 : 1, PDO::PARAM_INT);
        $stmt->bindValue(':category_id', $categoryId ?? 0, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function searchActive(string $keyword): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT p.id, p.code, p.name, p.price, p.stock,
                    c.name AS category_name
             FROM products p
             JOIN categories c ON c.id = p.category_id
             WHERE p.status = "active"
               AND c.status = "active"
               AND (p.name LIKE :keyword OR p.code LIKE :keyword)
             ORDER BY p.name
             LIMIT 10'
        );

        $stmt->execute(['keyword' => '%' . $keyword . '%']);
        return $stmt->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT p.*, c.name AS category_name
             FROM products p
             JOIN categories c ON c.id = p.category_id
             WHERE p.id = :id'
        );
        $stmt->execute(['id' => $id]);

        return $stmt->fetch() ?: null;
    }

    public function categories(): array
    {
        return $this->pdo
            ->query('SELECT id, name, status FROM categories ORDER BY name')
            ->fetchAll();
    }

    public function create(array $data): void
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO products(category_id, code, name, price, stock, status)
             VALUES (:category_id, :code, :name, :price, :stock, :status)'
        );

        $stmt->execute([
            'category_id' => $data['category_id'],
            'code' => $data['code'],
            'name' => $data['name'],
            'price' => $data['price'],
            'stock' => $data['stock'],
            'status' => $data['status'],
        ]);
    }

    public function update(int $id, array $data): void
    {
        $stmt = $this->pdo->prepare(
            'UPDATE products
             SET category_id = :category_id,
                 code = :code,
                 name = :name,
                 price = :price,
                 stock = :stock,
                 status = :status
             WHERE id = :id'
        );

        $stmt->execute([
            'id' => $id,
            'category_id' => $data['category_id'],
            'code' => $data['code'],
            'name' => $data['name'],
            'price' => $data['price'],
            'stock' => $data['stock'],
            'status' => $data['status'],
        ]);
    }

    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM products WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }
}
