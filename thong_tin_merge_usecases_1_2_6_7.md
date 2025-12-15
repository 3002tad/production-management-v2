# Thông tin Merge — Use Case 1, 2, 6, 7

Tài liệu tóm tắt hành vi hiện tại của mã (CRUD và logic liên quan) cho các Use Case UC1, UC2, UC6, UC7 và các lưu ý quan trọng khi merge thay đổi vào nhánh chính (staging/production).

---

**Tổng quan nhanh**:
- UC1: Quản lý Khách hàng (Customer). Model chính: `application/models/CustomerModel.php`.
- UC2: Quản lý Sản phẩm & BOM. Liên quan: `product` table, BOM lưu JSON, nhiều logic trong `application/models/OrderModel.php`.
- UC6: Quản lý Người dùng & Phân quyền. Model chính: `application/models/admin/UserManagementModel.php`, migration cập nhật `user` table dưới `db/migrations/cap1/usecase6/`.
- UC7: Quản lý Đơn hàng / kiểm tra năng lực & cảnh báo. Tập trung trong `application/models/OrderModel.php` và nhiều migration & test scripts under `db/migrations/cap1/usecase7/`.

---

**UC1 — Quản lý Khách hàng**
- Hiện trạng (code):
  - `CustomerModel` cung cấp CRUD: `addCustomer()`, `updateCustomer()`, `deleteCustomer()`, `getAllCustomers()`, `searchCustomers()`.
  - Validation: `validateCustomerData()` ép `telp` phải là số (`/^[0-9]{8,15}$/`) và `email` tối đa 25 ký tự.
  - `telp` hiện tại được mô tả là INT trong schema, nhưng có migration `db/migrations/cap1/usecase1/007_change_customer_telp_to_varchar.sql` chuyển `telp` sang `VARCHAR(20)`.
  - Xóa bị cấm nếu khách hàng có đơn hàng (FK với `project`).
- Lưu ý Merge (quan trọng):
  - Nếu áp migration đổi `telp` sang `VARCHAR(20)` thì phải:
    - Kiểm tra `CustomerModel::validateCustomerData()` để cho phép số có leading zeros và độ dài tối đa mới (hiện regex không cho leading '+' hay dấu cách — cân nhắc cho phép '+' nếu cần quốc tế hoá).
    - Kiểm tra form frontend/JS để xử lý chuỗi điện thoại (no casting sang int).
    - Trước khi chạy migration trên production: backup DB và kiểm tra dữ liệu trùng/overflow (các số vượt giới hạn integer, mất leading 0, v.v.). Migration có ghi chú mẫu `UPDATE` — KHÔNG chạy tự động mà không review.
  - Nếu thay đổi validation, bổ sung unit/integration test cho create/update khách hàng kiểm tra format phone mới.
- Files tham khảo:
  - [application/models/CustomerModel.php](application/models/CustomerModel.php#L1-L40)
  - [db/migrations/cap1/usecase1/007_change_customer_telp_to_varchar.sql](db/migrations/cap1/usecase1/007_change_customer_telp_to_varchar.sql#L1-L40)

---

**UC2 — Quản lý Sản phẩm & BOM**
- Hiện trạng (code):
  - BOM lưu trong cột `product.bom` dưới dạng JSON (mảng objects: `id_material`, `quantity_per_unit`, ...).
  - `OrderModel` đọc `bom` để tính NVL cần thiết, cập nhật kho NVL, và dùng `refreshProductWarnings()` để cập nhật phân tích đơn hàng khi BOM thay đổi.
  - Có migration/test script thêm BOM mẫu: `db/migrations/cap1/usecase7/008_update_product_bom.sql` (được dùng để seed/test UC7 scenarios).
- Lưu ý Merge (quan trọng):
  - Khi thay đổi cấu trúc BOM (tên trường, định dạng JSON) cần đảm bảo:
    - Mọi chỗ đọc/ghi `bom` đều được cập nhật (ví dụ `OrderModel::_updateMaterialStock`, `checkCapacity`, `refreshProductWarnings`).
    - Sau migration thay đổi BOM, phải chạy `OrderModel::refreshProductWarnings()` cho tất cả project liên quan (migration `008_update_product_bom.sql` + test scripts hướng dẫn làm việc này).
    - Nếu đổi tên trường hoặc loại (ví dụ từ `quantity_per_unit` → `qty_per_unit`), tạo migration chuyển đổi dữ liệu (UPDATE JSON_SAFE) và cập nhật code cùng lúc.
  - Kiểm tra các test UC7 (scripts `uc7_*`): chạy toàn bộ test UC7 để đảm bảo badges/warnings/logic hiển thị đúng.
- Files tham khảo:
  - [application/models/OrderModel.php](application/models/OrderModel.php#L480-L540)
  - [db/migrations/cap1/usecase7/008_update_product_bom.sql](db/migrations/cap1/usecase7/008_update_product_bom.sql#L1-L40)
  - [db/migrations/cap1/usecase7/UC7_TEST_SCENARIOS.md](db/migrations/cap1/usecase7/UC7_TEST_SCENARIOS.md#L1-L20)

---

**UC6 — Quản lý Người dùng & Phân quyền**
- Hiện trạng (code & DB):
  - Model: `application/models/admin/UserManagementModel.php` với CRUD: `createUser()`, `updateUser()`, `lockUser()`, `unlockUser()`, `resetPassword()`.
  - Quy tắc nghiệp vụ: `username` phải độc nhất (kiểm tra case-insensitive), `staff_id` chỉ được gán 1 user, `password` lưu theo plaintext theo đặc tả hiện tại (tạm thời), có `must_change_password` flag.
  - Migration `db/migrations/cap1/usecase6/001_extend_user_table.sql` thêm `temp_password` và `must_change_password`.
  - Migration bổ sung unique constraints: `db/migrations/002_add_user_unique_constraints.sql` (thêm unique index `ux_user_username` và `ux_user_staff_id`).
- Lưu ý Merge (quan trọng):
  - Trước khi áp migration `002_add_user_unique_constraints.sql` cần kiểm tra dữ liệu hiện tại để tìm các trùng lặp `username` và `staff_id` (có thể gây fail khi add unique index). Quy trình khuyến nghị:
    1. Chạy query kiểm tra duplicate (case-insensitive cho `username`) và duplicate `staff_id`.
    2. Nếu có duplicates: chuẩn bị script cleanup hoặc thông báo cho chủ sở hữu dữ liệu, hoặc thêm temporary fix (ví dụ: thêm suffix tạm, notify admins).
  - Lưu ý case-insensitive: model đã kiểm tra `LOWER(username)` nhưng index UNIQUE MySQL mặc định là case-insensitive trên collation nếu dùng utf8_general_ci; xác nhận collation để tránh khác biệt giữa runtime check và DB constraint.
  - Nếu định đổi cách lưu mật khẩu (ví dụ sang bcrypt), phải điều phối thay đổi auth flows (`Login.php`) và migration để chuyển user hiện hữu (không thể lưu plaintext và hashed đồng thời mà không xử lý đăng nhập cẩn thận).
  - Kiểm tra logic bảo vệ admin cuối cùng (`checkLastAdmin()`), tránh thay đổi role defaults mà vô tình làm không còn admin hợp lệ.
- Files tham khảo:
  - [application/models/admin/UserManagementModel.php](application/models/admin/UserManagementModel.php#L1-L60)
  - [db/migrations/cap1/usecase6/001_extend_user_table.sql](db/migrations/cap1/usecase6/001_extend_user_table.sql#L1-L40)
  - [db/migrations/002_add_user_unique_constraints.sql](db/002_add_user_unique_constraints.sql#L1-L40)

---

**UC7 — Order Management / Capacity & Warnings**
- Hiện trạng (code & DB):
  - `OrderModel::checkCapacity()` thực hiện phân tích năng lực, tồn kho, NVL, deadline, và trả về các trường dùng cho UI và lưu vào `project` (ví dụ: `warning_flag`, `warning_type`, `warning_details`, `capacity_level_used`, `finished_stock_available`, `material_shifts_available`, `stock_allocation`).
  - `refreshProductWarnings()` và `refreshProjectWarnings()` để cập nhật analysis sau khi BOM hoặc stock thay đổi.
  - Nhiều migration & scripts kiểm tra UC7 (uc7_* scripts and `UC7_TEST_SCENARIOS.md`).
  - Có migration `db/migrations/cap1/usecase7/007_sync_risk_flag_with_warning_flag.sql` để đồng bộ `risk_flag` → `warning_flag` (cần chú ý vì làm thay đổi cột cũ).
- Lưu ý Merge (quan trọng):
  - Nếu thay đổi logic tính toán capacity (số sản phẩm / ca, hiệu suất, công thức), cập nhật tất cả nơi tính toán (cả kiểm thử) và tune script test UC7 để phản ánh.
  - Khi thay đổi hoặc seed BOM (ví dụ migration 008), cần chạy `refreshProductWarnings()` cho các project liên quan để cập nhật `warning_details` và `warning_flag`.
  - Nếu đổi/tên/loại field trong `project` (ví dụ đổi `risk_flag` → `warning_flag`), chạy migration đồng bộ và đảm bảo UI & controller dùng đúng tên mới (`BOD.php`, views). Kiểm tra `uc7_*` test scripts để chạy lại và xác nhận behaviors (badges, toast messages).
  - Lưu ý format JSON trong `warning_details` và `stock_allocation`: nếu schema migration thay đổi format JSON, phải viết migration chuyển đổi dữ liệu và cập nhật code đọc/ghi tương ứng.
- Files tham khảo:
  - [application/models/OrderModel.php](application/models/OrderModel.php#L560-L760)
  - [db/migrations/cap1/usecase7/007_sync_risk_flag_with_warning_flag.sql](db/migrations/cap1/usecase7/007_sync_risk_flag_with_warning_flag.sql#L1-L40)
  - [db/migrations/cap1/usecase7/UC7_TEST_SCENARIOS.md](db/migrations/cap1/usecase7/UC7_TEST_SCENARIOS.md#L1-L20)

---

**Hướng dẫn Merge Checklist (tổng quát)**
- Trước khi merge migration có thay đổi schema (ADD/MODIFY/DROP column hoặc ADD UNIQUE):
  - Backup DB (dump) và thử migration trên staging.
  - Kiểm tra duplicates & dữ liệu không tương thích (ví dụ duplicate usernames, non-numeric phone overflow) — fix trước khi apply.
  - Nếu migration làm thay đổi dữ liệu nguồn (BOM, phone format, etc.), chuẩn bị script chuyển đổi an toàn và có rollback plan.
- Sau migration có ảnh hưởng đến dữ liệu dịch vụ (BOM, warnings, stock allocation):
  - Chạy các script refresh (ví dụ `OrderModel::refreshProductWarnings()`), chạy test scripts UC7 (`uc7_*`) để xác nhận.
- Đối với thay đổi nghiệp vụ (password hashing, unique checks, validation changes):
  - Cập nhật code server-side và UI forms cùng lúc.
  - Thông báo cho admin/OPS về thay đổi và hướng dẫn xử lý user nếu cần (ví dụ reset mật khẩu hàng loạt khi đổi scheme hashing).
- Document (thêm vào PR và description):
  - Mô tả những migration cần chạy (order), các manual steps (khi cần cleanup dữ liệu), và test scripts cần chạy sau merge.

---

