-- =====================================================
-- Migration 008: QC Finished Goods Control
-- =====================================================
-- Purpose: Enforce rule "Finished goods can only be received 
--          after QC approval"
-- Created: 2025-12-14
-- =====================================================

-- STEP 1: Add columns to finished_receipt table
-- =====================================================
ALTER TABLE `finished_receipt` ADD COLUMN (
  `qc_verified` TINYINT(1) DEFAULT 0 COMMENT '1=QC duyệt, 0=chưa hoặc từ chối',
  `qc_approved_at` DATETIME DEFAULT NULL COMMENT 'Thời gian QC duyệt',
  `qc_approved_by` VARCHAR(50) DEFAULT NULL COMMENT 'User code QC duyệt',
  `requires_qc_approval` TINYINT(1) DEFAULT 1 COMMENT 'Bắt buộc QC duyệt trước khi nhập'
);

-- Add indexes for QC verification checks
ALTER TABLE `finished_receipt` ADD INDEX `idx_qc_verified` (`qc_verified`);
ALTER TABLE `finished_receipt` ADD INDEX `idx_qc_approved_at` (`qc_approved_at`);


-- STEP 2: Verify shift_closures has can_receive_fg column
-- =====================================================
-- Note: shift_closures.can_receive_fg should already exist from QC module
-- If not, uncomment these lines:

-- ALTER TABLE `shift_closures` ADD COLUMN (
--   `can_receive_fg` TINYINT(1) DEFAULT 0 COMMENT 'Flag: Warehouse được nhập TP sau QC approve'
-- );

-- Check if column exists (for safety)
-- SELECT * FROM INFORMATION_SCHEMA.COLUMNS 
-- WHERE TABLE_NAME = 'shift_closures' AND COLUMN_NAME = 'can_receive_fg';


-- STEP 3: Create Trigger - Auto-update shift_closures when QC makes decision
-- =====================================================
DROP TRIGGER IF EXISTS `after_qc_decision_insert`;

DELIMITER $$
CREATE TRIGGER `after_qc_decision_insert` AFTER INSERT ON `qc_decisions`
FOR EACH ROW
BEGIN
  DECLARE v_closure_id INT UNSIGNED;
  
  -- Get the closure_id from qc_sessions
  SELECT closure_id INTO v_closure_id
  FROM qc_sessions
  WHERE id = NEW.session_id
  LIMIT 1;
  
  -- Update shift_closures based on QC decision
  IF NEW.result = 'APPROVE' THEN
    UPDATE shift_closures 
    SET can_receive_fg = 1,
        status = 'VERIFIED',
        updated_at = NOW()
    WHERE id = v_closure_id;
  ELSEIF NEW.result = 'REJECT' THEN
    UPDATE shift_closures 
    SET can_receive_fg = 0,
        status = 'REJECTED',
        updated_at = NOW()
    WHERE id = v_closure_id;
  END IF;
END $$
DELIMITER ;


-- STEP 4: Create Trigger - Prevent receipt creation without QC approval
-- =====================================================
DROP TRIGGER IF EXISTS `before_finished_receipt_insert`;

DELIMITER $$
CREATE TRIGGER `before_finished_receipt_insert` BEFORE INSERT ON `finished_receipt`
FOR EACH ROW
BEGIN
  DECLARE v_can_receive_fg TINYINT(1);
  DECLARE v_status VARCHAR(20);
  
  -- If requires_qc_approval is set to true, check QC approval
  IF NEW.requires_qc_approval = 1 AND NEW.id_finished_report IS NOT NULL THEN
    -- Check if shift_closure is QC approved
    SELECT can_receive_fg, status INTO v_can_receive_fg, v_status
    FROM shift_closures
    WHERE id = NEW.id_finished_report
    LIMIT 1;
    
    -- If not approved, raise error
    IF v_can_receive_fg IS NULL OR v_can_receive_fg = 0 THEN
      SIGNAL SQLSTATE '45000'
      SET MESSAGE_TEXT = 'ERRO_QC_NOT_APPROVED: Shift closure chưa được QC duyệt. Không thể nhập kho!';
    END IF;
    
    IF v_status != 'VERIFIED' THEN
      SIGNAL SQLSTATE '45000'
      SET MESSAGE_TEXT = 'ERROR_CLOSURE_NOT_VERIFIED: Trạng thái shift closure không hợp lệ';
    END IF;
  END IF;
END $$
DELIMITER ;


-- STEP 5: Create View - QC Approved Batches ready for receipt
-- =====================================================
DROP VIEW IF EXISTS `v_qc_approved_batches`;

CREATE VIEW `v_qc_approved_batches` AS
SELECT
  sc.id AS closure_id,
  sc.code AS closure_code,
  sc.line_code,
  sc.shift_code,
  sc.project_code,
  sc.product_code,
  sc.variant,
  sc.qty_finished,
  sc.qty_waste,
  sc.closed_at,
  sc.closed_by,
  p.project_name,
  pr.product_name,
  qd.result AS qc_result,
  qd.aql,
  qd.defect_rate,
  qd.decided_at AS qc_approved_at,
  qd.decided_by AS qc_approved_by,
  CASE 
    WHEN sc.can_receive_fg = 1 AND sc.status = 'VERIFIED' THEN 'Ready'
    WHEN sc.status = 'REJECTED' THEN 'Rejected'
    WHEN sc.status = 'PENDING_QC' THEN 'Pending QC'
    ELSE 'Unknown'
  END AS receipt_status
FROM
  shift_closures sc
  LEFT JOIN qc_sessions qs ON qs.closure_id = sc.id
  LEFT JOIN qc_decisions qd ON qd.session_id = qs.id
  LEFT JOIN project p ON p.id_project = sc.project_code
  LEFT JOIN product pr ON pr.id_product = sc.product_code
WHERE
  sc.can_receive_fg = 1 
  AND sc.status = 'VERIFIED'
  AND qd.result = 'APPROVE'
ORDER BY
  sc.closed_at DESC;


-- STEP 6: Create View - Finished Receipt with QC Info
-- =====================================================
DROP VIEW IF EXISTS `v_finished_receipts_with_qc`;

CREATE VIEW `v_finished_receipts_with_qc` AS
SELECT
  fr.id_receipt,
  fr.receipt_code,
  fr.id_project,
  fr.id_finished_report,
  fr.quantity_received,
  fr.quantity_planned,
  fr.created_date,
  fr.created_by_name,
  fr.status,
  fr.qc_verified,
  fr.qc_approved_at,
  fr.qc_approved_by,
  p.project_name,
  pr.product_name,
  CASE 
    WHEN fr.qc_verified = 1 THEN 'QC Approved'
    ELSE 'Not QC Verified'
  END AS qc_status,
  TIMESTAMPDIFF(HOUR, fr.created_date, NOW()) AS hours_since_receipt
FROM
  finished_receipt fr
  LEFT JOIN project p ON p.id_project = fr.id_project
  LEFT JOIN product pr ON pr.id_product = p.id_product
ORDER BY
  fr.created_date DESC;


-- STEP 7: Seed data - Mark existing receipts as QC verified for backward compatibility
-- =====================================================
-- UPDATE finished_receipt 
-- SET qc_verified = 1,
--     qc_approved_at = created_date,
--     qc_approved_by = created_by_name,
--     requires_qc_approval = 1
-- WHERE qc_verified = 0 OR qc_verified IS NULL;


-- STEP 8: Create Audit log entry for this migration
-- =====================================================
INSERT INTO `audit_log` (
  `user_id`, 
  `username`, 
  `action`, 
  `module`, 
  `record_id`, 
  `old_value`, 
  `new_value`, 
  `ip_address`,
  `user_agent`
) VALUES (
  1,
  'system',
  'migration_run',
  'database',
  8,
  NULL,
  '{"migration":"008_qc_finished_goods_control","description":"Enforce QC approval before finished goods receipt"}',
  '127.0.0.1',
  'Database Migration Script'
);


-- STEP 9: Verification Queries
-- =====================================================
-- Run these to verify the migration is correct:

-- 1. Check if columns were added:
-- SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS 
-- WHERE TABLE_NAME = 'finished_receipt' 
-- AND COLUMN_NAME IN ('qc_verified', 'qc_approved_at', 'qc_approved_by');

-- 2. Check triggers:
-- SELECT TRIGGER_NAME, EVENT_OBJECT_TABLE, ACTION_STATEMENT 
-- FROM INFORMATION_SCHEMA.TRIGGERS
-- WHERE TRIGGER_SCHEMA = 'db_production' 
-- AND TRIGGER_NAME LIKE 'qc%' OR TRIGGER_NAME LIKE 'finished%';

-- 3. Check views:
-- SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES 
-- WHERE TABLE_SCHEMA = 'db_production' AND TABLE_TYPE = 'VIEW';

-- 4. Test QC approved batches:
-- SELECT * FROM v_qc_approved_batches;

-- 5. Test finished receipts with QC:
-- SELECT * FROM v_finished_receipts_with_qc;


-- =====================================================
-- ROLLBACK SCRIPT (if needed)
-- =====================================================
/*
-- Only run if you need to undo this migration:

DROP TRIGGER IF EXISTS `before_finished_receipt_insert`;
DROP TRIGGER IF EXISTS `after_qc_decision_insert`;
DROP VIEW IF EXISTS `v_qc_approved_batches`;
DROP VIEW IF EXISTS `v_finished_receipts_with_qc`;

ALTER TABLE `finished_receipt` DROP COLUMN `qc_verified`;
ALTER TABLE `finished_receipt` DROP COLUMN `qc_approved_at`;
ALTER TABLE `finished_receipt` DROP COLUMN `qc_approved_by`;
ALTER TABLE `finished_receipt` DROP COLUMN `requires_qc_approval`;

ALTER TABLE `finished_receipt` DROP INDEX `idx_qc_verified`;
ALTER TABLE `finished_receipt` DROP INDEX `idx_qc_approved_at`;
*/

-- =====================================================
-- END OF MIGRATION 008
-- =====================================================
