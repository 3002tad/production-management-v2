# Production Management System v2
## Hệ thống Quản lý Sản xuất Bút bi


Hệ thống quản lý sản xuất chuyên biệt cho ngành sản xuất bút bi, được phát triển bằng CodeIgniter 3 với giao diện Material Design. Hỗ trợ quản lý toàn bộ quy trình từ tiếp nhận đơn hàng, lập kế hoạch, sản xuất, kiểm chất, đến xuất kho.

## 📋 Mục lục

- [Tính năng](#tính-năng)
- [Yêu cầu hệ thống](#yêu-cầu-hệ-thống)
- [Cài đặt](#cài-đặt)
- [Phân quyền & Vai trò](#phân-quyền--vai-trò)
- [Các Module & Tính năng](#các-module--tính-năng)
- [Cấu trúc Database](#cấu-trúc-database)
- [Hướng dẫn sử dụng](#hướng-dẫn-sử-dụng)

## ✨ Tính năng

### 📊 Quản lý Master Data
- 👥 **Quản lý Khách hàng** - Thông tin khách hàng, lịch sử đơn hàng
- 🖊️ **Quản lý Sản phẩm** - Danh mục bút bi với thông số kỹ thuật (đường kính, màu mực, tự động điền đường kính)
- 🏭 **Quản lý Máy móc** - Theo dõi máy móc, công suất, trạng thái hoạt động
- 📦 **Quản lý Nguyên liệu** - Tồn kho nguyên liệu, tracking theo gram
- 👨‍💼 **Quản lý Nhân viên** - Thông tin nhân viên, vị trí, bộ phận, trạng thái (Full CRUD cho Admin)
- ⏰ **Quản lý Ca làm việc** - Phân ca sản xuất, gán nhân viên, máy móc, nguyên liệu

### 🏭 Quản lý Sản xuất
- 📊 **Dashboard Sản xuất** - Tổng quan tiến độ, báo cáo hoàn thành (Trưởng dây chuyền, Ban Giám Đốc)
- 📋 **Quản lý Đơn hàng** - Tạo, cập nhật, xem chi tiết đơn hàng từ khách hàng
- 📅 **Lập kế hoạch** - Phân bổ kế hoạch sản xuất theo ca, mục tiêu số lượng
- 🔧 **Sản xuất** - Ghi nhận sản lượng, máy móc, nhân viên, nguyên liệu sử dụng
- ✅ **Phân loại sản phẩm** - Thống kê sản phẩm đạt/lỗi, tính tỷ lệ waste
- 📦 **Quản lý Kho** - Báo cáo nhập/xuất kho thành phẩm, quản lý tồn kho
- 🔍 **QC & Kiểm chất** - Tạo session kiểm chất, ghi nhận kết quả, duyệt quyết định

### 🎯 Dashboard & Báo cáo
- **Dashboard Trưởng dây chuyền** - Công suất máy đang chạy, báo cáo hoàn thành, sắp xếp sản phẩm
- **Dashboard Ban Giám Đốc** - Tổng quan toàn bộ sản xuất, quản lý sản phẩm, khách hàng, duyệt kế hoạch
- **Dashboard QC** - Danh sách ca chờ kiểm chất, session kiểm tra, báo cáo điều chỉnh
- **Dashboard Kho** - Tồn kho, báo cáo nhập/xuất, yêu cầu điều chỉnh

### 🔐 Tính năng Đặc biệt
- 🎯 **Auto-fill Diameter** - Tự động điền đường kính bi từ sản phẩm khi tạo đơn hàng
- 🔗 **Database Relationships** - 12 Foreign Keys đảm bảo tính toàn vẹn dữ liệu
- 🌐 **Đa ngôn ngữ** - Hỗ trợ tiếng Việt đầy đủ (277+ translation keys)
- 📱 **Responsive Design** - Material Dashboard 2, UI đồng bộ trên tất cả module
- 🔐 **Phân quyền RBAC** - Phân quyền chi tiết theo vai trò (BOD, Leader, QC, Warehouse, etc.)
- 📄 **In ấn & Export** - Xuất PDF báo cáo sản xuất, đơn hàng
- 📳 **Thông báo** - Toast notification cho login/logout, flashdata messages
- 🎨 **Giao diện hiện đại** - Metric cards, gradient icons, consistent styling

## 💻 Yêu cầu hệ thống

- **PHP** >= 7.2 (khuyến nghị PHP 8.0+)
- **MySQL/MariaDB** >= 5.7 / 10.4+
- **Apache/Nginx** với mod_rewrite hoặc PHP built-in server
- **Composer** (optional, cho dependencies)

### PHP Extensions
- `mysqli` - Database connectivity
- `mbstring` - Multi-byte string support (UTF-8)
- `intl` - Internationalization
- `json` - JSON processing

## 🚀 Cài đặt

### 1. Clone Repository

```bash
git clone https://github.com/3002tad/production-management-v2.git
cd production-management-v2
```

### 2. Cấu hình Database

Tạo database mới:

```sql
CREATE DATABASE db_production CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Import database schema:

```bash
mysql -u root -p db_production < db_production.sql
```

### 3. Cấu hình CodeIgniter

Chỉnh sửa `application/config/database.php`:

```php
$db['default'] = array(
    'dsn'      => '',
    'hostname' => 'localhost',
    'username' => 'root',
    'password' => 'your_password',
    'database' => 'db_production',
    'dbdriver' => 'mysqli',
    'char_set' => 'utf8mb4',
    'dbcollat' => 'utf8mb4_unicode_ci',
);
```

Cấu hình base URL trong `application/config/config.php`:

```php
$config['base_url'] = 'http://localhost:8000/';
```

### 4. Chạy ứng dụng

**Sử dụng PHP Built-in Server:**

```bash
php -S localhost:8000
# Truy cập: http://localhost:8000/
```

**Hoặc sử dụng XAMPP/WAMP:**

- Copy folder vào `htdocs/`
- Truy cập: `http://localhost/production-management-v2/`

### 5. Đăng nhập

Tài khoản mặc định:

| Vai trò | Username | Password | Ghi chú |
|---------|----------|----------|---------|
| **BOD** | `bod` | `bod` | Ban Giám Đốc |
| **Leader** | `leader` | `leader` | Trưởng dây chuyền |
| **QC Staff** | `qc` | `qc` | Nhân viên QC |
| **Warehouse** | `warehouse` | `warehouse` | Nhân viên kho |
| **Admin** | `admin` | `admin` | System Admin |

⚠️ **LƯU Ý:** Đổi mật khẩu mặc định sau khi cài đặt!

## 🔐 Phân quyền & Vai trò

Hệ thống sử dụng Role-Based Access Control (RBAC) với các vai trò sau:

### BOD (Ban Giám Đốc) - Level 100
- 📊 Dashboard tổng quan sản xuất
- ✏️ Tạo/chỉnh sửa/xóa đơn hàng
- ✏️ Quản lý sản phẩm, khách hàng
- ✅ Duyệt kế hoạch sản xuất
- 📈 Xem báo cáo tổng hợp
- 🔧 Quản lý nhân viên, máy móc

**Controller:** `application/controllers/BOD.php`
**Views:** `application/views/bod/`

### Leader (Trưởng dây chuyền) - Level 50
- 📊 Dashboard sản xuất theo dây chuyền (công suất máy, báo cáo hoàn thành)
- 📋 Lập kế hoạch sản xuất
- 🔍 Xem đơn hàng (read-only)
- 👥 Quản lý nhân viên dây chuyền (view-only)
- 📊 Xem báo cáo phân loại, finished inventory
- 🎯 Theo dõi incident (sự cố)

**Controller:** `application/controllers/leader/Leader.php`
**Views:** `application/views/leader/`

### QC Staff (Nhân viên QC) - Level 60
- ✅ Xem danh sách ca chờ kiểm chất
- 🔍 Tạo session kiểm chất chi tiết
- 📝 Ghi nhận kết quả kiểm tra
- 📸 Upload tài liệu đính kèm
- ✅ Duyệt kết quả (Approve/Reject)
- 📊 Xem báo cáo yêu cầu điều chỉnh

**Controller:** `application/controllers/Qc.php`
**Views:** `application/views/qc/`

### Warehouse (Nhân viên kho) - Level 40
- 📦 Dashboard tồn kho
- 📥 Báo cáo nhập kho thành phẩm (Finished Receipt)
- 📤 Báo cáo xuất kho (Finished Issue/Delivery)
- 🔍 Xem chi tiết phiếu nhập/xuất
- 📊 Báo cáo tồn kho hàng ngày

**Controller:** `application/controllers/Warehouse.php`
**Views:** `application/views/warehouse/`

### System Admin - Level 90
- 🔧 Quản lý toàn hệ thống
- 👥 Quản lý người dùng, vai trò
- 🏭 Quản lý máy móc, nguyên liệu, ca làm việc
- 📝 Quản lý sản phẩm, khách hàng
- 📊 Xem tất cả báo cáo
- ⚙️ Cấu hình hệ thống

**Controller:** `application/controllers/Admin.php`
**Views:** `application/views/admin/`

## 🗄️ Cấu trúc Database

### Các bảng chính

| Bảng | Mô tả | Ghi chú |
|------|-------|---------|
| `user` | Tài khoản đăng nhập | Liên kết với staff |
| `role` | Vai trò người dùng | BOD, Leader, QC, Warehouse, etc. |
| `customer` | Khách hàng | Foreign Key từ project |
| `product` | Sản phẩm bút bi | Lưu diameter, màu mực |
| `project` | Dự án/Đơn hàng | Liên kết customer + product |
| `planning` | Kế hoạch sản xuất | Phân bổ dự án theo ca |
| `plan_shift` | Chi tiết ca sản xuất | Gán nhân viên, máy móc, nguyên liệu |
| `machine` | Máy móc sản xuất | Theo dõi trạng thái, công suất |
| `material` | Nguyên liệu | Tồn kho, tracking |
| `staff` | Nhân viên | Bộ phận, vị trí, trạng thái |
| `shiftment` | Ca làm việc | Morning, Afternoon, Night, etc. |
| `production_records` | Ghi nhận sản xuất | Chi tiết sản lượng, máy, nhân viên |
| `production_shifts` | Ca sản xuất hoạt động | Trạng thái: 1=Mở, 2=Đang chạy, 3=Đóng |
| `sorting_report` | Báo cáo phân loại | Sản phẩm đạt/lỗi, tỷ lệ waste |
| `finished_report` | Báo cáo thành phẩm hoàn thành | Số lượng hoàn thành, ngày |
| `qc_sessions` | Session kiểm chất | Ghi nhận kết quả QC chi tiết |
| `qc_checklist_items` | Chi tiết kiểm tra QC | Các item kiểm tra từ checklist |
| `qc_attachments` | Tài liệu đính kèm QC | Hình ảnh, video, tài liệu |
| `w_receipts` | Phiếu nhập kho | Thành phẩm nhập vào kho |
| `w_issues` | Phiếu xuất kho | Thành phẩm xuất khỏi kho |

### Foreign Keys

Hệ thống sử dụng 12+ Foreign Key relationships để đảm bảo tính toàn vẹn dữ liệu:
- **ON DELETE RESTRICT**: Master data (customer, product, machine, material, staff)
- **ON DELETE CASCADE**: Transaction data (planning, reports, production records)

## 📖 Hướng dẫn sử dụng

### Quy trình làm việc cơ bản

#### 1️⃣ Ban Giám Đốc (BOD) tạo Đơn hàng
- Đăng nhập với tài khoản BOD
- Chọn **Đơn hàng** → **Thêm đơn hàng mới**
- Chọn khách hàng, sản phẩm (đường kính tự động điền)
- Nhập số lượng yêu cầu, ngày dự kiến
- Lưu đơn hàng

#### 2️⃣ Trưởng dây chuyền (Leader) lập Kế hoạch
- Đăng nhập với tài khoản Leader
- Chọn **Kế hoạch** → **Tạo kế hoạch**
- Chọn đơn hàng từ danh sách
- Đặt mục tiêu sản xuất hàng ngày
- Phân bổ theo các ca làm việc (Morning, Afternoon, Night)

#### 3️⃣ Gán Nhân viên & Máy móc
- Từ kế hoạch, chọn **Cập nhật chi tiết ca**
- Gán nhân viên, máy móc cho mỗi ca
- Chọn nguyên liệu cần sử dụng
- Lưu

#### 4️⃣ Ghi nhận Sản xuất
- Ca làm việc bắt đầu, nhân viên ghi nhận sản lượng
- Chọn **Sản xuất** → **Ghi nhận**
- Nhập số lượng hoàn thành, số lượng lỗi
- Ghi nhận máy móc, nguyên liệu sử dụng

#### 5️⃣ Kiểm chất (QC)
- Nhân viên QC xem danh sách ca chờ kiểm chất
- Tạo session kiểm chất mới
- Ghi nhận kết quả kiểm tra chi tiết (từng item)
- Upload tài liệu đính kèm nếu cần
- Duyệt kết quả: Approve hoặc Reject

#### 6️⃣ Báo cáo Hoàn thành
- Nhập kho thành phẩm sau kiểm chất
- Chọn **Kho** → **Báo cáo nhập**
- Chọn phiếu kiểm chất đã duyệt
- Xác nhận tồn kho

#### 7️⃣ Phân loại & Báo cáo
- Xem báo cáo phân loại sản phẩm đạt/lỗi
- Xem báo cáo tồn kho hàng ngày
- Dashboard tổng quan công suất máy, sản lượng hoàn thành

### Các tính năng chính theo vai trò

#### Dashboard Trưởng dây chuyền
- **Công suất máy đang chạy** - Top 5 máy sản xuất nhiều nhất trong ca
- **Báo cáo hoàn thành** - 10 đơn hàng hoàn thành gần đây
- **Báo cáo phân loại** - Sản phẩm đạt/lỗi theo dây chuyền
- **Sự cố & Incidents** - Danh sách sự cố cần xử lý

#### Admin - Quản lý Nhân viên
- ✏️ **Thêm nhân viên** - Điền thông tin, bộ phận, vị trí, trạng thái
- ✏️ **Chỉnh sửa nhân viên** - Cập nhật thông tin, bộ phận, vị trí
- ❌ **Xóa nhân viên** - Xóa nhân viên khỏi hệ thống
- 🔍 **Bộ lọc** - Tìm kiếm theo tên, bộ phận, trạng thái
- 📊 **Thống kê** - Tổng số, đang làm, có account, không có account

#### Xem đơn hàng (Trưởng dây chuyền)
- 📋 **Danh sách đơn hàng** - Tất cả đơn hàng với bộ lọc
- 🔍 **Chi tiết đơn hàng** - Khách hàng, sản phẩm, số lượng, kế hoạch liên quan
- 📥 **Read-only** - Xem thông tin, không thể chỉnh sửa

### Giao diện & Trải nghiệm

- **Material Design** - Giao diện hiện đại, responsive
- **Metric Cards** - Hiển thị thống kê nhanh gọn
- **Toast Notifications** - Thông báo login/logout thành công
- **Logout Confirmation** - Xác nhận khi đăng xuất
- **Consistent Styling** - Tất cả module sử dụng style đồng bộ
- **Filter & Search** - Tìm kiếm nhanh trong các danh sách

## 📁 Cấu trúc thư mục

```
production-management-v2/
├── application/                # CodeIgniter application
│   ├── config/                 # Cấu hình
│   │   ├── config.php          # Base config
│   │   ├── database.php        # Database config
│   │   ├── routes.php          # URL routing
│   │   └── ...
│   ├── controllers/            # Controllers
│   │   ├── Admin.php           # Admin/System controller
│   │   ├── BOD.php             # BOD (Ban Giám Đốc)
│   │   ├── Qc.php              # QC (Kiểm chất)
│   │   ├── Warehouse.php       # Warehouse (Kho)
│   │   ├── Login.php           # Authentication
│   │   ├── leader/
│   │   │   └── Leader.php      # Leader dashboard & features
│   │   └── ...
│   ├── models/                 # Database models
│   │   ├── CrudModel.php       # Generic CRUD
│   │   ├── OrderModel.php      # Order management
│   │   ├── QcModel.php         # QC data access
│   │   ├── ProductionSimulatorModel.php
│   │   └── ...
│   ├── views/                  # Views/Templates
│   │   ├── admin/              # Admin dashboard views
│   │   │   ├── VBackend.php    # Admin layout
│   │   │   ├── Beranda.php     # Admin dashboard
│   │   │   ├── staff/          # Staff management
│   │   │   ├── project/        # Project management
│   │   │   ├── product/        # Product management
│   │   │   ├── customer/       # Customer management
│   │   │   ├── planning/       # Planning views
│   │   │   ├── production/     # Production tracking
│   │   │   ├── finished/       # Finished inventory
│   │   │   └── ...
│   │   ├── leader/             # Leader dashboard views
│   │   │   ├── VBackend.php    # Leader layout
│   │   │   ├── Beranda.php     # Dashboard (máy móc, báo cáo)
│   │   │   ├── staff/          # View-only staff list
│   │   │   ├── order/          # Read-only orders
│   │   │   ├── planning/       # Planning management
│   │   │   ├── production/     # Production views
│   │   │   ├── finished/       # Finished inventory
│   │   │   └── ...
│   │   ├── bod/                # BOD dashboard views
│   │   │   ├── vbackend.php    # BOD layout
│   │   │   ├── beranda.php     # BOD dashboard
│   │   │   ├── project/        # Order management
│   │   │   ├── customer/       # Customer management
│   │   │   ├── product/        # Product management
│   │   │   └── ...
│   │   ├── qc/                 # QC module views
│   │   │   ├── vbackend.php    # QC layout
│   │   │   ├── pending.php     # Pending closures
│   │   │   ├── session.php     # Inspection session
│   │   │   └── ...
│   │   ├── warehouse/          # Warehouse views
│   │   │   ├── vbackend.php    # Warehouse layout
│   │   │   ├── finished/       # Finished inventory
│   │   │   ├── receipts/       # Receipt management
│   │   │   └── ...
│   │   ├── Login.php           # Login page
│   │   └── ...
│   ├── libraries/              # Custom libraries
│   │   ├── ChecklistService.php # QC checklist logic
│   │   └── ...
│   ├── modules/                # CodeIgniter modules
│   │   └── staff/              # Staff module (reusable)
│   ├── language/               # Language files
│   │   ├── vietnamese/         # Vietnamese translations (277+ keys)
│   │   └── english/
│   └── ...
├── asset/                      # Frontend assets
│   ├── Backend/                # Material Dashboard template
│   │   ├── assets/             # CSS, JS, images
│   │   └── json/               # JSON data files
│   └── Login&Register/         # Login page styling
├── db/                         # Database files
│   └── db_production.sql       # Main schema
├── system/                     # CodeIgniter system files
├── uploads/                    # User uploads
│   ├── qc/                     # QC attachments
│   └── materials/              # Material uploads
├── index.php                   # Entry point
├── composer.json               # Dependencies
├── README.md                   # This file
└── .gitignore
```

## 🛠️ Các Use Cases được triển khai

### ✅ Hoàn thành

-  Tiếp nhận đơn hàng bút bi (Create order - BOD)
-  Lập kế hoạch sản xuất (Create planning - Leader)
-  Quản lý nhân sự (Staff management - Admin/Leader)
-  Ghi nhận sản xuất (Record production)
-  Phân loại sản phẩm (Sorting report)
-  Kiểm chất QC (Quality Control with checklist)
-  Quản lý nhân viên kho (Warehouse management)
-  Xuất lệnh sản xuất (Production plan export)
-  Quản lý tồn kho, báo cáo incident, dashboard, authentication

### 📊 Controller/View Mapping

| Controller | Views | Tính năng chính |
|------------|-------|-----------------|
| **Admin.php** | admin/ | CRUD master data, staff, machines, materials |
| **BOD.php** | bod/ | Tạo/quản lý đơn hàng, duyệt kế hoạch |
| **Leader.php** | leader/ | Dashboard, lập kế hoạch, xem đơn hàng, nhân viên |
| **Qc.php** | qc/ | Kiểm chất, session inspection, uploads |
| **Warehouse.php** | warehouse/ | Nhập/xuất kho, tồn kho |
| **Simulator.php** | leader/simulator/ | Mô phỏng sản xuất (tính toán) |
| **Login.php** | Login.php | Authentication, role-based redirect |


## 📝 Ghi chú phát triển

### Gần đây đã cập nhật

- ✅ **Leader Dashboard** - Thêm công suất máy đang chạy (Top 5 by production), cập nhật title thành "Trưởng dây chuyền"
- ✅ **Admin Staff CRUD** - Thêm tính năng add/edit/delete nhân viên với UI hiện đại (action buttons)
- ✅ **Staff UI Styling** - Cập nhật giao diện staff module với metric cards, section header, filters
- ✅ **Leader Staff** - Enforce read-only với "View Only" badge
- ✅ **Leader Orders** - Thêm read-only orders list và detail views
- ✅ **Sidebar Menu** - Sắp xếp menu, đặt "Đơn hàng" trên "Kế hoạch"
- ✅ **Login Notifications** - Thêm toast success notification cho tất cả vai trò
- ✅ **Logout Confirmation** - Thêm confirm dialog khi đăng xuất
- ✅ **Toast System** - Fix repeating toast bằng sessionStorage + URL param cleanup
- ✅ **Query Fixes** - Sửa undefined property errors với null coalescing


## Công Nghệ sử dụng

- [CodeIgniter](https://codeigniter.com/) - PHP Web Application Framework
- [Material Dashboard 2](https://www.creative-tim.com/product/material-dashboard) - Professional Admin Template
- [Bootstrap 4/5](https://getbootstrap.com/) - CSS Framework
- [Font Awesome](https://fontawesome.com/) - Icon Library
- [Chart.js](https://www.chartjs.org/) - Charting Library

## 🚀 Mục tiêu dự án

Hệ thống này được phát triển với mục tiêu:
- ✅ Tự động hóa quy trình sản xuất bút bi
- ✅ Theo dõi tiến độ sản xuất real-time
- ✅ Giảm lỗi thủ công, tăng hiệu suất
- ✅ Quản lý tồn kho chính xác
- ✅ Đảm bảo chất lượng sản phẩm
- ✅ Tối ưu hóa chi phí sản xuất
- ✅ Cung cấp báo cáo chi tiết cho quản lý



**Made with ❤️ by Production Management Development Team**

*Cập nhật lần cuối: Tháng 12, 2025*