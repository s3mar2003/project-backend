<?php
use App\Controllers\EmployeeController;
use App\Controllers\AuthController;
use App\Controllers\LeaveController;
use App\Controllers\SalaryController;
use App\Controllers\DashboardController;
use App\Models\Activity;

$employeeController = new EmployeeController();
$authController = new AuthController();
$leaveController = new LeaveController();
$salaryController = new SalaryController();
$dashboardController = new DashboardController();
$activityModel = new Activity();

// routes المصادقة
$router->add('POST', '/api/login', [$authController, 'login']);
$router->add('POST', '/api/logout', [$authController, 'logout']);
$router->add('GET', '/api/check-auth', [$authController, 'checkAuth']);

// routes الموظفين
$router->add('GET', '/api/employees', [$employeeController, 'index']);
$router->add('GET', '/api/employees/(\d+)', [$employeeController, 'show']);
$router->add('POST', '/api/employees', [$employeeController, 'store']);
$router->add('PUT', '/api/employees/(\d+)', [$employeeController, 'update']);
$router->add('DELETE', '/api/employees/(\d+)', [$employeeController, 'delete']);
$router->add('GET', '/api/employees/department/([^/]+)', [$employeeController, 'byDepartment']);
$router->add('GET', '/api/employees/search/([^/]+)', [$employeeController, 'search']);

// routes الإجازات
$router->add('GET', '/api/leaves', [$leaveController, 'index']);
$router->add('GET', '/api/leaves/(\d+)', [$leaveController, 'show']);
$router->add('POST', '/api/leaves', [$leaveController, 'store']);
$router->add('PUT', '/api/leaves/(\d+)', [$leaveController, 'update']);
$router->add('PATCH', '/api/leaves/(\d+)/status', [$leaveController, 'updateStatus']);
$router->add('DELETE', '/api/leaves/(\d+)', [$leaveController, 'delete']);

// routes الرواتب
$router->add('GET', '/api/salaries', [$salaryController, 'index']);
$router->add('GET', '/api/salaries/(\d+)', [$salaryController, 'show']);
$router->add('POST', '/api/salaries', [$salaryController, 'store']);
$router->add('PUT', '/api/salaries/(\d+)', [$salaryController, 'update']);
$router->add('DELETE', '/api/salaries/(\d+)', [$salaryController, 'delete']);
$router->add('GET', '/api/salaries/employee/(\d+)', [$salaryController, 'byEmployee']);

// routes الداشبورد والأنشطة
$router->add('GET', '/api/dashboard', [$dashboardController, 'index']);
$router->add('GET', '/api/activities', function() use ($activityModel) {
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 5;
    $activities = $activityModel->all($limit);
    echo json_encode(['success' => true, 'data' => $activities]);
});