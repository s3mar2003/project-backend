<?php
namespace App\Core;

class Request
{
    public static function all(): array
    {
        $data = [];
        if ($_SERVER['CONTENT_TYPE'] ?? '' === 'application/json') {
            $data = json_decode(file_get_contents('php://input'), true) ?? [];
        } else {
            $data = $_REQUEST;
        }
        return $data;
    }

    public static function input(string $key, $default = null)
    {
        $data = self::all();
        return $data[$key] ?? $default;
    }
}
