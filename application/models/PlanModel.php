<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * PlanModel
 *
 * Xử lý nghiệp vụ Lập và Duyệt Kế hoạch Sản xuất (KeHoachSX)
 * - Tính nhu cầu NVL theo BOM (dùng `product.bom` JSON)
 * - Kiểm tra coverage NVL so với tồn kho và phân bổ hiện tại (p_material)
 * - Tạo bản ghi `planning` và (nếu có) tạo `plan_shift` / `p_material` / `p_machine`
 *
 * Lưu ý:
 * - Thiết kế nhằm tương thích với schema hiện có trong DB dump.
 * - Nếu tồn tại bảng `plan_line` sẽ dùng bảng đó để lưu phân rã theo dây chuyền,
 *   nếu không thì lưu trực tiếp `plan_shift` liên kết đến `id_plan`.
 * - Các hàm có ghi chú chi tiết bằng tiếng Việt để bạn dễ hiểu.
 */
class PlanModel extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->model('ProductModel');
        $this->load->model('CrudModel');
    }

    /**
     * Lấy danh sách đơn hàng đã "đã duyệt" để BGĐ lập kế hoạch
     * @param array $filters (optional) - hỗ trợ filter theo customer, project, date
     * @return array
     */
    public function getApprovedOrdersForPlanning($filters = [])
    {
        // Giả sử pr_status = 1 là "Đã duyệt" theo dump mẫu
        // Chỉ trả về các đơn đã duyệt mà CHƯA có bản planning (để ẩn đơn sau khi đã lập kế hoạch)
        $sql = "SELECT pr.* , p.product_name, c.cust_name
            FROM project pr
            LEFT JOIN product p ON pr.id_product = p.id_product
            LEFT JOIN customer c ON pr.id_cust = c.id_cust
            LEFT JOIN planning pl ON pl.id_project = pr.id_project
            WHERE pr.pr_status = 1 AND pl.id_project IS NULL";

        $params = [];

        if (!empty($filters['customer'])) {
            $sql .= " AND c.cust_name LIKE ?";
            $params[] = "%{$filters['customer']}%";
        }
        if (!empty($filters['project_name'])) {
            $sql .= " AND pr.project_name LIKE ?";
            $params[] = "%{$filters['project_name']}%";
        }
        if (!empty($filters['due_before'])) {
            $sql .= " AND pr.entry_date <= ?";
            $params[] = $filters['due_before'];
        }

        $sql .= " ORDER BY pr.entry_date ASC";

        $query = $this->db->query($sql, $params);
        return $query->result();
    }

    /**
     * Lấy chi tiết đơn hàng (project) kèm thông tin sản phẩm và BOM
     * @param int $id_project
     * @return object|null
     */
    public function getOrderDetails($id_project)
    {
        $query = $this->db->get_where('project', ['id_project' => $id_project]);
        $project = $query->row();
        if (!$project) {
            return null;
        }

        // Lấy thông tin product + BOM
        $product = $this->ProductModel->getProductById($project->id_product);
        $project->product = $product;

        return $project;
    }

    /**
     * Tính nhu cầu NVL (required materials) cho 1 sản phẩm với target qty
     * Dùng BOM lưu trong cột `product.bom` (đã có ProductModel->bom_data)
     * @param int $product_id
     * @param int|float $qty_target
     * @return array danh sách vật tư: [ ['id_material','material_name','required_qty','unit'] , ... ]
     */
    public function computeMaterialRequirements($product_id, $qty_target)
    {
        $product = $this->ProductModel->getProductById($product_id);
        $result = [];

        if (!$product) {
            return $result;
        }

        $materials = $product->bom_data['materials'] ?? [];
        foreach ($materials as $m) {
            $per_unit = isset($m['quantity']) ? floatval($m['quantity']) : 0;
            $required = $per_unit * floatval($qty_target);

            $result[] = [
                'id_material' => isset($m['id_material']) ? $m['id_material'] : null,
                'material_name' => isset($m['material_name']) ? $m['material_name'] : ($m['name'] ?? null),
                'required_qty' => $required,
                'unit' => $m['unit'] ?? null,
                'per_unit' => $per_unit
            ];
        }

        return $result;
    }

    /**
     * Lấy tổng phân bổ NVL hiện có (từ p_material) cho 1 material
     * @param int $id_material
     * @return float
     */
    public function getAllocatedMaterialQty($id_material)
    {
        $this->db->select_sum('used_stock', 'allocated');
        $this->db->where('id_material', $id_material);
        $row = $this->db->get('p_material')->row();
        return $row ? floatval($row->allocated) : 0.0;
    }

    /**
     * Kiểm tra coverage NVL cho 1 product + target
     * - So sánh required_qty với (material.stock - allocated)
     * @param int $product_id
     * @param int|float $qty_target
     * @return array [ 'ok' => bool, 'details' => [ {material, required, available, shortage} ] ]
     */
    public function checkMaterialCoverage($product_id, $qty_target)
    {
        $requirements = $this->computeMaterialRequirements($product_id, $qty_target);
        $details = [];
        $ok = true;

        foreach ($requirements as $r) {
            $mat_id = $r['id_material'];
            $stock = 0;
            $allocated = 0;

            if ($mat_id) {
                $m = $this->db->get_where('material', ['id_material' => $mat_id])->row();
                if ($m) {
                    $stock = floatval($m->stock);
                }
                $allocated = $this->getAllocatedMaterialQty($mat_id);
            }

            $available = max(0, $stock - $allocated);
            $shortage = max(0, $r['required_qty'] - $available);

            if ($shortage > 0) {
                $ok = false;
            }

            $details[] = [
                'material_name' => $r['material_name'],
                'required_qty' => $r['required_qty'],
                'available_qty' => $available,
                'shortage' => $shortage,
                'unit' => $r['unit'],
                'id_material' => $mat_id
            ];
        }

        return ['ok' => $ok, 'details' => $details];
    }

    /**
     * Tạo Kế hoạch sản xuất (planning) và các bản ghi liên quan
     * Input $data: [ 'plan_name','id_project','qty_target','end_date', 'lines' => [ ... ], 'create_allocate' => bool ]
     * - lines: mỗi phần tử có ['daychuyen_id','variant_id','target_qty', 'shifts' => [ ... ] ]
     * Behavior:
     * - Nếu bảng `plan_line` tồn tại thì tạo bản ghi phân rã line
     * - Tạo plan_shift từ `lines[].shifts` hoặc tự sinh nếu chỉ có số ca
     * - Nếu create_allocate=true sẽ tạo bản ghi `p_material` (dựa trên requirement phân bổ cho toàn bộ kế hoạch)
     * @param array $data
     * @return array ['success'=>bool,'message'=>string,'id_plan'=>int|null]
     */
    public function createPlan(array $data)
    {
        $this->db->trans_start();

        try {
            // Validate bắt buộc
            if (empty($data['id_project']) || empty($data['qty_target'])) {
                throw new Exception('Thiếu thông tin id_project hoặc qty_target');
            }

            $plan = [
                'plan_name' => $data['plan_name'] ?? 'Kế hoạch ' . time(),
                'id_project' => $data['id_project'],
                'qty_target' => $data['qty_target'],
                'end_date' => $data['end_date'] ?? null,
                'pl_status' => isset($data['pl_status']) ? $data['pl_status'] : 0, // 0 = draft, 1 = approved
            ];

            // Nếu có field note, suggested_shifts, machine_id hoặc materials trong table planning thì thêm vào insert
            // Nếu table planning có các cột start_date/finish_date thì lưu chúng từ $data
            if (!empty($data['start_date']) && $this->db->field_exists('start_date', 'planning')) {
                $plan['start_date'] = $data['start_date'];
            }
            if (!empty($data['finish_date']) && $this->db->field_exists('finish_date', 'planning')) {
                $plan['finish_date'] = $data['finish_date'];
            }
            if (!empty($data['note']) && $this->db->field_exists('note', 'planning')) {
                $plan['note'] = $data['note'];
            }
            if (isset($data['suggested_shifts']) && $this->db->field_exists('suggested_shifts', 'planning')) {
                $plan['suggested_shifts'] = intval($data['suggested_shifts']);
            }
            if (!empty($data['machine_id']) && $this->db->field_exists('machine_id', 'planning')) {
                $plan['machine_id'] = $data['machine_id'];
            }
            // materials: nếu planning có cột materials (JSON) thì lưu; nếu không, xử lý dưới dạng allocation
            $plan_materials = !empty($data['materials']) ? $data['materials'] : [];
            if (!empty($plan_materials) && $this->db->field_exists('materials', 'planning')) {
                $plan['materials'] = json_encode($plan_materials, JSON_UNESCAPED_UNICODE);
            }

            // lines: nếu caller cung cấp và table planning có cột `lines`, lưu dưới dạng JSON
            $lines_for_plan = $data['lines'] ?? [];
            if (!empty($lines_for_plan) && $this->db->field_exists('lines', 'planning')) {
                // If caller provided a plain string (label), store it as-is.
                if (is_string($lines_for_plan)) {
                    $plan['lines'] = $lines_for_plan;
                } else {
                    // Otherwise store JSON representation (array/object)
                    $plan['lines'] = json_encode($lines_for_plan, JSON_UNESCAPED_UNICODE);
                }
            }

            // Insert planning
            $this->db->insert('planning', $plan);
            $id_plan = $this->db->insert_id();

            // Nếu có lines và tồn tại table plan_line thì tạo
            $lines = $data['lines'] ?? [];
            if ($this->db->table_exists('plan_line') && !empty($lines)) {
                foreach ($lines as $line) {
                    $pl = [
                        'id_plan' => $id_plan,
                        'daychuyen_id' => $line['daychuyen_id'] ?? null,
                        'variant_id' => $line['variant_id'] ?? null,
                        'target_qty' => $line['target_qty'] ?? 0,
                        'start_date' => $line['start_date'] ?? null,
                        'end_date' => $line['end_date'] ?? null,
                        'assigned_leader' => $line['assigned_leader'] ?? null,
                        'status' => $line['status'] ?? 1
                    ];
                    $this->db->insert('plan_line', $pl);
                    $id_plan_line = $this->db->insert_id();

                    // Tạo các ca (shifts) nếu có
                    if (!empty($line['shifts'])) {
                        foreach ($line['shifts'] as $sh) {
                            $this->createPlanShift($id_plan, $id_plan_line, $sh);
                        }
                    }
                }
            } else {
                // Fallback: nếu không có plan_line, tạo plan_shift trực tiếp liên kết id_plan
                if (!empty($lines)) {
                    foreach ($lines as $line) {
                        if (!empty($line['shifts'])) {
                            foreach ($line['shifts'] as $sh) {
                                $this->createPlanShift($id_plan, null, $sh);
                            }
                        }
                    }
                }
            }

            // LƯU Ý (tiếng Việt): Đã xóa chức năng tạo tự động phân bổ NVL (bảng `p_material`).
            // - Nếu người dùng cung cấp `materials` trong form và bảng `planning` có cột `materials`,
            //   dữ liệu JSON này vẫn được lưu như trên.
            // - Tuy nhiên, việc tự động sinh các bản ghi `p_material` từ BOM sản phẩm đã bị vô hiệu hóa
            //   để tránh thay đổi tồn kho không mong muốn.
            // - Việc phân bổ NVL cần được thực hiện thông qua một quy trình phân bổ riêng (hoặc UI/endpoint
            //   rõ ràng) nếu cần.
            // NOTE: Automatic creation of p_material (NVL allocations) has been removed.
            // If the caller provided explicit materials and the `planning` table
            // supports a `materials` JSON column, that JSON is saved above. Any
            // automatic allocation from BOM or implicit creation of `p_material`
            // records is intentionally disabled to avoid unexpected inventory
            // changes. Allocation should be performed by a dedicated allocation
            // process or an explicit user action.

            // Log vào audit_log nếu tồn tại
            if ($this->db->table_exists('audit_log')) {
                $this->db->insert('audit_log', [
                    'user_id' => $this->session->userdata('user_id'),
                    'username' => $this->session->userdata('username'),
                    'action' => 'create_plan',
                    'module' => 'planning',
                    'record_id' => $id_plan,
                    'old_value' => null,
                    'new_value' => json_encode($plan, JSON_UNESCAPED_UNICODE),
                    'ip_address' => $this->input->ip_address(),
                    'user_agent' => $this->input->user_agent()
                ]);
            }

            $this->db->trans_complete();
            if ($this->db->trans_status() === false) {
                throw new Exception('Lỗi khi lưu kế hoạch vào DB');
            }

            return ['success' => true, 'message' => 'Kế hoạch được tạo', 'id_plan' => $id_plan];

        } catch (Exception $e) {
            $this->db->trans_rollback();
            return ['success' => false, 'message' => $e->getMessage(), 'id_plan' => null];
        }
    }

    /**
     * Tạo 1 bản ghi plan_shift (CaSX) - hàm helper
     * @param int $id_plan
     * @param int|null $id_plan_line
     * @param array $sh (['id_shift','id_staff','start_date','start_time','end_time','target_qty','ps_status'])
     * @return int|null id_planshift
     */
    public function createPlanShift($id_plan, $id_plan_line = null, $sh = [])
    {
        $row = [
            'id_plan' => $id_plan,
            'id_shift' => $sh['id_shift'] ?? null,
            'id_staff' => $sh['id_staff'] ?? null,
            'start_date' => $sh['start_date'] ?? null,
            'ps_status' => $sh['ps_status'] ?? 1,
        ];

        // Nếu có plan_line_id trường này lưu vào table nếu tồn tại
        if (!is_null($id_plan_line) && $this->db->field_exists('plan_line_id', 'plan_shift')) {
            $row['plan_line_id'] = $id_plan_line;
        }

        // Nếu table có trường start_time/end_time/target_qty thì thêm
        if ($this->db->field_exists('start_time', 'plan_shift') && isset($sh['start_time'])) {
            $row['start_time'] = $sh['start_time'];
        }
        if ($this->db->field_exists('end_time', 'plan_shift') && isset($sh['end_time'])) {
            $row['end_time'] = $sh['end_time'];
        }
        if ($this->db->field_exists('target_qty', 'plan_shift') && isset($sh['target_qty'])) {
            $row['target_qty'] = $sh['target_qty'];
        }

        $this->db->insert('plan_shift', $row);
        return $this->db->insert_id();
    }

    /**
     * Phê duyệt kế hoạch (BGĐ)
     * - Cập nhật pl_status
     * - Ghi audit_log
     * @param int $id_plan
     * @param int $approver_id (optional)
     * @return array
     */
    public function approvePlan($id_plan, $approver_id = null)
    {
        $this->db->trans_start();
        try {
            $plan = $this->db->get_where('planning', ['id_plan' => $id_plan])->row();
            if (!$plan) {
                throw new Exception('Kế hoạch không tồn tại');
            }

            // Cập nhật trạng thái (1 = approved)
            $this->db->where('id_plan', $id_plan)->update('planning', ['pl_status' => 1]);

            // Log
            if ($this->db->table_exists('audit_log')) {
                $this->db->insert('audit_log', [
                    'user_id' => $approver_id ?? $this->session->userdata('user_id'),
                    'username' => $this->session->userdata('username'),
                    'action' => 'approve_plan',
                    'module' => 'planning',
                    'record_id' => $id_plan,
                    'old_value' => json_encode($plan, JSON_UNESCAPED_UNICODE),
                    'new_value' => json_encode(['pl_status' => 1], JSON_UNESCAPED_UNICODE),
                    'ip_address' => $this->input->ip_address(),
                    'user_agent' => $this->input->user_agent()
                ]);
            }

            $this->db->trans_complete();
            if ($this->db->trans_status() === false) {
                throw new Exception('Lỗi khi phê duyệt kế hoạch');
            }

            return ['success' => true, 'message' => 'Kế hoạch đã được phê duyệt'];
        } catch (Exception $e) {
            $this->db->trans_rollback();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Auto-generate shifts (ví dụ tạo N ca mỗi ngày với target chia đều)
     * - Đây là hàm tiện ích, dùng khi BGĐ chọn auto-generate ca theo target
     * @param int $id_plan
     * @param int|null $id_plan_line
     * @param int $days_count Số ngày
     * @param int $shifts_per_day Số ca/ngày
     * @param int $total_target Tổng mục tiêu cho đoạn này
     * @return array created shifts ids
     */
    public function autoGenerateShifts($id_plan, $id_plan_line = null, $days_count = 1, $shifts_per_day = 1, $total_target = 0)
    {
        $created = [];
        if ($days_count <= 0 || $shifts_per_day <= 0) {
            return $created;
        }

        // Lấy danh sách shiftment mẫu (giả sử shiftment có start_time/end_time)
        $shift_defs = $this->db->get('shiftment')->result();
        if (empty($shift_defs)) {
            // Không có định nghĩa ca, tạo ca đơn giản
            for ($d = 0; $d < $days_count; $d++) {
                for ($s = 0; $s < $shifts_per_day; $s++) {
                    $target_each = $total_target ? intval(round($total_target / ($days_count * $shifts_per_day))) : 0;
                    $sh = [
                        'id_shift' => null,
                        'id_staff' => null,
                        'start_date' => date('Y-m-d', strtotime("+{$d} days")),
                        'start_time' => null,
                        'end_time' => null,
                        'target_qty' => $target_each,
                        'ps_status' => 1
                    ];
                    $created[] = $this->createPlanShift($id_plan, $id_plan_line, $sh);
                }
            }
            return $created;
        }

        // Có định nghĩa ca, dùng vòng lặp theo ngày
        for ($d = 0; $d < $days_count; $d++) {
            for ($s = 0; $s < $shifts_per_day; $s++) {
                $def = $shift_defs[$s % count($shift_defs)];
                $target_each = $total_target ? intval(round($total_target / ($days_count * $shifts_per_day))) : 0;
                $sh = [
                    'id_shift' => $def->id_shift,
                    'id_staff' => null,
                    'start_date' => date('Y-m-d', strtotime("+{$d} days")),
                    'start_time' => $def->start_time ?? null,
                    'end_time' => $def->end_time ?? null,
                    'target_qty' => $target_each,
                    'ps_status' => 1
                ];
                $created[] = $this->createPlanShift($id_plan, $id_plan_line, $sh);
            }
        }

        return $created;
    }

    /**
     * Lấy công suất máy (capacity) từ bảng `machine`
     * GIẢ ĐỊNH: trường `machine.capacity` biểu thị số sản phẩm / GIỜ (units per hour).
     * Nếu repo của bạn lưu theo đơn vị khác, hãy sửa lại logic tương ứng.
     * @param int $id_machine
     * @return float|null capacity per hour hoặc null nếu không tìm thấy
     */
    public function getMachineCapacity($id_machine)
    {
        if (empty($id_machine)) {
            return null;
        }
        $m = $this->db->get_where('machine', ['id_machine' => $id_machine])->row();
        if (!$m) {
            return null;
        }
        return floatval($m->capacity);
    }

    /**
     * Tính số giờ cần thiết để sản xuất $qty_target dựa trên công suất (units per hour)
     * @param float $qty_target
     * @param float $capacity_per_hour
     * @return float hours needed (giờ), trả về 0 nếu capacity_per_hour <= 0
     */
    public function computeRequiredHours($qty_target, $capacity_per_hour)
    {
        $qty = floatval($qty_target);
        $cap = floatval($capacity_per_hour);
        if ($cap <= 0) {
            return 0.0;
        }
        $hours = $qty / $cap;
        // Làm tròn lên 2 chữ số thập phân để tránh sai số nhỏ
        return round($hours, 2);
    }

    /**
     * Lấy độ dài ca mặc định (giờ) từ bảng `shiftment` (tính trung bình nếu có nhiều loại ca)
     * Xử lý ca qua đêm: nếu end_time < start_time thì cộng 24 giờ
     * @return float shift length hours (ví dụ 8.00)
     */
    public function getDefaultShiftLengthHours()
    {
        $shifts = $this->db->get('shiftment')->result();
        if (empty($shifts)) {
            return 8.0; // fallback mặc định 8 giờ
        }
        $total = 0.0;
        $count = 0;
        foreach ($shifts as $s) {
            if (!isset($s->start_time) || !isset($s->end_time)) {
                continue;
            }
            $start = strtotime($s->start_time);
            $end = strtotime($s->end_time);
            $diff = ($end - $start) / 3600.0;
            if ($diff <= 0) {
                // ca qua đêm => cộng 24h
                $diff = (($end + 24 * 3600) - $start) / 3600.0;
            }
            $total += $diff;
            $count++;
        }
        if ($count == 0) {
            return 8.0;
        }
        return round($total / $count, 2);
    }

    /**
     * Ước tính số giờ và số ca cần thiết dựa trên số lượng và công suất máy
     * Quy tắc: hours_needed = qty_target / capacity_per_hour
     *          shifts_needed = ceil(hours_needed / shift_length_hours)
     * @param float $qty_target
     * @param int|null $machine_id nếu có, sẽ lấy công suất từ bảng `machine`
     * @param float|null $capacity_per_hour (nếu muốn override)
     * @param float|null $shift_length_hours (nếu muốn override; nếu null lấy mặc định từ `shiftment`)
     * @return array ['hours_needed'=>float,'shift_length_hours'=>float,'shifts_needed'=>int,'capacity_used'=>float]
     */
    public function estimateShiftsNeeded($qty_target, $machine_id = null, $capacity_per_hour = null, $shift_length_hours = null)
    {
        // Lấy capacity
        $cap = null;
        if (!empty($capacity_per_hour)) {
            $cap = floatval($capacity_per_hour);
        } elseif (!empty($machine_id)) {
            $cap = $this->getMachineCapacity($machine_id);
        }

        if (empty($cap) || $cap <= 0) {
            // Nếu không có thông tin capacity, trả về shifts_needed = 0 và hours_needed = 0
            return [
                'hours_needed' => 0.0,
                'shift_length_hours' => $shift_length_hours ?? $this->getDefaultShiftLengthHours(),
                'shifts_needed' => 0,
                'capacity_used' => $cap
            ];
        }

        $hours = $this->computeRequiredHours($qty_target, $cap);
        $shift_len = $shift_length_hours ?? $this->getDefaultShiftLengthHours();
        if ($shift_len <= 0) {
            $shift_len = 8.0;
        }
        $shifts = intval(ceil($hours / $shift_len));

        return [
            'hours_needed' => $hours,
            'shift_length_hours' => $shift_len,
            'shifts_needed' => $shifts,
            'capacity_used' => $cap
        ];
    }
}
