<?php
namespace App\Core;

use PDO;

class Database 
{
    private static ?PDO $instance = null;
    
    public static function getInstance(): PDO
     {
        if (self::$instance === null) {
            $config = require __DIR__ . '/../../config/database.php';
            
            self::$instance = new PDO(
                "mysql:host={$config['host']};dbname={$config['database']};charset={$config['charset']}",
                $config['username'],
                $config['password'],
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );
        }
        return self::$instance;
    }
}