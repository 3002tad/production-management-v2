<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Warehouse extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('CrudModel', 'crudModel');
        $this->load->library('session');
        
        // Check if user is logged in
        if (!$this->session->userdata('user_id')) {
            redirect('login/');
        }
        
        // RBAC: Check if user has warehouse access
        // Allow: BOD, System Admin, Warehouse Staff
        $role_name = $this->session->userdata('role_name');
        $level = $this->session->userdata('level');
        
        $has_access = false;
        
        // New RBAC system
        if ($role_name) {
            $allowed_roles = ['bod', 'system_admin', 'warehouse_staff'];
            $has_access = in_array($role_name, $allowed_roles) || ($level >= 90);
        }
        
        if (!$has_access) {
            show_error('Access Denied - Warehouse Staff Only', 403, 'Forbidden');
        }
    }

    /**
     * Warehouse Dashboard
     */
    public function index()
    {
        $data = [
            // Dashboard statistics
            'total_materials' => $this->crudModel->getData('material')->num_rows(),
            'low_stock_materials' => $this->db->query('SELECT COUNT(*) as count FROM material WHERE stock < 100')->row()->count,
            
            // Recent activities (example)
            'recent_materials' => $this->crudModel->getData('material')->result(),
            
            'content' => 'warehouse/dashboard',
            'navlink' => 'dashboard',
        ];

        $this->load->view('warehouse/vbackend', $data);
    }

    /**
     * Material Management (Nguyên vật liệu)
     */
    public function material()
    {
        $data = [
            'materials' => $this->crudModel->getData('material')->result(),
            'content' => 'warehouse/material/index',
            'navlink' => 'material',
        ];

        $this->load->view('warehouse/vbackend', $data);
    }

    /**
     * Stock In (Nhập kho NVL)
     */
    public function stock_in()
    {
        $data = [
            'materials' => $this->crudModel->getData('material')->result(),
            'content' => 'warehouse/stock_in',
            'navlink' => 'stock_in',
        ];

        $this->load->view('warehouse/vbackend', $data);
    }

    /**
     * Stock Out (Xuất kho NVL)
     */
    public function stock_out()
    {
        $data = [
            'materials' => $this->crudModel->getData('material')->result(),
            'content' => 'warehouse/stock_out',
            'navlink' => 'stock_out',
        ];

        $this->load->view('warehouse/vbackend', $data);
    }

    /**
     * Finished Product Warehouse (Kho thành phẩm)
     */
    public function finished_product()
    {
        $data = [
            'products' => $this->crudModel->getData('product')->result(),
            'content' => 'warehouse/finished_product',
            'navlink' => 'finished_product',
        ];

        $this->load->view('warehouse/vbackend', $data);
    }

    /**
     * Stock Report (Báo cáo tồn kho)
     */
    public function report()
    {
        $data = [
            'materials' => $this->db->query('
                SELECT 
                    m.*,
                    CASE 
                        WHEN m.stock < 100 THEN "Low Stock"
                        WHEN m.stock < 500 THEN "Medium"
                        ELSE "Good"
                    END as stock_status
                FROM material m
                ORDER BY m.stock ASC
            ')->result(),
            
            'content' => 'warehouse/report',
            'navlink' => 'report',
        ];

        $this->load->view('warehouse/vbackend', $data);
    }
}
