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
                // Hệ thống RBAC mới
                $this->redirect_by_role($role_name);
            } elseif ($old_role) {
                // Dự phòng hệ thống cũ
                $old_role === 'admin' ? redirect('admin/staff') : redirect('leader/');
            } else {
                redirect('login/');
            }
        } else {
            $this->form_validation->set_rules('username', 'Username', 'required');
            $this->form_validation->set_rules('password', 'Password', 'required');

            $this->form_validation->set_message('required', '<div class="alert alert-danger" style="margin-top: 3px">
                    <div class="header"><b><i class="fa fa-exclamation-circle"></i> {field}</b> bắt buộc</div></div>');

            if ($this->form_validation->run() === true) {
                $username = $this->input->post('username', true);
                $password = $this->input->post('password', true);

                // Password is stored as PLAINTEXT in database (NOT MD5!)
                                $checking = $this->login->check_login($username, $password);

                if ($checking) {
                                        $data = $checking;
                                        // Check if user is locked
                    if ($data->is_active == 0) {
                        $this->session->set_flashdata('login_error', 'Tài khoản đã bị khóa. Vui lòng liên hệ quản trị viên.');
                        $this->load->view('login');
                        return;
                    }

                    // Build session data with RBAC support
                    $session_data = [
                        'user_id' => $data->user_id,
                        'username' => $data->username,
                        'full_name' => $data->full_name ?: $data->username,
                        'email' => $data->email,
                        'role_id' => $data->role_id,
                        'role_name' => $data->role_name,
                        'role_display_name' => $data->role_display_name,
                        'level' => $data->level,
                        'department' => $data->department ?: 'Chưa xác định',
                        'position' => $data->position ?: 'Chưa xác định',
                        'must_change_password' => $data->must_change_password ?? 0
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

                    // UC6: Check if must change password
                    if (isset($data->must_change_password) && $data->must_change_password == 1) {
                        redirect('login/change_password_required');
                        return;
                    }

                    // Thiết lập thông báo đăng nhập thành công cho system_admin và legacy admin
                    if (isset($data->role_name) && ($data->role_name === 'system_admin' || $data->role_name === 'admin')) {
                        $this->session->set_flashdata('success', 'Đăng nhập thành công! Chào mừng ' . $data->full_name);
                    }

                    // Redirect based on role
                    $this->redirect_by_role($data->role_name);
                } else {
                    log_message('debug', 'Login failed for username: ' . $username);
                    $this->session->set_flashdata('login_error', 'Tên đăng nhập hoặc mật khẩu không đúng!');
                    $this->load->view('login');
                }
            } else {
                $this->load->view('login');
            }
        }
    }

    /**
     * Redirect user based on their role with success message
     */
    private function redirect_by_role($role_name)
    {
        switch ($role_name) {
            case 'bod':
                // BOD has dedicated controller and dashboard
                if (file_exists(APPPATH . 'controllers/BOD.php')) {
                    redirect(site_url('bod/?msg=success'));
                } else {
                    redirect(site_url('admin/staff?msg=success')); // Fallback về trang quản trị (nếu controller BOD không tồn tại)
                }
                break;
            case 'system_admin':
                redirect(site_url('admin/staff?msg=success'));
                break;
            case 'line_manager':
                redirect(site_url('leader/?msg=success'));
                break;
            case 'warehouse_staff':
                // Kiểm tra nếu controller Warehouse tồn tại, nếu không fallback về leader
                if (file_exists(APPPATH . 'controllers/Warehouse.php')) {
                    redirect(site_url('warehouse/?msg=success'));
                } else {
                    redirect(site_url('leader/?msg=success')); // Fallback tạm thời
                }
                break;
            case 'qc_staff':
                // Check if QC controller exists, otherwise fallback to leader
                if (file_exists(APPPATH . 'controllers/Qc.php')) {
                    redirect(site_url('qc/?msg=success'));
                } else {
                    redirect(site_url('leader/?msg=success')); // Fallback tạm thời
                }
                break;
            case 'technical_staff':
                // Check if Technical controller exists, otherwise fallback to leader
                if (file_exists(APPPATH . 'controllers/Technical.php')) {
                    redirect(site_url('technical/?msg=success'));
                } else {
                    redirect(site_url('leader/?msg=success')); // Fallback tạm thời
                }
                break;
            case 'worker':
                // Worker: chuyển thẳng vào UC15 - Báo cáo sự cố
                // Sử dụng route đã khai báo cho UC15_BCSC
                redirect(site_url('uc15_qlns/uc15_bcsc?msg=success'));
                break;
            default:
                redirect('login/');
        }
    }

    /**
     * Force user to change password (first login or after reset)
     */
    public function change_password_required()
    {
        // Check if user is logged in
        if (!$this->session->userdata('user_id')) {
            redirect('login/');
            return;
        }

        // Check if user actually needs to change password
        if (!$this->session->userdata('must_change_password')) {
            // User doesn't need to change password, redirect to their dashboard
            switch($this->session->userdata('role_name')) {
                case 'system_admin':
                    redirect('admin/staff');
                    break;
                case 'bod':
                    redirect('bod/');
                    break;
                case 'line_manager':
                case 'warehouse_staff':
                case 'qc_staff':
                case 'technical_staff':
                case 'worker':
                    redirect('leader/');
                    break;
                default:
                    redirect('login/');
            }
            return;
        }

        // Determine which backend template to use based on role
        $role_name = $this->session->userdata('role_name');
        $template = 'admin/vbackend'; // default

        switch($role_name) {
            case 'system_admin':
            case 'bod':
                $template = 'admin/vbackend';
                break;
            case 'line_manager':
            case 'warehouse_staff':
            case 'qc_staff':
            case 'technical_staff':
            case 'worker':
                $template = 'leader/vbackend';
                break;
            default:
                $template = 'admin/vbackend';
        }

        // Load the change password view with full layout
        $data = [
            'content' => 'change_password_required_content',
            'navlink' => 'change_password' // for navigation highlighting
        ];

        $this->load->view($template, $data);
    }

    /**
     * Process password change
     */
    public function change_password_process()
    {
        // Must be POST request
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('login/change_password_required');
            return;
        }

        // Check if user is logged in
        if (!$this->session->userdata('user_id')) {
            redirect('login/');
            return;
        }

        $user_id = $this->session->userdata('user_id');
        $current_password = $this->input->post('current_password');
        $new_password = $this->input->post('new_password');
        $confirm_password = $this->input->post('confirm_password');

        // Validate inputs
        if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
            $this->session->set_flashdata('password_error', 'Vui lòng điền đầy đủ thông tin');
            redirect('login/change_password_required');
            return;
        }

        // Check new password length
        if (strlen($new_password) < 6) {
            $this->session->set_flashdata('password_error', 'Mật khẩu mới phải có ít nhất 6 ký tự');
            redirect('login/change_password_required');
            return;
        }

        // Check password strength (server-side validation)
        $password_strength = $this->checkPasswordStrength($new_password);
        if ($password_strength['level'] === 'weak') {
            $this->session->set_flashdata('password_error', 'Mật khẩu quá yếu. Vui lòng sử dụng mật khẩu mạnh hơn (tối thiểu 8 ký tự, bao gồm chữ hoa, chữ thường, số và ký tự đặc biệt)');
            redirect('login/change_password_required');
            return;
        }

        // Check if new password matches confirmation
        if ($new_password !== $confirm_password) {
            $this->session->set_flashdata('password_error', 'Mật khẩu xác nhận không khớp');
            redirect('login/change_password_required');
            return;
        }

        // Get user data
        $this->db->where('user_id', $user_id);
        $user = $this->db->get('user')->row();

        if (!$user) {
            $this->session->set_flashdata('password_error', 'Không tìm thấy thông tin người dùng');
            redirect('login/change_password_required');
            return;
        }

        // Verify current password (check both temp_password and actual password)
        $current_password_valid = false;
        
        // Check if matches temp_password (plaintext)
        if (!empty($user->temp_password) && $current_password === $user->temp_password) {
            $current_password_valid = true;
        }
        
        // Check if matches actual password (PLAINTEXT - UC6 specification)
        if ($current_password === $user->password) {
            $current_password_valid = true;
        }

        if (!$current_password_valid) {
            $this->session->set_flashdata('password_error', 'Mật khẩu hiện tại không đúng');
            redirect('login/change_password_required');
            return;
        }

        // Check if new password is same as current
        if ($current_password === $new_password) {
            $this->session->set_flashdata('password_error', 'Mật khẩu mới phải khác mật khẩu hiện tại');
            redirect('login/change_password_required');
            return;
        }

        // Update password in database (PLAINTEXT - UC6 specification)
        $update_data = array(
            'password' => $new_password,  // Store as plaintext, NOT MD5
            'temp_password' => NULL,
            'must_change_password' => 0,
            'updated_at' => date('Y-m-d H:i:s')
        );

        $this->db->where('user_id', $user_id);
        $update_result = $this->db->update('user', $update_data);

        if (!$update_result) {
            $this->session->set_flashdata('error', 'Có lỗi xảy ra khi cập nhật mật khẩu. Vui lòng thử lại');
            redirect('login/change_password_required');
            return;
        }

        // Log activity
        $this->login->log_activity(
            $user_id,
            $user->username,
            'change_password',
            'auth',
            'Đổi mật khẩu bắt buộc sau lần đăng nhập đầu tiên'
        );

        // Update session
        $this->session->set_userdata('must_change_password', 0);

        // Set success message only for system admin so only admin sees success toast after redirect
        if ($this->session->userdata('role_name') === 'system_admin') {
            $this->session->set_flashdata('success', 'Đổi mật khẩu thành công! Chào mừng bạn đến với hệ thống.');
        }

        // Redirect to appropriate dashboard based on role
        switch($this->session->userdata('role_name')) {
            case 'system_admin':
                redirect(site_url('admin/staff?msg=success'));
                break;
            case 'bod':
                redirect(site_url('bod/?msg=success'));
                break;
            case 'line_manager':
                redirect(site_url('leader/?msg=success'));
                break;
            case 'warehouse_staff':
                if (file_exists(APPPATH . 'controllers/Warehouse.php')) {
                    redirect(site_url('warehouse/?msg=success'));
                } else {
                    redirect(site_url('leader/?msg=success'));
                }
                break;
            case 'qc_staff':
                if (file_exists(APPPATH . 'controllers/Qc.php')) {
                    redirect(site_url('qc/?msg=success'));
                } else {
                    redirect(site_url('leader/?msg=success'));
                }
                break;
            case 'technical_staff':
                if (file_exists(APPPATH . 'controllers/Technical.php')) {
                    redirect(site_url('technical/?msg=success'));
                } else {
                    redirect(site_url('leader/?msg=success'));
                }
                break;
            case 'worker':
                if (file_exists(APPPATH . 'controllers/Worker.php')) {
                    redirect(site_url('worker/?msg=success'));
                } else {
                    redirect(site_url('leader/?msg=success'));
                }
                break;
            default:
                redirect('login/');
        }
    }

    /**
     * Check password strength
     * @param string $password
     * @return array
     */
    private function checkPasswordStrength($password) {
// Strong password: at least 8 chars, include upper, lower, number, special
        $length = strlen($password);
$hasUpper = preg_match('/[A-Z]/', $password);
        $hasLower = preg_match('/[a-z]/', $password);
        $hasDigit = preg_match('/[0-9]/', $password);
        $hasSpecial = preg_match('/[^a-zA-Z0-9]/', $password);

        if ($length >= 8 && $hasUpper && $hasLower && $hasDigit && $hasSpecial) {
            return array('score' => 5, 'level' => 'strong', 'checks' => array('length' => true, 'upper' => true, 'lower' => true, 'digit' => true, 'special' => true));
        }

            return array('score' => 0, 'level' => 'weak', 'checks' => array('length' => ($length >= 8), 'upper' => $hasUpper, 'lower' => $hasLower, 'digit' => $hasDigit, 'special' => $hasSpecial));
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
        $this->session->set_flashdata('login_success', 'Đăng xuất thành công!');
        redirect('login/');
    }
}
