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
 * @author AI Pair Programmer
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
        
        // Check QC role permission
        $role_name = $this->session->userdata('role_name');
        if ($role_name !== 'qc_staff') {
            // Allow higher level roles to access
            $level = $this->session->userdata('level');
            if ($level < 60) {
                $this->session->set_flashdata('error', 'Access denied. QC staff only.');
                redirect('login/');
                exit();
            }
        }
        
        $this->load->model('QcModel', 'qcModel');
        $this->load->library('ChecklistService', 'checklistService');
        $this->load->library('upload');
    }
    
    // ========================================
    // MAIN VIEWS
    // ========================================
    
    /**
     * Dashboard / Pending closures list
     * GET /qc/
     * GET /qc/pending
     */
    public function index()
    {
        $this->pending();
    }
    
    public function pending()
    {
        // Get filters from query params
        $filters = [
            'line_code' => $this->input->get('line_code'),
            'shift_code' => $this->input->get('shift_code'),
            'project_code' => $this->input->get('project_code'),
            'date_from' => $this->input->get('date_from'),
            'date_to' => $this->input->get('date_to')
        ];
        
        // Remove empty filters
        $filters = array_filter($filters);
        
        // Get pending closures
        $data = [
            'title' => 'Pending QC Inspections',
            'closures' => $this->qcModel->getPendingClosures($filters),
            'filters' => $filters,
            'user' => [
                'username' => $this->session->userdata('username'),
                'full_name' => $this->session->userdata('full_name'),
                'role_display_name' => $this->session->userdata('role_display_name')
            ]
        ];
        
        $this->load->view('qc/pending', $data);
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
        
        $permission = $this->checklistService->checkPermission($session_id, $user_context);
        if (!$permission['allowed']) {
            $this->session->set_flashdata('error', $permission['reason']);
            redirect('qc/pending');
            return;
        }
        
        // Get checklist items
        $checklist = $this->checklistService->getChecklist($session->product_code, $session->variant);
        
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
        
        // Get recommendation if session is still OPEN
        $recommendation = null;
        if ($session->status === 'OPEN' && !empty($qc_items)) {
            $recommendation = $this->checklistService->calculateDecisionRecommendation($session_id);
        }
        
        $data = [
            'title' => 'QC Session: ' . $session->code,
            'session' => $session,
            'checklist' => $checklist,
            'qc_items' => $qc_items_map,
            'attachments' => $attachments,
            'decision' => $decision,
            'recommendation' => $recommendation,
            'user' => [
                'username' => $this->session->userdata('username'),
                'full_name' => $this->session->userdata('full_name'),
                'role_display_name' => $this->session->userdata('role_display_name')
            ]
        ];
        
        $this->load->view('qc/session', $data);
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
        
        // Validate closure exists and is PENDING_QC
        $closure = $this->qcModel->getClosureById($closure_id);
        
        if (!$closure) {
            $this->jsonResponse(['error' => 'Closure not found'], 404);
            return;
        }
        
        if ($closure->status !== 'PENDING_QC') {
            $this->jsonResponse(['error' => 'Closure is not in PENDING_QC status'], 400);
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
            $this->jsonResponse(['error' => 'Failed to create session'], 500);
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
        
        // Get items from POST data
        $items_json = $this->input->post('items');
        
        if (!$items_json) {
            $this->jsonResponse(['error' => 'items data is required'], 400);
            return;
        }
        
        $items = json_decode($items_json, true);
        
        if (!is_array($items)) {
            $this->jsonResponse(['error' => 'items must be an array'], 400);
            return;
        }
        
        // Validate and save items
        $result = $this->qcModel->saveQcItems($session_id, $items);
        
        if ($result) {
            $this->jsonResponse([
                'success' => true,
                'message' => 'QC items saved successfully',
                'count' => count($items)
            ]);
        } else {
            $this->jsonResponse(['error' => 'Failed to save QC items'], 500);
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
        
        if (!is_dir($upload_path)) {
            mkdir($upload_path, 0755, true);
        }
        
        $config = [
            'upload_path' => $upload_path,
            'allowed_types' => 'jpg|jpeg|png|gif|mp4|mov',
            'max_size' => 10240, // 10MB
            'encrypt_name' => true
        ];
        
        $this->upload->initialize($config);
        
        if (!$this->upload->do_upload('file')) {
            $this->jsonResponse([
                'error' => $this->upload->display_errors('', '')
            ], 400);
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
            $this->jsonResponse([
                'success' => true,
                'message' => 'File uploaded successfully',
                'attachment' => [
                    'id' => $attachment_id,
                    'filename' => $attachment_data['filename'],
                    'path' => $attachment_data['path'],
                    'url' => base_url($attachment_data['path'])
                ]
            ]);
        } else {
            // Delete uploaded file if DB insert fails
            @unlink($upload_path . $upload_data['file_name']);
            $this->jsonResponse(['error' => 'Failed to save attachment record'], 500);
        }
    }
    
    /**
     * Make decision (APPROVE/REJECT)
     * POST /qc/sessions/{id}/decision
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
        
        if (!$result || !in_array($result, ['APPROVE', 'REJECT'])) {
            $this->jsonResponse(['error' => 'result must be APPROVE or REJECT'], 400);
            return;
        }
        
        // Validate decision
        $validation = $this->checklistService->validateDecision($session_id, $result, $reason);
        
        if (!$validation['valid']) {
            $this->jsonResponse([
                'error' => 'Validation failed',
                'errors' => $validation['errors']
            ], 400);
            return;
        }
        
        // Calculate defect rate
        $stats = $this->qcModel->calculateDefectRate($session_id);
        
        // Get AQL (use provided or default)
        if (!$aql) {
            $aql = $this->qcModel->getDefaultAql();
        }
        
        // Check near-threshold condition
        $recommendation = $this->checklistService->calculateDecisionRecommendation($session_id, $aql);
        
        if ($recommendation['action'] === 'INCREASE_SAMPLE') {
            // Return 409 with suggestion to increase sample
            $this->jsonResponse([
                'error' => 'Near threshold detected',
                'code' => 'NEAR_THRESHOLD',
                'action' => 'INCREASE_SAMPLE',
                'message' => $recommendation['analysis'],
                'defect_rate' => $recommendation['defect_rate'],
                'aql_threshold' => $recommendation['aql_threshold']
            ], 409);
            return;
        }
        
        // Prepare decision data
        $decision_data = [
            'aql' => $aql,
            'defect_rate' => $stats['defect_rate'],
            'reason' => $reason,
            'decided_by' => $this->session->userdata('username')
        ];
        
        // Process decision
        if ($result === 'APPROVE') {
            $success = $this->qcModel->processApproveDecision($session_id, $decision_data);
        } else {
            $success = $this->qcModel->processRejectDecision($session_id, $decision_data);
        }
        
        if ($success) {
            $this->jsonResponse([
                'success' => true,
                'message' => "Decision {$result} recorded successfully",
                'result' => $result,
                'defect_rate' => $stats['defect_rate'],
                'redirect' => base_url('qc/sessions/' . $session_id)
            ]);
        } else {
            $this->jsonResponse(['error' => 'Failed to process decision'], 500);
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
        
        $recommendation = $this->checklistService->calculateDecisionRecommendation($session_id);
        
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
