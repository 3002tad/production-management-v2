<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('admin/UserManagementModel', 'userModel');
        $this->load->library('session');
        $this->load->helper(array('form','url'));
        $this->load->library('form_validation');

        // Check if user is logged in
        if (!$this->session->userdata('user_id')) {
            redirect('login/');
        }

        // RBAC: Check if user has admin access
        $role = $this->session->userdata('role');
        if (empty($role)) {
            $role = $this->session->userdata('role_name');
        }
        $role = strtolower(trim((string)$role));

        // Allowed roles for User Management
        $allowed_roles = ['admin', 'bod', 'system_admin'];

        if (!in_array($role, $allowed_roles, true)) {
            show_error('Access Denied - Admin Only. Your role: ' . var_export($role, true), 403, 'Forbidden');
        }
    }

    public function index()
    {
        $filters = [
            'role' => $this->input->get('role'),
            'status' => $this->input->get('status'),
            'search' => $this->input->get('search')
        ];

        $data = [
            'users' => $this->userModel->getAllUsers($filters),
            'roles' => $this->userModel->getRoles(),
            'statistics' => $this->userModel->getStatistics(),
            'content' => 'admin/user/user_list',
            'navlink' => 'user'
        ];

        $this->load->view('admin/vbackend', $data);
    }

    public function add()
    {
        if ($this->input->method() === 'post') {
            $this->form_validation->set_rules('username', 'Username', 'required|is_unique[user.username]');
            $this->form_validation->set_rules('password', 'Password', 'required|min_length[6]');
            $this->form_validation->set_rules('email', 'Email', 'valid_email');
            $this->form_validation->set_rules('role_id', 'Role', 'required');

            if ($this->form_validation->run() == FALSE) {
                $data['error'] = validation_errors();
                $data['staff_without_user'] = $this->userModel->getStaffWithoutUser();
                $data['roles'] = $this->userModel->getRoles();
                $data['content'] = 'admin/user/user_add';
                $this->load->view('admin/vbackend', $data);
                return;
            }

            $data = [
                'username' => $this->input->post('username'),
                'password' => password_hash($this->input->post('password'), PASSWORD_DEFAULT),
                'email' => $this->input->post('email'),
                'role_id' => $this->input->post('role_id'),
                'staff_id' => $this->input->post('staff_id') ?: null
            ];

            $result = $this->userModel->createUser($data, $this->session->userdata('user_id'));

            if ($result['success']) {
                $this->session->set_flashdata('success', $result['message']);
                redirect('admin/user');
            } else {
                $this->session->set_flashdata('error', $result['message']);
                redirect('admin/user/add');
            }
        }

        $data = [
            'staff_without_user' => $this->userModel->getStaffWithoutUser(),
            'roles' => $this->userModel->getRoles(),
            'content' => 'admin/user/user_add'
        ];

        $this->load->view('admin/vbackend', $data);
    }

    public function edit($id)
    {
        $user = $this->userModel->getUserById($id);
        if (!$user) {
            show_error('User not found', 404);
        }

        if ($this->input->method() === 'post') {
            $this->form_validation->set_rules('username', 'Username', 'required');
            $this->form_validation->set_rules('email', 'Email', 'valid_email');
            $this->form_validation->set_rules('role_id', 'Role', 'required');

            if ($this->form_validation->run() == FALSE) {
                $data['error'] = validation_errors();
                $data['user'] = $user;
                $data['staff_without_user'] = $this->userModel->getStaffWithoutUser();
                $data['roles'] = $this->userModel->getRoles();
                $data['content'] = 'admin/user/user_edit';
                $this->load->view('admin/vbackend', $data);
                return;
            }

            $data = [
                'username' => $this->input->post('username'),
                'email' => $this->input->post('email'),
                'role_id' => $this->input->post('role_id'),
                'staff_id' => $this->input->post('staff_id') ?: null
            ];

            if ($this->input->post('password')) {
                $data['password'] = password_hash($this->input->post('password'), PASSWORD_DEFAULT);
            }

            $result = $this->userModel->updateUser($id, $data, $this->session->userdata('user_id'));

            if ($result['success']) {
                $this->session->set_flashdata('success', $result['message']);
                redirect('admin/user');
            } else {
                $this->session->set_flashdata('error', $result['message']);
                redirect('admin/user/edit/' . $id);
            }
        }

        $data = [
            'user' => $user,
            'staff_without_user' => $this->userModel->getStaffWithoutUser(),
            'roles' => $this->userModel->getRoles(),
            'content' => 'admin/user/user_edit'
        ];

        $this->load->view('admin/vbackend', $data);
    }

    public function delete($id)
    {
        $result = $this->userModel->deleteUser($id, $this->session->userdata('user_id'));

        if ($result['success']) {
            $this->session->set_flashdata('success', $result['message']);
        } else {
            $this->session->set_flashdata('error', $result['message']);
        }

        redirect('admin/user');
    }

    public function toggle_status($id)
    {
        $result = $this->userModel->toggleUserStatus($id, $this->session->userdata('user_id'));

        if ($result['success']) {
            $this->session->set_flashdata('success', $result['message']);
        } else {
            $this->session->set_flashdata('error', $result['message']);
        }

        redirect('admin/user');
    }
}