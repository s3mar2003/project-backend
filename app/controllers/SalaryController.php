<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Salary;
use App\Models\Employee;
use App\Models\Activity;

class SalaryController extends Controller
{
    private $salaryModel;
    private $employeeModel;
    private $activityModel;

    public function __construct()
    {
        $this->salaryModel = new Salary();
        $this->employeeModel = new Employee();
        $this->activityModel = new Activity();
    }

    public function index()
    {
        try {
            $salaries = $this->salaryModel->all();
            $this->json(['success' => true, 'data' => $salaries]);
        } catch (\Exception $e) {
            $this->json(['success' => false, 'error' => 'Failed to load salaries'], 500);
        }
    }

    public function show($id)
    {
        try {
            $salary = $this->salaryModel->find($id);
            if (!$salary) {
                $this->json(['success' => false, 'error' => 'Salary not found'], 404);
                return;
            }
            $this->json(['success' => true, 'data' => $salary]);
        } catch (\Exception $e) {
            $this->json(['success' => false, 'error' => 'Server error'], 500);
        }
    }

    public function byEmployee($employee_id)
    {
        try {
            $employee = $this->employeeModel->find($employee_id);
            if (!$employee) {
                $this->json(['success' => false, 'error' => 'Employee not found'], 404);
                return;
            }

            $salaries = $this->salaryModel->findByEmployee($employee_id);
            $this->json(['success' => true, 'data' => $salaries]);
        } catch (\Exception $e) {
            $this->json(['success' => false, 'error' => 'Server error'], 500);
        }
    }

    public function store()
    {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!$data || !isset($data['employee_id'], $data['base_salary'])) {
                $this->json(['success' => false, 'error' => 'Invalid input'], 400);
                return;
            }

            $data['bonus_percent'] = $data['bonus_percent'] ?? 0;
            $data['deduction_percent'] = $data['deduction_percent'] ?? 0;
            $data['approved_by'] = $data['approved_by'] ?? null;

            $employee = $this->employeeModel->find($data['employee_id']);
            if (!$employee) {
                $this->json(['success' => false, 'error' => 'Employee not found'], 404);
                return;
            }

            $result = $this->salaryModel->create($data);
            
            if ($result) {
                $this->activityModel->log($data['approved_by'] ?? 1, 'Add Salary', 'Added salary for employee ID: '.$data['employee_id']);
                $this->json(['success' => true, 'message' => 'Salary created successfully']);
            } else {
                $this->json(['success' => false, 'error' => 'Failed to create salary'], 500);
            }
        } catch (\Exception $e) {
            $this->json(['success' => false, 'error' => 'Server error: ' . $e->getMessage()], 500);
        }
    }

    public function update($id)
    {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!$data) {
                $this->json(['success' => false, 'error' => 'Invalid input'], 400);
                return;
            }

            $result = $this->salaryModel->updateSalary($id, $data);
            
            if ($result) {
                $this->activityModel->log(1, 'Update Salary', 'Updated salary ID: '.$id);
                $this->json(['success' => true, 'message' => 'Salary updated successfully']);
            } else {
                $this->json(['success' => false, 'error' => 'Failed to update salary'], 500);
            }
        } catch (\Exception $e) {
            $this->json(['success' => false, 'error' => 'Server error'], 500);
        }
    }

    public function delete($id)
    {
        try {
            $result = $this->salaryModel->delete($id);
            
            if ($result) {
                $this->activityModel->log(1, 'Delete Salary', 'Deleted salary ID: '.$id);
                $this->json(['success' => true, 'message' => 'Salary deleted successfully']);
            } else {
                $this->json(['success' => false, 'error' => 'Failed to delete salary'], 500);
            }
        } catch (\Exception $e) {
            $this->json(['success' => false, 'error' => 'Server error'], 500);
        }
    }

    public function search($query)
    {
        try {
            $this->json(['success' => true, 'data' => []]);
        } catch (\Exception $e) {
            $this->json(['success' => false, 'error' => 'Server error'], 500);
        }
    }
}