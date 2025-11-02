<?php

defined('BASEPATH') or exit('no direct script access allowed');

class Login extends CI_Controller
{
    /**
     * @var CI_DB_query_builder
     */
    public $db;

    /**
     * @var CI_Session
     */
    public $session;

    /**
     * @var CI_URI
     */
    public $uri;

    /**
     * @var CI_Input
     */
    public $input;

    /**
     * @var CI_Form_validation
     */
    public $form_validation;

    /**
     * @var LoginModel
     */
    public $login;

    /**
     * @var CrudModel
     */
    public $crudModel;
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
            // Prefer the new RBAC key 'role_name', fall back to legacy 'role'
            $role = $this->session->userdata('role_name') ?: $this->session->userdata('role');

            // Map role names to controllers
            if (in_array($role, ['admin', 'system_admin', 'bod'])) {
                redirect('admin/');
            } elseif (in_array($role, ['leader', 'line_manager'])) {
                redirect('leader/');
            } elseif (in_array($role, ['warehouse', 'warehouse_staff'])) {
                redirect('warehouse/');
            } elseif (in_array($role, ['qc_staff'])) {
                redirect('qc/');
            } elseif (in_array($role, ['technical_staff'])) {
                redirect('technical/');
            } elseif (in_array($role, ['worker'])) {
                redirect('worker/');
            } else {
                // Unknown role: destroy session to avoid redirect loops and show login form
                $this->session->sess_destroy();
                $this->load->view('login');
                return;
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
                    // LoginModel::check_login returns a single user object (or false).
                    // Use the returned object directly instead of foreach (which yields scalar values
                    // for object properties and caused "Trying to get property of non-object").
                    $role_name = isset($checking->role_name) ? $checking->role_name : (isset($checking->role) ? $checking->role : null);

                    $session_data = [
                        'user_id' => $checking->user_id,
                        'username' => $checking->username,
                        // Do NOT store the password in session for security reasons.
                        'role' => $role_name,
                        // Keep role_name too because some code (e.g., LoginModel::is_role) reads this key.
                        'role_name' => $role_name,
                    ];

                    $this->session->set_userdata($session_data);

                    if ($this->session->userdata('role') === 'admin') {
                        redirect('admin/');
                    } elseif ($this->session->userdata('role') === 'leader') {
                        redirect('leader/');
                    }
                } else {
                    $this->load->view('login');
                }
            } else {
                $this->load->view('login');
            }
        }
    }

    /**
     * Logout the current user: clear session and redirect to login page
     */
    public function logout()
    {
        // remove specific userdata keys then destroy the session
        $this->session->unset_userdata(['user_id', 'username', 'role', 'role_name']);
        $this->session->sess_destroy();

        // redirect to login page
        redirect('login');
    }
}