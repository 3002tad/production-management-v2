# 🔥 MIGRATION 006 - Checklist & Code Updates

## ⚠️ BREAKING CHANGE WARNING

Migration này sẽ **XÓA HOÀN TOÀN** cột `role` cũ và bắt buộc dùng `role_id`.

---

## 📋 Pre-Migration Checklist

### ✅ Trước khi chạy migration 006:

- [ ] **Backup database**
  ```bash
  mysqldump -u root -p db_production > backup_before_migration_006.sql
  ```

- [ ] **Verify migrations 001-005 đã chạy thành công**
  ```sql
  SELECT COUNT(*) FROM roles;        -- Expected: 7
  SELECT COUNT(*) FROM permissions;  -- Expected: 174+
  SELECT COUNT(*) FROM role_permissions; -- Expected: 400+
  ```

- [ ] **Kiểm tra tất cả users đã có role_id**
  ```sql
  SELECT user_id, username, role, role_id 
  FROM user 
  WHERE role_id IS NULL;
  -- Expected: Empty result
  ```

- [ ] **Chuẩn bị file code cần update:**
  - [ ] `application/models/LoginModel.php`
  - [ ] `application/controllers/Admin.php`
  - [ ] `application/controllers/Leader.php`
  - [ ] `application/views/` (nếu có hiển thị role)

- [ ] **Thông báo team về downtime**
  - Dự kiến: 2-3 giờ để update code + test

- [ ] **Có thời gian liên tục** để hoàn thành migration + update code

---

## 🚀 Migration Steps

### 1. Chạy Migration 006

```bash
mysql -u root -p db_production < migrations/006_migrate_to_full_rbac.sql
```

### 2. Verify Migration Success

```sql
-- Kiểm tra cột role đã bị xóa
SHOW COLUMNS FROM user LIKE 'role';
-- Expected: Empty set (0.00 sec) ✅

-- Kiểm tra role_id là NOT NULL
SHOW COLUMNS FROM user LIKE 'role_id';
-- Expected: NULL = "NO" ✅

-- Kiểm tra data migration
SELECT 
  u.username,
  u.role_id,
  r.role_name,
  r.role_display_name
FROM user u
LEFT JOIN roles r ON u.role_id = r.role_id;
```

---

## 💻 Code Updates Required

### ✅ 1. Update LoginModel.php

**File:** `application/models/LoginModel.php`

#### **CŨ (SẼ LỖI):**
```php
public function check_login($username, $password)
{
    $this->db->select('user_id, username, password, role');
    $this->db->from('user');
    $this->db->where('username', $username);
    $this->db->where('password', $password);
    $query = $this->db->get();
    
    return $query->row();
}
```

#### **MỚI (SAU MIGRATION):**
```php
public function check_login($username, $password)
{
    $this->db->select('
        u.user_id, 
        u.username, 
        u.password, 
        u.role_id,
        u.full_name,
        u.email,
        r.role_name,
        r.role_display_name,
        r.level
    ');
    $this->db->from('user u');
    $this->db->join('roles r', 'r.role_id = u.role_id', 'left');
    $this->db->where('u.username', $username);
    $this->db->where('u.password', $password);
    $this->db->where('u.is_active', 1); // ✅ Thêm check active
    $query = $this->db->get();
    
    return $query->row();
}
```

---

### ✅ 2. Update Login Controller

**File:** `application/controllers/Login.php`

#### **CŨ (SẼ LỖI):**
```php
public function do_login()
{
    $username = $this->input->post('username');
    $password = $this->input->post('password');
    
    $user = $this->LoginModel->check_login($username, $password);
    
    if ($user) {
        $this->session->set_userdata([
            'user_id' => $user->user_id,
            'username' => $user->username,
            'role' => $user->role  // ❌ Column không tồn tại
        ]);
        
        // Redirect based on role
        if ($user->role == 'admin') {
            redirect('admin');
        } else {
            redirect('leader');
        }
    }
}
```

#### **MỚI (SAU MIGRATION):**
```php
public function do_login()
{
    $username = $this->input->post('username');
    $password = $this->input->post('password');
    
    $user = $this->LoginModel->check_login($username, $password);
    
    if ($user) {
        // ✅ Update session data
        $this->session->set_userdata([
            'user_id' => $user->user_id,
            'username' => $user->username,
            'role_id' => $user->role_id,
            'role_name' => $user->role_name,
            'role_display_name' => $user->role_display_name,
            'full_name' => $user->full_name,
            'email' => $user->email,
            'level' => $user->level
        ]);
        
        // ✅ Log audit
        $this->db->insert('audit_log', [
            'user_id' => $user->user_id,
            'username' => $user->username,
            'action' => 'login',
            'module' => 'auth',
            'ip_address' => $this->input->ip_address(),
            'user_agent' => $this->input->user_agent()
        ]);
        
        // ✅ Update last_login
        $this->db->where('user_id', $user->user_id);
        $this->db->update('user', ['last_login' => date('Y-m-d H:i:s')]);
        
        // ✅ Redirect based on role_id or role_name
        switch ($user->role_name) {
            case 'bod':
                redirect('admin/dashboard'); // BOD dashboard
                break;
            case 'system_admin':
                redirect('admin'); // Admin panel
                break;
            case 'line_manager':
                redirect('leader'); // Line manager panel
                break;
            case 'warehouse_staff':
                redirect('warehouse'); // Warehouse panel
                break;
            case 'qc_staff':
                redirect('qc'); // QC panel
                break;
            case 'technical_staff':
                redirect('technical'); // Technical panel
                break;
            case 'worker':
                redirect('worker'); // Worker panel
                break;
            default:
                redirect('login');
        }
    } else {
        $this->session->set_flashdata('error', 'Username hoặc password không đúng!');
        redirect('login');
    }
}
```

---

### ✅ 3. Update Admin Controller

**File:** `application/controllers/Admin.php`

#### **CŨ (SẼ LỖI):**
```php
public function __construct()
{
    parent::__construct();
    
    // Check login
    if (!$this->session->userdata('user_id')) {
        redirect('login');
    }
    
    // Check role
    if ($this->session->userdata('role') != 'admin') {  // ❌ Lỗi
        redirect('login');
    }
}
```

#### **MỚI (SAU MIGRATION):**
```php
public function __construct()
{
    parent::__construct();
    
    // Check login
    if (!$this->session->userdata('user_id')) {
        redirect('login');
    }
    
    // ✅ Option 1: Check by role_id
    $allowed_roles = [1, 4]; // BOD, System Admin
    if (!in_array($this->session->userdata('role_id'), $allowed_roles)) {
        show_error('Access denied - Admin only', 403);
    }
    
    // ✅ Option 2: Check by role_name (flexible)
    $allowed_role_names = ['bod', 'system_admin'];
    if (!in_array($this->session->userdata('role_name'), $allowed_role_names)) {
        show_error('Access denied - Admin only', 403);
    }
    
    // ✅ Option 3: Check by level (recommended)
    if ($this->session->userdata('level') < 90) {
        show_error('Access denied - Insufficient permissions', 403);
    }
}
```

---

### ✅ 4. Update Leader Controller

**File:** `application/controllers/Leader.php`

#### **CŨ (SẼ LỖI):**
```php
public function __construct()
{
    parent::__construct();
    
    if (!$this->session->userdata('user_id')) {
        redirect('login');
    }
    
    if ($this->session->userdata('role') != 'leader') {  // ❌ Lỗi
        redirect('login');
    }
}
```

#### **MỚI (SAU MIGRATION):**
```php
public function __construct()
{
    parent::__construct();
    
    if (!$this->session->userdata('user_id')) {
        redirect('login');
    }
    
    // ✅ Check by role_name
    $allowed_roles = ['bod', 'line_manager', 'system_admin'];
    if (!in_array($this->session->userdata('role_name'), $allowed_roles)) {
        show_error('Access denied - Line Manager only', 403);
    }
}
```

---

### ✅ 5. Update Views

**File:** `application/views/admin/header.php` (hoặc tương tự)

#### **CŨ (SẼ LỖI):**
```php
<div class="user-info">
    <p>Xin chào, <?= $this->session->userdata('username') ?></p>
    <p>Role: <?= $this->session->userdata('role') ?></p>  <!-- ❌ Lỗi -->
</div>
```

#### **MỚI (SAU MIGRATION):**
```php
<div class="user-info">
    <p>Xin chào, <strong><?= $this->session->userdata('full_name') ?: $this->session->userdata('username') ?></strong></p>
    <p>Vai trò: <span class="badge badge-primary"><?= $this->session->userdata('role_display_name') ?></span></p>
</div>
```

---

## ✅ Testing Checklist

### 1. Test Login với từng role:

```
✅ BOD:              bod / bod123
✅ System Admin:     admin / admin
✅ Line Manager:     leader / leader
✅ Warehouse:        warehouse / wh123
✅ QC:               qc / qc123
✅ Technical:        technical / tech123
✅ Worker:           worker / worker123
```

### 2. Verify Session Data:

```php
// Trong controller bất kỳ
var_dump($this->session->all_userdata());

// Expected output:
array(
  'user_id' => 1,
  'username' => 'admin',
  'role_id' => 4,
  'role_name' => 'system_admin',
  'role_display_name' => 'Quản trị viên Hệ thống',
  'full_name' => 'Administrator',
  'email' => 'admin@company.com',
  'level' => 90
)
```

### 3. Test Access Control:

- [ ] Admin user CÓ THỂ truy cập `/admin`
- [ ] Leader user CÓ THỂ truy cập `/leader`
- [ ] Worker user KHÔNG THỂ truy cập `/admin` → 403 error
- [ ] Warehouse user KHÔNG THỂ truy cập `/leader` → 403 error

### 4. Verify Audit Log:

```sql
SELECT * FROM audit_log 
WHERE action = 'login' 
ORDER BY created_at DESC 
LIMIT 10;
```

---

## 🐛 Troubleshooting

### Lỗi: "Unknown column 'role' in 'field list'"

**Nguyên nhân:** Code vẫn dùng cột `role` cũ  
**Giải pháp:** Update code theo hướng dẫn trên

### Lỗi: "Call to undefined method"

**Nguyên nhân:** Session không có `role_name` hoặc `role_id`  
**Giải pháp:** 
1. Đăng xuất
2. Đăng nhập lại để tạo session mới
3. Hoặc clear session: `$this->session->sess_destroy()`

### Lỗi: "Cannot login"

**Nguyên nhân:** LoginModel chưa JOIN với bảng `roles`  
**Giải pháp:** Update LoginModel theo hướng dẫn trên

---

## 📝 Post-Migration Notes

- ✅ Cột `role` cũ đã bị xóa hoàn toàn
- ✅ Tất cả users giờ dùng `role_id` (INT NOT NULL)
- ✅ Session giờ chứa: role_id, role_name, role_display_name, level
- ✅ Có thể mở rộng thêm roles mới trong bảng `roles`
- ✅ Chuẩn bị cho PHASE 2: RBAC Permission checking

---

## 🎯 Next Steps

1. ✅ Migration 006 hoàn thành
2. ⏭️ Tiếp tục PHASE 2:
   - Tạo `AuthModel.php`
   - Tạo `Auth.php` library với methods: `can()`, `require_permission()`
   - Tạo `MY_Controller.php` với permission checking tự động
   - Update controllers để dùng RBAC permissions

---

**🎉 Chúc mừng! Hệ thống đã migrate HOÀN TOÀN sang RBAC!**
