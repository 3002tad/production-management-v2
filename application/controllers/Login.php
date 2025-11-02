<?php

defined('BASEPATH') or exit('no direct script access allowed');

class Login extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('session');
        $this->load->library('form_validation');
        $this->load->model('LoginModel', 'login');
        $this->load->model('CrudModel', 'crudModel');
    }

    public function index()
    {
        if ($this->login->is_logged_in()) {
            // Phân quyền redirect dựa trên role
            $role = $this->login->is_role();
            
            // Support cả 'admin' (legacy) và 'system_admin' (RBAC mới)
            if ($role === 'admin' || $role === 'system_admin') {
                redirect('admin/');
                return;
            } elseif ($role === 'bod') {
                redirect('BOD/');
                return;
            } elseif ($role === 'leader' || $role === 'line_manager') {
                redirect('leader/');
                return;
            } else {
                // Role khác không có quyền truy cập - KHÔNG redirect về login để tránh loop
                $this->session->sess_destroy();
                // Hiển thị form login với error message
                $data['error'] = 'Bạn không có quyền truy cập hệ thống.';
                $this->load->view('login', $data);
                return;
            }
        } else {
            $this->form_validation->set_rules('username', 'Username', 'required');
            $this->form_validation->set_rules('password', 'Password', 'required');

            $this->form_validation->set_message('required', '<div class="alert alert-danger" style="margin-top: 3px">
                    <div class="header"><b><i class="fa fa-exclamation-circle"></i> {field}</b> harus diisi</div></div>');

            if ($this->form_validation->run() === true) {
                $username = $this->input->post('username', true);
                $password = $this->input->post('password', true);

                $checking = $this->login->check_login('user', ['username' => $username], ['password' => $password]);

                if ($checking !== false) {
                    foreach ($checking as $data) {
                        $session_data = [
                            'user_id' => $data->user_id,
                            'username' => $data->username,
                            'password' => $data->password,
                            'role' => $data->role,
                        ];

                        $this->session->set_userdata($session_data);

                        // Redirect dựa trên role
                        $role = $this->session->userdata('role');
                        
                        // Support cả 'admin' (legacy) và 'system_admin' (RBAC mới)
                        if ($role === 'admin' || $role === 'system_admin') {
                            redirect('admin/');
                        } elseif ($role === 'bod') {
                            redirect('BOD/');
                        } elseif ($role === 'leader' || $role === 'line_manager') {
                            redirect('leader/');
                        } else {
                            // Role khác (warehouse_staff, qc_staff, technical_staff, worker) không có quyền truy cập web
                            $this->session->sess_destroy();
                            redirect('login/');
                        }
                    }
                } else {
                    $this->load->view('login');
                }
            } else {
                $this->load->view('login');
            }
        }
    }

    /**
     * Logout - Xóa session và redirect về login
     */
    public function logout()
    {
        // Xóa toàn bộ session
        $this->session->sess_destroy();
        
        // Redirect về trang login
        redirect('login/');
    }
}
