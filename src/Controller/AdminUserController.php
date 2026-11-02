<?php
declare(strict_types=1);

namespace App\Controller;

use App\Core\Auth;
use App\Core\Controller;
use App\Repository\UserRepository;

final class AdminUserController extends Controller
{
    public function __construct(private UserRepository $users)
    {
    }

    public function index(): void
    {
        Auth::requireRole('admin');

        $this->render('admin/user/index', [
            'users' => $this->users->all(),
        ]);
    }

    public function createForm(): void
    {
        Auth::requireRole('admin');
        $this->render('admin/user/create');
    }

    public function store(): void
    {
        Auth::requireRole('admin');
        Auth::verifyCsrf($_POST['csrf'] ?? null);

        $fullName = trim($_POST['full_name'] ?? '');
        $email = strtolower(trim($_POST['email'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');
        $role = $_POST['role'] ?? 'customer';
        $status = $_POST['status'] ?? 'active';

        if ($fullName === '') {
            flash('error', 'Họ tên không được để trống.');
            $this->redirect('admin/users/create');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            flash('error', 'Email không hợp lệ.');
            $this->redirect('admin/users/create');
        }

        if (strlen($password) < 8) {
            flash('error', 'Mật khẩu phải có ít nhất 8 ký tự.');
            $this->redirect('admin/users/create');
        }

        if (!in_array($role, ['customer', 'admin'], true)) {
            flash('error', 'Role không hợp lệ.');
            $this->redirect('admin/users/create');
        }

        if (!in_array($status, ['active', 'locked'], true)) {
            flash('error', 'Trạng thái không hợp lệ.');
            $this->redirect('admin/users/create');
        }

        if ($this->users->emailExists($email)) {
            flash('error', 'Email đã tồn tại.');
            $this->redirect('admin/users/create');
        }

        // QUAN TRỌNG: không lưu mật khẩu rõ vào CSDL.
        // password_hash() tạo hash một chiều; khi đăng nhập dùng password_verify().
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        if ($passwordHash === false) {
            http_response_code(500);
            exit('Không thể tạo password hash.');
        }

        $this->users->create(
            $email,
            $passwordHash,
            $fullName,
            $role,
            $status
        );

        flash('success', 'Đã tạo người dùng. Mật khẩu được lưu dưới dạng password_hash.');
        $this->redirect('admin/users');
    }
}
