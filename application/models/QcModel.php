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
            u.full_name as inspector_name
        ');
        $this->db->from('qc_sessions qs');
        $this->db->join('shift_closures sc', 'qs.closure_id = sc.id', 'left');
        $this->db->join('project p', 'sc.project_code = p.id_project', 'left');
        $this->db->join('product pr', 'sc.product_code = pr.id_product', 'left');
        $this->db->join('users u', 'qs.inspector_code = u.username', 'left');
        $this->db->where('qs.id', $id);
        
        return $this->db->get()->row();
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
        $this->db->join('users u', 'qs.inspector_code = u.username', 'left');
        $this->db->where('qs.closure_id', $closure_id);
        $this->db->order_by('qs.started_at', 'DESC');
        
        return $this->db->get()->result();
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
        $this->db->join('users u', 'ar.created_by = u.username', 'left');
        
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
}
