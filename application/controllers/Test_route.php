<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Test_route extends CI_Controller {
    
    public function index() {
        echo json_encode([
            'success' => true,
            'message' => 'Test route works!',
            'timestamp' => date('Y-m-d H:i:s')
        ]);
    }
}
