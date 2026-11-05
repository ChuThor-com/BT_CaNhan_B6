<?php
declare(strict_types=1);

// Kiểm tra tài khoản mẫu trong database/minishop.sql dùng password_hash (không lưu mật khẩu rõ).
// Chạy:  php tests/seed_password_test.php
$sql = file_get_contents(dirname(__DIR__) . '/database/minishop.sql');

$expected = [
    'admin@example.com' => 'Admin@123',
    'customer@example.com' => 'Customer@123',
    'locked@example.com' => 'Customer@123',
];

$fail = 0;
$check = function (string $name, bool $ok) use (&$fail): void {
    echo ($ok ? '[PASS] ' : '[FAIL] '), $name, PHP_EOL;
    if (!$ok) {
        $fail++;
    }
};

foreach ($expected as $email => $plain) {
    $found = preg_match("/\\('" . preg_quote($email, '/') . "',\\s*'([^']+)'/", $sql, $m) === 1;
    $check("$email có trong file SQL", $found);
    if (!$found) {
        continue;
    }

    $hash = $m[1];
    $check("$email: cột password_hash không phải mật khẩu rõ", $hash !== $plain);
    $check("$email: là hash bcrypt (password_get_info)", (password_get_info($hash)['algoName'] ?? '') === 'bcrypt');
    $check("$email: password_verify đúng mật khẩu", password_verify($plain, $hash));
    $check("$email: password_verify sai mật khẩu -> false", !password_verify($plain . 'x', $hash));
}

echo $fail === 0 ? "\nTất cả đạt.\n" : "\n$fail kiểm tra lỗi.\n";
exit($fail === 0 ? 0 : 1);
