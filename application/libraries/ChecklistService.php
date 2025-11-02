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
     * Map database fields to expected view fields
     * 
     * @param string $product_code
     * @param string|null $variant
     * @return array
     */
    public function getChecklist($product_code, $variant = null)
    {
        $raw_items = $this->CI->qcModel->getChecklistItems($product_code, $variant);
        
        // Map database fields to expected fields
        $mapped_items = [];
        foreach ($raw_items as $item) {
            $mapped_items[] = (object)[
                'item_code' => $item->code,                    // code → item_code
                'criteria_name' => $item->item_name,           // item_name → criteria_name
                'description' => $item->criteria ?? '',        // criteria → description
                'test_method' => $item->test_method ?? null,   // test_method (may not exist)
                'sample_size' => $item->sample_size ?? 0,
                'aql' => $item->aql ?? 2.5,
                'category' => $item->category ?? '',
                'sequence' => $item->sequence ?? 0
            ];
        }
        
        return $mapped_items;
    }
    
    /**
     * Calculate decision recommendation based on AQL
     * Use Case 19 - Step 6: AI suggestion for Pass/Fail
     * Alternative Flow 6.1: Near threshold detection
     * 
     * @param int $session_id
     * @param float|null $aql_threshold Default from config if null
     * @return array [recommendation, defect_rate, analysis, action, confidence]
     */
    public function calculateDecisionRecommendation($session_id, $aql_threshold = null)
    {
        // 1. Check checklist completeness
        $checklist_status = $this->CI->qcModel->isChecklistComplete($session_id);
        
        if (!$checklist_status['complete']) {
            return [
                'recommendation' => 'INCOMPLETE',
                'defect_rate' => 0,
                'aql_threshold' => $aql_threshold ?? $this->CI->qcModel->getDefaultAql(),
                'analysis' => sprintf(
                    'Checklist chưa đầy đủ (%d/%d items hoàn thành). Vui lòng kiểm tra đầy đủ trước khi quyết định.',
                    $checklist_status['filled'],
                    $checklist_status['total']
                ),
                'action' => 'COMPLETE_CHECKLIST',
                'confidence' => 'LOW',
                'stats' => null,
                'checklist_status' => $checklist_status
            ];
        }
        
        // 2. Get defect statistics
        $stats = $this->CI->qcModel->calculateDefectRate($session_id);
        
        // 3. Get AQL threshold
        if ($aql_threshold === null) {
            $aql_threshold = $this->CI->qcModel->getDefaultAql();
        }
        
        $defect_rate = $stats['defect_rate'];
        
        // 4. Check for critical defects - AUTO REJECT
        if ($stats['critical_count'] > 0) {
            return [
                'recommendation' => 'REJECT',
                'defect_rate' => $defect_rate,
                'aql_threshold' => $aql_threshold,
                'analysis' => sprintf(
                    '⛔ Phát hiện %d lỗi CRITICAL. Lô hàng phải BỊ TỪ CHỐI để đảm bảo chất lượng.',
                    $stats['critical_count']
                ),
                'action' => 'REJECT_CRITICAL',
                'confidence' => 'HIGH',
                'stats' => $stats,
                'checklist_status' => $checklist_status
            ];
        }
        
        // 5. Use isNearThreshold method for detection
        $threshold_check = $this->CI->qcModel->isNearThreshold($defect_rate, $aql_threshold);
        
        // 6. Determine recommendation and confidence level
        $recommendation = $threshold_check['recommendation'];
        $confidence = 'HIGH';
        
        // Map recommendation to Vietnamese analysis
        if ($threshold_check['near_threshold']) {
            $confidence = 'MEDIUM';
            
            if ($defect_rate > $aql_threshold) {
                $analysis = sprintf(
                    '⚠️ Tỷ lệ lỗi %.2f%% gần ngưỡng AQL %.2f%% (chênh lệch %.2f%%). '.
                    'Khuyến nghị: TĂNG CỠ MẪU để đánh giá chính xác hơn trước khi quyết định.',
                    $defect_rate,
                    $aql_threshold,
                    $threshold_check['distance']
                );
                $action = 'INCREASE_SAMPLE_SIZE';
            } else {
                $analysis = sprintf(
                    '⚠️ Tỷ lệ lỗi %.2f%% gần ngưỡng AQL %.2f%%. '.
                    'Khuyến nghị: XEM XÉT KỸ LƯỠNG trước khi duyệt.',
                    $defect_rate,
                    $aql_threshold
                );
                $action = 'REVIEW_CAREFULLY';
            }
        } elseif ($defect_rate > $aql_threshold) {
            $analysis = sprintf(
                '❌ Tỷ lệ lỗi %.2f%% VƯỢT QUÁ ngưỡng AQL %.2f%%. '.
                'Khuyến nghị: TỪ CHỐI lô hàng.',
                $defect_rate,
                $aql_threshold
            );
            $action = 'REJECT';
        } else {
            $analysis = sprintf(
                '✅ Tỷ lệ lỗi %.2f%% NẰM TRONG giới hạn chấp nhận (AQL: %.2f%%). '.
                'Khuyến nghị: PHÊ DUYỆT lô hàng.',
                $defect_rate,
                $aql_threshold
            );
            $action = 'APPROVE';
        }
        
        // 7. Add defect breakdown to analysis
        if ($stats['major_count'] > 0 || $stats['minor_count'] > 0) {
            $analysis .= sprintf(
                ' | Chi tiết: %d lỗi Major, %d lỗi Minor trên tổng %d mẫu kiểm tra.',
                $stats['major_count'],
                $stats['minor_count'],
                $stats['total_inspected']
            );
        }
        
        return [
            'recommendation' => $recommendation,
            'defect_rate' => $defect_rate,
            'aql_threshold' => $aql_threshold,
            'analysis' => $analysis,
            'action' => $action,
            'confidence' => $confidence,
            'stats' => $stats,
            'threshold_check' => $threshold_check,
            'checklist_status' => $checklist_status
        ];
    }
    
    /**
     * Validate decision request
     * Use Case 19 - Step 7: Decision validation
     * Alternative Flow 8.1: Reject requires reason + attachment
     * 
     * @param int $session_id
     * @param string $result APPROVE|REJECT
     * @param string|null $reason
     * @return array [valid, errors[], warnings[]]
     */
    public function validateDecision($session_id, $result, $reason = null)
    {
        $errors = [];
        $warnings = [];
        
        // 1. Check session exists and is OPEN
        $session = $this->CI->qcModel->getSessionById($session_id);
        if (!$session) {
            $errors[] = 'Session không tồn tại';
            return ['valid' => false, 'errors' => $errors, 'warnings' => $warnings];
        }
        
        if ($session->status !== 'OPEN') {
            $errors[] = 'Session đã được quyết định. Không thể sửa đổi';
            return ['valid' => false, 'errors' => $errors, 'warnings' => $warnings];
        }
        
        // 2. Check closure is still PENDING_QC
        if ($session->closure_status !== 'PENDING_QC') {
            $errors[] = 'Phiếu chốt ca không ở trạng thái PENDING_QC';
        }
        
        // 3. Check checklist completeness (REQUIRED for both APPROVE and REJECT)
        $checklist_status = $this->CI->qcModel->isChecklistComplete($session_id);
        if (!$checklist_status['complete']) {
            $errors[] = sprintf(
                'Checklist chưa đầy đủ (%d/%d items). Còn thiếu: %s',
                $checklist_status['filled'],
                $checklist_status['total'],
                implode(', ', array_column($checklist_status['missing'], 'name'))
            );
        }
        
        // 4. Specific validation for REJECT
        if ($result === 'REJECT') {
            $reject_validation = $this->CI->qcModel->validateRejectRequirements($session_id, $reason);
            
            if (!$reject_validation['valid']) {
                $errors = array_merge($errors, $reject_validation['errors']);
            }
        }
        
        // 5. Specific validation for APPROVE
        if ($result === 'APPROVE') {
            $approve_validation = $this->CI->qcModel->validateApproveRequirements($session_id);
            
            if (!$approve_validation['valid']) {
                $errors = array_merge($errors, $approve_validation['errors']);
            }
            
            // Warning: Check if defect rate is high but still approving
            $stats = $approve_validation['stats'];
            $aql = $this->CI->qcModel->getDefaultAql();
            
            if ($stats['defect_rate'] > ($aql * 0.8)) {
                $warnings[] = sprintf(
                    'Cảnh báo: Tỷ lệ lỗi %.2f%% gần ngưỡng AQL %.2f%%. Vui lòng xem xét kỹ trước khi phê duyệt.',
                    $stats['defect_rate'],
                    $aql
                );
            }
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'warnings' => $warnings,
            'checklist_status' => $checklist_status
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
