<?php
declare(strict_types=1);

namespace App\Repository;

use PDO;

final class UserRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    public function all(): array
    {
        $stmt = $this->pdo->query(
            'SELECT id, email, full_name, role, status
             FROM users
             ORDER BY id ASC'
        );

        return $stmt->fetchAll();
    }

    public function findActiveByEmail(string $email): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, email, password_hash, full_name, role, status
             FROM users
             WHERE email = :email
               AND status = "active"
             LIMIT 1'
        );

        $stmt->execute(['email' => $email]);
        return $stmt->fetch() ?: null;
    }

    public function emailExists(string $email): bool
    {
        $stmt = $this->pdo->prepare(
            'SELECT 1
             FROM users
             WHERE email = :email
             LIMIT 1'
        );

        $stmt->execute(['email' => $email]);
        return (bool) $stmt->fetchColumn();
    }

    public function create(
        string $email,
        string $passwordHash,
        string $fullName,
        string $role,
        string $status
    ): int {
        $stmt = $this->pdo->prepare(
            'INSERT INTO users(email, password_hash, full_name, role, status)
             VALUES (:email, :password_hash, :full_name, :role, :status)'
        );

        $stmt->execute([
            'email' => $email,
            'password_hash' => $passwordHash,
            'full_name' => $fullName,
            'role' => $role,
            'status' => $status,
        ]);

        return (int) $this->pdo->lastInsertId();
    }
}
