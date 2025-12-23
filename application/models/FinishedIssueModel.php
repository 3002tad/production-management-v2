<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class FinishedIssueModel extends CI_Model {

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Lấy danh sách project cần giao hàng (với tồn kho hiện tại)
     */
    public function getProjectsForDelivery()
    {
        if (!$this->db->table_exists('project')) {
            return [];
        }

        $sql = "SELECT 
                  p.id_project,
                  p.project_name,
                  p.qty_request,
                  COALESCE(fs.quantity_in_stock, 0) AS qty_available,
                  COALESCE(SUM(CASE WHEN iss.status IN ('full', 'partial') THEN iss.quantity_issued ELSE 0 END), 0) AS qty_already_issued,
                  (p.qty_request - COALESCE(SUM(CASE WHEN iss.status IN ('full', 'partial') THEN iss.quantity_issued ELSE 0 END), 0)) AS qty_remaining
                FROM project p
                LEFT JOIN finished_stock fs ON fs.id_product = 1
                LEFT JOIN finished_issue iss ON p.id_project = iss.id_project
                WHERE p.pr_status != 'completed'
                GROUP BY p.id_project
                ORDER BY p.id_project DESC";

        return $this->db->query($sql)->result();
    }

    /**
     * Lấy thông tin tồn kho hiện tại
     */
    public function getCurrentStock($product_id = 1)
    {
        if (!$this->db->table_exists('finished_stock')) {
            return 0;
        }

        $stock = $this->db->where('id_product', $product_id)
                          ->get('finished_stock')
                          ->row();

        return $stock ? $stock->quantity_in_stock : 0;
    }

    /**
     * Tạo phiếu xuất giao hàng
     */
    public function createIssue($data)
    {
        // Generate issue code
        $date = date('YmdHis');
        $random = substr(str_shuffle('0123456789'), 0, 3);
        $data['issue_code'] = 'XTP-' . $date . '-' . $random;

        if (!$this->db->table_exists('finished_issue')) {
            return false;
        }

        $this->db->insert('finished_issue', $data);
        return $this->db->insert_id();
    }

    /**
     * Lấy thông tin phiếu xuất theo ID
     */
    public function getIssueById($issue_id)
    {
        if (!$this->db->table_exists('finished_issue')) {
            return null;
        }

        return $this->db->where('id_issue', $issue_id)
                        ->get('finished_issue')
                        ->row();
    }

    /**
     * Lấy danh sách phiếu xuất kèm thông tin project
     */
    public function getIssues($limit = 50, $offset = 0)
    {
        if (!$this->db->table_exists('finished_issue')) {
            return [];
        }

        $sql = "SELECT 
                  iss.id_issue,
                  iss.issue_code,
                  iss.id_project,
                  p.project_name,
                  iss.quantity_requested,
                  iss.quantity_issued,
                  iss.created_by_name,
                  iss.created_date,
                  iss.status
                FROM finished_issue iss
                LEFT JOIN project p ON iss.id_project = p.id_project
                ORDER BY iss.created_date DESC
                LIMIT {$limit} OFFSET {$offset}";

        return $this->db->query($sql)->result();
    }

    /**
     * Cập nhật tồn kho thành phẩm khi xuất
     */
    public function updateStockAfterIssue($quantity, $product_id = 1)
    {
        if (!$this->db->table_exists('finished_stock')) {
            return false;
        }

        // Get current stock
        $stock = $this->db->where('id_product', $product_id)
                          ->get('finished_stock')
                          ->row();

        if ($stock && $stock->quantity_in_stock >= $quantity) {
            // Update stock
            $this->db->where('id_product', $product_id)
                     ->update('finished_stock', [
                         'quantity_in_stock' => $stock->quantity_in_stock - $quantity,
                         'quantity_issued' => $stock->quantity_issued + $quantity,
                         'last_updated' => date('Y-m-d H:i:s')
                     ]);
            return true;
        }

        return false;
    }

    /**
     * Kiểm tra tồn kho có đủ không
     */
    public function hasEnoughStock($quantity, $product_id = 1)
    {
        $current = $this->getCurrentStock($product_id);
        return $current >= $quantity;
    }

    /**
     * Hủy phiếu xuất
     */
    public function cancelIssue($issue_id)
    {
        if (!$this->db->table_exists('finished_issue')) {
            return false;
        }

        $issue = $this->getIssueById($issue_id);
        if (!$issue) {
            return false;
        }

        $qty = (int)($issue->quantity_issued ?? 0);

        // Determine product id to reverse stock for (use project's id_product if available)
        $product_id = 1;
        if (!empty($issue->id_project)) {
            $proj = $this->db->select('id_product')->get_where('project', ['id_project' => $issue->id_project])->row();
            if ($proj && !empty($proj->id_product)) {
                $product_id = (int)$proj->id_product;
            }
        }

        // Begin transaction to ensure atomic update
        $this->db->trans_start();

        // Update issue status
        $this->db->where('id_issue', $issue_id)
                 ->update('finished_issue', ['status' => 'cancelled']);

        // Reverse stock update only when there is a positive quantity and the stock table exists
        if ($qty > 0 && $this->db->table_exists('finished_stock')) {
            // Use GREATEST to avoid negative quantity_issued values
            $this->db->set('quantity_in_stock', 'quantity_in_stock + ' . $qty, FALSE)
                     ->set('quantity_issued', 'GREATEST(quantity_issued - ' . $qty . ', 0)', FALSE)
                     ->where('id_product', $product_id)
                     ->update('finished_stock');
        }

        $this->db->trans_complete();

        return $this->db->trans_status();
    }

    /**
     * Lấy tổng số phiếu xuất
     */
    public function countIssues()
    {
        if (!$this->db->table_exists('finished_issue')) {
            return 0;
        }

        return $this->db->count_all('finished_issue');
    }

    /**
     * Lấy tổng tồn kho thành phẩm hiện tại
     */
    public function getTotalStock()
    {
        if (!$this->db->table_exists('finished_stock')) {
            return 0;
        }

        $result = $this->db->select_sum('quantity_in_stock')
                           ->get('finished_stock')
                           ->row();

        return $result && $result->quantity_in_stock ? (int)$result->quantity_in_stock : 0;
    }
}
