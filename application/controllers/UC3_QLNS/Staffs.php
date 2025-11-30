<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Staffs extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Staff_model');
        $this->load->library('session');
        $this->load->helper(array('form','url'));
        
        // Check if user is logged in
        if (!$this->session->userdata('user_id')) {
            redirect('login/');
        }
        
        // RBAC: Check if user is leader
        $role = $this->session->userdata('role');
        if (empty($role)) {
            $role = $this->session->userdata('role_name');
        }
        $role = strtolower(trim((string)$role));
        
        // Allowed roles for Staff Management
        $allowed_roles = ['leader', 'line_manager', 'admin', 'bod', 'system_admin'];
        
        if (!in_array($role, $allowed_roles, true)) {
            log_message('error', 'UC3_QLNS access denied for user ' . $this->session->userdata('username') . ' with role=' . $role);
            show_error('Chỉ trưởng dây chuyền được phép quản lý nhân sự. Your role: ' . var_export($role, true), 403, 'Forbidden');
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
        // list staffs, allow filtering by code or skill
        $code = $this->input->get('code');
        $skill = $this->input->get('skill');

        $results = $this->Staff_model->search($code, $skill);

        if (($this->input->get('code') || $this->input->get('skill')) && empty($results)) {
            $data['error'] = 'Mã nhân sự hoặc kỹ năng không tồn tại.';
            $data['show_retry'] = true;
        }

        $data['staffs'] = $results;
        $data['readonly'] = $this->_is_read_only();
        $this->load->view('uc3_qlns/list', $data);
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
            if (empty($payload['code']) || empty($payload['name'])) {
                $data['error'] = 'Vui lòng điền đầy đủ mã và tên nhân sự.';
                $this->load->view('uc3_qlns/form', $data);
                return;
            }

            // check duplicate code
            if ($this->Staff_model->exists_code($payload['code'])) {
                $data['error'] = 'Mã nhân sự đã tồn tại.';
                $data['duplicate_code'] = true;
                $data['old'] = $payload;
                $this->load->view('uc3_qlns/form', $data);
                return;
            }

            // phone is required and must start with 0 and be exactly 10 digits
            if (empty($payload['phone'])) {
                $data['error'] = 'Số điện thoại là bắt buộc.';
                $data['old'] = $payload;
                $this->load->view('uc3_qlns/form', $data);
                return;
            }
            if (!preg_match('/^0[0-9]{9}$/', $payload['phone'])) {
                $data['error'] = 'Số điện thoại không hợp lệ: phải bắt đầu bằng 0 và đúng 10 chữ số (chỉ gồm số).';
                $data['old'] = $payload;
                $this->load->view('uc3_qlns/form', $data);
                return;
            }
            // duplicate check
            if ($this->Staff_model->exists_phone($payload['phone'])) {
                $data['error'] = 'Số điện thoại đã có trong hệ thống.';
                $data['duplicate_phone'] = true;
                $data['old'] = $payload;
                $this->load->view('uc3_qlns/form', $data);
                return;
            }

            $ok = $this->Staff_model->create($payload);
            if ($ok) {
                redirect('UC3_QLNS/Staffs');
            }
            $data['error'] = 'Lỗi hệ thống khi tạo nhân sự.';
            $this->load->view('uc3_qlns/form', $data);
            return;
        }

        $this->load->view('uc3_qlns/form');
    }

    public function edit($id = null)
    {
        if (! $this->_is_leader()) {
            show_error('Chỉ trưởng dây chuyền/phó trưởng được phép cập nhật nhân sự.', 403);
            return;
        }

        $staff = $this->Staff_model->get($id);
        if (!$staff) {
            show_error('Nhân sự không tồn tại.', 404);
            return;
        }

        if ($this->input->method() === 'post') {
            $payload = $this->input->post();

            // logic checks: if updating code to existing code (other than self)
            if (!empty($payload['code']) && $payload['code'] !== $staff->code && $this->Staff_model->exists_code($payload['code'])) {
                $data['error'] = 'Mã nhân sự bị trùng.';
                $data['show_retry'] = true;
                $data['staff'] = $staff;
                $this->load->view('uc3_qlns/form', $data);
                return;
            }

            // validate phone on update: must start with 0 and be exactly 10 digits
            if (!empty($payload['phone'])) {
                if (!preg_match('/^0[0-9]{9}$/', $payload['phone'])) {
                    $data['error'] = 'Số điện thoại không hợp lệ: phải bắt đầu bằng 0 và đúng 10 chữ số (chỉ gồm số).';
                    $data['old'] = $payload;
                    $data['staff'] = $staff;
                    $this->load->view('uc3_qlns/form', $data);
                    return;
                }
                // check duplicate excluding current staff id
                if ($this->Staff_model->exists_phone($payload['phone'], $id)) {
                    $data['error'] = 'Số điện thoại bị trùng trong hệ thống.';
                    $data['duplicate_phone'] = true;
                    $data['old'] = $payload;
                    $data['staff'] = $staff;
                    $this->load->view('uc3_qlns/form', $data);
                    return;
                }
            }

            // check skill exists (optional) — allow new skills? requirement: check and error if skill not in system
            if (!empty($payload['skill']) && ! $this->Staff_model->skill_exists($payload['skill'])) {
                $data['error'] = 'Kỹ năng chưa có trong hệ thống.';
                $data['show_retry'] = true;
                $data['staff'] = $staff;
                $this->load->view('uc3_qlns/form', $data);
                return;
            }

            $ok = $this->Staff_model->update($id, $payload);
            if ($ok) {
                redirect('UC3_QLNS/Staffs');
            }

            $data['error'] = 'Lỗi khi cập nhật.';
            $data['staff'] = $staff;
            $this->load->view('uc3_qlns/form', $data);
            return;
        }

        $data['staff'] = $staff;
        $this->load->view('uc3_qlns/form', $data);
    }

    public function deactivate($id = null)
    {
        if (! $this->_is_leader()) {
            show_error('Chỉ trưởng dây chuyền/phó trưởng được phép thay đổi trạng thái.', 403);
            return;
        }

        $staff = $this->Staff_model->get($id);
        if (!$staff) {
            show_error('Nhân sự không tồn tại.', 404);
            return;
        }

        // check assignments
        $has_assign = $this->Staff_model->has_active_assignments($id);
        if (!$has_assign) {
            // No assignments: ask user whether to deactivate or leave active and allow new assignment
            $data['staff'] = $staff;
            $this->load->view('uc3_qlns/deactivate_confirm', $data);
            return;
        }

        // There are assignments: do not deactivate automatically
        $this->session->set_flashdata('error', 'Nhân sự còn phân công, không thể ngừng hoạt động.');
        redirect('UC3_QLNS/Staffs');
    }

}

?>
