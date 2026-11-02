<?php
declare(strict_types=1);

use App\Core\Auth;

function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function url(string $route = 'products', array $params = []): string
{
    $query = array_merge(['route' => $route], $params);
    return 'index.php?' . http_build_query($query);
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf" value="' . e(Auth::csrfToken()) . '">';
}

function flash(string $key, ?string $message = null): ?string
{
    if ($message !== null) {
        $_SESSION['_flash'][$key] = $message;
        return null;
    }

    $value = $_SESSION['_flash'][$key] ?? null;
    unset($_SESSION['_flash'][$key]);
    return $value;
}
