<?php
namespace App\Models;

use App\Core\Model;
use PDO;

class Activity extends Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function all($limit = 50)
    {
        try {
            $stmt = self::getDB()->prepare("
                SELECT a.*, u.username 
                FROM activities a 
                LEFT JOIN users u ON a.user_id = u.id 
                ORDER BY a.created_at DESC 
                LIMIT :limit
            ");
            $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (\PDOException $e) {
            error_log("Database error in all(): " . $e->getMessage());
            return [];
        }
    }

    public function log($user_id, $action, $description = '')
    {
        try {
            $stmt = self::getDB()->prepare("
                INSERT INTO activities (user_id, action, description) 
                VALUES (:user_id, :action, :description)
            ");
            return $stmt->execute([
                'user_id' => $user_id,
                'action' => $action,
                'description' => $description
            ]);
        } catch (\PDOException $e) {
            error_log("Database error in log(): " . $e->getMessage());
            return false;
        }
    }
}