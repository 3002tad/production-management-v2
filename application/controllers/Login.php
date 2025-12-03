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
        // If user is already logged in, redirect to appropriate page
        if ($this->login->is_logged_in()) {
            $role = $this->session->userdata('role');
            if (!empty($role)) {
                $this->redirect_by_role($role);
                exit();
            }
            // If no role, logout
            $this->logout();
            exit();
        }
        
        // Prepare data untuk view
        $data = [];
        $data['error'] = $this->session->flashdata('error');
        
        // User is not logged in - show login form
        $this->form_validation->set_rules('username', 'Username', 'required');
        $this->form_validation->set_rules('password', 'Password', 'required');

        $this->form_validation->set_message('required', '{field} harus diisi');

        if ($this->form_validation->run() === true) {
            $username = $this->input->post('username', true);
            $password = $this->input->post('password', true);

            $checking = $this->login->check_login('user', ['username' => $username], ['password' => $password]);

            if ($checking !== false) {
                foreach ($checking as $data_user) {
                    // Build session data - backward compatible với database cũ
                    // Resolve role name: some databases store `role` (string),
                    // others store `role_id` (FK to `roles.role_name`).
                    $resolvedRole = null;
                    if (!empty($data_user->role)) {
                        $resolvedRole = strtolower(trim($data_user->role));
                    } elseif (!empty($data_user->role_id)) {
                        // Lookup role_name from `roles` table
                        $roleRow = $this->db->select('role_name')->from('roles')->where('role_id', $data_user->role_id)->limit(1)->get()->row();
                        if ($roleRow && !empty($roleRow->role_name)) {
                            $resolvedRole = strtolower(trim($roleRow->role_name));
                        }
                    }

                    // Backward compatibility: if still empty, fall back to 'worker'
                    if (empty($resolvedRole)) {
                        $resolvedRole = 'worker';
                    }

                    $session_data = [
                        'user_id' => $data_user->user_id,
                        'username' => $data_user->username,
                        'role' => $resolvedRole
                    ];

                    $this->session->set_userdata($session_data);

                    // Update last login timestamp (nếu column tồn tại)
                    $this->login->update_last_login($data_user->user_id);

                    // Log login activity (nếu table tồn tại)
                    $this->login->log_activity(
                        $data_user->user_id,
                        $data_user->username,
                        'login',
                        'auth'
                    );

                    // Get role for redirect (use resolved role mapping)
                    $roleName = $resolvedRole;

                    // Redirect based on role
                    $this->redirect_by_role($roleName);
                    exit();
                }
            } else {
                $data['error'] = 'Username hoặc password sai!';
            }
        } else {
            // Form validation error
            $data['error'] = validation_errors('<div class="alert alert-danger">', '</div>');
        }
        
        $this->load->view('login', $data);
    }

    /**
     * Redirect user based on their role
     */
    private function redirect_by_role($role_name)
    {
        $role_name = strtolower(trim($role_name));
        
        switch ($role_name) {
            case 'bod':
                // BOD chỉ được xem, chuyển đến trang bod với quyền xem
                redirect('bod/?view_only=1');
                exit();
            case 'admin':
                // Admin chỉ được xem, chuyển đến trang admin với quyền xem
                redirect('admin/?view_only=1');
                exit();
            case 'system_admin':
                // System admin vẫn chuyển đến admin, có thể chỉnh sửa nếu cần
                redirect('admin/');
                exit();
            case 'line_manager':
            case 'leader':
                // Leader có trạng thái: Sẵn sàng, Đã xếp lịch, Ngừng hoạt động
                // Trạng thái này nên được xử lý ở controller leader, chuyển trạng thái qua query string
                $leader_status = $this->session->userdata('leader_status');
                if (!$leader_status) {
                    $leader_status = 'san_sang'; // mặc định là sẵn sàng
                }
                redirect('leader/?status=' . $leader_status);
                exit();
            case 'warehouse_staff':
                if (file_exists(APPPATH . 'controllers/Warehouse.php')) {
                    redirect('warehouse/');
                } else {
                    redirect('leader/');
                }
                exit();
            case 'qc_staff':
                if (file_exists(APPPATH . 'controllers/Qc.php')) {
                    redirect('qc/');
                } else {
                    redirect('leader/');
                }
                exit();
            case 'technical_staff':
            case 'technical':
                // Technical staff redirected to incident report system
                redirect('uc15_bcsc/technical');
                exit();
            case 'worker':
                // Redirect worker to incident report page (UC15_BCSC)
                redirect('uc15_bcsc/uc15_bcsc');
                exit();
            default:
                // Unknown role - redirect to leader as default
                redirect('leader/');
                exit();
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