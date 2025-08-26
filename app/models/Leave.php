<?php
namespace App\Models;

use App\Core\Model;
use PDO;

class Leave extends Model {
    protected $table = 'leaves';
    
    public function __construct() {
        parent::__construct();
    }
    
    public function all() {
        try {
            $query = "SELECT l.*, e.full_name as employee_name 
                      FROM {$this->table} l 
                      JOIN employees e ON l.employee_id = e.id 
                      ORDER BY l.created_at DESC";
            
            $stmt = self::getDB()->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Database error in all(): " . $e->getMessage());
            return [];
        }
    }
    
    public function find($id) {
        try {
            $query = "SELECT l.*, e.full_name as employee_name 
                      FROM {$this->table} l 
                      JOIN employees e ON l.employee_id = e.id 
                      WHERE l.id = :id";
            
            $stmt = self::getDB()->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Database error in find(): " . $e->getMessage());
            return null;
        }
    }
    
   public function create($data)
{
    try {
        $query = "INSERT INTO {$this->table} (employee_id, leave_type, start_date, end_date, status, reason) 
                  VALUES (:employee_id, :leave_type, :start_date, :end_date, :status, :reason)";
        
        $stmt = self::getDB()->prepare($query);
        $stmt->bindParam(':employee_id', $data['employee_id'], PDO::PARAM_INT);
        $stmt->bindParam(':leave_type', $data['leave_type']);
        $stmt->bindParam(':start_date', $data['start_date']);
        $stmt->bindParam(':end_date', $data['end_date']);
        $stmt->bindParam(':status', $data['status']);
        $stmt->bindParam(':reason', $data['reason']);
        
        $result = $stmt->execute();
        
        if (!$result) {
            error_log("PDO Error: " . print_r($stmt->errorInfo(), true));
            return false;
        }
        
        return self::getDB()->lastInsertId();
    } catch (\PDOException $e) {
        error_log("Database error in create(): " . $e->getMessage());
        error_log("SQL: " . $query);
        error_log("Data: " . print_r($data, true));
        return false;
    }
}
    
    public function update($id, $data) {
        try {
            $query = "UPDATE {$this->table} SET 
                      employee_id = :employee_id, 
                      leave_type = :leave_type, 
                      start_date = :start_date, 
                      end_date = :end_date, 
                      status = :status, 
                      reason = :reason 
                      WHERE id = :id";
            
            $stmt = self::getDB()->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PAR极速vpn可以加速github吗AM_INT);
            $stmt->bindParam(':employee_id', $data['employee_id'], PDO::PARAM_INT);
            $stmt->bindParam(':leave_type', $data['leave_type']);
            $stmt->bindParam(':start_date', $data['start_date']);
            $stmt->bindParam(':end_date', $data['end_date']);
            $stmt->bindParam(':status', $data['status']);
            $stmt->bindParam(':reason', $data['reason']);
            
            return $stmt->execute();
        } catch (\PDOException $e) {
            error_log("Database error in update(): " . $e->getMessage());
            return false;
        }
    }
    
    public function updateStatus($id, $status) {
        try {
            $stmt = self::getDB()->prepare("UPDATE {$this->table} SET status = :status WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':status', $status);
            return $stmt->execute();
        } catch (\PDOException $e) {
            error_log("Database error in updateStatus(): " . $e->getMessage());
            return false;
        }
    }
    
    public function delete($id) {
        try {
            $stmt = self::getDB()->prepare("DELETE FROM {$this->table} WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (\PDOException $e) {
            error_log("Database error in delete(): " . $e->getMessage());
            return false;
        }
    }
    
    public function getByEmployee($employeeId) {
        try {
            $query= "SELECT l.*, e.full_name as employee_name 
                      FROM {$this->table} l 
                      JOIN employees e ON l.employee_id = e.id 
                      WHERE l.employee_id = :employee_id 
                      ORDER BY l.created_at DESC";
            
            $stmt = self::getDB()->prepare($query);
            $stmt->bindParam(':employee_id', $employeeId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Database error in getByEmployee(): " . $e->getMessage());
            return [];
        }
    }
}