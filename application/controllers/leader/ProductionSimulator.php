<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * ProductionSimulator Controller
 * 
 * Controller xử lý giả lập sản lượng sản xuất tự động
 * - Endpoint để trigger simulation
 * - UI quản lý settings
 * - AJAX endpoints
 * 
 * @author Copilot AI
 * @date 2025-12-18
 */
class ProductionSimulator extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('leader/ProductionSimulatorModel');
        $this->load->library('session');
        
        // Check login
        if (!$this->session->userdata('user_id')) {
            redirect('login/');
            exit();
        }
        
        // Check role permissions
        $role_name = strtolower(trim($this->session->userdata('role_name')));
        $allowed_roles = ['bod', 'system_admin', 'line_manager', 'leader', 'admin'];
        
        if (!in_array($role_name, $allowed_roles)) {
            $this->session->set_flashdata('error', 'Không có quyền truy cập production simulator');
            redirect('leader/');
            exit();
        }
    }
    
    /**
     * Settings page - UI to configure simulator
     */
    public function settings()
    {
        $data = [
            'title' => 'Cấu hình Production Simulator',
            'settings' => $this->ProductionSimulatorModel->getSettings(),
            'content' => 'leader/simulator/settings',
            'navlink' => 'shift' // Link to shift menu
        ];
        
        $this->load->view('leader/VBackend', $data);
    }
    
    /**
     * Update simulator settings via AJAX
     */
    public function update_settings()
    {
        // Temporarily disable for debugging
        // if (!$this->input->is_ajax_request()) {
        //     show_404();
        //     return;
        // }
        log_message('info', 'ProductionSimulator::update_settings() called');
        
        try {
            $post_data = $this->input->post();
            $username = $this->session->userdata('username');
            
            $updated_count = 0;
            foreach ($post_data as $key => $value) {
                if ($this->ProductionSimulatorModel->updateSetting($key, $value, $username)) {
                    $updated_count++;
                }
            }
            
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => true,
                    'message' => "Đã cập nhật {$updated_count} cài đặt",
                    'updated_count' => $updated_count
                ]));
                
        } catch (Exception $e) {
            $this->output
                ->set_content_type('application/json')
                ->set_status_header(500)
                ->set_output(json_encode([
                    'success' => false,
                    'message' => 'Lỗi: ' . $e->getMessage()
                ]));
        }
    }
    
    /**
     * Run simulation - Main endpoint called by AJAX
     */
    public function run()
    {
        // Temporarily disable for debugging
        // if (!$this->input->is_ajax_request()) {
        //     show_404();
        //     return;
        // }
        log_message('info', 'ProductionSimulator::run() called');
        
        try {
            // Check if simulator is enabled
            $settings = $this->ProductionSimulatorModel->getSettings();
            
            if (!$settings['simulator_enabled']) {
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'success' => false,
                        'message' => 'Simulator chưa được bật',
                        'enabled' => false
                    ]));
                return;
            }
            
            // Get active shifts
            $shifts = $this->ProductionSimulatorModel->getActiveShiftsForSimulation();
            
            $total_records = 0;
            $simulated_shifts = [];
            
            foreach ($shifts as $shift) {
                $shift_records = 0;
                
                foreach ($shift->machines as $machine) {
                    // Generate simulated data
                    $production_data = $this->generateProductionData($machine, $settings);
                    
                    // Save to database
                    $result = $this->ProductionSimulatorModel->createProductionRecord(
                        $shift->shift_id,
                        $machine->machine_id,
                        $machine->staff_id,
                        $production_data
                    );
                    
                    if ($result['success']) {
                        $shift_records++;
                        $total_records++;
                    }
                }
                
                if ($shift_records > 0) {
                    $simulated_shifts[] = [
                        'shift_id' => $shift->shift_id,
                        'shift_name' => $shift->shift_name,
                        'records_created' => $shift_records
                    ];
                }
            }
            
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => true,
                    'message' => "Đã tạo {$total_records} bản ghi sản lượng",
                    'total_records' => $total_records,
                    'shifts_count' => count($simulated_shifts),
                    'simulated_shifts' => $simulated_shifts,
                    'timestamp' => date('Y-m-d H:i:s')
                ]));
                
        } catch (Exception $e) {
            log_message('error', 'Production Simulator Error: ' . $e->getMessage());
            
            $this->output
                ->set_content_type('application/json')
                ->set_status_header(500)
                ->set_output(json_encode([
                    'success' => false,
                    'message' => 'Lỗi hệ thống: ' . $e->getMessage()
                ]));
        }
    }
    
    /**
     * Generate random production data based on settings
     * 
     * @param object $machine Machine info
     * @param array $settings Simulator settings
     * @return array Production data
     */
    private function generateProductionData($machine, $settings)
    {
        // Random good count
        $good_count = rand(
            $settings['good_count_min'],
            $settings['good_count_max']
        );
        
        // Random defect rate
        $defect_rate = rand(
            $settings['defect_rate_min'] * 100,
            $settings['defect_rate_max'] * 100
        ) / 100;
        
        // Calculate defect count
        $defect_count = (int)($good_count * ($defect_rate / 100));
        
        // Calculate target
        $target_count = (int)($good_count * $settings['target_multiplier']);
        
        // Random downtime
        $has_downtime = (rand(1, 100) / 100) <= $settings['downtime_probability'];
        $downtime_minutes = 0;
        $downtime_reason = null;
        
        if ($has_downtime) {
            $downtime_minutes = rand(
                $settings['downtime_min'],
                $settings['downtime_max']
            );
            $downtime_reason = $this->ProductionSimulatorModel->getRandomDowntimeReason();
        }
        
        // Calculate efficiency
        $total_produced = $good_count + $defect_count;
        $efficiency_rate = $target_count > 0 
            ? round(($total_produced / $target_count) * 100, 2)
            : 0;
        
        return [
            'good_count' => $good_count,
            'defect_count' => $defect_count,
            'target_count' => $target_count,
            'downtime_minutes' => $downtime_minutes,
            'downtime_reason' => $downtime_reason,
            'efficiency_rate' => $efficiency_rate,
            'defect_rate' => $defect_rate
        ];
    }
    
    /**
     * Get production records for a shift (AJAX)
     */
    public function get_shift_records($shift_id)
    {
        // Temporarily disable for debugging
        // if (!$this->input->is_ajax_request()) {
        //     show_404();
        //     return;
        // }
        log_message('info', 'ProductionSimulator::get_shift_records() called with shift_id=' . $shift_id);
        
        try {
            $machine_id = $this->input->get('machine_id');
            
            $records = $this->ProductionSimulatorModel->getShiftProductionRecords(
                $shift_id,
                $machine_id
            );
            
            $summary = $this->ProductionSimulatorModel->getShiftProductionSummary($shift_id);
            
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => true,
                    'records' => $records,
                    'summary' => $summary,
                    'count' => count($records)
                ]));
                
        } catch (Exception $e) {
            $this->output
                ->set_content_type('application/json')
                ->set_status_header(500)
                ->set_output(json_encode([
                    'success' => false,
                    'message' => 'Lỗi: ' . $e->getMessage()
                ]));
        }
    }
    
    /**
     * Get current simulator status (AJAX)
     */
    public function status()
    {
        // Temporarily disable AJAX check for debugging
        // if (!$this->input->is_ajax_request()) {
        //     show_404();
        //     return;
        // }
        
        // Log that we reached this method
        log_message('info', 'ProductionSimulator::status() called');
        
        try {
            // Check if tables exist
            if (!$this->db->table_exists('simulator_settings')) {
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'success' => false,
                        'enabled' => false,
                        'message' => 'Migration 016 chưa chạy. Vui lòng chạy migration để tạo bảng dữ liệu.',
                        'migration_required' => true
                    ]));
                return;
            }
            
            $settings = $this->ProductionSimulatorModel->getSettings();
            
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => true,
                    'enabled' => $settings['simulator_enabled'],
                    'interval' => $settings['simulator_interval'],
                    'settings' => $settings
                ]));
                
        } catch (Exception $e) {
            $this->output
                ->set_content_type('application/json')
                ->set_status_header(500)
                ->set_output(json_encode([
                    'success' => false,
                    'message' => 'Lỗi: ' . $e->getMessage()
                ]));
        }
    }
    
    /**
     * Toggle simulator on/off (AJAX)
     */
    public function toggle()
    {
        // Temporarily disable for debugging
        // if (!$this->input->is_ajax_request()) {
        //     show_404();
        //     return;
        // }
        log_message('info', 'ProductionSimulator::toggle() called');
        
        try {
            $enabled = $this->input->post('enabled');
            $username = $this->session->userdata('username');
            
            $this->ProductionSimulatorModel->updateSetting(
                'simulator_enabled',
                $enabled ? '1' : '0',
                $username
            );
            
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => true,
                    'message' => $enabled ? 'Simulator đã bật' : 'Simulator đã tắt',
                    'enabled' => (bool)$enabled
                ]));
                
        } catch (Exception $e) {
            $this->output
                ->set_content_type('application/json')
                ->set_status_header(500)
                ->set_output(json_encode([
                    'success' => false,
                    'message' => 'Lỗi: ' . $e->getMessage()
                ]));
        }
    }
}
