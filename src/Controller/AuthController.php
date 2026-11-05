<?php
declare(strict_types=1);

namespace App\Controller;

use App\Core\Auth;
use App\Core\Controller;
use App\Repository\UserRepository;

final class AuthController extends Controller
{
    // Hash giả (của chuỗi ngẫu nhiên) để password_verify() vẫn chạy khi email không tồn tại,
    // giúp thời gian phản hồi gần như nhau -> khó dò email có tồn tại hay không.
    private const DUMMY_HASH = '$2y$12$JOH5EDD7hP.5MddArAEaOegGIPa21GLj6udmHEst6bHFztUKSDo0K';

    public function __construct(private UserRepository $users)
    {
    }

    public function form(): void
    {
        if (Auth::check()) {
            $this->redirect('products');
        }

        $this->render('auth/login');
    }

    public function login(): void
    {
        Auth::verifyCsrf($_POST['csrf'] ?? null);

        $email = strtolower(trim((string) ($_POST['email'] ?? '')));
        $password = (string) ($_POST['password'] ?? '');

        if ($email === '' || $password === '') {
            flash('error', 'Vui lòng nhập email và mật khẩu.');
            $this->redirect('login');
        }

        $user = $this->users->findActiveByEmail($email);

        // Luôn gọi password_verify() (kể cả khi không có user) và dùng cùng một thông báo lỗi.
        $hash = $user['password_hash'] ?? self::DUMMY_HASH;
        $passwordOk = password_verify($password, $hash);

        if (!$user || !$passwordOk) {
            flash('error', 'Email hoặc mật khẩu không đúng.');
            $this->redirect('login');
        }

        // Nếu thuật toán/cost mặc định của PHP đã đổi thì băm lại mật khẩu.
        if (password_needs_rehash($user['password_hash'], PASSWORD_DEFAULT)) {
            $this->users->updatePasswordHash((int) $user['id'], password_hash($password, PASSWORD_DEFAULT));
        }

        $intended = Auth::pullIntendedUrl();
        Auth::login($user); // session_regenerate_id(true) nằm trong hàm này
        flash('success', 'Đăng nhập thành công.');

        if ($intended !== null) {
            header('Location: ' . $intended);
            exit;
        }

        $this->redirect('products');
    }

    public function logout(): void
    {
        Auth::requireLogin();
        Auth::verifyCsrf($_POST['csrf'] ?? null);

        Auth::logout();
        session_start();
        session_regenerate_id(true);
        flash('success', 'Đã đăng xuất.');
        $this->redirect('login');
    }
}
