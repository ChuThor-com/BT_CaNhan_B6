<?php
declare(strict_types=1);

// Tạo password_hash cho tài khoản mẫu để dán vào database/minishop.sql
// Dùng:  php tools/hash_password.php "Admin@123"
if (PHP_SAPI !== 'cli') {
    exit('Chỉ chạy bằng dòng lệnh.');
}

$password = $argv[1] ?? '';

if ($password === '') {
    fwrite(STDERR, "Cách dùng: php tools/hash_password.php \"MậtKhẩu\"\n");
    exit(1);
}

$hash = password_hash($password, PASSWORD_DEFAULT);
echo $hash, PHP_EOL;
echo 'Verify: ', password_verify($password, $hash) ? 'OK' : 'FAIL', PHP_EOL;
