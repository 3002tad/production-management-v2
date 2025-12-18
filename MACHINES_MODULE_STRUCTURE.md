# Cấu Trúc Module Machines - Xác Định Lại

## 📋 Tổng Quan

### **Cấu trúc phân cấp:**
```
Zone (Khu vực)
├── Production Line (Dây chuyền)
│   ├── Line Type: production_raw (Sản xuất thô)
│   ├── Line Type: assembly_qc (Lắp ráp - QC)
│   └── Line Type: virtual (Line ảo điều phối)
└── Machines (Máy móc)
    ├── Machine Role: primary (Máy chính)
    └── Machine Role: backup (Máy dự phòng)
```

---

## 🏭 1. ZONES (Khu vực sản xuất)

### **Trạng thái hiện tại:**
| Zone Code | Zone Name | Status | Mô tả |
|-----------|-----------|--------|-------|
| **ZONE_A** | Khu A | ✅ **Đang hoạt động** | Khu sản xuất chính |
| ZONE_B | Khu B | ⏸️ Dự trữ | Chờ mở rộng sản xuất |
| ZONE_C | Khu C | ⏸️ Dự trữ | Chờ mở rộng sản xuất |

### **Database Schema:**
```sql
zones (
  zone_id INT PK,
  zone_code VARCHAR(50) UNIQUE,
  zone_name VARCHAR(100),
  description TEXT,
  status TINYINT(2) -- 1=Hoạt động, 0=Ngừng
)
```

---

## 🔧 2. PRODUCTION LINES (Dây chuyền sản xuất)

### **Quy tắc:**
- ✅ Mỗi Zone **luôn có 2 Line chính cố định**:
  1. **Line Sản xuất thô** (`production_raw`)
  2. **Line Lắp ráp - QC** (`assembly_qc`)
- ✅ Cho phép tạo thêm **1 Line ảo** (`virtual`) để điều phối khi:
  - Ép công suất
  - Sự cố lớn
- ❌ **Không được xóa Line chính** (is_primary = 1)

### **Database Schema:**
```sql
production_lines (
  id INT PK,
  zone_id INT FK → zones.zone_id,
  line_code VARCHAR(50) UNIQUE,
  line_name VARCHAR(100),
  line_type ENUM('production_raw', 'assembly_qc', 'virtual'),
  is_primary TINYINT(1), -- 1=Line chính (cố định), 0=Line phụ/ảo
  capacity_per_hour INT,
  status TINYINT(2)
)
```

### **Ví dụ Zone A:**
| Line Code | Line Name | Line Type | Is Primary | Machines |
|-----------|-----------|-----------|------------|----------|
| LINE01 | Dây chuyền sản xuất thô | production_raw | 1 | 4-5 máy |
| LINE02 | Dây chuyền lắp ráp - QC | assembly_qc | 1 | 4-5 máy |
| LINE_VIRTUAL_01 | Line điều phối ảo | virtual | 0 | Động (khi cần) |

---

## ⚙️ 3. MACHINES (Máy móc)

### **Phân loại:**
1. **Máy chính** (`primary`): Máy hoạt động thường xuyên, gán cố định vào Line
2. **Máy dự phòng** (`backup`): Máy để sẵn, thay thế khi máy chính bị sự cố

### **Database Schema:**
```sql
machines (
  id INT PK,
  line_id INT FK → production_lines.id,
  code VARCHAR(20) UNIQUE,
  name VARCHAR(100),
  machine_role ENUM('primary', 'backup'), -- NEW
  stage_type ENUM('molding', 'assembly', 'packaging', 'quality_check', 'other'),
  equipment_category VARCHAR(50), -- 'production', 'quality_control', 'maintenance'
  status ENUM('active', 'maintenance', 'inactive', 'broken'),
  capacity DECIMAL(10,2),
  description TEXT
)
```

### **Quy tắc phân bổ máy:**
- **Line Sản xuất thô**: Gán 4-5 máy có cùng nhiệm vụ (ép nhựa, đúc, cắt...)
- **Line Lắp ráp - QC**: Gán 4-5 máy (lắp ráp + máy kiểm định chất lượng)
- **Line ảo**: Không gán máy cố định, chỉ điều phối khi cần
- **Máy backup**: Không gán line_id, hoặc gán line_id nhưng role=backup

---

## 🔄 4. WORKFLOW

### **Khi tạo Zone mới (mở rộng sản xuất):**
```
1. Tạo Zone mới (ZONE_B, ZONE_C...)
2. Tự động tạo 2 Line chính:
   - LINE_B01 (production_raw, is_primary=1)
   - LINE_B02 (assembly_qc, is_primary=1)
3. Cho phép tạo LINE_VIRTUAL_B01 nếu cần
4. Gán máy primary vào 2 line chính
```

### **Khi máy chính bị sự cố:**
```
1. Đánh dấu máy chính: status = 'broken'
2. Tìm máy backup cùng stage_type
3. Thay thế: Cập nhật line_id của máy backup
4. Log vào machine_status_logs
```

### **Khi ép công suất:**
```
1. Kích hoạt Line ảo (virtual)
2. Tạm thời gán máy backup vào line ảo
3. Tạo ca làm việc cho line ảo
4. Sau khi xong → Ngừng line ảo, trả máy backup
```

---

## 📊 5. QUERIES MẪU

### **Lấy danh sách Line của Zone A:**
```sql
SELECT 
    pl.line_code, 
    pl.line_name, 
    pl.line_type,
    pl.is_primary,
    COUNT(m.id) as total_machines,
    SUM(CASE WHEN m.machine_role = 'primary' THEN 1 ELSE 0 END) as primary_machines,
    SUM(CASE WHEN m.machine_role = 'backup' THEN 1 ELSE 0 END) as backup_machines
FROM production_lines pl
LEFT JOIN machines m ON m.line_id = pl.id AND m.status = 'active'
WHERE pl.zone_id = 1
GROUP BY pl.id
ORDER BY pl.is_primary DESC, pl.line_type;
```

### **Lấy danh sách máy backup:**
```sql
SELECT 
    m.code, 
    m.name, 
    m.stage_type,
    m.equipment_category,
    m.status
FROM machines m
WHERE m.machine_role = 'backup'
AND m.status IN ('active', 'maintenance')
ORDER BY m.stage_type, m.code;
```

### **Tìm máy backup để thay thế:**
```sql
-- Tìm máy backup có cùng stage_type và equipment_category
SELECT m.*
FROM machines m
WHERE m.machine_role = 'backup'
AND m.status = 'active'
AND m.stage_type = ? -- stage_type của máy bị hỏng
AND m.equipment_category = ? -- equipment_category của máy bị hỏng
ORDER BY m.capacity DESC
LIMIT 1;
```

---

## ✅ 6. CHECKLIST MIGRATION

- [x] Tạo migration 015_restructure_zones_lines_machines.sql
- [ ] Chạy migration trên database
- [ ] Kiểm tra zones: 1 active, 2 inactive
- [ ] Kiểm tra production_lines: 2 line chính + 1 line ảo
- [ ] Kiểm tra machines: có phân primary/backup
- [ ] Cập nhật UI:
  - [ ] Zone list: Hiển thị status (active/inactive)
  - [ ] Line list: Hiển thị line_type và is_primary
  - [ ] Machine list: Hiển thị machine_role (Primary/Backup)
  - [ ] Machine form: Thêm dropdown chọn machine_role
  - [ ] Thêm trang "Máy dự phòng" riêng biệt
- [ ] Cập nhật logic:
  - [ ] Khi tạo zone mới → tự động tạo 2 line chính
  - [ ] Khi xóa line → check is_primary (không cho xóa line chính)
  - [ ] Khi máy bị sự cố → suggest máy backup thay thế
  - [ ] Khi tạo ca → chỉ cho chọn line có is_primary=1 hoặc line_type='virtual'

---

## 🎯 7. UI/UX SUGGESTIONS

### **Machine Management Page:**
```
┌─────────────────────────────────────────┐
│ [Tất cả máy] [Máy chính] [Máy dự phòng]│ ← Tabs
├─────────────────────────────────────────┤
│ Line: [All ▼] | Type: [All ▼] | Search │
├─────────────────────────────────────────┤
│ AS001 - Máy ép nhựa 1    [PRIMARY] 🟢  │
│ AS002 - Máy ép nhựa 2    [PRIMARY] 🟢  │
│ AS003 - Máy ép nhựa 3    [BACKUP]  🟡  │
│ ML001 - Máy đúc kim loại [PRIMARY] 🟢  │
│ ML002 - Máy đúc dự phòng [BACKUP]  🟡  │
└─────────────────────────────────────────┘
```

### **Zone Overview:**
```
┌─────────────────────────────────────────┐
│ ZONE A - Khu sản xuất chính     [ACTIVE]│
├─────────────────────────────────────────┤
│ ├─ LINE01: Sản xuất thô (5 máy)        │
│ ├─ LINE02: Lắp ráp - QC (4 máy)        │
│ └─ LINE_VIRTUAL_01: Điều phối (0 máy)  │
├─────────────────────────────────────────┤
│ ZONE B - Khu dự trữ            [INACTIVE]│
│ └─ Chưa có dây chuyền                   │
└─────────────────────────────────────────┘
```

---

**Updated:** 2025-12-18  
**Status:** ✅ Migration ready to run  
**Next Step:** Chạy migration 015, sau đó cập nhật UI/UX
