# Production Management System v2
## Hệ thống Quản lý Sản xuất Bút bi

[![PHP Version](https://img.shields.io/badge/PHP-%3E%3D7.2-blue.svg)](https://php.net)
[![CodeIgniter](https://img.shields.io/badge/CodeIgniter-3.x-orange.svg)](https://codeigniter.com)
[![MySQL](https://img.shields.io/badge/MySQL-5.7+-blue.svg)](https://mysql.com)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)

Hệ thống quản lý sản xuất chuyên biệt cho ngành sản xuất bút bi, được phát triển bằng CodeIgniter 3 với giao diện Material Design.

## 📋 Mục lục

- [Tính năng](#tính-năng)
- [Yêu cầu hệ thống](#yêu-cầu-hệ-thống)
- [Cài đặt](#cài-đặt)
- [Cấu trúc Database](#cấu-trúc-database)
- [Hướng dẫn sử dụng](#hướng-dẫn-sử-dụng)
- [Đóng góp](#đóng-góp)
- [License](#license)

## ✨ Tính năng

### Quản lý Master Data
- 👥 **Quản lý Khách hàng** - Thông tin khách hàng, lịch sử đơn hàng
- 🖊️ **Quản lý Sản phẩm** - Danh mục bút bi với thông số kỹ thuật (đường kính, màu mực)
- 🏭 **Quản lý Máy móc** - Theo dõi máy móc, công suất, trạng thái
- 📦 **Quản lý Nguyên liệu** - Tồn kho nguyên liệu (gram)
- 👨‍💼 **Quản lý Nhân viên** - Thông tin nhân viên, ca làm việc
- ⏰ **Quản lý Ca làm việc** - Phân ca sản xuất

### Quản lý Sản xuất
- 📊 **Quản lý Dự án** - Đơn hàng từ khách hàng
- 📅 **Lập kế hoạch Sản xuất** - Phân bổ kế hoạch theo ca
- 🔧 **Quản lý Sản xuất** - Theo dõi tiến độ sản xuất
- ✅ **Phân loại Sản phẩm** - Thống kê sản phẩm đạt/lỗi
- 📦 **Quản lý Kho** - Báo cáo nhập kho thành phẩm

### Tính năng Đặc biệt
- 🎯 **Auto-fill Diameter** - Tự động điền đường kính bi từ sản phẩm
- 🔗 **Database Relationships** - 12 Foreign Keys đảm bảo tính toàn vẹn dữ liệu
- 🌐 **Đa ngôn ngữ** - Hỗ trợ tiếng Việt đầy đủ (277+ translation keys)
- 📱 **Responsive Design** - Material Dashboard 2
- 🔐 **Phân quyền** - Admin & Leader roles
- 📄 **In ấn** - Export PDF reports

## 💻 Yêu cầu hệ thống

- **PHP** >= 7.2 (khuyến nghị PHP 8.0+)
- **MySQL/MariaDB** >= 5.7 / 10.4+
- **Apache/Nginx** với mod_rewrite
- **Composer** (tùy chọn, cho dependencies)

### PHP Extensions
- `mysqli` - Database connectivity
- `mbstring` - Multi-byte string support
- `intl` - Internationalization
- `json` - JSON processing
- `gd` hoặc `imagick` - Image processing (tùy chọn)

## 🚀 Cài đặt

### 1. Clone Repository

```bash
git clone https://github.com/[your-username]/production-management-v2.git
cd production-management-v2
```

### 2. Cấu hình Database

Tạo database mới:

```sql
CREATE DATABASE db_production CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Import database schema:

```bash
mysql -u root -p db_production < db/db_production.sql
```

### 3. Chạy Migrations

**Bước 1:** Fix charset encoding (nếu cần)
```bash
mysql -u root -p db_production < db/fix_vietnamese_charset.sql
```

**Bước 2:** Convert diameter sang DECIMAL (nếu cần)
```bash
mysql -u root -p db_production < db/migration_optional_diameter_decimal.sql
```

**Bước 3:** Thêm cột diameter vào product
```bash
mysql -u root -p db_production < db/migration_add_diameter_to_product.sql
```

**Bước 4:** Tạo Foreign Key relationships
```bash
mysql -u root -p db_production < db/add_foreign_keys.sql
```

### 4. Cấu hình CodeIgniter

Sao chép file config:

```bash
cp application/config/database.php.example application/config/database.php
```

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
    // ... other settings
);
```

Cấu hình base URL trong `application/config/config.php`:

```php
$config['base_url'] = 'http://localhost:8000/production-management-v2/';
```

### 5. Chạy ứng dụng

**Sử dụng PHP Built-in Server:**

```bash
php -S localhost:8000
```

**Hoặc sử dụng XAMPP/WAMP:**

- Copy folder vào `htdocs/`
- Truy cập: `http://localhost/production-management-v2/`

### 6. Đăng nhập

**Admin:**
- Username: `admin`
- Password: `admin`

**Leader:**
- Username: `leader`
- Password: `leader`

⚠️ **LƯU Ý:** Đổi mật khẩu mặc định sau khi cài đặt!

## 🗄️ Cấu trúc Database

### ERD Diagram

Xem chi tiết trong [db/DATABASE_RELATIONSHIPS.md](db/DATABASE_RELATIONSHIPS.md)

### Các bảng chính

| Bảng | Mô tả |
|------|-------|
| `customer` | Khách hàng |
| `product` | Sản phẩm bút bi |
| `project` | Dự án/Đơn hàng |
| `planning` | Kế hoạch sản xuất |
| `plan_shift` | Chi tiết ca sản xuất |
| `machine` | Máy móc |
| `material` | Nguyên liệu |
| `staff` | Nhân viên |
| `shiftment` | Ca làm việc |
| `p_machine` | Máy móc sử dụng trong ca |
| `p_material` | Nguyên liệu sử dụng trong ca |
| `sorting_report` | Báo cáo phân loại |
| `finished_report` | Báo cáo thành phẩm |
| `user` | Tài khoản đăng nhập |

### Foreign Keys

12 Foreign Key relationships đảm bảo tính toàn vẹn dữ liệu:
- ON DELETE RESTRICT: Master data (customer, product, machine, material, staff, shiftment)
- ON DELETE CASCADE: Transaction data (planning, reports)

## 📖 Hướng dẫn sử dụng

### Quy trình làm việc cơ bản

1. **Tạo Sản phẩm** (Product)
   - Thêm thông tin bút bi: tên, mô tả, màu mực, đường kính

2. **Tạo Dự án** (Project)
   - Chọn khách hàng
   - Chọn sản phẩm → Đường kính tự động điền
   - Nhập số lượng yêu cầu

3. **Lập Kế hoạch** (Planning)
   - Chọn dự án
   - Đặt mục tiêu sản xuất
   - Phân bổ theo ca làm việc

4. **Sản xuất** (Production)
   - Gán nhân viên, máy móc, nguyên liệu
   - Theo dõi tiến độ

5. **Phân loại** (Sorting)
   - Báo cáo số lượng đạt/lỗi
   - Tính toán tỷ lệ waste

6. **Nhập kho** (Finished)
   - Báo cáo thành phẩm hoàn thành
   - Cập nhật tồn kho

## 📁 Cấu trúc thư mục

```
production-management-v2/
├── application/           # CodeIgniter application
│   ├── controllers/       # Controllers
│   ├── models/            # Models
│   ├── views/             # Views
│   │   ├── admin/         # Admin views
│   │   └── leader/        # Leader views
│   ├── language/          # Language files
│   │   └── vietnamese/    # Vietnamese translations
│   └── config/            # Configuration files
├── asset/                 # Frontend assets
│   ├── Backend/           # Material Dashboard
│   └── Login&Register/    # Login page assets
├── db/                    # Database files
│   ├── db_production.sql  # Main schema
│   ├── add_foreign_keys.sql
│   └── *.md               # Documentation
├── system/                # CodeIgniter system files
├── .gitignore
├── README.md
└── index.php              # Entry point
```

## 🤝 Đóng góp

Chúng tôi hoan nghênh mọi đóng góp! Vui lòng làm theo các bước sau:

### 1. Fork repository

```bash
git clone https://github.com/[your-username]/production-management-v2.git
cd production-management-v2
git checkout -b feature/ten-tinh-nang
```

### 2. Commit changes

```bash
git add .
git commit -m "feat: thêm tính năng XYZ"
```

### 3. Push và tạo Pull Request

```bash
git push origin feature/ten-tinh-nang
```

### Coding Standards

- Tuân thủ [CodeIgniter Style Guide](https://codeigniter.com/userguide3/general/styleguide.html)
- Comment code bằng tiếng Việt hoặc tiếng Anh
- Test kỹ trước khi commit

### Commit Message Convention

```
feat: Thêm tính năng mới
fix: Sửa lỗi
docs: Cập nhật documentation
style: Format code
refactor: Tái cấu trúc code
test: Thêm test cases
chore: Maintenance tasks
```

## 📝 Changelog

Xem chi tiết trong [CHANGELOG.md](CHANGELOG.md)

### Version 2.0.0 (2025-10-27)

**Added:**
- ✨ Localization tiếng Việt đầy đủ (277+ keys)
- ✨ Auto-fill diameter từ product sang project
- ✨ Database foreign key relationships (12 FKs)
- ✨ Migration scripts với rollback support

**Changed:**
- 🔄 Đổi diameter từ INT sang DECIMAL(3,1)
- 🔄 Đơn vị: Kg → pieces/gram
- 🔄 Application field → Ink Color

**Fixed:**
- 🐛 Vietnamese charset encoding issues
- 🐛 Undefined property errors
- 🐛 Missing shiftment JOIN in queries

## 🐛 Báo lỗi

Nếu phát hiện lỗi, vui lòng tạo [Issue](https://github.com/[your-username]/production-management-v2/issues) với thông tin:

- Mô tả lỗi
- Các bước tái hiện
- Screenshots (nếu có)
- Environment info (PHP version, MySQL version, OS)

## 📄 License

Dự án này được phân phối dưới giấy phép MIT. Xem file [LICENSE](LICENSE) để biết thêm chi tiết.

## 👥 Team

- **Developer 1** - [GitHub](https://github.com/dev1)
- **Developer 2** - [GitHub](https://github.com/dev2)
- **Developer 3** - [GitHub](https://github.com/dev3)

## 🙏 Acknowledgments

- [CodeIgniter](https://codeigniter.com/) - PHP Framework
- [Material Dashboard 2](https://www.creative-tim.com/product/material-dashboard) - Admin Template
- [Bootstrap](https://getbootstrap.com/) - CSS Framework

## 📞 Liên hệ

- Email: support@example.com
- Website: https://example.com

---

Made with ❤️ by Production Management Team
