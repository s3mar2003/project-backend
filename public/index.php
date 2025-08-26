<?php
// تمكين التحميل التلقائي للكلاسات
spl_autoload_register(function ($class) {
    $class = str_replace('App\\', '', $class);
    $class = str_replace('\\', '/', $class);
    $file = __DIR__ . '/../app/' . $class . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

// تمكين CORS بشكل أكثر أماناً
header("Access-Control-Allow-Origin: " . (getenv('ALLOWED_ORIGINS') ?: '*'));
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS, PATCH");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json; charset=UTF-8");

// التعامل مع طلبات OPTIONS (preflight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// التعامل مع الأخطاء
set_error_handler(function($severity, $message, $file, $line) {
    throw new ErrorException($message, 0, $severity, $file, $line);
});

set_exception_handler(function($e) {
    error_log("Uncaught exception: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Internal server error',
        'message' => getenv('APP_ENV') === 'production' ? 'Something went wrong' : $e->getMessage()
    ]);
});

use App\Core\Router;

// تهيئة الراوتر
$router = new Router();

// تحميل المسارات من ملف منفصل
require_once __DIR__ . '/../routes/api.php';

// معالجة الطلب
$method = $_SERVER['REQUEST_METHOD'];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$basePath = '/blog-mvc/public';
$uri = str_replace($basePath, '', $uri);

// إذا كان URI فارغاً أو الجذر، توجيه إلى الداشبورد
if ($uri === '' || $uri === '/') {
    $uri = '/api/dashboard';
}

$router->dispatch($method, $uri);