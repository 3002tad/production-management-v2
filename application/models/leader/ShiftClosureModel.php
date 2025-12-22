<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Shift Closure Model
 * 
 * Xử lý logic chốt ca: tổng hợp dữ liệu, tạo phiếu chốt, cảnh báo, và tạo đề nghị nhập kho
 * 
 * @package    Production Management
 * @subpackage Models/Leader
 * @category   Shift Management
 * @author     System
 * @created    2025-12-18
 */
class ShiftClosureModel extends CI_Model
{
    private $defect_warning_threshold = 5.0;   // %
    private $defect_critical_threshold = 10.0; // %
    private $efficiency_warning_threshold = 70.0; // %
    
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        
        // Load thresholds from system_config
        $this->loadSystemConfig();
    }
    
    /**
     * Load system configuration thresholds
     */
    private function loadSystemConfig()
    {
        if (!$this->db->table_exists('system_config')) {
            return;
        }
        
        $configs = $this->db->where_in('config_key', [
            'defect_rate_warning_threshold',
            'defect_rate_critical_threshold',
            'efficiency_warning_threshold'
        ])->get('system_config')->result();
        
        foreach ($configs as $config) {
            switch ($config->config_key) {
                case 'defect_rate_warning_threshold':
                    $this->defect_warning_threshold = (float)$config->config_value;
                    break;
                case 'defect_rate_critical_threshold':
                    $this->defect_critical_threshold = (float)$config->config_value;
                    break;
                case 'efficiency_warning_threshold':
                    $this->efficiency_warning_threshold = (float)$config->config_value;
                    break;
            }
        }
    }
    
    /**
     * Tổng hợp dữ liệu ca để chuẩn bị chốt
     * 
     * @param int $shift_id Shift ID
     * @return array|false Dữ liệu tổng hợp hoặc false nếu lỗi
     */
    public function aggregateShiftData($shift_id)
    {
        try {
            // Check if tables exist
            if (!$this->db->table_exists('production_shifts') || !$this->db->table_exists('production_records')) {
                return false;
            }
            
            // Get shift info
            $shift = $this->db->where('shift_id', $shift_id)->get('production_shifts')->row();
            if (!$shift) {
                return false;
            }
            
            // Get machines assigned to shift
            $machines = $this->getShiftMachinesWithData($shift_id);
            
            // Calculate totals
            $totals = [
                'total_target' => 0,
                'total_produced' => 0,
                'total_good' => 0,
                'total_defect' => 0,
                'total_downtime' => 0
            ];
            
            $warnings = [];
            
            foreach ($machines as $machine) {
                $totals['total_target'] += $machine->target_count;
                $totals['total_produced'] += $machine->produced_count;
                $totals['total_good'] += $machine->good_count;
                $totals['total_defect'] += $machine->defect_count;
                $totals['total_downtime'] += $machine->downtime_minutes;
                
                // Check warnings for this machine
                if ($machine->defect_rate >= $this->defect_critical_threshold) {
                    $warnings[] = [
                        'type' => 'critical',
                        'machine_id' => $machine->machine_id,
                        'machine_name' => $machine->machine_name,
                        'message' => "Tỷ lệ phế phẩm nghiêm trọng: {$machine->defect_rate}% (>= {$this->defect_critical_threshold}%)",
                        'defect_rate' => $machine->defect_rate
                    ];
                } elseif ($machine->defect_rate >= $this->defect_warning_threshold) {
                    $warnings[] = [
                        'type' => 'warning',
                        'machine_id' => $machine->machine_id,
                        'machine_name' => $machine->machine_name,
                        'message' => "Tỷ lệ phế phẩm cao: {$machine->defect_rate}% (>= {$this->defect_warning_threshold}%)",
                        'defect_rate' => $machine->defect_rate
                    ];
                }
                
                if ($machine->efficiency_rate < $this->efficiency_warning_threshold) {
                    $warnings[] = [
                        'type' => 'warning',
                        'machine_id' => $machine->machine_id,
                        'machine_name' => $machine->machine_name,
                        'message' => "Hiệu suất thấp: {$machine->efficiency_rate}% (< {$this->efficiency_warning_threshold}%)",
                        'efficiency_rate' => $machine->efficiency_rate
                    ];
                }
            }
            
            // Calculate overall rates
            $efficiency_rate = $totals['total_target'] > 0 
                ? round(($totals['total_produced'] / $totals['total_target']) * 100, 2)
                : 0;
                
            $defect_rate = $totals['total_produced'] > 0
                ? round(($totals['total_defect'] / $totals['total_produced']) * 100, 2)
                : 0;
            
            return [
                'shift' => $shift,
                'machines' => $machines,
                'totals' => array_merge($totals, [
                    'efficiency_rate' => $efficiency_rate,
                    'defect_rate' => $defect_rate
                ]),
                'warnings' => $warnings,
                'has_warnings' => !empty($warnings),
                'thresholds' => [
                    'defect_warning' => $this->defect_warning_threshold,
                    'defect_critical' => $this->defect_critical_threshold,
                    'efficiency_warning' => $this->efficiency_warning_threshold
                ]
            ];
            
        } catch (Exception $e) {
            log_message('error', 'ShiftClosureModel::aggregateShiftData() - Error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get machines with production data for a shift
     * 
     * @param int $shift_id Shift ID
     * @return array List of machines with aggregated data
     */
    private function getShiftMachinesWithData($shift_id)
    {
        $this->db->select('
            sms.machine_id,
            m.code AS machine_code,
            m.name AS machine_name,
            sms.staff_id,
            u.username AS staff_name,
            COUNT(pr.id) AS record_count,
            COALESCE(SUM(pr.target_count), 0) AS target_count,
            COALESCE(SUM(pr.good_count + pr.defect_count), 0) AS produced_count,
            COALESCE(SUM(pr.good_count), 0) AS good_count,
            COALESCE(SUM(pr.defect_count), 0) AS defect_count,
            COALESCE(SUM(pr.downtime_minutes), 0) AS downtime_minutes,
            ROUND(AVG(pr.efficiency_rate), 2) AS efficiency_rate,
            ROUND(AVG(pr.defect_rate), 2) AS defect_rate
        ');
        $this->db->from('shift_machine_staff sms');
        $this->db->join('machines m', 'sms.machine_id = m.id', 'inner');
        $this->db->join('user u', 'sms.staff_id = u.user_id', 'left');
        $this->db->join('production_records pr', 'pr.shift_id = sms.shift_id AND pr.machine_id = sms.machine_id', 'left');
        $this->db->where('sms.shift_id', $shift_id);
        $this->db->where('sms.status', 1); // Active machines only
        $this->db->group_by('sms.machine_id, m.code, m.name, sms.staff_id, u.username');
        
        $machines = $this->db->get()->result();
        
        // Get defect details for each machine
        foreach ($machines as $machine) {
            $machine->defect_details = $this->getDefectDetailsByMachine($shift_id, $machine->machine_id);
            $machine->downtime_details = $this->getDowntimeDetailsByMachine($shift_id, $machine->machine_id);
        }
        
        return $machines;
    }
    
    /**
     * Get defect details by machine
     * 
     * @param int $shift_id Shift ID
     * @param int $machine_id Machine ID
     * @return array Defect details grouped by reason
     */
    private function getDefectDetailsByMachine($shift_id, $machine_id)
    {
        // For now, return empty array
        // In real implementation, this would query defect_tracking table
        return [];
    }
    
    /**
     * Get downtime details by machine
     * 
     * @param int $shift_id Shift ID
     * @param int $machine_id Machine ID
     * @return array Downtime details
     */
    private function getDowntimeDetailsByMachine($shift_id, $machine_id)
    {
        if (!$this->db->table_exists('production_records')) {
            return [];
        }
        
        $this->db->select('downtime_minutes, downtime_reason, timestamp');
        $this->db->from('production_records');
        $this->db->where('shift_id', $shift_id);
        $this->db->where('machine_id', $machine_id);
        $this->db->where('downtime_minutes >', 0);
        
        return $this->db->get()->result();
    }
    
    /**
     * Create shift closure record
     * 
     * @param int $shift_id Shift ID
     * @param int $user_id User creating closure
     * @param array $confirmed_data Confirmed quantities from user
     * @param string $notes Notes from user
     * @return array Success status with closure_id or error
     */
    public function createClosure($shift_id, $user_id, $confirmed_data = [], $notes = '', $quantity_overrides = [])
    {
        // Start transaction
        $this->db->trans_start();
        
        try {
            // Check if tables exist
            if (!$this->db->table_exists('shift_closures')) {
                throw new Exception('Migration 017 chưa chạy. Vui lòng chạy migration trước.');
            }
            
            // Check if shift already closed
            $existing = $this->db->where('shift_id', $shift_id)->get('shift_closures')->row();
            if ($existing) {
                throw new Exception('Ca này đã được chốt. Mã phiếu: ' . $existing->closure_code);
            }
            
            // Get aggregated data
            $data = $this->aggregateShiftData($shift_id);
            if (!$data) {
                throw new Exception('Không thể tổng hợp dữ liệu ca');
            }
            
            // Apply quantity overrides if provided
            $final_raw_qty = $data['totals']['total_produced'];
            $final_good_qty = $data['totals']['total_good'];
            $final_defect_qty = $data['totals']['total_defect'];
            
            if (!empty($quantity_overrides)) {
                if (isset($quantity_overrides['raw_qty']) && $quantity_overrides['raw_qty'] !== null) {
                    $final_raw_qty = (int)$quantity_overrides['raw_qty'];
                }
                if (isset($quantity_overrides['good_qty']) && $quantity_overrides['good_qty'] !== null) {
                    $final_good_qty = (int)$quantity_overrides['good_qty'];
                }
                if (isset($quantity_overrides['defect_qty']) && $quantity_overrides['defect_qty'] !== null) {
                    $final_defect_qty = (int)$quantity_overrides['defect_qty'];
                }
            }
            
            // Generate closure code
            $closure_code = $this->generateClosureCode();
            
            // Prepare closure data
            $closure_insert = [
                'shift_id' => $shift_id,
                'closure_code' => $closure_code,
                'closure_date' => date('Y-m-d H:i:s'),
                'closed_by' => $user_id,
                'total_target' => $data['totals']['total_target'],
                'total_produced' => $final_raw_qty,
                'total_good' => $final_good_qty,
                'total_defect' => $final_defect_qty,
                'total_downtime' => $data['totals']['total_downtime'],
                'efficiency_rate' => $data['totals']['efficiency_rate'],
                'defect_rate' => $data['totals']['defect_rate'],
                'has_warnings' => $data['has_warnings'] ? 1 : 0,
                'warning_details' => !empty($data['warnings']) ? json_encode($data['warnings']) : null,
                'notes' => $notes,
                'confirmed_quantities' => !empty($confirmed_data) ? json_encode($confirmed_data) : null,
                'status' => 'confirmed'
            ];
            
            // Insert closure
            $this->db->insert('shift_closures', $closure_insert);
            $closure_id = $this->db->insert_id();
            
            if (!$closure_id) {
                throw new Exception('Không thể tạo phiếu chốt ca');
            }
            
            // Insert machine details
            foreach ($data['machines'] as $machine) {
                $machine_insert = [
                    'closure_id' => $closure_id,
                    'machine_id' => $machine->machine_id,
                    'staff_id' => $machine->staff_id,
                    'target_count' => $machine->target_count,
                    'produced_count' => $machine->produced_count,
                    'good_count' => $machine->good_count,
                    'defect_count' => $machine->defect_count,
                    'downtime_minutes' => $machine->downtime_minutes,
                    'efficiency_rate' => $machine->efficiency_rate,
                    'defect_rate' => $machine->defect_rate,
                    'confirmed_good' => isset($confirmed_data[$machine->machine_id]['good']) 
                        ? $confirmed_data[$machine->machine_id]['good'] 
                        : $machine->good_count,
                    'confirmed_defect' => isset($confirmed_data[$machine->machine_id]['defect'])
                        ? $confirmed_data[$machine->machine_id]['defect']
                        : $machine->defect_count,
                    'defect_details' => !empty($machine->defect_details) ? json_encode($machine->defect_details) : null,
                    'downtime_details' => !empty($machine->downtime_details) ? json_encode($machine->downtime_details) : null,
                    'is_warning' => ($machine->defect_rate >= $this->defect_warning_threshold || 
                                    $machine->efficiency_rate < $this->efficiency_warning_threshold) ? 1 : 0
                ];
                
                $this->db->insert('shift_closure_machines', $machine_insert);
            }
            
            // Create warehouse import request (use final quantities)
            $warehouse_result = $this->createWarehouseRequest($closure_id, $shift_id, $final_good_qty, $user_id);
            
            if ($warehouse_result['success']) {
                // Update closure with warehouse_request_id
                $this->db->where('closure_id', $closure_id);
                $this->db->update('shift_closures', ['warehouse_request_id' => $warehouse_result['request_id']]);
            }
            
            // Update shift status to completed and closed, with actual quantity
            $this->updateShiftStatus($shift_id, 3, $user_id, true, $final_raw_qty); // 3 = Completed, pass actual quantity
            
            // Complete transaction
            $this->db->trans_complete();
            
            if ($this->db->trans_status() === FALSE) {
                throw new Exception('Transaction failed');
            }
            
            return [
                'success' => true,
                'closure_id' => $closure_id,
                'closure_code' => $closure_code,
                'warehouse_request_code' => $warehouse_result['request_code'] ?? null,
                'message' => 'Chốt ca thành công. Mã phiếu: ' . $closure_code
            ];
            
        } catch (Exception $e) {
            $this->db->trans_rollback();
            log_message('error', 'ShiftClosureModel::createClosure() - Error: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Lỗi chốt ca: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Generate unique closure code
     * Format: SC-YYYYMMDD-XXX
     * 
     * @return string Closure code
     */
    private function generateClosureCode()
    {
        $date_prefix = 'SC-' . date('Ymd');
        
        // Get last code of the day
        $this->db->select('closure_code');
        $this->db->from('shift_closures');
        $this->db->like('closure_code', $date_prefix, 'after');
        $this->db->order_by('closure_id', 'DESC');
        $this->db->limit(1);
        
        $last = $this->db->get()->row();
        
        if ($last) {
            // Extract number and increment
            $last_number = (int)substr($last->closure_code, -3);
            $new_number = $last_number + 1;
        } else {
            $new_number = 1;
        }
        
        return $date_prefix . '-' . str_pad($new_number, 3, '0', STR_PAD_LEFT);
    }
    
    /**
     * Create warehouse import request
     * 
     * @param int $closure_id Closure ID
     * @param int $shift_id Shift ID
     * @param int $quantity Quantity of good products
     * @param int $user_id User creating request
     * @return array Success status with request details
     */
    private function createWarehouseRequest($closure_id, $shift_id, $quantity, $user_id)
    {
        try {
            if (!$this->db->table_exists('warehouse_import_requests')) {
                throw new Exception('Bảng warehouse_import_requests không tồn tại');
            }
            
            // Generate request code
            $request_code = $this->generateWarehouseRequestCode();
            
            // Get shift info for product name
            $shift = $this->db->where('shift_id', $shift_id)->get('production_shifts')->row();
            
            $insert_data = [
                'request_code' => $request_code,
                'closure_id' => $closure_id,
                'shift_id' => $shift_id,
                'product_name' => $shift->shift_name ?? 'Sản phẩm ca ' . $shift_id,
                'quantity' => $quantity,
                'unit' => 'cái',
                'status' => 'pending_qc',
                'notes' => 'Đề nghị nhập kho tự động từ phiếu chốt ca',
                'created_by' => $user_id
            ];
            
            $this->db->insert('warehouse_import_requests', $insert_data);
            $request_id = $this->db->insert_id();
            
            return [
                'success' => true,
                'request_id' => $request_id,
                'request_code' => $request_code
            ];
            
        } catch (Exception $e) {
            log_message('error', 'ShiftClosureModel::createWarehouseRequest() - Error: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Không thể tạo đề nghị nhập kho: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Generate unique warehouse request code
     * Format: WIR-YYYYMMDD-XXX
     * 
     * @return string Request code
     */
    private function generateWarehouseRequestCode()
    {
        $date_prefix = 'WIR-' . date('Ymd');
        
        $this->db->select('request_code');
        $this->db->from('warehouse_import_requests');
        $this->db->like('request_code', $date_prefix, 'after');
        $this->db->order_by('request_id', 'DESC');
        $this->db->limit(1);
        
        $last = $this->db->get()->row();
        
        if ($last) {
            $last_number = (int)substr($last->request_code, -3);
            $new_number = $last_number + 1;
        } else {
            $new_number = 1;
        }
        
        return $date_prefix . '-' . str_pad($new_number, 3, '0', STR_PAD_LEFT);
    }
    
    /**
     * Update shift status
     * 
     * @param int $shift_id Shift ID
     * @param int $new_status New status (1=Planned, 2=Running, 3=Completed, 4=Paused)
     * @param int $user_id User performing action
     * @param bool $is_closed Is shift closed
     * @return bool Success
     */
    public function updateShiftStatus($shift_id, $new_status, $user_id, $is_closed = false, $actual_quantity = null)
    {
        $update_data = [
            'shift_status' => $new_status
        ];
        
        // Add timestamp fields based on status
        if ($new_status == 2) {
            // Starting shift
            $update_data['started_at'] = date('Y-m-d H:i:s');
            $update_data['started_by'] = $user_id;  // Store user_id as before
        } elseif ($new_status == 3 && $is_closed) {
            // Completing and closing shift
            $update_data['ended_at'] = date('Y-m-d H:i:s');
            $update_data['ended_by'] = $user_id;  // Store user_id as before
            $update_data['is_closed'] = 1;
            
            // Update actual_quantity if provided
            if ($actual_quantity !== null) {
                $update_data['actual_quantity'] = (int)$actual_quantity;
            }
        }
        
        $this->db->where('shift_id', $shift_id);
        return $this->db->update('production_shifts', $update_data);
    }
    
    /**
     * Get closure details
     * 
     * @param int $closure_id Closure ID
     * @return object|false Closure data or false
     */
    public function getClosureDetails($closure_id)
    {
        if (!$this->db->table_exists('shift_closures')) {
            return false;
        }
        
        $this->db->select('sc.*, ps.shift_name, ps.shift_date, u.username AS closed_by_name');
        $this->db->from('shift_closures sc');
        $this->db->join('production_shifts ps', 'sc.shift_id = ps.shift_id', 'inner');
        $this->db->join('user u', 'sc.closed_by = u.user_id', 'left');
        $this->db->where('sc.closure_id', $closure_id);
        
        $closure = $this->db->get()->row();
        
        if ($closure) {
            // Get machine details
            $closure->machines = $this->getClosureMachines($closure_id);
            
            // Decode JSON fields
            $closure->warning_details = json_decode($closure->warning_details);
            $closure->confirmed_quantities = json_decode($closure->confirmed_quantities);
        }
        
        return $closure;
    }
    
    /**
     * Get machines for a closure
     * 
     * @param int $closure_id Closure ID
     * @return array List of machines
     */
    private function getClosureMachines($closure_id)
    {
        $this->db->select('scm.*, m.code AS machine_code, m.name AS machine_name, u.username AS staff_name');
        $this->db->from('shift_closure_machines scm');
        $this->db->join('machines m', 'scm.machine_id = m.id', 'inner');
        $this->db->join('user u', 'scm.staff_id = u.user_id', 'left');
        $this->db->where('scm.closure_id', $closure_id);
        
        $machines = $this->db->get()->result();
        
        // Decode JSON fields
        foreach ($machines as $machine) {
            $machine->defect_details = json_decode($machine->defect_details);
            $machine->downtime_details = json_decode($machine->downtime_details);
        }
        
        return $machines;
    }
    
    /**
     * Get defect reasons
     * 
     * @return array List of defect reasons
     */
    public function getDefectReasons()
    {
        if (!$this->db->table_exists('defect_reasons')) {
            return [];
        }
        
        $this->db->where('is_active', 1);
        $this->db->order_by('category, reason_name');
        
        return $this->db->get('defect_reasons')->result();
    }
}
