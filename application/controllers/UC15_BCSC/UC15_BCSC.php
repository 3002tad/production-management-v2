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
     * 
     * Permissions:
     * - worker: add, edit, delete, view
     * - leader: view only
     * - admin: view only
     * - bod: view only
     * 
     * NOTE: Technical staff should use the Technical.php controller instead
     */
    private function check_permission($action)
    {
        // Primary role permissions
        $permissions = [
            'worker' => ['add', 'edit', 'delete', 'view'],
            'leader' => ['view'],
            'line_manager' => ['view'],
            'admin' => ['view'],
            'system_admin' => ['view'],
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
        
        $user_id = $this->session->userdata('user_id');

        // Get current shift for user
        $current_shift = $this->get_current_shift_for_user($user_id);

        // Get shift history for user (last 5)
        $shift_history = $this->get_shift_history_for_user($user_id);
        if (count($shift_history) > 5) {
            $shift_history = array_slice($shift_history, 0, 5);
        }

        $data = [
            'incidents' => $this->bcscModel->get_all(),
            'current_shift' => $current_shift,
            'shift_history' => $shift_history,
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

        // Load zones for dropdown
        $this->load->model('leader/ZoneModel');
        $zones = $this->ZoneModel->getZones();

        // Load machines with line and zone info
        $this->db->select('m.id, m.code as machine_code, m.name as machine_name, m.stage_type, m.status, pl.line_code, pl.line_name, z.zone_name');
        $this->db->from('machines m');
        $this->db->join('production_lines pl', 'm.line_id = pl.id', 'left');
        $this->db->join('zones z', 'pl.zone_id = z.zone_id', 'left');
        $this->db->where('m.status', 'active');
        $this->db->order_by('z.zone_code, pl.line_code, m.code');
        $machines = $this->db->get()->result();
        
        // Debug log
        if (ENVIRONMENT === 'development') {
            log_message('debug', 'UC15_BCSC::add() - Loaded machines: ' . count($machines));
            log_message('debug', 'UC15_BCSC::add() - SQL: ' . $this->db->last_query());
        }

        // Load production lines with zone info
        $this->db->select('pl.id, pl.line_code, pl.line_name, z.zone_name');
        $this->db->from('production_lines pl');
        $this->db->join('zones z', 'pl.zone_id = z.zone_id', 'left');
        $this->db->order_by('z.zone_code, pl.line_code');
        $lines = $this->db->get()->result();

        // Load active/upcoming shifts (today and future) - get shifts that are NOT completed (status != 3)
        $this->load->model('leader/ShiftModel');
        $user_id = $this->session->userdata('user_id');
        
        // Get shifts assigned to current user (worker) - don't check time, just check if assigned
        $this->db->distinct();
        $this->db->select('ps.shift_id, ps.shift_code, ps.shift_name, ps.line_id, ps.shift_date, ps.start_time, ps.end_time, ps.shift_status,
            pl.line_code, pl.line_name,
            z.zone_code, z.zone_name');
        $this->db->from('production_shifts ps');
        $this->db->join('production_lines pl', 'ps.line_id = pl.id', 'left');
        $this->db->join('zones z', 'pl.zone_id = z.zone_id', 'left');
        $this->db->join('shift_machine_staff sms', 'ps.shift_id = sms.shift_id AND sms.status = 1', 'inner');
        $this->db->where('sms.staff_id', $user_id);
        $this->db->where('ps.shift_status IN (1,2,4)'); // Not started, running, or paused
        $this->db->order_by('ps.shift_date', 'ASC');
        $this->db->order_by('ps.start_time', 'ASC');
        $shifts = $this->db->get()->result();
        
        // Debug: log assigned shifts
        if (ENVIRONMENT === 'development') {
            log_message('debug', 'UC15_BCSC::add() - Assigned shifts count: ' . count($shifts));
            log_message('debug', 'UC15_BCSC::add() - SQL: ' . $this->db->last_query());
            if (count($shifts) > 0) {
                log_message('debug', 'UC15_BCSC::add() - First shift: ' . json_encode($shifts[0]));
            }
        }

        // Get current shift (first assigned shift, or null if none)
        $current_shift_id = !empty($shifts) ? $shifts[0]->shift_id : null;

        $data = [
            'zones' => $zones,
            'machines' => $machines,
            'lines' => $lines,
            'shifts' => $shifts,
            'current_shift_id' => $current_shift_id, // Pass to view
            'content' => 'uc15_bcsc/add_v2',
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

        // Line is required, machine is optional
        $this->form_validation->set_rules('line_id', 'Dây chuyền', 'required|numeric');
        $this->form_validation->set_rules('category', 'Loại sự cố', 'required|in_list[equipment,quality,safety,other]');
        $this->form_validation->set_rules('severity_level', 'Mức độ nghiêm trọng', 'required|in_list[1,2,3,4]');
        $this->form_validation->set_rules('incident_description', 'Mô tả sự cố', 'required|min_length[10]');

        $this->form_validation->set_message('required', '{field} không được để trống');
        $this->form_validation->set_message('min_length', '{field} phải có ít nhất 10 ký tự');

        if ($this->form_validation->run() === false) {
            $this->add();
            return;
        }

        $upload_data = $this->handle_file_upload();

        $data = [
            'user_id' => $this->session->userdata('user_id'),
            'id_machine' => $this->input->post('id_machine') ?: null,
            'line_id' => $this->input->post('line_id') ?: null,
            'shift_id' => $this->input->post('shift_id') ?: null,
            'id_planshift' => null, // Deprecated field
            'category' => $this->input->post('category'),
            'severity_level' => $this->input->post('severity_level'),
            'incident_description' => $this->input->post('incident_description'),
            'media_path' => $upload_data['file_path'],
            'status' => 0, // Always start as pending
            'assignee_id' => null, // Technical staff will assign later
            'created_at' => date('Y-m-d H:i:s'),
        ];

        $result = $this->bcscModel->insert($data);

        if ($result) {
            // Clear any old flashdata before setting new message
            $this->session->unset_userdata('error');
            $this->session->set_flashdata('success', 'Báo cáo sự cố đã được tạo thành công');
            redirect('uc15_bcsc/uc15_bcsc');
        } else {
            $this->session->unset_userdata('success');
            $this->session->set_flashdata('error', 'Lỗi khi tạo báo cáo sự cố');
            redirect('uc15_bcsc/uc15_bcsc/add');
        }
    }

    /**
     * Display edit incident form
     */
    public function edit($id)
    {
        if (!$this->check_permission('edit')) {
            show_error('Access Denied - Only Worker and Technical can edit incident reports', 403, 'Forbidden');
        }

        $incident = $this->bcscModel->get_by_id($id);
        if (!$incident) {
            show_404();
        }
        
        // Load coordination / progress history if table exists
        if ($this->db->table_exists('incident_coordination')) {
            $coordination = $this->db->where('incident_id', $id)->order_by('created_at', 'ASC')->get('incident_coordination')->result();
        } else {
            $coordination = [];
        }
        // Load zones
        $this->load->model('leader/ZoneModel');
        $zones = $this->ZoneModel->getZones();

        // Load machines with line/zone info
        $this->db->select('m.id, m.code as machine_code, m.name as machine_name, m.stage_type, pl.line_code, pl.line_name, z.zone_name');
        $this->db->from('machines m');
        $this->db->join('production_lines pl', 'm.line_id = pl.id', 'left');
        $this->db->join('zones z', 'pl.zone_id = z.zone_id', 'left');
        $this->db->order_by('z.zone_code, pl.line_code, m.code');
        $machines = $this->db->get()->result();

        // Load production lines
        $this->db->select('pl.id, pl.line_code, pl.line_name, z.zone_name');
        $this->db->from('production_lines pl');
        $this->db->join('zones z', 'pl.zone_id = z.zone_id', 'left');
        $this->db->order_by('z.zone_code, pl.line_code');
        $lines = $this->db->get()->result();

        // Load shifts
        $this->load->model('leader/ShiftModel');
        $shifts = $this->ShiftModel->getShifts(['date_from' => date('Y-m-d', strtotime('-7 days'))]);

        $data = [
            'incident' => $incident,
            'zones' => $zones,
            'machines' => $machines,
            'lines' => $lines,
            'shifts' => $shifts,
            'user_role' => $this->user_role,
            'content' => 'uc15_bcsc/edit_v2',
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
            show_error('Access Denied - Only Worker and Technical can edit incident reports', 403, 'Forbidden');
        }

        $incident = $this->bcscModel->get_by_id($id);
        if (!$incident) {
            show_404();
        }

        $this->form_validation->set_rules('category', 'Loại sự cố', 'required|in_list[equipment,quality,safety,other]');
        $this->form_validation->set_rules('severity_level', 'Mức độ nghiêm trọng', 'required|in_list[1,2,3,4]');
        $this->form_validation->set_rules('incident_description', 'Mô tả sự cố', 'required|min_length[10]');

        if ($this->form_validation->run() === false) {
            $this->edit($id);
            return;
        }

        $upload_data = $this->handle_file_upload();

        $data = [
            'id_machine' => $this->input->post('id_machine') ?: null,
            'line_id' => $this->input->post('line_id') ?: null,
            'shift_id' => $this->input->post('shift_id') ?: null,
            'category' => $this->input->post('category'),
            'severity_level' => $this->input->post('severity_level'),
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
            // Clear any old flashdata
            $this->session->unset_userdata('error');
            $this->session->set_flashdata('success', 'Báo cáo sự cố đã được cập nhật thành công');
            redirect('uc15_bcsc/uc15_bcsc');
        } else {
            $this->session->unset_userdata('success');
            $this->session->set_flashdata('error', 'Lỗi khi cập nhật báo cáo sự cố');
            redirect('uc15_bcsc/uc15_bcsc/edit/' . $id);
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
            redirect('uc15_bcsc/uc15_bcsc');
            return;
        }

        $data = [
            'status' => $status,
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        // If status is completed (1), set resolved_at timestamp
        if ($status == 1) {
            $data['resolved_at'] = date('Y-m-d H:i:s');
        }

        // Add optional resolution notes if provided
        if ($this->input->post('resolution_notes')) {
            $data['resolution_notes'] = $this->input->post('resolution_notes');
        }

        $result = $this->bcscModel->update($id, $data);

        if ($result) {
            $status_text = ($status == 1) ? 'Đã hoàn thành' : (($status == 2) ? 'Đang xử lý' : 'Chưa hoàn thành');
            $this->session->set_flashdata('success', 'Trạng thái đã được cập nhật thành: ' . $status_text);
            redirect('uc15_bcsc/uc15_bcsc');
        } else {
            $this->session->set_flashdata('error', 'Lỗi khi cập nhật trạng thái');
            redirect('uc15_bcsc/uc15_bcsc');
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
        // Load coordination / progress history if table exists
        if ($this->db->table_exists('incident_coordination')) {
            $coordination = $this->db->where('incident_id', $id)->order_by('created_at', 'ASC')->get('incident_coordination')->result();
        } else {
            $coordination = [];
        }

        $data = [
            'incident' => $incident,
            'coordination' => $coordination,
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
            redirect('uc15_bcsc/uc15_bcsc');
        } else {
            $this->session->set_flashdata('error', 'Lỗi khi xóa báo cáo sự cố');
            redirect('uc15_bcsc/uc15_bcsc');
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

    /**
     * Display shift information for worker
     */
    public function shift()
    {
        if (!$this->check_permission('view')) {
            show_error('Access Denied - Insufficient Permissions', 403, 'Forbidden');
        }

        $user_id = $this->session->userdata('user_id');

        // Get current shift for user
        $current_shift = $this->get_current_shift_for_user($user_id);

        // Get shift history for user
        $shift_history = $this->get_shift_history_for_user($user_id);

        $data = [
            'current_shift' => $current_shift,
            'shift_history' => $shift_history,
            'incidents' => $this->bcscModel->get_recent_incidents_for_user($user_id),
            'user_role' => $this->user_role,
            'content' => 'uc15_bcsc/shift',
            'navlink' => 'shift',
        ];

        $this->load->view('uc15_bcsc/vbackend', $data);
    }

    /**
     * Get current shift for user
     */
    private function get_current_shift_for_user($user_id)
    {
        $this->db->select('ps.*, pl.line_code, pl.line_name, z.zone_code, z.zone_name, m.code as machine_code, m.name as machine_name');
        $this->db->from('shift_machine_staff sms');
        $this->db->join('production_shifts ps', 'sms.shift_id = ps.shift_id');
        $this->db->join('production_lines pl', 'ps.line_id = pl.id', 'left');
        $this->db->join('zones z', 'pl.zone_id = z.zone_id', 'left');
        $this->db->join('machines m', 'sms.machine_id = m.id', 'left');
        $this->db->where('sms.staff_id', $user_id);
        $this->db->where('sms.status', 1);
        $this->db->where('ps.shift_status IN (1, 2)'); // 1=scheduled, 2=running
        $this->db->order_by('ps.shift_date', 'DESC');
        $this->db->order_by('ps.start_time', 'DESC');
        $this->db->limit(1);
        return $this->db->get()->row();
    }

    /**
     * Get shift history for user
     */
    private function get_shift_history_for_user($user_id)
    {
        $this->db->select('ps.*, pl.line_code, pl.line_name, z.zone_code, z.zone_name, m.code as machine_code, m.name as machine_name');
        $this->db->from('shift_machine_staff sms');
        $this->db->join('production_shifts ps', 'sms.shift_id = ps.shift_id');
        $this->db->join('production_lines pl', 'ps.line_id = pl.id', 'left');
        $this->db->join('zones z', 'pl.zone_id = z.zone_id', 'left');
        $this->db->join('machines m', 'sms.machine_id = m.id', 'left');
        $this->db->where('sms.staff_id', $user_id);
        $this->db->where('sms.status', 1);
        $this->db->where('ps.shift_status IN (3, 4)'); // 3=ended, 4=closed
        $this->db->order_by('ps.shift_date', 'DESC');
        $this->db->order_by('ps.start_time', 'DESC');
        $this->db->limit(10); // Last 10 shifts
        return $this->db->get()->result();
    }

    /**
     * Display control page (redirect to shift for now)
     */
    public function control()
    {
        redirect('uc15_bcsc/uc15_bcsc/shift');
    }
}
