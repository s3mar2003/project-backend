<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;
use App\Models\Activity;

class AuthController extends Controller
{
    private $userModel;
    private $activityModel;

    public function __construct()
    {
        $this->userModel = new User();
        $this->activityModel = new Activity();
        session_start();
    }

    
    public function login()
    {
        try {
       $data = json_decode(file_get_contents("php://input"), true);
       
       if (!$data || empty($data['username']) || empty($data['password'])) {
           $this->error('اسم المستخدم وكلمة المرور مطلوبان', 400);
           return;
       }

       $user = $this->userModel->findByUsername($data['username']);
       
       if (!$user || !password_verify($data['password'], $user['password'])) {
           $this->error('بيانات الدخول غير صحيحة', 401);
           return;
       }

       // إنشاء جلسة
       $_SESSION['user_id'] = $user['id'];
       $_SESSION['username'] = $user['username'];
       $_SESSION['logged_in'] = true;

       $this->activityModel->log($user['id'], 'Login', 'User logged in');

            $this->success([
                'user' => [
                    'id' => $user['id'],
                    'username' => $user['username']
                ]
            ], 'تم تسجيل الدخول بنجاح');

        } catch (\Exception $e) {
            $this->error('خطأ في الخادم: ' . $e->getMessage(), 500);
        }
    }

    
    public function logout()
    {
        try {
            if (isset($_SESSION['user_id'])) {
                $this->activityModel->log($_SESSION['user_id'], 'Logout', 'User logged out');
            }

            session_destroy();
            session_unset();

            $this->success(null, 'تم تسجيل الخروج بنجاح');

        } catch (\Exception $e) {
            $this->error('خطأ في الخادم: ' . $e->getMessage(), 500);
        }
    }

    
    public function checkAuth()
    {
        try {
            if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
                $this->success([
                    'authenticated' => true,
                    'user' => [
                        'id' => $_SESSION['user_id'],
                        'username' => $_SESSION['username']
                    ]
                ], 'المستخدم مصادق عليه');
            } else {
                $this->success([
                    'authenticated' => false
                ], 'المستخدم غير مصادق عليه');
            }
        } catch (\Exception $e) {
            $this->error('خطأ في الخادم: ' . $e->getMessage(), 500);
        }
    }
}