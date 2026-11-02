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
}