<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Machine Model - Quản lý máy/dây chuyền sản xuất
 * 
 * Model chuyên dụng cho module quản lý máy/dây chuyền
 * Hỗ trợ:
 * - CRUD operations cho machines
 * - Status logging và audit trail
 * - Maintenance scheduling
 * - Integration với production shift assignments
 * 
 * @author Copilot AI
 * @date 2025-11-27
 */
class MachineModel extends CI_Model
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Lấy danh sách zones
     */
    public function getZones()
    {
        $this->db->select('*');
        $this->db->from('zones');
        $this->db->where('status', 1);
        $this->db->order_by('zone_code', 'ASC');
        return $this->db->get()->result();
    }

    /**
     * Lấy danh sách production lines theo zone
     */
    public function getLinesByZone($zone_id = null)
    {
        $this->db->select('pl.*, z.zone_name, z.zone_code');
        $this->db->from('production_lines pl');
        $this->db->join('zones z', 'z.zone_id = pl.zone_id', 'left');
        
        if ($zone_id) {
            $this->db->where('pl.zone_id', $zone_id);
        }
        
        $this->db->order_by('z.zone_code', 'ASC');
        $this->db->order_by('pl.line_code', 'ASC');
        return $this->db->get()->result();
    }

    /**
     * Lấy machines grouped by zone and line
     */
    public function getMachinesGrouped()
    {
        $this->db->select('
            m.*,
            pl.line_code,
            pl.line_name,
            pl.line_type,
            pl.is_primary,
            pl.zone_id,
            z.zone_code,
            z.zone_name,
            (SELECT COUNT(*) FROM machine_maintenances mm 
             WHERE mm.machine_id = m.id AND mm.status IN ("planned", "in_progress")) as pending_maintenances
        ');
        $this->db->from('machines m');
        $this->db->join('production_lines pl', 'pl.id = m.line_id', 'left');
        $this->db->join('zones z', 'z.zone_id = pl.zone_id', 'left');
        $this->db->order_by('z.zone_code', 'ASC');
        $this->db->order_by('pl.line_code', 'ASC');
        $this->db->order_by('m.code', 'ASC');
        
        $machines = $this->db->get()->result();
        
        // Group by zone and line
        $grouped = [];
        foreach ($machines as $machine) {
            $zone_key = $machine->zone_id ?: 'unassigned';
            $line_key = $machine->line_id ?: 'unassigned';
            
            if (!isset($grouped[$zone_key])) {
                $grouped[$zone_key] = [
                    'zone_id' => $machine->zone_id,
                    'zone_code' => $machine->zone_code,
                    'zone_name' => $machine->zone_name ?: 'Chưa phân khu',
                    'lines' => []
                ];
            }
            
            if (!isset($grouped[$zone_key]['lines'][$line_key])) {
                $grouped[$zone_key]['lines'][$line_key] = [
                    'line_id' => $machine->line_id,
                    'line_code' => $machine->line_code,
                    'line_name' => $machine->line_name ?: 'Chưa phân dây chuyền',
                    'line_type' => $machine->line_type ?? null,
                    'is_primary' => $machine->is_primary ?? 0,
                    'machines' => []
                ];
            }
            
            $grouped[$zone_key]['lines'][$line_key]['machines'][] = $machine;
        }
        
        return $grouped;
    }

    /**
     * Lấy danh sách máy với filter và pagination
     * 
     * @param array $filters Filter conditions
     * @param int $limit Records per page
     * @param int $offset Starting record
     * @return array Query result and total count
     */
    public function getMachines($filters = [], $limit = 20, $offset = 0)
    {
        // Build base query
        $this->db->select('
            m.*,
            (SELECT COUNT(*) FROM machine_maintenances mm 
             WHERE mm.machine_id = m.id AND mm.status IN ("planned", "in_progress")) as pending_maintenances,
            (SELECT MAX(ml.changed_at) FROM machine_status_logs ml 
             WHERE ml.machine_id = m.id) as last_status_change
        ');
        $this->db->from('machines m');

        // Apply filters
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $this->db->group_start()
                     ->like('m.code', $search)
                     ->or_like('m.name', $search)
                     ->or_like('m.description', $search)
                     ->group_end();
        }

        if (!empty($filters['status'])) {
            $this->db->where('m.status', $filters['status']);
        }

        if (!empty($filters['stage_type'])) {
            $this->db->where('m.stage_type', $filters['stage_type']);
        }

        if (!empty($filters['capacity_min'])) {
            $this->db->where('m.capacity >=', $filters['capacity_min']);
        }

        if (!empty($filters['capacity_max'])) {
            $this->db->where('m.capacity <=', $filters['capacity_max']);
        }

        // Count total for pagination
        $total_query = clone $this->db;
        $total = $total_query->count_all_results('', false);

        // Apply pagination and get results
        $this->db->order_by('m.code', 'ASC');
        $this->db->limit($limit, $offset);
        $machines = $this->db->get()->result();

        return [
            'machines' => $machines,
            'total' => $total,
            'limit' => $limit,
            'offset' => $offset
        ];
    }

    /**
     * Lấy thông tin chi tiết một máy
     * 
     * @param int $id Machine ID
     * @return object|null Machine data
     */
    public function getMachineById($id)
    {
        $this->db->select('
            machines.*,
            pl.id as line_id,
            pl.line_code,
            pl.line_name,
            z.zone_id,
            z.zone_code,
            z.zone_name,
            z.description as zone_description
        ');
        $this->db->from('machines');
        $this->db->join('production_lines pl', 'machines.line_id = pl.id', 'left');
        $this->db->join('zones z', 'pl.zone_id = z.zone_id', 'left');
        $this->db->where('machines.id', $id);
        
        return $this->db->get()->row();
    }

    /**
     * Lấy máy theo code
     * 
     * @param string $code Machine code
     * @return object|null Machine data
     */
    public function getMachineByCode($code)
    {
        $this->db->select('*');
        $this->db->from('machines');
        $this->db->where('code', $code);
        
        return $this->db->get()->row();
    }

    /**
     * Tạo máy mới với validation
     * 
     * @param array $data Machine data
     * @return array Result with success status and message
     */
    public function createMachine($data)
    {
        try {
            // Validate required fields
            $validation = $this->validateMachineData($data);
            if (!$validation['valid']) {
                return $validation;
            }

            // Check unique code
            if ($this->getMachineByCode($data['code'])) {
                return [
                    'success' => false,
                    'message' => 'Mã máy "' . $data['code'] . '" đã tồn tại trong hệ thống',
                    'field' => 'code'
                ];
            }

            // Prepare data (include machine_role)
            $machine_data = [
                'code' => trim($data['code']),
                'name' => trim($data['name']),
                'capacity' => floatval($data['capacity']),
                'stage_type' => $data['stage_type'],
                'status' => $data['status'] ?? 'active',
                'machine_role' => !empty($data['machine_role']) && in_array($data['machine_role'], ['primary','backup']) ? $data['machine_role'] : 'primary',
                'description' => trim($data['description'] ?? ''),
                'location' => trim($data['location'] ?? ''),
                'line_id' => !empty($data['line_id']) ? intval($data['line_id']) : null,
                'purchase_date' => !empty($data['purchase_date']) ? $data['purchase_date'] : null,
                'warranty_until' => !empty($data['warranty_until']) ? $data['warranty_until'] : null,
                'created_by' => $data['created_by'] ?? null
            ];

            // Start transaction
            $this->db->trans_start();

            // Insert machine
            $this->db->insert('machines', $machine_data);
            $machine_id = $this->db->insert_id();

            // Log initial status
            $this->logStatusChange($machine_id, null, $machine_data['status'], 'Khởi tạo máy mới', $data['created_by']);

            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                throw new Exception('Database transaction failed');
            }

            return [
                'success' => true,
                'message' => 'Tạo máy thành công',
                'machine_id' => $machine_id,
                'machine_code' => $machine_data['code']
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Lỗi hệ thống: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Cập nhật thông tin máy
     * 
     * @param int $id Machine ID
     * @param array $data Updated data
     * @return array Result with success status and message
     */
    public function updateMachine($id, $data)
    {
        try {
            // Get current machine
            $current_machine = $this->getMachineById($id);
            if (!$current_machine) {
                return [
                    'success' => false,
                    'message' => 'Không tìm thấy máy cần cập nhật'
                ];
            }

            // Validate data
            $validation = $this->validateMachineData($data, $id);
            if (!$validation['valid']) {
                return $validation;
            }

            // Check if changing to maintenance while machine is in use
            if ($data['status'] === 'maintenance' && $current_machine->status === 'active') {
                $usage_check = $this->checkMachineInUse($id);
                if ($usage_check['in_use']) {
                    return [
                        'success' => false,
                        'message' => 'Máy đang được sử dụng trong ca sản xuất hiện tại',
                        'details' => $usage_check['details'],
                        'suggestion' => 'Vui lòng dừng ca sản xuất hoặc chuyển sang máy khác trước khi bảo trì'
                    ];
                }
            }

            // Prepare update data
            $update_data = [
                'name' => trim($data['name']),
                'capacity' => floatval($data['capacity']),
                'stage_type' => $data['stage_type'],
                'status' => $data['status'],
                'description' => trim($data['description'] ?? ''),
                'location' => trim($data['location'] ?? ''),
                'machine_role' => !empty($data['machine_role']) && in_array($data['machine_role'], ['primary','backup']) ? $data['machine_role'] : 'primary',
                'line_id' => !empty($data['line_id']) ? intval($data['line_id']) : null,
                'purchase_date' => !empty($data['purchase_date']) ? $data['purchase_date'] : null,
                'warranty_until' => !empty($data['warranty_until']) ? $data['warranty_until'] : null,
                'updated_by' => $data['updated_by'] ?? null
            ];

            // Start transaction
            $this->db->trans_start();

            // Update machine
            $this->db->where('id', $id);
            $this->db->update('machines', $update_data);

            // Log status change if different
            if ($current_machine->status !== $data['status']) {
                $reason = $data['status_reason'] ?? 'Cập nhật trạng thái máy';
                $this->logStatusChange($id, $current_machine->status, $data['status'], $reason, $data['updated_by']);
            }

            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                throw new Exception('Database transaction failed');
            }

            return [
                'success' => true,
                'message' => 'Cập nhật máy thành công',
                'status_changed' => $current_machine->status !== $data['status']
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Lỗi hệ thống: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Ghi log thay đổi trạng thái
     * 
     * @param int $machine_id Machine ID
     * @param string $old_status Old status
     * @param string $new_status New status
     * @param string $reason Reason for change
     * @param string $changed_by Username
     * @return bool Success status
     */
    public function logStatusChange($machine_id, $old_status, $new_status, $reason, $changed_by)
    {
        $log_data = [
            'machine_id' => $machine_id,
            'old_status' => $old_status,
            'new_status' => $new_status,
            'reason' => $reason,
            'changed_by_username' => $changed_by,
            'notes' => null
        ];

        return $this->db->insert('machine_status_logs', $log_data);
    }

    /**
     * Lấy lịch sử thay đổi trạng thái máy
     * 
     * @param int $machine_id Machine ID
     * @param int $limit Records limit
     * @return array Status logs
     */
    public function getStatusLogs($machine_id, $limit = 20)
    {
        $this->db->select('*');
        $this->db->from('machine_status_logs');
        $this->db->where('machine_id', $machine_id);
        $this->db->order_by('changed_at', 'DESC');
        $this->db->limit($limit);

        return $this->db->get()->result();
    }

    /**
     * Lấy lịch bảo trì của máy
     * 
     * @param int $machine_id Machine ID
     * @param array $filters Additional filters
     * @return array Maintenance schedules
     */
    public function getMaintenanceSchedules($machine_id, $filters = [])
    {
        $this->db->select('*');
        $this->db->from('machine_maintenances');
        $this->db->where('machine_id', $machine_id);

        // Apply filters
        if (!empty($filters['status'])) {
            if (is_array($filters['status'])) {
                $this->db->where_in('status', $filters['status']);
            } else {
                $this->db->where('status', $filters['status']);
            }
        }

        if (!empty($filters['from_date'])) {
            $this->db->where('start_time >=', $filters['from_date']);
        }

        if (!empty($filters['to_date'])) {
            $this->db->where('end_time <=', $filters['to_date']);
        }

        $this->db->order_by('start_time', 'DESC');
        
        return $this->db->get()->result();
    }

    /**
     * Tạo lịch bảo trì mới
     * 
     * @param array $data Maintenance data
     * @return array Result with success status and message
     */
    public function createMaintenance($data)
    {
        try {
            // Validate maintenance data
            $validation = $this->validateMaintenanceData($data);
            if (!$validation['valid']) {
                return $validation;
            }

            // Check time conflicts
            $conflict_check = $this->checkMaintenanceConflicts($data['machine_id'], $data['start_time'], $data['end_time']);
            if (!$conflict_check['valid']) {
                return $conflict_check;
            }

            // Check production shift conflicts
            $shift_check = $this->checkShiftConflicts($data['machine_id'], $data['start_time'], $data['end_time']);
            if (!$shift_check['valid']) {
                return $shift_check;
            }

            // Prepare maintenance data
            $maintenance_data = [
                'machine_id' => $data['machine_id'],
                'title' => trim($data['title']),
                'description' => trim($data['description'] ?? ''),
                'start_time' => $data['start_time'],
                'end_time' => $data['end_time'],
                'maintenance_type' => $data['maintenance_type'] ?? 'preventive',
                'status' => $data['status'] ?? 'planned',
                'estimated_cost' => floatval($data['estimated_cost'] ?? 0),
                'technician_name' => trim($data['technician_name'] ?? ''),
                'created_by_username' => $data['created_by'] ?? null,
                'notes' => trim($data['notes'] ?? '')
            ];

            $this->db->insert('machine_maintenances', $maintenance_data);
            $maintenance_id = $this->db->insert_id();

            return [
                'success' => true,
                'message' => 'Tạo lịch bảo trì thành công',
                'maintenance_id' => $maintenance_id
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Lỗi hệ thống: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Validation data máy
     */
    private function validateMachineData($data, $exclude_id = null)
    {
        $errors = [];

        // Required fields
        if (empty($data['code'])) {
            $errors[] = 'Mã máy là bắt buộc';
        }

        if (empty($data['name'])) {
            $errors[] = 'Tên máy là bắt buộc';
        }

        if (empty($data['capacity']) || !is_numeric($data['capacity']) || $data['capacity'] <= 0) {
            $errors[] = 'Công suất phải là số dương';
        }

        if (empty($data['stage_type'])) {
            $errors[] = 'Loại công đoạn là bắt buộc';
        }

        // Valid enum values
        $valid_stages = ['molding', 'assembly', 'packaging', 'quality_check', 'other'];
        if (!in_array($data['stage_type'], $valid_stages)) {
            $errors[] = 'Loại công đoạn không hợp lệ';
        }

        $valid_statuses = ['active', 'maintenance', 'inactive', 'broken'];
        if (!empty($data['status']) && !in_array($data['status'], $valid_statuses)) {
            $errors[] = 'Trạng thái không hợp lệ';
        }

        if (!empty($errors)) {
            return [
                'valid' => false,
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $errors
            ];
        }

        return ['valid' => true];
    }

    /**
     * Validation data bảo trì
     */
    private function validateMaintenanceData($data)
    {
        $errors = [];

        if (empty($data['machine_id'])) {
            $errors[] = 'Machine ID là bắt buộc';
        }

        if (empty($data['title'])) {
            $errors[] = 'Tiêu đề bảo trì là bắt buộc';
        }

        if (empty($data['start_time'])) {
            $errors[] = 'Thời gian bắt đầu là bắt buộc';
        }

        if (empty($data['end_time'])) {
            $errors[] = 'Thời gian kết thúc là bắt buộc';
        }

        if (!empty($data['start_time']) && !empty($data['end_time'])) {
            if (strtotime($data['start_time']) >= strtotime($data['end_time'])) {
                $errors[] = 'Thời gian bắt đầu phải nhỏ hơn thời gian kết thúc';
            }
        }

        if (!empty($errors)) {
            return [
                'valid' => false,
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $errors
            ];
        }

        return ['valid' => true];
    }

    /**
     * Kiểm tra xung đột lịch bảo trì
     */
    private function checkMaintenanceConflicts($machine_id, $start_time, $end_time, $exclude_id = null)
    {
        $this->db->select('id, title, start_time, end_time');
        $this->db->from('machine_maintenances');
        $this->db->where('machine_id', $machine_id);
        $this->db->where('status !=', 'cancelled');
        
        // Check time overlap
        $this->db->group_start()
                 ->where('start_time <', $end_time)
                 ->where('end_time >', $start_time)
                 ->group_end();

        if ($exclude_id) {
            $this->db->where('id !=', $exclude_id);
        }

        $conflicts = $this->db->get()->result();

        if (!empty($conflicts)) {
            return [
                'valid' => false,
                'success' => false,
                'message' => 'Thời gian bảo trì bị trùng với lịch bảo trì khác',
                'conflicts' => $conflicts
            ];
        }

        return ['valid' => true];
    }

    /**
     * Kiểm tra xung đột với ca sản xuất
     */
    private function checkShiftConflicts($machine_id, $start_time, $end_time)
    {
        // TODO: Implement check với bảng production shifts khi có
        // Hiện tại return valid = true
        return ['valid' => true];
    }

    /**
     * Kiểm tra máy có đang được sử dụng không
     */
    private function checkMachineInUse($machine_id)
    {
        // TODO: Implement check với bảng production shifts khi có
        // Hiện tại return in_use = false
        return [
            'in_use' => false,
            'details' => []
        ];
    }

    /**
     * Lấy máy khả dụng cho ca sản xuất
     * 
     * @param string $stage_type Loại công đoạn
     * @param string $start_time Thời gian bắt đầu ca
     * @param string $end_time Thời gian kết thúc ca
     * @return array Available machines
     */
    public function getAvailableMachines($stage_type, $start_time, $end_time)
    {
        $this->db->select('m.*');
        $this->db->from('machines m');
        $this->db->where('m.status', 'active');
        $this->db->where('m.stage_type', $stage_type);

        // Exclude machines with maintenance in the time range
        $this->db->where('m.id NOT IN (
            SELECT mm.machine_id 
            FROM machine_maintenances mm 
            WHERE mm.status IN ("planned", "in_progress") 
            AND mm.start_time < "' . $end_time . '" 
            AND mm.end_time > "' . $start_time . '"
        )');

        $this->db->order_by('m.capacity', 'DESC');

        return $this->db->get()->result();
    }

    /**
     * Lấy thống kê tổng quan
     */
    public function getStatistics()
    {
        $stats = [];

        // Count by status
        $this->db->select('status, COUNT(*) as count');
        $this->db->from('machines');
        $this->db->group_by('status');
        $status_counts = $this->db->get()->result();

        foreach ($status_counts as $status) {
            $stats['by_status'][$status->status] = $status->count;
        }

        // Count by stage type
        $this->db->select('stage_type, COUNT(*) as count');
        $this->db->from('machines');
        $this->db->group_by('stage_type');
        $stage_counts = $this->db->get()->result();

        foreach ($stage_counts as $stage) {
            $stats['by_stage'][$stage->stage_type] = $stage->count;
        }

        // Maintenance statistics
        $this->db->select('COUNT(*) as total_maintenances');
        $this->db->from('machine_maintenances');
        $this->db->where('created_at >=', date('Y-m-01')); // This month
        $stats['monthly_maintenances'] = $this->db->get()->row()->total_maintenances;

        // Total machines
        $stats['total_machines'] = $this->db->count_all('machines');

        return $stats;
    }
}