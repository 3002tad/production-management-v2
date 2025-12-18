<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Staff extends CI_Controller {
    
    public function __construct() {
        parent::__construct();
        // Kiểm tra đăng nhập và phân quyền
        if(!$this->session->userdata('logged_in')) {
            redirect('login');
        }
        if($this->session->userdata('role') != 'admin') {
            show_error('Unauthorized access', 403);
        }
        // Make module models/views available
        $this->load->add_package_path(APPPATH . 'modules/staff/');
        $this->load->model('StaffModel');
    }

    // Hiển thị danh sách nhân sự (chỉ xem)
    public function index() {
        $data['title'] = 'Danh sách nhân sự';
        $data['staffs'] = $this->StaffModel->getAll();
        $this->load->view('admin/templates/header', $data);
        $this->load->view('admin/staff/list', $data);
        $this->load->view('admin/templates/footer');
    }

    // Tìm kiếm nhân sự
    public function search() {
        $keyword = $this->input->get('keyword');
        $data['title'] = 'Tìm kiếm nhân sự';
        $data['staffs'] = $this->StaffModel->search($keyword);
        $data['keyword'] = $keyword;
        
        $this->load->view('admin/templates/header', $data);
        $this->load->view('admin/staff/list', $data);
        $this->load->view('admin/templates/footer');
    }
}