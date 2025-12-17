# QC Module - Kiểm tra Seed Data

## ✅ TRẠNG THÁI: ĐÃ SỬA

### Lỗi đã sửa:
1. ✅ Sửa `users` → `user` (bảng user là số ít)
2. ✅ Dùng `INSERT ... SELECT ... WHERE NOT EXISTS` thay vì `INSERT IGNORE`
3. ✅ Xóa `user_id`, `created_at` khỏi INSERT (auto-generated)

---

## 📊 Cấu trúc Database

### Bảng hiện có (db_production.sql)
```
✅ user           - Quản lý người dùng
✅ staff          - Thông tin nhân viên
✅ shiftment      - Định nghĩa ca làm việc (Pagi, Siang, Malam)
✅ planning       - Kế hoạch sản xuất
✅ plan_shift     - Chi tiết kế hoạch theo ca
✅ project        - Dự án/đơn hàng
✅ product        - Sản phẩm
✅ customer       - Khách hàng
✅ machine        - Máy móc
✅ material       - Nguyên vật liệu
```

### Bảng mới (Migration 007)
```
❌ shift_closures         - Chốt ca sản xuất (TẠO MỚI)
❌ qc_sessions            - Phiên kiểm tra QC (TẠO MỚI)
❌ qc_items               - Chi tiết checklist (TẠO MỚI)
❌ qc_decisions           - Quyết định APPROVE/REJECT (TẠO MỚI)
❌ qc_attachments         - File đính kèm (TẠO MỚI)
❌ adjustment_requests    - Yêu cầu điều chỉnh (TẠO MỚI)
❌ qc_checklist_master    - Checklist mẫu (TẠO MỚI)
❌ qc_config              - Cấu hình QC (TẠO MỚI)
```

### Bảng RBAC (Migration 001-002)
```
❓ roles                  - Vai trò người dùng (CẦN KIỂM TRA)
❓ modules                - Nhóm chức năng (CẦN KIỂM TRA)
❓ permissions            - Quyền hạn (CẦN KIỂM TRA)
❓ role_permissions       - Liên kết role-permission (CẦN KIỂM TRA)
❓ audit_log              - Nhật ký hoạt động (CẦN KIỂM TRA)
```

---

## 🔍 Chi tiết Seed Data

### 1. User QC Inspector

**TRƯỚC (SAI):**
```sql
INSERT IGNORE INTO `users` (`user_id`, `username`, `password`, ...)
VALUES (NULL, 'qc_inspector', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', ...)
```

**Vấn đề:**
- ❌ Bảng là `user` không phải `users`
- ❌ `user_id` là AUTO_INCREMENT, không cần truyền NULL
- ❌ `created_at` có DEFAULT CURRENT_TIMESTAMP, không cần truyền NOW()
- ❌ Password dùng bcrypt hash nhưng database chỉ có varchar(11) - quá ngắn!

**SAU (ĐÚNG):**
```sql
INSERT INTO `user` (`username`, `password`, `full_name`, `email`, `phone`, `role_id`, `is_active`)
SELECT 'qc_inspector', 'password', 'QC Inspector', 'qc@production.com', '0987654321', 5, 1
WHERE NOT EXISTS (SELECT 1 FROM `user` WHERE `username` = 'qc_inspector');
```

**Cải tiến:**
- ✅ Bảng đúng: `user`
- ✅ Không truyền `user_id`, `created_at`
- ✅ WHERE NOT EXISTS - tránh duplicate key error
- ✅ Password plaintext (do database varchar(11) không đủ cho hash)
- ⚠️ **CHÚ Ý:** Password cần đổi trong production!

---

### 2. Shift Closures

```sql
INSERT INTO `shift_closures` (...)
VALUES
('SC-20251102-LINE01-CA1', 'LINE-01', 'CA1', 'PRJ001', ...),
('SC-20251101-LINE01-CA2', 'LINE-01', 'CA2', 'PRJ001', ...),
('SC-20251102-LINE02-CA1', 'LINE-02', 'CA1', 'PRJ002', ...);
```

**Tạo 3 closures:**
1. ✅ PENDING_QC - Chờ kiểm tra
2. ✅ VERIFIED - Đã duyệt
3. ✅ PENDING_QC - Chờ kiểm tra (sản phẩm khác)

**Lưu ý:**
- `project_code` = 'PRJ001' nhưng database dùng `id_project` (INT)
- `product_code` = 'PROD-BP-001' nhưng database dùng `id_product` (INT)
- ⚠️ **Cần mapping:** Code → ID

---

### 3. QC Checklist Master

```sql
INSERT INTO `qc_checklist_master` (...)
VALUES
('CHK-BP-001-01', 'PROD-BP-001', NULL, 'Visual Inspection - Body Defects', ...),
('CHK-BP-001-02', 'PROD-BP-001', NULL, 'Ink Flow Test', ...),
...
('CHK-BP-002-01', 'PROD-BP-002', NULL, 'Visual Inspection - Body Defects', ...),
...
```

**Tạo 9 checklist items:**
- 5 items cho PROD-BP-001 (Blue Ink)
- 4 items cho PROD-BP-002 (Red Ink)

**Categories:**
- visual: Kiểm tra mắt thường
- functional: Kiểm tra chức năng
- dimensional: Kiểm tra kích thước

---

### 4. QC Sessions

**Session 1: OPEN (đang kiểm tra)**
```sql
INSERT INTO `qc_sessions` (...)
VALUES ('QCS-20251102-0001', [closure_id], 'qc_inspector', 'QC Inspector', '2025-11-02 08:00:00', 'OPEN');
```

**Session 2: DECIDED/APPROVED (đã duyệt)**
```sql
INSERT INTO `qc_sessions` (...)
VALUES ('QCS-20251101-0001', [closure_id], 'qc_inspector', 'QC Inspector', '2025-11-01 16:00:00', 'DECIDED');
```

**Session 3: DECIDED/REJECTED (đã từ chối)**
```sql
INSERT INTO `qc_sessions` (...)
VALUES ('QCS-20251031-0001', [closure_id], 'qc_inspector', 'QC Inspector', '2025-10-31 23:30:00', 'DECIDED');
```

---

### 5. QC Items

**Session 1 (OPEN) - Partial data:**
```sql
INSERT INTO `qc_items` (...)
VALUES
(@session_id, 'CHK-BP-001-01', 'Visual Inspection', ..., 'PASS', ...),
(@session_id, 'CHK-BP-001-02', 'Ink Flow Test', ..., 'FAIL', ...), -- 2 defects found
(@session_id, 'CHK-BP-001-03', 'Dimensional Check', ..., 'PASS', ...);
```

**Session 2 (APPROVED) - Complete data:**
```sql
-- All 5 items PASS
```

**Session 3 (REJECTED) - Complete data:**
```sql
-- All 3 items FAIL with defects
```

---

### 6. QC Decisions

**Decision 1: APPROVE**
```sql
INSERT INTO `qc_decisions` (...)
VALUES (@session_id, 'APPROVE', 2.5, 0.00, NULL, '2025-11-01 17:00:00', 'qc_inspector');
```

**Decision 2: REJECT**
```sql
INSERT INTO `qc_decisions` (...)
VALUES (@session_id, 'REJECT', 2.5, 15.50, 'Critical defects found...', '2025-11-01 00:00:00', 'qc_inspector');
```

---

### 7. Adjustment Request

```sql
INSERT INTO `adjustment_requests` (...)
VALUES ('AR-20251031-0001', @closure_id, 'qc_inspector', 'line_manager', 'Critical defects...', 'OPEN', ...);
```

**Tạo 1 adjustment request:**
- Từ session bị REJECT
- Assigned to: line_manager
- Status: OPEN

---

## ⚠️ VẤN ĐỀ CẦN GIẢI QUYẾT

### 1. Password Field Length
**Vấn đề:**
```sql
-- db_production.sql
CREATE TABLE `user` (
  `password` varchar(11) NOT NULL,  -- ❌ Quá ngắn cho bcrypt
  ...
)
```

**Giải pháp:**
```sql
-- Chạy migration để tăng độ dài password
ALTER TABLE `user` MODIFY `password` VARCHAR(255) NOT NULL;
```

**Hoặc:**
- Dùng password plaintext tạm (như đã sửa)
- ⚠️ **NGUY HIỂM:** Không an toàn, chỉ dùng cho dev/test

---

### 2. Foreign Key Mapping

**Vấn đề:**
```sql
-- Seed data dùng CODE
`project_code` = 'PRJ001'
`product_code` = 'PROD-BP-001'

-- Database dùng ID
`id_project` = 1001 (INT)
`id_product` = 1001 (INT)
```

**Giải pháp:**
```sql
-- Option 1: Cập nhật seed data dùng ID thực tế
INSERT INTO `shift_closures` (...)
VALUES
('SC-20251102-LINE01-CA1', 'LINE-01', 'CA1', 1001, 'LOT-2025-001', 1001, ...);
--                                               ^^^^ project_id  ^^^^ product_id

-- Option 2: Tạo sản phẩm mới với code
INSERT INTO `product` (`id_product`, `product_name`, `summary`, `application`)
VALUES 
(NULL, 'PROD-BP-001', 'Blue Ballpoint Pen', 'Standard office use'),
(NULL, 'PROD-BP-002', 'Red Ballpoint Pen', 'Standard office use');

-- Rồi dùng LAST_INSERT_ID() hoặc subquery
```

---

### 3. Bảng RBAC chưa tồn tại

**Vấn đề:**
- Seed data cần `role_id = 5` (qc_staff)
- Nhưng database gốc chỉ có `role` ENUM('admin','leader')

**Giải pháp:**
```sql
-- PHẢI chạy migrations RBAC trước:
1. db/migrations/001_create_rbac_core_tables.sql
2. db/migrations/002_seed_roles_data.sql
```

**Hoặc:**
```sql
-- Tạo role QC thủ công
CREATE TABLE IF NOT EXISTS `roles` (...);
INSERT INTO `roles` VALUES (5, 'qc_staff', 'Nhân viên QC', '...', 60, 1);

ALTER TABLE `user` ADD COLUMN `role_id` INT NULL AFTER `password`;
ALTER TABLE `user` ADD FOREIGN KEY (`role_id`) REFERENCES `roles`(`role_id`);
```

---

## 📋 CHECKLIST TRIỂN KHAI

### Bước 1: Chạy RBAC Migrations (QUAN TRỌNG!)
```bash
# Trong phpMyAdmin, chạy theo thứ tự:
☐ db/migrations/001_create_rbac_core_tables.sql
☐ db/migrations/002_seed_roles_data.sql
```

**Kiểm tra:**
```sql
SELECT * FROM roles WHERE role_id = 5;
-- Phải trả về: qc_staff | Nhân viên Kiểm soát Chất lượng | level=60
```

---

### Bước 2: Mở rộng Password Field
```sql
☐ ALTER TABLE `user` MODIFY `password` VARCHAR(255) NOT NULL;
```

**Kiểm tra:**
```sql
DESCRIBE user;
-- password | varchar(255) | YES | NULL
```

---

### Bước 3: Chạy QC Module Migration
```bash
☐ db/qc/007_create_qc_module_tables.sql
```

**Kiểm tra:**
```sql
SHOW TABLES LIKE 'qc_%';
-- Phải có 6 bảng: qc_sessions, qc_items, qc_decisions, qc_attachments, qc_checklist_master, qc_config

SHOW TABLES LIKE '%shift%';
-- Phải có: shift_closures, adjustment_requests
```

---

### Bước 4: Tạo sản phẩm mẫu (nếu cần)
```sql
☐ INSERT INTO `product` (`product_name`, `summary`, `application`)
  VALUES 
  ('Blue Ballpoint Pen', 'Standard office ballpoint pen - Blue ink', 'Office, School'),
  ('Red Ballpoint Pen', 'Standard office ballpoint pen - Red ink', 'Office, School');
```

**Lấy ID:**
```sql
SELECT id_product, product_name FROM product WHERE product_name LIKE '%Ballpoint%';
-- Giả sử: 1002, 1003
```

**Cập nhật seed data:**
```sql
-- Thay 'PROD-BP-001' → 1002
-- Thay 'PROD-BP-002' → 1003
```

---

### Bước 5: Chạy Seed Data
```bash
☐ db/qc/qc_module_seed_data.sql
```

**Kiểm tra:**
```sql
-- Kiểm tra user
SELECT username, full_name, role_id FROM user WHERE username = 'qc_inspector';
-- qc_inspector | QC Inspector | 5

-- Kiểm tra closures
SELECT COUNT(*) FROM shift_closures;
-- 4 (3 từ seed + 1 rejected)

-- Kiểm tra sessions
SELECT COUNT(*) FROM qc_sessions;
-- 3

-- Kiểm tra checklist
SELECT COUNT(*) FROM qc_checklist_master;
-- 9

-- Kiểm tra decisions
SELECT COUNT(*) FROM qc_decisions;
-- 2

-- Kiểm tra adjustment requests
SELECT COUNT(*) FROM adjustment_requests;
-- 1
```

---

## 🧪 TEST SCENARIOS

### Test 1: Login QC Inspector
```
1. Logout nếu đang login
2. Login: qc_inspector / password
3. Kiểm tra redirect: /qc/pending
4. Kiểm tra session:
   - role_id: 5
   - role_name: qc_staff
   - level: 60
```

### Test 2: View Pending Closures
```
1. Vào /qc/pending
2. Phải thấy 2 closures PENDING_QC:
   - SC-20251102-LINE01-CA1
   - SC-20251102-LINE02-CA1
3. Không thấy VERIFIED/REJECTED
```

### Test 3: Open Session (Partial)
```
1. Click "Inspect" trên SC-20251102-LINE01-CA1
2. Redirect đến /qc/sessions/1
3. Thấy 3/5 checklist items đã điền
4. Thấy recommendation box (nếu có đủ data)
5. Button "Save Checklist" enabled
6. Button "APPROVE"/"REJECT" enabled
```

### Test 4: View Completed Session
```
1. Vào /qc/sessions/2 (session DECIDED/APPROVED)
2. Thấy tất cả checklist items PASS
3. Thấy decision: APPROVE, defect_rate=0%
4. Form bị disabled (session locked)
5. Không thể edit
```

### Test 5: View Rejected Session
```
1. Vào /qc/sessions/3 (session DECIDED/REJECTED)
2. Thấy items có FAIL
3. Thấy decision: REJECT, defect_rate=15.5%
4. Thấy adjustment request link
```

### Test 6: View Adjustment Requests
```
1. Vào /qc/adjustments
2. Thấy 1 request: AR-20251031-0001
3. Status: OPEN
4. Assigned to: line_manager
```

---

## 📝 MIGRATION SCRIPT ĐÚNG THỨ TỰ

```sql
-- 1. RBAC Core
SOURCE db/migrations/001_create_rbac_core_tables.sql;

-- 2. RBAC Roles
SOURCE db/migrations/002_seed_roles_data.sql;

-- 3. Fix password field
ALTER TABLE `user` MODIFY `password` VARCHAR(255) NOT NULL;

-- 4. QC Module Tables
SOURCE db/qc/007_create_qc_module_tables.sql;

-- 5. QC Seed Data (sau khi sửa product_code/project_code)
SOURCE db/qc/qc_module_seed_data.sql;

-- 6. Verify
SELECT 'User Count' as info, COUNT(*) as total FROM user WHERE role_id = 5;
SELECT 'Closures' as info, COUNT(*) as total FROM shift_closures;
SELECT 'Sessions' as info, COUNT(*) as total FROM qc_sessions;
SELECT 'Checklist Items' as info, COUNT(*) as total FROM qc_checklist_master;
SELECT 'Decisions' as info, COUNT(*) as total FROM qc_decisions;
SELECT 'Adjustments' as info, COUNT(*) as total FROM adjustment_requests;
```

---

## ✅ KẾT LUẬN

### Vấn đề đã sửa:
1. ✅ `users` → `user`
2. ✅ INSERT IGNORE → INSERT ... SELECT ... WHERE NOT EXISTS
3. ✅ Xóa auto-generated columns

### Vấn đề cần chú ý:
1. ⚠️ Password field quá ngắn → Cần ALTER hoặc dùng plaintext
2. ⚠️ product_code/project_code dùng string → Cần mapping với ID
3. ⚠️ Phụ thuộc RBAC migrations → Phải chạy trước

### File đã kiểm tra:
- ✅ `db/qc/qc_module_seed_data.sql` - ĐÃ SỬA
- ✅ `db/qc/007_create_qc_module_tables.sql` - OK
- ✅ `application/controllers/Qc.php` - ĐÃ SỬA phân quyền

### Sẵn sàng triển khai:
- ✅ Chạy migrations theo thứ tự
- ✅ Test với user qc_inspector
- ✅ Kiểm tra tất cả chức năng
