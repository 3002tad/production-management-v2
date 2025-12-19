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
        $batch = $this->db->where('id', $id_finished_report)
                          ->get('shift_closures')
                          ->row();

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

        // Check if quantity exceeds available
        $qty_available = isset($batch->qty_finished) ? $batch->qty_finished : $batch->total_finished;
        if ($quantity_received > $qty_available) {
            $this->session->set_flashdata('error', 
                'Số lượng nhập (' . $quantity_received . ') vượt quá số lượng hoàn thành (' . $qty_available . ')'
            );
            redirect('warehouse/finished/receipt_form');
        }

        // ✅ ALL CHECKS PASSED - Proceed with receipt creation
        // Get id_project from batch (project_code is now integer ID)
        $id_project = $batch->project_code;
        
        if (!$id_project) {
            $this->session->set_flashdata('error', 'Dự án không tồn tại');
            redirect('warehouse/finished/receipt_form');
        }

        // Note: id_finished_report should reference finished_report.id_finished (old schema)
        // For new schema using shift_closures, we set it to NULL to avoid FK constraint
        // The actual shift_closures.id is tracked via application logic
        $id_finished_report_fk = null;

        $receipt_data = [
            'id_project' => $id_project,
            'id_finished_report' => $id_finished_report_fk,  // NULL to avoid FK constraint with finished_report
            'quantity_received' => $quantity_received,
            'quantity_planned' => $qty_available,
            'created_by' => $this->session->userdata('user_id'),
            'created_by_name' => $this->session->userdata('username'),
            'notes' => $notes,
            // Mark as QC verified
            'qc_verified' => 1,
            'qc_approved_at' => date('Y-m-d H:i:s'),
            'qc_approved_by' => $this->session->userdata('user_code') ?? $this->session->userdata('username'),
            'requires_qc_approval' => 1
        ];

        $receipt_id = $this->FinishedReceiptModel->createReceipt($receipt_data);

        if ($receipt_id) {
            $this->FinishedReceiptModel->updateStockAfterReceipt($quantity_received, 1);
            
            // Log audit trail
            log_message('info', "Finished Receipt Created: ID={$receipt_id}, Closure={$id_finished_report}, Qty={$quantity_received}, User=" . $this->session->userdata('username'));
            
            $this->session->set_flashdata('success', '✅ Nhập kho thành công - Phiếu #' . $receipt_id . ' | QC Verified');
            redirect('warehouse/finished/receipt_view/' . $receipt_id);
        } else {
            $this->session->set_flashdata('error', 'Lỗi: Không thể lưu phiếu');
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
