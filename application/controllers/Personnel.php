<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Personnel extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('PersonnelModel', 'personnel');
        $this->load->library('session');
        
        // Check if user is logged in
        if (!$this->session->userdata('user_id')) {
            redirect('login');
        }
    }

    public function index()
    {
        $data['title'] = 'Quản lý nhân sự';
        $data['personnel_list'] = $this->personnel->get_all_personnel();
        
        $this->load->view('admin/header', $data);
        $this->load->view('admin/personnel/list', $data);
        $this->load->view('admin/footer');
    }

    public function add()
    {
        if ($this->input->post()) {
            $data = [
                'name' => $this->input->post('name'),
                'position' => $this->input->post('position'),
                'department' => $this->input->post('department'),
                'phone' => $this->input->post('phone'),
                'email' => $this->input->post('email'),
                'address' => $this->input->post('address'),
                'status' => $this->input->post('status')
            ];

            if ($this->personnel->add_personnel($data)) {
                $this->session->set_flashdata('success', 'Thêm nhân sự thành công');
            } else {
                $this->session->set_flashdata('error', 'Thêm nhân sự thất bại');
            }
            redirect('personnel');
        }

        $data['title'] = 'Thêm nhân sự mới';
        $this->load->view('admin/header', $data);
        $this->load->view('admin/personnel/add', $data);
        $this->load->view('admin/footer');
    }

    public function edit($id)
    {
        if ($this->input->post()) {
            $data = [
                'name' => $this->input->post('name'),
                'position' => $this->input->post('position'),
                'department' => $this->input->post('department'),
                'phone' => $this->input->post('phone'),
                'email' => $this->input->post('email'),
                'address' => $this->input->post('address'),
                'status' => $this->input->post('status')
            ];

            if ($this->personnel->update_personnel($id, $data)) {
                $this->session->set_flashdata('success', 'Cập nhật thành công');
            } else {
                $this->session->set_flashdata('error', 'Cập nhật thất bại');
            }
            redirect('personnel');
        }

        $data['title'] = 'Chỉnh sửa thông tin nhân sự';
        $data['personnel'] = $this->personnel->get_personnel($id);
        $this->load->view('admin/header', $data);
        $this->load->view('admin/personnel/edit', $data);
        $this->load->view('admin/footer');
    }

    public function delete($id)
    {
        if ($this->personnel->delete_personnel($id)) {
            $this->session->set_flashdata('success', 'Xóa nhân sự thành công');
        } else {
            $this->session->set_flashdata('error', 'Xóa nhân sự thất bại');
        }
        redirect('personnel');
    }
}