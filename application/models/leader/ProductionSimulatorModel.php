<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * ProductionSimulator Model
 * 
 * Model xử lý dữ liệu giả lập sản lượng sản xuất
 * - Lưu/đọc production records
 * - Quản lý simulator settings
 * - Tính toán thống kê sản lượng
 * 
 * @author Copilot AI
 * @date 2025-12-18
 */
class ProductionSimulatorModel extends CI_Model
{
    /**
     * Get simulator settings
     * 
     * @return array Key-value array of settings
     */
    public function getSettings()
    {
        // Check if table exists first
        if (!$this->db->table_exists('simulator_settings')) {
            log_message('error', 'Table simulator_settings does not exist. Please run migration 016.');
            return $this->getDefaultSettings();
        }
        
        $results = $this->db->get('simulator_settings')->result();
        
        // If no settings found, return defaults
        if (empty($results)) {
            return $this->getDefaultSettings();
        }
        
        $settings = [];
        foreach ($results as $row) {
            $value = $row->setting_value;
            
            // Convert based on type
            switch ($row->setting_type) {
                case 'boolean':
                    $value = (bool)(int)$value;
                    break;
                case 'integer':
                    $value = (int)$value;
                    break;
                case 'float':
                    $value = (float)$value;
                    break;
                case 'json':
                    $value = json_decode($value, true);
                    break;
            }
            
            $settings[$row->setting_key] = $value;
        }
        
        return $settings;
    }
    
    /**
     * Get default settings if table doesn't exist
     * 
     * @return array Default settings
     */
    private function getDefaultSettings()
    {
        return [
            'simulator_enabled' => false,
            'simulator_interval' => 300,
            'good_count_min' => 50,
            'good_count_max' => 200,
            'defect_rate_min' => 1.0,
            'defect_rate_max' => 8.0,
            'downtime_probability' => 0.15,
            'downtime_min' => 5,
            'downtime_max' => 30,
            'target_multiplier' => 1.2,
            'simulate_active_shifts_only' => true
        ];
    }
    
    /**
     * Update simulator setting
     * 
     * @param string $key Setting key
     * @param mixed $value Setting value
     * @param string $updated_by Username
     * @return bool Success status
     */
    public function updateSetting($key, $value, $updated_by = null)
    {
        // Convert value to string for storage
        if (is_array($value) || is_object($value)) {
            $value = json_encode($value);
        }
        
        $data = [
            'setting_value' => $value,
            'updated_by' => $updated_by,
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        $this->db->where('setting_key', $key);
        return $this->db->update('simulator_settings', $data);
    }
    
    /**
     * Get active shifts that should be simulated
     * 
     * @return array List of active shifts with machines
     */
    public function getActiveShiftsForSimulation()
    {
        // Check if tables exist
        if (!$this->db->table_exists('production_shifts') || !$this->db->table_exists('production_records')) {
            log_message('error', 'Required tables do not exist. Please run migration 016.');
            return [];
        }
        
        $settings = $this->getSettings();
        
        // Build query for active shifts
        $this->db->select('
            ps.shift_id,
            ps.shift_name,
            ps.shift_date,
            ps.start_time,
            ps.end_time,
            ps.shift_status,
            ps.id_plan
        ');
        $this->db->from('production_shifts ps');
        
        if ($settings['simulate_active_shifts_only']) {
            // shift_status: 2 = Đang chạy
            $this->db->where('ps.shift_status', 2);
        } else {
            // shift_status: 1 = Chưa bắt đầu, 2 = Đang chạy
            $this->db->where_in('ps.shift_status', [1, 2]);
        }
        
        $shifts = $this->db->get()->result();
        
        // Get machines and staff for each shift
        foreach ($shifts as $shift) {
            $shift->machines = $this->getShiftMachines($shift->shift_id);
        }
        
        return $shifts;
    }
    
    /**
     * Get machines assigned to a shift
     * 
     * @param int $shift_id Shift ID
     * @return array List of machines with staff
     */
    public function getShiftMachines($shift_id)
    {
        $this->db->select('
            sms.machine_id,
            m.code AS machine_code,
            m.name AS machine_name,
            m.capacity,
            sms.staff_id,
            u.username AS staff_name
        ');
        $this->db->from('shift_machine_staff sms');
        $this->db->join('machines m', 'sms.machine_id = m.id', 'inner');
        $this->db->join('user u', 'sms.staff_id = u.user_id', 'left');
        $this->db->where('sms.shift_id', $shift_id);
        $this->db->where('sms.status', 1); // 1 = Active, 0 = Removed
        
        return $this->db->get()->result();
    }
    
    /**
     * Create simulated production record
     * 
     * @param int $shift_id Shift ID
     * @param int $machine_id Machine ID
     * @param int|null $staff_id Staff ID
     * @param array $data Production data
     * @return array Result with success status
     */
    public function createProductionRecord($shift_id, $machine_id, $staff_id, $data)
    {        // Check if table exists
        if (!$this->db->table_exists('production_records')) {
            log_message('error', 'Table production_records does not exist. Please run migration 016.');
            return ['success' => false, 'message' => 'Database table not found. Please run migration 016.'];
        }
                $record = [
            'shift_id' => $shift_id,
            'machine_id' => $machine_id,
            'staff_id' => $staff_id,
            'timestamp' => date('Y-m-d H:i:s'),
            'good_count' => $data['good_count'],
            'defect_count' => $data['defect_count'],
            'target_count' => $data['target_count'],
            'downtime_minutes' => $data['downtime_minutes'],
            'downtime_reason' => $data['downtime_reason'] ?? null,
            'efficiency_rate' => $data['efficiency_rate'],
            'defect_rate' => $data['defect_rate'],
            'is_simulated' => 1
        ];
        
        if ($this->db->insert('production_records', $record)) {
            return [
                'success' => true,
                'record_id' => $this->db->insert_id(),
                'message' => 'Production record created'
            ];
        }
        
        return [
            'success' => false,
            'message' => 'Failed to create production record'
        ];
    }
    
    /**
     * Get production records for a shift
     * 
     * @param int $shift_id Shift ID
     * @param int|null $machine_id Optional machine filter
     * @return array List of production records
     */
    public function getShiftProductionRecords($shift_id, $machine_id = null)
    {
        // Check if table exists
        if (!$this->db->table_exists('production_records')) {
            log_message('error', 'Table production_records does not exist. Please run migration 016.');
            return [];
        }
        
        $this->db->select('
            pr.*,
            m.code AS machine_code,
            m.name AS machine_name,
            u.username AS staff_name
        ');
        $this->db->from('production_records pr');
        $this->db->join('machines m', 'pr.machine_id = m.id', 'inner');
        $this->db->join('user u', 'pr.staff_id = u.user_id', 'left');
        $this->db->where('pr.shift_id', $shift_id);
        
        if ($machine_id) {
            $this->db->where('pr.machine_id', $machine_id);
        }
        
        $this->db->order_by('pr.timestamp', 'DESC');
        
        return $this->db->get()->result();
    }
    
    /**
     * Get production summary for a shift
     * 
     * @param int $shift_id Shift ID
     * @return array Summary statistics
     */
    public function getShiftProductionSummary($shift_id)
    {
        // Query directly from table instead of VIEW to avoid issues
        $this->db->select('
            pr.machine_id,
            m.code as machine_code,
            m.name as machine_name,
            COUNT(*) as record_count,
            SUM(pr.good_count) as total_good,
            SUM(pr.defect_count) as total_defect,
            SUM(pr.good_count + pr.defect_count) as total_produced,
            AVG(pr.target_count) as avg_target,
            SUM(pr.downtime_minutes) as total_downtime,
            AVG(pr.efficiency_rate) as avg_efficiency,
            AVG(pr.defect_rate) as avg_defect_rate
        ');
        $this->db->from('production_records pr');
        $this->db->join('machines m', 'm.id = pr.machine_id', 'left');
        $this->db->where('pr.shift_id', $shift_id);
        $this->db->group_by('pr.machine_id, m.code, m.name');
        
        $result = $this->db->get()->result();
        return $result ? $result : [];
    }
    
    /**
     * Get random downtime reason
     * 
     * @return string|null Downtime reason
     */
    public function getRandomDowntimeReason()
    {
        $this->db->select('reason_name');
        $this->db->from('downtime_reasons');
        $this->db->where('is_active', 1);
        $this->db->order_by('RAND()');
        $this->db->limit(1);
        
        $result = $this->db->get()->row();
        return $result ? $result->reason_name : 'Downtime không xác định';
    }
    
    /**
     * Delete old simulated records
     * 
     * @param int $days_old Delete records older than X days
     * @return int Number of deleted records
     */
    public function deleteOldSimulatedRecords($days_old = 30)
    {
        $cutoff_date = date('Y-m-d H:i:s', strtotime("-{$days_old} days"));
        
        $this->db->where('is_simulated', 1);
        $this->db->where('timestamp <', $cutoff_date);
        $this->db->delete('production_records');
        
        return $this->db->affected_rows();
    }
}
