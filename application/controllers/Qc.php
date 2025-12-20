<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Qc Controller - Quality Control Module
 * 
 * Handles QC inspection workflow:
 * - View pending shift closures
 * - Create and manage QC sessions
 * - Input inspection results
 * - Make APPROVE/REJECT decisions
 * - Upload evidence attachments
 * 
 * @author Production Management System v2 Team
 * @date 2025-11-02
 */
class Qc extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        
        // Check authentication
        if (!$this->session->userdata('user_id')) {
            redirect('login/');
            exit();
        }
        
        // Check QC role permission - support new RBAC `role_name` and legacy `role`
        $role_name = strtolower(trim((string)$this->session->userdata('role_name')));
        $legacy_role = strtolower(trim((string)$this->session->userdata('role')));
        $role = $role_name ?: $legacy_role;

        // Allowed roles for QC module
        $allowed_roles = ['qc_staff', 'admin', 'bod'];

        if (!in_array($role, $allowed_roles, true)) {
            $this->session->set_flashdata('error', 'Bạn không có quyền truy cập module QC.');
            redirect('login/');
            exit();
        }
        
        // Load models and libraries
        $this->load->model('QcModel', 'qcModel');
        
        // Load ChecklistService library
        // Note: CodeIgniter auto-lowercases library names, use 'checklistservice' to access
        $this->load->library('ChecklistService');
        
        $this->load->library('upload');
    }
    
    // ========================================
    // MAIN VIEWS
    // ========================================
    
    /**
     * Dashboard / Pending closures list (Use Case Bước 1, 2)
     * GET /qc/
     * GET /qc/index
     */
    public function index()
    {
        // Use Case Bước 2: Lọc theo ca/line/dự án
        $filters = [
            'line_code' => $this->input->get('line_code'),
            'shift_code' => $this->input->get('shift_code'),
            'project_code' => $this->input->get('project_code'),
            'date_from' => $this->input->get('date_from'),
            'date_to' => $this->input->get('date_to')
        ];
        
        // Get pending closures
        $closures = $this->qcModel->getPendingClosures($filters);
        
        // Get today's statistics
        $stats = $this->qcModel->getTodayStatistics();
        
        // Get statistics
        $data = [
            'title' => 'Danh sách Pending-QC',
            'closures' => $closures,
            'stats' => $stats,
            'filters' => $filters,
            'user' => [
                'username' => $this->session->userdata('username'),
                'full_name' => $this->session->userdata('full_name'),
                'role_display_name' => $this->session->userdata('role_display_name')
            ]
        ];
        
        $this->load->view('qc/index', $data);
    }
    
    public function pending()
    {
        // Redirect to index for now
        redirect('qc/');
    }
    
    /**
     * List all QC sessions (My Sessions)
     * GET /qc/sessions
     */
    public function session_list()
    {
        $username = $this->session->userdata('username');
        
        // Get all sessions for current user
        $sessions = $this->qcModel->getSessionsByInspector($username);
        
        $data = [
            'title' => 'Phiên Kiểm Tra Của Tôi',
            'sessions' => $sessions,
            'user' => [
                'username' => $username,
                'full_name' => $this->session->userdata('full_name'),
                'role_display_name' => $this->session->userdata('role_display_name')
            ]
        ];
        
        $this->load->view('qc/session_list', $data);
    }
    
    /**
     * QC Reports
     * GET /qc/reports
     */
    public function reports()
    {
        // Get filter parameters
        $filters = [
            'line_code' => $this->input->get('line_code'),
            'shift_code' => $this->input->get('shift_code'),
            'project_code' => $this->input->get('project_code'),
            'result' => $this->input->get('result'), // APPROVE|REJECT
            'date_from' => $this->input->get('date_from'),
            'date_to' => $this->input->get('date_to')
        ];
        
        // Get decided sessions with statistics
        $sessions = $this->qcModel->getDecidedSessions($filters);
        
        // Calculate summary statistics
        $stats = [
            'total' => count($sessions),
            'approved' => 0,
            'rejected' => 0,
            'avg_defect_rate' => 0
        ];
        
        $total_defect_rate = 0;
        foreach ($sessions as $session) {
            if ($session->result == 'APPROVE') {
                $stats['approved']++;
            } elseif ($session->result == 'REJECT') {
                $stats['rejected']++;
            }
            $total_defect_rate += $session->defect_rate ?? 0;
        }
        
        if ($stats['total'] > 0) {
            $stats['avg_defect_rate'] = $total_defect_rate / $stats['total'];
        }
        
        $data = [
            'title' => 'Báo Cáo QC',
            'sessions' => $sessions,
            'filters' => $filters,
            'stats' => $stats,
            'user' => [
                'username' => $this->session->userdata('username'),
                'full_name' => $this->session->userdata('full_name'),
                'role_display_name' => $this->session->userdata('role_display_name')
            ]
        ];
        
        $this->load->view('qc/reports', $data);
    }
    
    /**
     * View QC session details
     * GET /qc/sessions/{id}
     */
    public function sessions($session_id = null)
    {
        if (!$session_id) {
            show_404();
            return;
        }
        
        // Get session with closure details
        $session = $this->qcModel->getSessionById($session_id);
        
        if (!$session) {
            show_404();
            return;
        }
        
        // Check permission
        $user_context = [
            'username' => $this->session->userdata('username'),
            'role_name' => $this->session->userdata('role_name'),
            'level' => $this->session->userdata('level'),
            'line_code' => null // TODO: Add line_code to user session if needed
        ];
        
        $permission = $this->checklistservice->checkPermission($session_id, $user_context);
        if (!$permission['allowed']) {
            $this->session->set_flashdata('error', $permission['reason']);
            redirect('qc/pending');
            return;
        }
        
        // Get checklist items (and use for sample_size/aql)
        $checklist = $this->checklistservice->getChecklist($session->product_code, $session->variant);
        
        // Debug: Log checklist fetch result
        error_log("DEBUG: getChecklist($session->product_code, $session->variant) returned " . count($checklist) . " items");
        
        // Debug: Nếu thiếu product_code hoặc variant hoặc checklist rỗng, hiển thị cảnh báo
        if (empty($session->product_code) || empty($session->variant)) {
            $debug_msg = 'Thiếu mã sản phẩm hoặc variant! product_code: ' . ($session->product_code ?? 'NULL') . ', variant: ' . ($session->variant ?? 'NULL');
            $this->session->set_flashdata('error', $debug_msg);
            error_log("DEBUG: " . $debug_msg);
        } else if (empty($checklist)) {
            $debug_msg = 'Không tìm thấy checklist cho product_code: ' . $session->product_code . ', variant: ' . $session->variant;
            $this->session->set_flashdata('error', $debug_msg);
            error_log("DEBUG: " . $debug_msg);
        }
        // Add sample_size and aql_threshold to session object from checklist
        if (!empty($checklist)) {
            // Use first checklist item's sample_size and aql as defaults
            $session->sample_size = $checklist[0]->sample_size ?? 0;
            $session->aql_threshold = $checklist[0]->aql ?? 2.5;
        } else {
            // Fallback defaults if no checklist found
            $session->sample_size = 0;
            $session->aql_threshold = 2.5;
        }
        
        // Get existing QC items
        $qc_items = $this->qcModel->getQcItemsBySessionId($session_id);
        
        // Map QC items by checklist_item_code for easy lookup
        $qc_items_map = [];
        foreach ($qc_items as $item) {
            $qc_items_map[$item->checklist_item_code] = $item;
        }
        
        // Get attachments
        $attachments = $this->qcModel->getAttachmentsBySessionId($session_id);
        
        // Get decision if exists
        $decision = $this->qcModel->getDecisionBySessionId($session_id);
        
        // Get checklist status
        $checklist_status = $this->qcModel->isChecklistComplete($session_id);
        
        // Get recommendation if session is still OPEN
        $recommendation = null;
        $near_threshold_warning = null;
        if ($session->status === 'OPEN' && !empty($qc_items)) {
            $recommendation = $this->checklistservice->calculateDecisionRecommendation($session_id);
            
            // Check near threshold (Alternative Flow 6.1)
            if ($recommendation['recommendation'] !== 'INCOMPLETE') {
                $total_defects = 0;
                foreach ($qc_items as $item) {
                    $total_defects += $item->defect_count ?? 0;
                }
                $defect_rate = $session->sample_size > 0 ? ($total_defects / $session->sample_size) * 100 : 0;
                $near_threshold = $this->qcModel->isNearThreshold($defect_rate, $session->aql_threshold);
                if ($near_threshold['near_threshold']) {
                    $near_threshold_warning = $near_threshold;
                }
            }
        }
        
        // Get closure details
        $closure = $this->qcModel->getClosureById($session->closure_id);
        
        // Get checklist status (if not already fetched above)
        $checklist_status = $this->qcModel->isChecklistComplete($session_id);
        
        // Combine checklist master with QC items for display
        $items = [];
        foreach ($checklist as $master_item) {
            $qc_item = $qc_items_map[$master_item->item_code] ?? null;
            $items[] = (object)[
                'id' => $qc_item->id ?? null,
                'item_code' => $master_item->item_code,  // Add item_code for form identification
                'criteria_name' => $master_item->criteria_name,
                'item_name' => $master_item->criteria_name,
                'description' => $master_item->description,
                'test_method' => $master_item->test_method,
                'result' => $qc_item->result ?? null,
                'defect_count' => $qc_item->defect_count ?? 0,
                'severity' => $qc_item->severity ?? null,
                'defect_details' => $qc_item->note ?? null  // Map DB 'note' to view 'defect_details'
            ];
        }
        
        // Auto-generate 5 default checklist items if none exist in master
        if (empty($items)) {
            $default_items = [
                ['code' => 'CHK_001', 'name' => 'Kiểm tra ngoại hình', 'desc' => 'Kiểm tra kích thước, hình dạng, màu sắc phù hợp tiêu chuẩn'],
                ['code' => 'CHK_002', 'name' => 'Kiểm tra chất lượng bề mặt', 'desc' => 'Kiểm tra không có vết xước, trầy xát, nứt nẻ'],
                ['code' => 'CHK_003', 'name' => 'Kiểm tra kích thước chính xác', 'desc' => 'Kiểm tra độ dày, chiều dài, chiều rộng đúng công thức'],
                ['code' => 'CHK_004', 'name' => 'Kiểm tra độ bền', 'desc' => 'Kiểm tra độ chắc chắn, không bị hỏng sau lực tác động'],
                ['code' => 'CHK_005', 'name' => 'Kiểm tra hoàn thiện', 'desc' => 'Kiểm tra sản phẩm đã được hoàn thiện và sạch sẽ']
            ];
            
            foreach ($default_items as $idx => $default) {
                // Check if we already have results for these default items in qc_items
                $qc_item = $qc_items_map[$default['code']] ?? null;
                
                $items[] = (object)[
                    'id' => $qc_item->id ?? null,
                    'item_code' => $default['code'],
                    'criteria_name' => $default['name'],
                    'item_name' => $default['name'],
                    'description' => $default['desc'],
                    'test_method' => 'Kiểm tra trực quan (Visual inspection)',
                    'result' => $qc_item->result ?? null,
                    'defect_count' => $qc_item->defect_count ?? 0,
                    'severity' => $qc_item->severity ?? null,
                    'defect_details' => $qc_item->note ?? null,
                    'is_default' => true  // Mark as auto-generated
                ];
            }
        }
        
        $data = [
            'title' => 'Kiểm tra QC: ' . $session->code,
            'session' => $session,
            'closure' => $session,  // closure data comes from session object (it has all joined fields)
            'items' => $items,
            'attachments' => $attachments,
            'decision' => $decision,
            'recommendation' => $recommendation,
            'near_threshold_warning' => $near_threshold_warning,
            'checklist_status' => $checklist_status,
            'user' => [
                'username' => $this->session->userdata('username'),
                'full_name' => $this->session->userdata('full_name'),
                'role_display_name' => $this->session->userdata('role_display_name')
            ]
        ];
        
        $this->load->view('qc/session_v2', $data);
    }
    
    // ========================================
    // API ENDPOINTS
    // ========================================
    
    /**
     * Create new QC session
     * POST /qc/sessions/create
     */
    public function createSession()
    {
        $closure_id = $this->input->post('closure_id');
        
        if (!$closure_id) {
            $this->jsonResponse(['error' => 'closure_id is required'], 400);
            return;
        }
        
        // Validate closure exists
        $closure = $this->qcModel->getClosureById($closure_id);
        
        if (!$closure) {
            $this->jsonResponse(['error' => 'Closure not found'], 404);
            return;
        }
        
        // Check if warehouse import request is PENDING_QC
        $import_request = $this->qcModel->getWarehouseImportRequest($closure_id);
        
        if (!$import_request || $import_request->status !== 'pending_qc') {
            $this->session->set_flashdata('error', 'Phiếu nhập kho này không ở trạng thái chờ QC');
            redirect('qc/');
            return;
        }
        
        // Check if session already exists for this closure
        $existing_session = $this->qcModel->getSessionByClosureId($closure_id);
        
        if ($existing_session) {
            // Redirect to existing session instead of creating new one
            $this->session->set_flashdata('info', 'Phiên kiểm tra đã tồn tại cho phiếu chốt ca này');
            redirect('qc/sessions/' . $existing_session->id);
            return;
        }
        
        // Create session
        $session_data = [
            'closure_id' => $closure_id,
            'inspector_code' => $this->session->userdata('username'),
            'inspector_name' => $this->session->userdata('full_name'),
            'started_at' => date('Y-m-d H:i:s'),
            'status' => 'OPEN'
        ];
        
        $session_id = $this->qcModel->createSession($session_data);
        
        if ($session_id) {
            // Redirect to session page
            redirect('qc/sessions/' . $session_id);
        } else {
            $this->session->set_flashdata('error', 'Lỗi khi tạo phiên kiểm tra');
            redirect('qc/');
        }
    }
    
    /**
     * Save QC items (bulk)
     * POST /qc/sessions/{id}/items
     */
    public function saveItems($session_id = null)
    {
        if (!$session_id) {
            $this->jsonResponse(['error' => 'session_id is required'], 400);
            return;
        }
        
        // Check session exists and is OPEN
        $session = $this->qcModel->getSessionById($session_id);
        
        if (!$session) {
            $this->jsonResponse(['error' => 'Session not found'], 404);
            return;
        }
        
        if ($session->status !== 'OPEN') {
            $this->jsonResponse([
                'error' => 'Session is already decided. Cannot modify items.',
                'code' => 'SESSION_LOCKED'
            ], 409);
            return;
        }
        
        // Get items from POST data (either JSON or form arrays)
        $items_json = $this->input->post('items');
        
        if ($items_json) {
            // JSON format (for API calls)
            $items = json_decode($items_json, true);
            
            if (!is_array($items)) {
                $this->jsonResponse(['error' => 'items must be an array'], 400);
                return;
            }
        } else {
            // Form data format (from web form)
            $results = $this->input->post('results');
            $defects = $this->input->post('defects');
            $severity = $this->input->post('severity');
            $notes = $this->input->post('notes');
            $item_names = $this->input->post('item_names');
            $descriptions = $this->input->post('descriptions');
            $test_methods = $this->input->post('test_methods');
            
            if (!$results || !is_array($results)) {
                $this->jsonResponse(['error' => 'items data is required'], 400);
                return;
            }
            
            // Convert form arrays to items array
            // Key is item_code, need to lookup existing qc_item id
            $items = [];
            foreach ($results as $item_code => $result) {
                // In the view, 'defects' input contains the text description (note)
                $item_note = isset($defects[$item_code]) ? $defects[$item_code] : null;
                
                $items[] = [
                    'checklist_item_code' => $item_code,  // Use item_code from checklist master
                    'result' => $result,
                    'defect_count' => 0, // Default to 0, can be enhanced later if needed
                    'severity' => $severity[$item_code] ?? null,
                    'note' => $item_note,  // Map 'defects' text to DB 'note'
                    'item_name' => $item_names[$item_code] ?? null,  // For auto-create functionality
                    'description' => $descriptions[$item_code] ?? null,  // For auto-create functionality
                    'test_method' => $test_methods[$item_code] ?? null  // For auto-create functionality
                ];
            }
        }
        
        // Validate and save items
        $result = $this->qcModel->saveQcItems($session_id, $items);
        
        if ($result) {
            // Check if AJAX request
            if ($this->input->is_ajax_request()) {
                $this->jsonResponse([
                    'success' => true,
                    'message' => 'QC items saved successfully',
                    'count' => count($items)
                ]);
            } else {
                // Regular form submit - redirect back to session page
                $this->session->set_flashdata('success', 'Đã lưu ' . count($items) . ' kết quả kiểm tra QC');
                redirect('qc/sessions/' . $session_id);
            }
        } else {
            if ($this->input->is_ajax_request()) {
                $this->jsonResponse(['error' => 'Failed to save QC items'], 500);
            } else {
                $this->session->set_flashdata('error', 'Lỗi khi lưu kết quả QC');
                redirect('qc/sessions/' . $session_id);
            }
        }
    }
    
    /**
     * Upload attachment (photo/video)
     * POST /qc/sessions/{id}/attachments
     */
    public function uploadAttachment($session_id = null)
    {
        if (!$session_id) {
            $this->jsonResponse(['error' => 'session_id is required'], 400);
            return;
        }
        
        // Check session exists
        $session = $this->qcModel->getSessionById($session_id);
        
        if (!$session) {
            $this->jsonResponse(['error' => 'Session not found'], 404);
            return;
        }
        
        if ($session->status !== 'OPEN') {
            $this->jsonResponse([
                'error' => 'Session is already decided. Cannot upload attachments.',
                'code' => 'SESSION_LOCKED'
            ], 409);
            return;
        }
        
        // Configure upload
        $upload_path = './uploads/qc/' . date('Y/m/');
        
        // Create directory if not exists
        if (!is_dir($upload_path)) {
            if (!mkdir($upload_path, 0755, true)) {
                $this->jsonResponse([
                    'error' => 'Cannot create upload directory: ' . $upload_path,
                    'debug' => [
                        'upload_path' => $upload_path,
                        'parent_exists' => is_dir('./uploads/'),
                        'parent_writable' => is_writable('./uploads/')
                    ]
                ], 500);
                return;
            }
        }
        
        // Check if directory is writable
        if (!is_writable($upload_path)) {
            $this->jsonResponse([
                'error' => 'Upload directory is not writable: ' . $upload_path,
                'debug' => [
                    'upload_path' => $upload_path,
                    'permissions' => substr(sprintf('%o', fileperms($upload_path)), -4)
                ]
            ], 500);
            return;
        }
        
        $config = [
            'upload_path' => $upload_path,
            'allowed_types' => 'jpg|jpeg|png|gif|mp4|mov|avi|pdf|doc|docx',
            'max_size' => 10240, // 10MB
            'encrypt_name' => true
        ];
        
        $this->upload->initialize($config);
        
        if (!$this->upload->do_upload('attachment')) {
            $upload_errors = $this->upload->display_errors('', '');
            
            if ($this->input->is_ajax_request()) {
                $this->jsonResponse(['error' => $upload_errors], 400);
                return;
            }

            // Check if no file was selected
            if (strpos($upload_errors, 'You did not select a file to upload') !== false) {
                $this->session->set_flashdata('upload_error', 'Vui lòng chọn file để tải lên.');
                redirect('qc/sessions/' . $session_id);
                return;
            }
            
            // Log detailed error for debugging
            log_message('error', 'QC Upload Error: ' . $upload_errors);
            log_message('error', 'Upload Path: ' . $upload_path);
            log_message('error', 'Path exists: ' . (is_dir($upload_path) ? 'YES' : 'NO'));
            log_message('error', 'Path writable: ' . (is_writable($upload_path) ? 'YES' : 'NO'));
            
            $this->session->set_flashdata('upload_error', 'Lỗi tải file: ' . $upload_errors);
            redirect('qc/sessions/' . $session_id);
            return;
        }
        
        $upload_data = $this->upload->data();
        
        // Save attachment record
        $attachment_data = [
            'session_id' => $session_id,
            'filename' => $upload_data['orig_name'],
            'path' => 'uploads/qc/' . date('Y/m/') . $upload_data['file_name'],
            'mime_type' => $upload_data['file_type'],
            'file_size' => $upload_data['file_size'] * 1024,
            'uploaded_by' => $this->session->userdata('username')
        ];
        
        $attachment_id = $this->qcModel->saveAttachment($attachment_data);
        
        if ($attachment_id) {
            // Check if AJAX request
            if ($this->input->is_ajax_request()) {
                $this->jsonResponse([
                    'success' => true,
                    'message' => 'Tải file thành công',
                    'attachment' => [
                        'id' => $attachment_id,
                        'filename' => $attachment_data['filename'],
                        'path' => site_url($attachment_data['path']),
                        'mime_type' => $attachment_data['mime_type']
                    ]
                ]);
                return;
            }

            // Set success message
            $this->session->set_flashdata('upload_success', 'Tải file thành công: ' . $attachment_data['filename']);
            
            // Redirect back to session detail
            redirect('qc/sessions/' . $session_id);
        } else {
            // Log database error
            $db_error = $this->db->error();
            log_message('error', 'QC Attachment DB Error: ' . json_encode($db_error));
            log_message('error', 'QC Attachment Data: ' . json_encode($attachment_data));
            
            // Delete uploaded file if DB insert fails
            @unlink($upload_path . $upload_data['file_name']);
            
            if ($this->input->is_ajax_request()) {
                $this->jsonResponse(['error' => 'Lỗi lưu thông tin file vào database'], 500);
                return;
            }

            // Set error message and redirect back
            $this->session->set_flashdata('upload_error', 'Lỗi lưu thông tin file: ' . json_encode($db_error));
            redirect('qc/sessions/' . $session_id);
        }
    }
    
    /**
     * Make decision (APPROVE/REJECT)
     * POST /qc/sessions/{id}/decision
     * 
     * Use Case 19 - Bước 7-8: Luồng quyết định
     * Alternative Flow 6.1: Near threshold → increase sample
     * Alternative Flow 8.1: Reject → require reason + attachment
     */
    public function makeDecision($session_id = null)
    {
        if (!$session_id) {
            $this->jsonResponse(['error' => 'session_id is required'], 400);
            return;
        }
        
        // Get POST data
        $result = $this->input->post('result'); // APPROVE|REJECT
        $reason = $this->input->post('reason');
        $aql = $this->input->post('aql');
        $force = $this->input->post('force') === 'true'; // Force decision despite warnings
        
        if (!$result || !in_array($result, ['APPROVE', 'REJECT'])) {
            $this->jsonResponse(['error' => 'result must be APPROVE or REJECT'], 400);
            return;
        }
        
        // Bước 1: Kiểm tra điều kiện tiên quyết cho quyết định
        $validation = $this->checklistservice->validateDecision($session_id, $result, $reason);
        
        if (!$validation['valid']) {
            $this->jsonResponse([
                'error' => 'Validation failed',
                'errors' => $validation['errors'],
                'code' => 'VALIDATION_FAILED'
            ], 400);
            return;
        }
        
        // Calculate defect rate
        $stats = $this->qcModel->calculateDefectRate($session_id);
        
        // Get AQL (use provided or default)
        if (!$aql) {
            $aql = $this->qcModel->getDefaultAql();
        }
        
        // Bước 2: Lấy gợi ý kết luận
        $recommendation = $this->checklistservice->calculateDecisionRecommendation($session_id, $aql);
        
        // Bước 3: Luồng thay thế 6.1 - Phát hiện gần ngưỡng
        if ($recommendation['action'] === 'INCREASE_SAMPLE_SIZE' && !$force) {
            // Return 409 with suggestion to increase sample
            $this->jsonResponse([
                'error' => 'Kết quả tiệm cận ngưỡng AQL',
                'code' => 'NEAR_THRESHOLD',
                'action' => 'INCREASE_SAMPLE_SIZE',
                'message' => $recommendation['analysis'],
                'defect_rate' => $recommendation['defect_rate'],
                'aql_threshold' => $recommendation['aql_threshold'],
                'suggestion' => 'Hệ thống khuyến nghị TĂNG CỠ MẪU kiểm tra thêm để đánh giá chính xác hơn.',
                'can_force' => true,
                'force_message' => 'Bạn có thể bỏ qua cảnh báo và tiếp tục quyết định bằng cách click "Xác nhận dù sao".'
            ], 409);
            return;
        }
        
        // Bước 4: Hiển thị cảnh báo nếu có (nhưng cho phép tiếp tục)
        $warnings = $validation['warnings'] ?? [];
        
        // Bước 5: Chuẩn bị dữ liệu quyết định
        $decision_data = [
            'aql' => $aql,
            'defect_rate' => $stats['defect_rate'],
            'reason' => $reason,
            'decided_by' => $this->session->userdata('username')
        ];
        
        // Bước 6: Xử lý quyết định (giao dịch)
        try {
            if ($result === 'APPROVE') {
                // Bước 8: Luồng APPROVE
                // - Update session status to DECIDED
                // - Update closure status to VERIFIED
                // - Set can_receive_fg = 1 (allow warehouse to receive)
                $success = $this->qcModel->processApproveDecision($session_id, $decision_data);
                
                $message = 'Lô hàng đã được PHÊ DUYỆT. Kho có thể nhận thành phẩm.';
            } else {
                // Bước 8.1: Luồng REJECT flow
                // - Update session status to DECIDED
                // - Update closure status to REJECTED
                // - Create adjustment request for Leader
                $success = $this->qcModel->processRejectDecision($session_id, $decision_data);
                
                $message = 'Lô hàng đã bị TỪ CHỐI. Yêu cầu điều chỉnh đã được gửi cho Leader.';
            }
            
            if ($success) {
                // Check if AJAX request
                if ($this->input->is_ajax_request()) {
                    // AJAX: Return JSON with redirect URL
                    $response = [
                        'success' => true,
                        'message' => $message,
                        'result' => $result,
                        'defect_rate' => $stats['defect_rate'],
                        'aql' => $aql,
                        'redirect' => base_url('qc/sessions/' . $session_id)
                    ];
                    
                    if (!empty($warnings)) {
                        $response['warnings'] = $warnings;
                    }
                    
                    $this->jsonResponse($response);
                } else {
                    // Regular form submit: Redirect with flash message
                    $this->session->set_flashdata('success', $message);
                    redirect('qc/sessions/' . $session_id);
                }
            } else {
                // Exception: Database error
                if ($this->input->is_ajax_request()) {
                    $this->jsonResponse([
                        'error' => 'Lỗi khi ghi dữ liệu. Trạng thái ca không thay đổi.',
                        'code' => 'DATABASE_ERROR',
                        'message' => 'Vui lòng thử lại hoặc liên hệ quản trị viên.'
                    ], 500);
                } else {
                    $this->session->set_flashdata('error', 'Lỗi khi ghi dữ liệu. Vui lòng thử lại.');
                    redirect('qc/sessions/' . $session_id);
                }
            }
        } catch (Exception $e) {
            // Exception: Connection or system error
            log_message('error', 'QC Decision Error: ' . $e->getMessage());
            
            if ($this->input->is_ajax_request()) {
                $this->jsonResponse([
                    'error' => 'Lỗi hệ thống. Trạng thái ca không thay đổi.',
                    'code' => 'SYSTEM_ERROR',
                    'message' => 'Vui lòng kiểm tra kết nối và thử lại.'
                ], 500);
            } else {
                $this->session->set_flashdata('error', 'Lỗi hệ thống. Vui lòng thử lại.');
                redirect('qc/sessions/' . $session_id);
            }
        }
    }
    
    /**
     * Get decision recommendation (preview)
     * GET /qc/sessions/{id}/recommendation
     */
    public function getRecommendation($session_id = null)
    {
        if (!$session_id) {
            $this->jsonResponse(['error' => 'session_id is required'], 400);
            return;
        }
        
        $recommendation = $this->checklistservice->calculateDecisionRecommendation($session_id);
        
        $this->jsonResponse($recommendation);
    }
    
    // ========================================
    // ADJUSTMENT REQUESTS
    // ========================================
    
    /**
     * View adjustment requests
     * GET /qc/adjustments
     */
    public function adjustments()
    {
        $status = $this->input->get('status');
        
        $data = [
            'title' => 'Adjustment Requests',
            'requests' => $this->qcModel->getAdjustmentRequests($status),
            'status_filter' => $status,
            'user' => [
                'username' => $this->session->userdata('username'),
                'full_name' => $this->session->userdata('full_name'),
                'role_display_name' => $this->session->userdata('role_display_name')
            ]
        ];
        
        $this->load->view('qc/adjustments', $data);
    }
    
    // ========================================
    // HELPERS
    // ========================================
    
    /**
     * Send JSON response
     * 
     * @param mixed $data
     * @param int $status_code
     */
    private function jsonResponse($data, $status_code = 200)
    {
        $this->output
            ->set_status_header($status_code)
            ->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode($data))
            ->_display();
        exit;
    }
}
