<?php
declare(strict_types=1);

namespace App\Controller;

use App\Core\Auth;
use App\Core\Controller;
use App\Repository\OrderRepository;

final class OrderController extends Controller
{
    public function __construct(private OrderRepository $orders)
    {
    }

    public function mine(): void
    {
        Auth::requireRole('customer');

        $this->render('order/index', [
            'orders' => $this->orders->findByUser(Auth::id()),
        ]);
    }

    public function show(): void
    {
        Auth::requireRole('customer');

        $orderId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        if (!$orderId) {
            http_response_code(400);
            exit('ID không hợp lệ.');
        }

        // Ownership: chỉ đọc đơn thuộc user hiện tại.
        $order = $this->orders->findOwned($orderId, Auth::id());

        if (!$order) {
            http_response_code(404);
            exit('Không tìm thấy đơn hàng hoặc bạn không có quyền xem.');
        }

        $this->render('order/show', [
            'order' => $order,
            'items' => $this->orders->items($orderId),
        ]);
    }
}
