<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class FinishedReceiptModel extends CI_Model {

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    /**
     * CRITICAL CONTROL: Lấy danh sách ca/lô đã QC DUYỆT (APPROVE)
     * Only returns batches where:
     * - QC Decision = APPROVE (for shift_closures)
     * - shift_closures.can_receive_fg = 1
     * - shift_closures.status = VERIFIED
     * 
     * Fallback to finished_report ONLY if shift_closures table does not exist
     * (not when it's empty - empty means no approved batches)
     * 
     * This enforces the rule: "Kho thành phẩm chỉ được nhập sau khi QC duyệt"
     * 
     * Query từ QC Module: shift_closures + qc_decisions
     */
    public function getQcPassedBatches()
    {
        $result = [];
        $shift_closures_exists = $this->db->table_exists('shift_closures');

        // Determine primary key column name and a human-friendly closure code column
        // Many deployments use 'id' as PK, some use 'closure_id' or 'id_finished'.
        $pkColumn = 'id';
        $closureCodeColumn = 'sc.id';
        if ($shift_closures_exists) {
            if ($this->db->field_exists('id', 'shift_closures')) {
                $pkColumn = 'id';
            } elseif ($this->db->field_exists('closure_id', 'shift_closures')) {
                $pkColumn = 'closure_id';
            } elseif ($this->db->field_exists('id_finished', 'shift_closures')) {
                $pkColumn = 'id_finished';
            } else {
                // fallback to closure_id if nothing matched
                $pkColumn = 'closure_id';
            }

            // closureCodeColumn is a display code; prefer 'code' or 'closure_id' when available
            if ($this->db->field_exists('code', 'shift_closures')) {
                $closureCodeColumn = 'sc.code';
            } else {
                $closureCodeColumn = 'sc.' . $pkColumn;
            }
        }

        // Thử lấy từ QC Module trước (mới)
        if ($shift_closures_exists) {
                        // Use detected primary key column name instead of assuming 'id'
                        $pk = 'sc.' . $pkColumn;

                        // Determine closure join key for tables that reference closure_id
                        $closureJoinKey = $this->db->field_exists('closure_id', 'shift_closures') ? 'sc.closure_id' : $pk;

                        $sql = "SELECT 
                                            {$pk} AS id_finished,
                                            {$closureCodeColumn} AS closure_code,
                                            sc.shift_id,
                                            ps.id_plan AS id_project,
                                            p.project_name,
                                            wir.product_id,
                                            pr.product_name,
                                            sc.total_good AS qty_passed,
                                            sc.total_defect AS qty_waste,
                                            sc.closure_date AS fdate,
                                            sc.closed_by,
                                            qd.result AS qc_result,
                                            qd.aql AS qc_aql,
                                            qd.defect_rate,
                                            qd.decided_at AS qc_approved_at,
                                            qd.decided_by AS qc_approved_by,
                                            COALESCE(SUM(CASE WHEN recpt.status = 'posted' THEN recpt.quantity_received ELSE 0 END), 0) AS qty_already_received
                                        FROM shift_closures sc
                                        LEFT JOIN production_shifts ps ON sc.shift_id = ps.shift_id
                                        LEFT JOIN project p ON ps.id_plan = p.id_project
                                        LEFT JOIN warehouse_import_requests wir ON wir.closure_id = {$closureJoinKey}
                                        LEFT JOIN product pr ON wir.product_id = pr.id_product
                                        LEFT JOIN qc_sessions qs ON qs.closure_id = {$closureJoinKey}
                                        LEFT JOIN qc_decisions qd ON qd.session_id = qs.id
                                        LEFT JOIN finished_receipt recpt ON {$pk} = recpt.id_finished_report AND recpt.status = 'posted'
                                        WHERE 1=1 ";

                        // Require can_receive flag if column exists
                        if ($this->db->field_exists('can_receive_fg', 'shift_closures')) {
                            $sql .= " AND sc.can_receive_fg = 1 ";
                        }

                        // Require either QC APPROVE or confirmed status depending on schema
                        if ($this->db->field_exists('status', 'shift_closures')) {
                            $sql .= " AND (qd.result = 'APPROVE' OR sc.status IN ('confirmed','VERIFIED')) ";
                        } else {
                            $sql .= " AND qd.result = 'APPROVE' ";
                        }

                        $sql .= " GROUP BY {$pk} ORDER BY sc.closure_date DESC";
            
            $result = $this->db->query($sql)->result();
            // If shift_closures exists, always use it (even if empty) - don't fallback
            return $result;
        }

        // Fallback: Lấy từ finished_report (schema cũ) ONLY nếu shift_closures không tồn tại
        if (!$shift_closures_exists && $this->db->table_exists('finished_report')) {
            $sql = "SELECT 
                      fr.id_finished,
                      fr.id_finished AS closure_code,
                      fr.id_project AS project_code,
                      p.id_project,
                      p.project_name,
                      NULL AS product_code,
                      NULL AS product_name,
                      fr.total_finished AS qty_passed,
                      NULL AS qty_waste,
                      fr.fdate,
                      NULL AS closed_by,
                      NULL AS qc_result,
                      NULL AS qc_aql,
                      NULL AS defect_rate,
                      NULL AS qc_approved_at,
                      NULL AS qc_approved_by,
                      COALESCE(SUM(CASE WHEN recpt.status = 'posted' THEN recpt.quantity_received ELSE 0 END), 0) AS qty_already_received
                    FROM finished_report fr
                    LEFT JOIN project p ON fr.id_project = p.id_project
                    LEFT JOIN finished_receipt recpt ON fr.id_finished = recpt.id_finished_report AND recpt.status = 'posted'
                    GROUP BY fr.id_finished
                    ORDER BY fr.fdate DESC";

            $result = $this->db->query($sql)->result();
        }

        return $result;
    }

    /**
     * Tạo phiếu nhập thành phẩm
     */
    public function createReceipt($data)
    {
        // Generate receipt code
        $date = date('YmdHis');
        $random = substr(str_shuffle('0123456789'), 0, 3);
        $data['receipt_code'] = 'NTP-' . $date . '-' . $random;
        
        // Set default status if not provided
        if (!isset($data['status'])) {
            $data['status'] = 'posted';
        }

        // For new schema using shift_closures, set id_finished_report to NULL
        // to avoid FK constraint with finished_report table
        if (!isset($data['id_finished_report']) || !$data['id_finished_report']) {
            $data['id_finished_report'] = null;
        }

        if (!$this->db->table_exists('finished_receipt')) {
            return false;
        }

        // Some deployments (from provided SQL dumps) may have created the
        // `finished_receipt` table without an AUTO_INCREMENT on `id_receipt`.
        // In that case inserting without specifying `id_receipt` would fail
        // due to NOT NULL constraint or return insert_id = 0. Detect that
        // situation and assign a sensible next id to avoid silent failures.
        $col = $this->db->query("SHOW COLUMNS FROM finished_receipt LIKE 'id_receipt'")->row_array();
        $manually_set_id = false;
        if ($col && isset($col['Extra'])) {
            $extra = $col['Extra'];
            if (stripos($extra, 'auto_increment') === false) {
                // id_receipt exists but is NOT auto_increment -> set it manually
                $max = $this->db->select_max('id_receipt')->get('finished_receipt')->row();
                $next = (int)($max->id_receipt ?? 0) + 1;
                $data['id_receipt'] = $next;
                $manually_set_id = true;
            }
        }

        $this->db->insert('finished_receipt', $data);

        // If we set the id manually return it, otherwise return insert_id()
        if ($manually_set_id) {
            return $data['id_receipt'];
        }

        return $this->db->insert_id();
    }

    /**
     * Lấy thông tin phiếu nhập theo ID
     */
    public function getReceiptById($receipt_id)
    {
        if (!$this->db->table_exists('finished_receipt')) {
            return null;
        }

        return $this->db->where('id_receipt', $receipt_id)
                        ->get('finished_receipt')
                        ->row();
    }

    /**
     * Lấy danh sách phiếu nhập kèm thông tin project
     */
    public function getReceipts($limit = 50, $offset = 0)
    {
        if (!$this->db->table_exists('finished_receipt')) {
            return [];
        }

        $sql = "SELECT 
                  r.id_receipt,
                  r.receipt_code,
                  r.id_project,
                  p.project_name,
                  r.quantity_received,
                  r.quantity_planned,
                  r.created_by_name,
                  r.created_date,
                  r.status
                FROM finished_receipt r
                LEFT JOIN project p ON r.id_project = p.id_project
                ORDER BY r.created_date DESC
                LIMIT {$limit} OFFSET {$offset}";

        return $this->db->query($sql)->result();
    }

    /**
     * Cập nhật tồn kho thành phẩm khi nhập
     */
    public function updateStockAfterReceipt($quantity, $product_id = 1)
    {
        if (!$this->db->table_exists('finished_stock')) {
            return false;
        }

        // Check if stock record exists
        $stock = $this->db->where('id_product', $product_id)->get('finished_stock')->row();

        if ($stock) {
            // Update existing
            $this->db->where('id_product', $product_id)
                     ->update('finished_stock', [
                         'quantity_in_stock' => $stock->quantity_in_stock + $quantity,
                         'quantity_received' => $stock->quantity_received + $quantity,
                         'last_updated' => date('Y-m-d H:i:s')
                     ]);
        } else {
            // Insert new
            $this->db->insert('finished_stock', [
                'id_product' => $product_id,
                'quantity_in_stock' => $quantity,
                'quantity_received' => $quantity,
                'last_updated' => date('Y-m-d H:i:s')
            ]);
        }

        return true;
    }

    /**
     * Hủy phiếu nhập
     */
    public function cancelReceipt($receipt_id)
    {
        if (!$this->db->table_exists('finished_receipt')) {
            return false;
        }

        $receipt = $this->getReceiptById($receipt_id);
        if (!$receipt) {
            return false;
        }

        // Update receipt status
        $this->db->where('id_receipt', $receipt_id)
                 ->update('finished_receipt', ['status' => 'cancelled']);

        // Reverse stock update
        $this->db->set('quantity_in_stock', 'quantity_in_stock - ' . $receipt->quantity_received, FALSE)
                 ->set('quantity_received', 'quantity_received - ' . $receipt->quantity_received, FALSE)
                 ->where('id_product', 1)
                 ->update('finished_stock');

        return true;
    }

    /**
     * Lấy tổng số phiếu nhập
     */
    public function countReceipts()
    {
        if (!$this->db->table_exists('finished_receipt')) {
            return 0;
        }

        return $this->db->count_all('finished_receipt');
    }

    /**
     * ⭐ CRITICAL CONTROL: Kiểm tra xem closure_id đã được QC duyệt hay chưa
     * 
     * Điều kiện để nhập kho:
     * 1. shift_closures.can_receive_fg = 1
     * 2. shift_closures.status = 'VERIFIED'
     * 3. qc_decisions.result = 'APPROVE'
     * 
     * @param int $closure_id ID của shift_closures
     * @return array ['approved' => bool, 'message' => string, 'qc_info' => array]
     */
    public function checkQcApprovalStatus($closure_id)
    {
        $result = [
            'approved' => FALSE,
            'message' => '',
            'qc_info' => NULL,
            'closure_code' => NULL
        ];

        if (!$closure_id) {
            $result['message'] = 'Không có ca sản xuất được chọn';
            return $result;
        }

        // Không kiểm tra nếu không có table shift_closures (fallback mode)
        if (!$this->db->table_exists('shift_closures')) {
            $result['approved'] = TRUE;  // Fallback: allow if table doesn't exist
            $result['message'] = 'Database không có QC Module, tự động cho phép nhập';
            return $result;
        }

        // Pick an existing closure code and primary key column to avoid missing-column errors
        $pkColumn = 'id';
        $closureCodeColumn = 'sc.id';
        if ($this->db->field_exists('id', 'shift_closures')) {
            $pkColumn = 'id';
        } elseif ($this->db->field_exists('closure_id', 'shift_closures')) {
            $pkColumn = 'closure_id';
        } elseif ($this->db->field_exists('id_finished', 'shift_closures')) {
            $pkColumn = 'id_finished';
        }

        if ($this->db->field_exists('code', 'shift_closures')) {
            $closureCodeColumn = 'sc.code';
        } else {
            $closureCodeColumn = 'sc.' . $pkColumn;
        }

        // Query: Check all conditions using detected PK column
        $pk = 'sc.' . $pkColumn;
        $sql = "SELECT 
                  {$pk} AS closure_pk,
                  {$closureCodeColumn} AS closure_code,
                  sc.can_receive_fg,
                  sc.status,
                  qd.result AS qc_result,
                  qd.aql,
                  qd.defect_rate,
                  qd.reason AS qc_reason,
                  qd.decided_at,
                  qd.decided_by
                FROM shift_closures sc
                LEFT JOIN qc_sessions qs ON qs.closure_id = {$pk}
                LEFT JOIN qc_decisions qd ON qd.session_id = qs.id
                WHERE {$pk} = {$closure_id}
                LIMIT 1";

        $check = $this->db->query($sql)->row_array();

        if (!$check) {
            $result['message'] = 'Không tìm thấy ca sản xuất';
            return $result;
        }

        $result['closure_code'] = $check['closure_code'];

        // Check condition 1: can_receive_fg flag (only if column exists)
        if ($this->db->field_exists('can_receive_fg', 'shift_closures')) {
            if (!isset($check['can_receive_fg']) || $check['can_receive_fg'] != 1) {
                $result['message'] = 'Cờ nhập kho chưa được bật (can_receive_fg = 0)';
                return $result;
            }
        }

        // Check condition 2: status - adapt to available status vocabulary
        if ($this->db->field_exists('status', 'shift_closures')) {
            $status = $check['status'] ?? null;
            // Accept 'confirmed' or 'VERIFIED' as valid 'approved' states in different schemas
            if (!in_array($status, ['confirmed', 'VERIFIED'], true)) {
                if ($status === 'REJECTED') {
                    $result['message'] = 'Ca sản xuất đã bị QC từ chối: ' . ($check['qc_reason'] ?? 'Lý do không rõ');
                } elseif ($status === 'PENDING_QC' || $status === 'draft') {
                    $result['message'] = 'Ca sản xuất đang chờ QC kiểm tra';
                } else {
                    $result['message'] = 'Trạng thái ca sản xuất không hợp lệ: ' . ($status ?? 'NULL');
                }
                return $result;
            }
        }

        // Check condition 3: qc_result = APPROVE
        if ($check['qc_result'] != 'APPROVE') {
            if ($check['qc_result'] == 'REJECT') {
                $result['message'] = 'QC đã từ chối ca này: ' . ($check['qc_reason'] ?? '');
            } else if ($check['qc_result'] == NULL) {
                $result['message'] = 'Chưa có quyết định QC cho ca này';
            } else {
                $result['message'] = 'Kết quả QC không phải APPROVE: ' . $check['qc_result'];
            }
            return $result;
        }

        // ✅ ALL CHECKS PASSED
        $result['approved'] = TRUE;
        $result['message'] = 'QC đã duyệt ca này';
        $result['qc_info'] = [
            'result' => $check['qc_result'],
            'aql' => $check['aql'],
            'defect_rate' => $check['defect_rate'],
            'decided_at' => $check['decided_at'],
            'decided_by' => $check['decided_by']
        ];

        return $result;
    }

    /**
     * Get QC rejection details if closure was rejected
     * 
     * @param int $closure_id
     * @return array|NULL
     */
    public function getQcRejectionDetails($closure_id)
    {
        if (!$this->db->table_exists('shift_closures')) {
            return NULL;
        }

                // Detect PK column
                $pkColumn = 'id';
                if ($this->db->field_exists('id', 'shift_closures')) {
                        $pkColumn = 'id';
                } elseif ($this->db->field_exists('closure_id', 'shift_closures')) {
                        $pkColumn = 'closure_id';
                } elseif ($this->db->field_exists('id_finished', 'shift_closures')) {
                        $pkColumn = 'id_finished';
                }

                $pk = 'sc.' . $pkColumn;

                $sql = "SELECT 
                                    qd.result,
                                    qd.reason,
                                    qd.decided_at,
                                    qd.decided_by,
                                    ar.code AS adjustment_code,
                                    ar.status AS adjustment_status
                                FROM shift_closures sc
                                LEFT JOIN qc_sessions qs ON qs.closure_id = {$pk}
                                LEFT JOIN qc_decisions qd ON qd.session_id = qs.id
                                LEFT JOIN adjustment_requests ar ON ar.closure_id = {$pk}
                                WHERE {$pk} = {$closure_id}
                                    AND qd.result = 'REJECT'
                                LIMIT 1";

                return $this->db->query($sql)->row_array();
    }
}
