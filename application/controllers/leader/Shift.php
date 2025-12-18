<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Shift extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('leader/ShiftModel', 'shiftModel');
        $this->load->library('session');

        // Check if user is logged in
        if (!$this->session->userdata('user_id')) {
            redirect('login/');
        }

        // RBAC: Check if user has leader access
        $role = $this->session->userdata('role');
        if (empty($role)) {
            $role = $this->session->userdata('role_name');
        }
        $role = strtolower(trim((string)$role));

        $allowed_roles = ['leader', 'line_manager', 'admin', 'bod', 'system_admin'];

        if (!in_array($role, $allowed_roles, true)) {
            show_error('Access Denied - Leader Only. Your role: ' . var_export($role, true), 403, 'Forbidden');
        }
    }

    /**
     * Test endpoint to check if controller works
     */
    public function test()
    {
        echo json_encode([
            'success' => true,
            'message' => 'Shift controller is working',
            'timestamp' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Dashboard Ca làm việc - Grouped by Plan
     */
    public function index()
    {
        // Get filters (NO default date filter)
        $filters = [
            'line_id' => $this->input->get('line_id'),
            'shift_date' => $this->input->get('shift_date'), // No default
            'date_from' => $this->input->get('date_from'),
            'date_to' => $this->input->get('date_to'),
            'shift_status' => $this->input->get('shift_status'),
            'id_plan' => $this->input->get('id_plan')
        ];

        // Get production plans with shift counts
        $this->db->select('
            planning.id_plan, 
            planning.plan_name, 
            planning.pl_status,
            planning.qty_target,
            planning.end_date
        ');
        $this->db->from('planning');

        if (!empty($filters['id_plan'])) {
            $this->db->where('planning.id_plan', $filters['id_plan']);
        }
        
        $this->db->order_by('planning.pl_status', 'ASC'); // Active plans first
        $this->db->order_by('planning.end_date', 'ASC');
        $plans = $this->db->get()->result();
        
        // Calculate shift counts for each plan
        foreach ($plans as $plan) {
            // Count suggested shifts from plan_shift
            $plan->suggested_shift_count = $this->db->where('id_plan', $plan->id_plan)->count_all_results('plan_shift');
            
            // Count actual shifts
            $plan->actual_shift_count = $this->db->where('id_plan', $plan->id_plan)->count_all_results('production_shifts');
            
            // Count completed shifts (status = 3)
            $plan->completed_shift_count = $this->db->where('id_plan', $plan->id_plan)->where('shift_status', 3)->count_all_results('production_shifts');
        }

        // Get shifts for each plan
        foreach ($plans as $plan) {
            $this->db->select('production_shifts.*, 
                pl.line_code, pl.line_name,
                z.zone_code, z.zone_name,
                COUNT(DISTINCT sms.staff_id) as assigned_staff_count');
            $this->db->from('production_shifts');
            $this->db->join('production_lines pl', 'production_shifts.line_id = pl.id', 'left');
            $this->db->join('zones z', 'pl.zone_id = z.zone_id', 'left');
            $this->db->join('shift_machine_staff sms', 'sms.shift_id = production_shifts.shift_id AND sms.status = 1', 'left');
            $this->db->where('production_shifts.id_plan', $plan->id_plan);
            
            // Apply filters
            if (!empty($filters['line_id'])) {
                $this->db->where('production_shifts.line_id', $filters['line_id']);
            }
            if (!empty($filters['shift_date'])) {
                $this->db->where('production_shifts.shift_date', $filters['shift_date']);
            }
            if (!empty($filters['date_from'])) {
                $this->db->where('production_shifts.shift_date >=', $filters['date_from']);
            }
            if (!empty($filters['date_to'])) {
                $this->db->where('production_shifts.shift_date <=', $filters['date_to']);
            }
            if (isset($filters['shift_status']) && $filters['shift_status'] !== '') {
                $this->db->where('production_shifts.shift_status', $filters['shift_status']);
            }
            
            $this->db->group_by('production_shifts.shift_id');
            $this->db->order_by('production_shifts.shift_date', 'ASC');
            $this->db->order_by('production_shifts.start_time', 'ASC');
            $plan->shifts = $this->db->get()->result();
        } // End foreach plans

        // Get lines for filter dropdown
        $this->db->select('pl.id, pl.line_code, pl.line_name, z.zone_name');
        $this->db->from('production_lines pl');
        $this->db->join('zones z', 'pl.zone_id = z.zone_id', 'left');
        $this->db->order_by('z.zone_code, pl.line_code');
        $lines = $this->db->get()->result();

        // Get all planning for filter dropdown
        $this->db->select('id_plan, plan_name');
        $this->db->from('planning');
        $this->db->order_by('pl_status', 'ASC');
        $this->db->order_by('plan_name', 'ASC');
        $all_plans = $this->db->get()->result();

        $data = [
            'plans' => $plans,
            'lines' => $lines,
            'all_plans' => $all_plans,
            'filters' => $filters,
            'content' => 'leader/shift/dashboard',
            'navlink' => 'shift'
        ];

        $this->load->view('leader/VBackend', $data);
    } // End index()

    /**
     * Chi tiết 1 ca - Tab Nhân sự & Tab Máy
     */
    public function detail($shift_id = null)
    {
        if (!$shift_id) {
            $this->session->set_flashdata('error', 'Không tìm thấy ca làm việc');
            redirect('leader/shift');
            return;
        }

        $shift = $this->shiftModel->getShiftById($shift_id);

        if (!$shift) {
            show_404();
        }

        // Tab active
        $active_tab = $this->input->get('tab') ?: 'staff';

        // Get assigned staff (from shift_machine_staff - staff assigned to machines)
        $assigned_staff = $this->shiftModel->getAssignedStaff($shift_id);

        // Get breakdown logs
        $breakdown_logs = $this->shiftModel->getBreakdownLogs($shift_id);

        $data = [
            'shift' => $shift,
            'assigned_staff' => $assigned_staff,
            'breakdown_logs' => $breakdown_logs,
            'active_tab' => $active_tab,
            'content' => 'leader/shift/detail',
            'navlink' => 'shift'
        ];

        $this->load->view('leader/VBackend', $data);
    }

    /**
     * API: Lấy danh sách nhân sự khả dụng
     */
    public function get_available_staff()
    {
        header('Content-Type: application/json');
        
        $shift_id = $this->input->post('shift_id');
        
        if (!$shift_id) {
            echo json_encode(['success' => false, 'message' => 'shift_id is required']);
            return;
        }
        
        $shift = $this->shiftModel->getShiftById($shift_id);

        if (!$shift) {
            echo json_encode(['success' => false, 'message' => 'Ca không tồn tại']);
            return;
        }

        try {
            $available_staff = $this->shiftModel->getAvailableStaff(
                $shift->shift_date,
                $shift->start_time,
                $shift->end_time,
                $shift_id
            );
            
            echo json_encode([
                'success' => true, 
                'data' => $available_staff ?: [], 
                'count' => count($available_staff),
                'shift_date' => $shift->shift_date,
                'start_time' => $shift->start_time,
                'end_time' => $shift->end_time
            ]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * API: Lấy danh sách máy theo dây chuyền (cho logic mới)
     */
    public function get_machines_by_line()
    {
        header('Content-Type: application/json');
        
        $line_id = $this->input->post('line_id');
        $shift_id = $this->input->post('shift_id');
        
        if (!$line_id) {
            echo json_encode(['success' => false, 'message' => 'line_id is required']);
            return;
        }
        
        try {
            if ($shift_id) {
                // Get machines with assigned staff
                $machines = $this->shiftModel->getMachinesWithStaff($shift_id, $line_id);
            } else {
                // Just get machines
                $machines = $this->shiftModel->getMachinesByLine($line_id);
            }
            
            echo json_encode([
                'success' => true, 
                'data' => $machines ?: [], 
                'count' => count($machines),
                'line_id' => $line_id
            ]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * API: Lấy nhân viên theo role (worker/qc)
     */
    public function get_staff_by_role()
    {
        header('Content-Type: application/json');
        
        $role = $this->input->post('role'); // 'worker' or 'qc'
        $machine_type = $this->input->post('machine_type'); // 'production' or 'quality_control'
        
        // Map machine type to appropriate roles
        $roles = [];
        if ($machine_type === 'quality_control') {
            $roles = ['qc_staff']; // Changed from 'qc' to match database role_name
        } else {
            $roles = ['worker', 'production_staff']; // Support both worker and production_staff
        }
        
        try {
            $staff = $this->shiftModel->getStaffByRole($roles);
            
            echo json_encode([
                'success' => true, 
                'data' => $staff ?: [], 
                'count' => count($staff),
                'roles_filter' => $roles
            ]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Phân công nhân sự (single)
     */
    public function assign_staff()
    {
        $shift_id = $this->input->post('shift_id');
        $staff_id = $this->input->post('staff_id');
        $role_in_shift = $this->input->post('role_in_shift') ?: 'worker';

        $assigned_by = $this->session->userdata('user_id');

        $result = $this->shiftModel->assignStaff($shift_id, $staff_id, $role_in_shift, $assigned_by);

        if ($result) {
            // Update shift staff status
            $this->shiftModel->updateShiftStaffStatus($shift_id);

            $this->session->set_flashdata('success', 'Phân công nhân sự thành công');
        } else {
            $this->session->set_flashdata('error', 'Phân công thất bại');
        }

        redirect('leader/shift/detail/' . $shift_id . '?tab=staff');
    }

    /**
     * Phân công hàng loạt
     */
    public function batch_assign_staff()
    {
        $shift_id = $this->input->post('shift_id');
        $staff_list = $this->input->post('staff_list'); // Array of {staff_id, role_in_shift}

        if (empty($staff_list)) {
            $this->session->set_flashdata('error', 'Chưa chọn nhân sự');
            redirect('leader/shift/detail/' . $shift_id . '?tab=staff');
            return;
        }

        $assigned_by = $this->session->userdata('user_id');
        $result = $this->shiftModel->batchAssignStaff($shift_id, $staff_list, $assigned_by);

        if ($result) {
            $this->shiftModel->updateShiftStaffStatus($shift_id);
            $this->session->set_flashdata('success', 'Phân công hàng loạt thành công');
        } else {
            $this->session->set_flashdata('error', 'Phân công thất bại');
        }

        redirect('leader/shift/detail/' . $shift_id . '?tab=staff');
    }

    /**
     * Xóa phân công nhân sự
     */
    public function remove_staff()
    {
        $shift_id = $this->input->post('shift_id');
        $staff_id = $this->input->post('staff_id');

        $result = $this->shiftModel->removeStaffAssignment($shift_id, $staff_id);

        if ($result) {
            $this->shiftModel->updateShiftStaffStatus($shift_id);
            $this->session->set_flashdata('success', 'Đã xóa phân công');
        } else {
            $this->session->set_flashdata('error', 'Xóa thất bại');
        }

        redirect('leader/shift/detail/' . $shift_id . '?tab=staff');
    }

    /**
     * API: Lấy danh sách máy khả dụng
     */
    public function get_available_machines()
    {
        header('Content-Type: application/json');
        
        $shift_id = $this->input->post('shift_id');
        
        if (!$shift_id) {
            echo json_encode(['success' => false, 'message' => 'shift_id is required']);
            return;
        }
        
        $shift = $this->shiftModel->getShiftById($shift_id);

        if (!$shift) {
            echo json_encode(['success' => false, 'message' => 'Ca không tồn tại']);
            return;
        }

        try {
            $available_machines = $this->shiftModel->getAvailableMachines(
                $shift->line_id,
                $shift->shift_date,
                $shift->start_time,
                $shift->end_time,
                $shift_id
            );
            
            echo json_encode([
                'success' => true, 
                'data' => $available_machines ?: [], 
                'count' => count($available_machines),
                'line_id' => $shift->line_id
            ]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Gán máy cho ca
     */
    public function assign_machine()
    {
        $shift_id = $this->input->post('shift_id');
        $machine_id = $this->input->post('machine_id');
        $shift = $this->shiftModel->getShiftById($shift_id);

        if (!$shift) {
            $this->session->set_flashdata('error', 'Ca không tồn tại');
            redirect('leader/shift/detail/' . $shift_id . '?tab=machine');
            return;
        }

        $start_at = $shift->shift_date . ' ' . $shift->start_time;
        $assigned_by = $this->session->userdata('user_id');

        $result = $this->shiftModel->assignMachine($shift_id, $machine_id, $start_at, $assigned_by);

        if ($result) {
            $this->session->set_flashdata('success', 'Gán máy thành công');
        } else {
            $this->session->set_flashdata('error', 'Gán máy thất bại');
        }

        redirect('leader/shift/detail/' . $shift_id . '?tab=machine');
    }

    /**
     * NEW: Gán nhân sự vào máy cụ thể
     */
    public function assign_staff_to_machine()
    {
        $shift_id = $this->input->post('shift_id');
        $machine_id = $this->input->post('machine_id');
        $staff_id = $this->input->post('staff_id');
        $notes = $this->input->post('notes');

        if (!$shift_id || !$machine_id || !$staff_id) {
            $this->session->set_flashdata('error', 'Thiếu thông tin phân công');
            redirect('leader/shift/detail/' . $shift_id . '?tab=machine');
            return;
        }

        $assigned_by = $this->session->userdata('user_id');

        $result = $this->shiftModel->assignStaffToMachine($shift_id, $machine_id, $staff_id, $assigned_by, $notes);

        if ($result) {
            $this->session->set_flashdata('success', 'Phân công nhân sự vào máy thành công');
        } else {
            $this->session->set_flashdata('error', 'Nhân sự đã được gán vào máy này');
        }

        redirect('leader/shift/detail/' . $shift_id . '?tab=machine');
    }

    /**
     * NEW: Xóa phân công nhân sự khỏi máy
     */
    public function remove_staff_from_machine()
    {
        $assignment_id = $this->input->post('assignment_id');
        $shift_id = $this->input->post('shift_id');

        if (!$assignment_id) {
            $this->session->set_flashdata('error', 'Thiếu thông tin');
            redirect('leader/shift/detail/' . $shift_id . '?tab=machine');
            return;
        }

        $result = $this->shiftModel->removeStaffFromMachine($assignment_id);

        if ($result) {
            $this->session->set_flashdata('success', 'Đã xóa phân công');
        } else {
            $this->session->set_flashdata('error', 'Xóa thất bại');
        }

        redirect('leader/shift/detail/' . $shift_id . '?tab=machine');
    }

    /**
     * Xử lý breakdown - Đổi máy
     */
    public function handle_breakdown()
    {
        $shift_id = $this->input->post('shift_id');
        $old_machine_id = $this->input->post('old_machine_id');
        $new_machine_id = $this->input->post('new_machine_id');
        $breakdown_time = $this->input->post('breakdown_time') ?: date('Y-m-d H:i:s');
        $reason = $this->input->post('reason');

        if (empty($reason)) {
            $this->session->set_flashdata('error', 'Vui lòng nhập lý do breakdown');
            redirect('leader/shift/detail/' . $shift_id . '?tab=machine');
            return;
        }

        $handled_by = $this->session->userdata('user_id');

        $result = $this->shiftModel->handleMachineBreakdown(
            $shift_id,
            $old_machine_id,
            $new_machine_id,
            $breakdown_time,
            $reason,
            $handled_by
        );

        if ($result) {
            $this->session->set_flashdata('success', 'Xử lý breakdown thành công. Đã chuyển sang máy mới.');
        } else {
            $this->session->set_flashdata('error', 'Xử lý breakdown thất bại');
        }

        redirect('leader/shift/detail/' . $shift_id . '?tab=machine');
    }

    /**
     * Form tạo ca mới
     */
    public function create()
    {
        // Load zones and production lines
        $this->load->model('leader/ZoneModel');
        $zones = $this->ZoneModel->getZones();
        
        $this->db->select('pl.*, z.zone_name');
        $this->db->from('production_lines pl');
        $this->db->join('zones z', 'pl.zone_id = z.zone_id', 'left');
        $this->db->order_by('z.zone_code, pl.line_code');
        $lines = $this->db->get()->result();

        // Get id_plan from URL if provided
        $id_plan = $this->input->get('id_plan');
        $plan_info = null;
        
        if ($id_plan) {
            $plan_info = $this->db->get_where('planning', ['id_plan' => $id_plan])->row();
        }

        $data = [
            'zones' => $zones,
            'lines' => $lines,
            'id_plan' => $id_plan,
            'plan_info' => $plan_info,
            'content' => 'leader/shift/create',
            'navlink' => 'shift'
        ];

        $this->load->view('leader/VBackend', $data);
    }

    /**
     * Lưu ca mới
     */
    public function store()
    {
        $shift_data = [
            'line_id' => $this->input->post('line_id'),
            'shift_name' => $this->input->post('shift_name'),
            'shift_date' => $this->input->post('shift_date'),
            'start_time' => $this->input->post('start_time'),
            'end_time' => $this->input->post('end_time'),
            'target_quantity' => $this->input->post('target_quantity'),
            'notes' => $this->input->post('notes'),
            'created_by' => $this->session->userdata('user_id')
        ];

        // Add id_plan if provided
        $id_plan = $this->input->post('id_plan');
        if (!empty($id_plan)) {
            $shift_data['id_plan'] = $id_plan;
        }

        $result = $this->shiftModel->createShift($shift_data);

        if ($result) {
            $this->session->set_flashdata('success', 'Tạo ca làm việc thành công');
            
            // Redirect back to plan detail if id_plan exists, otherwise to shift detail
            if (!empty($id_plan)) {
                redirect('leader/shift?id_plan=' . $id_plan);
            } else {
                redirect('leader/shift/detail/' . $result);
            }
        } else {
            $this->session->set_flashdata('error', 'Tạo ca thất bại');
            redirect('leader/shift/create' . (!empty($id_plan) ? '?id_plan=' . $id_plan : ''));
        }
    }

    /**
     * Form chỉnh sửa ca
     */
    public function edit($shift_id)
    {
        $shift = $this->shiftModel->getShiftById($shift_id);

        if (!$shift) {
            show_404();
        }

        // Load zones and production lines
        $this->load->model('leader/ZoneModel');
        $zones = $this->ZoneModel->getZones();
        
        $this->db->select('pl.*, z.zone_name');
        $this->db->from('production_lines pl');
        $this->db->join('zones z', 'pl.zone_id = z.zone_id', 'left');
        $this->db->order_by('z.zone_code, pl.line_code');
        $lines = $this->db->get()->result();

        $data = [
            'shift' => $shift,
            'zones' => $zones,
            'lines' => $lines,
            'content' => 'leader/shift/edit',
            'navlink' => 'shift'
        ];

        $this->load->view('leader/VBackend', $data);
    }

    /**
     * Cập nhật ca
     */
    public function update($shift_id)
    {
        $shift_data = [
            'line_id' => $this->input->post('line_id'),
            'shift_name' => $this->input->post('shift_name'),
            'shift_date' => $this->input->post('shift_date'),
            'start_time' => $this->input->post('start_time'),
            'end_time' => $this->input->post('end_time'),
            'target_quantity' => $this->input->post('target_quantity'),
            'notes' => $this->input->post('notes'),
        ];

        $result = $this->shiftModel->updateShift($shift_id, $shift_data);

        if ($result) {
            $this->session->set_flashdata('success', 'Cập nhật ca thành công');
        } else {
            $this->session->set_flashdata('error', 'Cập nhật ca thất bại');
        }

        redirect('leader/shift/detail/' . $shift_id);
    }

    /**
     * Xóa ca
     */
    public function delete($shift_id)
    {
        header('Content-Type: application/json');

        $shift = $this->shiftModel->getShiftById($shift_id);
        
        if (!$shift) {
            echo json_encode(['success' => false, 'message' => 'Không tìm thấy ca làm việc']);
            return;
        }

        // Check if shift has started
        $shift_datetime = strtotime($shift->shift_date . ' ' . $shift->start_time);
        if (time() >= $shift_datetime) {
            echo json_encode(['success' => false, 'message' => 'Không thể xóa ca đã bắt đầu hoặc đã qua']);
            return;
        }

        // Delete assignments first
        $this->db->where('shift_id', $shift_id);
        $this->db->delete('shift_staff_assignments');

        $this->db->where('shift_id', $shift_id);
        $this->db->delete('shift_machine_assignments');

        $this->db->where('shift_id', $shift_id);
        $this->db->delete('machine_breakdown_logs');

        // Delete shift
        $this->db->where('id', $shift_id);
        if ($this->db->delete('production_shifts')) {
            echo json_encode(['success' => true, 'message' => 'Xóa ca thành công']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Xóa ca thất bại']);
        }
    }}