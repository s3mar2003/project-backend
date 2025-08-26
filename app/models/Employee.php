<?php
namespace App\Models;

use App\Core\Model;
use PDO;

class Employee extends Model {
    protected $table = 'employees';

    public function __construct() {
        parent::__construct();
    }


    public function all(): array {
        try {
            $stmt = self::getDB()->prepare("
                SELECT * FROM {$this->table} 
                WHERE deleted_at IS NULL 
                ORDER BY created_at DESC
            ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Database error in all(): " . $e->getMessage());
            return [];
        }
    }

    public function find(int $id): ?array {
        try {
            $stmt = self::getDB()->prepare("
                SELECT * FROM {$this->table} 
                WHERE id = :id AND deleted_at IS NULL
            ");
            $stmt->execute(['id' => $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (\PDOException $e) {
            error_log("Database error in find(): " . $e->getMessage());
            return null;
        }
    }


    public function findByEmail(string $email): ?array {
        try {
            $stmt = self::getDB()->prepare("
                SELECT * FROM {$this->table} 
                WHERE email = :email AND deleted_at IS NULL
            ");
            $stmt->execute(['email' => $email]);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (\PDOException $e) {
            error_log("Database error in findByEmail(): " . $e->getMessage());
            return null;
        }
    }


    public function create(array $data): bool {
        try {
            $stmt = self::getDB()->prepare("
                INSERT INTO {$this->table} 
                (full_name, email, department, contract, evaluation, phone, address) 
                VALUES (:full_name, :email, :department, :contract, :evaluation, :phone, :address)
            ");

            return $stmt->execute([
                'full_name' => $data['full_name'] ?? '',
                'email' => $data['email'] ?? '',
                'department' => $data['department'] ?? '',
                'contract' => $data['contract'] ?? '',
                'evaluation' => $data['evaluation'] ?? '',
                'phone' => $data['phone'] ?? '',
                'address' => $data['address'] ?? ''
            ]);
        } catch (\PDOException $e) {
            error_log("Database error in create(): " . $e->getMessage());
            return false;
        }
    }


    public function update(int $id, array $data): bool {
        try {
            $fields = [];
            $params = ['id' => $id];

            $allowedFields = ['full_name', 'email', 'department', 'contract', 'evaluation', 'phone', 'address'];

            foreach ($data as $key => $value) {
                if (in_array($key, $allowedFields)) {
                    $fields[] = "$key = :$key";
                    $params[$key] = $value;
                }
            }

            if (empty($fields)) {
                return false;
            }

            $sql = "UPDATE {$this->table} SET " . implode(', ', $fields) . " 
                    WHERE id = :id AND deleted_at IS NULL";

            $stmt = self::getDB()->prepare($sql);
            return $stmt->execute($params);
        } catch (\PDOException $e) {
            error_log("Database error in update(): " . $e->getMessage());
            return false;
        }
    }


    public function delete(int $id): bool {
        try {
            $stmt = self::getDB()->prepare("
                UPDATE {$this->table} 
                SET deleted_at = NOW() 
                WHERE id = :id
            ");
            return $stmt->execute(['id' => $id]);
        } catch (\PDOException $e) {
            error_log("Database error in delete(): " . $e->getMessage());
            return false;
        }
    }


    public function count(): int {
        try {
            $stmt = self::getDB()->prepare("
                SELECT COUNT(*) as count 
                FROM {$this->table} 
                WHERE deleted_at IS NULL
            ");
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int)($result['count'] ?? 0);
        } catch (\PDOException $e) {
            error_log("Database error in count(): " . $e->getMessage());
            return 0;
        }
    }


    public function getByDepartment(string $department): array {
        try {
            $stmt = self::getDB()->prepare("
                SELECT * FROM {$this->table} 
                WHERE department = :department AND deleted_at IS NULL 
                ORDER BY full_name
            ");
            $stmt->execute(['department' => $department]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Database error in getByDepartment(): " . $e->getMessage());
            return [];
        }
    }
}