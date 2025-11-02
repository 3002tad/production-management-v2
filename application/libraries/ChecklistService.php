<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * ChecklistService - Business logic for QC checklists
 * 
 * Provides:
 * - Checklist retrieval by product/variant
 * - AQL calculation and decision recommendation
 * - Near-threshold detection
 * 
 * @author AI Pair Programmer
 * @date 2025-11-02
 */
class ChecklistService
{
    protected $CI;
    
    public function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->load->model('QcModel', 'qcModel');
    }
    
    /**
     * Get checklist for product/variant
     * 
     * @param string $product_code
     * @param string|null $variant
     * @return array
     */
    public function getChecklist($product_code, $variant = null)
    {
        return $this->CI->qcModel->getChecklistItems($product_code, $variant);
    }
    
    /**
     * Calculate decision recommendation based on AQL
     * 
     * @param int $session_id
     * @param float|null $aql_threshold Default from config if null
     * @return array [recommendation, defect_rate, analysis, action]
     */
    public function calculateDecisionRecommendation($session_id, $aql_threshold = null)
    {
        // Get defect rate
        $stats = $this->CI->qcModel->calculateDefectRate($session_id);
        
        // Get AQL threshold
        if ($aql_threshold === null) {
            $aql_threshold = $this->CI->qcModel->getDefaultAql();
        }
        
        $defect_rate = $stats['defect_rate'];
        $near_margin = $this->CI->qcModel->getNearThresholdMargin();
        
        // Calculate thresholds
        $upper_threshold = $aql_threshold + $near_margin;
        $lower_threshold = $aql_threshold - $near_margin;
        
        // Decision logic
        $recommendation = 'APPROVE'; // Default
        $action = null;
        $analysis = '';
        
        // Critical defects = auto reject
        if ($stats['critical_count'] > 0) {
            $recommendation = 'REJECT';
            $analysis = "Found {$stats['critical_count']} CRITICAL defect(s). Auto-reject.";
        }
        // Defect rate exceeds AQL + margin
        elseif ($defect_rate > $upper_threshold) {
            $recommendation = 'REJECT';
            $analysis = "Defect rate {$defect_rate}% exceeds threshold {$upper_threshold}%.";
        }
        // Near threshold - need more sampling
        elseif ($defect_rate >= $lower_threshold && $defect_rate <= $upper_threshold) {
            $recommendation = 'REVIEW_NEEDED';
            $action = 'INCREASE_SAMPLE';
            $analysis = "Defect rate {$defect_rate}% is near threshold ({$lower_threshold}%-{$upper_threshold}%). Recommend increasing sample size.";
        }
        // Within acceptable range
        else {
            $recommendation = 'APPROVE';
            $analysis = "Defect rate {$defect_rate}% is within acceptable limit (< {$lower_threshold}%).";
        }
        
        return [
            'recommendation' => $recommendation,
            'defect_rate' => $defect_rate,
            'aql_threshold' => $aql_threshold,
            'analysis' => $analysis,
            'action' => $action,
            'stats' => $stats
        ];
    }
    
    /**
     * Validate decision request
     * 
     * @param int $session_id
     * @param string $result APPROVE|REJECT
     * @param string|null $reason
     * @return array [valid, errors[]]
     */
    public function validateDecision($session_id, $result, $reason = null)
    {
        $errors = [];
        
        // 1. Check session exists and is OPEN
        $session = $this->CI->qcModel->getSessionById($session_id);
        if (!$session) {
            $errors[] = 'Session not found.';
            return ['valid' => false, 'errors' => $errors];
        }
        
        if ($session->status !== 'OPEN') {
            $errors[] = 'Session already decided. Cannot modify.';
            return ['valid' => false, 'errors' => $errors];
        }
        
        // 2. Check closure is still PENDING_QC
        if ($session->closure_status !== 'PENDING_QC') {
            $errors[] = 'Closure is not in PENDING_QC status.';
        }
        
        // 3. Check QC items exist
        $items = $this->CI->qcModel->getQcItemsBySessionId($session_id);
        if (empty($items)) {
            $errors[] = 'No QC inspection items found. Please complete checklist first.';
        }
        
        // 4. REJECT requires reason
        if ($result === 'REJECT') {
            if (empty($reason) || trim($reason) === '') {
                $errors[] = 'Reason is required for REJECT decision.';
            }
            
            // REJECT requires at least 1 attachment
            $attachment_count = $this->CI->qcModel->countAttachments($session_id);
            if ($attachment_count === 0) {
                $errors[] = 'At least one attachment (photo/video) is required for REJECT decision.';
            }
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }
    
    /**
     * Check if user has permission to access session
     * 
     * @param int $session_id
     * @param array $user_context [username, role_name, level, line_code]
     * @return array [allowed, reason]
     */
    public function checkPermission($session_id, $user_context)
    {
        // 1. Must be QC role
        if ($user_context['role_name'] !== 'qc_staff') {
            return [
                'allowed' => false,
                'reason' => 'Access denied. Only QC staff can access this session.'
            ];
        }
        
        // 2. Check session exists
        $session = $this->CI->qcModel->getSessionById($session_id);
        if (!$session) {
            return [
                'allowed' => false,
                'reason' => 'Session not found.'
            ];
        }
        
        // 3. Check line assignment (if user has specific line_code)
        $user_line = $user_context['line_code'] ?? null;
        if ($user_line !== null && $session->line_code !== $user_line) {
            return [
                'allowed' => false,
                'reason' => "Access denied. This session is for line {$session->line_code}, but you are assigned to line {$user_line}."
            ];
        }
        
        return [
            'allowed' => true,
            'reason' => null
        ];
    }
}
