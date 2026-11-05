<?php
declare(strict_types=1);

namespace App\Core;

final class Auth
{
    public static function check(): bool
    {
        return isset($_SESSION['user_id']);
    }

    public static function id(): ?int
    {
        return isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : null;
    }

    public static function role(): ?string
    {
        return $_SESSION['role'] ?? null;
    }

    public static function name(): ?string
    {
        return $_SESSION['full_name'] ?? null;
    }

    public static function login(array $user): void
    {
        session_regenerate_id(true);

        $_SESSION['user_id'] = (int) $user['id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['full_name'] = $user['full_name'];
    }

    public static function logout(): void
    {
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        session_destroy();
    }

    public static function requireLogin(): void
    {
        if (!self::check()) {
            // Nhớ URL (chỉ GET) để đăng nhập xong quay lại đúng trang đang vào.
            if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'GET') {
                $_SESSION['intended'] = $_SERVER['REQUEST_URI'] ?? null;
            }

            flash('error', 'Vui lòng đăng nhập.');
            header('Location: ' . url('login'));
            exit;
        }
    }

    public static function requireRole(string ...$roles): void
    {
        self::requireLogin();

        if (!in_array(self::role(), $roles, true)) {
            http_response_code(403);
            exit('403 - Bạn không có quyền truy cập chức năng này.');
        }
    }

    /**
     * Lấy (và xóa) URL nội bộ đã lưu trước khi bị chuyển sang trang đăng nhập.
     * Chỉ chấp nhận đường dẫn tương đối "/..." để tránh open redirect.
     */
    public static function pullIntendedUrl(): ?string
    {
        $url = $_SESSION['intended'] ?? null;
        unset($_SESSION['intended']);

        if (!is_string($url) || $url === '' || $url[0] !== '/' || str_starts_with($url, '//')
            || str_contains($url, "\r") || str_contains($url, "\n") || str_contains($url, '\\')) {
            return null;
        }

        return $url;
    }

    /**
     * Dành cho JSON endpoint: không redirect sang HTML mà trả 401/403 dạng JSON.
     */
    public static function requireRoleJson(string ...$roles): void
    {
        if (!self::check()) {
            self::jsonError(401, 'Bạn chưa đăng nhập hoặc phiên đã hết hạn.');
        }

        if (!in_array(self::role(), $roles, true)) {
            self::jsonError(403, 'Bạn không có quyền truy cập chức năng này.');
        }
    }

    private static function jsonError(int $status, string $message): never
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['ok' => false, 'message' => $message], JSON_UNESCAPED_UNICODE);
        exit;
    }

    public static function csrfToken(): string
    {
        if (empty($_SESSION['csrf'])) {
            $_SESSION['csrf'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['csrf'];
    }

    public static function verifyCsrf(?string $token): void
    {
        $sessionToken = $_SESSION['csrf'] ?? '';

        if (!$token || !$sessionToken || !hash_equals($sessionToken, $token)) {
            http_response_code(419);
            exit('419 - CSRF token không hợp lệ.');
        }
    }
}
