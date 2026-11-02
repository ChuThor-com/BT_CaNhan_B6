<?php
declare(strict_types=1);

use App\Controller\AdminProductController;
use App\Controller\AdminUserController;
use App\Controller\ApiProductController;
use App\Controller\AuthController;
use App\Controller\OrderController;
use App\Controller\ProductController;
use App\Core\Database;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use App\Controller\AdminCategoryController;
use App\Repository\CategoryRepository;

require dirname(__DIR__) . '/vendor/autoload.php';
require dirname(__DIR__) . '/src/Core/helpers.php';

session_set_cookie_params([
    'httponly' => true,
    'samesite' => 'Lax',
    'secure' => isset($_SERVER['HTTPS']),
]);
session_start();

$pdo = Database::connection();

$productRepository = new ProductRepository($pdo);
$userRepository = new UserRepository($pdo);
$orderRepository = new OrderRepository($pdo);

$productController = new ProductController($productRepository);
$authController = new AuthController($userRepository);
$adminProductController = new AdminProductController($productRepository);
$adminUserController = new AdminUserController($userRepository);
$orderController = new OrderController($orderRepository);
$apiProductController = new ApiProductController($productRepository);
$categoryRepository = new CategoryRepository($pdo);
$adminCategoryController = new AdminCategoryController($categoryRepository);

$route = $_GET['route'] ?? 'products';

$method = $_SERVER['REQUEST_METHOD'];

$routes = [
    'GET' => [
        'products' => [$productController, 'index'],
        'login' => [$authController, 'form'],
        'admin/products' => [$adminProductController, 'index'],
        'admin/products/create' => [$adminProductController, 'createForm'],
        'admin/products/edit' => [$adminProductController, 'editForm'],
        'admin/users' => [$adminUserController, 'index'],
        'admin/users/create' => [$adminUserController, 'createForm'],
        'my-orders' => [$orderController, 'mine'],
        'my-orders/show' => [$orderController, 'show'],
        'api/products' => [$apiProductController, 'search'],
        'admin/categories' => [$adminCategoryController, 'index'],
'admin/categories/create' => [$adminCategoryController, 'createForm'],
'admin/categories/edit' => [$adminCategoryController, 'editForm'],
    ],
    'POST' => [
        'login' => [$authController, 'login'],
        'logout' => [$authController, 'logout'],
        'admin/products/store' => [$adminProductController, 'store'],
        'admin/products/update' => [$adminProductController, 'update'],
        'admin/products/delete' => [$adminProductController, 'delete'],
        'admin/users/store' => [$adminUserController, 'store'],
        'admin/categories/store' => [$adminCategoryController, 'store'],
'admin/categories/update' => [$adminCategoryController, 'update'],
'admin/categories/delete' => [$adminCategoryController, 'delete'],
    ],
];

$handler = $routes[$method][$route] ?? null;

if (!$handler) {
    http_response_code(404);
    exit('404 - Route không tồn tại.');
}

$handler();
