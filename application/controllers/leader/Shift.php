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
            planning.end_date,
            planning.suggested_shifts
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
            // Get suggested shift count from planning table
            $plan->suggested_shift_count = intval($plan->suggested_shifts ?? 0);
            
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

        // Chuẩn bị dữ liệu NVL theo kế hoạch (nếu có)
        $material_coverage = null;
        $bom_source = 'none';
        $material_confirm = null;
        try {
            // Chỉ xử lý nếu có liên kết tới kế hoạch sản xuất
            if (isset($shift->id_plan) && !empty($shift->id_plan)) {
                $this->load->model('PlanModel', 'planModel');

                $plan = $this->planModel->getPlanById($shift->id_plan);
                if ($plan && isset($plan->project) && isset($plan->project->id_product)) {
                    $product_id = $plan->project->id_product;

                    // Xác định số lượng dùng để tính NVL: CHỈ lấy theo sản lượng của CA
                    // - Nếu bảng production_shifts có cột target_quantity và ca này đã khai báo, dùng giá trị đó
                    // - Nếu không có target_quantity hoặc bằng 0/NULL thì KHÔNG fallback về qty_target của kế hoạch
                    //   (tránh trường hợp ca chỉ thực hiện một phần nhưng lại tính NVL cho cả kế hoạch)
                    $qty_for_calc = 0;
                    if ($this->db->field_exists('target_quantity', 'production_shifts') && !empty($shift->target_quantity)) {
                        $qty_for_calc = (float) $shift->target_quantity;
                    }

                    if ($qty_for_calc > 0) {
                        // 1) Thử tính coverage dựa trên BOM của product (product.bom)
                        $material_coverage = $this->planModel->checkMaterialCoverage($product_id, $qty_for_calc);
                        if (is_array($material_coverage) && !empty($material_coverage['details'])) {
                            $bom_source = 'product_bom';
                        }

                        // 2) Nếu không có chi tiết (BOM rỗng) nhưng kế hoạch có lưu JSON materials,
                        //    fallback: dùng trực tiếp plan.materials để tính nhu cầu NVL.
                        $needs_fallback = empty($material_coverage) || empty($material_coverage['details']);
                        if ($needs_fallback && isset($plan->materials) && is_array($plan->materials) && !empty($plan->materials)) {
                            $details = [];
                            $ok = true;

                            foreach ($plan->materials as $m) {
                                $id_material = isset($m['id_material']) ? $m['id_material'] : null;

                                // Hỗ trợ cả quantity_per_unit (mới) và quantity (legacy)
                                if (isset($m['quantity_per_unit'])) {
                                    $per_unit = (float) $m['quantity_per_unit'];
                                } elseif (isset($m['quantity'])) {
                                    $per_unit = (float) $m['quantity'];
                                } else {
                                    $per_unit = 0.0;
                                }

                                if ($per_unit <= 0) {
                                    continue;
                                }

                                $required = $per_unit * (float) $qty_for_calc;

                                // Lấy stock hiện tại (không trừ phân bổ đã có để đơn giản)
                                $stock = 0.0;
                                if (!empty($id_material)) {
                                    $mat = $this->db->get_where('material', ['id_material' => $id_material])->row();
                                    if ($mat && isset($mat->stock)) {
                                        $stock = (float) $mat->stock;
                                    }
                                }

                                $available = max(0.0, $stock);
                                $shortage = max(0.0, $required - $available);
                                if ($shortage > 0) {
                                    $ok = false;
                                }

                                $details[] = [
                                    'material_name' => isset($m['material_name']) ? $m['material_name'] : null,
                                    'required_qty' => $required,
                                    'available_qty' => $available,
                                    'shortage' => $shortage,
                                    'unit' => isset($m['uom']) ? $m['uom'] : (isset($m['unit']) ? $m['unit'] : null),
                                    'id_material' => $id_material,
                                ];
                            }

                            $material_coverage = [
                                'ok' => $ok,
                                'details' => $details,
                            ];

                            $bom_source = 'planning_materials';
                        }

                        // Bổ sung thêm thông tin ngữ cảnh cho view
                        if (is_array($material_coverage)) {
                            $material_coverage['_meta'] = [
                                'product_id' => $product_id,
                                'plan_id' => $plan->id_plan,
                                'plan_name' => $plan->plan_name ?? '',
                                'qty_for_calc' => $qty_for_calc,
                                'bom_source' => $bom_source,
                            ];
                        }
                    }
                }
            }
        } catch (Exception $e) {
            // Không chặn màn hình chi tiết ca nếu tính NVL lỗi – chỉ bỏ qua tab NVL
            log_message('error', 'Shift::detail material coverage error: ' . $e->getMessage());
            $material_coverage = null;
        }

        if (function_exists('log_message')) {
            $src = isset($bom_source) ? $bom_source : 'none';
            $cnt = (is_array($material_coverage) && !empty($material_coverage['details'])) ? count($material_coverage['details']) : 0;
            log_message('debug', 'Shift::detail NVL source=' . $src . ' shift_id=' . $shift_id . ' plan_id=' . (isset($shift->id_plan) ? $shift->id_plan : 'null') . ' detail_count=' . $cnt);
        }

        // Lấy thông tin xác nhận NVL (nếu đã xác nhận trước đó)
        if ($this->db->table_exists('shift_material_confirmations')) {
            $material_confirm = $this->db
                ->where('shift_id', $shift_id)
                ->order_by('id', 'DESC')
                ->get('shift_material_confirmations')
                ->row();
        }

        $data = [
            'shift' => $shift,
            'assigned_staff' => $assigned_staff,
            'breakdown_logs' => $breakdown_logs,
            'material_coverage' => $material_coverage,
            'material_confirm' => $material_confirm,
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
     * API: Xác nhận nhu cầu Nguyên Vật Liệu cho một ca
     * - Tính lại coverage NVL giống như màn detail
     * - Chỉ cho phép xác nhận khi không thiếu NVL (shortage = 0)
     * - Lưu snapshot vào bảng shift_material_confirmations
     */
    public function confirm_material()
    {
        header('Content-Type: application/json');

        $shift_id = (int) $this->input->post('shift_id');
        if (!$shift_id) {
            echo json_encode(['success' => false, 'message' => 'Thiếu shift_id']);
            return;
        }

        $shift = $this->shiftModel->getShiftById($shift_id);
        if (!$shift) {
            echo json_encode(['success' => false, 'message' => 'Ca không tồn tại']);
            return;
        }

        if (empty($shift->id_plan)) {
            echo json_encode(['success' => false, 'message' => 'Ca chưa gắn với kế hoạch sản xuất']);
            return;
        }

        $this->load->model('PlanModel', 'planModel');

        try {
            $plan = $this->planModel->getPlanById($shift->id_plan);
            if (!$plan || !isset($plan->project) || !isset($plan->project->id_product)) {
                echo json_encode(['success' => false, 'message' => 'Không tìm thấy sản phẩm để tính NVL']);
                return;
            }

            $product_id = $plan->project->id_product;

            // Xác định số lượng dùng để tính NVL (giống detail): chỉ theo sản lượng ca
            $qty_for_calc = 0;
            if ($this->db->field_exists('target_quantity', 'production_shifts') && !empty($shift->target_quantity)) {
                $qty_for_calc = (float) $shift->target_quantity;
            }

            if ($qty_for_calc <= 0) {
                echo json_encode(['success' => false, 'message' => 'Ca chưa có sản lượng mục tiêu (target_quantity) hợp lệ để xác nhận NVL']);
                return;
            }

            // Tính coverage dựa trên BOM sản phẩm
            $material_coverage = $this->planModel->checkMaterialCoverage($product_id, $qty_for_calc);
            $bom_source = 'product_bom';

            // Fallback: nếu BOM rỗng, nhưng kế hoạch có lưu materials JSON thì dùng materials
            $needs_fallback = empty($material_coverage) || empty($material_coverage['details']);
            if ($needs_fallback && isset($plan->materials) && is_array($plan->materials) && !empty($plan->materials)) {
                $details = [];
                $ok = true;

                foreach ($plan->materials as $m) {
                    $id_material = isset($m['id_material']) ? $m['id_material'] : null;

                    if (isset($m['quantity_per_unit'])) {
                        $per_unit = (float) $m['quantity_per_unit'];
                    } elseif (isset($m['quantity'])) {
                        $per_unit = (float) $m['quantity'];
                    } else {
                        $per_unit = 0.0;
                    }

                    if ($per_unit <= 0) {
                        continue;
                    }

                    $required = $per_unit * (float) $qty_for_calc;

                    $stock = 0.0;
                    if (!empty($id_material)) {
                        $mat = $this->db->get_where('material', ['id_material' => $id_material])->row();
                        if ($mat && isset($mat->stock)) {
                            $stock = (float) $mat->stock;
                        }
                    }

                    $available = max(0.0, $stock);
                    $shortage = max(0.0, $required - $available);
                    if ($shortage > 0) {
                        $ok = false;
                    }

                    $details[] = [
                        'material_name' => isset($m['material_name']) ? $m['material_name'] : null,
                        'required_qty' => $required,
                        'available_qty' => $available,
                        'shortage' => $shortage,
                        'unit' => isset($m['uom']) ? $m['uom'] : (isset($m['unit']) ? $m['unit'] : null),
                        'id_material' => $id_material,
                    ];
                }

                $material_coverage = [
                    'ok' => $ok,
                    'details' => $details,
                ];

                $bom_source = 'planning_materials';
            }

            if (!is_array($material_coverage) || empty($material_coverage['details'])) {
                echo json_encode(['success' => false, 'message' => 'Không có dữ liệu NVL để xác nhận']);
                return;
            }

            // Không cho xác nhận nếu còn thiếu NVL
            $ok = isset($material_coverage['ok']) ? (bool) $material_coverage['ok'] : true;
            if (!$ok) {
                echo json_encode(['success' => false, 'message' => 'NVL còn thiếu, không thể xác nhận']);
                return;
            }

            // Bổ sung meta giống detail
            $material_coverage['_meta'] = [
                'product_id' => $product_id,
                'plan_id' => $plan->id_plan,
                'plan_name' => $plan->plan_name ?? '',
                'qty_for_calc' => $qty_for_calc,
                'bom_source' => $bom_source,
            ];

            // Đảm bảo bảng xác nhận tồn tại
            $this->db->query('CREATE TABLE IF NOT EXISTS `shift_material_confirmations` (
                `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `shift_id` INT NOT NULL,
                `plan_id` INT NULL,
                `product_id` INT NULL,
                `qty_calculated` DECIMAL(15,3) NULL,
                `status` VARCHAR(20) DEFAULT "confirmed",
                `coverage_ok` TINYINT(1) DEFAULT 1,
                `confirmed_by` INT NULL,
                `confirmed_username` VARCHAR(100) NULL,
                `confirmed_at` DATETIME NOT NULL,
                `snapshot_json` LONGTEXT NULL,
                PRIMARY KEY (`id`),
                KEY `idx_shift` (`shift_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;');

            $data = [
                'shift_id' => $shift_id,
                'plan_id' => $plan->id_plan,
                'product_id' => $product_id,
                'qty_calculated' => $qty_for_calc,
                'status' => 'confirmed',
                'coverage_ok' => 1,
                'confirmed_by' => $this->session->userdata('user_id'),
                'confirmed_username' => $this->session->userdata('username'),
                'confirmed_at' => date('Y-m-d H:i:s'),
                'snapshot_json' => json_encode($material_coverage, JSON_UNESCAPED_UNICODE),
            ];

            $this->db->insert('shift_material_confirmations', $data);

            echo json_encode(['success' => true, 'message' => 'Đã xác nhận NVL cho ca']);
        } catch (Exception $e) {
            log_message('error', 'Shift::confirm_material error: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Lỗi khi xác nhận NVL: ' . $e->getMessage()]);
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
    public function delete($shift_id = null)
    {
        header('Content-Type: application/json');

        log_message('debug', 'Shift::delete called with shift_id=' . var_export($shift_id, true));

        if (empty($shift_id)) {
            echo json_encode(['success' => false, 'message' => 'Không tìm thấy ID ca']);
            return;
        }

        try {
            $shift = $this->shiftModel->getShiftById($shift_id);
            log_message('debug', 'Shift::delete getShiftById result: ' . var_export($shift, true));
            
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

            // Delete related records in correct order (reverse dependency)
            // 1. Get all closure IDs for this shift
            $closures = $this->db->where('shift_id', $shift_id)->get('shift_closures')->result();
            foreach ($closures as $closure) {
                // 2. Get all closure_machine IDs for this closure
                $closure_machines = $this->db->where('closure_id', $closure->closure_id)->get('shift_closure_machines')->result();
                foreach ($closure_machines as $cm) {
                    // 3. Delete defects for this closure_machine
                    $this->db->where('closure_machine_id', $cm->id);
                    $this->db->delete('shift_closure_defects');
                }
                // 4. Delete closure_machines for this closure
                $this->db->where('closure_id', $closure->closure_id);
                $this->db->delete('shift_closure_machines');
            }
            // 5. Delete closures for this shift
            $this->db->where('shift_id', $shift_id);
            $this->db->delete('shift_closures');

            // 6. Delete shift_machine_staff assignments
            $this->db->where('shift_id', $shift_id);
            $this->db->delete('shift_machine_staff');

            // 7. Delete shift
            $this->db->where('shift_id', $shift_id);
            $deleted = $this->db->delete('production_shifts');
            log_message('debug', 'Shift::delete result: ' . var_export($deleted, true) . ' - Affected rows: ' . $this->db->affected_rows());
            
            if ($deleted) {
                echo json_encode(['success' => true, 'message' => 'Xóa ca thành công']);
            } else {
                $db_error = $this->db->error();
                echo json_encode(['success' => false, 'message' => 'Xóa ca thất bại: ' . $db_error['message']]);
            }
        } catch (Exception $e) {
            log_message('error', 'Shift::delete exception: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Lỗi server: ' . $e->getMessage()]);
        }
    }}