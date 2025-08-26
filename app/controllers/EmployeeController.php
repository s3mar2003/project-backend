<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Employee;
use App\Models\Activity;

class EmployeeController extends Controller
 {
    private Employee $employeeModel;
    private Activity $activityModel;

    public function __construct()
 {
        $this->employeeModel = new Employee();
        $this->activityModel = new Activity();
    }


    public function index() 
    {
        try {
            $employees = $this->employeeModel->all();

            $this->activityModel->log(1, 'View Employees', 'Viewed all employees list');

            $this->success($employees, 'Employees retrieved successfully');
        } catch (\Exception $e) {
            $this->error('Failed to retrieve employees: ' . $e->getMessage(), 500);
        }
    }

    public function show($id) 
    {
        try {
            $employee = $this->employeeModel->find((int)$id);

            if ($employee) {
                $this->success($employee, 'Employee retrieved successfully');
            } else {
                $this->notFound('Employee not found');
            }
        } catch (\Exception $e) {
            $this->error('Failed to retrieve employee: ' . $e->getMessage(), 500);
        }
    }


    public function store() 
    {
        try 
        {
            $data = json_decode(file_get_contents("php://input"), true);

            if (!$data) {
                $this->error('No input data', 400);
                return;
            }

            $required = ['full_name', 'email', 'department', 'contract', 'evaluation'];
            foreach ($required as $field)
                 {
                if (empty($data[$field])) 
                    {
                    $this->error("Field $field is required", 400);
                    return;
                    }
                 }

            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) 
                {
                $this->error('Invalid email format', 400);
                return;
                }

            $existing = $this->employeeModel->findByEmail($data['email']);
            if ($existing) {
                $this->error('Email already exists', 400);
                return;
            }

            $data['phone'] = $data['phone'] ?? '';
            $data['address'] = $data['address'] ?? '';

            $result = $this->employeeModel->create($data);

            if ($result)
                 {
                $this->activityModel->log(1, 'Add Employee', 'Added new employee: ' . $data['full_name']);

                $this->success(['id' => $result], 'Employee added successfully', 201);
            } else {
                $this->error('Failed to add employee', 500);
            }
        } catch (\Exception $e) {
            $this->error('Server error: ' . $e->getMessage(), 500);
        }
    }

    public function update($id) 
    {
        try {
            $data = json_decode(file_get_contents("php://input"), true);

            if (!$data) {
                $this->error('No input data', 400);
                return;
            }

            $existing = $this->employeeModel->find((int)$id);
            if (!$existing)
                 {
                $this->notFound('Employee not found');
                return;
                 }

            if (isset($data['email']) && $data['email'] !== $existing['email']) 
                {
                $emailExists = $this->employeeModel->findByEmail($data['email']);
                if ($emailExists) 
                    {
                    $this->error('Email already exists', 400);
                    return;
                }
               }

            $result = $this->employeeModel->update((int)$id, $data);

            if ($result)
                 {
                $this->activityModel->log(1, 'Update Employee', 'Updated employee data: ' . $existing['full_name']);

                $this->success(null, 'Employee updated successfully');
            } else {
                $this->error('Failed to update employee', 500);
            }
        } catch (\Exception $e) {
            $this->error('Server error: ' . $e->getMessage(), 500);
        }
    }


    public function delete($id) 
    {
        try {
            $existing = $this->employeeModel->find((int)$id);
            if (!$existing) 
                {
                $this->notFound('Employee not found');
                return;
               }

            $result = $this->employeeModel->delete((int)$id);

            if ($result) {
                $this->activityModel->log(1, 'Delete Employee', 'Deleted employee: ' . $existing['full_name']);

                $this->success(null, 'Employee deleted successfully');
            } else {
                $this->error('Failed to delete employee', 500);
            }
        } catch (\Exception $e) {
            $this->error('Server error: ' . $e->getMessage(), 500);
        }
    }


    // public function byDepartment($department)
    //  {
    //     try {
    //         $employees = $this->employeeModel->all();

    //         $filtered = array_filter($employees, function($employee) use ($department) {
    //             return $employee['department'] === urldecode($department);
    //         });

    //         $this->success(array_values($filtered), 'Employees retrieved by department');
    //     } catch (\Exception $e) {
    //         $this->error('Failed to retrieve employees: ' . $e->getMessage(), 500);
    //     }
    // }


    // public function search($query)
    //  {
    //     try {
    //         $employees = $this->employeeModel->all();
    //         $decodedQuery = urldecode($query);

    //         $filtered = array_filter($employees, function($employee) use ($decodedQuery) {
    //             $nameMatch = stripos($employee['full_name'] ?? '', $decodedQuery) !== false;
    //             $emailMatch = stripos($employee['email'] ?? '', $decodedQuery) !== false;
    //             return $nameMatch || $emailMatch;
    //         });

    //         $this->success(array_values($filtered), 'Employees search results');
    //     } catch (\Exception $e) {
    //         $this->error('Failed to search employees: ' . $e->getMessage(), 500);
    //     }
    // }
}