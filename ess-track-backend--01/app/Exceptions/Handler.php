<?php

namespace App\Exceptions;

use App\Http\Response;

class Handler {
    public static function handle(\Throwable $e) {
        $config = require __DIR__ . '/../../config/app.php';

        $logMessage = sprintf("%s in %s:%d", $e->getMessage(), $e->getFile(), $e->getLine());
        error_log($logMessage);

        if ($e instanceof ValidationException) {
            Response::error($e->getMessage(), ['errors' => $e->getErrors()], 422);
        }

        if ($e instanceof DatabaseException) {
            Response::error($e->getMessage(), [], 500);
        }

        if ($config['debug']) {
            Response::json(false, 'Internal Server Error', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }

        Response::error('Internal Server Error', [], 500);
    }
}
