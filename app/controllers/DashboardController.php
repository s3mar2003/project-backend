<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Employee;
use App\Models\Salary;
use App\Models\Leave;
use App\Models\Activity;

class DashboardController extends Controller
{
    private $employeeModel;
    private $salaryModel;
    private $leaveModel;
    private $activityModel;

    public function __construct()
    {
        $this->employeeModel = new Employee();
        $this->salaryModel = new Salary();
        $this->leaveModel = new Leave();
        $this->activityModel = new Activity();
    }

    public function index()
    {
        try {
            $employees = $this->employeeModel->all();
            $employeeCount = count($employees);

            $leaves = $this->leaveModel->all();
            $pendingLeaves = 0;
            foreach ($leaves as $leave) {
                $status = is_array($leave) ? ($leave['status'] ?? $leave[4] ?? 'pending') : ($leave->status ?? 'pending');
                if ($status === 'pending') {
                    $pendingLeaves++;
                }
            }

            $departments = [];
            foreach ($employees as $employee) {
                $dept = is_array($employee) ? ($employee['department'] ?? $employee[3] ?? '') : ($employee->department ?? '');
                if (!empty($dept)) {
                    $departments[$dept] = true;
                }
            }
            $departmentCount = count($departments);

            $activities = $this->activityModel->all(5);
            $formattedActivities = [];
            
            foreach ($activities as $activity) {
                if (is_array($activity)) {
                    $formattedActivities[] = [
                        'id' => $activity['id'] ?? $activity[0] ?? null,
                        'user_id' => $activity['user_id'] ?? $activity[1] ?? null,
                        'action' => $activity['action'] ?? $activity[2] ?? '',
                        'description' => $activity['description'] ?? $activity[3] ?? '',
                        'created_at' => $activity['created_at'] ?? $activity[4] ?? '',
                        'username' => $activity['username'] ?? $activity[5] ?? 'System'
                    ];
                } else {
                    $formattedActivities[] = $activity;
                }
            }

            $this->success([
                'employee_count' => $employeeCount,
                'pending_leaves' => $pendingLeaves,
                'department_count' => $departmentCount,
                'latest_activities' => $formattedActivities
            ]);
            
        } catch (\Exception $e) {
            $this->serverError('Failed to load dashboard data: ' . $e->getMessage());
        }
    }
    
}