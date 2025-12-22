<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Staffs extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('StaffManagementModel', 'staffModel');
        $this->load->library('session');
        $this->load->helper(array('form','url'));
        
        // Check if user is logged in
        if (!$this->session->userdata('user_id')) {
            redirect('login/');
        }
        
        // RBAC: Check if user is leader or admin
        $role = $this->session->userdata('role');
        if (empty($role)) {
            $role = $this->session->userdata('role_name');
        }
        $role = strtolower(trim((string)$role));
        
        // Allowed roles for Staff Management
        $allowed_roles = ['leader', 'line_manager', 'admin', 'bod', 'system_admin'];
        
        if (!in_array($role, $allowed_roles, true)) {
            log_message('error', 'UC3_QLNS access denied for user ' . $this->session->userdata('username') . ' with role=' . $role);
            show_error('Chỉ trưởng dây chuyền hoặc admin được phép quản lý nhân sự. Your role: ' . var_export($role, true), 403, 'Forbidden');
        }
    }

    protected function _is_leader()
    {
        $role = $this->session->userdata('role');
        return ($role === 'leader' || $role === 'line_manager');
    }

    protected function _is_read_only()
    {
        $role = $this->session->userdata('role');
        // Admin, BOD, System_admin chỉ có quyền xem (read-only)
        return ($role === 'admin' || $role === 'bod' || $role === 'system_admin');
    }

    public function index()
    {
        // Get filters từ GET params
        $filters = [
            'department' => $this->input->get('department'),
            'position'   => $this->input->get('position'),
            'status'     => $this->input->get('status'),
            'search'     => $this->input->get('search')
        ];

        $data = [
            'staff'       => $this->staffModel->getAllStaff($filters),
            'departments' => $this->staffModel->getDepartments(),
            'positions'   => $this->staffModel->getPositions(),
            'statistics'  => $this->staffModel->getStatistics(),
            'readonly'    => $this->_is_read_only(),
            'content'     => 'uc3_qlns/list',
            'navlink'     => 'staff'
        ];

        $this->load->view('leader/VBackend', $data);
    }

    public function create()
    {
        if (! $this->_is_leader()) {
            show_error('Chỉ trưởng dây chuyền/phó trưởng được phép tạo nhân sự.', 403);
            return;
        }

        if ($this->input->method() === 'post') {
            $payload = $this->input->post();

            // basic validation
            if (empty($payload['staff_name'])) {
                $data['error'] = 'Vui lòng điền tên nhân sự.';
                $data['departments'] = $this->staffModel->getDepartments();
                $data['positions'] = $this->staffModel->getPositions();
                $data['content'] = 'uc3_qlns/form';
                $this->load->view('leader/VBackend', $data);
                return;
            }

            // email is required and must be unique
            if (empty($payload['email'])) {
                $data['error'] = 'Email là bắt buộc.';
                $data['old'] = $payload;
                $data['departments'] = $this->staffModel->getDepartments();
                $data['positions'] = $this->staffModel->getPositions();
                $data['content'] = 'uc3_qlns/form';
                $this->load->view('leader/VBackend', $data);
                return;
            }
            if (!filter_var($payload['email'], FILTER_VALIDATE_EMAIL)) {
                $data['error'] = 'Email không hợp lệ.';
                $data['old'] = $payload;
                $data['departments'] = $this->staffModel->getDepartments();
                $data['positions'] = $this->staffModel->getPositions();
                $data['content'] = 'uc3_qlns/form';
                $this->load->view('leader/VBackend', $data);
                return;
            }
            if ($this->staffModel->exists_email($payload['email'])) {
                $data['error'] = 'Email đã có trong hệ thống.';
                $data['duplicate_email'] = true;
                $data['old'] = $payload;
                $data['departments'] = $this->staffModel->getDepartments();
                $data['positions'] = $this->staffModel->getPositions();
                $data['content'] = 'uc3_qlns/form';
                $this->load->view('leader/VBackend', $data);
                return;
            }

            // phone is required and must start with 0 and be exactly 10 digits
            if (empty($payload['phone'])) {
                $data['error'] = 'Số điện thoại là bắt buộc.';
                $data['old'] = $payload;
                $data['departments'] = $this->staffModel->getDepartments();
                $data['positions'] = $this->staffModel->getPositions();
                $data['content'] = 'uc3_qlns/form';
                $this->load->view('leader/VBackend', $data);
                return;
            }
            if (!preg_match('/^0[0-9]{9}$/', $payload['phone'])) {
                $data['error'] = 'Số điện thoại không hợp lệ: phải bắt đầu bằng 0 và đúng 10 chữ số (chỉ gồm số).';
                $data['old'] = $payload;
                $data['departments'] = $this->staffModel->getDepartments();
                $data['positions'] = $this->staffModel->getPositions();
                $data['content'] = 'uc3_qlns/form';
                $this->load->view('leader/VBackend', $data);
                return;
            }
            if ($this->staffModel->exists_phone($payload['phone'])) {
                $data['error'] = 'Số điện thoại đã tồn tại trong hệ thống.';
                $data['duplicate_phone'] = true;
                $data['old'] = $payload;
                $data['departments'] = $this->staffModel->getDepartments();
                $data['positions'] = $this->staffModel->getPositions();
                $data['content'] = 'uc3_qlns/form';
                $this->load->view('leader/VBackend', $data);
                return;
            }

            $data = [
                'staff_name' => trim($payload['staff_name']),
                'email'      => trim($payload['email']),
                'phone'      => trim($payload['phone']),
                'department' => $payload['department'] ?: 'Chưa Phân Loại',
                'position'   => $payload['position'] ?: 'Chưa Phân Loại',
                'st_status'  => (int)($payload['st_status'] ?: 1)
            ];

            $result = $this->staffModel->createStaff($data, $this->session->userdata('user_id'));

            if ($result['success']) {
                $this->session->set_flashdata('success', $result['message']);
                redirect('UC3_QLNS/Staffs');
            } else {
                $this->session->set_flashdata('error', $result['message']);
                redirect('UC3_QLNS/Staffs/create');
            }
        }

        $data = [
            'departments' => $this->staffModel->getDepartments(),
            'positions'   => $this->staffModel->getPositions(),
            'content'     => 'uc3_qlns/form'
        ];
        $this->load->view('leader/VBackend', $data);
    }

    public function edit($id = null)
    {
        if (! $this->_is_leader()) {
            show_error('Chỉ trưởng dây chuyền/phó trưởng được phép cập nhật nhân sự.', 403);
            return;
        }

        $staff = $this->staffModel->getStaffById($id);
        if (!$staff) {
            show_error('Nhân sự không tồn tại.', 404);
            return;
        }

        if ($this->input->method() === 'post') {
            $payload = $this->input->post();
            file_put_contents(APPPATH . 'logs/staffs_debug.log', "\n[" . date('Y-m-d H:i:s') . "] EDIT POST for ID $id - Phone: " . ($payload['phone'] ?? 'empty') . "\n", FILE_APPEND);

            // validate email on update: must be valid and unique excluding current
            if (!empty($payload['email'])) {
                if (!filter_var($payload['email'], FILTER_VALIDATE_EMAIL)) {
                    $data['error'] = 'Email không hợp lệ.';
                    $data['old'] = $payload;
                    $data['staff'] = $staff;
                    $data['departments'] = $this->staffModel->getDepartments();
                    $data['positions'] = $this->staffModel->getPositions();
                    $data['content'] = 'uc3_qlns/form';
                    $this->load->view('leader/VBackend', $data);
                    return;
                }
                // check duplicate excluding current staff id
                if ($this->staffModel->exists_email($payload['email'], $id)) {
                    $data['error'] = 'Email bị trùng trong hệ thống.';
                    $data['duplicate_email'] = true;
                    $data['old'] = $payload;
                    $data['staff'] = $staff;
                    $data['departments'] = $this->staffModel->getDepartments();
                    $data['positions'] = $this->staffModel->getPositions();
                    $data['content'] = 'uc3_qlns/form';
                    $this->load->view('leader/VBackend', $data);
                    return;
                }
            }

            // validate phone on update: must be valid and unique excluding current
            if (!empty($payload['phone'])) {
                if (!preg_match('/^0[0-9]{9}$/', $payload['phone'])) {
                    $data['error'] = 'Số điện thoại không hợp lệ: phải bắt đầu bằng 0 và đúng 10 chữ số (chỉ gồm số).';
                    $data['old'] = $payload;
                    $data['staff'] = $staff;
                    $data['departments'] = $this->staffModel->getDepartments();
                    $data['positions'] = $this->staffModel->getPositions();
                    $data['content'] = 'uc3_qlns/form';
                    $this->load->view('leader/VBackend', $data);
                    return;
                }
                // check duplicate excluding current staff id
                if ($this->staffModel->exists_phone($payload['phone'], $id)) {
                    file_put_contents(APPPATH . 'logs/staffs_debug.log', "[" . date('Y-m-d H:i:s') . "] EDIT - Duplicate phone detected for id $id: " . $payload['phone'] . "\n", FILE_APPEND);
                    $data['error'] = 'Số điện thoại đã tồn tại trong hệ thống.';
                    $data['duplicate_phone'] = true;
                    $data['old'] = $payload;
                    $data['staff'] = $staff;
                    $data['departments'] = $this->staffModel->getDepartments();
                    $data['positions'] = $this->staffModel->getPositions();
                    $data['content'] = 'uc3_qlns/form';
                    $this->load->view('leader/VBackend', $data);
                    return;
                }
            }

            $data = [
                'staff_name' => trim($payload['staff_name']),
                'email'      => trim($payload['email']),
                'phone'      => trim($payload['phone']),
                'department' => $payload['department'] ?: 'Chưa Phân Loại',
                'position'   => $payload['position'] ?: 'Chưa Phân Loại',
                'st_status'  => (int)($payload['st_status'] ?: 1)
            ];

            $result = $this->staffModel->updateStaff($id, $data, $this->session->userdata('user_id'));

            if ($result['success']) {
                $this->session->set_flashdata('success', $result['message']);
                redirect('UC3_QLNS/Staffs');
            } else {
                $this->session->set_flashdata('error', $result['message']);
                redirect('UC3_QLNS/Staffs/edit/' . $id);
            }
        }

        $data = [
            'staff'       => $staff,
            'departments' => $this->staffModel->getDepartments(),
            'positions'   => $this->staffModel->getPositions(),
            'content'     => 'uc3_qlns/form'
        ];
        $this->load->view('leader/VBackend', $data);
    }

    public function deactivate($id = null)
    {
        if (! $this->_is_leader()) {
            show_error('Chỉ trưởng dây chuyền/phó trưởng được phép thay đổi trạng thái.', 403);
            return;
        }

        $staff = $this->staffModel->getStaffById($id);
        if (!$staff) {
            show_error('Nhân sự không tồn tại.', 404);
            return;
        }

        // Check if staff has user account
        if ($this->staffModel->hasUserAccount($id)) {
            $this->session->set_flashdata('error', 'Không thể xóa nhân viên đã có tài khoản user.');
            redirect('UC3_QLNS/Staffs');
        }

        $result = $this->staffModel->deleteStaff($id, $this->session->userdata('user_id'));

        if ($result['success']) {
            $this->session->set_flashdata('success', $result['message']);
        } else {
            $this->session->set_flashdata('error', $result['message']);
        }

        redirect('UC3_QLNS/Staffs');
    }

}

?>
