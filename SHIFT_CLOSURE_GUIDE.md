# Hệ Thống Chốt Ca - Tài Liệu Hướng Dẫn

## Tổng Quan

Module chốt ca tự động tổng hợp dữ liệu sản xuất, cảnh báo vượt định mức, và tạo đề nghị nhập kho khi kết thúc ca làm việc.

## Quy Trình Chốt Ca

### 1. Bắt Đầu Ca

**Điều kiện**: Ca có `shift_status = 1` (Chưa bắt đầu)

**Thao tác**:
1. Vào chi tiết ca: `/leader/shift/{shift_id}/detail`
2. Nhấn nút **"Bắt đầu ca"** (màu xanh lá)
3. Xác nhận trong popup

**Kết quả**:
- `shift_status` → 2 (Đang chạy)
- `started_at` → Thời điểm hiện tại
- `started_by` → User ID người bắt đầu

---

### 2. Kết Thúc & Chốt Ca

**Điều kiện**: Ca có `shift_status = 2` (Đang chạy) và `is_closed = 0`

**Thao tác**:
1. Nhấn nút **"Kết thúc & Chốt ca"** (màu vàng)
2. Hệ thống hiển thị form chốt ca với:
   - **Tổng quan ca**: Mục tiêu, sản lượng, thành phẩm, phế phẩm, downtime
   - **Tỷ lệ**: Hiệu suất (%), tỷ lệ phế phẩm (%)
   - **Chi tiết theo máy**: Bảng liệt kê từng máy với dữ liệu
   - **Cảnh báo**: Máy nào vượt định mức sẽ được highlight

**Xác nhận số lượng**:
- Mỗi máy có 2 ô input: **Xác nhận Thành phẩm** và **Xác nhận Phế phẩm**
- Mặc định = số liệu đã ghi nhận
- Người dùng có thể điều chỉnh nếu có sai lệch

**Ghi chú** (tùy chọn):
- Nhập ghi chú về ca, sự cố, điều chỉnh

**Nhấn "Chốt ca"**:
- Popup xác nhận: "Xác nhận chốt ca? Hành động không thể hoàn tác"

---

### 3. Hệ Thống Xử Lý (Backend)

#### A. Tổng Hợp Dữ Liệu (`aggregateShiftData`)

1. **Query tất cả máy trong ca**:
   ```sql
   SELECT machine_id, target, good, defect, downtime
   FROM production_records
   WHERE shift_id = ?
   GROUP BY machine_id
   ```

2. **Tính toán**:
   - `total_target` = SUM(target_count)
   - `total_good` = SUM(good_count)
   - `total_defect` = SUM(defect_count)
   - `total_produced` = total_good + total_defect
   - `efficiency_rate` = (total_produced / total_target) × 100%
   - `defect_rate` = (total_defect / total_produced) × 100%

3. **Kiểm tra cảnh báo** (theo từng máy):
   - **Warning** (Vàng): 
     - `defect_rate >= 5%` HOẶC
     - `efficiency_rate < 70%`
   - **Critical** (Đỏ):
     - `defect_rate >= 10%`

#### B. Lưu Phiếu Chốt Ca (`createClosure`)

**Transaction Start**

1. **Kiểm tra ca đã chốt chưa**:
   ```sql
   SELECT * FROM shift_closures WHERE shift_id = ?
   ```
   - Nếu có → Báo lỗi "Ca đã được chốt"

2. **Tạo mã phiếu chốt**:
   - Format: `SC-YYYYMMDD-XXX`
   - Ví dụ: `SC-20251218-001`

3. **Insert bảng `shift_closures`**:
   ```sql
   INSERT INTO shift_closures (
       shift_id, closure_code, closure_date, closed_by,
       total_target, total_produced, total_good, total_defect, total_downtime,
       efficiency_rate, defect_rate, has_warnings, warning_details, notes, status
   ) VALUES (...)
   ```

4. **Insert chi tiết máy `shift_closure_machines`**:
   ```sql
   INSERT INTO shift_closure_machines (
       closure_id, machine_id, staff_id,
       target_count, good_count, defect_count, downtime_minutes,
       confirmed_good, confirmed_defect, efficiency_rate, defect_rate, is_warning
   ) VALUES (...) -- Mỗi máy 1 record
   ```

5. **Tạo đề nghị nhập kho `warehouse_import_requests`**:
   - Mã: `WIR-YYYYMMDD-XXX`
   - Số lượng: `total_good` (tổng thành phẩm đã xác nhận)
   - Trạng thái: `pending_qc` (Chờ QC)
   ```sql
   INSERT INTO warehouse_import_requests (
       request_code, closure_id, shift_id,
       product_name, quantity, unit, status, created_by
   ) VALUES (...)
   ```

6. **Update trạng thái ca**:
   ```sql
   UPDATE production_shifts
   SET shift_status = 3,        -- Hoàn thành
       ended_at = NOW(),
       ended_by = ?,
       is_closed = 1            -- Đã chốt
   WHERE shift_id = ?
   ```

**Transaction Commit**

#### C. Xử Lý Lỗi

Nếu bất kỳ bước nào thất bại:
- **Rollback Transaction**
- Không thay đổi trạng thái ca
- Trả về JSON:
  ```json
  {
      "success": false,
      "message": "Lỗi chốt ca: [Chi tiết lỗi]"
  }
  ```

---

## Cảnh Báo Định Mức

### Ngưỡng Mặc Định (từ `system_config`)

| Loại | Ngưỡng | Mô tả |
|------|--------|-------|
| **Defect Warning** | 5% | Tỷ lệ phế phẩm cảnh báo |
| **Defect Critical** | 10% | Tỷ lệ phế phẩm nghiêm trọng |
| **Efficiency Warning** | 70% | Hiệu suất thấp |

### Hiển Thị Cảnh Báo

**Trong form chốt ca**:
- Máy có cảnh báo → highlight màu vàng/đỏ
- Badge màu đỏ nếu vượt critical
- Alert box đầu trang liệt kê tất cả cảnh báo

**Lưu vào database**:
```json
{
    "warning_details": [
        {
            "type": "critical",
            "machine_id": 3,
            "machine_name": "Máy ép M003",
            "message": "Tỷ lệ phế phẩm nghiêm trọng: 12.5% (>= 10%)",
            "defect_rate": 12.5
        },
        {
            "type": "warning",
            "machine_id": 5,
            "message": "Hiệu suất thấp: 65% (< 70%)",
            "efficiency_rate": 65
        }
    ]
}
```

---

## Đề Nghị Nhập Kho

### Workflow

```
Chốt ca thành công
     ↓
Tạo warehouse_import_request (status = pending_qc)
     ↓
QC kiểm tra (Phê duyệt/Từ chối)
     ↓ (approved)
Update status = qc_approved
     ↓
Nhập kho (Warehouse staff)
     ↓
Update status = imported
```

### Trạng Thái

| Status | Tên | Mô tả |
|--------|-----|-------|
| `pending_qc` | Chờ QC | Mới tạo, chờ QC kiểm tra |
| `qc_approved` | QC đã duyệt | QC chấp nhận, chờ nhập kho |
| `qc_rejected` | QC từ chối | QC không duyệt |
| `imported` | Đã nhập kho | Đã nhập kho thành công |
| `cancelled` | Hủy | Đề nghị bị hủy |

---

## API Endpoints

### 1. Bắt Đầu Ca
```
GET /leader/start_shift/{shift_id}
```
**Response**: Redirect về chi tiết ca với flash message

---

### 2. Hiển Thị Form Chốt Ca
```
GET /leader/end_shift/{shift_id}
```
**Response**: View `leader/shift/closure_form.php`

---

### 3. Lưu Phiếu Chốt Ca (AJAX)
```
POST /leader/save_closure
```

**Request Body**:
```json
{
    "shift_id": 1,
    "notes": "Máy M003 bị lỗi 30 phút",
    "confirmed_data": {
        "3": {"good": 180, "defect": 15},
        "5": {"good": 200, "defect": 8}
    }
}
```

**Success Response**:
```json
{
    "success": true,
    "closure_id": 1,
    "closure_code": "SC-20251218-001",
    "warehouse_request_code": "WIR-20251218-001",
    "message": "Chốt ca thành công. Mã phiếu: SC-20251218-001"
}
```

**Error Response**:
```json
{
    "success": false,
    "message": "Lỗi chốt ca: Ca đã được chốt. Mã phiếu: SC-20251218-001"
}
```

---

### 4. Xem Chi Tiết Phiếu Chốt
```
GET /leader/closure_detail/{closure_id}
```
**Response**: View `leader/shift/closure_detail.php`

---

## Database Schema

### `shift_closures`
```sql
- closure_id (PK)
- shift_id (FK → production_shifts)
- closure_code (UNIQUE: SC-YYYYMMDD-XXX)
- closure_date
- closed_by (FK → user)
- total_target, total_produced, total_good, total_defect, total_downtime
- efficiency_rate, defect_rate
- has_warnings (0/1)
- warning_details (JSON)
- notes (TEXT)
- confirmed_quantities (JSON)
- status (draft/confirmed/cancelled)
- warehouse_request_id (FK → warehouse_import_requests)
```

### `shift_closure_machines`
```sql
- id (PK)
- closure_id (FK → shift_closures)
- machine_id (FK → machines)
- staff_id (FK → user)
- target_count, produced_count, good_count, defect_count, downtime_minutes
- efficiency_rate, defect_rate
- confirmed_good, confirmed_defect
- defect_details (JSON)
- downtime_details (JSON)
- is_warning (0/1)
- notes
```

### `warehouse_import_requests`
```sql
- request_id (PK)
- request_code (UNIQUE: WIR-YYYYMMDD-XXX)
- closure_id (FK → shift_closures, UNIQUE)
- shift_id (FK → production_shifts)
- product_name, product_code, quantity, unit
- status (pending_qc/qc_approved/qc_rejected/imported/cancelled)
- qc_by, qc_date, qc_notes, qc_approved_quantity, qc_rejected_quantity
- imported_by, imported_date, warehouse_location
- created_by
```

### `defect_reasons`
```sql
- reason_id (PK)
- reason_code (UNIQUE: DR001, DR002...)
- reason_name (Lỗi nguyên liệu, Lỗi máy móc...)
- category (material/machine/operator/process/other)
- is_active (0/1)
```

### `system_config`
```sql
- config_key (PK: defect_rate_warning_threshold, etc.)
- config_value
- config_type (string/number/boolean/json)
- description
```

### Thêm cột vào `production_shifts`
```sql
ALTER TABLE production_shifts ADD:
- started_at DATETIME
- started_by INT (FK → user)
- ended_at DATETIME
- ended_by INT (FK → user)
- is_closed TINYINT(1) DEFAULT 0
```

---

## Testing Checklist

### 1. Bắt Đầu Ca
- [ ] Ca status = 1 → Hiển thị nút "Bắt đầu ca"
- [ ] Click nút → Popup xác nhận
- [ ] Sau khi bắt đầu → status = 2, started_at có giá trị
- [ ] Không thể bắt đầu ca đang chạy (status = 2)
- [ ] Không thể bắt đầu ca đã hoàn thành (status = 3)

### 2. Form Chốt Ca
- [ ] Hiển thị đúng tổng overview (target, good, defect, efficiency, defect_rate)
- [ ] Bảng chi tiết máy có đủ thông tin
- [ ] Màu highlight đúng cho máy có cảnh báo (vàng/đỏ)
- [ ] Alert cảnh báo hiển thị nếu has_warnings = true
- [ ] Input xác nhận số lượng có giá trị mặc định
- [ ] Ô textarea ghi chú hoạt động

### 3. Lưu Phiếu Chốt
- [ ] Validation: shift_id bắt buộc
- [ ] Không chốt được ca đã chốt (is_closed = 1)
- [ ] Transaction rollback nếu lỗi
- [ ] Insert đúng vào shift_closures
- [ ] Insert đủ records vào shift_closure_machines
- [ ] Tạo được warehouse_import_request
- [ ] Update shift_status = 3, is_closed = 1
- [ ] Flash message success
- [ ] Redirect về shift detail

### 4. Cảnh Báo
- [ ] Defect rate >= 5% → warning (vàng)
- [ ] Defect rate >= 10% → critical (đỏ)
- [ ] Efficiency < 70% → warning (vàng)
- [ ] warning_details JSON lưu đúng format

### 5. Error Handling
- [ ] Ca không tồn tại → Báo lỗi, redirect
- [ ] Ca chưa start (status != 2) → Báo lỗi
- [ ] Ca đã closed → Báo lỗi
- [ ] Lỗi DB connection → Rollback, báo lỗi
- [ ] Lỗi insert → Rollback, không đổi status ca

### 6. UI/UX
- [ ] Nút "Bắt đầu ca" màu xanh lá, có icon
- [ ] Nút "Kết thúc & Chốt ca" màu vàng, có icon
- [ ] Nút "Đã chốt ca" disabled, màu xám
- [ ] Badge efficiency màu xanh (>=90%), vàng (70-89%), đỏ (<70%)
- [ ] Badge defect màu xanh (<5%), vàng (5-10%), đỏ (>10%)
- [ ] Spinner khi submit form
- [ ] Alert popup xác nhận trước khi chốt

---

## Troubleshooting

### Lỗi: "Migration 017 chưa chạy"
**Nguyên nhân**: Các bảng `shift_closures`, `warehouse_import_requests` chưa tồn tại

**Giải pháp**:
```sql
-- Chạy file migration trong phpMyAdmin
db/migrations/Cap2/CaLamViec/017_create_shift_closure_tables.sql
```

---

### Lỗi: "Ca đã được chốt"
**Nguyên nhân**: `is_closed = 1` hoặc có record trong `shift_closures`

**Giải pháp**:
1. Kiểm tra:
   ```sql
   SELECT * FROM shift_closures WHERE shift_id = ?
   ```
2. Nếu muốn chốt lại (DEV only):
   ```sql
   DELETE FROM shift_closures WHERE shift_id = ?;
   UPDATE production_shifts SET is_closed = 0, shift_status = 2 WHERE shift_id = ?;
   ```

---

### Lỗi: Transaction Rollback
**Log**: Check `application/logs/log-YYYY-MM-DD.php`

**Các nguyên nhân thường gặp**:
- Foreign key constraint failed
- Duplicate key (closure_code hoặc request_code)
- NULL value trong required field
- Data type mismatch

**Debug**:
```php
log_message('error', 'ShiftClosureModel::createClosure() - Error: ' . $e->getMessage());
```

---

## Security Considerations

1. **CSRF Protection**: Form có `<?= $this->security->get_csrf_token_name() ?>`
2. **RBAC**: Chỉ Leader/Admin/BOD mới chốt được ca
3. **Transaction**: Dùng `$this->db->trans_start()` / `trans_complete()`
4. **SQL Injection**: Dùng Query Builder, không concat string
5. **XSS**: `htmlspecialchars()` khi hiển thị user input

---

## Future Enhancements

1. **Export PDF**: In phiếu chốt ca thành PDF
2. **Email Notification**: Gửi mail cho QC khi có đề nghị nhập kho mới
3. **Dashboard**: Báo cáo tổng hợp phiếu chốt theo ngày/tuần/tháng
4. **Approval Flow**: Thêm bước duyệt phiếu chốt trước khi tạo WIR
5. **Mobile App**: View phiếu chốt trên mobile
6. **Barcode**: Quét mã vạch khi QC/nhập kho
7. **Auto-close**: Tự động chốt ca khi hết giờ (Cron job)

---

## Changelog

### v1.0.0 - 2025-12-18
- Initial release
- Tạo migration 017
- Model ShiftClosureModel với transaction handling
- Controller endpoints: start_shift, end_shift, save_closure
- View closure_form với cảnh báo động
- Tích hợp vào shift detail view

---

**Contact**: System Admin / Dev Team  
**Last Updated**: 2025-12-18
