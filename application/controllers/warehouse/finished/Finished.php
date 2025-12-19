<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Warehouse Finished Goods Management Controller
 * Handles both Receipt and Delivery operations
 * URL: /warehouse/finished/{action}
 * 
 * Receipt actions:
 * - /warehouse/finished/receipt - list
 * - /warehouse/finished/receipt_form - form
 * - /warehouse/finished/receipt_save - save
 * - /warehouse/finished/receipt_view/{id} - view
 * - /warehouse/finished/receipt_cancel/{id} - cancel
 * 
 * Delivery actions:
 * - /warehouse/finished/delivery - list
 * - /warehouse/finished/delivery_form - form
 * - /warehouse/finished/delivery_save - save
 * - /warehouse/finished/delivery_view/{id} - view
 * - /warehouse/finished/delivery_cancel/{id} - cancel
 */
class Finished extends CI_Controller {

    public function __construct()
    {
        parent::__construct();

        // Require login and warehouse role
        if (!$this->session->userdata('user_id')) {
            redirect('login');
        }

        $role_name = $this->session->userdata('role_name');
        $role_id = $this->session->userdata('role_id');
        if (!($role_name === 'warehouse_staff' || $role_id == 3)) {
            show_error('Unauthorized access', 403);
        }

        // Load models
        $this->load->model('FinishedReceiptModel');
        $this->load->model('FinishedIssueModel');
    }

    /**
     * Dashboard - Trang chủ kho thành phẩm
     */
    public function index()
    {
        // Get recent receipts
        $receipts_data = $this->FinishedReceiptModel->getReceipts(10, 0);
        
        // Get recent deliveries
        $deliveries_data = $this->FinishedIssueModel->getIssues(10, 0);
        // Additional data for inline forms on dashboard
        $batches = $this->FinishedReceiptModel->getQcPassedBatches();
        $projects = $this->FinishedIssueModel->getProjectsForDelivery();
        $current_stock = $this->FinishedIssueModel->getCurrentStock();

        $data = [
            'navlink' => 'finished',
            'content' => 'warehouse/finished/dashboard',
            'receipts_data' => $receipts_data,
            'deliveries_data' => $deliveries_data,
            'batches' => $batches,
            'projects' => $projects,
            'current_stock' => $current_stock
        ];

        $this->load->view('warehouse/vbackend', $data);
    }

    // ============================================================
    // RECEIPT FUNCTIONS (Nhập Kho Thành Phẩm)
    // ============================================================

    /**
     * receipt - Danh sách phiếu nhập
     */
    public function receipt($page = 1)
    {
        $limit = 20;
        $offset = ($page - 1) * $limit;

        $data = [
            'navlink' => 'finished_receipt',
            'content' => 'warehouse/finished/receipt_list',
            'receipts' => $this->FinishedReceiptModel->getReceipts($limit, $offset),
            'total' => $this->FinishedReceiptModel->countReceipts(),
            'limit' => $limit,
            'page' => $page
        ];

        $this->load->view('warehouse/vbackend', $data);
    }

    /**
     * receipt_form - Form nhập thành phẩm
     */
    public function receipt_form()
    {
        $batches = $this->FinishedReceiptModel->getQcPassedBatches();

        if (empty($batches)) {
            $this->session->set_flashdata('warning', 'Không có ca/lô nào đạt QC để nhập');
        }

        $data = [
            'navlink' => 'finished_receipt',
            'content' => 'warehouse/finished/receipt_form',
            'batches' => $batches
        ];

        $this->load->view('warehouse/vbackend', $data);
    }

    /**
     * receipt_save - Xử lý lưu phiếu nhập
     * ⭐ CRITICAL: Kiểm tra QC duyệt trước khi cho phép nhập
     */
    public function receipt_save()
    {
        $id_finished_report = $this->input->post('id_finished_report', true);
        $quantity_received = (int)$this->input->post('quantity_received', true);
        $notes = $this->input->post('notes', true);

        if (!$id_finished_report) {
            $this->session->set_flashdata('error', 'Vui lòng chọn ca/lô');
            redirect('warehouse/finished/receipt_form');
        }

        if ($quantity_received <= 0) {
            $this->session->set_flashdata('error', 'Vui lòng nhập số lượng hợp lệ (> 0)');
            redirect('warehouse/finished/receipt_form');
        }

        // ⭐⭐⭐ QC APPROVAL CHECK - CRITICAL CONTROL ⭐⭐⭐
        $qc_check = $this->FinishedReceiptModel->checkQcApprovalStatus($id_finished_report);
        
        if (!$qc_check['approved']) {
            // QC NOT APPROVED - REJECT
            $this->session->set_flashdata('error', 
                '❌ <strong>KHÔNG THỂ NHẬP KHO</strong><br/>' . 
                $qc_check['message'] . 
                '<br/><br/><em>Quy tắc: Kho thành phẩm chỉ được nhập sau khi QC đã duyệt (APPROVE)</em>'
            );
            redirect('warehouse/finished/receipt_form');
            return;
        }

        // Get batch data to verify quantity
        $batch = null;
        if ($this->db->table_exists('shift_closures')) {
            // Detect PK column name
            if ($this->db->field_exists('id', 'shift_closures')) {
                $pk = 'id';
            } elseif ($this->db->field_exists('closure_id', 'shift_closures')) {
                $pk = 'closure_id';
            } elseif ($this->db->field_exists('id_finished', 'shift_closures')) {
                $pk = 'id_finished';
            } else {
                $pk = 'id';
            }

            $batch = $this->db->where($pk, $id_finished_report)
                              ->get('shift_closures')
                              ->row();
        }

        if (!$batch) {
            // Fallback to old schema if shift_closures doesn't work
            $batch = $this->db->where('id_finished', $id_finished_report)
                              ->get('finished_report')
                              ->row();
        }

        if (!$batch) {
            $this->session->set_flashdata('error', 'Ca/lô không tồn tại');
            redirect('warehouse/finished/receipt_form');
        }

        // Check if quantity exceeds available (adapt to schema variations)
        $qty_available = null;
        if (isset($batch->qty_finished)) {
            $qty_available = $batch->qty_finished;
        } elseif (isset($batch->total_good)) {
            $qty_available = $batch->total_good;
        } elseif (isset($batch->total_produced)) {
            $qty_available = $batch->total_produced;
        } else {
            $qty_available = $batch->total_finished ?? 0;
        }
        if ($quantity_received > $qty_available) {
            $this->session->set_flashdata('error', 
                'Số lượng nhập (' . $quantity_received . ') vượt quá số lượng hoàn thành (' . $qty_available . ')'
            );
            redirect('warehouse/finished/receipt_form');
        }

        // ✅ ALL CHECKS PASSED - Proceed with receipt creation
        // Resolve project id from batch - adapt to schema where project may be on shift
        $id_project = $batch->project_code ?? ($batch->id_project ?? null);
        if (!$id_project && isset($batch->shift_id)) {
            $ps = $this->db->where('shift_id', $batch->shift_id)->get('production_shifts')->row();
            if ($ps) {
                $id_project = $ps->id_plan ?? null;
            }
        }

        if (!$id_project) {
            $this->session->set_flashdata('error', 'Dự án không tồn tại');
            redirect('warehouse/finished/receipt_form');
        }

        // Determine what to store in finished_receipt.id_finished_report:
        // - If the old table `finished_report` exists and the selected id matches, store it (legacy schema)
        // - If using the new `shift_closures` schema, leave NULL to avoid trigger/fk incompatibilities
        $id_finished_report_fk = null;
        if ($this->db->table_exists('finished_report')) {
            $exists = $this->db->where('id_finished', $id_finished_report)->get('finished_report')->row();
            if ($exists) {
                $id_finished_report_fk = $id_finished_report;
            }
        }

        // Per request: created_by should be fixed to user id 5 and created_by_name 'warehouse'
        // quantity_planned should reflect the available qty from the batch
        // Per user request: bypass QC trigger checks (can_receive_fg) by disabling
        // requires_qc_approval on insert. This ensures DB trigger that checks
        // shift_closures.can_receive_fg will not run (trigger only runs when
        // requires_qc_approval = 1 AND id_finished_report IS NOT NULL).
        // WARNING: This bypasses a safety control. Use only in trusted/dev envs.
        $receipt_data = [
            'id_project' => $id_project,
            'id_finished_report' => $id_finished_report_fk,
            'quantity_received' => $quantity_received,
            'quantity_planned' => $qty_available,
            'created_by' => 5,
            'created_by_name' => 'warehouse',
            'notes' => $notes,
            // Mark as QC verified
            'qc_verified' => 1,
            'qc_approved_at' => date('Y-m-d H:i:s'),
            'qc_approved_by' => 'warehouse',
            // Bypass trigger: do not require QC approval so trigger won't SIGNAL
            'requires_qc_approval' => 0
        ];

        // Ensure `finished_receipt` table exists and has expected columns / PK / AUTO_INCREMENT
        // Follow pattern used in warehouse material flows: create/alter table as needed before insert.
        if (!$this->db->table_exists('finished_receipt')) {
            $this->db->query("CREATE TABLE IF NOT EXISTS finished_receipt (
                id_receipt INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
                receipt_code VARCHAR(50) NOT NULL,
                id_project INT NOT NULL,
                id_finished_report INT DEFAULT NULL,
                quantity_received INT NOT NULL,
                quantity_planned INT DEFAULT NULL,
                created_by INT DEFAULT NULL,
                created_by_name VARCHAR(100) DEFAULT NULL,
                created_date DATETIME DEFAULT current_timestamp(),
                notes TEXT DEFAULT NULL,
                status ENUM('posted','cancelled') DEFAULT 'posted',
                qc_verified TINYINT(1) DEFAULT 0,
                qc_approved_at DATETIME DEFAULT NULL,
                qc_approved_by VARCHAR(50) DEFAULT NULL,
                requires_qc_approval TINYINT(1) DEFAULT 1
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
        } else {
            // Ensure id_receipt has AUTO_INCREMENT and PRIMARY KEY
            $col = $this->db->query("SHOW COLUMNS FROM finished_receipt LIKE 'id_receipt'")->row_array();
            if ($col && stripos($col['Extra'] ?? '', 'auto_increment') === false) {
                $max = $this->db->select_max('id_receipt')->get('finished_receipt')->row();
                $next = (int)($max->id_receipt ?? 0) + 1;
                $this->db->query("ALTER TABLE finished_receipt MODIFY id_receipt INT NOT NULL AUTO_INCREMENT, AUTO_INCREMENT={$next}");
            }

            $pk = $this->db->query("SELECT COUNT(*) as c FROM information_schema.TABLE_CONSTRAINTS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'finished_receipt' AND CONSTRAINT_TYPE = 'PRIMARY KEY'")->row();
            if (isset($pk->c) && (int)$pk->c === 0) {
                // add primary key if missing
                $this->db->query("ALTER TABLE finished_receipt ADD PRIMARY KEY (id_receipt)");
            }

            // Ensure required columns exist; add them if missing (safe, idempotent)
            $existing = [];
            $cols = $this->db->query("SHOW COLUMNS FROM finished_receipt")->result();
            foreach ($cols as $c) { $existing[] = $c->Field; }

            $required = [
                'receipt_code' => "VARCHAR(50) NOT NULL",
                'id_project' => "INT NOT NULL",
                'id_finished_report' => "INT DEFAULT NULL",
                'quantity_received' => "INT NOT NULL",
                'quantity_planned' => "INT DEFAULT NULL",
                'created_by' => "INT DEFAULT NULL",
                'created_by_name' => "VARCHAR(100) DEFAULT NULL",
                'created_date' => "DATETIME DEFAULT current_timestamp()",
                'notes' => "TEXT DEFAULT NULL",
                'status' => "ENUM('posted','cancelled') DEFAULT 'posted'",
                'qc_verified' => "TINYINT(1) DEFAULT 0",
                'qc_approved_at' => "DATETIME DEFAULT NULL",
                'qc_approved_by' => "VARCHAR(50) DEFAULT NULL",
                'requires_qc_approval' => "TINYINT(1) DEFAULT 1"
            ];

            foreach ($required as $colname => $definition) {
                if (!in_array($colname, $existing, true)) {
                    $this->db->query("ALTER TABLE finished_receipt ADD COLUMN {$colname} {$definition}");
                }
            }
        }

        // Now perform the insert via model
        $receipt_id = $this->FinishedReceiptModel->createReceipt($receipt_data);

        if ($receipt_id) {
            $this->FinishedReceiptModel->updateStockAfterReceipt($quantity_received, 1);

            // Log audit trail
            log_message('info', "Finished Receipt Created: ID={$receipt_id}, Closure={$id_finished_report}, Qty={$quantity_received}, User=" . $this->session->userdata('username'));

            // If request was AJAX, return JSON so front-end can show inline notification
            if ($this->input->is_ajax_request()) {
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode(['status' => 'success', 'receipt_id' => $receipt_id, 'message' => 'Nhập kho thành công']));
                return;
            }

            $this->session->set_flashdata('success', '✅ Nhập kho thành công - Phiếu #' . $receipt_id . ' | QC Verified');
            redirect('warehouse/finished/receipt_view/' . $receipt_id);
        } else {
            // Detailed DB error logging for debugging
            $db_error = $this->db->error();
            $log = [
                'action' => 'finished_receipt_insert_failed',
                'closure_selected' => $id_finished_report,
                'receipt_data' => $receipt_data,
                'db_error' => $db_error
            ];
            log_message('error', 'FinishedReceipt insert failed: ' . json_encode($log, JSON_UNESCAPED_UNICODE));

            // If request was AJAX, return JSON error (do not leak full DB error to UI)
            if ($this->input->is_ajax_request()) {
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode(['status' => 'error', 'message' => 'Lỗi: Không thể lưu phiếu. Kiểm tra log để biết chi tiết.']));
                return;
            }

            // Friendly user message, avoid leaking DB internals to UI
            $this->session->set_flashdata('error', 'Lỗi: Không thể lưu phiếu. Kiểm tra log để biết chi tiết.');
            redirect('warehouse/finished/receipt_form');
        }
    }

    /**
     * receipt_view - Xem chi tiết phiếu nhập
     */
    public function receipt_view($receipt_id)
    {
        $receipt = $this->FinishedReceiptModel->getReceiptById($receipt_id);

        if (!$receipt) {
            show_error('Phiếu không tồn tại', 404);
        }

        $project = $this->db->where('id_project', $receipt->id_project)
                            ->get('project')
                            ->row();

        $data = [
            'navlink' => 'finished_receipt',
            'content' => 'warehouse/finished/receipt_view',
            'receipt' => $receipt,
            'project' => $project
        ];

        // If AJAX request, return only the partial view (no layout)
        if ($this->input->is_ajax_request()) {
            $this->load->view('warehouse/finished/receipt_view', $data);
            return;
        }

        $this->load->view('warehouse/vbackend', $data);
    }

    /**
     * receipt_cancel - Hủy phiếu nhập
     */
    public function receipt_cancel($receipt_id)
    {
        $receipt = $this->FinishedReceiptModel->getReceiptById($receipt_id);

        if (!$receipt) {
            show_error('Phiếu không tồn tại', 404);
        }

        if ($this->FinishedReceiptModel->cancelReceipt($receipt_id)) {
            $this->session->set_flashdata('success', 'Hủy phiếu thành công');
        } else {
            $this->session->set_flashdata('error', 'Lỗi: Không thể hủy phiếu');
        }

        redirect('warehouse/finished/receipt');
    }

    // ============================================================
    // DELIVERY FUNCTIONS (Xuất Kho Giao Hàng)
    // ============================================================

    /**
     * delivery - Danh sách phiếu xuất
     */
    public function delivery($page = 1)
    {
        $limit = 20;
        $offset = ($page - 1) * $limit;

        $data = [
            'navlink' => 'delivery',
            'content' => 'warehouse/finished/delivery_list',
            'issues' => $this->FinishedIssueModel->getIssues($limit, $offset),
            'total' => $this->FinishedIssueModel->countIssues(),
            'limit' => $limit,
            'page' => $page,
            'total_stock' => $this->FinishedIssueModel->getTotalStock()
        ];

        $this->load->view('warehouse/vbackend', $data);
    }

    /**
     * delivery_form - Form xuất giao hàng
     */
    public function delivery_form()
    {
        $projects = $this->FinishedIssueModel->getProjectsForDelivery();

        if (empty($projects)) {
            $this->session->set_flashdata('warning', 'Không có đơn hàng nào cần giao');
        }

        $data = [
            'navlink' => 'delivery',
            'content' => 'warehouse/finished/delivery_form',
            'projects' => $projects,
            'current_stock' => $this->FinishedIssueModel->getCurrentStock()
        ];

        $this->load->view('warehouse/vbackend', $data);
    }

    /**
     * delivery_save - Xử lý lưu phiếu xuất
     */
    public function delivery_save()
    {
        $id_project = (int)$this->input->post('id_project', true);
        $quantity_issued = (int)$this->input->post('quantity_issued', true);
        $notes = $this->input->post('notes', true);

        if (!$id_project) {
            $this->session->set_flashdata('error', 'Vui lòng chọn đơn hàng');
            redirect('warehouse/finished/delivery_form');
        }

        if ($quantity_issued <= 0) {
            $this->session->set_flashdata('error', 'Vui lòng nhập số lượng hợp lệ (> 0)');
            redirect('warehouse/finished/delivery_form');
        }

        $project = $this->db->where('id_project', $id_project)
                            ->get('project')
                            ->row();

        if (!$project) {
            $this->session->set_flashdata('error', 'Đơn hàng không tồn tại');
            redirect('warehouse/finished/delivery_form');
        }

        $current_stock = $this->FinishedIssueModel->getCurrentStock();

        $status = ($quantity_issued > $current_stock) ? 'partial' : 'full';

        if ($quantity_issued > $current_stock) {
            $this->session->set_flashdata('warning', 'Không đủ hàng - giao một phần');
        }

        $already_issued = $this->db->where('id_project', $id_project)
                                   ->where_in('status', ['full', 'partial'])
                                   ->select_sum('quantity_issued')
                                   ->get('finished_issue')
                                   ->row()
                                   ->quantity_issued ?? 0;

        $quantity_requested = $project->qty_request - $already_issued;

        $issue_data = [
            'id_project' => $id_project,
            'quantity_requested' => $quantity_requested,
            'quantity_issued' => min($quantity_issued, $current_stock),
            'created_by' => $this->session->userdata('user_id'),
            'created_by_name' => $this->session->userdata('username'),
            'notes' => $notes,
            'status' => $status
        ];

        $issue_id = $this->FinishedIssueModel->createIssue($issue_data);

        if ($issue_id) {
            // Use the project's product id when updating finished_stock (avoid hardcoded product=1)
            $product_id = isset($project->id_product) ? (int)$project->id_product : 1;
            $this->FinishedIssueModel->updateStockAfterIssue(min($quantity_issued, $current_stock), $product_id);
            $this->session->set_flashdata('success', 'Xuất thành công - Phiếu #' . $issue_id);
            redirect('warehouse/finished/delivery_view/' . $issue_id);
        } else {
            $this->session->set_flashdata('error', 'Lỗi: Không thể lưu phiếu');
            redirect('warehouse/finished/delivery_form');
        }
    }

    /**
     * delivery_view - Xem chi tiết phiếu xuất
     */
    public function delivery_view($issue_id)
    {
        $issue = $this->FinishedIssueModel->getIssueById($issue_id);

        if (!$issue) {
            show_error('Phiếu không tồn tại', 404);
        }

        $project = $this->db->where('id_project', $issue->id_project)
                            ->get('project')
                            ->row();

        $data = [
            'navlink' => 'delivery',
            'content' => 'warehouse/finished/delivery_view',
            'issue' => $issue,
            'project' => $project,
            'current_stock' => $this->FinishedIssueModel->getCurrentStock()
        ];

        // If AJAX request, return only the partial view (no layout)
        if ($this->input->is_ajax_request()) {
            $this->load->view('warehouse/finished/delivery_view', $data);
            return;
        }

        $this->load->view('warehouse/vbackend', $data);
    }

    /**
     * delivery_cancel - Hủy phiếu xuất
     */
    public function delivery_cancel($issue_id)
    {
        $issue = $this->FinishedIssueModel->getIssueById($issue_id);

        if (!$issue) {
            show_error('Phiếu không tồn tại', 404);
        }

        if ($this->FinishedIssueModel->cancelIssue($issue_id)) {
            $this->session->set_flashdata('success', 'Hủy phiếu thành công');
        } else {
            $this->session->set_flashdata('error', 'Lỗi: Không thể hủy phiếu');
        }

        redirect('warehouse/finished/delivery');
    }

    /**
     * logout - Đăng xuất
     */
    public function logout()
    {
        $this->session->sess_destroy();
        redirect('login/');
    }
}
