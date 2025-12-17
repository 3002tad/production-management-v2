# Changelog

Tất cả các thay đổi quan trọng của dự án sẽ được ghi lại trong file này.

## [2.0.0] - 2025-10-27

### Added ✨

#### Localization System
- Hệ thống đa ngôn ngữ hoàn chỉnh với 277+ translation keys
- File language tiếng Việt: `translation_lang.php`
- CodeIgniter language files:
  - `form_validation_lang.php` - Validation messages
  - `db_lang.php` - Database error messages
  - `upload_lang.php` - Upload error messages
  - `pagination_lang.php` - Pagination labels
  - `migration_lang.php` - Migration messages
  - `number_lang.php` - Number formatting

#### Product & Project Enhancement
- Cột `diameter` (DECIMAL 3,1) trong bảng `product`
- Auto-fill diameter khi tạo/cập nhật project
- Visual feedback (highlight) khi auto-fill thành công

#### Database Improvements
- 12 Foreign Key constraints:
  - `fk_project_customer` - Project → Customer
  - `fk_project_product` - Project → Product
  - `fk_planning_project` - Planning → Project
  - `fk_planshift_planning` - Plan_shift → Planning
  - `fk_planshift_shift` - Plan_shift → Shiftment
  - `fk_planshift_staff` - Plan_shift → Staff
  - `fk_pmachine_planshift` - P_machine → Plan_shift
  - `fk_pmachine_machine` - P_machine → Machine
  - `fk_pmaterial_planshift` - P_material → Plan_shift
  - `fk_pmaterial_material` - P_material → Material
  - `fk_sorting_planshift` - Sorting_report → Plan_shift
  - `fk_finished_project` - Finished_report → Project

#### Migration Files
- `db/migration_ballpen_units.sql` - Convert units Kg → pieces/gram
- `db/migration_optional_diameter_decimal.sql` - Convert INT → DECIMAL
- `db/migration_add_diameter_to_product.sql` - Add diameter column
- `db/add_foreign_keys.sql` - Create all FK relationships
- `db/fix_vietnamese_charset.sql` - Fix UTF-8 encoding
- `db/fix_add_diameter_column.sql` - Quick fix for diameter

#### Documentation
- `db/DATABASE_RELATIONSHIPS.md` - ERD và relationship diagram
- `db/MIGRATION_README.md` - Hướng dẫn migration chi tiết
- `db/CHANGELOG_DIAMETER.md` - Changelog cho diameter feature
- `db/UPDATE_SUMMARY_DECIMAL_AUTOFILL.md` - Summary DECIMAL update
- `db/FIX_FONT_GUIDE.md` - Hướng dẫn fix font tiếng Việt
- `README.md` - Project documentation đầy đủ
- `.gitignore` - Git ignore rules

### Changed 🔄

#### Units Conversion
- Product quantity: Kg → pieces (cái)
- Material stock: Kg → gram
- Machine capacity: Kg → pieces/hour (cái/giờ)
- Planning target: Kg → pieces/shift (cái/ca)

#### Database Schema
- `product.diameter`: INT(25) → DECIMAL(3,1)
- `project.diameter`: INT(25) → DECIMAL(3,1)
- `product.application`: Renamed concept to "Ink Color" (Màu mực)
- Database charset: latin1 → utf8mb4_unicode_ci

#### UI/UX Improvements
- Product form: Input diameter với step="0.1", placeholder với ví dụ
- Project form: Auto-fill diameter với visual highlight
- Product list: Hiển thị diameter với format "0.5 mm"
- Footer: Bỏ năm "2024" khỏi copyright

#### Machine Status
- Thêm status thứ 4: "Bảo trì" (Maintenance)
- Color coding: Normal (green), Warning (yellow), Error (red), Maintenance (blue)

### Fixed 🐛

#### Database Errors
- Fix undefined property: `$diameter` trong Product.php
- Fix undefined property: `$shift_name` trong Sorting.php
- Thêm JOIN với bảng `shiftment` trong Sorting controller

#### Encoding Issues
- Fix Vietnamese charset từ latin1 → utf8mb4
- Fix garbled Vietnamese text: "m?c" → "mực"
- Update database connection config to utf8mb4

#### Language File Errors
- Fix "Unable to load form_validation_lang.php" error
- Tạo đầy đủ các file language cần thiết cho CodeIgniter

#### Query Issues
- Fix missing shiftment JOIN in sorting query
- Add proper ON DELETE/UPDATE CASCADE for foreign keys

### Removed ❌
- Năm "2024" từ footer copyright
- Các note về nhập giá trị x10 cho diameter (đã chuyển sang DECIMAL)

---

## [1.0.0] - 2023-11-09

### Initial Release
- Hệ thống quản lý sản xuất cơ bản
- Modules: Customer, Product, Project, Planning, Production, Machine, Material, Staff, Shiftment, Sorting, Finished
- Admin & Leader roles
- Material Dashboard 2 template
- Basic reporting features

---

## Quy ước Version

Dự án tuân theo [Semantic Versioning](https://semver.org/):

- **MAJOR**: Thay đổi lớn, breaking changes
- **MINOR**: Thêm tính năng mới, backward compatible
- **PATCH**: Bug fixes, không thay đổi API

## Loại thay đổi

- `Added` ✨ - Tính năng mới
- `Changed` 🔄 - Thay đổi trong tính năng hiện có
- `Deprecated` ⚠️ - Tính năng sẽ bị loại bỏ trong tương lai
- `Removed` ❌ - Tính năng đã bị loại bỏ
- `Fixed` 🐛 - Bug fixes
- `Security` 🔒 - Security fixes
