<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Staff extends CI_Controller {
    
    public function __construct() {
        parent::__construct();
        // Kiểm tra đăng nhập và phân quyền
        if(!$this->session->userdata('logged_in')) {
            redirect('login');
        }
        if($this->session->userdata('role') != 'leader') {
            show_error('Unauthorized access', 403);
        }
        // Make module models/views available
        $this->load->add_package_path(APPPATH . 'modules/staff/');
        $this->load->model('StaffModel');
    }

    // Hiển thị danh sách nhân sự
    public function index() {
        $data['title'] = 'Quản lý nhân sự';
        $data['staffs'] = $this->StaffModel->getAll();
        $this->load->view('leader/templates/header', $data);
        $this->load->view('leader/staff/list', $data);
        $this->load->view('leader/templates/footer');
    }

    // Form thêm nhân sự mới
    public function add() {
        $data['title'] = 'Thêm nhân sự mới';
        
        if($this->input->post()) {
            $this->load->library('form_validation');
            
            // Quy tắc validation
            $this->form_validation->set_rules('name', 'Tên nhân viên', 'required');
            $this->form_validation->set_rules('position', 'Chức vụ', 'required');
            $this->form_validation->set_rules('department', 'Phòng ban', 'required');
            
            if($this->form_validation->run()) {
                $data = array(
                    'name' => $this->input->post('name'),
                    'position' => $this->input->post('position'),
                    'department' => $this->input->post('department'),
                    'phone' => $this->input->post('phone'),
                    'email' => $this->input->post('email'),
                    'address' => $this->input->post('address'),
                    'created_at' => date('Y-m-d H:i:s')
                );
                
                $this->StaffModel->insert($data);
                $this->session->set_flashdata('success', 'Thêm nhân sự thành công');
                redirect('leader/staff');
            }
        }
        
        $this->load->view('leader/templates/header', $data);
        $this->load->view('leader/staff/add', $data);
        $this->load->view('leader/templates/footer');
    }

    // Form sửa thông tin nhân sự
    public function edit($id) {
        $data['title'] = 'Sửa thông tin nhân sự';
        $data['staff'] = $this->StaffModel->getById($id);
        
        if(!$data['staff']) {
            show_404();
        }
        
        if($this->input->post()) {
            $this->load->library('form_validation');
            
            // Quy tắc validation
            $this->form_validation->set_rules('name', 'Tên nhân viên', 'required');
            $this->form_validation->set_rules('position', 'Chức vụ', 'required');
            $this->form_validation->set_rules('department', 'Phòng ban', 'required');
            
            if($this->form_validation->run()) {
                $data = array(
                    'name' => $this->input->post('name'),
                    'position' => $this->input->post('position'),
                    'department' => $this->input->post('department'),
                    'phone' => $this->input->post('phone'),
                    'email' => $this->input->post('email'),
                    'address' => $this->input->post('address'),
                    'updated_at' => date('Y-m-d H:i:s')
                );
                
                $this->StaffModel->update($id, $data);
                $this->session->set_flashdata('success', 'Cập nhật thông tin thành công');
                redirect('leader/staff');
            }
        }
        
        $this->load->view('leader/templates/header', $data);
        $this->load->view('leader/staff/edit', $data);
        $this->load->view('leader/templates/footer');
    }

    // Xóa nhân sự
    public function delete($id) {
        $staff = $this->StaffModel->getById($id);
        
        if(!$staff) {
            show_404();
        }
        
        $this->StaffModel->delete($id);
        $this->session->set_flashdata('success', 'Xóa nhân sự thành công');
        redirect('leader/staff');
    }

    // Tìm kiếm nhân sự
    public function search() {
        $keyword = $this->input->get('keyword');
        $data['title'] = 'Tìm kiếm nhân sự';
        $data['staffs'] = $this->StaffModel->search($keyword);
        $data['keyword'] = $keyword;
        
        $this->load->view('leader/templates/header', $data);
        $this->load->view('leader/staff/list', $data);
        $this->load->view('leader/templates/footer');
    }
}