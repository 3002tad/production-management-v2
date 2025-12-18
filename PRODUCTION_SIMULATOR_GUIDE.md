# Production Simulator - Hệ thống giả lập sản lượng sản xuất

**Version:** 1.0  
**Created:** 2025-12-18  
**Migration:** 016_create_production_simulator_tables.sql

---

## Tổng quan

Production Simulator là hệ thống tự động giả lập ghi nhận sản lượng sản xuất theo từng máy trong ca làm việc. Hệ thống giúp:

- ✅ Tạo dữ liệu mẫu để test và demo
- ✅ Giả lập môi trường sản xuất thực tế
- ✅ Theo dõi sản lượng: thành phẩm, phế phẩm, mục tiêu, downtime
- ✅ Tính toán hiệu suất và tỷ lệ lỗi tự động
- ✅ Bật/tắt dễ dàng qua UI

---

## Kiến trúc hệ thống

### 1. Database Tables

#### `production_records`
Lưu trữ dữ liệu sản lượng theo máy:
```sql
- shift_id: ID ca làm việc
- machine_id: ID máy
- staff_id: ID nhân viên
- timestamp: Thời điểm ghi nhận
- good_count: Số lượng thành phẩm tốt
- defect_count: Số lượng phế phẩm
- target_count: Mục tiêu sản lượng
- downtime_minutes: Thời gian ngừng máy (phút)
- downtime_reason: Lý do downtime
- efficiency_rate: Tỷ lệ hiệu suất (%)
- defect_rate: Tỷ lệ phế phẩm (%)
- is_simulated: 1=Giả lập, 0=Thực
```

#### `simulator_settings`
Cấu hình cho simulator:
```sql
- setting_key: Khóa cấu hình
- setting_value: Giá trị
- setting_type: boolean/integer/float/string/json
- description: Mô tả
```

#### `downtime_reasons`
Danh mục lý do downtime:
```sql
- reason_code: Mã lý do (MAINT, BREAKDOWN, etc.)
- reason_name: Tên lý do
- category: mechanical/material/quality/operator/other
```

#### View: `v_production_summary`
Tổng hợp sản lượng theo shift và machine

### 2. Backend Components

**Model:** `ProductionSimulatorModel.php`
- `getSettings()` - Lấy cấu hình
- `updateSetting()` - Cập nhật cấu hình
- `getActiveShiftsForSimulation()` - Lấy ca cần giả lập
- `createProductionRecord()` - Tạo bản ghi sản lượng
- `getShiftProductionRecords()` - Lấy dữ liệu sản lượng ca
- `getShiftProductionSummary()` - Tổng hợp sản lượng ca

**Controller:** `ProductionSimulator.php`
- `settings()` - Trang cấu hình
- `run()` - Chạy simulator (AJAX endpoint)
- `toggle()` - Bật/tắt simulator
- `update_settings()` - Cập nhật cấu hình
- `get_shift_records()` - Lấy dữ liệu ca
- `status()` - Kiểm tra trạng thái

### 3. Frontend Components

**Settings Page:** `application/views/leader/simulator/settings.php`
- Toggle ON/OFF
- Cấu hình interval (60-3600 giây)
- Range cho good_count (min/max)
- Range cho defect_rate (%)
- Xác suất và thời gian downtime
- Hệ số nhân mục tiêu

**Shift Detail - Production Tab:** `application/views/leader/shift/detail.php`
- Tab "Sản lượng" mới
- Auto-polling khi simulator bật
- Hiển thị summary cards (theo máy)
- Bảng chi tiết production records

---

## Cách sử dụng

### Bước 1: Chạy Migration

```bash
# Mở phpMyAdmin hoặc MySQL client
# Import file:
db/migrations/Cap2/CaLamViec/016_create_production_simulator_tables.sql
```

Verify tables created:
```sql
SHOW TABLES LIKE '%production%';
SHOW TABLES LIKE '%simulator%';
SHOW TABLES LIKE '%downtime%';
```

### Bước 2: Truy cập Settings Page

URL: `http://localhost/leader/productionsimulator/settings`

**Cấu hình mặc định:**
- ✅ Simulator: TẮT (simulator_enabled = 0)
- 🕐 Interval: 300 giây (5 phút)
- 📦 Good count: 50-200 sản phẩm/lần
- ⚠️ Defect rate: 1%-8%
- ⏸️ Downtime probability: 15% (0.15)
- ⏱️ Downtime duration: 5-30 phút
- 🎯 Target multiplier: 1.2 (target = good_count × 1.2)

### Bước 3: Bật Simulator

**Cách 1: Từ Settings Page**
1. Toggle "BẬT/TẮT" ở góc trên bên phải
2. Status indicator chuyển sang màu xanh
3. Text hiển thị "ĐANG HOẠT ĐỘNG"

**Cách 2: Update database trực tiếp**
```sql
UPDATE simulator_settings 
SET setting_value = '1' 
WHERE setting_key = 'simulator_enabled';
```

### Bước 4: Tạo ca làm việc có máy và nhân viên

1. Tạo shift với status = 'running'
2. Assign machines vào shift (qua line)
3. Assign staff vào từng machine

**Lưu ý:** Simulator chỉ tạo dữ liệu cho:
- Shifts có `shift_status = 'running'` (hoặc 'planned' nếu config)
- Machines đã có trong `shift_machine_staff`

### Bước 5: Xem dữ liệu

**Trang Shift Detail:**
1. Mở ca: `http://localhost/leader/shift/detail/{shift_id}`
2. Click tab "Sản lượng"
3. Dữ liệu sẽ tự động load và refresh

**Dữ liệu hiển thị:**
- 📊 **Summary Cards**: Tổng hợp theo từng máy
  - Tổng thành phẩm / phế phẩm
  - Hiệu suất trung bình
  - Tỷ lệ lỗi trung bình
  - Tổng downtime
- 📋 **Records Table**: Chi tiết từng lần ghi nhận
  - Thời gian ghi
  - Máy và nhân viên
  - Số lượng chi tiết
  - Hiệu suất và downtime

---

## Cơ chế hoạt động

### Auto-Polling Flow

```
[Page Load]
    ↓
Check Simulator Status (/status)
    ↓
Is Enabled?
    ├─ NO → Show "TẮT" message
    └─ YES → Start Polling
              ↓
        Run Simulator (/run) every [interval] seconds
              ↓
        For each Active Shift:
            ↓
        For each Machine in Shift:
            ↓
        Generate Random Data
            ↓
        Save to production_records
            ↓
        Auto-refresh tab if open
```

### Data Generation Logic

```javascript
// Thành phẩm (random)
good_count = random(good_count_min, good_count_max)

// Phế phẩm (dựa trên defect_rate)
defect_rate = random(defect_rate_min%, defect_rate_max%)
defect_count = good_count × (defect_rate / 100)

// Mục tiêu
target_count = good_count × target_multiplier

// Downtime (xác suất)
if (random(0,1) <= downtime_probability) {
    downtime_minutes = random(downtime_min, downtime_max)
    downtime_reason = random từ downtime_reasons table
} else {
    downtime_minutes = 0
}

// Hiệu suất
total_produced = good_count + defect_count
efficiency_rate = (total_produced / target_count) × 100
```

---

## Cấu hình nâng cao

### Điều chỉnh Interval

**Nhanh hơn (cho testing):**
```sql
UPDATE simulator_settings 
SET setting_value = '60' 
WHERE setting_key = 'simulator_interval';
-- Ghi nhận mỗi 1 phút
```

**Chậm hơn (giống thực tế):**
```sql
UPDATE simulator_settings 
SET setting_value = '900' 
WHERE setting_key = 'simulator_interval';
-- Ghi nhận mỗi 15 phút
```

### Điều chỉnh tỷ lệ lỗi thấp hơn

```sql
UPDATE simulator_settings 
SET setting_value = '0.5' 
WHERE setting_key = 'defect_rate_min';

UPDATE simulator_settings 
SET setting_value = '3' 
WHERE setting_key = 'defect_rate_max';
-- Defect rate: 0.5%-3% (chất lượng cao)
```

### Tăng xác suất downtime

```sql
UPDATE simulator_settings 
SET setting_value = '0.3' 
WHERE setting_key = 'downtime_probability';
-- 30% khả năng downtime mỗi lần ghi
```

### Chỉ giả lập ca đang chạy

```sql
UPDATE simulator_settings 
SET setting_value = '1' 
WHERE setting_key = 'simulate_active_shifts_only';
-- 1 = Chỉ ca 'running'
-- 0 = Cả ca 'planned' và 'running'
```

---

## API Endpoints

### GET `/leader/productionsimulator/settings`
Trang cấu hình UI

### POST `/leader/productionsimulator/run` (AJAX)
Chạy simulator một lần
- Tạo production records cho tất cả active shifts
- Returns: `{ success, total_records, shifts_count, simulated_shifts }`

### POST `/leader/productionsimulator/toggle` (AJAX)
Bật/tắt simulator
- Body: `enabled=1` hoặc `enabled=0`
- Returns: `{ success, message, enabled }`

### POST `/leader/productionsimulator/update_settings` (AJAX)
Cập nhật cấu hình
- Body: Form data với các setting keys
- Returns: `{ success, message, updated_count }`

### GET `/leader/productionsimulator/status` (AJAX)
Kiểm tra trạng thái simulator
- Returns: `{ success, enabled, interval, settings }`

### GET `/leader/productionsimulator/get_shift_records/{shift_id}` (AJAX)
Lấy dữ liệu sản lượng của ca
- Optional param: `?machine_id=X`
- Returns: `{ success, records[], summary[], count }`

---

## Troubleshooting

### ❌ Simulator không tạo dữ liệu

**Kiểm tra:**
1. Simulator đã BẬT? Check `/settings`
2. Có ca nào `status='running'`? Check `production_shifts`
3. Ca có máy được assign? Check `shift_machine_staff`
4. Browser console có lỗi? Check F12 → Console
5. PHP errors? Check `application/logs/`

**Debug queries:**
```sql
-- Check simulator enabled
SELECT * FROM simulator_settings WHERE setting_key = 'simulator_enabled';

-- Check active shifts
SELECT * FROM production_shifts WHERE shift_status = 'running';

-- Check machines in shifts
SELECT sms.*, m.code, m.name 
FROM shift_machine_staff sms
JOIN machines m ON sms.machine_id = m.id
WHERE sms.shift_id = YOUR_SHIFT_ID;

-- Check if data being created
SELECT * FROM production_records 
WHERE is_simulated = 1 
ORDER BY timestamp DESC 
LIMIT 10;
```

### ❌ Tab "Sản lượng" không hiển thị

**Nguyên nhân:**
- File `detail.php` không được update đúng
- JavaScript conflict

**Giải pháp:**
1. Clear browser cache (Ctrl+Shift+Del)
2. Hard refresh (Ctrl+F5)
3. Check browser console for errors

### ❌ Auto-polling không chạy

**Kiểm tra:**
1. Simulator có BẬT không?
2. Browser tab có bị minimize/hidden? (polling pause khi tab inactive)
3. Console log: "Simulator polling started"?

**Force manual run:**
```javascript
// Mở browser console (F12) và chạy:
runSimulator();
```

### ❌ Settings không lưu

**Kiểm tra:**
1. Có quyền truy cập? Check role: `bod/system_admin/line_manager/leader/admin`
2. AJAX request success? Check Network tab in F12
3. Database permissions? Check MySQL user privileges

---

## Xóa dữ liệu giả lập cũ

### Xóa dữ liệu cũ hơn 30 ngày
```sql
DELETE FROM production_records 
WHERE is_simulated = 1 
  AND timestamp < DATE_SUB(NOW(), INTERVAL 30 DAY);
```

### Xóa tất cả dữ liệu giả lập
```sql
DELETE FROM production_records WHERE is_simulated = 1;
```

### Xóa dữ liệu của 1 ca
```sql
DELETE FROM production_records 
WHERE shift_id = YOUR_SHIFT_ID 
  AND is_simulated = 1;
```

---

## Best Practices

### ✅ Development/Testing
- **Interval:** 60-120 giây (1-2 phút)
- **Good count:** 50-200
- **Defect rate:** 1%-8%
- **Downtime probability:** 0.15 (15%)

### ✅ Demo/Presentation
- **Interval:** 60 giây (refresh nhanh để demo)
- **Good count:** 100-300 (số đẹp)
- **Defect rate:** 2%-5% (realistic)
- **Downtime probability:** 0.1 (10%, ít downtime hơn)

### ✅ Production-like Simulation
- **Interval:** 300-900 giây (5-15 phút)
- **Good count:** Dựa trên machine capacity thực tế
- **Defect rate:** 0.5%-3% (chất lượng tốt)
- **Downtime probability:** 0.05-0.1 (5-10%)

### ⚠️ Lưu ý hiệu suất
- Interval < 60 giây: Có thể tạo nhiều records, cần cleanup định kỳ
- Interval > 3600 giây: Dữ liệu quá ít, không đủ demo
- Nên cleanup dữ liệu cũ > 30 ngày để giảm database size

---

## Security

### Quyền truy cập
Controller chỉ cho phép roles:
- `bod`
- `system_admin`
- `line_manager`
- `leader`
- `admin`

### AJAX Protection
Tất cả endpoints check `$this->input->is_ajax_request()`

### Input Validation
- `machine_role` enum validation
- `simulator_interval` range: 60-3600
- Setting types enforced (boolean/integer/float)

---

## Future Enhancements

### 🔮 Planned Features
1. **Real-time Dashboard**
   - Live chart cập nhật real-time khi có dữ liệu mới
   - WebSocket hoặc Server-Sent Events

2. **Machine Learning Predictions**
   - Dự đoán defect rate dựa trên historical data
   - Alert khi efficiency giảm bất thường

3. **Export Reports**
   - Export production data to Excel/PDF
   - Scheduled email reports

4. **Advanced Downtime Tracking**
   - Root cause analysis
   - MTBF/MTTR calculations
   - Downtime cost estimation

5. **Staff Performance Metrics**
   - So sánh hiệu suất giữa nhân viên
   - Training recommendations

---

## Changelog

### Version 1.0 (2025-12-18)
- ✅ Initial release
- ✅ Migration 016 created
- ✅ Auto-polling simulator
- ✅ Settings UI
- ✅ Production tab in shift detail
- ✅ Summary cards and records table

---

## Support

**Documentation:**
- This file: `PRODUCTION_SIMULATOR_GUIDE.md`
- Migration: `db/migrations/Cap2/CaLamViec/016_create_production_simulator_tables.sql`

**Files to check:**
- Model: `application/models/leader/ProductionSimulatorModel.php`
- Controller: `application/controllers/leader/ProductionSimulator.php`
- Settings View: `application/views/leader/simulator/settings.php`
- Shift Detail: `application/views/leader/shift/detail.php` (production tab)

**Contact:** Development Team
