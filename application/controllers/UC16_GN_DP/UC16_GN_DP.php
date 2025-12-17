<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * UC16_GN_DP Controller
 *
 * Ghi nhận sự cố và điều phối nhân viên kỹ thuật (Giải Ngân & Điều Phối)
 * - Leader: nhận thông báo, phân công technical, điều phối đổi máy/điều chỉnh lịch
 * - BOD/Admin/Technical: chỉ được xem
 *
 * NOTE: controller sử dụng (và sẽ tạo/ghi) bảng `incident_coordination` để lưu lịch sử điều phối.
 * Schema gợi ý (tạo migration nếu cần):
 *  - id INT PK AUTO_INCREMENT
 *  - incident_id INT NOT NULL
 *  - leader_id INT NOT NULL
 *  - action_type VARCHAR(50) NOT NULL -- assign_technical|replace_machine|adjust_shift|overtime
 *  - assignee_id INT NULL
 *  - machine_id VARCHAR(50) NULL
 *  - shift_info VARCHAR(255) NULL
 *  - notes TEXT NULL
 *  - status VARCHAR(50) NULL -- assigned|in_progress|completed
 *  - created_at DATETIME
 */
class UC16_GN_DP extends CI_Controller
{
    protected $user_role;
    protected $user_id;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('UC15_BCSCModel', 'bcscModel');
        $this->load->model('Staff_model', 'staffModel');
        $this->load->library('session');
        $this->load->database();

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

    private function is_leader()
    {
        return in_array($this->user_role, ['leader', 'line_manager', 'leader_staff'], true) || $this->user_role === 'leader';
    }

    /**
     * Dashboard / list of incoming incidents for coordination
     * - Only leaders see actionable UI; others can view list (read-only)
     */
    public function index()
    {
        // By default show all incidents; leader can filter new ones via ?filter=new
        $filter = $this->input->get('filter');
        if ($filter === 'new') {
            $incidents = $this->db->where('status', 0)->order_by('created_at', 'DESC')->get('incident_reports')->result();
        } else {
            $incidents = $this->bcscModel->get_all();
        }

        $data = [
            'incidents' => $incidents,
            'user_role' => $this->user_role,
            'can_assign' => $this->is_leader(),
            'content' => 'uc16_gn_dp/index',
            'navlink' => 'incident',
        ];

        // Render inside leader or technical layout so header/footer/CSS are applied
        if ($this->is_leader()) {
            $this->load->view('leader/vbackend', $data);
            return;
        } else {
            // technical users see within technical layout
            $this->load->view('uc15_bcsc/technical_vbackend', $data);
            return;
        }
    }

    /**
     * View incident detail + coordination history
     */
    public function view($incident_id)
    {
        $incident = $this->bcscModel->get_by_id($incident_id);
        if (!$incident) {
            show_404();
        }

        // coordination table may not exist yet on clean DBs; guard with table_exists
        if ($this->db->table_exists('incident_coordination')) {
            $coord = $this->db->where('incident_id', $incident_id)->order_by('created_at', 'ASC')->get('incident_coordination')->result();
        } else {
            $coord = [];
        }
        
        // Get list of users for assignee dropdown (if needed) and machines for replacement select
        $users = $this->db->where('role_id IS NOT NULL', null, false)
                  ->select('user_id, username')
                  ->get('user')
                  ->result();
        // Get only technicians for assignment selection (roles like technical, technical_staff)
        $technicians = $this->db->select('u.user_id, u.username')
            ->from('user u')
            ->join('roles r', 'u.role_id = r.role_id', 'left')
            ->where("LOWER(r.role_name) LIKE 'technical%'")
            ->get()
            ->result();
        
        // Load machines with hierarchy (zone -> line -> machine)
        $this->db->select('m.id, m.code as machine_code, m.name as machine_name, m.stage_type, pl.line_code, pl.line_name, z.zone_name');
        $this->db->from('machines m');
        $this->db->join('production_lines pl', 'm.line_id = pl.id', 'left');
        $this->db->join('zones z', 'pl.zone_id = z.zone_id', 'left');
        $this->db->where('m.status', 'active');
        $this->db->order_by('z.zone_code, pl.line_code, m.code');
        $machines = $this->db->get()->result();
        
        // Load production lines with zone info for line-wide assignments
        $this->db->select('pl.id, pl.line_code, pl.line_name, z.zone_name');
        $this->db->from('production_lines pl');
        $this->db->join('zones z', 'pl.zone_id = z.zone_id', 'left');
        $this->db->order_by('z.zone_code, pl.line_code');
        $lines = $this->db->get()->result();

        $data = [
            'incident' => $incident,
            'coordination' => $coord,
            'users' => $users,
            'technicians' => $technicians,
            'machines' => $machines,
            'lines' => $lines,
            'user_role' => $this->user_role,
            'can_assign' => $this->is_leader(),
            'content' => 'uc16_gn_dp/view',
            'navlink' => 'incident',
        ];

        // Load inside the appropriate layout so CSS/menus are present
        if ($this->is_leader()) {
            $this->load->view('leader/vbackend', $data);
            return;
        } else {
            $this->load->view('uc15_bcsc/technical_vbackend', $data);
            return;
        }
    }

    /**
     * Technical staff submits repair report
     * POST params:
     * - incident_id (int)
     * - action_type = 'assign_technical'
     * - shift_info (string) - repair time
     * - notes (string) - repair details
     * 
     * NOTE: Does NOT change incident status, so leader can still see it in dashboard
     */
    public function submit_report()
    {
        $is_technical = in_array($this->user_role, ['technical', 'technical_staff'], true);
        
        if (!$is_technical) {
            show_error('Access Denied - Only Technical staff can submit repair reports', 403, 'Forbidden');
        }

        $incident_id = (int)$this->input->post('incident_id');
        $action_type = $this->input->post('action_type');

        if (!$incident_id || $action_type !== 'assign_technical') {
            $this->session->set_flashdata('error', 'Thiếu thông tin sự cố hoặc hành động không hợp lệ');
            redirect($_SERVER['HTTP_REFERER'] ?? 'uc16_gn_dp');
        }

        $payload = [
            'incident_id' => $incident_id,
            'assignee_id' => $this->user_id,
            'action_type' => 'assign_technical',
            'shift_info' => $this->input->post('shift_info') ?: null,
            'notes' => $this->input->post('notes') ?: null,
            'status' => 'submitted',
            'created_at' => date('Y-m-d H:i:s'),
        ];

        if (!$this->db->table_exists('incident_coordination')) {
            $this->session->set_flashdata('error', 'Bảng `incident_coordination` chưa tồn tại. Vui lòng chạy migration.');
            redirect($_SERVER['HTTP_REFERER'] ?? 'uc16_gn_dp');
        }

        $this->db->insert('incident_coordination', $payload);

        // UPDATE: Do NOT change incident status here
        // Status remains 0 (new) so leader can still see it in dashboard
        // Only update incident to mark it has been reported on
        $this->db->where('id', $incident_id)->update('incident_reports', [
            'assignee_id' => $this->user_id,
        ]);

        $this->session->set_flashdata('success', 'Báo cáo sửa chữa đã được gửi thành công. Leader sẽ xem và điều phối.');
        redirect($_SERVER['HTTP_REFERER'] ?? 'uc16_gn_dp');
    }

    /**
     * Leader confirms dispatch/coordination action
     * POST params:
     * - incident_id (int)
     * - action_type (replace_machine|adjust_shift|overtime)
     * - machine_id (string|null) - for replace_machine
     * - notes (string|null) - coordination notes
     * 
     * NOTE: This WILL change incident status to 2 (in-progress), hiding it from leader dashboard
     */
    public function confirm_dispatch()
    {
        if (!$this->is_leader()) {
            show_error('Access Denied - Only Leader can confirm dispatch', 403, 'Forbidden');
        }

        $incident_id = (int)$this->input->post('incident_id');
        $action_type = $this->input->post('action_type');

        if (!$incident_id || !$action_type) {
            $this->session->set_flashdata('error', 'Thiếu thông tin sự cố hoặc loại hành động');
            redirect($_SERVER['HTTP_REFERER'] ?? 'uc16_gn_dp');
        }

        // Validate action type is leader-only actions
        $allowed_actions = ['replace_machine', 'adjust_shift', 'overtime'];
        if (!in_array($action_type, $allowed_actions)) {
            show_error('Access Denied - Invalid action type', 403, 'Forbidden');
        }

        $payload = [
            'incident_id' => $incident_id,
            'leader_id' => $this->user_id,
            'action_type' => $action_type,
            'machine_id' => $this->input->post('machine_id') ?: null,
            'notes' => $this->input->post('notes') ?: null,
            'status' => 'assigned',
            'created_at' => date('Y-m-d H:i:s'),
        ];

        if (!$this->db->table_exists('incident_coordination')) {
            $this->session->set_flashdata('error', 'Bảng `incident_coordination` chưa tồn tại. Vui lòng chạy migration.');
            redirect($_SERVER['HTTP_REFERER'] ?? 'uc16_gn_dp');
        }

        $this->db->insert('incident_coordination', $payload);

        // NOW: Update incident status to 2 (in-progress) so it disappears from leader dashboard
        $this->db->where('id', $incident_id)->update('incident_reports', [
            'status' => 2, // in-progress
        ]);

        $this->session->set_flashdata('success', 'Đã xác nhận điều phối. Sự cố sẽ được ẩn khỏi danh sách mới.');
        redirect($_SERVER['HTTP_REFERER'] ?? 'uc16_gn_dp');
    }

    /**
     * Legacy method for backward compatibility
     * Leader assigns coordination action OR Technical staff submits repair report
     * POST params expected:
     * - incident_id (int)
     * - action_type (assign_technical|replace_machine|adjust_shift|overtime)
     * - assignee_id (int|null) - for leader only
     * - machine_id (string|null) - for leader only
     * - shift_info (string|null) - for technical: repair time
     * - notes (string|null) - for technical: repair details; for leader: coordination notes
     */
    public function assign_action()
    {
        // Allow both leader and technical staff, with different behaviors
        $is_technical = in_array($this->user_role, ['technical', 'technical_staff'], true);
        
        if (!$this->is_leader() && !$is_technical) {
            show_error('Access Denied - Only Leader or Technical can perform this action', 403, 'Forbidden');
        }

        $incident_id = (int)$this->input->post('incident_id');
        $action_type = $this->input->post('action_type');

        if (!$incident_id || !$action_type) {
            $this->session->set_flashdata('error', 'Thiếu thông tin sự cố hoặc loại hành động');
            redirect($_SERVER['HTTP_REFERER'] ?? 'uc16_gn_dp');
        }

        // Technical staff can only submit 'assign_technical' reports
        if ($is_technical && $action_type !== 'assign_technical') {
            show_error('Access Denied - Technical staff can only submit repair reports', 403, 'Forbidden');
        }

        $payload = [
            'incident_id' => $incident_id,
            'leader_id' => $this->is_leader() ? $this->user_id : null,
            'assignee_id' => $is_technical ? $this->user_id : ($this->input->post('assignee_id') ?: null),
            'action_type' => $action_type,
            'machine_id' => $this->input->post('machine_id') ?: null,
            'shift_info' => $this->input->post('shift_info') ?: null,
            'notes' => $this->input->post('notes') ?: null,
            'status' => $is_technical ? 'submitted' : 'assigned',
            'created_at' => date('Y-m-d H:i:s'),
        ];

        if ($this->db->table_exists('incident_coordination')) {
            $this->db->insert('incident_coordination', $payload);
        } else {
            $this->session->set_flashdata('error', 'Bảng `incident_coordination` chưa tồn tại. Vui lòng chạy file `db/archives/create_incident_coordination.sql` trước.');
            redirect($_SERVER['HTTP_REFERER'] ?? 'uc16_gn_dp');
        }

        // For technical: do NOT change status (keep at 0 so leader sees it)
        // For leader: change status to 2 (in-progress) so it disappears
        $update = [];
        if (!$is_technical) {
            $update['status'] = 2; // in-progress (only for leader actions)
        }
        if (!empty($payload['assignee_id'])) {
            $update['assignee_id'] = $payload['assignee_id'];
        }
        if (!empty($update)) {
            $this->db->where('id', $incident_id)->update('incident_reports', $update);
        }

        // Set appropriate success message
        if ($is_technical) {
            $this->session->set_flashdata('success', 'Báo cáo sửa chữa đã được gửi thành công.');
        } else {
            $this->session->set_flashdata('success', 'Đã phân công/ghi nhận hành động điều phối.');
        }
        redirect($_SERVER['HTTP_REFERER'] ?? 'uc16_gn_dp');
    }

    /**
     * Mark coordination action or incident as completed (leader triggers)
     * POST param: incident_id, coordination_id (optional), notes
     */
    public function mark_completed()
    {
        if (!$this->is_leader()) {
            show_error('Access Denied - Only Leader can mark completion', 403, 'Forbidden');
        }

        $incident_id = (int)$this->input->post('incident_id');
        $coordination_id = $this->input->post('coordination_id');

        if (!$incident_id) {
            $this->session->set_flashdata('error', 'Thiếu incident_id');
            redirect($_SERVER['HTTP_REFERER'] ?? 'uc16_gn_dp');
        }

        // Update coordination entry if provided
        if ($coordination_id) {
            if ($this->db->table_exists('incident_coordination')) {
                $this->db->where('id', $coordination_id)->update('incident_coordination', [
                    'status' => 'completed',
                    'notes' => $this->input->post('notes') ?: null,
                ]);
            }
        }

        // Mark incident as completed
        $this->db->where('id', $incident_id)->update('incident_reports', [
            'status' => 1,
            'resolved_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        // Optionally insert a coordination record noting completion (only if table exists)
        if ($this->db->table_exists('incident_coordination')) {
            $this->db->insert('incident_coordination', [
                'incident_id' => $incident_id,
                'leader_id' => $this->user_id,
                'action_type' => 'mark_completed',
                'notes' => $this->input->post('notes') ?: 'Marked completed by leader',
                'status' => 'completed',
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        }

        $this->session->set_flashdata('success', 'Đã đánh dấu hoàn tất xử lý sự cố.');
        redirect($_SERVER['HTTP_REFERER'] ?? 'uc16_gn_dp');
    }

    /**
     * API: get coordination history as JSON
     */
    public function history($incident_id)
    {
        if ($this->db->table_exists('incident_coordination')) {
            $coord = $this->db->where('incident_id', (int)$incident_id)->order_by('created_at', 'ASC')->get('incident_coordination')->result();
        } else {
            $coord = [];
        }
        $this->output->set_content_type('application/json')->set_output(json_encode($coord));
    }
}



