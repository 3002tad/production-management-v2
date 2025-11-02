# 🧪 HƯỚNG DẪN TEST USE CASE: TIẾP NHẬN & TẠO ĐƠN HÀNG

**Ngày:** 2025-11-02  
**Actor:** Ban Giám Đốc (BOD)  
**URL Test:** `http://localhost:8080/production-management-v2/BOD/project/addproject`

---

## 📁 SO SÁNH 2 FILE TESTCASE

| Tiêu chí | UC_ORDER_MANAGEMENT_TESTCASES.txt | UC_ORDER_TESTCASES_DETAILED.txt | ✅ Khuyến nghị |
|----------|-----------------------------------|----------------------------------|----------------|
| **Số lượng TC** | 20 testcases | 18 testcases | **FILE 1** (nhiều hơn) |
| **Cấu trúc** | Nhóm theo chức năng (Basic, AF, Exception, Integration) | Nhóm theo flow (BF, AF41, AF61, EX51, EX52) | **FILE 2** (rõ ràng hơn) |
| **Chi tiết** | Bảng format đẹp, dễ đọc | Nhiều metadata, theo chuẩn | **FILE 1** (dễ đọc) |
| **Coverage** | 20 TC = BASIC + AF + EX + Integration + Edge cases | 18 TC = chỉ theo đặc tả chính | **FILE 1** (đầy đủ hơn) |
| **Thực tế** | Có TC cho UI/UX, performance | Chỉ functional testing | **FILE 1** (thực tế hơn) |

### 🎯 QUYẾT ĐỊNH:

**SỬ DỤNG FILE: `UC_ORDER_MANAGEMENT_TESTCASES.txt`**

**Lý do:**
1. ✅ Nhiều testcases hơn (20 vs 18)
2. ✅ Cover thêm edge cases (số lượng âm, SQL injection, XSS)
3. ✅ Có integration test với màn hình khác
4. ✅ Format dễ đọc, dễ ghi kết quả
5. ✅ Có test cho UI/UX và performance

---

## 🚀 CHUẨN BỊ TRƯỚC KHI TEST

### 1️⃣ Kiểm tra Database

```sql
-- Bước 1: Chạy migration (NẾU CHƯA CHẠY)
-- File: db/migrations/006_add_order_management_columns.sql
ALTER TABLE `project` ADD COLUMN `risk_flag` TINYINT(1) DEFAULT 0 COMMENT 'Cờ nguy cơ trễ hạn';
ALTER TABLE `project` ADD COLUMN `customer_request` TEXT NULL COMMENT 'Yêu cầu của khách hàng';
ALTER TABLE `project` ADD COLUMN `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Thời gian tạo đơn';

-- Bước 2: Kiểm tra có data test
SELECT COUNT(*) FROM customer;  -- Phải >= 1
SELECT COUNT(*) FROM product;   -- Phải >= 1
SELECT COUNT(*) FROM machine WHERE mc_status = 1;  -- Phải >= 1

-- Bước 3: Kiểm tra user BOD
SELECT * FROM user WHERE username = 'bod';
-- Nếu chưa có, chạy:
-- INSERT INTO user (username, password, role, role_id) VALUES ('bod', 'bod123', 'bod', 1);
```

### 2️⃣ Đăng nhập

```
URL: http://localhost:8080/production-management-v2/login
Username: bod
Password: [mật khẩu của bạn]

Expected: Redirect về /BOD/index (Dashboard)
```

### 3️⃣ Kiểm tra menu

```
Sidebar phải có:
✅ Dashboard
✅ Đơn hàng
✅ Khách hàng
✅ Sản phẩm
✅ Kế hoạch sản xuất
✅ Báo cáo
✅ Đăng xuất
```

---

## 📝 HƯỚNG DẪN TEST CHI TIẾT

### 🎯 NHÓM 1: BASIC FLOW (TC-001 → TC-004)

#### ✅ **TC-001: Tạo đơn hàng thành công - Happy Path**

**Priority:** 🔴 P1 - Critical

**Các bước test:**

1. **Mở form tạo đơn hàng:**
   ```
   URL: http://localhost:8080/production-management-v2/BOD/project/addproject
   ```
   - ✅ Kiểm tra: Trang hiển thị không lỗi 404
   - ✅ Kiểm tra: Form hiển thị đầy đủ fields

2. **Điền thông tin đơn hàng:**

   | Field | Giá trị test | Cách nhập |
   |-------|-------------|-----------|
   | Tên đơn hàng | *(Để trống)* | Không nhập gì |
   | Khách hàng | "Tes Customer" | Click dropdown → chọn |
   | Sản phẩm | "Test Prdc" | Click dropdown → chọn |
   | Đường kính | "0.5mm" | Click dropdown → chọn giá trị 5 |
   | Số lượng | `500` | Nhập số |
   | Hạn giao | `2025-12-01` | Click date picker → chọn ngày |
   | Yêu cầu | "Đóng gói cẩn thận" | Nhập text |

3. **Kiểm tra auto-fill:**
   - ✅ Sau khi chọn sản phẩm → đường kính tự động điền
   - ✅ JavaScript chạy không lỗi (mở F12 Console)

4. **Click nút "Lưu và duyệt đơn hàng"**
   - ✅ Popup confirm xuất hiện với nội dung:
     ```
     🎯 XÁC NHẬN TẠO ĐƠN HÀNG
     ━━━━━━━━━━━━━━━━━━━━━━━━━
     📦 Sản phẩm: Test Prdc
     👤 Khách hàng: Tes Customer
     📊 Số lượng: 500 chiếc
     📏 Đường kính: 0.5 mm
     📅 Hạn giao: 2025-12-01
     ━━━━━━━━━━━━━━━━━━━━━━━━━
     ✅ Bấm OK để LƯU VÀ DUYỆT
     ❌ Bấm Cancel để HỦY
     ```

5. **Click OK trong confirm dialog**

6. **Kiểm tra kết quả:**

   **A. Giao diện:**
   - ✅ Redirect về: `/BOD/project`
   - ✅ Flash message màu XANH hiển thị:
     ```
     ✅ Thành công!
     Đơn hàng đã được tạo và duyệt thành công
     ```
   - ✅ Danh sách đơn hàng hiển thị đơn mới

   **B. Database:**
   ```sql
   -- Mở phpMyAdmin hoặc MySQL Workbench
   SELECT * FROM project ORDER BY id_project DESC LIMIT 1;
   
   -- Kiểm tra:
   ✅ project_name = "ORD-1001-20251102-001" (hoặc tương tự)
   ✅ id_cust = 1001
   ✅ id_product = 1001
   ✅ diameter = 5
   ✅ qty_request = 500
   ✅ entry_date = '2025-12-01'
   ✅ pr_status = 1 (Đã duyệt)
   ✅ risk_flag = 0 (Bình thường - vì 500 chiếc không vượt capacity)
   ✅ customer_request = "Đóng gói cẩn thận"
   ✅ created_at = timestamp hiện tại
   ```

7. **Ghi kết quả:**
   - [ ] ✅ PASS
   - [ ] ❌ FAIL - Lý do: _______________________

---

#### ✅ **TC-002: Auto-fill đường kính từ sản phẩm**

**Priority:** 🟡 P2 - High

**Các bước:**

1. Mở form tạo đơn hàng
2. **KHÔNG CHỌN** đường kính
3. Chọn sản phẩm "Test Prdc"
4. **Quan sát dropdown "Đường kính"**

**Expected Result:**
- ✅ Dropdown đường kính **TỰ ĐỘNG** chọn giá trị tương ứng
- ✅ User vẫn có thể thay đổi nếu muốn

**JavaScript kiểm tra:** Mở F12 Console, gõ:
```javascript
$('select[name="diameter"]').val()
// Phải trả về giá trị đường kính của sản phẩm (ví dụ: "5")
```

**Ghi kết quả:**
- [ ] ✅ PASS
- [ ] ❌ FAIL

---

#### ✅ **TC-003: Tạo tên Project tự động**

**Priority:** 🔴 P1 - Critical

**Các bước:**

1. Tạo đơn hàng **ĐỂ TRỐNG** trường "Tên đơn hàng"
2. Chọn khách hàng id_cust = 1001
3. Điền các trường khác và submit
4. Kiểm tra database:

```sql
SELECT project_name, id_cust, created_at 
FROM project 
WHERE id_cust = 1001 
ORDER BY created_at DESC 
LIMIT 1;
```

**Expected:**
- ✅ Format: `ORD-{id_cust}-{YYYYMMDD}-{seq}`
- ✅ Ví dụ: `ORD-1001-20251102-001`

**Test tạo 2 đơn trong ngày:**
1. Tạo đơn 1 lúc 10:00 → `ORD-1001-20251102-001`
2. Tạo đơn 2 lúc 14:00 → `ORD-1001-20251102-002`
3. ✅ Seq tự động +1

**Ghi kết quả:**
- [ ] ✅ PASS
- [ ] ❌ FAIL

---

### 🎯 NHÓM 2: ALTERNATIVE FLOW 4.1 - THIẾU DỮ LIỆU (TC-005 → TC-010)

#### ❌ **TC-005: Không chọn khách hàng**

**Các bước:**

1. Mở form
2. **BỎ TRỐNG** dropdown "Khách hàng"
3. Điền đầy đủ các trường khác
4. Click "Lưu"

**Expected Result:**

**Client-side validation (JavaScript):**
```
⚠️ LỖI: Thiếu dữ liệu bắt buộc

• Vui lòng chọn khách hàng

Vui lòng nhập đầy đủ thông tin.
```

**Server-side validation (nếu bypass JavaScript):**
- ✅ Redirect về `/BOD/project/addproject`
- ✅ Flash message màu ĐỎ:
  ```
  ❌ Lỗi!
  Vui lòng chọn khách hàng
  ```

**Ghi kết quả:**
- [ ] ✅ PASS
- [ ] ❌ FAIL

---

#### ❌ **TC-006: Số lượng = 0 hoặc âm**

**Test Case 6a: Số lượng = 0**

1. Nhập số lượng: `0`
2. Click "Lưu"

**Expected:**
```
⚠️ LỖI: Số lượng phải lớn hơn 0
```

**Test Case 6b: Số lượng âm**

1. Nhập số lượng: `-100`
2. Click "Lưu"

**Expected:**
```
⚠️ LỖI: Số lượng phải lớn hơn 0
```

**Ghi kết quả:**
- [ ] ✅ PASS (cả 2 trường hợp)
- [ ] ❌ FAIL

---

#### ❌ **TC-007: Hạn giao < hôm nay**

**Các bước:**

1. Chọn hạn giao: `2025-10-01` (ngày trong quá khứ)
2. Điền đầy đủ các trường khác
3. Click "Lưu"

**Expected Result:**

**Client-side validation:**
```
⚠️ LỖI: Hạn giao phải từ hôm nay trở đi

Ngày bạn chọn: 2025-10-01
Ngày hôm nay: 2025-11-02
```

**Server-side validation:**
```
❌ Lỗi!
Hạn giao phải từ hôm nay trở đi
```

**Ghi kết quả:**
- [ ] ✅ PASS
- [ ] ❌ FAIL

---

### 🎯 NHÓM 3: ALTERNATIVE FLOW 6.1 - VƯỢT CÔNG SUẤT (TC-011 → TC-012)

#### ⚠️ **TC-011: Tạo đơn vượt công suất**

**Setup data:**

```sql
-- Kiểm tra tổng công suất hiện tại
SELECT SUM(capacity) as total_capacity 
FROM machine 
WHERE mc_status = 1;

-- Giả sử kết quả: total_capacity = 390 (300 + 90)
-- Công suất/ngày = 390 × 2 ca × 0.85 = 663 chiếc
-- Công suất 30 ngày = 663 × 30 = 19,890 chiếc
```

**Các bước test:**

1. Nhập số lượng: `25000` (vượt quá 19,890)
2. Chọn hạn giao: 30 ngày sau (ví dụ: `2025-12-02`)
3. Click "Lưu" → OK

**Expected Result:**

✅ **ĐƠN VẪN ĐƯỢC LƯU** (không reject)

✅ Flash message màu **VÀNG/CAM** (warning):
```
⚠️ Cảnh báo!
Có thể chậm tiến độ, cần duyệt tăng ca/máy

Số lượng yêu cầu: 25,000 chiếc
Công suất khả thi: 19,890 chiếc (85% hiệu suất)
Thiếu hụt: 5,110 chiếc

Đơn hàng đã được lưu nhưng cần duyệt tăng ca hoặc máy bổ sung.
```

✅ Database:
```sql
SELECT risk_flag FROM project ORDER BY id_project DESC LIMIT 1;
-- Kết quả: risk_flag = 1
```

**Ghi kết quả:**
- [ ] ✅ PASS
- [ ] ❌ FAIL

---

### 🎯 NHÓM 4: EXCEPTION 5.1 - HỦY ĐƠN TRƯỚC KHI LƯU (TC-013 → TC-014)

#### ❌ **TC-013: Click Cancel trong confirm dialog**

**Các bước:**

1. Điền đầy đủ form (dữ liệu hợp lệ)
2. Click "Lưu và duyệt"
3. **Popup confirm xuất hiện**
4. **Click "Cancel"**

**Expected Result:**

✅ Alert thông báo:
```
❌ Đã hủy tạo đơn hàng.

Bạn có thể tiếp tục chỉnh sửa hoặc quay lại.
```

✅ **Vẫn ở lại trang form** (`/BOD/project/addproject`)

✅ **Dữ liệu đã nhập vẫn còn** (không bị mất)

✅ **Database KHÔNG có record mới:**
```sql
SELECT COUNT(*) FROM project WHERE created_at > NOW() - INTERVAL 1 MINUTE;
-- Kết quả: 0 (không tăng)
```

**Ghi kết quả:**
- [ ] ✅ PASS
- [ ] ❌ FAIL

---

### 🎯 NHÓM 5: EXCEPTION 5.2 - LỖI CSDL (TC-015)

#### 💥 **TC-015: Simulate lỗi database**

**Cách 1: Tắt MySQL server tạm thời**

1. Điền form đầy đủ
2. **Tắt MySQL service:**
   ```cmd
   net stop MySQL
   ```
3. Click "Lưu" → OK

**Expected:**
```
❌ Lỗi!
Không thể kết nối đến cơ sở dữ liệu. Lỗi: ...
```

4. **Bật lại MySQL:**
   ```cmd
   net start MySQL
   ```

**Cách 2: Sửa database config tạm thời**

1. Sửa `application/config/database.php`:
   ```php
   'password' => 'wrong_password',  // Cố tình sai
   ```
2. Test tạo đơn
3. Expected: Flash error message
4. **Đổi lại password đúng**

**Ghi kết quả:**
- [ ] ✅ PASS
- [ ] ❌ FAIL

---

## 📊 BẢNG TỔNG HỢP KẾT QUẢ

Sau khi test xong, điền vào bảng này:

| TC ID | Tên test | Priority | Status | Ghi chú |
|-------|----------|----------|--------|---------|
| TC-001 | Tạo đơn hàng thành công | P1 | [ ] PASS [ ] FAIL | |
| TC-002 | Auto-fill đường kính | P2 | [ ] PASS [ ] FAIL | |
| TC-003 | Tên project tự động | P1 | [ ] PASS [ ] FAIL | |
| TC-005 | Thiếu khách hàng | P1 | [ ] PASS [ ] FAIL | |
| TC-006 | Số lượng = 0 hoặc âm | P1 | [ ] PASS [ ] FAIL | |
| TC-007 | Hạn giao < hôm nay | P1 | [ ] PASS [ ] FAIL | |
| TC-011 | Vượt công suất | P1 | [ ] PASS [ ] FAIL | |
| TC-013 | Cancel confirm dialog | P2 | [ ] PASS [ ] FAIL | |
| TC-015 | Lỗi database | P2 | [ ] PASS [ ] FAIL | |

---

## 🐛 CÁC LỖI THƯỜNG GẶP & CÁCH FIX

### Lỗi 1: Flash message không hiển thị

**Nguyên nhân:** Session chưa được load

**Fix:**
```php
// Kiểm tra BOD.php __construct()
$this->load->library('session');  // Đảm bảo có dòng này
```

### Lỗi 2: Auto-fill không chạy

**Nguyên nhân:** jQuery chưa load hoặc selector sai

**Fix:** Mở F12 Console, kiểm tra:
```javascript
// Test jQuery
$('select[name="id_product"]').length  // Phải > 0
```

### Lỗi 3: Confirm dialog không xuất hiện

**Nguyên nhân:** JavaScript bị block hoặc syntax error

**Fix:**
```javascript
// Kiểm tra trong AddProject.php (line 325-344)
// Đảm bảo có event listener:
$('#order_form').on('submit', function(e) { ... });
```

### Lỗi 4: Database không lưu risk_flag

**Nguyên nhân:** Migration chưa chạy

**Fix:**
```sql
-- Chạy migration
ALTER TABLE `project` ADD COLUMN `risk_flag` TINYINT(1) DEFAULT 0;
```

---

## 📸 SCREENSHOT YÊU CẦU

Chụp màn hình các trường hợp sau:

1. ✅ **TC-001 PASS:** Flash message xanh "Đơn hàng đã được tạo..."
2. ❌ **TC-005 FAIL:** Flash message đỏ "Vui lòng chọn khách hàng"
3. ⚠️ **TC-011 WARNING:** Flash message vàng "Có thể chậm tiến độ..."
4. 🎯 **Confirm dialog:** Popup xác nhận với đầy đủ thông tin
5. 📊 **Database:** Bảng project với risk_flag = 1

---

## ✅ CHECKLIST CUỐI CÙNG

Trước khi báo cáo kết quả test, đảm bảo:

- [ ] Đã chạy migration `006_add_order_management_columns.sql`
- [ ] Database có ít nhất 1 customer, 1 product, 1 machine
- [ ] User 'bod' đã được tạo và có thể login
- [ ] Test đủ 9 testcases ưu tiên cao (TC-001, 002, 003, 005, 006, 007, 011, 013, 015)
- [ ] Chụp screenshot các trường hợp quan trọng
- [ ] Ghi rõ môi trường test (OS, Browser, PHP version, MySQL version)

---

**File testcase chính:** `docs/UC_ORDER_MANAGEMENT_TESTCASES.txt`  
**Tài liệu tham khảo:** `docs/VERIFICATION_USECASE_TIEP_NHAN_DON_HANG.md`

**Happy Testing! 🚀**
