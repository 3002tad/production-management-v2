<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class FinishedReceiptModel extends CI_Model {

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Lấy danh sách ca/lô đạt QC (chưa nhập kho)
     * Query từ QC Module: shift_closures + qc_decisions
     */
    public function getQcPassedBatches()
    {
        // Thử lấy từ QC Module trước (mới)
        if ($this->db->table_exists('shift_closures') && $this->db->table_exists('qc_decisions')) {
            $sql = "SELECT 
                      sc.id AS id_finished,
                      sc.project_code AS id_project,
                      p.project_name,
                      sc.qty_finished AS qty_passed,
                      sc.closed_at AS fdate,
                      COALESCE(SUM(CASE WHEN recpt.status = 'posted' THEN recpt.quantity_received ELSE 0 END), 0) AS qty_already_received
                    FROM shift_closures sc
                    INNER JOIN qc_decisions qd ON sc.id = qd.session_id
                    LEFT JOIN project p ON sc.project_code = p.id_project
                    LEFT JOIN finished_receipt recpt ON sc.id = recpt.id_finished_report AND recpt.status = 'posted'
                    WHERE qd.result = 'APPROVE' 
                      AND sc.can_receive_fg = 1
                      AND sc.status = 'VERIFIED'
                    GROUP BY sc.id
                    ORDER BY sc.closed_at DESC";
            
            $result = $this->db->query($sql)->result();
            if (!empty($result)) {
                return $result;
            }
        }

        // Fallback: Lấy từ finished_report (schema cũ)
        if ($this->db->table_exists('finished_report')) {
            $sql = "SELECT 
                      fr.id_finished,
                      fr.id_project,
                      p.project_name,
                      fr.total_finished AS qty_passed,
                      fr.fdate,
                      COALESCE(SUM(CASE WHEN recpt.status = 'posted' THEN recpt.quantity_received ELSE 0 END), 0) AS qty_already_received
                    FROM finished_report fr
                    LEFT JOIN project p ON fr.id_project = p.id_project
                    LEFT JOIN finished_receipt recpt ON fr.id_finished = recpt.id_finished_report AND recpt.status = 'posted'
                    GROUP BY fr.id_finished
                    ORDER BY fr.fdate DESC";

            return $this->db->query($sql)->result();
        }

        return [];
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

        if (!$this->db->table_exists('finished_receipt')) {
            return false;
        }

        $this->db->insert('finished_receipt', $data);
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
}
