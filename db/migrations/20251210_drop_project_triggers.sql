-- This migration removes the triggers from the `project` table.
-- From now on, all stock logic will be handled exclusively by the PHP application (OrderModel).
-- This ensures a single source of truth and resolves the logic conflicts.

DROP TRIGGER IF EXISTS `trg_project_after_insert_deduct_stock`;
DROP TRIGGER IF EXISTS `trg_project_after_update_deduct_stock`;
DROP TRIGGER IF EXISTS `trg_project_after_delete_restore_stock`;

DROP TRIGGER IF EXISTS `trg_project_after_insert_deduct_materials`;
DROP TRIGGER IF EXISTS `trg_project_after_update_deduct_materials`;
DROP TRIGGER IF EXISTS `trg_project_after_delete_restore_materials`;


-- Log this critical action
INSERT INTO `audit_log` (`username`, `action`, `module`, `new_value`) 
VALUES ('system_migration', 'drop_triggers', 'project', '{"details": "Removed all stock management triggers from the project table to centralize logic in the application layer."}');

COMMIT;
