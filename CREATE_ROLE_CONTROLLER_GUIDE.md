# 🎯 Hướng dẫn tạo Controller & View cho Role mới

## ✅ VÍ DỤ ĐÃ TẠO: Warehouse

Đã tạo thành công:
- ✅ `application/controllers/Warehouse.php`
- ✅ `application/views/warehouse/vbackend.php`
- ✅ `application/views/warehouse/dashboard.php`

Login với: `warehouse / wh123` sẽ tự redirect về `/warehouse/`

---

## 📋 CÁCH TẠO CONTROLLER MỚI CHO ROLE KHÁC

### **1. Tạo Controller** (Copy từ Warehouse.php)

**File:** `application/controllers/Qc.php` (hoặc Technical.php, Worker.php)

```php
<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Qc extends CI_Controller  // ⬅️ Đổi tên class
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('CrudModel', 'crudModel');
        $this->load->library('session');
        
        if (!$this->session->userdata('user_id')) {
            redirect('login/');
        }
        
        // ⬅️ ĐỔI allowed_roles
        $role_name = $this->session->userdata('role_name');
        $allowed_roles = ['bod', 'system_admin', 'qc_staff']; // ⬅️ Thay đổi ở đây
        
        if (!in_array($role_name, $allowed_roles)) {
            show_error('Access Denied - QC Staff Only', 403);
        }
    }

    public function index()
    {
        $data = [
            'content' => 'qc/dashboard',  // ⬅️ Đổi view path
            'navlink' => 'dashboard',
        ];
        $this->load->view('qc/vbackend', $data);  // ⬅️ Đổi backend view
    }
}
```

---

### **2. Tạo Backend View** (Copy từ warehouse/vbackend.php)

**File:** `application/views/qc/vbackend.php`

**Những chỗ cần đổi:**

```php
<!-- Line 61: Đổi icon & title -->
<div class="avatar">
    <i class="fas fa-check-circle"></i>  <!-- ⬅️ Icon cho QC -->
</div>

<!-- Line 74: Đổi menu links -->
<a class="nav-link" href="<?= base_url('qc/') ?>">  <!-- ⬅️ Đổi 'warehouse' thành 'qc' -->
    <i class="fas fa-tachometer-alt"></i> Dashboard
</a>
<a class="nav-link" href="<?= base_url('qc/inspection') ?>">
    <i class="fas fa-clipboard-check"></i> Kiểm tra chất lượng
</a>
<!-- ... thêm menu items khác -->

<!-- Line 103: Đổi title -->
<h4 class="mb-0">
    <i class="fas fa-check-circle text-primary"></i>
    QC Management System  <!-- ⬅️ Đổi title -->
</h4>
```

---

### **3. Tạo Dashboard View**

**File:** `application/views/qc/dashboard.php`

```php
<div class="container-fluid">
    <h2 class="mb-4">
        <i class="fas fa-check-circle"></i> QC Dashboard
    </h2>

    <!-- Statistics Cards -->
    <div class="row">
        <div class="col-md-4">
            <div class="stat-card">
                <h3><i class="fas fa-clipboard-check"></i> 25</h3>
                <p>Inspections Today</p>
            </div>
        </div>
        <!-- ... thêm cards khác -->
    </div>

    <!-- Content -->
    <div class="card">
        <div class="card-header">
            <h5><i class="fas fa-list"></i> Recent QC Reports</h5>
        </div>
        <div class="card-body">
            <!-- Table hoặc content ở đây -->
        </div>
    </div>
</div>
```

---

### **4. Update Login.php** (ĐÃ UPDATE SẴN)

File `Login.php` đã có check tự động:

```php
case 'qc_staff':
    if (file_exists(APPPATH . 'controllers/Qc.php')) {
        redirect('qc/');  // ✅ Tự động redirect
    } else {
        redirect('leader/'); // Fallback nếu chưa tạo controller
    }
    break;
```

---

## 🚀 QUICK CREATE TEMPLATES

### **Template cho QC Controller:**

```php
<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Qc extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('CrudModel', 'crudModel');
        $this->load->library('session');
        
        if (!$this->session->userdata('user_id')) {
            redirect('login/');
        }
        
        $allowed_roles = ['bod', 'system_admin', 'qc_staff'];
        if (!in_array($this->session->userdata('role_name'), $allowed_roles)) {
            show_error('Access Denied - QC Staff Only', 403);
        }
    }

    public function index()
    {
        $data = [
            'total_inspections' => 25, // Example
            'content' => 'qc/dashboard',
            'navlink' => 'dashboard',
        ];
        $this->load->view('qc/vbackend', $data);
    }

    public function inspection()
    {
        $data = [
            'content' => 'qc/inspection',
            'navlink' => 'inspection',
        ];
        $this->load->view('qc/vbackend', $data);
    }
}
```

---

### **Template cho Technical Controller:**

```php
<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Technical extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('CrudModel', 'crudModel');
        $this->load->library('session');
        
        if (!$this->session->userdata('user_id')) {
            redirect('login/');
        }
        
        $allowed_roles = ['bod', 'system_admin', 'technical_staff'];
        if (!in_array($this->session->userdata('role_name'), $allowed_roles)) {
            show_error('Access Denied - Technical Staff Only', 403);
        }
    }

    public function index()
    {
        $data = [
            'machines' => $this->crudModel->getData('machine')->result(),
            'content' => 'technical/dashboard',
            'navlink' => 'dashboard',
        ];
        $this->load->view('technical/vbackend', $data);
    }

    public function maintenance()
    {
        $data = [
            'content' => 'technical/maintenance',
            'navlink' => 'maintenance',
        ];
        $this->load->view('technical/vbackend', $data);
    }
}
```

---

### **Template cho Worker Controller:**

```php
<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Worker extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('CrudModel', 'crudModel');
        $this->load->library('session');
        
        if (!$this->session->userdata('user_id')) {
            redirect('login/');
        }
        
        $allowed_roles = ['bod', 'system_admin', 'line_manager', 'worker'];
        if (!in_array($this->session->userdata('role_name'), $allowed_roles)) {
            show_error('Access Denied - Worker Only', 403);
        }
    }

    public function index()
    {
        $data = [
            'my_shifts' => $this->db->query('SELECT * FROM plan_shift WHERE id_staff = ?', 
                [$this->session->userdata('staff_id')])->result(),
            'content' => 'worker/dashboard',
            'navlink' => 'dashboard',
        ];
        $this->load->view('worker/vbackend', $data);
    }

    public function my_shift()
    {
        $data = [
            'content' => 'worker/my_shift',
            'navlink' => 'my_shift',
        ];
        $this->load->view('worker/vbackend', $data);
    }
}
```

---

## 📋 CHECKLIST TẠO ROLE MỚI

### Cho QC Staff:
- [ ] Copy `Warehouse.php` → `Qc.php`
- [ ] Đổi class name: `class Qc`
- [ ] Đổi `$allowed_roles = ['bod', 'system_admin', 'qc_staff']`
- [ ] Copy folder `views/warehouse/` → `views/qc/`
- [ ] Đổi tất cả `warehouse/` thành `qc/` trong views
- [ ] Đổi icons & titles
- [ ] Test login: `qc / qc123`

### Cho Technical Staff:
- [ ] Copy `Warehouse.php` → `Technical.php`
- [ ] Đổi class name: `class Technical`
- [ ] Đổi `$allowed_roles = ['bod', 'system_admin', 'technical_staff']`
- [ ] Copy folder `views/warehouse/` → `views/technical/`
- [ ] Đổi tất cả `warehouse/` thành `technical/` trong views
- [ ] Test login: `technical / tech123`

### Cho Worker:
- [ ] Copy `Warehouse.php` → `Worker.php`
- [ ] Đổi class name: `class Worker`
- [ ] Đổi `$allowed_roles = ['worker', 'line_manager']`
- [ ] Copy folder `views/warehouse/` → `views/worker/`
- [ ] Test login: `worker / worker123`

---

## 🎨 ICON RECOMMENDATIONS

| Role | Font Awesome Icon | Color Gradient |
|------|-------------------|----------------|
| Warehouse | `fa-warehouse` | `#667eea → #764ba2` (Purple) |
| QC | `fa-check-circle` | `#f093fb → #f5576c` (Pink) |
| Technical | `fa-tools` | `#4facfe → #00f2fe` (Blue) |
| Worker | `fa-user-hard-hat` | `#43e97b → #38f9d7` (Green) |
| BOD | `fa-crown` | `#fa709a → #fee140` (Gold) |

---

## 🧪 TEST LOGIN REDIRECT

```
✅ warehouse / wh123  → Redirect: /warehouse/ (VÌ ĐÃ TẠO CONTROLLER)
⏳ qc / qc123         → Redirect: /leader/ (CHƯA TẠO CONTROLLER)
⏳ technical / tech123 → Redirect: /leader/ (CHƯA TẠO CONTROLLER)
⏳ worker / worker123  → Redirect: /leader/ (CHƯA TẠO CONTROLLER)
```

Khi tạo xong controller → Tự động redirect đúng route!

---

## 💡 TIP: Copy nhanh với CMD

```cmd
# Tạo QC controller
copy application\controllers\Warehouse.php application\controllers\Qc.php

# Tạo QC views folder
xcopy application\views\warehouse application\views\qc\ /E /I
```

Sau đó Find & Replace:
- `Warehouse` → `Qc`
- `warehouse/` → `qc/`
- `fa-warehouse` → `fa-check-circle`
- `Warehouse Management` → `QC Management`

---

**🎉 VẬY LÀ XONG! Mỗi role sẽ tự redirect đúng panel của mình!**
