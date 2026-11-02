<?php
declare(strict_types=1);

namespace App\Controller;

use App\Core\Controller;
use App\Repository\ProductRepository;

final class ProductController extends Controller
{
    public function __construct(private ProductRepository $products)
    {
    }

    public function index(): void
    {
        $keyword = trim($_GET['q'] ?? '');
        $categoryId = filter_input(INPUT_GET, 'category_id', FILTER_VALIDATE_INT) ?: null;

        $this->render('product/index', [
            'products' => $this->products->findAll($keyword, $categoryId),
            'categories' => $this->products->categories(),
            'keyword' => $keyword,
            'categoryId' => $categoryId,
        ]);
    }
}
