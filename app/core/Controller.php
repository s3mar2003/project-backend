<?php
namespace App\Core;

class Controller
 {
    protected function json($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
    
    protected function success($data = null, $message = 'Success', $statusCode = 200)
     {
        $response = [
            'success' => true,
            'message' => $message,
        ];
        
        if ($data !== null) {
            $response['data'] = $data;
        }
        
        $this->json($response, $statusCode);
    }
    
    protected function error($message = 'Error', $statusCode = 400, $errors = null)
     {
        $response = [
            'success' => false,
            'message' => $message,
        ];
        
        if ($errors !== null) {
            $response['errors'] = $errors;
        }
        
        $this->json($response, $statusCode);
    }
    
    protected function notFound($message = 'Resource not found') 
    {
        $this->error($message, 404);
    }
    
    protected function unauthorized($message = 'Unauthorized') 
    {
        $this->error($message, 401);
    }
    
    protected function forbidden($message = 'Forbidden') 
    {
        $this->error($message, 403);
    }
    
    protected function serverError($message = 'Internal server error')
     {
        $this->error($message, 500);
    }
}