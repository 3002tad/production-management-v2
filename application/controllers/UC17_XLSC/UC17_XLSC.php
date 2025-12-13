<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class UC17_XLSC extends CI_Controller
{
    protected $user_role;
    protected $user_id;

    public function __construct()
    {
        parent::__construct();
        $this->load->library('session');
        $this->load->database();
        $this->load->model('UC15_BCSCModel', 'bcscModel');

        if (!$this->session->userdata('user_id')) {
            redirect('login/');
        }

        $this->user_id = $this->session->userdata('user_id');
        $user = $this->db->select('u.*, r.role_name')
            ->from('user u')
            ->join('roles r', 'u.role_id = r.role_id', 'left')
            ->where('u.user_id', $this->user_id)
            ->get()
            ->row();

        if (!$user) {
            redirect('login/');
        }

        $this->user_role = strtolower(trim((string)$user->role_name));
    }

    private function is_technical()
    {
        return in_array($this->user_role, ['technical', 'technical_staff'], true);
    }

    private function is_leader_or_admin()
    {
        return in_array($this->user_role, ['leader', 'line_manager', 'admin', 'bod', 'system_admin'], true);
    }

    // List incidents for technical to handle (new or assigned)
    public function index()
    {
        // Only logged users can see; technicals see their view, leaders/admins can also view but read-only
        $filter = $this->input->get('filter');

        // Technical should see incidents with status = 0 (new) or status = 2 (in-progress) or assigned to them
        $this->db->start_cache();
        $this->db->from('incident_reports');
        $this->db->order_by('created_at', 'DESC');

        // if filter specified, allow filtering
        if ($filter === 'new') {
            $this->db->where('status', 0);
        }

        $incidents = $this->db->get()->result();
        $this->db->stop_cache();
        $this->db->flush_cache();

        $data = [
            'incidents' => $incidents,
            'user_role' => $this->user_role,
            'content' => 'uc17_xlsc/index',
            'navlink' => 'incident',
        ];

        // Reuse leader layout for consistency; technicals will have read/write in view
        if ($this->is_technical()) {
            $this->load->view('uc15_bcsc/technical_vbackend', $data);
        } else {
            $this->load->view('leader/vbackend', $data);
        }
    }

    // Show details + history + forms to update by technical
    public function view($incident_id)
    {
        $incident = $this->bcscModel->get_by_id($incident_id);
        if (!$incident) {
            show_404();
        }

        // coordination history
        $coord = $this->db->where('incident_id', $incident_id)->order_by('created_at', 'ASC')->get('incident_coordination')->result();

        $data = [
            'incident' => $incident,
            'coordination' => $coord,
            'user_role' => $this->user_role,
            'content' => 'uc17_xlsc/view',
            'navlink' => 'incident',
        ];

        if ($this->is_technical()) {
            $this->load->view('uc15_bcsc/technical_vbackend', $data);
        } else {
            $this->load->view('leader/vbackend', $data);
        }
    }

    // Technical submits estimated completion time (keeps incident.status unchanged)
    public function submit_estimate()
    {
        if (!$this->is_technical()) {
            show_error('Access Denied - Only Technical can submit estimates', 403, 'Forbidden');
        }

        $incident_id = (int)$this->input->post('incident_id');
        $estimate = $this->input->post('estimated_completion') ?: null;
        $notes = $this->input->post('notes') ?: null;

        if (!$incident_id || !$estimate) {
            $this->session->set_flashdata('error', 'Thiếu thông tin estimate');
            redirect($_SERVER['HTTP_REFERER'] ?? 'uc17_xlsc');
        }

        if (!$this->db->table_exists('incident_coordination')) {
            $this->session->set_flashdata('error', 'Bảng `incident_coordination` chưa tồn tại. Vui lòng chạy migration.');
            redirect($_SERVER['HTTP_REFERER'] ?? 'uc17_xlsc');
        }

        $payload = [
            'incident_id' => $incident_id,
            'assignee_id' => $this->user_id,
            'action_type' => 'estimate',
            'shift_info' => $estimate,
            'notes' => $notes,
            'status' => 'submitted',
            'created_at' => date('Y-m-d H:i:s'),
        ];

        $this->db->insert('incident_coordination', $payload);

        $this->session->set_flashdata('success', 'Đã gửi thời gian dự kiến hoàn thành đến Leader.');
        redirect($_SERVER['HTTP_REFERER'] ?? 'uc17_xlsc');
    }

    // Technical updates progress (percent, notes). This will set incident.status = 2 (in-progress)
    public function update_progress()
    {
        if (!$this->is_technical()) {
            show_error('Access Denied - Only Technical can update progress', 403, 'Forbidden');
        }

        $incident_id = (int)$this->input->post('incident_id');
        $percent = (int)$this->input->post('percent');
        $notes = $this->input->post('notes') ?: null;

        if (!$incident_id || $percent < 0 || $percent > 100) {
            $this->session->set_flashdata('error', 'Thông tin tiến độ không hợp lệ');
            redirect($_SERVER['HTTP_REFERER'] ?? 'uc17_xlsc');
        }

        if (!$this->db->table_exists('incident_coordination')) {
            $this->session->set_flashdata('error', 'Bảng `incident_coordination` chưa tồn tại. Vui lòng chạy migration.');
            redirect($_SERVER['HTTP_REFERER'] ?? 'uc17_xlsc');
        }

        $payload = [
            'incident_id' => $incident_id,
            'assignee_id' => $this->user_id,
            'action_type' => 'progress',
            'shift_info' => $percent . '%',
            'notes' => $notes,
            'status' => 'in_progress',
            'created_at' => date('Y-m-d H:i:s'),
        ];

        $this->db->insert('incident_coordination', $payload);

        // Update incident to in-progress so leader sees it's being handled
        $this->db->where('id', $incident_id)->update('incident_reports', [
            'status' => 2,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        $this->session->set_flashdata('success', 'Đã cập nhật tiến độ xử lý.');
        redirect($_SERVER['HTTP_REFERER'] ?? 'uc17_xlsc');
    }

    // Technical can mark repair done but it will NOT set incident to completed (leader confirms final completion)
    public function mark_repair_done()
    {
        if (!$this->is_technical()) {
            show_error('Access Denied - Only Technical can mark repair done', 403, 'Forbidden');
        }

        $incident_id = (int)$this->input->post('incident_id');
        $notes = $this->input->post('notes') ?: 'Kỹ thuật đã hoàn tất xử lý (chưa xác nhận bởi Leader)';

        if (!$incident_id) {
            $this->session->set_flashdata('error', 'Thiếu incident_id');
            redirect($_SERVER['HTTP_REFERER'] ?? 'uc17_xlsc');
        }

        if (!$this->db->table_exists('incident_coordination')) {
            $this->session->set_flashdata('error', 'Bảng `incident_coordination` chưa tồn tại. Vui lòng chạy migration.');
            redirect($_SERVER['HTTP_REFERER'] ?? 'uc17_xlsc');
        }

        $this->db->insert('incident_coordination', [
            'incident_id' => $incident_id,
            'assignee_id' => $this->user_id,
            'action_type' => 'repair_done',
            'notes' => $notes,
            'status' => 'repair_done_by_technical',
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        // Keep incident status as in-progress (2) — leader will confirm completion
        $this->db->where('id', $incident_id)->update('incident_reports', [
            'status' => 2,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        $this->session->set_flashdata('success', 'Kỹ thuật đã ghi nhận hoàn tất xử lý. Vui lòng chờ Leader xác nhận.');
        redirect($_SERVER['HTTP_REFERER'] ?? 'uc17_xlsc');
    }
}
