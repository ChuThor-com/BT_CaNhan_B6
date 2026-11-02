<?php
declare(strict_types=1);

namespace App\Controller;

use App\Core\Auth;
use App\Core\Controller;
use App\Repository\CategoryRepository;

final class AdminCategoryController extends Controller
{
    public function __construct(private CategoryRepository $categories)
    {
    }

    public function index(): void
    {
        Auth::requireRole('admin'); // Chỉ admin mới được vào
        $this->render('admin/category/index', [
            'categories' => $this->categories->all(),
        ]);
    }

    public function createForm(): void
    {
        Auth::requireRole('admin');
        $this->render('admin/category/form', ['category' => null]);
    }

    public function store(): void
    {
        Auth::requireRole('admin');
        Auth::verifyCsrf($_POST['csrf'] ?? null);

        $name = trim($_POST['name'] ?? '');
        $status = $_POST['status'] ?? 'active';

        if ($name !== '') {
            $this->categories->create($name, $status);
            flash('success', 'Đã thêm danh mục.');
        }
        $this->redirect('admin/categories');
    }

    public function editForm(): void
    {
        Auth::requireRole('admin');
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        
        if (!$id || !($category = $this->categories->find($id))) {
            http_response_code(404); exit('Không tìm thấy danh mục.');
        }

        $this->render('admin/category/form', ['category' => $category]);
    }

    public function update(): void
    {
        Auth::requireRole('admin');
        Auth::verifyCsrf($_POST['csrf'] ?? null);
        
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $name = trim($_POST['name'] ?? '');
        $status = $_POST['status'] ?? 'active';

        if ($id && $name !== '') {
            $this->categories->update($id, $name, $status);
            flash('success', 'Đã cập nhật danh mục.');
        }
        $this->redirect('admin/categories');
    }

    public function delete(): void
    {
        Auth::requireRole('admin');
        Auth::verifyCsrf($_POST['csrf'] ?? null);
        
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        if ($id) {
            // (Lưu ý: Nếu danh mục đang có sản phẩm, SQL sẽ báo lỗi khóa ngoại. Bạn có thể bắt lỗi ở đây)
            $this->categories->delete($id);
            flash('success', 'Đã xóa danh mục.');
        }
        $this->redirect('admin/categories');
    }
}