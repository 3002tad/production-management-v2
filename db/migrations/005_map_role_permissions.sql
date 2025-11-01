-- =====================================================
-- MIGRATION 005: Map Role Permissions
-- Description: Gán permissions cho 7 roles theo nghiệp vụ thực tế
-- Author: Production Management Team
-- Date: 2025-11-01
-- =====================================================

USE `db_production`;

-- =====================================================
-- ROLE 1: BAN GIÁM ĐỐC (BOD)
-- Level: 100
-- Nghiệp vụ: Quản trị cấp cao, phê duyệt, báo cáo tổng hợp
-- =====================================================

-- BOD: Full access Customer, Product, Order, Project
INSERT INTO role_permissions (role_id, permission_id)
SELECT 1, permission_id FROM permissions 
WHERE permission_name IN (
  -- Customer (full CRUD)
  'customer.view', 'customer.create', 'customer.edit', 'customer.delete', 'customer.export',
  
  -- Product & Variants (full CRUD)
  'product.view', 'product.create', 'product.edit', 'product.delete', 'product.export',
  'product_variant.view', 'product_variant.create', 'product_variant.edit', 'product_variant.delete',
  
  -- Order (full + approve)
  'order.view', 'order.create', 'order.edit', 'order.delete', 'order.approve', 'order.reject', 'order.export',
  
  -- Project (view + approve)
  'project.view', 'project.create', 'project.edit', 'project.approve', 'project.export',
  
  -- BOM (view only, kỹ thuật viên approve)
  'bom.view', 'bom.export'
)
ON DUPLICATE KEY UPDATE role_id = role_id;

-- BOD: Planning - view, approve
INSERT INTO role_permissions (role_id, permission_id)
SELECT 1, permission_id FROM permissions 
WHERE permission_name IN (
  'planning.view', 'planning.create', 'planning.approve', 'planning.reject',
  'planning_line.view', 'planning_line.approve'
)
ON DUPLICATE KEY UPDATE role_id = role_id;

-- BOD: Shift - view only
INSERT INTO role_permissions (role_id, permission_id)
SELECT 1, permission_id FROM permissions 
WHERE permission_name IN (
  'shift.view', 'shift_assignment.view', 'machine_assignment.view'
)
ON DUPLICATE KEY UPDATE role_id = role_id;

-- BOD: Production, Shift Closing - view only
INSERT INTO role_permissions (role_id, permission_id)
SELECT 1, permission_id FROM permissions 
WHERE permission_name IN (
  'production.view', 'production_by_machine.view', 'production_by_shift.view', 'production_by_line.view',
  'shift_closing.view', 'waste_reason.view'
)
ON DUPLICATE KEY UPDATE role_id = role_id;

-- BOD: Dashboard & Reports - FULL ACCESS
INSERT INTO role_permissions (role_id, permission_id)
SELECT 1, permission_id FROM permissions 
WHERE module_id = 16  -- Report module
ON DUPLICATE KEY UPDATE role_id = role_id;

-- =====================================================
-- ROLE 2: TRƯỞNG DÂY CHUYỀN (LINE MANAGER)
-- Level: 70
-- Nghiệp vụ: Vận hành line, phân ca, điều phối, chốt ca
-- =====================================================

-- Line Manager: Staff (view, edit nhân sự line)
INSERT INTO role_permissions (role_id, permission_id)
SELECT 2, permission_id FROM permissions 
WHERE permission_name IN (
  'staff.view', 'staff.edit', 'staff.export'
)
ON DUPLICATE KEY UPDATE role_id = role_id;

-- Line Manager: Machine (full CRUD + maintenance)
INSERT INTO role_permissions (role_id, permission_id)
SELECT 2, permission_id FROM permissions 
WHERE module_id = 10  -- Machine module - FULL ACCESS
ON DUPLICATE KEY UPDATE role_id = role_id;

-- Line Manager: Planning (create, edit, submit)
INSERT INTO role_permissions (role_id, permission_id)
SELECT 2, permission_id FROM permissions 
WHERE permission_name IN (
  'planning.view', 'planning.create', 'planning.edit', 'planning.submit',
  'planning_line.view', 'planning_line.create', 'planning_line.edit'
)
ON DUPLICATE KEY UPDATE role_id = role_id;

-- Line Manager: Shift (full CRUD + assignment)
INSERT INTO role_permissions (role_id, permission_id)
SELECT 2, permission_id FROM permissions 
WHERE module_id = 7  -- Shift module - FULL ACCESS (trừ worker views)
  AND permission_name NOT LIKE '%.view_own'
ON DUPLICATE KEY UPDATE role_id = role_id;

-- Line Manager: Production (view all levels)
INSERT INTO role_permissions (role_id, permission_id)
SELECT 2, permission_id FROM permissions 
WHERE permission_name IN (
  'production.view', 'production.create', 'production.edit',
  'production_by_machine.view', 'production_by_shift.view', 'production_by_line.view',
  'production.export'
)
ON DUPLICATE KEY UPDATE role_id = role_id;

-- Line Manager: Incident (full CRUD + handle)
INSERT INTO role_permissions (role_id, permission_id)
SELECT 2, permission_id FROM permissions 
WHERE module_id = 11  -- Incident module - FULL ACCESS
ON DUPLICATE KEY UPDATE role_id = role_id;

-- Line Manager: Shift Closing (full CRUD + approve)
INSERT INTO role_permissions (role_id, permission_id)
SELECT 2, permission_id FROM permissions 
WHERE module_id = 9  -- Shift Closing - FULL ACCESS
ON DUPLICATE KEY UPDATE role_id = role_id;

-- Line Manager: Reports (line performance, shift summary)
INSERT INTO role_permissions (role_id, permission_id)
SELECT 2, permission_id FROM permissions 
WHERE permission_name IN (
  'dashboard.view_line',
  'report.production_summary', 'report.line_performance', 'report.shift_summary',
  'report.defect_analysis', 'report.incident_history'
)
ON DUPLICATE KEY UPDATE role_id = role_id;

-- Line Manager: View Product, Project, BOM
INSERT INTO role_permissions (role_id, permission_id)
SELECT 2, permission_id FROM permissions 
WHERE permission_name IN (
  'product.view', 'product_variant.view', 'project.view', 'bom.view'
)
ON DUPLICATE KEY UPDATE role_id = role_id;

-- =====================================================
-- ROLE 3: NHÂN VIÊN KHO (WAREHOUSE STAFF)
-- Level: 50
-- Nghiệp vụ: Quản lý NVL & thành phẩm, chứng từ kho
-- =====================================================

-- Warehouse: Material (full CRUD)
INSERT INTO role_permissions (role_id, permission_id)
SELECT 3, permission_id FROM permissions 
WHERE module_id = 12  -- Material module - FULL ACCESS
ON DUPLICATE KEY UPDATE role_id = role_id;

-- Warehouse: Warehouse module (full CRUD all warehouse operations)
INSERT INTO role_permissions (role_id, permission_id)
SELECT 3, permission_id FROM permissions 
WHERE module_id = 13  -- Warehouse module - FULL ACCESS
ON DUPLICATE KEY UPDATE role_id = role_id;

-- Warehouse: BOM (view để tính NVL cần xuất)
INSERT INTO role_permissions (role_id, permission_id)
SELECT 3, permission_id FROM permissions 
WHERE permission_name IN (
  'bom.view'
)
ON DUPLICATE KEY UPDATE role_id = role_id;

-- Warehouse: Project (view để giao hàng)
INSERT INTO role_permissions (role_id, permission_id)
SELECT 3, permission_id FROM permissions 
WHERE permission_name IN (
  'project.view', 'order.view'
)
ON DUPLICATE KEY UPDATE role_id = role_id;

-- Warehouse: Shift (view để biết ca nào cần xuất NVL)
INSERT INTO role_permissions (role_id, permission_id)
SELECT 3, permission_id FROM permissions 
WHERE permission_name IN (
  'shift.view', 'planning.view'
)
ON DUPLICATE KEY UPDATE role_id = role_id;

-- Warehouse: Stock reports
INSERT INTO role_permissions (role_id, permission_id)
SELECT 3, permission_id FROM permissions 
WHERE permission_name IN (
  'dashboard.view_warehouse',
  'stock.view', 'stock_report.view', 'stock_report.export',
  'report.inventory', 'report.material_movement'
)
ON DUPLICATE KEY UPDATE role_id = role_id;

-- =====================================================
-- ROLE 4: QUẢN TRỊ VIÊN HỆ THỐNG (SYSTEM ADMIN)
-- Level: 90
-- Nghiệp vụ: Quản lý tài khoản & phân quyền
-- =====================================================

-- System Admin: User Management (full CRUD)
INSERT INTO role_permissions (role_id, permission_id)
SELECT 4, permission_id FROM permissions 
WHERE module_id = 17  -- User module - FULL ACCESS
ON DUPLICATE KEY UPDATE role_id = role_id;

-- System Admin: System Settings (full CRUD)
INSERT INTO role_permissions (role_id, permission_id)
SELECT 4, permission_id FROM permissions 
WHERE module_id = 18  -- System module - FULL ACCESS
ON DUPLICATE KEY UPDATE role_id = role_id;

-- System Admin: View other modules (read-only cho troubleshooting)
INSERT INTO role_permissions (role_id, permission_id)
SELECT 4, permission_id FROM permissions 
WHERE action = 'view' AND module_id NOT IN (17, 18)
ON DUPLICATE KEY UPDATE role_id = role_id;

-- =====================================================
-- ROLE 5: NHÂN VIÊN KIỂM SOÁT CHẤT LƯỢNG (QC STAFF)
-- Level: 60
-- Nghiệp vụ: Kiểm soát chất lượng sau ca
-- =====================================================

-- QC: QC module (full CRUD + approve/reject)
INSERT INTO role_permissions (role_id, permission_id)
SELECT 5, permission_id FROM permissions 
WHERE module_id = 14  -- QC module - FULL ACCESS
ON DUPLICATE KEY UPDATE role_id = role_id;

-- QC: Shift Closing (view để biết ca nào cần QC)
INSERT INTO role_permissions (role_id, permission_id)
SELECT 5, permission_id FROM permissions 
WHERE permission_name IN (
  'shift_closing.view', 'waste_reason.view'
)
ON DUPLICATE KEY UPDATE role_id = role_id;

-- QC: Finished Goods Receipt (approve để cho phép nhập kho TP)
INSERT INTO role_permissions (role_id, permission_id)
SELECT 5, permission_id FROM permissions 
WHERE permission_name IN (
  'finished_goods_receipt.view', 'finished_goods_receipt.approve'
)
ON DUPLICATE KEY UPDATE role_id = role_id;

-- QC: Product & Variant (view)
INSERT INTO role_permissions (role_id, permission_id)
SELECT 5, permission_id FROM permissions 
WHERE permission_name IN (
  'product.view', 'product_variant.view', 'project.view'
)
ON DUPLICATE KEY UPDATE role_id = role_id;

-- QC: Reports (quality reports)
INSERT INTO role_permissions (role_id, permission_id)
SELECT 5, permission_id FROM permissions 
WHERE permission_name IN (
  'report.quality_summary', 'report.defect_analysis', 'qc_report.view', 'qc_report.export'
)
ON DUPLICATE KEY UPDATE role_id = role_id;

-- =====================================================
-- ROLE 6: NHÂN VIÊN KỸ THUẬT (TECHNICAL STAFF)
-- Level: 60
-- Nghiệp vụ: Bảo trì & xử lý sự cố, xác nhận máy
-- =====================================================

-- Technical: Incident (view, update, resolve, close)
INSERT INTO role_permissions (role_id, permission_id)
SELECT 6, permission_id FROM permissions 
WHERE permission_name IN (
  'incident.view', 'incident.update', 'incident.resolve', 'incident.close', 'incident.export'
)
ON DUPLICATE KEY UPDATE role_id = role_id;

-- Technical: Machine (view, update status, confirm ready, maintenance)
INSERT INTO role_permissions (role_id, permission_id)
SELECT 6, permission_id FROM permissions 
WHERE permission_name IN (
  'machine.view', 'machine.update_status', 'machine.confirm_ready',
  'machine_maintenance.view', 'machine_maintenance.create', 'machine_maintenance.edit',
  'machine_maintenance.delete', 'machine_maintenance.complete'
)
ON DUPLICATE KEY UPDATE role_id = role_id;

-- Technical: BOM (view + approve định mức NVL)
INSERT INTO role_permissions (role_id, permission_id)
SELECT 6, permission_id FROM permissions 
WHERE permission_name IN (
  'bom.view', 'bom.approve'
)
ON DUPLICATE KEY UPDATE role_id = role_id;

-- Technical: View shift, planning để biết lịch máy
INSERT INTO role_permissions (role_id, permission_id)
SELECT 6, permission_id FROM permissions 
WHERE permission_name IN (
  'shift.view', 'planning.view', 'machine_assignment.view'
)
ON DUPLICATE KEY UPDATE role_id = role_id;

-- Technical: Reports (maintenance, incident history)
INSERT INTO role_permissions (role_id, permission_id)
SELECT 6, permission_id FROM permissions 
WHERE permission_name IN (
  'report.maintenance', 'report.incident_history'
)
ON DUPLICATE KEY UPDATE role_id = role_id;

-- =====================================================
-- ROLE 7: CÔNG NHÂN SẢN XUẤT (WORKER)
-- Level: 10
-- Nghiệp vụ: Thực hiện ca & phản hồi hiện trường
-- =====================================================

-- Worker: Shift (view own schedule)
INSERT INTO role_permissions (role_id, permission_id)
SELECT 7, permission_id FROM permissions 
WHERE permission_name IN (
  'shift.view_own',
  'shift_assignment.view',
  'shift_assignment.confirm'
)
ON DUPLICATE KEY UPDATE role_id = role_id;

-- Worker: Production (view own production)
INSERT INTO role_permissions (role_id, permission_id)
SELECT 7, permission_id FROM permissions 
WHERE permission_name IN (
  'production.view_own'
)
ON DUPLICATE KEY UPDATE role_id = role_id;

-- Worker: Incident (create để báo cáo sự cố)
INSERT INTO role_permissions (role_id, permission_id)
SELECT 7, permission_id FROM permissions 
WHERE permission_name IN (
  'incident.create'
)
ON DUPLICATE KEY UPDATE role_id = role_id;

-- Worker: View project, product (để biết đang làm gì)
INSERT INTO role_permissions (role_id, permission_id)
SELECT 7, permission_id FROM permissions 
WHERE permission_name IN (
  'project.view', 'product.view', 'product_variant.view'
)
ON DUPLICATE KEY UPDATE role_id = role_id;

-- =====================================================
-- VERIFICATION: Kiểm tra số permissions cho mỗi role
-- =====================================================

SELECT 
  r.role_name,
  r.role_display_name,
  r.level,
  COUNT(rp.permission_id) AS total_permissions
FROM roles r
LEFT JOIN role_permissions rp ON r.role_id = rp.role_id
GROUP BY r.role_id, r.role_name, r.role_display_name, r.level
ORDER BY r.level DESC;

-- Chi tiết permissions cho từng role
SELECT 
  r.role_display_name AS 'Role',
  m.module_display_name AS 'Module',
  p.permission_display_name AS 'Permission',
  p.action AS 'Action'
FROM role_permissions rp
JOIN roles r ON rp.role_id = r.role_id
JOIN permissions p ON rp.permission_id = p.permission_id
JOIN modules m ON p.module_id = m.module_id
WHERE r.role_id = 1  -- Thay đổi để xem từng role
ORDER BY m.module_id, p.permission_name;

-- =====================================================
-- HOÀN THÀNH MIGRATION 005
-- =====================================================
-- Kết quả dự kiến:
-- ✓ BOD (role_id=1): ~70+ permissions (strategic + reports)
-- ✓ Line Manager (role_id=2): ~80+ permissions (operational)
-- ✓ Warehouse Staff (role_id=3): ~40+ permissions (inventory)
-- ✓ System Admin (role_id=4): ~20+ permissions (user management)
-- ✓ QC Staff (role_id=5): ~25+ permissions (quality control)
-- ✓ Technical Staff (role_id=6): ~20+ permissions (maintenance)
-- ✓ Worker (role_id=7): ~10 permissions (minimal, own data only)
-- =====================================================

SELECT 'Migration 005: Map Role Permissions - COMPLETED ✓' AS status;
