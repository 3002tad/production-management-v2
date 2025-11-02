-- =====================================================
-- QC Module - Seed Data
-- =====================================================
-- Purpose: Insert sample data for testing QC module
-- Run this AFTER migration 007
-- =====================================================

-- 1. CREATE QC USER (if not exists from RBAC migration)
-- Assumes QC role has role_id = 5 from RBAC system
INSERT IGNORE INTO `users` (`user_id`, `username`, `password`, `full_name`, `email`, `phone`, `role_id`, `is_active`, `created_at`)
VALUES 
(NULL, 'qc_inspector', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'QC Inspector', 'qc@production.com', '0987654321', 5, 1, NOW());
-- Password: password

-- 2. CREATE SAMPLE SHIFT CLOSURES
INSERT INTO `shift_closures` (`code`, `line_code`, `shift_code`, `project_code`, `lot_code`, `product_code`, `variant`, `qty_finished`, `qty_waste`, `status`, `closed_at`, `closed_by`)
VALUES
-- Closure awaiting QC
('SC-20251102-LINE01-CA1', 'LINE-01', 'CA1', 'PRJ001', 'LOT-2025-001', 'PROD-BP-001', 'Blue Ink', 5000, 150, 'PENDING_QC', '2025-11-02 07:00:00', 'leader'),

-- Closure already verified
('SC-20251101-LINE01-CA2', 'LINE-01', 'CA2', 'PRJ001', 'LOT-2025-002', 'PROD-BP-001', 'Black Ink', 4800, 100, 'VERIFIED', '2025-11-01 15:00:00', 'leader'),

-- Another pending closure with different product
('SC-20251102-LINE02-CA1', 'LINE-02', 'CA1', 'PRJ002', 'LOT-2025-003', 'PROD-BP-002', 'Red Ink', 3500, 200, 'PENDING_QC', '2025-11-02 07:30:00', 'leader');

-- 3. CREATE QC CHECKLIST MASTER FOR SAMPLE PRODUCTS
INSERT INTO `qc_checklist_master` (`code`, `product_code`, `variant`, `item_name`, `criteria`, `sample_size`, `aql`, `category`, `sequence`, `is_active`)
VALUES
-- Checklist for PROD-BP-001 (Blue Ink)
('CHK-BP-001-01', 'PROD-BP-001', NULL, 'Visual Inspection - Body Defects', 'Check for cracks, scratches, discoloration on pen body', 50, 2.5, 'visual', 1, 1),
('CHK-BP-001-02', 'PROD-BP-001', NULL, 'Ink Flow Test', 'Write 10 meters continuously without skipping', 20, 1.5, 'functional', 2, 1),
('CHK-BP-001-03', 'PROD-BP-001', NULL, 'Dimensional Check - Length', 'Length must be 145mm ± 0.5mm', 30, 2.5, 'dimensional', 3, 1),
('CHK-BP-001-04', 'PROD-BP-001', NULL, 'Clip Strength Test', 'Clip must withstand 500g pull force', 15, 1.0, 'functional', 4, 1),
('CHK-BP-001-05', 'PROD-BP-001', NULL, 'Ink Color Consistency', 'Color must match Pantone standard within tolerance', 25, 2.0, 'visual', 5, 1),

-- Checklist for PROD-BP-002 (Red Ink)
('CHK-BP-002-01', 'PROD-BP-002', NULL, 'Visual Inspection - Body Defects', 'Check for cracks, scratches, discoloration on pen body', 50, 2.5, 'visual', 1, 1),
('CHK-BP-002-02', 'PROD-BP-002', NULL, 'Ink Flow Test', 'Write 10 meters continuously without skipping', 20, 1.5, 'functional', 2, 1),
('CHK-BP-002-03', 'PROD-BP-002', NULL, 'Cap Fit Test', 'Cap must fit snugly without wobbling', 30, 2.0, 'functional', 3, 1),
('CHK-BP-002-04', 'PROD-BP-002', NULL, 'Red Ink Color Match', 'Color must match approved red standard', 25, 1.5, 'visual', 4, 1);

-- 4. CREATE SAMPLE QC SESSION (OPEN)
INSERT INTO `qc_sessions` (`code`, `closure_id`, `inspector_code`, `inspector_name`, `started_at`, `status`)
VALUES
('QCS-20251102-0001', 
 (SELECT id FROM shift_closures WHERE code = 'SC-20251102-LINE01-CA1'), 
 'qc_inspector', 
 'QC Inspector', 
 '2025-11-02 08:00:00', 
 'OPEN');

-- 5. CREATE SAMPLE QC ITEMS (partial inspection in progress)
SET @session_id = (SELECT id FROM qc_sessions WHERE code = 'QCS-20251102-0001');

INSERT INTO `qc_items` (`session_id`, `checklist_item_code`, `checklist_item_name`, `measure_value`, `defect_code`, `defect_count`, `severity`, `result`, `note`)
VALUES
(@session_id, 'CHK-BP-001-01', 'Visual Inspection - Body Defects', NULL, NULL, 0, NULL, 'PASS', 'All samples clean'),
(@session_id, 'CHK-BP-001-02', 'Ink Flow Test', NULL, 'INK-SKIP', 2, 'MINOR', 'FAIL', 'Found 2 pens with minor skipping'),
(@session_id, 'CHK-BP-001-03', 'Dimensional Check - Length', 145.2, NULL, 0, NULL, 'PASS', 'Within tolerance');

-- 6. CREATE SAMPLE COMPLETED SESSION WITH DECISION
INSERT INTO `qc_sessions` (`code`, `closure_id`, `inspector_code`, `inspector_name`, `started_at`, `status`)
VALUES
('QCS-20251101-0001', 
 (SELECT id FROM shift_closures WHERE code = 'SC-20251101-LINE01-CA2'), 
 'qc_inspector', 
 'QC Inspector', 
 '2025-11-01 16:00:00', 
 'DECIDED');

SET @completed_session_id = LAST_INSERT_ID();

INSERT INTO `qc_items` (`session_id`, `checklist_item_code`, `checklist_item_name`, `result`, `defect_count`, `severity`, `note`)
VALUES
(@completed_session_id, 'CHK-BP-001-01', 'Visual Inspection - Body Defects', 'PASS', 0, NULL, 'Perfect'),
(@completed_session_id, 'CHK-BP-001-02', 'Ink Flow Test', 'PASS', 0, NULL, 'Smooth'),
(@completed_session_id, 'CHK-BP-001-03', 'Dimensional Check - Length', 'PASS', 0, NULL, 'On spec'),
(@completed_session_id, 'CHK-BP-001-04', 'Clip Strength Test', 'PASS', 0, NULL, 'Good'),
(@completed_session_id, 'CHK-BP-001-05', 'Ink Color Consistency', 'PASS', 0, NULL, 'Perfect match');

INSERT INTO `qc_decisions` (`session_id`, `result`, `aql`, `defect_rate`, `reason`, `decided_at`, `decided_by`)
VALUES
(@completed_session_id, 'APPROVE', 2.5, 0.00, NULL, '2025-11-01 17:00:00', 'qc_inspector');

-- 7. SAMPLE REJECTED SESSION WITH ADJUSTMENT REQUEST
INSERT INTO `shift_closures` (`code`, `line_code`, `shift_code`, `project_code`, `lot_code`, `product_code`, `variant`, `qty_finished`, `qty_waste`, `status`, `closed_at`, `closed_by`)
VALUES
('SC-20251031-LINE01-CA3', 'LINE-01', 'CA3', 'PRJ001', 'LOT-2025-000', 'PROD-BP-001', 'Blue Ink', 2000, 500, 'REJECTED', '2025-10-31 23:00:00', 'leader');

INSERT INTO `qc_sessions` (`code`, `closure_id`, `inspector_code`, `inspector_name`, `started_at`, `status`)
VALUES
('QCS-20251031-0001', 
 (SELECT id FROM shift_closures WHERE code = 'SC-20251031-LINE01-CA3'), 
 'qc_inspector', 
 'QC Inspector', 
 '2025-10-31 23:30:00', 
 'DECIDED');

SET @rejected_session_id = LAST_INSERT_ID();
SET @rejected_closure_id = (SELECT id FROM shift_closures WHERE code = 'SC-20251031-LINE01-CA3');

INSERT INTO `qc_items` (`session_id`, `checklist_item_code`, `checklist_item_name`, `result`, `defect_count`, `severity`, `note`)
VALUES
(@rejected_session_id, 'CHK-BP-001-01', 'Visual Inspection - Body Defects', 'FAIL', 15, 'MAJOR', 'Multiple scratches found'),
(@rejected_session_id, 'CHK-BP-001-02', 'Ink Flow Test', 'FAIL', 8, 'CRITICAL', 'Severe ink skipping issues'),
(@rejected_session_id, 'CHK-BP-001-03', 'Dimensional Check - Length', 'FAIL', 10, 'MAJOR', 'Out of tolerance');

INSERT INTO `qc_decisions` (`session_id`, `result`, `aql`, `defect_rate`, `reason`, `decided_at`, `decided_by`)
VALUES
(@rejected_session_id, 'REJECT', 2.5, 15.50, 
 'Critical defects found:\n- Severe ink flow issues affecting functionality\n- Body scratches beyond acceptable limits\n- Dimensional variance exceeding tolerance\n\nRecommend rework and re-inspection.', 
 '2025-11-01 00:00:00', 'qc_inspector');

INSERT INTO `adjustment_requests` (`code`, `closure_id`, `created_by`, `assigned_to`, `reason`, `status`, `created_at`)
VALUES
('AR-20251031-0001', @rejected_closure_id, 'qc_inspector', 'line_manager', 
 'Critical defects found:\n- Severe ink flow issues affecting functionality\n- Body scratches beyond acceptable limits\n- Dimensional variance exceeding tolerance\n\nRecommend rework and re-inspection.', 
 'OPEN', '2025-11-01 00:00:00');

-- =====================================================
-- VERIFICATION QUERIES
-- =====================================================

SELECT '=== SHIFT CLOSURES ===' as info;
SELECT code, line_code, shift_code, product_code, qty_finished, qty_waste, status, closed_at
FROM shift_closures
ORDER BY closed_at DESC;

SELECT '=== QC SESSIONS ===' as info;
SELECT qs.code, sc.code as closure_code, qs.inspector_name, qs.status, qs.started_at
FROM qc_sessions qs
JOIN shift_closures sc ON qs.closure_id = sc.id
ORDER BY qs.started_at DESC;

SELECT '=== QC CHECKLIST MASTER ===' as info;
SELECT code, product_code, item_name, aql, category
FROM qc_checklist_master
ORDER BY product_code, sequence;

SELECT '=== QC ITEMS (Latest Session) ===' as info;
SELECT qi.checklist_item_name, qi.result, qi.defect_count, qi.severity, qi.note
FROM qc_items qi
WHERE qi.session_id = (SELECT id FROM qc_sessions ORDER BY started_at DESC LIMIT 1);

SELECT '=== QC DECISIONS ===' as info;
SELECT qd.result, qd.aql, qd.defect_rate, qd.decided_at, qs.code as session_code
FROM qc_decisions qd
JOIN qc_sessions qs ON qd.session_id = qs.id
ORDER BY qd.decided_at DESC;

SELECT '=== ADJUSTMENT REQUESTS ===' as info;
SELECT ar.code, sc.code as closure_code, ar.status, ar.created_at
FROM adjustment_requests ar
JOIN shift_closures sc ON ar.closure_id = sc.id
ORDER BY ar.created_at DESC;

-- =====================================================
-- END OF SEED DATA
-- =====================================================
