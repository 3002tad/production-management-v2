<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ShiftModel extends CI_Model
{
    protected $table_shifts = 'production_shifts';
    protected $table_staff_assignments = 'shift_staff_assignments';
    protected $table_machine_assignments = 'shift_machine_assignments';
    protected $table_breakdown_logs = 'machine_breakdown_logs';

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
            COUNT(DISTINCT sma.machine_id) as assigned_machine_count');
        $this->db->from($this->table_shifts);
        $this->db->join('production_lines pl', 'production_shifts.line_id = pl.id', 'left');
        $this->db->join('zones z', 'pl.zone_id = z.zone_id', 'left');
        $this->db->join($this->table_staff_assignments . ' ssa', 'ssa.shift_id = production_shifts.shift_id AND ssa.status = 1', 'left');
        $this->db->join($this->table_machine_assignments . ' sma', 'sma.shift_id = production_shifts.shift_id AND sma.assignment_status = "active"', 'left');

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
        $this->db->insert($this->table_shifts, $data);
        return $this->db->insert_id();
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
        $this->db->select('shift_staff_assignments.*, 
            CONCAT("NV", LPAD(staff.id_staff, 4, "0")) as staff_code, 
            staff.staff_name as full_name, 
            staff.department, 
            staff.position');
        $this->db->from($this->table_staff_assignments);
        $this->db->join('staff', 'staff.id_staff = shift_staff_assignments.staff_id');
        $this->db->where('shift_staff_assignments.shift_id', $shift_id);
        $this->db->where('shift_staff_assignments.status', 1);
        $this->db->order_by('shift_staff_assignments.role_in_shift', 'ASC');
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
    public function getAssignedMachines($shift_id)
    {
        $this->db->select('shift_machine_assignments.*, 
            machines.code as machine_code, machines.name as machine_name, machines.stage_type as machine_type, machines.status as machine_status');
        $this->db->from($this->table_machine_assignments);
        $this->db->join('machines', 'machines.id = shift_machine_assignments.machine_id');
        $this->db->where('shift_machine_assignments.shift_id', $shift_id);
        $this->db->order_by('shift_machine_assignments.start_at', 'DESC');
        return $this->db->get()->result();
    }

    /**
     * Lấy danh sách máy khả dụng cho ca
     * - Status = Ready
     * - Không đang maintenance
     * - Không trùng lịch với ca khác
     */
    public function getAvailableMachines($line_id, $shift_date, $start_time, $end_time, $exclude_shift_id = null)
    {
        // Get all active machines
        $this->db->select('machines.id as machine_id, machines.code as machine_code, machines.name as machine_name, machines.stage_type as machine_type, machines.status');
        $this->db->from('machines');
        $this->db->where('machines.status', 'active'); // Only active machines
        
        $all_machines = $this->db->get()->result();
        
        // Get machines already assigned to overlapping shifts
        $datetime_start = $shift_date . ' ' . $start_time;
        $datetime_end = $shift_date . ' ' . $end_time;
        
        $this->db->select('sma.machine_id');
        $this->db->from($this->table_machine_assignments . ' sma');
        $this->db->join($this->table_shifts . ' ps', 'ps.shift_id = sma.shift_id');
        $this->db->where('sma.assignment_status', 'active');
        $this->db->where('ps.shift_date', $shift_date);
        $this->db->group_start();
        $this->db->where("sma.start_at < '{$datetime_end}'", null, false);
        $this->db->group_start();
        $this->db->where('sma.end_at IS NULL', null, false);
        $this->db->or_where("sma.end_at > '{$datetime_start}'", null, false);
        $this->db->group_end();
        $this->db->group_end();
        
        if ($exclude_shift_id) {
            $this->db->where('sma.shift_id !=', $exclude_shift_id);
        }
        
        $assigned_machines = $this->db->get()->result();
        $assigned_ids = array_column($assigned_machines, 'machine_id');
        
        // Filter out assigned machines
        $available_machines = array_filter($all_machines, function($machine) use ($assigned_ids) {
            return !in_array($machine->machine_id, $assigned_ids);
        });
        
        return array_values($available_machines); // Re-index array
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

        // 1. Đóng assignment máy cũ
        $this->db->where('shift_id', $shift_id)
            ->where('machine_id', $old_machine_id)
            ->where('assignment_status', 'active')
            ->update($this->table_machine_assignments, [
                'end_at' => $breakdown_time,
                'assignment_status' => 'breakdown',
                'breakdown_reason' => $reason
            ]);

        // 2. Tạo assignment máy mới
        $this->db->insert($this->table_machine_assignments, [
            'shift_id' => $shift_id,
            'machine_id' => $new_machine_id,
            'start_at' => $breakdown_time,
            'assignment_status' => 'active',
            'assigned_by' => $handled_by
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
}
