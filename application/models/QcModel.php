<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * QcModel - Quality Control Module Data Access Layer
 * 
 * Handles all QC-related database operations:
 * - Shift closures management
 * - QC sessions and inspection items
 * - Decisions (APPROVE/REJECT)
 * - Attachments and adjustment requests
 * 
 * @author AI Pair Programmer
 * @date 2025-11-02
 */
class QcModel extends CI_Model
{
    // ========================================
    // SHIFT CLOSURES
    // ========================================
    
    /**
     * Get pending shift closures awaiting QC inspection
     * 
     * @param array $filters Optional filters: line_code, shift_code, project_code, date_from, date_to
     * @return array
     */
    public function getPendingClosures($filters = [])
    {
        $this->db->select('sc.*, p.project_name, pr.product_name');
        $this->db->from('shift_closures sc');
        $this->db->join('project p', 'sc.project_code = p.id_project', 'left');
        $this->db->join('product pr', 'sc.product_code = pr.id_product', 'left');
        $this->db->where('sc.status', 'PENDING_QC');
        
        if (!empty($filters['line_code'])) {
            $this->db->where('sc.line_code', $filters['line_code']);
        }
        
        if (!empty($filters['shift_code'])) {
            $this->db->where('sc.shift_code', $filters['shift_code']);
        }
        
        if (!empty($filters['project_code'])) {
            $this->db->where('sc.project_code', $filters['project_code']);
        }
        
        if (!empty($filters['date_from'])) {
            $this->db->where('sc.closed_at >=', $filters['date_from']);
        }
        
        if (!empty($filters['date_to'])) {
            $this->db->where('sc.closed_at <=', $filters['date_to']);
        }
        
        $this->db->order_by('sc.closed_at', 'DESC');
        
        return $this->db->get()->result();
    }
    
    /**
     * Get today's QC statistics
     * 
     * @return array
     */
    public function getTodayStatistics()
    {
        $today = date('Y-m-d');
        
        // Count pending QC
        $this->db->where('status', 'PENDING_QC');
        $this->db->where('DATE(closed_at)', $today);
        $pending = $this->db->count_all_results('shift_closures');
        
        // Count verified today
        $this->db->select('COUNT(*) as count');
        $this->db->from('qc_decisions qd');
        $this->db->join('qc_sessions qs', 'qd.session_id = qs.id', 'inner');
        $this->db->where('qd.result', 'APPROVE');
        $this->db->where('DATE(qd.decided_at)', $today);
        $verified_query = $this->db->get();
        $verified = $verified_query->row()->count ?? 0;
        
        // Count rejected today
        $this->db->select('COUNT(*) as count');
        $this->db->from('qc_decisions qd');
        $this->db->join('qc_sessions qs', 'qd.session_id = qs.id', 'inner');
        $this->db->where('qd.result', 'REJECT');
        $this->db->where('DATE(qd.decided_at)', $today);
        $rejected_query = $this->db->get();
        $rejected = $rejected_query->row()->count ?? 0;
        
        return [
            'pending' => $pending,
            'verified' => $verified,
            'rejected' => $rejected
        ];
    }
    
    /**
     * Get closure by ID
     * 
     * @param int $id
     * @return object|null
     */
    public function getClosureById($id)
    {
        $this->db->select('sc.*, p.project_name, pr.product_name');
        $this->db->from('shift_closures sc');
        $this->db->join('project p', 'sc.project_code = p.id_project', 'left');
        $this->db->join('product pr', 'sc.product_code = pr.id_product', 'left');
        $this->db->where('sc.id', $id);
        
        return $this->db->get()->row();
    }
    
    /**
     * Update closure status
     * 
     * @param int $closure_id
     * @param string $status VERIFIED|REJECTED
     * @param bool $can_receive_fg
     * @return bool
     */
    public function updateClosureStatus($closure_id, $status, $can_receive_fg = false)
    {
        $data = [
            'status' => $status,
            'can_receive_fg' => $can_receive_fg ? 1 : 0
        ];
        
        return $this->db->update('shift_closures', $data, ['id' => $closure_id]);
    }
    
    // ========================================
    // QC SESSIONS
    // ========================================
    
    /**
     * Create new QC session
     * 
     * @param array $data
     * @return int|bool Session ID or false
     */
    public function createSession($data)
    {
        // Generate session code
        if (empty($data['code'])) {
            $data['code'] = $this->generateSessionCode();
        }
        
        if (empty($data['started_at'])) {
            $data['started_at'] = date('Y-m-d H:i:s');
        }
        
        if ($this->db->insert('qc_sessions', $data)) {
            return $this->db->insert_id();
        }
        
        return false;
    }
    
    /**
     * Get session by ID with closure details
     * 
     * @param int $id
     * @return object|null
     */
    public function getSessionById($id)
    {
        $this->db->select('
            qs.*,
            sc.code as closure_code,
            sc.line_code,
            sc.shift_code,
            sc.project_code,
            sc.lot_code,
            sc.product_code,
            sc.variant,
            sc.qty_finished,
            sc.qty_waste,
            sc.status as closure_status,
            p.project_name,
            pr.product_name,
            u.full_name as inspector_name,
            qd.result,
            qd.reason as decision_reason,
            qd.defect_rate,
            qd.aql as decision_aql,
            qd.decided_by,
            qd.decided_at
        ');
        $this->db->from('qc_sessions qs');
        $this->db->join('shift_closures sc', 'qs.closure_id = sc.id', 'left');
        $this->db->join('project p', 'sc.project_code = p.id_project', 'left');
        $this->db->join('product pr', 'sc.product_code = pr.id_product', 'left');
        $this->db->join('user u', 'qs.inspector_code = u.username', 'left');
        $this->db->join('qc_decisions qd', 'qs.id = qd.session_id', 'left');
        $this->db->where('qs.id', $id);
        
        return $this->db->get()->row();
    }
    
    /**
     * Get all sessions by inspector username
     * 
     * @param string $inspector_code
     * @return array
     */
    public function getSessionsByInspector($inspector_code)
    {
        $this->db->select('
            qs.*,
            sc.code as closure_code,
            sc.line_code,
            sc.shift_code,
            sc.project_code,
            sc.product_code,
            p.project_name,
            pr.product_name,
            qd.result,
            qd.decided_at
        ');
        $this->db->from('qc_sessions qs');
        $this->db->join('shift_closures sc', 'qs.closure_id = sc.id', 'left');
        $this->db->join('project p', 'sc.project_code = p.id_project', 'left');
        $this->db->join('product pr', 'sc.product_code = pr.id_product', 'left');
        $this->db->join('qc_decisions qd', 'qs.id = qd.session_id', 'left');
        $this->db->where('qs.inspector_code', $inspector_code);
        $this->db->order_by('qs.created_at', 'DESC');
        
        return $this->db->get()->result();
    }
    
    /**
     * Get decided sessions with filters (for reports)
     * 
     * @param array $filters
     * @return array
     */
    public function getDecidedSessions($filters = [])
    {
        $this->db->select('
            qs.*,
            sc.code as closure_code,
            sc.line_code,
            sc.shift_code,
            sc.project_code,
            sc.product_code,
            sc.lot_code,
            p.project_name,
            pr.product_name,
            qd.result,
            qd.defect_rate,
            qd.aql,
            qd.reason as decision_reason,
            qd.decided_by,
            qd.decided_at,
            u.full_name as inspector_name,
            u2.full_name as decided_by_name
        ');
        $this->db->from('qc_sessions qs');
        $this->db->join('shift_closures sc', 'qs.closure_id = sc.id', 'left');
        $this->db->join('project p', 'sc.project_code = p.id_project', 'left');
        $this->db->join('product pr', 'sc.product_code = pr.id_product', 'left');
        $this->db->join('qc_decisions qd', 'qs.id = qd.session_id', 'inner');
        $this->db->join('user u', 'qs.inspector_code = u.username', 'left');
        $this->db->join('user u2', 'qd.decided_by = u2.username', 'left');
        $this->db->where('qs.status', 'DECIDED');
        
        // Apply filters
        if (!empty($filters['line_code'])) {
            $this->db->where('sc.line_code', $filters['line_code']);
        }
        if (!empty($filters['shift_code'])) {
            $this->db->where('sc.shift_code', $filters['shift_code']);
        }
        if (!empty($filters['project_code'])) {
            $this->db->where('sc.project_code', $filters['project_code']);
        }
        if (!empty($filters['result'])) {
            $this->db->where('qd.result', $filters['result']);
        }
        if (!empty($filters['date_from'])) {
            $this->db->where('DATE(qd.decided_at) >=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $this->db->where('DATE(qd.decided_at) <=', $filters['date_to']);
        }
        
        $this->db->order_by('qd.decided_at', 'DESC');
        
        return $this->db->get()->result();
    }
    
    /**
     * Get sessions by closure ID
     * 
     * @param int $closure_id
     * @return array
     */
    public function getSessionsByClosureId($closure_id)
    {
        $this->db->select('qs.*, u.full_name as inspector_name');
        $this->db->from('qc_sessions qs');
        $this->db->join('user u', 'qs.inspector_code = u.username', 'left');
        $this->db->where('qs.closure_id', $closure_id);
        $this->db->order_by('qs.started_at', 'DESC');
        
        return $this->db->get()->result();
    }
    
    /**
     * Get existing session by closure ID (OPEN status or latest)
     * 
     * @param int $closure_id
     * @return object|null
     */
    public function getSessionByClosureId($closure_id)
    {
        // First try to get OPEN session
        $this->db->select('qs.*');
        $this->db->from('qc_sessions qs');
        $this->db->where('qs.closure_id', $closure_id);
        $this->db->where('qs.status', 'OPEN');
        $this->db->order_by('qs.started_at', 'DESC');
        $this->db->limit(1);
        
        $open_session = $this->db->get()->row();
        
        if ($open_session) {
            return $open_session;
        }
        
        // If no OPEN session, get latest session (any status)
        $this->db->select('qs.*');
        $this->db->from('qc_sessions qs');
        $this->db->where('qs.closure_id', $closure_id);
        $this->db->order_by('qs.started_at', 'DESC');
        $this->db->limit(1);
        
        return $this->db->get()->row();
    }
    
    /**
     * Update session status
     * 
     * @param int $session_id
     * @param string $status OPEN|DECIDED
     * @return bool
     */
    public function updateSessionStatus($session_id, $status)
    {
        return $this->db->update('qc_sessions', ['status' => $status], ['id' => $session_id]);
    }
    
    /**
     * Check if user can access session (permission check)
     * 
     * @param int $session_id
     * @param string $user_code
     * @param string|null $line_code User's assigned line (null = can access all)
     * @return bool
     */
    public function canUserAccessSession($session_id, $user_code, $line_code = null)
    {
        $this->db->select('qs.inspector_code, sc.line_code');
        $this->db->from('qc_sessions qs');
        $this->db->join('shift_closures sc', 'qs.closure_id = sc.id');
        $this->db->where('qs.id', $session_id);
        
        $session = $this->db->get()->row();
        
        if (!$session) {
            return false;
        }
        
        // Inspector owns the session
        if ($session->inspector_code === $user_code) {
            return true;
        }
        
        // Check line assignment if provided
        if ($line_code !== null && $session->line_code !== $line_code) {
            return false;
        }
        
        return true;
    }
    
    // ========================================
    // QC ITEMS
    // ========================================
    
    /**
     * Save multiple QC items (bulk insert/update)
     * 
     * @param int $session_id
     * @param array $items
     * @return bool
     */
    public function saveQcItems($session_id, $items)
    {
        if (empty($items)) {
            return false;
        }
        
        // Start transaction
        $this->db->trans_start();
        
        foreach ($items as $item) {
            $item['session_id'] = $session_id;
            
            // Check if item exists (by session_id + checklist_item_code)
            $existing = $this->db->get_where('qc_items', [
                'session_id' => $session_id,
                'checklist_item_code' => $item['checklist_item_code']
            ])->row();
            
            if ($existing) {
                // Update
                $this->db->update('qc_items', $item, ['id' => $existing->id]);
            } else {
                // Insert
                $this->db->insert('qc_items', $item);
            }
        }
        
        $this->db->trans_complete();
        
        return $this->db->trans_status();
    }
    
    /**
     * Get QC items by session ID
     * 
     * @param int $session_id
     * @return array
     */
    public function getQcItemsBySessionId($session_id)
    {
        $this->db->select('*');
        $this->db->from('qc_items');
        $this->db->where('session_id', $session_id);
        $this->db->order_by('id', 'ASC');
        
        return $this->db->get()->result();
    }
    
    /**
     * Calculate defect rate from QC items
     * 
     * @param int $session_id
     * @return array [total_items, failed_items, defect_rate, critical_count, major_count, minor_count]
     */
    public function calculateDefectRate($session_id)
    {
        $items = $this->getQcItemsBySessionId($session_id);
        
        $total = count($items);
        $failed = 0;
        $critical = 0;
        $major = 0;
        $minor = 0;
        
        foreach ($items as $item) {
            if ($item->result === 'FAIL') {
                $failed++;
                
                switch ($item->severity) {
                    case 'CRITICAL':
                        $critical++;
                        break;
                    case 'MAJOR':
                        $major++;
                        break;
                    case 'MINOR':
                        $minor++;
                        break;
                }
            }
        }
        
        $defect_rate = $total > 0 ? ($failed / $total) * 100 : 0;
        
        return [
            'total_items' => $total,
            'failed_items' => $failed,
            'passed_items' => $total - $failed,
            'defect_rate' => round($defect_rate, 2),
            'critical_count' => $critical,
            'major_count' => $major,
            'minor_count' => $minor
        ];
    }
    
    // ========================================
    // QC DECISIONS
    // ========================================
    
    /**
     * Create QC decision
     * 
     * @param array $data
     * @return int|bool Decision ID or false
     */
    public function createDecision($data)
    {
        if (empty($data['decided_at'])) {
            $data['decided_at'] = date('Y-m-d H:i:s');
        }
        
        if ($this->db->insert('qc_decisions', $data)) {
            return $this->db->insert_id();
        }
        
        return false;
    }
    
    /**
     * Get decision by session ID
     * 
     * @param int $session_id
     * @return object|null
     */
    public function getDecisionBySessionId($session_id)
    {
        return $this->db->get_where('qc_decisions', ['session_id' => $session_id])->row();
    }
    
    /**
     * Process APPROVE decision (transactional)
     * 
     * @param int $session_id
     * @param array $decision_data [aql, defect_rate, decided_by]
     * @return bool
     */
    public function processApproveDecision($session_id, $decision_data)
    {
        $this->db->trans_start();
        
        // 1. Get session to find closure_id
        $session = $this->getSessionById($session_id);
        if (!$session) {
            $this->db->trans_rollback();
            return false;
        }
        
        // 2. Create decision record
        $decision = [
            'session_id' => $session_id,
            'result' => 'APPROVE',
            'aql' => $decision_data['aql'] ?? null,
            'defect_rate' => $decision_data['defect_rate'] ?? null,
            'reason' => $decision_data['reason'] ?? null,
            'decided_by' => $decision_data['decided_by'] ?? null,
            'decided_at' => date('Y-m-d H:i:s')
        ];
        $this->db->insert('qc_decisions', $decision);
        
        // 3. Update session status to DECIDED
        $this->db->update('qc_sessions', ['status' => 'DECIDED'], ['id' => $session_id]);
        
        // 4. Update closure status to VERIFIED with can_receive_fg flag
        $this->db->update('shift_closures', [
            'status' => 'VERIFIED',
            'can_receive_fg' => 1
        ], ['id' => $session->closure_id]);
        
        $this->db->trans_complete();
        
        return $this->db->trans_status();
    }
    
    /**
     * Process REJECT decision (transactional)
     * 
     * @param int $session_id
     * @param array $decision_data [aql, defect_rate, reason, decided_by]
     * @return bool
     */
    public function processRejectDecision($session_id, $decision_data)
    {
        $this->db->trans_start();
        
        // 1. Get session to find closure_id
        $session = $this->getSessionById($session_id);
        if (!$session) {
            $this->db->trans_rollback();
            return false;
        }
        
        // 2. Create decision record
        $decision = [
            'session_id' => $session_id,
            'result' => 'REJECT',
            'aql' => $decision_data['aql'] ?? null,
            'defect_rate' => $decision_data['defect_rate'] ?? null,
            'reason' => $decision_data['reason'],
            'decided_by' => $decision_data['decided_by'] ?? null,
            'decided_at' => date('Y-m-d H:i:s')
        ];
        $this->db->insert('qc_decisions', $decision);
        
        // 3. Update session status to DECIDED
        $this->db->update('qc_sessions', ['status' => 'DECIDED'], ['id' => $session_id]);
        
        // 4. Update closure status to REJECTED
        $this->db->update('shift_closures', [
            'status' => 'REJECTED',
            'can_receive_fg' => 0
        ], ['id' => $session->closure_id]);
        
        // 5. Create adjustment request
        $adj_request = [
            'code' => $this->generateAdjustmentRequestCode(),
            'closure_id' => $session->closure_id,
            'created_by' => $decision_data['decided_by'] ?? $session->inspector_code,
            'reason' => $decision_data['reason'],
            'status' => 'OPEN'
        ];
        $this->db->insert('adjustment_requests', $adj_request);
        
        $this->db->trans_complete();
        
        return $this->db->trans_status();
    }
    
    // ========================================
    // QC ATTACHMENTS
    // ========================================
    
    /**
     * Save attachment record
     * 
     * @param array $data
     * @return int|bool Attachment ID or false
     */
    public function saveAttachment($data)
    {
        if ($this->db->insert('qc_attachments', $data)) {
            return $this->db->insert_id();
        }
        
        return false;
    }
    
    /**
     * Get attachments by session ID
     * 
     * @param int $session_id
     * @return array
     */
    public function getAttachmentsBySessionId($session_id)
    {
        $this->db->select('*');
        $this->db->from('qc_attachments');
        $this->db->where('session_id', $session_id);
        $this->db->order_by('created_at', 'ASC');
        
        return $this->db->get()->result();
    }
    
    /**
     * Count attachments for a session
     * 
     * @param int $session_id
     * @return int
     */
    public function countAttachments($session_id)
    {
        return $this->db->where('session_id', $session_id)->count_all_results('qc_attachments');
    }
    
    // ========================================
    // ADJUSTMENT REQUESTS
    // ========================================
    
    /**
     * Get adjustment requests by status
     * 
     * @param string|null $status OPEN|ACKED|DONE
     * @return array
     */
    public function getAdjustmentRequests($status = null)
    {
        $this->db->select('ar.*, sc.code as closure_code, sc.line_code, sc.shift_code, u.full_name as creator_name');
        $this->db->from('adjustment_requests ar');
        $this->db->join('shift_closures sc', 'ar.closure_id = sc.id', 'left');
        $this->db->join('user u', 'ar.created_by = u.username', 'left');
        
        if ($status !== null) {
            $this->db->where('ar.status', $status);
        }
        
        $this->db->order_by('ar.created_at', 'DESC');
        
        return $this->db->get()->result();
    }
    
    // ========================================
    // CHECKLIST MASTER
    // ========================================
    
    /**
     * Get checklist items for product/variant
     * 
     * @param string $product_code
     * @param string|null $variant
     * @return array
     */
    public function getChecklistItems($product_code, $variant = null)
    {
        $this->db->select('*');
        $this->db->from('qc_checklist_master');
        $this->db->where('product_code', $product_code);
        $this->db->where('is_active', 1);
        
        // Match specific variant or NULL (applies to all variants)
        if ($variant !== null) {
            $this->db->group_start();
            $this->db->where('variant', $variant);
            $this->db->or_where('variant IS NULL');
            $this->db->group_end();
        } else {
            $this->db->where('variant IS NULL');
        }
        
        $this->db->order_by('sequence', 'ASC');
        
        return $this->db->get()->result();
    }
    
    // ========================================
    // QC CONFIG
    // ========================================
    
    /**
     * Get QC config value
     * 
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getConfig($key, $default = null)
    {
        $config = $this->db->get_where('qc_config', ['config_key' => $key])->row();
        
        return $config ? $config->config_value : $default;
    }
    
    /**
     * Get default AQL
     * 
     * @return float
     */
    public function getDefaultAql()
    {
        return (float) $this->getConfig('QC_AQL_DEFAULT', 2.5);
    }
    
    /**
     * Get near threshold margin
     * 
     * @return float
     */
    public function getNearThresholdMargin()
    {
        return (float) $this->getConfig('QC_NEAR_THRESHOLD_MARGIN', 5);
    }
    
    // ========================================
    // HELPER METHODS
    // ========================================
    
    /**
     * Generate unique session code
     * Format: QCS-YYYYMMDD-NNNN
     * 
     * @return string
     */
    private function generateSessionCode()
    {
        $date = date('Ymd');
        $prefix = "QCS-{$date}-";
        
        // Find max sequence for today
        $this->db->select('code');
        $this->db->from('qc_sessions');
        $this->db->like('code', $prefix, 'after');
        $this->db->order_by('code', 'DESC');
        $this->db->limit(1);
        
        $last = $this->db->get()->row();
        
        if ($last) {
            $last_seq = (int) substr($last->code, -4);
            $seq = $last_seq + 1;
        } else {
            $seq = 1;
        }
        
        return $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }
    
    /**
     * Generate unique adjustment request code
     * Format: AR-YYYYMMDD-NNNN
     * 
     * @return string
     */
    private function generateAdjustmentRequestCode()
    {
        $date = date('Ymd');
        $prefix = "AR-{$date}-";
        
        // Find max sequence for today
        $this->db->select('code');
        $this->db->from('adjustment_requests');
        $this->db->like('code', $prefix, 'after');
        $this->db->order_by('code', 'DESC');
        $this->db->limit(1);
        
        $last = $this->db->get()->row();
        
        if ($last) {
            $last_seq = (int) substr($last->code, -4);
            $seq = $last_seq + 1;
        } else {
            $seq = 1;
        }
        
        return $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }
    
    // ========================================
    // VALIDATION METHODS (Use Case 19)
    // ========================================
    
    /**
     * Check if checklist is complete
     * Validates that all required checklist items have been filled
     * 
     * @param int $session_id
     * @return array ['complete' => bool, 'total' => int, 'filled' => int, 'missing' => array]
     */
    public function isChecklistComplete($session_id)
    {
        $session = $this->getSessionById($session_id);
        
        if (!$session) {
            return [
                'complete' => false,
                'error' => 'Session not found'
            ];
        }
        
        // Get required checklist items
        $checklist_items = $this->getChecklistItems($session->product_code, $session->variant);
        $total_items = count($checklist_items);
        
        // Get filled items
        $qc_items = $this->getQcItemsBySessionId($session_id);
        $filled_items = count($qc_items);
        
        // Find missing items
        $filled_codes = array_column($qc_items, 'checklist_item_code');
        $required_codes = array_column($checklist_items, 'code');
        $missing_codes = array_diff($required_codes, $filled_codes);
        
        $missing_items = [];
        foreach ($checklist_items as $item) {
            if (in_array($item->code, $missing_codes)) {
                $missing_items[] = [
                    'code' => $item->code,
                    'name' => $item->item_name,
                    'category' => $item->category
                ];
            }
        }
        
        return [
            'complete' => $filled_items >= $total_items,
            'total' => $total_items,
            'filled' => $filled_items,
            'missing' => $missing_items,
            'completion_rate' => $total_items > 0 ? round(($filled_items / $total_items) * 100, 2) : 0
        ];
    }
    
    /**
     * Validate requirements for REJECT decision
     * Use Case 19 - Alternative Flow 8.1: Reject requires reason + attachment
     * 
     * @param int $session_id
     * @param string $reason
     * @return array ['valid' => bool, 'errors' => array]
     */
    public function validateRejectRequirements($session_id, $reason)
    {
        $errors = [];
        
        // Check reason is provided
        if (empty($reason) || trim($reason) === '') {
            $errors[] = 'Lý do từ chối là bắt buộc';
        }
        
        // Check reason minimum length
        if (strlen(trim($reason)) < 20) {
            $errors[] = 'Lý do từ chối phải có ít nhất 20 ký tự';
        }
        
        // Check attachments exist
        $attachment_count = $this->countAttachments($session_id);
        if ($attachment_count === 0) {
            $errors[] = 'Phải đính kèm ít nhất 1 ảnh/video làm bằng chứng khi từ chối';
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'attachment_count' => $attachment_count
        ];
    }
    
    /**
     * Check if defect rate is near AQL threshold
     * Use Case 19 - Alternative Flow 6.1: Near threshold detection
     * 
     * @param float $defect_rate Current defect rate
     * @param float $aql Acceptance Quality Limit
     * @param float $margin Margin percentage (default from config)
     * @return array ['near_threshold' => bool, 'distance' => float, 'recommendation' => string]
     */
    public function isNearThreshold($defect_rate, $aql, $margin = null)
    {
        if ($margin === null) {
            $margin = $this->getNearThresholdMargin();
        }
        
        // Calculate threshold range
        $lower_bound = $aql - ($aql * $margin / 100);
        $upper_bound = $aql + ($aql * $margin / 100);
        
        $near_threshold = ($defect_rate >= $lower_bound && $defect_rate <= $upper_bound);
        $distance = abs($defect_rate - $aql);
        $distance_percent = $aql > 0 ? ($distance / $aql) * 100 : 0;
        
        $recommendation = '';
        if ($near_threshold) {
            if ($defect_rate > $aql) {
                $recommendation = 'INCREASE_SAMPLE_SIZE';
            } else {
                $recommendation = 'REVIEW_CAREFULLY';
            }
        } elseif ($defect_rate > $aql) {
            $recommendation = 'REJECT';
        } else {
            $recommendation = 'APPROVE';
        }
        
        return [
            'near_threshold' => $near_threshold,
            'distance' => round($distance, 2),
            'distance_percent' => round($distance_percent, 2),
            'lower_bound' => round($lower_bound, 2),
            'upper_bound' => round($upper_bound, 2),
            'recommendation' => $recommendation,
            'message' => $near_threshold 
                ? "Tỷ lệ lỗi ({$defect_rate}%) gần ngưỡng AQL ({$aql}%). {$recommendation}."
                : "Tỷ lệ lỗi rõ ràng. {$recommendation}."
        ];
    }
    
    /**
     * Validate APPROVE decision requirements
     * 
     * @param int $session_id
     * @return array ['valid' => bool, 'errors' => array]
     */
    public function validateApproveRequirements($session_id)
    {
        $errors = [];
        
        // Check checklist completeness
        $checklist_status = $this->isChecklistComplete($session_id);
        if (!$checklist_status['complete']) {
            $errors[] = sprintf(
                'Checklist chưa đầy đủ (%d/%d items). Còn thiếu: %s',
                $checklist_status['filled'],
                $checklist_status['total'],
                implode(', ', array_column($checklist_status['missing'], 'name'))
            );
        }
        
        // Check for critical defects
        $stats = $this->calculateDefectRate($session_id);
        if ($stats['critical_count'] > 0) {
            $errors[] = sprintf(
                'Không thể duyệt khi có %d lỗi CRITICAL',
                $stats['critical_count']
            );
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'stats' => $stats
        ];
    }
}
