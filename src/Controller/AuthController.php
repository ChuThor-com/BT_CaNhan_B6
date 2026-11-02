<?php
declare(strict_types=1);

namespace App\Controller;

use App\Core\Auth;
use App\Core\Controller;
use App\Repository\UserRepository;

final class AuthController extends Controller
{
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

        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        $user = $this->users->findActiveByEmail($email);

        if (!$user || !password_verify($password, $user['password_hash'])) {
            flash('error', 'Email hoặc mật khẩu không đúng.');
            $this->redirect('login');
        }

        Auth::login($user);
        flash('success', 'Đăng nhập thành công.');
        $this->redirect('products');
    }

    public function logout(): void
    {
        Auth::requireLogin();
        Auth::verifyCsrf($_POST['csrf'] ?? null);

        Auth::logout();
        session_start();
        flash('success', 'Đã đăng xuất.');
        $this->redirect('login');
    }
}
