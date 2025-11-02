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
            // Redirect based on role_name (new RBAC) or role (old system)
            $role_name = $this->session->userdata('role_name');
            $old_role = $this->session->userdata('role');
            
            if ($role_name) {
                // New RBAC system
                $this->redirect_by_role($role_name);
            } elseif ($old_role) {
                // Old system fallback
                $old_role === 'admin' ? redirect('admin/') : redirect('leader/');
            } else {
                redirect('login/');
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
                        // Build session data with RBAC support
                        $session_data = [
                            'user_id' => $data->user_id,
                            'username' => $data->username,
                            'full_name' => $data->full_name ?: $data->username,
                            'email' => $data->email,
                            'role_id' => $data->role_id,
                            'role_name' => $data->role_name,
                            'role_display_name' => $data->role_display_name,
                            'level' => $data->level
                        ];

                        $this->session->set_userdata($session_data);

                        // Update last login timestamp
                        $this->login->update_last_login($data->user_id);

                        // Log login activity
                        $this->login->log_activity(
                            $data->user_id,
                            $data->username,
                            'login',
                            'auth'
                        );

                        // Redirect based on role
                        $this->redirect_by_role($data->role_name);
                    }
                } else {
                    $this->session->set_flashdata('error', 'Username atau password salah!');
                    $this->load->view('login');
                }
            } else {
                $this->load->view('login');
            }
        }
    }

    /**
     * Redirect user based on their role
     */
    private function redirect_by_role($role_name)
    {
        switch ($role_name) {
            case 'bod':
                redirect('admin/'); // BOD uses admin panel
                break;
            case 'system_admin':
                redirect('admin/');
                break;
            case 'line_manager':
                redirect('leader/');
                break;
            case 'warehouse_staff':
                // Check if Warehouse controller exists, otherwise fallback to leader
                if (file_exists(APPPATH . 'controllers/Warehouse.php')) {
                    redirect('warehouse/');
                } else {
                    redirect('leader/'); // Temporary fallback
                }
                break;
            case 'qc_staff':
                // Check if QC controller exists, otherwise fallback to leader
                if (file_exists(APPPATH . 'controllers/Qc.php')) {
                    redirect('qc/');
                } else {
                    redirect('leader/'); // Temporary fallback
                }
                break;
            case 'technical_staff':
                // Check if Technical controller exists, otherwise fallback to leader
                if (file_exists(APPPATH . 'controllers/Technical.php')) {
                    redirect('technical/');
                } else {
                    redirect('leader/'); // Temporary fallback
                }
                break;
            case 'worker':
                // Check if Worker controller exists, otherwise fallback to leader
                if (file_exists(APPPATH . 'controllers/Worker.php')) {
                    redirect('worker/');
                } else {
                    redirect('leader/'); // Temporary fallback
                }
                break;
            default:
                redirect('login/');
        }
    }

    /**
     * Logout user
     */
    public function logout()
    {
        // Log logout activity before destroying session
        if ($this->session->userdata('user_id')) {
            $this->login->log_activity(
                $this->session->userdata('user_id'),
                $this->session->userdata('username'),
                'logout',
                'auth'
            );
        }

        $this->session->sess_destroy();
        redirect('login/');
    }
}
