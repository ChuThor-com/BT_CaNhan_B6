<?php
declare(strict_types=1);

/**
 * Test truy cập URL khi CHƯA đăng nhập (không có cookie session).
 *
 * Chạy (WAMP đang bật, đã import SQL):
 *   php tests/unauth_access_test.php http://localhost/minishop_mvc_demo/public/index.php
 */
$base = rtrim($argv[1] ?? 'http://localhost/minishop_mvc_demo/public/index.php', '/');

function request(string $method, string $url): array
{
    $context = stream_context_create(['http' => [
        'method' => $method,
        'follow_location' => 0,   // không theo redirect để đọc được 302
        'ignore_errors' => true,  // vẫn đọc body/headers khi 4xx/5xx
        'header' => "Accept: application/json, text/html\r\nContent-Type: application/x-www-form-urlencoded\r\n",
        'content' => $method === 'POST' ? 'id=1' : '',
        'timeout' => 10,
    ]]);

    $body = @file_get_contents($url, false, $context);
    $headers = $http_response_header ?? [];

    if ($body === false && $headers === []) {
        fwrite(STDERR, "Không kết nối được tới $url\n");
        exit(2);
    }

    preg_match('#HTTP/\\S+\\s+(\\d{3})#', $headers[0] ?? '', $m);
    $location = '';
    $type = '';
    foreach ($headers as $h) {
        if (stripos($h, 'Location:') === 0) {
            $location = trim(substr($h, 9));
        }
        if (stripos($h, 'Content-Type:') === 0) {
            $type = trim(substr($h, 13));
        }
    }

    return ['status' => (int) ($m[1] ?? 0), 'location' => $location, 'type' => $type, 'body' => (string) $body];
}

// [id, method, route, status mong đợi, kiểu kiểm tra thêm]
$cases = [
    ['TC01', 'GET',  'admin/categories',        302, 'redirect-login'],
    ['TC02', 'GET',  'admin/categories/create', 302, 'redirect-login'],
    ['TC03', 'GET',  'admin/categories/edit&id=1', 302, 'redirect-login'],
    ['TC04', 'POST', 'admin/categories/delete', 302, 'redirect-login'],
    ['TC05', 'GET',  'admin/products',          302, 'redirect-login'],
    ['TC06', 'GET',  'admin/users',             302, 'redirect-login'],
    ['TC07', 'GET',  'my-orders',               302, 'redirect-login'],
    ['TC08', 'GET',  'api/categories',          401, 'json-error'],
    ['TC09', 'POST', 'logout',                  302, 'redirect-login'],
    ['TC10', 'GET',  'login',                   200, 'public'],
    ['TC11', 'GET',  'products',                200, 'public'],
    ['TC12', 'GET',  'khong-ton-tai',           404, 'public'],
];

$fail = 0;

foreach ($cases as [$id, $method, $route, $status, $kind]) {
    $res = request($method, $base . '?route=' . $route);
    $ok = $res['status'] === $status;

    if ($kind === 'redirect-login') {
        $ok = $ok && str_contains($res['location'], 'route=login');
    }
    if ($kind === 'json-error') {
        $json = json_decode($res['body'], true);
        $ok = $ok && str_contains($res['type'], 'application/json')
            && is_array($json) && ($json['ok'] ?? null) === false;
    }
    // Không được lộ dữ liệu quản trị trong body của trang bị chặn.
    if (in_array($kind, ['redirect-login', 'json-error'], true)) {
        $ok = $ok && !str_contains($res['body'], 'Quản trị Danh mục') && !str_contains($res['body'], 'password_hash');
    }

    echo ($ok ? '[PASS] ' : '[FAIL] '), "$id $method ?route=$route -> HTTP {$res['status']} (mong đợi $status)", PHP_EOL;
    if (!$ok) {
        $fail++;
    }
}

echo $fail === 0 ? "\nTất cả đạt.\n" : "\n$fail test lỗi.\n";
exit($fail === 0 ? 0 : 1);
