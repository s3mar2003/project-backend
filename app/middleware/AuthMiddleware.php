<?php
namespace App\Middlewares;

use App\Controllers\AuthController;

class AuthMiddleware {
    public static function check() {
        $authController = new AuthController();
        return $authController->checkAuthMiddleware();
    }
}