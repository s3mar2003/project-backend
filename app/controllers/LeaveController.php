<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Leave;
use App\Models\Employee;
use App\Models\Activity;

class LeaveController extends Controller
{
    private $leaveModel;
    private $employeeModel;
    private $activityModel;

    public function __construct()
    {
        $this->leaveModel = new Leave();
        $this->employeeModel = new Employee();
        $this->activityModel = new Activity();
    }

    public function index()
    {
        try {
            $leaves = $this->leaveModel->all();
            $this->json(['success' => true, 'data' => $leaves]);
        } catch (\Exception $e) {
            http_response_code(500);
            $this->json(['success' => false, 'error' => 'Server error']);
        }
    }

    public function show($id)
    {
        try {
            $leave = $this->leaveModel->find($id);
            if (!$leave) {
                http_response_code(404);
                $this->json(['success' => false, 'error' => 'Leave not found']);
                return;
            }
            $this->json(['success' => true, 'data' => $leave]);
        } catch (\Exception $e) {
            http_response_code(500);
            $this->json(['success' => false, 'error' => 'Server error']);
        }
    }

  public function store()
{
    try {
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);
        
        error_log("Leave request data: " . print_r($data, true));
        
        if (!$data || !isset($data['employee_id'], $data['start_date'], $data['end_date'])) {
            http_response_code(400);
            $this->json(['success' => false, 'error' => 'Invalid input']);
            return;
        }

        $data['reason'] = $data['reason'] ?? '';
        $data['status'] = 'pending';
        $data['leave_type'] = $data['leave_type'] ?? 'annual';

        $result = $this->leaveModel->create($data);
        
        if ($result) {
            $this->activityModel->log($data['employee_id'], 'Request Leave', 'Leave request submitted');
            http_response_code(201);
            $this->json(['success' => true, 'message' => 'Leave request submitted', 'data' => ['id' => $result]]);
        } else {
            http_response_code(500);
            $this->json(['success' => false, 'error' => 'Failed to submit leave request']);
        }
    } catch (\Exception $e) {
        error_log("Leave store error: " . $e->getMessage());
        error_log("Stack trace: " . $e->getTraceAsString());
        
        http_response_code(500);
        $this->json(['success' => false, 'error' => 'Server error: ' . $e->getMessage()]);
    }
}

    public function update($id)
    {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!$data) {
                http_response_code(400);
                $this->json(['success' => false, 'error' => 'Invalid input']);
                return;
            }

            $existingLeave = $this->leaveModel->find($id);
            if (!$existingLeave) {
                http_response_code(404);
                $this->json(['success' => false, 'error' => 'Leave not found']);
                return;
            }

            $result = $this->leaveModel->update($id, $data);
            
            if ($result) {
                $approverId = $data['approver_id'] ?? 0;
                $this->activityModel->log($approverId, 'Update Leave', 'Leave ID '.$id.' updated');
                $this->json(['success' => true, 'message' => 'Leave updated successfully']);
            } else {
                http_response_code(500);
                $this->json(['success' => false, 'error' => 'Failed to update leave']);
            }
        } catch (\Exception $e) {
            http_response_code(500);
            $this->json(['success' => false, 'error' => 'Server error']);
        }
    }

    public function updateStatus($id)
    {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!$data || !isset($data['status'])) {
                http_response_code(400);
                $this->json(['success' => false, 'error' => 'Status is required']);
                return;
            }

            $allowedStatuses = ['pending', 'approved', 'rejected'];
            if (!in_array($data['status'], $allowedStatuses)) {
                http_response_code(400);
                $this->json(['success' => false, 'error' => 'Invalid status']);
                return;
            }

            $result = $this->leaveModel->updateStatus($id, $data['status']);
            
            if ($result) {
                $approverId = $data['approver_id'] ?? 0;
                $this->activityModel->log($approverId, 'Update Leave', 'Leave ID '.$id.' status changed to '.$data['status']);
                $this->json(['success' => true, 'message' => 'Leave status updated successfully']);
            } else {
                http_response_code(500);
                $this->json(['success' => false, 'error' => 'Failed to update leave status']);
            }
        } catch (\Exception $e) {
            http_response_code(500);
            $this->json(['success' => false, 'error' => 'Server error']);
        }
    }

public function delete($id)
{
    try {
        $existingLeave = $this->leaveModel->find($id);
        if (!$existingLeave) {
            http_response_code(404);
            $this->json(['success' => false, 'error' => 'Leave not found']);
            return;
        }

        $result = $this->leaveModel->delete($id);
        
        if ($result) {
            $this->activityModel->log(0, 'Delete Leave', 'Leave ID '.$id.' deleted');
            $this->json(['success' => true, 'message' => 'Leave deleted successfully']);
        } else {
            http_response_code(500);
            $this->json(['success' => false, 'error' => 'Failed to delete leave']);
        }
    } catch (\Exception $e) {
        http_response_code(500);
        $this->json(['success' => false, 'error' => 'Server error']);
    }
}
}