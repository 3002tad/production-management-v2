<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class HRController extends CI_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->load->model('CrudModel');
        $this->load->library('session');
        
        // Check if user is logged in
        if (!$this->session->userdata('logged_in')) {
            redirect('login');
        }
    }

    public function index() {
        $data['title'] = 'Quản lý Nhân sự';
        $data['staff_list'] = $this->CrudModel->get_all('user');
        $data['roles'] = $this->CrudModel->get_all('roles');
        
        $this->load->view('admin/header', $data);
        $this->load->view('admin/staff/index', $data);
        $this->load->view('admin/footer');
    }

    public function add() {
        if ($this->input->post()) {
            // Tạo staff_id tự động
            $last_staff = $this->db->select('staff_id')
                                 ->from('user')
                                 ->order_by('staff_id', 'DESC')
                                 ->limit(1)
                                 ->get()
                                 ->row();
            
            $new_staff_id = $last_staff ? intval(substr($last_staff->staff_id, 3)) + 1 : 1;
            $staff_id = 'STF' . str_pad($new_staff_id, 4, '0', STR_PAD_LEFT);
            
            $data = array(
                'username' => $this->input->post('username'),
                'password' => $this->input->post('password'),
                'staff_id' => $staff_id,
                'role_id' => $this->input->post('role_id'),
                'full_name' => $this->input->post('full_name'),
                'email' => $this->input->post('email'),
                'phone' => $this->input->post('phone'),
                'is_active' => 1,
                'created_by' => $this->session->userdata('staff_id'),
                'created_at' => date('Y-m-d H:i:s')
            );

            $insert_id = $this->CrudModel->insert('user', $data);
            
            if ($insert_id) {
                $this->session->set_flashdata('success', 'Thêm nhân viên thành công');
            } else {
                $this->session->set_flashdata('error', 'Có lỗi xảy ra');
            }
            redirect('hr');
        }

        $data['title'] = 'Thêm Nhân viên Mới';
        $data['roles'] = $this->CrudModel->get_all('roles');
        
        $this->load->view('admin/header', $data);
        $this->load->view('admin/staff/form', $data);
        $this->load->view('admin/footer');
    }

    public function edit($id) {
        if ($this->input->post()) {
            $data = array(
                'role_id' => $this->input->post('role_id'),
                'full_name' => $this->input->post('full_name'),
                'email' => $this->input->post('email'),
                'phone' => $this->input->post('phone'),
                'updated_at' => date('Y-m-d H:i:s')
            );

            // Only update password if provided
            if ($this->input->post('password')) {
                $data['password'] = $this->input->post('password');
            }

            $success = $this->CrudModel->update('user', array('staff_id' => $id), $data);
            
            if ($success) {
                $this->session->set_flashdata('success', 'Cập nhật thông tin thành công');
            } else {
                $this->session->set_flashdata('error', 'Có lỗi xảy ra');
            }
            redirect('hr');
        }

        $data['title'] = 'Chỉnh sửa Thông tin Nhân viên';
        $data['staff'] = $this->CrudModel->get_by_id('user', $id);
        $data['roles'] = $this->CrudModel->get_all('roles');
        
        $this->load->view('admin/header', $data);
        $this->load->view('admin/staff/form', $data);
        $this->load->view('admin/footer');
    }

    public function delete($staff_id) {
        $success = $this->CrudModel->delete('user', array('staff_id' => $staff_id));
        
        if ($success) {
            $this->session->set_flashdata('success', 'Xóa nhân viên thành công');
        } else {
            $this->session->set_flashdata('error', 'Có lỗi xảy ra');
        }
        redirect('hr');
    }

    public function toggle_status($staff_id) {
        $user = $this->CrudModel->get_where('user', array('staff_id' => $staff_id))[0];
        $data = array(
            'is_active' => !$user->is_active,
            'updated_at' => date('Y-m-d H:i:s')
        );
        
        $success = $this->CrudModel->update('user', array('staff_id' => $staff_id), $data);
        
        if ($success) {
            $this->session->set_flashdata('success', 'Cập nhật trạng thái thành công');
        } else {
            $this->session->set_flashdata('error', 'Có lỗi xảy ra');
        }
        redirect('hr');
    }

    public function view($staff_id) {
        $data['title'] = 'Thông tin Chi tiết Nhân viên';
        $data['staff'] = $this->CrudModel->get_where('user', array('staff_id' => $staff_id))[0];
        $data['role'] = $this->CrudModel->get_by_id('roles', $data['staff']->role_id);
        
        $this->load->view('admin/header', $data);
        $this->load->view('admin/staff/view', $data);
        $this->load->view('admin/footer');
    }
}
