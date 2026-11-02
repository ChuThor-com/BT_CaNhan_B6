<?php
declare(strict_types=1);

namespace App\Controller;

use App\Repository\ProductRepository;

final class ApiProductController
{
    public function __construct(private ProductRepository $products)
    {
    }

    public function search(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $keyword = trim($_GET['q'] ?? '');

        try {
            $data = $this->products->searchActive($keyword);

            echo json_encode([
                'ok' => true,
                'data' => $data,
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        } catch (\Throwable $e) {
            http_response_code(500);

            echo json_encode([
                'ok' => false,
                'message' => 'Không tải được dữ liệu sản phẩm.',
            ], JSON_UNESCAPED_UNICODE);
        }
    }
}
