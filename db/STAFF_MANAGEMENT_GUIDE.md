# 📋 HƯỚNG DẪN TRIỂN KHAI CHỨC NĂNG QUẢN LÝ NHÂN VIÊN (STAFF MANAGEMENT)

**Ngày tạo:** December 12, 2025
**Phiên bản:** 1.0
**Người tạo:** System Analyst

---

## 🎯 MỤC ĐÍCH VÀ PHẠM VI

Bạn được giao nhiệm vụ triển khai **hệ thống quản lý nhân viên hoàn chỉnh** với các chức năng chính:

- ✅ Xem danh sách nhân viên với bộ lọc và thống kê
- ✅ Thêm nhân viên mới
- ✅ Sửa thông tin nhân viên
- ✅ Xóa nhân viên (với điều kiện ràng buộc)
- ✅ Ghi log audit cho tất cả thao tác

**Quan trọng:** Hệ thống này phải **tích hợp chặt chẽ** với hệ thống quản lý người dùng (UC6) hiện tại.

---

## 🗄️ HIỂU VỀ DATABASE

### Cấu trúc bảng `staff`

```sql
CREATE TABLE `staff` (
  `id_staff` int(11) NOT NULL,           -- Mã nhân viên (PK, auto increment từ 1001)
  `staff_name` varchar(50) NOT NULL,     -- Tên nhân viên (bắt buộc)
  `phone` varchar(20) DEFAULT NULL,      -- Số điện thoại
  `email` varchar(100) DEFAULT NULL,     -- Email (phải unique trong bảng staff)
  `department` varchar(100) DEFAULT NULL,-- Bộ phận (có thể null, mặc định "Chưa Phân Loại")
  `position` varchar(100) DEFAULT NULL,  -- Chức vụ (có thể null, mặc định "Chưa Phân Loại")
  `st_status` int(2) NOT NULL,           -- Trạng thái (1=active, 2=inactive)
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Indexes
ALTER TABLE `staff` ADD PRIMARY KEY (`id_staff`);
ALTER TABLE `staff` MODIFY `id_staff` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1034;
```

### Quan hệ với các bảng khác

- **Liên kết với `user`:** Qua trường `staff_id` (1 staff có thể có 0 hoặc 1 user account)
- **Audit logging:** Ghi vào bảng `audit_log` cho mọi thao tác
- **Permissions:** Sử dụng hệ thống RBAC với bảng `roles`, `permissions`, `role_permissions`

---

## ⚖️ QUY TẮC NGHIỆP VỤ (BUSINESS RULES)

### Quy tắc bắt buộc khi code:

1. **🔐 Quyền truy cập:**
   - Chỉ user có role `system_admin` (role_id = 4) mới được quản lý nhân viên
   - Các role khác (leader, manager, etc.) không được truy cập

2. **✅ Validation dữ liệu:**
   - `staff_name`: Không được để trống, trim whitespace
   - `email`: Phải đúng định dạng email và unique trong bảng `staff`
   - `phone`: Có thể để trống, nhưng nếu có thì phải đúng định dạng
   - `department` & `position`: Có thể null, hệ thống hiển thị "Chưa Phân Loại"

3. **🚫 Ràng buộc xóa:**
   - **KHÔNG ĐƯỢC** xóa nhân viên đã có tài khoản user
   - Chỉ được xóa nhân viên chưa có user account
   - Phải hiện confirmation dialog trước khi xóa

4. **📊 Trạng thái:**
   - `st_status = 1`: Nhân viên đang hoạt động
   - `st_status = 2`: Nhân viên đã nghỉ việc/không hoạt động

5. **📝 Audit logging:**
   - Mọi thao tác thêm/sửa/xóa đều phải ghi audit log
   - Ghi đầy đủ old_value và new_value
   - Ghi thông tin user thực hiện thao tác

---

## 🛠️ YÊU CẦU KỸ THUẬT

### 1. StaffController (application/controllers/admin/StaffController.php)

```php
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class StaffController extends CI_Controller
{
    // ==================== CORE METHODS ====================

    public function index()          // GET /admin/staff - Danh sách nhân viên
    public function add()            // GET /admin/staff/add - Form thêm
    public function add_process()    // POST /admin/staff/add_process - Xử lý thêm
    public function edit($id)        // GET /admin/staff/edit/{id} - Form sửa
    public function edit_process()   // POST /admin/staff/edit_process - Xử lý sửa
    public function delete($id)      // GET /admin/staff/delete/{id} - Xóa

    // ==================== HELPER METHODS ====================

    private function _hasPermission($permission_name)  // Kiểm tra quyền RBAC
}
```

### 2. StaffManagementModel (application/models/admin/StaffManagementModel.php)

```php
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class StaffManagementModel extends CI_Model
{
    // ==================== READ OPERATIONS ====================

    public function getAllStaff($filters = [])        // Lấy danh sách với bộ lọc
    public function getStaffById($staff_id)           // Lấy chi tiết 1 nhân viên
    public function getDepartments()                  // Lấy danh sách bộ phận unique
    public function getPositions()                    // Lấy danh sách chức vụ unique
    public function getStatistics()                   // Thống kê tổng quan

    // ==================== CRUD OPERATIONS ====================

    public function createStaff($data, $created_by)   // Thêm nhân viên mới
    public function updateStaff($id, $data, $updated_by) // Cập nhật nhân viên
    public function deleteStaff($id, $deleted_by)     // Xóa nhân viên

    // ==================== HELPER METHODS ====================

    private function generateStaffId()                // Tạo ID tự động
    private function logAudit(...)                    // Ghi audit log
}
```

---

## 📋 CHI TIẾT IMPLEMENTATION

### Bước 1: Tạo Controller

**Copy structure từ UserController.php và sửa đổi:**

```php
public function index()
{
    // Check permission: staff.view
    if (!$this->_hasPermission('staff.view')) {
        show_error('Bạn không có quyền xem danh sách nhân viên.', 403);
    }

    // Get filters từ GET params
    $filters = [
        'department' => $this->input->get('department'),
        'position'   => $this->input->get('position'),
        'status'     => $this->input->get('status'),
        'search'     => $this->input->get('search')
    ];

    $data = [
        'staff'       => $this->StaffManagementModel->getAllStaff($filters),
        'departments' => $this->StaffManagementModel->getDepartments(),
        'positions'   => $this->StaffManagementModel->getPositions(),
        'statistics'  => $this->StaffManagementModel->getStatistics(),
        'content'     => 'admin/staff/staff_list',
        'navlink'     => 'staff'
    ];

    $this->load->view('admin/vbackend', $data);
}
```

### Bước 2: Tạo Model

**Copy structure từ UserManagementModel.php và sửa đổi:**

```php
public function createStaff($data, $created_by)
{
    // Generate ID tự động
    $data['id_staff'] = $this->generateStaffId();
    $data['created_at'] = date('Y-m-d H:i:s');
    $data['updated_at'] = date('Y-m-d H:i:s');

    $this->db->insert('staff', $data);
    $staff_id = $this->db->insert_id();

    if ($staff_id) {
        // Log audit
        $this->logAudit($created_by, 'create', 'staff', $staff_id, null, $data);
        return ['success' => true, 'message' => 'Thêm nhân viên thành công.', 'staff_id' => $staff_id];
    } else {
        return ['success' => false, 'message' => 'Có lỗi xảy ra khi thêm nhân viên.'];
    }
}
```

### Bước 3: Tạo Views

#### staff_list.php (Danh sách)
- DataTable responsive với sorting/filtering
- Cột: Tên, Email, Phone, Department, Position, Status, Actions
- Bộ lọc dropdown cho department, position, status
- Thống kê cards: Tổng, Active, Có user, Chưa có user

#### staff_add.php (Form thêm)
- Input fields: staff_name (required), email (required), phone (optional)
- Dropdown department & position (từ data existing + "Chưa Phân Loại")
- Client-side validation
- Auto-generate ID preview

#### staff_edit.php (Form sửa)
- Giống form thêm nhưng có data cũ
- Validation email unique (exclude current record)

### Bước 4: Thêm Permissions

```sql
-- Thêm permissions cho staff management
INSERT INTO permissions (module_id, permission_name, permission_display_name, description) VALUES
(18, 'staff.view', 'Xem danh sách nhân viên', 'Quyền xem danh sách nhân viên'),
(18, 'staff.create', 'Thêm nhân viên', 'Quyền thêm nhân viên mới'),
(18, 'staff.edit', 'Sửa nhân viên', 'Quyền sửa thông tin nhân viên'),
(18, 'staff.delete', 'Xóa nhân viên', 'Quyền xóa nhân viên');

-- Gán permissions cho system_admin (role_id = 4)
INSERT INTO role_permissions (role_id, permission_id)
SELECT 4, permission_id FROM permissions WHERE permission_name LIKE 'staff.%';
```

### Bước 5: Cấu hình Routes

```php
# application/config/routes.php
$route['admin/staff'] = 'admin/StaffController';
$route['admin/staff/add'] = 'admin/StaffController/add';
$route['admin/staff/add_process'] = 'admin/StaffController/add_process';
$route['admin/staff/edit/(:num)'] = 'admin/StaffController/edit/$1';
$route['admin/staff/edit_process'] = 'admin/StaffController/edit_process';
$route['admin/staff/delete/(:num)'] = 'admin/StaffController/delete/$1';
```

---

## 🔗 TÍCH HỢP VỚI HỆ THỐNG HIỆN TẠI

### 1. Tích hợp với UC6 User Management

**Quan trọng: Đảm bảo tương thích hoàn toàn với hệ thống user management**

- **Khi thêm nhân viên:** Nhân viên mới sẽ xuất hiện trong dropdown "Chọn nhân viên" của UC6
- **Khi sửa department/position:** Cần update logic auto-suggest role trong UC6
- **Khi xóa nhân viên:** Phải check không có user account liên kết
- **getStaffWithoutUser():** Method này sẽ tự động reflect changes

### 2. Quy tắc Auto-suggest Role (UC6 integration)

```javascript
// Trong user_add.php của UC6
const positionToRoleMap = {
    'Giám Đốc': '1',     // bod
    'Trưởng Dây Chuyền': '2',   // line_manager
    'Nhân Viên Kho': '3',       // warehouse_staff
    'Nhân Viên QC': '5',        // qc_staff
    'Kỹ Thuật Viên': '6',       // technical_staff
    'Công Nhân': '7',           // worker
    'Administrator': '4',       // system_admin
    'Leader': '2'              // line_manager
};
```

### 3. Data Consistency

- **Real-time sync:** Thay đổi trong staff table sẽ immediate reflect trong UC6
- **Foreign key integrity:** staff_id trong user table phải valid
- **Cascade operations:** Khi xóa staff, phải check user dependencies

---

## 🎨 YÊU CẦU GIAO DIỆN

### Trang danh sách (staff_list.php)
```
┌─────────────────────────────────────────────────────────────┐
│  📊 THỐNG KÊ                                                │
│  ┌─────────┐ ┌─────────┐ ┌─────────┐ ┌─────────┐            │
│  │ Tổng: 14 │ │ Active: │ │ Có user:│ │ Chưa   │            │
│  │ nhân viên│ │ 13      │ │ 13      │ │ có: 1  │            │
│  └─────────┘ └─────────┘ └─────────┘ └─────────┘            │
│                                                             │
│  🔍 BỘ LỌC                                                  │
│  Department: [▼ Chọn bộ phận]  Position: [▼ Chọn chức vụ]   │
│  Status: [▼ Tất cả]  Search: [___________] [Tìm kiếm]       │ 
│                                                             │
│  📋 DANH SÁCH NHÂN VIÊN                                    │
│  ┌─────────────────────────────────────────────────────┐    │
│  │ Tên        │ Email        │ Bộ phận    │ Chức vụ    │    │
│  ├────────────┼──────────────┼────────────┼────────────┤    │
│  │ Nguyễn A   │ a@email.com  │ Sản xuất   │ Công nhân  │    │
│  │ Trần B     │ b@email.com  │ QC         │ NV QC      │    │
│  └────────────┴──────────────┴────────────┴────────────┘    │
│  [Thêm mới] [Sửa] [Xóa]                                     │
└─────────────────────────────────────────────────────────────┘
```

### Form thêm/sửa nhân viên
```
┌─────────────────────────────────────────────────────────────┐
│  ➕ THÊM NHÂN VIÊN MỚI                                       │
│                                                             │
│  Tên nhân viên: [____________________] *                    │
│  Email: [_________________________] *                       │
│  Số điện thoại: [_________________]                         │
│  Bộ phận: [▼ Chọn bộ phận ▼]                                 │
│  Chức vụ: [▼ Chọn chức vụ ▼]                                 │
│  Trạng thái: ○ Active  ○ Inactive                           │
│                                                             │
│  [Lưu lại] [Hủy bỏ]                                         │
└─────────────────────────────────────────────────────────────┘
```

---

## 🧪 TESTING CHECKLIST

### Trước khi bàn giao, bạn PHẢI test đầy đủ:

#### ✅ Chức năng cơ bản
- [ ] Thêm nhân viên mới với đầy đủ thông tin
- [ ] Sửa thông tin nhân viên
- [ ] Xóa nhân viên chưa có user account
- [ ] Không thể xóa nhân viên đã có user account

#### ✅ Validation
- [ ] Required fields (staff_name, email)
- [ ] Email format validation
- [ ] Email unique check
- [ ] Phone format validation (nếu có)

#### ✅ Filters & Search
- [ ] Filter theo department
- [ ] Filter theo position
- [ ] Filter theo status
- [ ] Search text trong tên/email/phone

#### ✅ Permissions
- [ ] Chỉ admin mới truy cập được
- [ ] Non-admin bị redirect với error message
- [ ] RBAC permissions hoạt động đúng

#### ✅ Audit Logging
- [ ] Create operations được log
- [ ] Update operations ghi old/new values
- [ ] Delete operations được log
- [ ] Log hiển thị đúng user thực hiện

#### ✅ Tích hợp UC6
- [ ] Nhân viên mới xuất hiện trong user creation dropdown
- [ ] Department/position changes reflect trong role suggestion
- [ ] getStaffWithoutUser() cập nhật real-time

#### ✅ UI/UX
- [ ] Responsive design trên mobile/desktop
- [ ] Loading states khi submit form
- [ ] Success/error messages với SweetAlert
- [ ] DataTable sorting và pagination
- [ ] Confirm dialogs cho delete operations

---

## ⚠️ LƯU Ý QUAN TRỌNG

### 1. **Đừng động đến code UC6 hiện tại**
- Tạo StaffController riêng biệt
- Không sửa đổi UserController hoặc UserManagementModel
- Chỉ tạo integration points cần thiết

### 2. **Audit logging phải giống hệt UC6**
```php
private function logAudit($user_id, $action, $module, $record_id, $old_value, $new_value)
{
    // Ghi vào bảng audit_log giống UC6
    // user_id, username, action, module, record_id, old_value, new_value, ip, user_agent
}
```

### 3. **Error handling toàn diện**
- Database errors → User-friendly messages
- Validation errors → Clear feedback
- Permission errors → Proper redirects
- Network errors → Graceful degradation

### 4. **Data integrity**
- Unique constraints trên email
- Foreign key relationships
- Transaction handling cho complex operations
- Rollback on failures

### 5. **Performance considerations**
- Database indexes cho search fields
- Pagination cho large datasets
- Efficient queries với JOINs
- Caching cho dropdown data

### 6. **Security requirements**
- Input sanitization (XSS protection)
- SQL injection prevention (Active Record)
- CSRF protection
- Session validation

---

## 📦 BÀN GIAO DELIVERABLES

### Source Code
- [ ] `application/controllers/admin/StaffController.php`
- [ ] `application/models/admin/StaffManagementModel.php`
- [ ] `application/views/admin/staff/staff_list.php`
- [ ] `application/views/admin/staff/staff_add.php`
- [ ] `application/views/admin/staff/staff_edit.php`

### Database Scripts
- [ ] SQL để add permissions
- [ ] Routes configuration
- [ ] Migration scripts (nếu cần)

### Documentation
- [ ] Testing results với screenshots
- [ ] API documentation
- [ ] Business rules documentation
- [ ] Integration guide với UC6

### Testing Evidence
- [ ] Unit tests cho model methods
- [ ] Integration tests với UC6
- [ ] UI testing screenshots
- [ ] Performance testing results

---

## 🚀 TRIỂN KHAI CHECKLIST

- [ ] Code review completed
- [ ] Unit tests passed
- [ ] Integration tests với UC6 passed
- [ ] UI/UX testing completed
- [ ] Performance testing passed
- [ ] Security audit passed
- [ ] Documentation completed
- [ ] User acceptance testing ready

---

**Tóm lại:** Bạn cần tạo một hệ thống CRUD hoàn chỉnh cho quản lý nhân viên, theo đúng pattern của UC6, với đầy đủ validation, permissions, audit logging, và tích hợp chặt chẽ với hệ thống user management hiện tại.

**Thời hạn hoàn thành:** [Điền thời hạn]
**Người liên hệ:** [Tên và contact]

---

*Document version: 1.0*
*Last updated: December 12, 2025*
*Created by: System Analyst*</content>
<parameter name="filePath">d:\PHAT TRIEN UNG DUNG\production-management-v2\STAFF_MANAGEMENT_GUIDE.md