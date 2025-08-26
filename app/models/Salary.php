<?php
namespace App\Models;

use App\Core\Model;
use PDO;

class Salary extends Model
{
    protected $table = 'salaries';

    public function __construct()
    {
        parent::__construct();
    }

   
    public function all(): array
    {
        try {
            $stmt = self::getDB()->prepare("
                SELECT s.*, e.full_name, e.department 
                FROM {$this->table} s 
                LEFT JOIN employees e ON s.employee_id = e.id 
                WHERE s.deleted_at IS NULL 
                ORDER BY s.created_at DESC
            ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Database error in all(): " . $e->getMessage());
            return [];
        }
    }

   
    public function findByEmployee(int $employee_id): array
    {
        try {
            $stmt = self::getDB()->prepare("
                SELECT s.*, e.full_name, e.department 
                FROM {$this->table} s 
                LEFT JOIN employees e ON s.employee_id = e.id 
                WHERE s.employee_id = :employee_id AND s.deleted_at IS NULL
            ");
            $stmt->execute(['employee_id' => $employee_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Database error in findByEmployee(): " . $e->getMessage());
            return [];
        }
    }

   
    public function create(array $data): bool
    {
        try {
            $stmt = self::getDB()->prepare("
                INSERT INTO {$this->table} 
                (employee_id, base_salary, bonus_percent, deduction_percent, approved_by) 
                VALUES (:employee_id, :base_salary, :bonus_percent, :deduction_percent, :approved_by)
            ");
            
            return $stmt->execute([
                'employee_id' => $data['employee_id'],
                'base_salary' => $data['base_salary'],
                'bonus_percent' => $data['bonus_percent'] ?? 0,
                'deduction_percent' => $data['deduction_percent'] ?? 0,
                'approved_by' => $data['approved_by'] ?? null
            ]);
        } catch (\PDOException $e) {
            error_log("Database error in create(): " . $e->getMessage());
            return false;
        }
    }

   
    public function updateSalary(int $id, array $data): bool
    {
        try {
            $fields = [];
            $params = ['id' => $id];
            
            $allowedFields = ['base_salary', 'bonus_percent', 'deduction_percent'];
            
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
            error_log("Database error in updateSalary(): " . $e->getMessage());
            return false;
        }
    }

    
    public function delete(int $id): bool
    {
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

    
    public function calculateFinalSalary(float $base, float $bonusPercent, float $deductionPercent): float
    {
        $bonus = $base * ($bonusPercent / 100);
        $deduction = $base * ($deductionPercent / 100);
        return $base + $bonus - $deduction;
    }
}