<?php
namespace App\Models;

use App\Core\Model;
use PDO;

class User extends Model
{
    protected $table = 'users';

    public function __construct()
    {
        parent::__construct();
    }

    public function findByUsername(string $username): ?array
    {
        try {
            $stmt = self::getDB()->prepare("
                SELECT * FROM {$this->table} 
                WHERE username = :username AND deleted_at IS NULL
            ");
            $stmt->execute(['username' => $username]);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (\PDOException $e) {
            error_log("Database error in findByUsername(): " . $e->getMessage());
            return null;
        }
    }

    
    public function findById(int $id): ?array
    {
        try {
            $stmt = self::getDB()->prepare("
                SELECT id, username, created_at 
                FROM {$this->table} 
                WHERE id = :id AND deleted_at IS NULL
            ");
            $stmt->execute(['id' => $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (\PDOException $e) {
            error_log("Database error in findById(): " . $e->getMessage());
            return null;
        }
    }
}