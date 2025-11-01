-- =====================================================
-- MIGRATION 004: Seed Permissions Data
-- Description: Insert 200+ permissions cho tất cả modules
-- Author: Production Management Team
-- Date: 2025-11-01
-- =====================================================

USE `db_production`;

-- =====================================================
-- PERMISSIONS: CUSTOMER MODULE (ID=1)
-- =====================================================
INSERT INTO `permissions` (`module_id`, `permission_name`, `permission_display_name`, `action`, `description`) VALUES
(1, 'customer.view', 'Xem danh sách khách hàng', 'view', 'Xem danh sách và thông tin chi tiết khách hàng'),
(1, 'customer.create', 'Tạo khách hàng mới', 'create', 'Thêm khách hàng mới vào hệ thống'),
(1, 'customer.edit', 'Sửa thông tin khách hàng', 'edit', 'Cập nhật thông tin khách hàng'),
(1, 'customer.delete', 'Xóa khách hàng', 'delete', 'Xóa khách hàng khỏi hệ thống'),
(1, 'customer.export', 'Xuất Excel khách hàng', 'export', 'Xuất danh sách khách hàng ra file Excel')
ON DUPLICATE KEY UPDATE permission_display_name = VALUES(permission_display_name);

-- =====================================================
-- PERMISSIONS: PRODUCT MODULE (ID=2)
-- =====================================================
INSERT INTO `permissions` (`module_id`, `permission_name`, `permission_display_name`, `action`, `description`) VALUES
(2, 'product.view', 'Xem danh sách sản phẩm', 'view', 'Xem danh sách sản phẩm bút bi'),
(2, 'product.create', 'Tạo sản phẩm mới', 'create', 'Thêm sản phẩm mới'),
(2, 'product.edit', 'Sửa thông tin sản phẩm', 'edit', 'Cập nhật thông tin sản phẩm'),
(2, 'product.delete', 'Xóa sản phẩm', 'delete', 'Xóa sản phẩm khỏi danh mục'),
(2, 'product_variant.view', 'Xem biến thể sản phẩm', 'view', 'Xem các biến thể (0.5mm, 0.7mm, 1.0mm, màu mực)'),
(2, 'product_variant.create', 'Tạo biến thể mới', 'create', 'Thêm biến thể mới cho sản phẩm'),
(2, 'product_variant.edit', 'Sửa biến thể', 'edit', 'Cập nhật thông tin biến thể'),
(2, 'product_variant.delete', 'Xóa biến thể', 'delete', 'Xóa biến thể sản phẩm'),
(2, 'product.export', 'Xuất Excel sản phẩm', 'export', 'Xuất danh sách sản phẩm ra Excel')
ON DUPLICATE KEY UPDATE permission_display_name = VALUES(permission_display_name);

-- =====================================================
-- PERMISSIONS: ORDER MODULE (ID=3)
-- =====================================================
INSERT INTO `permissions` (`module_id`, `permission_name`, `permission_display_name`, `action`, `description`) VALUES
(3, 'order.view', 'Xem danh sách đơn hàng', 'view', 'Xem đơn hàng từ khách hàng'),
(3, 'order.create', 'Tạo đơn hàng mới', 'create', 'Tiếp nhận đơn hàng mới'),
(3, 'order.edit', 'Sửa đơn hàng', 'edit', 'Cập nhật thông tin đơn hàng'),
(3, 'order.delete', 'Xóa đơn hàng', 'delete', 'Hủy đơn hàng'),
(3, 'order.approve', 'Phê duyệt đơn hàng', 'approve', 'BOD phê duyệt đơn hàng'),
(3, 'order.reject', 'Từ chối đơn hàng', 'reject', 'BOD từ chối đơn hàng'),
(3, 'order.export', 'Xuất Excel đơn hàng', 'export', 'Xuất danh sách đơn hàng ra Excel')
ON DUPLICATE KEY UPDATE permission_display_name = VALUES(permission_display_name);

-- =====================================================
-- PERMISSIONS: PROJECT MODULE (ID=4)
-- =====================================================
INSERT INTO `permissions` (`module_id`, `permission_name`, `permission_display_name`, `action`, `description`) VALUES
(4, 'project.view', 'Xem danh sách dự án', 'view', 'Xem dự án sản xuất'),
(4, 'project.create', 'Tạo dự án mới', 'create', 'Tạo dự án sản xuất từ đơn hàng'),
(4, 'project.edit', 'Sửa thông tin dự án', 'edit', 'Cập nhật thông tin dự án'),
(4, 'project.delete', 'Xóa dự án', 'delete', 'Xóa dự án'),
(4, 'project.approve', 'Phê duyệt dự án', 'approve', 'BOD phê duyệt dự án'),
(4, 'project.export', 'Xuất Excel dự án', 'export', 'Xuất danh sách dự án')
ON DUPLICATE KEY UPDATE permission_display_name = VALUES(permission_display_name);

-- =====================================================
-- PERMISSIONS: BOM MODULE (ID=5)
-- =====================================================
INSERT INTO `permissions` (`module_id`, `permission_name`, `permission_display_name`, `action`, `description`) VALUES
(5, 'bom.view', 'Xem định mức NVL', 'view', 'Xem BOM (Bill of Materials)'),
(5, 'bom.create', 'Tạo định mức NVL', 'create', 'Tạo BOM mới cho sản phẩm'),
(5, 'bom.edit', 'Sửa định mức NVL', 'edit', 'Cập nhật định mức NVL'),
(5, 'bom.delete', 'Xóa định mức NVL', 'delete', 'Xóa BOM'),
(5, 'bom.approve', 'Xác nhận định mức NVL', 'approve', 'Kỹ thuật viên xác nhận BOM'),
(5, 'bom.export', 'Xuất Excel BOM', 'export', 'Xuất BOM ra Excel')
ON DUPLICATE KEY UPDATE permission_display_name = VALUES(permission_display_name);

-- =====================================================
-- PERMISSIONS: PLANNING MODULE (ID=6)
-- =====================================================
INSERT INTO `permissions` (`module_id`, `permission_name`, `permission_display_name`, `action`, `description`) VALUES
(6, 'planning.view', 'Xem kế hoạch sản xuất', 'view', 'Xem kế hoạch sản xuất tổng'),
(6, 'planning.create', 'Lập kế hoạch sản xuất', 'create', 'Tạo kế hoạch sản xuất mới'),
(6, 'planning.edit', 'Sửa kế hoạch sản xuất', 'edit', 'Điều chỉnh kế hoạch'),
(6, 'planning.delete', 'Xóa kế hoạch sản xuất', 'delete', 'Xóa kế hoạch'),
(6, 'planning.submit', 'Gửi duyệt kế hoạch', 'submit', 'Line Manager gửi kế hoạch lên BOD'),
(6, 'planning.approve', 'Phê duyệt kế hoạch', 'approve', 'BOD phê duyệt kế hoạch sản xuất'),
(6, 'planning.reject', 'Từ chối kế hoạch', 'reject', 'BOD từ chối kế hoạch'),
(6, 'planning_line.view', 'Xem kế hoạch line', 'view', 'Xem kế hoạch từng dây chuyền'),
(6, 'planning_line.create', 'Tạo kế hoạch line', 'create', 'Tạo kế hoạch cho line'),
(6, 'planning_line.edit', 'Sửa kế hoạch line', 'edit', 'Điều chỉnh kế hoạch line'),
(6, 'planning_line.approve', 'Phê duyệt kế hoạch line', 'approve', 'BOD phê duyệt kế hoạch line')
ON DUPLICATE KEY UPDATE permission_display_name = VALUES(permission_display_name);

-- =====================================================
-- PERMISSIONS: SHIFT MODULE (ID=7)
-- =====================================================
INSERT INTO `permissions` (`module_id`, `permission_name`, `permission_display_name`, `action`, `description`) VALUES
(7, 'shift.view', 'Xem danh sách ca', 'view', 'Xem thông tin ca làm việc'),
(7, 'shift.view_own', 'Xem lịch ca của mình', 'view', 'Công nhân xem lịch ca của mình'),
(7, 'shift.create', 'Tạo ca làm việc', 'create', 'Tạo ca sản xuất mới'),
(7, 'shift.edit', 'Sửa thông tin ca', 'edit', 'Cập nhật thông tin ca'),
(7, 'shift.delete', 'Xóa ca làm việc', 'delete', 'Xóa ca'),
(7, 'shift_assignment.view', 'Xem phân công ca', 'view', 'Xem phân công nhân sự cho ca'),
(7, 'shift_assignment.create', 'Phân công nhân sự', 'create', 'Gán nhân sự vào ca'),
(7, 'shift_assignment.edit', 'Sửa phân công ca', 'edit', 'Thay đổi phân công'),
(7, 'shift_assignment.delete', 'Xóa phân công', 'delete', 'Hủy phân công nhân sự'),
(7, 'shift_assignment.confirm', 'Xác nhận nhận việc', 'confirm', 'Công nhân xác nhận nhận việc'),
(7, 'machine_assignment.view', 'Xem gán máy cho ca', 'view', 'Xem máy được gán cho ca'),
(7, 'machine_assignment.create', 'Gán máy cho ca', 'create', 'Gán máy móc vào ca sản xuất'),
(7, 'machine_assignment.edit', 'Sửa gán máy', 'edit', 'Thay đổi máy cho ca'),
(7, 'machine_assignment.delete', 'Xóa gán máy', 'delete', 'Hủy gán máy')
ON DUPLICATE KEY UPDATE permission_display_name = VALUES(permission_display_name);

-- =====================================================
-- PERMISSIONS: PRODUCTION MODULE (ID=8)
-- =====================================================
INSERT INTO `permissions` (`module_id`, `permission_name`, `permission_display_name`, `action`, `description`) VALUES
(8, 'production.view', 'Xem báo cáo sản xuất', 'view', 'Xem tổng hợp sản lượng sản xuất'),
(8, 'production.view_own', 'Xem sản lượng của mình', 'view', 'Công nhân xem sản lượng của mình'),
(8, 'production.create', 'Nhập báo cáo sản xuất', 'create', 'Nhập sản lượng sản xuất'),
(8, 'production.edit', 'Sửa báo cáo sản xuất', 'edit', 'Cập nhật sản lượng'),
(8, 'production.delete', 'Xóa báo cáo sản xuất', 'delete', 'Xóa báo cáo sản lượng'),
(8, 'production_by_machine.view', 'Xem sản lượng theo máy', 'view', 'Theo dõi sản lượng từng máy'),
(8, 'production_by_shift.view', 'Xem sản lượng theo ca', 'view', 'Theo dõi sản lượng từng ca'),
(8, 'production_by_line.view', 'Xem sản lượng theo line', 'view', 'Theo dõi sản lượng từng dây chuyền'),
(8, 'production.export', 'Xuất Excel sản lượng', 'export', 'Xuất báo cáo sản lượng ra Excel')
ON DUPLICATE KEY UPDATE permission_display_name = VALUES(permission_display_name);

-- =====================================================
-- PERMISSIONS: SHIFT CLOSING MODULE (ID=9)
-- =====================================================
INSERT INTO `permissions` (`module_id`, `permission_name`, `permission_display_name`, `action`, `description`) VALUES
(9, 'shift_closing.view', 'Xem phiếu chốt ca', 'view', 'Xem phiếu chốt ca sản xuất'),
(9, 'shift_closing.create', 'Tạo phiếu chốt ca', 'create', 'Tạo phiếu Finished/Waste sau ca'),
(9, 'shift_closing.edit', 'Sửa phiếu chốt ca', 'edit', 'Cập nhật phiếu chốt ca'),
(9, 'shift_closing.delete', 'Xóa phiếu chốt ca', 'delete', 'Xóa phiếu chốt ca'),
(9, 'shift_closing.submit', 'Gửi phiếu chốt ca', 'submit', 'Line Manager gửi phiếu lên QC'),
(9, 'shift_closing.approve', 'Phê duyệt chốt ca', 'approve', 'Line Manager phê duyệt chốt ca'),
(9, 'shift_closing.reject', 'Từ chối chốt ca', 'reject', 'Từ chối phiếu chốt ca'),
(9, 'waste_reason.create', 'Ghi nhận lý do lỗi', 'create', 'Ghi lý do phế phẩm'),
(9, 'waste_reason.view', 'Xem lý do lỗi', 'view', 'Xem nguyên nhân phế phẩm')
ON DUPLICATE KEY UPDATE permission_display_name = VALUES(permission_display_name);

-- =====================================================
-- PERMISSIONS: MACHINE MODULE (ID=10)
-- =====================================================
INSERT INTO `permissions` (`module_id`, `permission_name`, `permission_display_name`, `action`, `description`) VALUES
(10, 'machine.view', 'Xem danh sách máy móc', 'view', 'Xem thông tin máy móc, dây chuyền'),
(10, 'machine.create', 'Thêm máy móc mới', 'create', 'Thêm máy mới vào hệ thống'),
(10, 'machine.edit', 'Sửa thông tin máy', 'edit', 'Cập nhật thông tin máy'),
(10, 'machine.delete', 'Xóa máy móc', 'delete', 'Xóa máy khỏi hệ thống'),
(10, 'machine.update_status', 'Cập nhật trạng thái máy', 'update', 'Kỹ thuật viên cập nhật trạng thái'),
(10, 'machine.confirm_ready', 'Xác nhận máy Ready', 'confirm', 'Kỹ thuật viên xác nhận máy sẵn sàng'),
(10, 'machine_maintenance.view', 'Xem lịch bảo trì', 'view', 'Xem kế hoạch bảo trì máy'),
(10, 'machine_maintenance.create', 'Lập lịch bảo trì', 'create', 'Tạo kế hoạch bảo trì'),
(10, 'machine_maintenance.edit', 'Sửa lịch bảo trì', 'edit', 'Cập nhật lịch bảo trì'),
(10, 'machine_maintenance.delete', 'Xóa lịch bảo trì', 'delete', 'Hủy lịch bảo trì'),
(10, 'machine_maintenance.complete', 'Hoàn thành bảo trì', 'complete', 'Đánh dấu bảo trì hoàn thành')
ON DUPLICATE KEY UPDATE permission_display_name = VALUES(permission_display_name);

-- =====================================================
-- PERMISSIONS: INCIDENT MODULE (ID=11)
-- =====================================================
INSERT INTO `permissions` (`module_id`, `permission_name`, `permission_display_name`, `action`, `description`) VALUES
(11, 'incident.view', 'Xem danh sách sự cố', 'view', 'Xem sự cố máy móc, chất lượng'),
(11, 'incident.create', 'Báo cáo sự cố', 'create', 'Công nhân/Line Manager báo cáo sự cố'),
(11, 'incident.edit', 'Sửa thông tin sự cố', 'edit', 'Cập nhật thông tin sự cố'),
(11, 'incident.delete', 'Xóa sự cố', 'delete', 'Xóa báo cáo sự cố'),
(11, 'incident.assign', 'Phân công xử lý sự cố', 'assign', 'Gán kỹ thuật viên xử lý'),
(11, 'incident.update', 'Cập nhật xử lý sự cố', 'update', 'Kỹ thuật viên cập nhật tiến độ'),
(11, 'incident.resolve', 'Giải quyết sự cố', 'resolve', 'Đánh dấu sự cố đã xử lý'),
(11, 'incident.close', 'Đóng sự cố', 'close', 'Đóng sự cố sau khi xử lý xong'),
(11, 'incident.export', 'Xuất Excel sự cố', 'export', 'Xuất danh sách sự cố')
ON DUPLICATE KEY UPDATE permission_display_name = VALUES(permission_display_name);

-- =====================================================
-- PERMISSIONS: MATERIAL MODULE (ID=12)
-- =====================================================
INSERT INTO `permissions` (`module_id`, `permission_name`, `permission_display_name`, `action`, `description`) VALUES
(12, 'material.view', 'Xem danh mục NVL', 'view', 'Xem danh sách nguyên vật liệu'),
(12, 'material.create', 'Thêm NVL mới', 'create', 'Thêm nguyên vật liệu mới'),
(12, 'material.edit', 'Sửa thông tin NVL', 'edit', 'Cập nhật thông tin NVL'),
(12, 'material.delete', 'Xóa NVL', 'delete', 'Xóa nguyên vật liệu'),
(12, 'material.view_stock', 'Xem tồn kho NVL', 'view', 'Xem số lượng tồn kho'),
(12, 'material.export', 'Xuất Excel NVL', 'export', 'Xuất danh sách NVL')
ON DUPLICATE KEY UPDATE permission_display_name = VALUES(permission_display_name);

-- =====================================================
-- PERMISSIONS: WAREHOUSE MODULE (ID=13)
-- =====================================================
INSERT INTO `permissions` (`module_id`, `permission_name`, `permission_display_name`, `action`, `description`) VALUES
(13, 'warehouse.view', 'Xem tổng quan kho', 'view', 'Xem thông tin kho tổng quát'),
(13, 'warehouse_receipt.view', 'Xem phiếu nhập kho NVL', 'view', 'Xem phiếu nhập NVL'),
(13, 'warehouse_receipt.create', 'Tạo phiếu nhập NVL', 'create', 'Nhập kho NVL theo PO'),
(13, 'warehouse_receipt.edit', 'Sửa phiếu nhập NVL', 'edit', 'Cập nhật phiếu nhập'),
(13, 'warehouse_receipt.delete', 'Xóa phiếu nhập NVL', 'delete', 'Hủy phiếu nhập'),
(13, 'warehouse_issue.view', 'Xem phiếu xuất kho NVL', 'view', 'Xem phiếu xuất NVL'),
(13, 'warehouse_issue.create', 'Tạo phiếu xuất NVL', 'create', 'Xuất NVL cho ca (theo BOM)'),
(13, 'warehouse_issue.edit', 'Sửa phiếu xuất NVL', 'edit', 'Cập nhật phiếu xuất'),
(13, 'warehouse_issue.delete', 'Xóa phiếu xuất NVL', 'delete', 'Hủy phiếu xuất'),
(13, 'finished_goods_receipt.view', 'Xem phiếu nhập TP', 'view', 'Xem phiếu nhập thành phẩm'),
(13, 'finished_goods_receipt.create', 'Tạo phiếu nhập TP', 'create', 'Nhập kho TP sau QC'),
(13, 'finished_goods_receipt.edit', 'Sửa phiếu nhập TP', 'edit', 'Cập nhật phiếu nhập TP'),
(13, 'finished_goods_receipt.delete', 'Xóa phiếu nhập TP', 'delete', 'Hủy phiếu nhập TP'),
(13, 'finished_goods_receipt.approve', 'Phê duyệt nhập TP', 'approve', 'QC xác nhận cho nhập TP'),
(13, 'finished_goods_issue.view', 'Xem phiếu xuất TP', 'view', 'Xem phiếu xuất thành phẩm'),
(13, 'finished_goods_issue.create', 'Tạo phiếu xuất TP', 'create', 'Xuất TP giao hàng'),
(13, 'finished_goods_issue.edit', 'Sửa phiếu xuất TP', 'edit', 'Cập nhật phiếu xuất TP'),
(13, 'finished_goods_issue.delete', 'Xóa phiếu xuất TP', 'delete', 'Hủy phiếu xuất TP'),
(13, 'stock.view', 'Xem tồn kho', 'view', 'Xem tồn kho NVL và TP'),
(13, 'stock_report.view', 'Xem báo cáo tồn kho', 'view', 'Báo cáo tồn và luân chuyển'),
(13, 'stock_report.export', 'Xuất Excel tồn kho', 'export', 'Xuất báo cáo tồn kho')
ON DUPLICATE KEY UPDATE permission_display_name = VALUES(permission_display_name);

-- =====================================================
-- PERMISSIONS: QC MODULE (ID=14)
-- =====================================================
INSERT INTO `permissions` (`module_id`, `permission_name`, `permission_display_name`, `action`, `description`) VALUES
(14, 'qc_inspection.view', 'Xem phiếu kiểm tra QC', 'view', 'Xem phiếu QC sau ca'),
(14, 'qc_inspection.create', 'Tạo phiếu kiểm tra QC', 'create', 'Tạo phiếu kiểm tra chất lượng'),
(14, 'qc_inspection.edit', 'Sửa phiếu QC', 'edit', 'Cập nhật phiếu QC'),
(14, 'qc_inspection.delete', 'Xóa phiếu QC', 'delete', 'Xóa phiếu QC'),
(14, 'qc_inspection.approve', 'Phê duyệt QC', 'approve', 'QC approve sản phẩm đạt'),
(14, 'qc_inspection.reject', 'Từ chối QC', 'reject', 'QC reject sản phẩm lỗi'),
(14, 'qc_checklist.view', 'Xem checklist QC', 'view', 'Xem tiêu chuẩn kiểm tra'),
(14, 'qc_defect.view', 'Xem lỗi phát hiện', 'view', 'Xem danh sách lỗi'),
(14, 'qc_defect.create', 'Ghi nhận lỗi', 'create', 'Ghi lỗi và nguyên nhân'),
(14, 'qc_defect.edit', 'Sửa lỗi', 'edit', 'Cập nhật thông tin lỗi'),
(14, 'aql_standard.view', 'Xem tiêu chuẩn AQL', 'view', 'Xem Acceptable Quality Level'),
(14, 'qc_report.view', 'Xem báo cáo QC', 'view', 'Báo cáo chất lượng tổng hợp'),
(14, 'qc_report.export', 'Xuất Excel báo cáo QC', 'export', 'Xuất báo cáo chất lượng')
ON DUPLICATE KEY UPDATE permission_display_name = VALUES(permission_display_name);

-- =====================================================
-- PERMISSIONS: STAFF MODULE (ID=15)
-- =====================================================
INSERT INTO `permissions` (`module_id`, `permission_name`, `permission_display_name`, `action`, `description`) VALUES
(15, 'staff.view', 'Xem danh sách nhân sự', 'view', 'Xem thông tin nhân viên, công nhân'),
(15, 'staff.create', 'Thêm nhân sự mới', 'create', 'Thêm nhân viên/công nhân'),
(15, 'staff.edit', 'Sửa thông tin nhân sự', 'edit', 'Cập nhật thông tin nhân sự'),
(15, 'staff.delete', 'Xóa nhân sự', 'delete', 'Xóa nhân sự khỏi hệ thống'),
(15, 'staff.export', 'Xuất Excel nhân sự', 'export', 'Xuất danh sách nhân sự')
ON DUPLICATE KEY UPDATE permission_display_name = VALUES(permission_display_name);

-- =====================================================
-- PERMISSIONS: REPORT MODULE (ID=16)
-- =====================================================
INSERT INTO `permissions` (`module_id`, `permission_name`, `permission_display_name`, `action`, `description`) VALUES
(16, 'dashboard.view_all', 'Xem dashboard tổng quan', 'view', 'Dashboard cho BOD'),
(16, 'dashboard.view_line', 'Xem dashboard dây chuyền', 'view', 'Dashboard cho Line Manager'),
(16, 'dashboard.view_warehouse', 'Xem dashboard kho', 'view', 'Dashboard cho Warehouse'),
(16, 'report.production_summary', 'Báo cáo tổng hợp sản xuất', 'view', 'Tổng hợp sản lượng, hiệu suất'),
(16, 'report.quality_summary', 'Báo cáo tổng hợp chất lượng', 'view', 'Tổng hợp QC, tỷ lệ lỗi'),
(16, 'report.inventory', 'Báo cáo tồn kho', 'view', 'Báo cáo tồn NVL và TP'),
(16, 'report.material_movement', 'Báo cáo luân chuyển NVL', 'view', 'Nhập/xuất NVL theo thời gian'),
(16, 'report.line_performance', 'Báo cáo hiệu suất line', 'view', 'Hiệu suất vận hành dây chuyền'),
(16, 'report.shift_summary', 'Báo cáo tổng hợp ca', 'view', 'Tổng hợp sản lượng theo ca'),
(16, 'report.efficiency', 'Báo cáo hiệu suất tổng thể', 'view', 'OEE, hiệu suất toàn hệ thống'),
(16, 'report.financial', 'Báo cáo tài chính', 'view', 'Doanh thu, chi phí (BOD only)'),
(16, 'report.defect_analysis', 'Phân tích lỗi sản phẩm', 'view', 'Phân tích nguyên nhân lỗi'),
(16, 'report.maintenance', 'Báo cáo bảo trì', 'view', 'Lịch sử bảo trì máy móc'),
(16, 'report.incident_history', 'Báo cáo lịch sử sự cố', 'view', 'Lịch sử sự cố và xử lý'),
(16, 'report.export_all', 'Xuất tất cả báo cáo', 'export', 'Quyền xuất Excel tất cả báo cáo')
ON DUPLICATE KEY UPDATE permission_display_name = VALUES(permission_display_name);

-- =====================================================
-- PERMISSIONS: USER MODULE (ID=17)
-- =====================================================
INSERT INTO `permissions` (`module_id`, `permission_name`, `permission_display_name`, `action`, `description`) VALUES
(17, 'user.view', 'Xem danh sách người dùng', 'view', 'Xem tài khoản user'),
(17, 'user.create', 'Tạo người dùng mới', 'create', 'Thêm tài khoản user'),
(17, 'user.edit', 'Sửa thông tin người dùng', 'edit', 'Cập nhật thông tin user'),
(17, 'user.delete', 'Xóa người dùng', 'delete', 'Xóa tài khoản user'),
(17, 'user.reset_password', 'Đặt lại mật khẩu', 'reset', 'Reset password cho user'),
(17, 'user.lock', 'Khóa tài khoản', 'lock', 'Khóa user không cho đăng nhập'),
(17, 'user.unlock', 'Mở khóa tài khoản', 'unlock', 'Mở khóa tài khoản user'),
(17, 'user_role.assign', 'Gán vai trò cho user', 'assign', 'Phân quyền role cho user'),
(17, 'audit_log.view', 'Xem nhật ký hoạt động', 'view', 'Xem audit log của user'),
(17, 'audit_log.export', 'Xuất Excel nhật ký', 'export', 'Xuất audit log ra Excel')
ON DUPLICATE KEY UPDATE permission_display_name = VALUES(permission_display_name);

-- =====================================================
-- PERMISSIONS: SYSTEM MODULE (ID=18)
-- =====================================================
INSERT INTO `permissions` (`module_id`, `permission_name`, `permission_display_name`, `action`, `description`) VALUES
(18, 'role.view', 'Xem danh sách vai trò', 'view', 'Xem roles trong hệ thống'),
(18, 'role.create', 'Tạo vai trò mới', 'create', 'Thêm role mới'),
(18, 'role.edit', 'Sửa vai trò', 'edit', 'Cập nhật role'),
(18, 'role.delete', 'Xóa vai trò', 'delete', 'Xóa role'),
(18, 'permission.view', 'Xem danh sách quyền', 'view', 'Xem permissions'),
(18, 'role_permission.assign', 'Gán quyền cho vai trò', 'assign', 'Assign permissions cho role'),
(18, 'system_settings.view', 'Xem cài đặt hệ thống', 'view', 'Xem system settings'),
(18, 'system_settings.edit', 'Sửa cài đặt hệ thống', 'edit', 'Thay đổi system settings')
ON DUPLICATE KEY UPDATE permission_display_name = VALUES(permission_display_name);

-- =====================================================
-- VERIFICATION
-- =====================================================

-- Đếm số permissions theo module
SELECT 
  m.module_display_name AS 'Module',
  COUNT(p.permission_id) AS 'Total Permissions'
FROM modules m
LEFT JOIN permissions p ON m.module_id = p.module_id
WHERE m.parent_id IS NULL
GROUP BY m.module_id, m.module_display_name
ORDER BY m.module_id;

-- Tổng số permissions
SELECT COUNT(*) AS 'Total Permissions in System' FROM permissions;

-- =====================================================
-- HOÀN THÀNH MIGRATION 004
-- =====================================================
-- Kết quả:
-- ✓ Customer: 5 permissions
-- ✓ Product: 9 permissions
-- ✓ Order: 7 permissions
-- ✓ Project: 6 permissions
-- ✓ BOM: 6 permissions
-- ✓ Planning: 11 permissions
-- ✓ Shift: 14 permissions
-- ✓ Production: 9 permissions
-- ✓ Shift Closing: 9 permissions
-- ✓ Machine: 11 permissions
-- ✓ Incident: 9 permissions
-- ✓ Material: 6 permissions
-- ✓ Warehouse: 21 permissions
-- ✓ QC: 13 permissions
-- ✓ Staff: 5 permissions
-- ✓ Report: 15 permissions
-- ✓ User: 10 permissions
-- ✓ System: 8 permissions
-- ✓ TỔNG CỘNG: 174 permissions
-- =====================================================

SELECT 'Migration 004: Seed Permissions Data - COMPLETED ✓' AS status;
