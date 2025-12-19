<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ShiftModel extends CI_Model
{
    protected $table_shifts = 'production_shifts';
    protected $table_staff_assignments = 'shift_machine_staff';
    protected $table_machine_assignments = 'shift_machine_assignments';
    protected $table_breakdown_logs = 'machine_breakdown_logs';
    protected $table_machine_staff = 'shift_machine_staff'; // NEW: Phân công nhân sự vào máy

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    // ==================== SHIFT CRUD ====================

    /**
     * Lấy danh sách ca theo filter
     */
    public function getShifts($filters = [])
    {
        $this->db->select('production_shifts.*, 
            pl.line_code, pl.line_name,
            z.zone_code, z.zone_name,
            COUNT(DISTINCT ssa.staff_id) as assigned_staff_count,
            COUNT(DISTINCT ssa.machine_id) as assigned_machine_count');
        $this->db->from($this->table_shifts);
        $this->db->join('production_lines pl', 'production_shifts.line_id = pl.id', 'left');
        $this->db->join('zones z', 'pl.zone_id = z.zone_id', 'left');
        $this->db->join($this->table_staff_assignments . ' ssa', 'ssa.shift_id = production_shifts.shift_id AND ssa.status = 1', 'left');

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

        if (!empty($filters['staff_status'])) {
            $this->db->where('production_shifts.staff_status', $filters['staff_status']);
        }

        $this->db->group_by('production_shifts.shift_id');
        $this->db->order_by('production_shifts.shift_date', 'ASC');
        $this->db->order_by('production_shifts.start_time', 'ASC');

        return $this->db->get()->result();
    }

    /**
     * Lấy chi tiết 1 ca
     */
    public function getShiftById($shift_id)
    {
        $this->db->select('production_shifts.*, 
            pl.line_code, pl.line_name,
            z.zone_code, z.zone_name,
            u.username as created_by_username');
        $this->db->from($this->table_shifts);
        $this->db->join('production_lines pl', 'production_shifts.line_id = pl.id', 'left');
        $this->db->join('zones z', 'pl.zone_id = z.zone_id', 'left');
        $this->db->join('user u', 'production_shifts.created_by = u.user_id', 'left');
        $this->db->where('production_shifts.shift_id', $shift_id);
        return $this->db->get()->row();
    }

    /**
     * Tạo ca mới
     */
    public function createShift($data)
    {
        // Auto-generate shift_code if not provided
        if (!isset($data['shift_code']) || $data['shift_code'] === '' || $data['shift_code'] === null) {
            $data['shift_code'] = $this->generateShiftCode(
                isset($data['shift_date']) ? $data['shift_date'] : null,
                isset($data['start_time']) ? $data['start_time'] : null,
                isset($data['end_time']) ? $data['end_time'] : null
            );
        }

        $this->db->insert($this->table_shifts, $data);
        return $this->db->insert_id();
    }

    /**
     * Sinh mã ca (shift_code) dựa trên khung giờ
     * Quy ước mặc định:
     *  - 06:00-<14:00  => CA1
     *  - 14:00-<22:00  => CA2
     *  - Còn lại       => CA3 (ca đêm)
     */
    private function generateShiftCode($shift_date = null, $start_time = null, $end_time = null)
    {
        // Nếu thiếu start_time thì dùng CA1 làm mặc định an toàn
        if (empty($start_time)) {
            return 'CA1';
        }

        // Lấy giờ bắt đầu (0-23)
        $hour = (int)substr($start_time, 0, 2);

        if ($hour >= 6 && $hour < 14) {
            return 'CA1';
        }

        if ($hour >= 14 && $hour < 22) {
            return 'CA2';
        }

        // Các khung còn lại coi như ca đêm
        return 'CA3';
    }

    /**
     * Cập nhật ca
     */
    public function updateShift($shift_id, $data)
    {
        $this->db->where('shift_id', $shift_id);
        return $this->db->update($this->table_shifts, $data);
    }

    // ==================== STAFF ASSIGNMENT ====================

    /**
     * Lấy danh sách nhân sự đã phân công cho ca
     */
    public function getAssignedStaff($shift_id)
    {
        // NEW: Get staff from shift_machine_staff (staff assigned to machines)
        $this->db->select('sms.id, sms.shift_id, sms.machine_id, sms.staff_id,
            user.username,
            COALESCE(CONCAT("NV", LPAD(staff.id_staff, 4, "0")), user.username) as staff_code, 
            COALESCE(staff.staff_name, user.full_name, user.username) as full_name, 
            staff.department, 
            staff.position,
            roles.role_name as role_in_shift,
            machines.code as machine_code,
            machines.name as machine_name');
        $this->db->from('shift_machine_staff sms');
        $this->db->join('user', 'user.user_id = sms.staff_id', 'inner');
        $this->db->join('staff', 'staff.id_staff = user.staff_id', 'left');
        $this->db->join('roles', 'roles.role_id = user.role_id', 'left');
        $this->db->join('machines', 'machines.id = sms.machine_id', 'left');
        $this->db->where('sms.shift_id', $shift_id);
        $this->db->where('sms.status', 1);
        $this->db->order_by('machines.code', 'ASC');
        return $this->db->get()->result();
    }

    /**
     * Lấy danh sách nhân sự khả dụng cho ca
     * - Active (st_status = 1)
     * - Không bị trùng ca (overlap thời gian)
     */
    public function getAvailableStaff($shift_date, $start_time, $end_time, $exclude_shift_id = null)
    {
        // Get all active staff
        $this->db->select('staff.id_staff, 
            CONCAT("NV", LPAD(staff.id_staff, 4, "0")) as staff_code, 
            staff.staff_name as full_name, 
            staff.department, 
            staff.position, 
            staff.st_status');
        $this->db->from('staff');
        $this->db->where('staff.st_status', 1); // Active only
        
        $all_staff = $this->db->get()->result();
        
        // Get staff already assigned to overlapping shifts
        $this->db->select('ssa.staff_id');
        $this->db->from($this->table_staff_assignments . ' ssa');
        $this->db->join($this->table_shifts . ' ps', 'ps.shift_id = ssa.shift_id');
        $this->db->where('ssa.status', 1);
        $this->db->where('ps.shift_date', $shift_date);
        $this->db->group_start();
        $this->db->where("ps.start_time < '{$end_time}'", null, false);
        $this->db->where("ps.end_time > '{$start_time}'", null, false);
        $this->db->group_end();
        
        if ($exclude_shift_id) {
            $this->db->where('ssa.shift_id !=', $exclude_shift_id);
        }
        
        $assigned_staff = $this->db->get()->result();
        $assigned_ids = array_column($assigned_staff, 'staff_id');
        
        // Filter out assigned staff
        $available_staff = array_filter($all_staff, function($staff) use ($assigned_ids) {
            return !in_array($staff->id_staff, $assigned_ids);
        });
        
        return array_values($available_staff); // Re-index array

        return $this->db->get()->result();
    }

    /**
     * Lấy danh sách user theo vai trò hệ thống (role_name)
     * Dùng cho phân nhân sự vào máy theo loại máy (worker / qc_staff ...)
     *
     * @param array $role_names Danh sách role_name cần lọc, ví dụ ['worker', 'production_staff']
     * @return array
     */
    public function getStaffByRole($role_names = [])
    {
        // Nếu bảng user không tồn tại thì trả rỗng để tránh lỗi
        if (!$this->db->table_exists('user')) {
            return [];
        }

        $this->db->select('
            u.user_id,
            u.username,
            COALESCE(s.staff_name, u.full_name, u.username) AS full_name,
            r.role_name,
            r.role_display_name,
            s.department,
            s.position
        ');
        $this->db->from('user u');

        // Join staff info nếu có
        if ($this->db->table_exists('staff')) {
            $this->db->join('staff s', 's.id_staff = u.staff_id', 'left');
        } else {
            $this->db->join('staff s', '1=0', 'left');
        }

        // Join roles nếu schema RBAC đã tồn tại
        if ($this->db->table_exists('roles')) {
            $this->db->join('roles r', 'r.role_id = u.role_id', 'left');
        } else {
            $this->db->join('roles r', '1=0', 'left');
        }

        // Chỉ lấy user đang active
        if ($this->db->field_exists('is_active', 'user')) {
            $this->db->where('u.is_active', 1);
        }

        // Lọc theo danh sách role_name nếu được truyền vào và bảng roles tồn tại
        if (!empty($role_names) && $this->db->table_exists('roles')) {
            $this->db->where_in('r.role_name', $role_names);
        }

        $this->db->order_by('full_name', 'ASC');

        return $this->db->get()->result();
    }

    /**
     * Phân công nhân sự vào ca (single)
     */
    public function assignStaff($shift_id, $staff_id, $role_in_shift, $assigned_by)
    {
        // Check if already assigned
        $existing = $this->db->where('shift_id', $shift_id)
            ->where('staff_id', $staff_id)
            ->where('status', 1)
            ->get($this->table_staff_assignments)
            ->row();

        if ($existing) {
            // Update role
            return $this->db->where('assignment_id', $existing->assignment_id)
                ->update($this->table_staff_assignments, ['role_in_shift' => $role_in_shift]);
        } else {
            // Insert new
            return $this->db->insert($this->table_staff_assignments, [
                'shift_id' => $shift_id,
                'staff_id' => $staff_id,
                'role_in_shift' => $role_in_shift,
                'assigned_by' => $assigned_by
            ]);
        }
    }

    /**
     * Phân công hàng loạt
     */
    public function batchAssignStaff($shift_id, $staff_list, $assigned_by)
    {
        $this->db->trans_start();

        foreach ($staff_list as $item) {
            $this->assignStaff($shift_id, $item['staff_id'], $item['role_in_shift'], $assigned_by);
        }

        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    /**
     * Xóa phân công nhân sự
     */
    public function removeStaffAssignment($shift_id, $staff_id)
    {
        return $this->db->where('shift_id', $shift_id)
            ->where('staff_id', $staff_id)
            ->update($this->table_staff_assignments, ['status' => 0]);
    }

    /**
     * Cập nhật trạng thái nhân sự của ca
     */
    public function updateShiftStaffStatus($shift_id)
    {
        $assigned_count = $this->db->where('shift_id', $shift_id)
            ->where('status', 1)
            ->count_all_results($this->table_staff_assignments);

        $status = 'pending';
        if ($assigned_count >= 5) {
            $status = 'sufficient';
        } elseif ($assigned_count > 0) {
            $status = 'insufficient';
        }

        $this->db->where('shift_id', $shift_id);
        $this->db->update($this->table_shifts, ['staff_status' => $status]);
    }

    // ==================== MACHINE ASSIGNMENT ====================

    /**
     * Lấy danh sách máy đã gán cho ca
     */
    // DEPRECATED: Use getMachinesWithStaff() instead
    // Machines are now permanently assigned to lines, not to shifts
    public function getAssignedMachines($shift_id)
    {
        // Return empty array - machines are now fetched via getMachinesWithStaff()
        return [];
    }

    /**
     * Lấy danh sách máy khả dụng cho ca
     * - Status = Ready
     * - Không đang maintenance
     * - Không trùng lịch với ca khác
     */
    public function getAvailableMachines($line_id, $shift_date, $start_time, $end_time, $exclude_shift_id = null)
    {
        // Machines are now permanently assigned to lines, so return all active machines of the line
        $this->db->select('machines.id as machine_id, machines.code as machine_code, machines.name as machine_name, machines.stage_type as machine_type, machines.status');
        $this->db->from('machines');
        $this->db->where('machines.line_id', $line_id);
        $this->db->where('machines.status', 'active');
        $this->db->order_by('machines.code', 'ASC');
        
        return $this->db->get()->result();
    }

    /**
     * Gán máy cho ca
     */
    public function assignMachine($shift_id, $machine_id, $start_at, $assigned_by)
    {
        $data = [
            'shift_id' => $shift_id,
            'machine_id' => $machine_id,
            'start_at' => $start_at,
            'assignment_status' => 'active',
            'assigned_by' => $assigned_by
        ];

        $this->db->insert($this->table_machine_assignments, $data);
        
        // Update shift machine status
        $this->db->where('shift_id', $shift_id);
        $this->db->update($this->table_shifts, ['machine_status' => 'assigned']);

        return $this->db->insert_id();
    }

    /**
     * Xử lý breakdown: đổi máy
     */
    public function handleMachineBreakdown($shift_id, $old_machine_id, $new_machine_id, $breakdown_time, $reason, $handled_by)
    {
        $this->db->trans_start();

        // Since machines are fixed to lines, handle breakdown by reassigning staff from old machine to new machine
        $this->db->where('shift_id', $shift_id)
            ->where('machine_id', $old_machine_id)
            ->where('status', 1)
            ->update($this->table_machine_staff, [
                'machine_id' => $new_machine_id,
                'assigned_at' => $breakdown_time,
                'assigned_by' => $handled_by,
                'notes' => 'Reassigned due to breakdown: ' . $reason
            ]);

        // 3. Ghi log breakdown
        $this->db->insert($this->table_breakdown_logs, [
            'shift_id' => $shift_id,
            'old_machine_id' => $old_machine_id,
            'new_machine_id' => $new_machine_id,
            'breakdown_time' => $breakdown_time,
            'reason' => $reason,
            'handled_by' => $handled_by
        ]);

        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    /**
     * Lấy lịch sử breakdown của ca
     */
    public function getBreakdownLogs($shift_id)
    {
        $this->db->select('machine_breakdown_logs.*, 
            m1.code as old_machine_code, m1.name as old_machine_name,
            m2.code as new_machine_code, m2.name as new_machine_name,
            u.username as handled_by_username');
        $this->db->from($this->table_breakdown_logs);
        $this->db->join('machines m1', 'm1.id = machine_breakdown_logs.old_machine_id');
        $this->db->join('machines m2', 'm2.id = machine_breakdown_logs.new_machine_id');
        $this->db->join('user u', 'u.user_id = machine_breakdown_logs.handled_by', 'left');
        $this->db->where('machine_breakdown_logs.shift_id', $shift_id);
        $this->db->order_by('machine_breakdown_logs.breakdown_time', 'DESC');
        return $this->db->get()->result();
    }

    // ==================== NEW: MACHINE-STAFF ASSIGNMENT ====================

    /**
     * Lấy tất cả máy theo dây chuyền (cho logic mới)
     */
    public function getMachinesByLine($line_id)
    {
        $this->db->select('machines.*, 
            machines.code as machine_code, 
            machines.name as machine_name, 
            machines.stage_type as machine_type,
            COALESCE(machines.equipment_category, "production") as equipment_category');
        $this->db->from('machines');
        $this->db->where('machines.line_id', $line_id);
        $this->db->where('machines.status', 'active');
        $this->db->order_by('machines.code', 'ASC');
        return $this->db->get()->result();
    }

    /**
     * Lấy danh sách nhân sự đã gán vào máy cụ thể trong ca
     */
    public function getStaffByMachine($shift_id, $machine_id)
    {
        $this->db->select('shift_machine_staff.*, 
            user.username, user.email, 
            staff.staff_name as full_name, 
            staff.department, staff.position,
            roles.role_name');
        $this->db->from($this->table_machine_staff);
        $this->db->join('user', 'user.user_id = shift_machine_staff.staff_id');
        $this->db->join('staff', 'staff.id_staff = shift_machine_staff.staff_id', 'left');
        $this->db->join('roles', 'roles.role_id = user.role_id', 'left');
        $this->db->where('shift_machine_staff.shift_id', $shift_id);
        $this->db->where('shift_machine_staff.machine_id', $machine_id);
        $this->db->where('shift_machine_staff.status', 1);
        return $this->db->get()->result();
    }

    /**
     * Lấy tất cả máy của ca với danh sách nhân sự đã gán
     */
    public function getMachinesWithStaff($shift_id, $line_id)
    {
        $machines = $this->getMachinesByLine($line_id);
        
        foreach ($machines as &$machine) {
            $machine->assigned_staff = $this->getStaffByMachine($shift_id, $machine->id);
        }
        
        return $machines;
    }

    /**
     * Phân công nhân sự vào máy cụ thể
     */
    public function assignStaffToMachine($shift_id, $machine_id, $staff_id, $assigned_by, $notes = null)
    {
        // Check if already assigned
        $exists = $this->db->where([
            'shift_id' => $shift_id,
            'machine_id' => $machine_id,
            'staff_id' => $staff_id,
            'status' => 1
        ])->get($this->table_machine_staff)->row();

        if ($exists) {
            return false; // Already assigned
        }

        $data = [
            'shift_id' => $shift_id,
            'machine_id' => $machine_id,
            'staff_id' => $staff_id,
            'assigned_by' => $assigned_by,
            'notes' => $notes
        ];

        return $this->db->insert($this->table_machine_staff, $data);
    }

    /**
     * Xóa phân công nhân sự khỏi máy
     */
    public function removeStaffFromMachine($assignment_id)
    {
        $this->db->where('id', $assignment_id);
        return $this->db->update($this->table_machine_staff, ['status' => 0]);
    }

    /**
     * Chọn máy cho ca
     */
    public function assignMachineToShift($shift_id, $machine_id, $assigned_by)
    {
        // Check if already selected
        $exists = $this->db->where([
            'shift_id' => $shift_id,
            'machine_id' => $machine_id
        ])->get($this->table_selected_machines)->row();

        if ($exists) {
            return false; // Already selected
        }

        return $this->db->insert($this->table_selected_machines, [
            'shift_id' => $shift_id,
            'machine_id' => $machine_id,
            'assigned_by' => $assigned_by,
            'assigned_at' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Bỏ chọn máy khỏi ca
     */
    public function removeMachineFromShift($shift_id, $machine_id)
    {
        $this->db->where('shift_id', $shift_id);
        $this->db->where('machine_id', $machine_id);
        return $this->db->delete($this->table_selected_machines);
    }

    /**
     * Lấy danh sách máy đã chọn cho ca
     */
    public function getSelectedMachines($shift_id)
    {
        $this->db->select('shift_selected_machines.*, machines.code, machines.name, machines.stage_type, machines.machine_type');
        $this->db->from($this->table_selected_machines);
        $this->db->join('machines', 'machines.id = shift_selected_machines.machine_id');
        $this->db->where('shift_selected_machines.shift_id', $shift_id);
        return $this->db->get()->result();
    }
}
