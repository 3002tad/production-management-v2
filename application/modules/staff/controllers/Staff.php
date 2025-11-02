<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Staff extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        // Ensure loader can find module's models and views
        $this->load->add_package_path(APPPATH . 'modules/staff/');
        $this->load->model('StaffModel');
        // Load CrudModel helper for generateCode and other helpers
        if (!isset($this->crudModel)) {
            $this->load->model('CrudModel', 'crudModel');
        }
        $this->load->library('session');
        $this->load->helper('form');
        if ($this->session->userdata('role') !== 'leader') {
            redirect('login/');
        }
    }

    public function index()
    {
        $data = [
            'title' => 'Quản lý nhân viên',
            'staffs' => $this->StaffModel->getAll(),
            'content' => 'staff/views/list',
            'navlink' => 'staff',
        ];

        $this->load->view('leader/vbackend', $data);
    }

    public function add()
    {
        $data = [
            'title' => 'Thêm nhân viên mới',
            'content' => 'staff/views/add',
            'navlink' => 'staff',
        ];

        $this->load->view('leader/vbackend', $data);
    }

    public function addStaff()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('staff_name', 'Tên nhân viên', 'required');

        // skills optional, st_status optional (default 1)

        if ($this->form_validation->run() === FALSE) {
            $errors = '';
            if (function_exists('validation_errors')) {
                $errors = validation_errors();
            } else {
                $errors = $this->form_validation->error_string();
            }
            $data = [
                'title' => 'Thêm nhân viên mới',
                'content' => 'staff/views/add',
                'navlink' => 'staff',
                'validation_errors' => $errors,
            ];
            $this->load->view('leader/vbackend', $data);
            return;
        }

        $add = [
            'id_staff' => $this->crudModel->generateCode(1, 'id_staff', 'staff'),
            'staff_name' => trim($this->input->post('staff_name')),
            'phone' => trim($this->input->post('phone')),
            'email' => trim($this->input->post('email')),
            'st_status' => $this->input->post('st_status') !== null ? (int) $this->input->post('st_status') : 1,
        ];
        // Note: no skills field saved here (removed per request)

        if ($this->StaffModel->insert($add)) {
            $this->session->set_flashdata('success', 'Thêm nhân viên thành công');
            redirect('staff');
        } else {
            $this->session->set_flashdata('error', 'Không thể thêm nhân viên');
            redirect('staff/add');
        }
    }

    public function edit($id)
    {
        $staff = $this->StaffModel->getById($id);

        if (!$staff) {
            show_404();
        }

        $data = [
            'title' => 'Sửa thông tin nhân viên',
            'staff' => $staff,
            'content' => 'staff/views/edit',
            'navlink' => 'staff',
        ];

        $this->load->view('leader/vbackend', $data);
    }

    public function updateStaff()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('staff_name', 'Tên nhân viên', 'required');

        $id = $this->input->post('id_staff');

        if ($this->form_validation->run() === FALSE) {
            $errors = '';
            if (function_exists('validation_errors')) {
                $errors = validation_errors();
            } else {
                $errors = $this->form_validation->error_string();
            }
            $staff = $this->StaffModel->getById($id);
            $data = [
                'title' => 'Sửa thông tin nhân viên',
                'staff' => $staff,
                'content' => 'staff/views/edit',
                'navlink' => 'staff',
                'validation_errors' => $errors,
            ];
            $this->load->view('leader/vbackend', $data);
            return;
        }

        $update = [
            'staff_name' => trim($this->input->post('staff_name')),
            'phone' => trim($this->input->post('phone')),
            'email' => trim($this->input->post('email')),
            'st_status' => $this->input->post('st_status') !== null ? (int) $this->input->post('st_status') : 1,
        ];
        // Note: no skills field updated here (removed per request)

        if ($this->StaffModel->update($id, $update)) {
            $this->db->cache_delete_all();
            $this->session->set_flashdata('success', 'Cập nhật nhân viên thành công');
            redirect('staff');
        } else {
            $this->session->set_flashdata('error', 'Không thể cập nhật thông tin nhân viên');
            redirect('staff/edit/' . $id);
        }
    }

    public function delete($id)
    {
        if ($this->StaffModel->delete($id)) {
            $this->session->set_flashdata('success', 'Xóa nhân viên thành công');
        } else {
            $this->session->set_flashdata('error', 'Không thể xóa nhân viên');
        }
        redirect('staff');
    }

    /**
     * Deactivate (stop activity) a staff member (set st_status = 0)
     */
    public function deactivate($id)
    {
        $update = ['st_status' => 0];
        if ($this->StaffModel->update($id, $update)) {
            $this->session->set_flashdata('success', 'Ngừng hoạt động nhân viên thành công');
        } else {
            $this->session->set_flashdata('error', 'Không thể ngừng hoạt động nhân viên');
        }
        redirect('staff');
    }

    public function search()
    {
        $keyword = $this->input->get('keyword');
        $data = [
            'title' => 'Tìm kiếm nhân viên',
            'staffs' => $this->StaffModel->search($keyword),
            'content' => 'staff/views/list',
            'navlink' => 'staff',
            'keyword' => $keyword,
        ];

        $this->load->view('leader/vbackend', $data);
    }
}