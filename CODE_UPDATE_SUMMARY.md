# ✅ Code Update Summary - RBAC Migration

## 📅 Date: November 2, 2025

---

## 🎯 Tóm tắt

Đã cập nhật code để **hỗ trợ RBAC system** với **backward compatibility** (tương thích ngược với hệ thống cũ).

---

## 📝 Files đã update

### ✅ 1. LoginModel.php (`application/models/LoginModel.php`)

**Changes:**
- ✅ `check_login()` giờ JOIN với bảng `roles` để lấy thông tin role
- ✅ SELECT thêm: `role_id`, `role_name`, `role_display_name`, `level`, `full_name`, `email`
- ✅ Check `is_active = 1` để chỉ cho phép active users login
- ✅ Thêm method `get_user_by_id()` - Lấy user info kèm role
- ✅ Thêm method `update_last_login()` - Update timestamp login cuối
- ✅ Thêm method `log_activity()` - Ghi audit log
- ✅ `is_role()` hỗ trợ cả `role_name` (new) và `role` (old)

**SQL Query CŨ:**
```sql
SELECT * FROM user 
WHERE username = 'xxx' AND password = 'xxx'
```

**SQL Query MỚI:**
```sql
SELECT 
  u.user_id, u.username, u.password, 
  u.role_id, u.full_name, u.email, u.phone, u.is_active,
  r.role_name, r.role_display_name, r.level, r.description
FROM user u
LEFT JOIN roles r ON r.role_id = u.role_id
WHERE username = 'xxx' AND password = 'xxx' AND u.is_active = 1
```

---

### ✅ 2. Login.php (`application/controllers/Login.php`)

**Changes:**
- ✅ Session data mở rộng: Thêm `role_id`, `role_name`, `role_display_name`, `full_name`, `email`, `level`
- ✅ **XÓA** `password` khỏi session (bảo mật)
- ✅ Redirect theo `role_name` với 7 roles (BOD, System Admin, Line Manager, Warehouse, QC, Technical, Worker)
- ✅ Gọi `update_last_login()` sau khi login thành công
- ✅ Gọi `log_activity()` để ghi audit log (login/logout)
- ✅ Thêm method `redirect_by_role()` - Linh hoạt redirect theo role
- ✅ Thêm method `logout()` - Logout với audit log
- ✅ Backward compatibility: Hỗ trợ cả session cũ (`role`) và mới (`role_name`)

**Session Data CŨ:**
```php
[
  'user_id' => 1,
  'username' => 'admin',
  'password' => 'admin',  // ❌ Không an toàn
  'role' => 'admin'        // ❌ Chỉ 2 giá trị
]
```

**Session Data MỚI:**
```php
[
  'user_id' => 1,
  'username' => 'admin',
  'role_id' => 4,                          // ✅ INT (1-7)
  'role_name' => 'system_admin',           // ✅ String code
  'role_display_name' => 'Quản trị viên',  // ✅ Hiển thị
  'full_name' => 'Administrator',
  'email' => 'admin@company.com',
  'level' => 90                            // ✅ Hierarchy
]
```

**Redirect Logic MỚI:**
```php
switch ($role_name) {
    case 'bod': redirect('admin/'); break;
    case 'system_admin': redirect('admin/'); break;
    case 'line_manager': redirect('leader/'); break;
    case 'warehouse_staff': redirect('leader/'); break; // Temporary
    case 'qc_staff': redirect('leader/'); break;        // Temporary
    case 'technical_staff': redirect('leader/'); break; // Temporary
    case 'worker': redirect('leader/'); break;          // Temporary
}
```

---

### ✅ 3. Admin.php (`application/controllers/Admin.php`)

**Changes:**
- ✅ Check login trước khi check role
- ✅ RBAC check: Cho phép `bod` và `system_admin` (level >= 90)
- ✅ Backward compatibility: Vẫn cho phép `role = 'admin'` (old system)
- ✅ Show error 403 thay vì redirect về login (rõ ràng hơn)

**Check Logic CŨ:**
```php
if ($this->session->userdata('role') !== 'admin') {
    redirect('login/');
}
```

**Check Logic MỚI:**
```php
// Check login
if (!$this->session->userdata('user_id')) {
    redirect('login/');
}

// RBAC check
$role_name = $this->session->userdata('role_name');
$level = $this->session->userdata('level');

$has_access = false;

// New system
if ($role_name) {
    $allowed_roles = ['bod', 'system_admin'];
    $has_access = in_array($role_name, $allowed_roles) || ($level >= 90);
}
// Old system fallback
elseif ($this->session->userdata('role') === 'admin') {
    $has_access = true;
}

if (!$has_access) {
    show_error('Access Denied - Admin Only', 403);
}
```

---

### ✅ 4. Leader.php (`application/controllers/Leader.php`)

**Changes:**
- ✅ Check login trước khi check role
- ✅ RBAC check: Cho phép 6 roles (BOD, System Admin, Line Manager, Warehouse, QC, Technical)
- ✅ Level check: `level >= 50`
- ✅ Backward compatibility: Vẫn cho phép `role = 'leader'` hoặc `'admin'`
- ✅ Show error 403 thay vì redirect

**Check Logic MỚI:**
```php
$allowed_roles = [
    'bod', 
    'system_admin', 
    'line_manager', 
    'warehouse_staff',  // Temporary - tạo controller riêng sau
    'qc_staff',         // Temporary
    'technical_staff'   // Temporary
];

$has_access = in_array($role_name, $allowed_roles) || ($level >= 50);

// Old system fallback
if ($this->session->userdata('role') === 'leader' || 
    $this->session->userdata('role') === 'admin') {
    $has_access = true;
}
```

---

## 🔄 Migration Path

### **Phase 1: Hybrid Mode (HIỆN TẠI)** ✅

Hệ thống hỗ trợ **CẢ HAI**:
- ✅ Cột `role` cũ (enum 'admin','leader') - Cho users chưa migrate
- ✅ Cột `role_id` mới (INT) - Cho users đã migrate

**Khi login:**
1. Nếu user có `role_id` → Session chứa `role_id`, `role_name`, `level`
2. Nếu user chỉ có `role` → Session chứa `role` (backward compat)

**Controllers:**
- Check `role_name` trước (new system)
- Fallback về `role` nếu không có (old system)

---

### **Phase 2: Full RBAC (SAU KHI CHẠY MIGRATION 006)** 🔥

Sau khi chạy `006_migrate_to_full_rbac.sql`:
- ❌ Cột `role` bị XÓA
- ✅ Chỉ còn `role_id` (NOT NULL)
- ✅ Tất cả users BẮT BUỘC phải có role_id

**Backward compatibility code sẽ không còn cần thiết:**
```php
// Code này sẽ không bao giờ chạy sau migration 006
elseif ($this->session->userdata('role') === 'admin') {
    $has_access = true;
}
```

---

## 🧪 Testing Checklist

### ✅ Test Login

#### **Test với users CŨ** (có cột `role`)
```
Username: admin
Password: admin
Expected: Login thành công, redirect về /admin
Session: Có cả 'role' và 'role_id' (nếu đã chạy migration 002)
```

```
Username: leader
Password: leader
Expected: Login thành công, redirect về /leader
Session: Có cả 'role' và 'role_id' (nếu đã chạy migration 002)
```

#### **Test với users MỚI** (có cột `role_id`)
```
Username: bod
Password: bod123
Expected: Login thành công, redirect về /admin
Session: role_name='bod', role_display_name='Ban Giám Đốc', level=100
```

```
Username: warehouse
Password: wh123
Expected: Login thành công, redirect về /leader (temporary)
Session: role_name='warehouse_staff', level=50
```

```
Username: qc
Password: qc123
Expected: Login thành công, redirect về /leader (temporary)
Session: role_name='qc_staff', level=60
```

```
Username: technical
Password: tech123
Expected: Login thành công, redirect về /leader (temporary)
Session: role_name='technical_staff', level=60
```

```
Username: worker
Password: worker123
Expected: Login thành công, redirect về /leader (temporary)
Session: role_name='worker', level=10
```

---

### ✅ Test Access Control

#### **Admin Panel** (`/admin`)
```
✅ BOD (level 100)           → CÓ QUYỀN
✅ System Admin (level 90)   → CÓ QUYỀN
✅ Old 'admin' role          → CÓ QUYỀN (backward compat)
❌ Line Manager (level 70)   → 403 Forbidden
❌ Warehouse (level 50)      → 403 Forbidden
❌ Worker (level 10)         → 403 Forbidden
❌ Old 'leader' role         → 403 Forbidden
```

#### **Leader Panel** (`/leader`)
```
✅ BOD (level 100)           → CÓ QUYỀN
✅ System Admin (level 90)   → CÓ QUYỀN
✅ Line Manager (level 70)   → CÓ QUYỀN
✅ QC Staff (level 60)       → CÓ QUYỀN
✅ Technical (level 60)      → CÓ QUYỀN
✅ Warehouse (level 50)      → CÓ QUYỀN
✅ Old 'admin' role          → CÓ QUYỀN (backward compat)
✅ Old 'leader' role         → CÓ QUYỀN (backward compat)
❌ Worker (level 10)         → 403 Forbidden
```

---

### ✅ Test Session Data

Sau khi login, check session:
```php
// Trong controller bất kỳ
var_dump($this->session->all_userdata());

// Expected với user MỚI (có role_id):
array(
  'user_id' => 4,
  'username' => 'bod',
  'role_id' => 1,
  'role_name' => 'bod',
  'role_display_name' => 'Ban Giám Đốc',
  'full_name' => 'Nguyễn Văn A - Giám Đốc',
  'email' => 'bod@company.com',
  'level' => 100
)

// Expected với user CŨ (chỉ có role):
array(
  'user_id' => 1,
  'username' => 'admin',
  'role_id' => 4,                    // ✅ Nếu đã chạy migration 002
  'role_name' => 'system_admin',     // ✅ Nếu đã chạy migration 002
  'role_display_name' => 'Quản trị viên',
  'full_name' => 'Administrator',
  'level' => 90
)
```

---

### ✅ Test Audit Log

```sql
-- Check login logs
SELECT * FROM audit_log 
WHERE action = 'login' 
ORDER BY created_at DESC 
LIMIT 10;

-- Expected output:
| log_id | user_id | username | action | module | ip_address  | created_at          |
|--------|---------|----------|--------|--------|-------------|---------------------|
| 1      | 1       | admin    | login  | auth   | 127.0.0.1   | 2025-11-02 10:30:00 |
| 2      | 4       | bod      | login  | auth   | 127.0.0.1   | 2025-11-02 10:31:00 |
| 3      | 1       | admin    | logout | auth   | 127.0.0.1   | 2025-11-02 10:35:00 |

-- Check last_login updated
SELECT username, last_login FROM user ORDER BY last_login DESC;
```

---

## 🐛 Known Issues & Workarounds

### Issue 1: "Undefined property $session, $login, etc."

**Nguyên nhân:** IDE không hiểu CodeIgniter magic properties  
**Giải pháp:** Ignore - Đây là false positive, code vẫn chạy bình thường

### Issue 2: User cũ không có role_id

**Nguyên nhân:** Chưa chạy migration 002  
**Giải pháp:** Chạy migration 002 để update users cũ:
```bash
mysql -u root -p db_production < migrations/002_seed_roles_data.sql
```

### Issue 3: Login bị redirect loop

**Nguyên nhân:** Session không có cả `role` và `role_name`  
**Giải pháp:** 
1. Clear session: Logout
2. Clear browser cookies
3. Login lại

---

## 📋 Next Steps

### Immediate (Đã hoàn thành) ✅
- [x] Update LoginModel với RBAC support
- [x] Update Login controller với session mở rộng
- [x] Update Admin controller với RBAC check
- [x] Update Leader controller với RBAC check
- [x] Backward compatibility với hệ thống cũ

### Short-term (Tuần này)
- [ ] Test đầy đủ với tất cả 7 roles
- [ ] Chạy migration 006 để xóa cột `role` cũ (nếu sẵn sàng)
- [ ] Tạo controllers riêng cho: Warehouse, QC, Technical, Worker
- [ ] Update views để hiển thị `role_display_name` thay vì `role`

### Medium-term (Tháng này)
- [ ] PHASE 2: Tạo AuthModel, Auth library
- [ ] Implement permission checking: `$this->auth->require_permission('customer.create')`
- [ ] Update views với `can()` helper
- [ ] Tạo admin panel để quản lý users & roles

### Long-term
- [ ] Fine-grained permissions cho từng action
- [ ] Role hierarchy & inheritance
- [ ] Dynamic menu based on permissions
- [ ] Audit log viewer/dashboard

---

## 🎉 Summary

✅ **Code đã update thành công!**
- ✅ Hỗ trợ 7 roles mới (BOD, System Admin, Line Manager, Warehouse, QC, Technical, Worker)
- ✅ Backward compatible với hệ thống cũ (admin/leader)
- ✅ Session data mở rộng với thông tin role đầy đủ
- ✅ Audit log cho login/logout
- ✅ Security improvements (không lưu password vào session)
- ✅ Level-based access control

**Status:** READY FOR TESTING 🚀

**Người cập nhật:** GitHub Copilot  
**Ngày:** November 2, 2025
