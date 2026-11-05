<?php
declare(strict_types=1);

namespace App\Controller;

use App\Core\Auth;
use App\Repository\CategoryRepository;

/**
 * GET ?route=api/categories&q=...&status=active|inactive
 *
 * 200 {ok:true, count, data:[{id,name,status,product_count}]}
 * 401 chưa đăng nhập | 403 không phải admin | 422 tham số sai | 500 lỗi server
 */
final class ApiCategoryController
{
    public function __construct(private CategoryRepository $categories)
    {
    }

    public function search(): void
    {
        // Phải đặt header JSON trước để mọi nhánh lỗi đều là JSON, không phải HTML.
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-store');

        Auth::requireRoleJson('admin');

        $keyword = trim((string) ($_GET['q'] ?? ''));
        $status = (string) ($_GET['status'] ?? '');

        if (mb_strlen($keyword) > 100) {
            $this->fail(422, 'Từ khóa tối đa 100 ký tự.');
        }

        if ($status !== '' && !in_array($status, ['active', 'inactive'], true)) {
            $this->fail(422, 'Trạng thái chỉ nhận active hoặc inactive.');
        }

        try {
            $data = $this->categories->search($keyword, $status);

            foreach ($data as &$row) {
                $row['id'] = (int) $row['id'];
                $row['product_count'] = (int) $row['product_count'];
            }
            unset($row);

            echo json_encode([
                'ok' => true,
                'count' => count($data),
                'data' => $data,
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        } catch (\Throwable $e) {
            error_log('[api/categories] ' . $e->getMessage());
            $this->fail(500, 'Không tải được dữ liệu danh mục.');
        }
    }

    private function fail(int $status, string $message): never
    {
        http_response_code($status);
        echo json_encode(['ok' => false, 'message' => $message], JSON_UNESCAPED_UNICODE);
        exit;
    }
}
