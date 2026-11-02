<?php
declare(strict_types=1);

namespace App\Core;

abstract class Controller
{
    protected function render(string $view, array $data = []): void
    {
        extract($data, EXTR_SKIP);

        $viewFile = dirname(__DIR__) . '/View/' . $view . '.php';

        if (!is_file($viewFile)) {
            http_response_code(500);
            exit('Không tìm thấy view: ' . htmlspecialchars($view, ENT_QUOTES, 'UTF-8'));
        }

        require dirname(__DIR__) . '/View/layout/header.php';
        require $viewFile;
        require dirname(__DIR__) . '/View/layout/footer.php';
    }

    protected function redirect(string $route, array $params = []): never
    {
        header('Location: ' . url($route, $params));
        exit;
    }
}
