<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class StaffController extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('admin/StaffManagementModel', 'staffModel');
        $this->load->library('session');
        $this->load->helper(array('form','url'));
        
        // Check if user is logged in
        if (!$this->session->userdata('user_id')) {
            redirect('login/');
        }
        
        // RBAC: Check if user is system_admin (role_id = 4)
        $role_id = $this->session->userdata('role_id');
        if ($role_id != 4) {
            log_message('error', 'Staff management access denied for user ' . $this->session->userdata('username') . ' with role_id=' . $role_id);
            show_error('Chỉ System Admin mới được phép quản lý nhân viên. Your role_id: ' . var_export($role_id, true), 403, 'Forbidden');
        }
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
            'content'     => 'admin/staff/staff_list',
            'navlink'     => 'staff'
        ];

        $this->load->view('admin/vbackend', $data);
    }

    public function add()
    {
        $data = [
            'departments' => $this->staffModel->getDepartments(),
            'positions'   => $this->staffModel->getPositions(),
            'content'     => 'admin/staff/staff_add',
            'navlink'     => 'staff'
        ];
        $this->load->view('admin/vbackend', $data);
    }

    public function add_process()
    {
        if ($this->input->method() !== 'post') {
            redirect('admin/staff');
        }

        $payload = $this->input->post();

        // Validation
        if (empty(trim($payload['staff_name']))) {
            $this->session->set_flashdata('error', 'Tên nhân viên là bắt buộc.');
            redirect('admin/staff/add');
        }

        if (empty($payload['email']) || !filter_var($payload['email'], FILTER_VALIDATE_EMAIL)) {
            $this->session->set_flashdata('error', 'Email hợp lệ là bắt buộc.');
            redirect('admin/staff/add');
        }

        if ($this->staffModel->exists_email($payload['email'])) {
            $this->session->set_flashdata('error', 'Email đã tồn tại trong hệ thống.');
            redirect('admin/staff/add');
        }

        if (!empty($payload['phone']) && !preg_match('/^0[0-9]{9}$/', $payload['phone'])) {
            $this->session->set_flashdata('error', 'Số điện thoại không hợp lệ.');
            redirect('admin/staff/add');
        }

        if (!empty($payload['phone']) && $this->staffModel->exists_phone($payload['phone'])) {
            $this->session->set_flashdata('error', 'Số điện thoại đã tồn tại trong hệ thống.');
            redirect('admin/staff/add');
        }

        $data = [
            'staff_name' => trim($payload['staff_name']),
            'email'      => trim($payload['email']),
            'phone'      => trim($payload['phone']) ?: null,
            'department' => $payload['department'] ?: null,
            'position'   => $payload['position'] ?: null,
            'st_status'  => (int)($payload['st_status'] ?: 1)
        ];

        $result = $this->staffModel->createStaff($data, $this->session->userdata('user_id'));

        if ($result['success']) {
            $this->session->set_flashdata('success', $result['message']);
            redirect('admin/staff');
        } else {
            $this->session->set_flashdata('error', $result['message']);
            redirect('admin/staff/add');
        }
    }

    public function edit($id = null)
    {
        $staff = $this->staffModel->getStaffById($id);
        if (!$staff) {
            show_error('Nhân viên không tồn tại.', 404);
        }

        $data = [
            'staff'       => $staff,
            'departments' => $this->staffModel->getDepartments(),
            'positions'   => $this->staffModel->getPositions(),
            'content'     => 'admin/staff/staff_edit',
            'navlink'     => 'staff'
        ];
        $this->load->view('admin/vbackend', $data);
    }

    public function edit_process()
    {
        if ($this->input->method() !== 'post') {
            redirect('admin/staff');
        }

        $payload = $this->input->post();
        $id = $payload['id_staff'];

        $staff = $this->staffModel->getStaffById($id);
        if (!$staff) {
            $this->session->set_flashdata('error', 'Nhân viên không tồn tại.');
            redirect('admin/staff');
        }

        // Validation
        if (empty(trim($payload['staff_name']))) {
            $this->session->set_flashdata('error', 'Tên nhân viên là bắt buộc.');
            redirect('admin/staff/edit/' . $id);
        }

        if (empty($payload['email']) || !filter_var($payload['email'], FILTER_VALIDATE_EMAIL)) {
            $this->session->set_flashdata('error', 'Email hợp lệ là bắt buộc.');
            redirect('admin/staff/edit/' . $id);
        }

        if ($this->staffModel->exists_email($payload['email'], $id)) {
            $this->session->set_flashdata('error', 'Email đã tồn tại trong hệ thống.');
            redirect('admin/staff/edit/' . $id);
        }

        if (!empty($payload['phone']) && !preg_match('/^0[0-9]{9}$/', $payload['phone'])) {
            $this->session->set_flashdata('error', 'Số điện thoại không hợp lệ.');
            redirect('admin/staff/edit/' . $id);
        }

        if (!empty($payload['phone']) && $this->staffModel->exists_phone($payload['phone'], $id)) {
            $this->session->set_flashdata('error', 'Số điện thoại đã tồn tại trong hệ thống.');
            redirect('admin/staff/edit/' . $id);
        }

        $data = [
            'staff_name' => trim($payload['staff_name']),
            'email'      => trim($payload['email']),
            'phone'      => trim($payload['phone']) ?: null,
            'department' => $payload['department'] ?: null,
            'position'   => $payload['position'] ?: null,
            'st_status'  => (int)($payload['st_status'] ?: 1)
        ];

        $result = $this->staffModel->updateStaff($id, $data, $this->session->userdata('user_id'));

        if ($result['success']) {
            $this->session->set_flashdata('success', $result['message']);
            redirect('admin/staff');
        } else {
            $this->session->set_flashdata('error', $result['message']);
            redirect('admin/staff/edit/' . $id);
        }
    }

    public function delete($id = null)
    {
        $staff = $this->staffModel->getStaffById($id);
        if (!$staff) {
            $this->session->set_flashdata('error', 'Nhân viên không tồn tại.');
            redirect('admin/staff');
        }

        // Check if staff has user account
        if ($this->staffModel->hasUserAccount($id)) {
            $this->session->set_flashdata('error', 'Không thể xóa nhân viên đã có tài khoản user.');
            redirect('admin/staff');
        }

        $result = $this->staffModel->deleteStaff($id, $this->session->userdata('user_id'));

        if ($result['success']) {
            $this->session->set_flashdata('success', $result['message']);
        } else {
            $this->session->set_flashdata('error', $result['message']);
        }

        redirect('admin/staff');
    }
}