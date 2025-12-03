<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Technical Controller for UC15_BCSC Module
 * 
 * Specialized controller for Technical staff role
 * - View incident reports list
 * - Edit incident reports
 * - Update incident status
 * - NO access to staff management (Nhân viên)
 */
class Technical extends CI_Controller
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
        
        // Only allow technical staff
        $allowed_roles = ['technical', 'technical_staff'];
        if (!in_array($this->user_role, $allowed_roles, true)) {
            show_error('Access Denied - This area is for Technical Staff only', 403, 'Forbidden');
        }
    }

    /**
     * Check if user can perform action based on technical role
     * 
     * Permissions:
     * - technical: view, edit, update_status
     */
    private function check_permission($action)
    {
        $permissions = [
            'technical' => ['view', 'edit', 'update_status'],
            'technical_staff' => ['view', 'edit', 'update_status'],
        ];

        if (!isset($permissions[$this->user_role])) {
            return false;
        }

        return in_array($action, $permissions[$this->user_role]);
    }

    /**
     * Dashboard - Display incident reports list
     */
    public function index()
    {
        if (!$this->check_permission('view')) {
            show_error('Access Denied - Insufficient Permissions', 403, 'Forbidden');
        }

        $data = [
            'incidents' => $this->bcscModel->get_all(),
            'user_role' => $this->user_role,
            'content' => 'uc15_bcsc/technical_beranda',
            'navlink' => 'beranda',
        ];

        $this->load->view('uc15_bcsc/technical_vbackend', $data);
    }

    /**
     * Edit incident form
     */
    public function edit($id)
    {
        if (!$this->check_permission('edit')) {
            show_error('Access Denied - Only Technical can edit incident reports', 403, 'Forbidden');
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

        $this->load->view('uc15_bcsc/technical_vbackend', $data);
    }

    /**
     * Update incident report
     */
    public function update($id)
    {
        if (!$this->check_permission('edit')) {
            show_error('Access Denied - Only Technical can edit incident reports', 403, 'Forbidden');
        }

        $incident = $this->bcscModel->get_by_id($id);
        if (!$incident) {
            show_404();
        }

        $this->form_validation->set_rules('id_machine', 'Mã máy', 'required');
        $this->form_validation->set_rules('id_planshift', 'Mã dây chuyền', 'numeric');
        $this->form_validation->set_rules('category', 'Loại sự cố', 'required|in_list[equipment,quality,safety,other]');
        $this->form_validation->set_rules('severity_level', 'Mức độ nghiêm trọng', 'required|in_list[1,2,3,4]');
        $this->form_validation->set_rules('incident_description', 'Ghi rõ sự cố', 'required|min_length[10]');

        if ($this->form_validation->run() === false) {
            $this->edit($id);
            return;
        }

        $upload_data = $this->handle_file_upload();

        $data = [
            'id_machine' => $this->input->post('id_machine'),
            'id_planshift' => $this->input->post('id_planshift') ?: null,
            'category' => $this->input->post('category'),
            'severity_level' => $this->input->post('severity_level'),
            'incident_description' => $this->input->post('incident_description'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        // If status is provided from edit form, include it in update
        $posted_status = $this->input->post('status');
        if ($posted_status !== null) {
            $posted_status = (int) $posted_status;
            if (in_array($posted_status, [0, 1, 2], true)) {
                $data['status'] = $posted_status;
                if ($posted_status === 1) {
                    $data['resolved_at'] = date('Y-m-d H:i:s');
                }
            }
        }

        // Optional resolution notes
        if ($this->input->post('resolution_notes')) {
            $data['resolution_notes'] = $this->input->post('resolution_notes');
        }

        if (!empty($upload_data['file_path'])) {
            $data['media_path'] = $upload_data['file_path'];
            if (!empty($incident->media_path) && file_exists($incident->media_path)) {
                unlink($incident->media_path);
            }
        }

        $result = $this->bcscModel->update($id, $data);

        if ($result) {
            $this->session->set_flashdata('success', 'Báo cáo sự cố đã được cập nhật thành công');
            redirect('uc15_bcsc/technical');
        } else {
            $this->session->set_flashdata('error', 'Lỗi khi cập nhật báo cáo sự cố');
            redirect('uc15_bcsc/technical/edit/' . $id);
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
        if (!in_array($status, [0, 1, 2])) {
            $this->session->set_flashdata('error', 'Trạng thái không hợp lệ');
            redirect('uc15_bcsc/technical');
            return;
        }

        $data = [
            'status' => $status,
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        if ($status == 1) {
            $data['resolved_at'] = date('Y-m-d H:i:s');
        }

        if ($this->input->post('resolution_notes')) {
            $data['resolution_notes'] = $this->input->post('resolution_notes');
        }

        $result = $this->bcscModel->update($id, $data);

        if ($result) {
            $status_text = ($status == 1) ? 'Đã hoàn thành' : (($status == 2) ? 'Đang xử lý' : 'Chưa hoàn thành');
            $this->session->set_flashdata('success', 'Trạng thái đã được cập nhật thành: ' . $status_text);
            redirect('uc15_bcsc/technical');
        } else {
            $this->session->set_flashdata('error', 'Lỗi khi cập nhật trạng thái');
            redirect('uc15_bcsc/technical');
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
            'can_delete' => false, // Technical cannot delete
            'can_update_status' => $this->check_permission('update_status'),
            'content' => 'uc15_bcsc/detail',
            'navlink' => 'beranda',
        ];

        $this->load->view('uc15_bcsc/technical_vbackend', $data);
    }

    /**
     * Handle file upload (image/video)
     */
    private function handle_file_upload()
    {
        $config = [
            'upload_path' => './uploads/incidents/',
            'allowed_types' => 'gif|jpg|jpeg|png|mp4|avi|mov|mkv',
            'max_size' => 52428,
            'encrypt_name' => true,
        ];

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
