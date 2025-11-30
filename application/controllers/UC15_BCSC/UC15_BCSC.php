<?php

defined('BASEPATH') or exit('No direct script access allowed');

class UC15_BCSC extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('UC15_BCSCModel', 'bcscModel');
        $this->load->library('session');
        $this->load->library('form_validation');
        
        // Check if user is logged in
        if (!$this->session->userdata('user_id')) {
            redirect('login/');
        }
        
        // Get user role from database by role_id
        $user_id = $this->session->userdata('user_id');
        $user = $this->db->select('u.*, r.role_name')
            ->from('user u')
            ->join('roles r', 'u.role_id = r.role_id', 'left')
            ->where('u.user_id', $user_id)
            ->get()
            ->row();
        
        if ($user) {
            $this->user_role = strtolower(trim((string)$user->role_name));
        } else {
            redirect('login/');
        }
    }

    /**
     * Check if user can perform action based on role
     */
    private function check_permission($action)
    {
        $permissions = [
            'worker' => ['add', 'edit', 'delete', 'view'],
            'technical' => ['view', 'update_status'],
            'leader' => ['view'],
            'admin' => ['view'],
            'bod' => ['view'],
        ];

        if (!isset($permissions[$this->user_role])) {
            return false;
        }

        return in_array($action, $permissions[$this->user_role]);
    }

    /**
     * Display incident reports list
     */
    public function index()
    {
        if (!$this->check_permission('view')) {
            show_error('Access Denied - Insufficient Permissions', 403, 'Forbidden');
        }

        $data = [
            'incidents' => $this->bcscModel->get_all(),
            'user_role' => $this->user_role,
            'content' => 'uc15_bcsc/list',
            'navlink' => 'beranda',
        ];

        $this->load->view('uc15_bcsc/vbackend', $data);
    }

    /**
     * Display add incident form
     */
    public function add()
    {
        if (!$this->check_permission('add')) {
            show_error('Access Denied - Only Worker can add incident reports', 403, 'Forbidden');
        }

        $data = [
            'machines' => $this->db->get('machine')->result(),
            'staff' => $this->db->get('staff')->result(),
            'plan_shifts' => $this->db->get('plan_shift')->result(),
            'content' => 'uc15_bcsc/add',
            'navlink' => 'beranda',
        ];

        $this->load->view('uc15_bcsc/vbackend', $data);
    }

    /**
     * Store new incident report
     */
    public function store()
    {
        if (!$this->check_permission('add')) {
            show_error('Access Denied - Only Worker can add incident reports', 403, 'Forbidden');
        }

        $this->form_validation->set_rules('id_machine', 'Mã máy', 'required');
        $this->form_validation->set_rules('id_planshift', 'Mã dây chuyền', 'required');
        $this->form_validation->set_rules('incident_description', 'Ghi rõ sự cố', 'required|min_length[10]');
        $this->form_validation->set_rules('status', 'Trạng thái', 'required|in_list[0,1]');

        $this->form_validation->set_message('required', '{field} không được để trống');
        $this->form_validation->set_message('min_length', '{field} phải có ít nhất 10 ký tự');

        if ($this->form_validation->run() === false) {
            $this->add();
            return;
        }

        $upload_data = $this->handle_file_upload();

        $data = [
            'user_id' => $this->session->userdata('user_id'),
            'id_machine' => $this->input->post('id_machine'),
            'id_planshift' => $this->input->post('id_planshift'),
            'incident_description' => $this->input->post('incident_description'),
            'media_path' => $upload_data['file_path'],
            'status' => $this->input->post('status'),
            'created_at' => date('Y-m-d H:i:s'),
        ];

        $result = $this->bcscModel->insert($data);

        if ($result) {
            $this->session->set_flashdata('success', 'Báo cáo sự cố đã được tạo thành công');
            redirect('uc15_qlns/uc15_bcsc');
        } else {
            $this->session->set_flashdata('error', 'Lỗi khi tạo báo cáo sự cố');
            redirect('uc15_qlns/uc15_bcsc/add');
        }
    }

    /**
     * Display edit incident form
     */
    public function edit($id)
    {
        if (!$this->check_permission('edit')) {
            show_error('Access Denied - Only Worker can edit incident reports', 403, 'Forbidden');
        }

        $incident = $this->bcscModel->get_by_id($id);
        if (!$incident) {
            show_404();
        }

        $data = [
            'incident' => $incident,
            'machines' => $this->db->get('machine')->result(),
            'staff' => $this->db->get('staff')->result(),
            'plan_shifts' => $this->db->get('plan_shift')->result(),
            'content' => 'uc15_bcsc/edit',
            'navlink' => 'beranda',
        ];

        $this->load->view('uc15_bcsc/vbackend', $data);
    }

    /**
     * Update incident report
     */
    public function update($id)
    {
        if (!$this->check_permission('edit')) {
            show_error('Access Denied - Only Worker can edit incident reports', 403, 'Forbidden');
        }

        $incident = $this->bcscModel->get_by_id($id);
        if (!$incident) {
            show_404();
        }

        $this->form_validation->set_rules('id_machine', 'Mã máy', 'required');
        $this->form_validation->set_rules('id_planshift', 'Mã dây chuyền', 'required');
        $this->form_validation->set_rules('incident_description', 'Ghi rõ sự cố', 'required|min_length[10]');

        if ($this->form_validation->run() === false) {
            $this->edit($id);
            return;
        }

        $upload_data = $this->handle_file_upload();

        $data = [
            'id_machine' => $this->input->post('id_machine'),
            'id_planshift' => $this->input->post('id_planshift'),
            'incident_description' => $this->input->post('incident_description'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        if (!empty($upload_data['file_path'])) {
            $data['media_path'] = $upload_data['file_path'];
            // Delete old file if exists
            if (!empty($incident->media_path) && file_exists($incident->media_path)) {
                unlink($incident->media_path);
            }
        }

        $result = $this->bcscModel->update($id, $data);

        if ($result) {
            $this->session->set_flashdata('success', 'Báo cáo sự cố đã được cập nhật thành công');
            redirect('uc15_qlns/uc15_bcsc');
        } else {
            $this->session->set_flashdata('error', 'Lỗi khi cập nhật báo cáo sự cố');
            redirect('uc15_qlns/uc15_bcsc/edit/' . $id);
        }
    }

    /**
     * Update incident status (for technical staff)
     */
    public function update_status($id)
    {
        if (!$this->check_permission('update_status')) {
            show_error('Access Denied - Only Technical can update incident status', 403, 'Forbidden');
        }

        $incident = $this->bcscModel->get_by_id($id);
        if (!$incident) {
            show_404();
        }

        $status = $this->input->post('status');
        if (!in_array($status, [0, 1])) {
            $this->session->set_flashdata('error', 'Trạng thái không hợp lệ');
            redirect('uc15_qlns/uc15_bcsc');
            return;
        }

        $data = [
            'status' => $status,
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $result = $this->bcscModel->update($id, $data);

        if ($result) {
            $status_text = ($status == 1) ? 'Đã hoàn thành' : 'Chưa hoàn thành';
            $this->session->set_flashdata('success', 'Trạng thái đã được cập nhật thành: ' . $status_text);
            redirect('uc15_qlns/uc15_bcsc');
        } else {
            $this->session->set_flashdata('error', 'Lỗi khi cập nhật trạng thái');
            redirect('uc15_qlns/uc15_bcsc');
        }
    }

    /**
     * Display incident detail
     */
    public function detail($id)
    {
        if (!$this->check_permission('view')) {
            show_error('Access Denied - Insufficient Permissions', 403, 'Forbidden');
        }

        $incident = $this->bcscModel->get_by_id($id);
        if (!$incident) {
            show_404();
        }

        $data = [
            'incident' => $incident,
            'user_role' => $this->user_role,
            'can_edit' => $this->check_permission('edit'),
            'can_delete' => $this->check_permission('delete'),
            'can_update_status' => $this->check_permission('update_status'),
            'content' => 'uc15_bcsc/detail',
            'navlink' => 'beranda',
        ];

        $this->load->view('uc15_bcsc/vbackend', $data);
    }

    /**
     * Delete incident report
     */
    public function delete($id)
    {
        if (!$this->check_permission('delete')) {
            show_error('Access Denied - Only Worker can delete incident reports', 403, 'Forbidden');
        }

        $incident = $this->bcscModel->get_by_id($id);
        if (!$incident) {
            show_404();
        }

        // Delete file if exists
        if (!empty($incident->media_path) && file_exists($incident->media_path)) {
            unlink($incident->media_path);
        }

        $result = $this->bcscModel->delete($id);

        if ($result) {
            $this->session->set_flashdata('success', 'Báo cáo sự cố đã được xóa thành công');
            redirect('uc15_qlns/uc15_bcsc');
        } else {
            $this->session->set_flashdata('error', 'Lỗi khi xóa báo cáo sự cố');
            redirect('uc15_qlns/uc15_bcsc');
        }
    }

    /**
     * Handle file upload (image/video)
     */
    private function handle_file_upload()
    {
        $config = [
            'upload_path' => './uploads/incidents/',
            'allowed_types' => 'gif|jpg|jpeg|png|mp4|avi|mov|mkv',
            'max_size' => 52428, // 51 MB
            'encrypt_name' => true,
        ];

        // Create upload directory if not exists
        if (!is_dir($config['upload_path'])) {
            mkdir($config['upload_path'], 0755, true);
        }

        $this->load->library('upload', $config);

        $result = [
            'file_path' => null,
            'error' => null,
        ];

        if (!empty($_FILES['media']['name'])) {
            if (!$this->upload->do_upload('media')) {
                $result['error'] = $this->upload->display_errors();
                $this->session->set_flashdata('error', $result['error']);
            } else {
                $upload_info = $this->upload->data();
                $result['file_path'] = $config['upload_path'] . $upload_info['file_name'];
            }
        }

        return $result;
    }
}
