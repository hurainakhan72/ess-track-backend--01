<?php

use App\Http\Controllers\InquiryController;
use App\Http\Middleware\CorsMiddleware;
use App\Http\Middleware\RateLimitMiddleware;
use App\Exceptions\Handler;

// Apply middlewares
CorsMiddleware::handle();
RateLimitMiddleware::handle();

$requestUri = $_SERVER['REQUEST_URI'];
$requestMethod = $_SERVER['REQUEST_METHOD'];
$requestPath = parse_url($requestUri, PHP_URL_PATH);

try {
    if ($requestMethod === 'GET' && ($requestPath === '/' || $requestPath === '/form' || $requestPath === '/form.html')) {
        header('Content-Type: text/html; charset=UTF-8');
        echo file_get_contents(__DIR__ . '/../public/form.html');
        exit;
    }

    if ($requestMethod === 'POST' && strpos($requestPath, '/api/inquiries') === 0) {
        $controller = new InquiryController($db);
        $controller->submit();
    } elseif ($requestMethod === 'POST' && strpos($requestPath, '/api/send-otp') === 0) {
        $controller = new InquiryController($db);
        $controller->sendOtp();
    } elseif ($requestMethod === 'POST' && strpos($requestPath, '/api/verify-otp') === 0) {
        $controller = new InquiryController($db);
        $controller->verifyOtp();
    } elseif ($requestMethod === 'GET' && strpos($requestPath, '/api/status') === 0) {
        echo json_encode([
            'status' => 'ok',
            'message' => 'API is running',
            'timestamp' => date('Y-m-d H:i:s')
        ]);
    } elseif ($requestMethod === 'GET' && strpos($requestPath, '/api/test-db') === 0) {
        if ($db) {
            $result = $db->query("SHOW TABLES LIKE 'inquiries'");
            $tableExists = $result->num_rows > 0;
            echo json_encode([
                'status' => 'connected',
                'message' => 'Database connection successful',
                'host' => $db->host_info,
                'database' => getenv('DB_NAME'),
                'table_inquiries_exists' => $tableExists,
                'charset' => $db->character_set_name()
            ]);
        } else {
            echo json_encode([
                'status' => 'failed',
                'message' => 'Database connection failed'
            ]);
        }
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Not Found']);
    }
} catch (\Throwable $e) {
    Handler::handle($e);
}
