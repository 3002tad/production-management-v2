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
        
        // Allow AJAX requests to get_project_info without strict role checking
        $current_method = $this->router->method;
        $is_ajax_info_request = $current_method === 'get_project_info';
        
        if (!$is_ajax_info_request && !($role_name === 'warehouse_staff' || $role_id == 3)) {
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
        $data = [
            'navlink' => 'finished',
            'content' => 'warehouse/finished/dashboard'
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

        // Load projects list for dropdown with stats
        $projects = [];
        if ($this->db->table_exists('project')) {
            $this->db->select('p.id_project, p.project_name, p.qty_request as qty_target');
            $this->db->select('COALESCE((SELECT SUM(quantity_received) FROM finished_receipt WHERE id_project = p.id_project AND status = "posted"), 0) as qty_received');
            $this->db->from('project p');
            // Chỉ lấy các dự án chưa nhập đủ số lượng
            $this->db->having('qty_received < qty_target');
            $projects = $this->db->get()->result();
        }

        $data = [
            'navlink' => 'finished_receipt',
            'content' => 'warehouse/finished/receipt_form',
            'batches' => $batches,
            'projects' => $projects
        ];

        $this->load->view('warehouse/vbackend', $data);
    }

    /**
     * get_project_info - Get project info via AJAX
     */
    public function get_project_info($id_project = null)
    {
        header('Content-Type: application/json');
        
        if (!$id_project) {
            echo json_encode(['success' => false, 'message' => 'Project ID required', 'qty_target' => 0, 'qty_received' => 0]);
            return;
        }

        try {
            // Get project basic info
            $project = $this->db->where('id_project', $id_project)
                               ->get('project')
                               ->row();
            
            if (!$project) {
                echo json_encode(['success' => false, 'message' => 'Project not found', 'qty_target' => 0, 'qty_received' => 0]);
                return;
            }

            // Get total received quantity for this project
            $qty_received = 0;
            $received_row = $this->db->select('SUM(quantity_received) as total_received')
                                    ->where('id_project', $id_project)
                                    ->where('status', 'posted')
                                    ->get('finished_receipt')
                                    ->row();
            
            if ($received_row && !is_null($received_row->total_received)) {
                $qty_received = (int)$received_row->total_received;
            }
            
            echo json_encode([
                'success' => true,
                'qty_target' => isset($project->qty_request) ? (int)$project->qty_request : 0,
                'qty_received' => $qty_received,
                'project_name' => isset($project->project_name) ? $project->project_name : 'Unknown'
            ]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage(), 'qty_target' => 0, 'qty_received' => 0]);
        }
    }

    /**
     * receipt_save - Xử lý lưu phiếu nhập
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

        // Try to fetch from QC Module (shift_closures) first
        $batch = null;
        $quantity_planned = 0;
        $id_project = null;
        $data_source = null;
        
        if ($this->db->table_exists('shift_closures')) {
            $batch = $this->db->where('closure_id', $id_finished_report)
                              ->get('shift_closures')
                              ->row();
            
            if ($batch) {
                // For shift_closures, use total_good
                $quantity_planned = $batch->total_good;
                
                // Try to get project from planning if warehouse_request_id exists
                if (!empty($batch->warehouse_request_id)) {
                    $proj = $this->db->select('id_project')->get_where('planning', ['id_plan' => $batch->warehouse_request_id])->row();
                    if ($proj) {
                        $id_project = $proj->id_project;
                    }
                }
                $data_source = 'shift_closures';
            }
        }

        // Fallback to finished_report if not found in shift_closures
        if (!$batch && $this->db->table_exists('finished_report')) {
            $batch = $this->db->where('id_finished', $id_finished_report)
                              ->get('finished_report')
                              ->row();
            
            if ($batch) {
                $quantity_planned = $batch->total_finished;
                $id_project = $batch->id_project;
                $data_source = 'finished_report';
            }
        }

        if (!$batch) {
            $this->session->set_flashdata('error', 'Ca/lô không tồn tại (ID: ' . $id_finished_report . ')');
            redirect('warehouse/finished/receipt_form');
        }

        // Override id_project from form if provided
        $form_project_id = $this->input->post('id_project');
        if ($form_project_id) {
            $id_project = $form_project_id;
        }

        // Validate id_project was retrieved successfully
        if (!$id_project) {
            $this->session->set_flashdata('error', 'Không tìm thấy dự án liên kết với ca/lô. Vui lòng chọn dự án.');
            redirect('warehouse/finished/receipt_form');
        }

        $receipt_data = [
            'id_project' => $id_project,
            'id_finished_report' => $id_finished_report,
            'quantity_received' => $quantity_received,
            'quantity_planned' => $quantity_planned,
            'created_by' => $this->session->userdata('user_id'),
            'created_by_name' => $this->session->userdata('username'),
            'notes' => $notes,
            'created_date' => date('Y-m-d H:i:s'),
            'status' => 'posted'
        ];

        $receipt_id = $this->FinishedReceiptModel->createReceipt($receipt_data);

        if ($receipt_id) {
            // Get product ID from project
            $id_product = 1; // Default product
            if ($this->db->table_exists('project')) {
                $proj = $this->db->select('id_product')
                                ->where('id_project', $id_project)
                                ->get('project')
                                ->row();
                if ($proj && $proj->id_product) {
                    $id_product = $proj->id_product;
                }
            }
            
            // Update stock after receipt
            $this->FinishedReceiptModel->updateStockAfterReceipt($quantity_received, $id_product);
            $this->session->set_flashdata('success', 'Nhập thành công - Phiếu #' . $receipt_id);
            redirect('warehouse/finished/receipt_view/' . $receipt_id);
        } else {
            $db_error = $this->db->error();
            $error_msg = !empty($db_error['message']) ? $db_error['message'] : 'Không thể lưu phiếu vào cơ sở dữ liệu';
            $this->session->set_flashdata('error', $error_msg);
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
            $this->FinishedIssueModel->updateStockAfterIssue(min($quantity_issued, $current_stock), 1);
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
