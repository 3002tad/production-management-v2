-- =====================================================
-- DEBUG: Kiểm tra tại sao chỉ thấy 2/4 batch
-- =====================================================

-- 1. CHECK: Tất cả shift_closures VERIFIED
SELECT '=== shift_closures VERIFIED ===' as section;
SELECT id, code, status, can_receive_fg FROM shift_closures WHERE status = 'VERIFIED';

-- 2. CHECK: Các QC Sessions & Decisions
SELECT '=== QC Sessions & Decisions ===' as section;
SELECT 
  sc.id,
  sc.code,
  qs.id as session_id,
  qs.closure_id,
  qs.status as qc_status,
  qd.id as decision_id,
  qd.result,
  qd.defect_rate
FROM shift_closures sc
LEFT JOIN qc_sessions qs ON qs.closure_id = sc.id
LEFT JOIN qc_decisions qd ON qd.session_id = qs.id
WHERE sc.status = 'VERIFIED'
ORDER BY sc.id;

-- 3. CHECK: Query Model hiện tại (INNER JOIN)
SELECT '=== Current Model Query (INNER JOIN) ===' as section;
SELECT 
  sc.id,
  sc.code,
  qd.result
FROM shift_closures sc
INNER JOIN qc_sessions qs ON qs.closure_id = sc.id
INNER JOIN qc_decisions qd ON qd.session_id = qs.id
WHERE qd.result = 'APPROVE' 
  AND sc.can_receive_fg = 1
  AND sc.status = 'VERIFIED';

-- 4. CHECK: Dữ liệu QC Decision
SELECT '=== All QC Decisions ===' as section;
SELECT 
  qd.id,
  qd.session_id,
  qd.result,
  qd.aql,
  qd.defect_rate,
  qs.closure_id
FROM qc_decisions qd
LEFT JOIN qc_sessions qs ON qs.id = qd.session_id;

-- 5. CHECK: Những shift_closures nào KHÔNG có QC Decision
SELECT '=== shift_closures without QC Decision ===' as section;
SELECT 
  sc.id,
  sc.code,
  sc.status,
  sc.can_receive_fg,
  qs.id as session_id,
  qd.id as decision_id
FROM shift_closures sc
LEFT JOIN qc_sessions qs ON qs.closure_id = sc.id
LEFT JOIN qc_decisions qd ON qd.session_id = qs.id
WHERE sc.status = 'VERIFIED' AND qd.id IS NULL;

-- 6. FIX: Hiển thị ALL batch VERIFIED (không cần QC APPROVE - vì can_receive_fg=1 là proof)
SELECT '=== Alternative: Based on can_receive_fg=1 ===' as section;
SELECT 
  sc.id AS id_finished,
  sc.code AS closure_code,
  sc.project_code AS id_project,
  sc.product_code,
  sc.qty_finished AS qty_passed,
  sc.qty_waste,
  sc.closed_at AS fdate,
  sc.closed_by
FROM shift_closures sc
WHERE sc.can_receive_fg = 1 
  AND sc.status = 'VERIFIED'
ORDER BY sc.closed_at DESC;
