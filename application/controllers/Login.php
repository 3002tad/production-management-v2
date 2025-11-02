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
            // Khi đã đăng nhập rồi, ưu tiên điều hướng theo role_id trong session
            $roleId = (int) $this->session->userdata('role_id');
            if ($roleId === 4) {
                return redirect('admin/');
            } elseif ($roleId === 2) {
                return redirect('leader/');
            } elseif ($roleId === 3) {
                return redirect('warehouse/');
            }

            // Fallback theo cột text 'role' nếu session chưa có role_id (tương thích dữ liệu cũ)
            $role = $this->login->is_role();
            if ($role === 'admin') {
                return redirect('admin/');
            } elseif ($role === 'leader') {
                return redirect('leader/');
            } elseif ($role === 'warehouse') {
                return redirect('warehouse/');
            }
        } else {
            $this->form_validation->set_rules('username', 'Username', 'required');
            $this->form_validation->set_rules('password', 'Password', 'required');

            $this->form_validation->set_message('required', '<div class="alert alert-danger" style="margin-top: 3px">
                    <div class="header"><b><i class="fa fa-exclamation-circle"></i> {field}</b> harus diisi</div></div>');

            if ($this->form_validation->run() === true) {
                $username = $this->input->post('username', true);
                $password = $this->input->post('password', true);

                $checking = $this->login->check_login('user', ['username' => $username], ['password' => $password]);

                if ($checking !== false) {
                    foreach ($checking as $data) {
                        // Lưu cả role_id và role text để tương thích
                        $session_data = [
                            'user_id'  => $data->user_id,
                            'username' => $data->username,
                            'password' => $data->password,
                            'role_id'  => isset($data->role_id) ? (int) $data->role_id : null,
                            'role'     => $data->role,
                        ];

                        $this->session->set_userdata($session_data);

                        // Điều hướng ngay theo role_id nếu có
                        $rid = (int) $this->session->userdata('role_id');
                        if ($rid === 4) {
                            return redirect('admin/');
                        } elseif ($rid === 2) {
                            return redirect('leader/');
                        } elseif ($rid === 3) {
                            return redirect('warehouse/');
                        }

                        // Fallback theo role text
                        $r = $this->session->userdata('role');
                        if ($r === 'admin') {
                            return redirect('admin/');
                        } elseif ($r === 'leader') {
                            return redirect('leader/');
                        } elseif ($r === 'warehouse') {
                            return redirect('warehouse/');
                        }
                    }
                } else {
                    $this->load->view('login');
                }
            } else {
                $this->load->view('login');
            }
        }
    }
}
