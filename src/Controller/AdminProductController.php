<?php
declare(strict_types=1);

namespace App\Controller;

use App\Core\Auth;
use App\Core\Controller;
use App\Repository\ProductRepository;

final class AdminProductController extends Controller
{
    public function __construct(private ProductRepository $products)
    {
    }

    public function index(): void
    {
        Auth::requireRole('admin');

        $this->render('admin/product/index', [
            'products' => $this->products->findAll(),
        ]);
    }

    public function createForm(): void
    {
        Auth::requireRole('admin');

        $this->render('admin/product/form', [
            'product' => null,
            'categories' => $this->products->categories(),
            'action' => 'admin/products/store',
        ]);
    }

    public function store(): void
    {
        Auth::requireRole('admin');
        Auth::verifyCsrf($_POST['csrf'] ?? null);

        $data = $this->validatedData();

        if ($data === null) {
            $this->redirect('admin/products/create');
        }

        $this->products->create($data);
        flash('success', 'Đã thêm sản phẩm.');
        $this->redirect('admin/products');
    }

    public function editForm(): void
    {
        Auth::requireRole('admin');

        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        if (!$id || !($product = $this->products->find($id))) {
            http_response_code(404);
            exit('Không tìm thấy sản phẩm.');
        }

        $this->render('admin/product/form', [
            'product' => $product,
            'categories' => $this->products->categories(),
            'action' => 'admin/products/update',
        ]);
    }

    public function update(): void
    {
        Auth::requireRole('admin');
        Auth::verifyCsrf($_POST['csrf'] ?? null);

        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $data = $this->validatedData();

        if (!$id || $data === null) {
            $this->redirect('admin/products');
        }

        $this->products->update($id, $data);
        flash('success', 'Đã cập nhật sản phẩm.');
        $this->redirect('admin/products');
    }

    public function delete(): void
    {
        Auth::requireRole('admin');
        Auth::verifyCsrf($_POST['csrf'] ?? null);

        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

        if (!$id) {
            http_response_code(400);
            exit('ID không hợp lệ.');
        }

        $this->products->delete($id);
        flash('success', 'Đã xóa sản phẩm.');
        $this->redirect('admin/products');
    }

    private function validatedData(): ?array
    {
        $categoryId = filter_input(INPUT_POST, 'category_id', FILTER_VALIDATE_INT);
        $code = strtoupper(trim($_POST['code'] ?? ''));
        $name = trim($_POST['name'] ?? '');
        $price = filter_input(INPUT_POST, 'price', FILTER_VALIDATE_FLOAT);
        $stock = filter_input(INPUT_POST, 'stock', FILTER_VALIDATE_INT);
        $status = $_POST['status'] ?? 'active';

        $errors = [];

        if (!$categoryId) {
            $errors[] = 'Danh mục không hợp lệ.';
        }

        if ($code === '') {
            $errors[] = 'Mã sản phẩm không được rỗng.';
        }

        if ($name === '') {
            $errors[] = 'Tên sản phẩm không được rỗng.';
        }

        if ($price === false || $price < 0) {
            $errors[] = 'Giá phải >= 0.';
        }

        if ($stock === false || $stock < 0) {
            $errors[] = 'Tồn kho phải >= 0.';
        }

        if (!in_array($status, ['active', 'inactive'], true)) {
            $errors[] = 'Trạng thái không hợp lệ.';
        }

        if ($errors) {
            flash('error', implode(' ', $errors));
            return null;
        }

        return [
            'category_id' => $categoryId,
            'code' => $code,
            'name' => $name,
            'price' => $price,
            'stock' => $stock,
            'status' => $status,
        ];
    }
}
