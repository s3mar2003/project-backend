<?php
namespace App\Core;

use PDO;
use PDOException;

class Model
 {
    protected static $db = null;
    
    public function __construct()
     {
        if (self::$db === null) {
            $this->connect();
        }
    }
    
    protected function connect()
     {
        try {
            $dsn = 'mysql:host=localhost;dbname=employee_management;charset=utf8';
            self::$db = new PDO($dsn, "root", "");
            self::$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            self::$db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die('Database connection failed: ' . $e->getMessage());
        }
    }
    
    public static function getDB() 
    {
        if (self::$db === null) 
            {
            new self();
            }
        return self::$db;
    }
}