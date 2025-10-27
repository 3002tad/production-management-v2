# Contributing Guidelines

Cảm ơn bạn đã quan tâm đến việc đóng góp cho Production Management System! 🎉

## 📋 Mục lục

- [Code of Conduct](#code-of-conduct)
- [Cách đóng góp](#cách-đóng-góp)
- [Quy trình Development](#quy-trình-development)
- [Coding Standards](#coding-standards)
- [Commit Message Guidelines](#commit-message-guidelines)
- [Pull Request Process](#pull-request-process)

## 📜 Code of Conduct

### Cam kết của chúng tôi

- Tôn trọng tất cả mọi người
- Chấp nhận phản hồi mang tính xây dựng
- Tập trung vào điều tốt nhất cho cộng đồng
- Thể hiện sự đồng cảm với các thành viên khác

### Hành vi không được chấp nhận

- Ngôn ngữ hoặc hình ảnh khiêu dâm
- Trolling, bình luận xúc phạm
- Quấy rối công khai hoặc riêng tư
- Công khai thông tin cá nhân của người khác

## 🤝 Cách đóng góp

### Báo cáo Bug

Trước khi tạo bug report:
- Kiểm tra [Issues](https://github.com/[username]/production-management-v2/issues) xem bug đã được report chưa
- Kiểm tra [Changelog](CHANGELOG.md) xem bug đã được fix chưa

Khi tạo bug report, bao gồm:
- **Tiêu đề rõ ràng** và mô tả chi tiết
- **Các bước để tái hiện** bug
- **Kết quả mong đợi** vs **kết quả thực tế**
- **Screenshots** (nếu có)
- **Environment info**:
  - OS: [Windows 10, Ubuntu 20.04, etc.]
  - PHP Version: [7.4, 8.0, etc.]
  - MySQL/MariaDB Version: [5.7, 10.4, etc.]
  - Browser: [Chrome 95, Firefox 94, etc.]

**Template:**
```markdown
## Mô tả bug
Mô tả ngắn gọn về bug

## Các bước tái hiện
1. Vào trang '...'
2. Click vào '...'
3. Scroll xuống '...'
4. Thấy lỗi

## Kết quả mong đợi
Mô tả điều bạn mong đợi xảy ra

## Kết quả thực tế
Mô tả điều thực sự xảy ra

## Screenshots
Nếu có, thêm screenshots

## Environment
- OS: 
- PHP: 
- MySQL: 
- Browser: 
```

### Đề xuất tính năng mới

Khi đề xuất tính năng mới:
- **Giải thích lý do** cần tính năng này
- **Mô tả chi tiết** tính năng hoạt động như thế nào
- **Mockups/wireframes** (nếu có)
- **Alternatives** bạn đã cân nhắc

### Pull Requests

1. Fork repo và tạo branch từ `main`
2. Implement changes
3. Test kỹ trên local
4. Update documentation nếu cần
5. Tạo Pull Request

## 🔧 Quy trình Development

### 1. Setup Environment

```bash
# Clone repo
git clone https://github.com/[username]/production-management-v2.git
cd production-management-v2

# Checkout branch mới
git checkout -b feature/ten-tinh-nang
```

### 2. Development

```bash
# Làm việc trên code của bạn
# Test thường xuyên

# Kiểm tra changes
git status
git diff
```

### 3. Testing Checklist

Trước khi commit, kiểm tra:

- [ ] Code chạy không lỗi
- [ ] Tất cả features hoạt động đúng
- [ ] Không breaking existing functionality
- [ ] UI responsive trên mobile/tablet
- [ ] Tiếng Việt hiển thị đúng (không lỗi encoding)
- [ ] Database queries tối ưu
- [ ] Không có SQL injection vulnerabilities
- [ ] Không có XSS vulnerabilities
- [ ] Form validation hoạt động
- [ ] Error handling đầy đủ

### 4. Commit & Push

```bash
# Stage changes
git add .

# Commit với message rõ ràng
git commit -m "feat: thêm tính năng xuất Excel cho báo cáo"

# Push lên GitHub
git push origin feature/ten-tinh-nang
```

## 📝 Coding Standards

### PHP Coding Style

Tuân theo [CodeIgniter Style Guide](https://codeigniter.com/userguide3/general/styleguide.html):

**File Naming:**
```php
// Controllers: PascalCase
Admin.php
CustomerController.php

// Models: PascalCase + Model suffix
CustomerModel.php
ProductModel.php

// Views: lowercase, underscores
customer_list.php
add_product.php
```

**Code Formatting:**
```php
<?php
// Class names: PascalCase
class Customer_model extends CI_Model
{
    // Methods: camelCase
    public function getCustomerById($id)
    {
        // Variables: snake_case hoặc camelCase
        $customer_data = $this->db->get_where('customer', ['id_cust' => $id]);
        
        // Spaces around operators
        if ($customer_data->num_rows() > 0) {
            return $customer_data->row();
        }
        
        return null;
    }
}
```

**Database Queries:**
```php
// ✅ GOOD: Query builder (an toàn hơn)
$this->db->select('*')
         ->from('customer')
         ->where('id_cust', $id)
         ->get();

// ⚠️ CAUTION: Raw queries (cần escape input)
$this->db->query("SELECT * FROM customer WHERE id_cust = ?", array($id));

// ❌ BAD: SQL injection risk
$this->db->query("SELECT * FROM customer WHERE id_cust = $id");
```

### JavaScript Style

```javascript
// Use strict mode
'use strict';

// Constants: UPPERCASE
const API_URL = 'http://api.example.com';

// Variables: camelCase
let customerName = 'John Doe';

// Functions: camelCase
function calculateTotal(items) {
    return items.reduce((sum, item) => sum + item.price, 0);
}

// Use ES6+ features
const products = items.map(item => ({
    id: item.id,
    name: item.name
}));
```

### CSS/SCSS Style

```css
/* Use BEM naming convention */
.customer-card {}
.customer-card__header {}
.customer-card__body {}
.customer-card--active {}

/* Group related properties */
.button {
    /* Positioning */
    position: relative;
    
    /* Display & Box Model */
    display: inline-block;
    padding: 10px 20px;
    
    /* Typography */
    font-size: 14px;
    
    /* Visual */
    background: #007bff;
    border-radius: 4px;
    
    /* Misc */
    cursor: pointer;
}
```

### SQL Style

```sql
-- Uppercase for keywords
SELECT 
    c.id_cust,
    c.cust_name,
    p.project_name
FROM customer c
INNER JOIN project p ON c.id_cust = p.id_cust
WHERE c.id_cust = 1001
ORDER BY p.entry_date DESC;

-- Indent for readability
CREATE TABLE customer (
    id_cust INT(25) NOT NULL AUTO_INCREMENT,
    cust_name VARCHAR(50) NOT NULL,
    address VARCHAR(50) NOT NULL,
    PRIMARY KEY (id_cust)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

## 💬 Commit Message Guidelines

### Format

```
<type>(<scope>): <subject>

<body>

<footer>
```

### Type

- `feat`: Tính năng mới
- `fix`: Bug fix
- `docs`: Thay đổi documentation
- `style`: Formatting, missing semi colons, etc (không ảnh hưởng code)
- `refactor`: Code refactoring
- `perf`: Performance improvements
- `test`: Thêm tests
- `chore`: Maintain, dependencies, etc
- `revert`: Revert commit trước

### Scope (tùy chọn)

Module bị ảnh hưởng: `customer`, `product`, `planning`, `database`, etc.

### Subject

- Dùng imperative mood: "add" not "added"
- Không viết hoa chữ cầu đầu
- Không dấu chấm ở cuối
- Tối đa 50 ký tự

### Body (tùy chọn)

- Giải thích **what** và **why**, không phải **how**
- Ngắt dòng ở 72 ký tự

### Footer (tùy chọn)

- Reference Issues: `Closes #123, Fixes #456`
- Breaking changes: `BREAKING CHANGE: description`

### Ví dụ

**Simple:**
```
feat(product): thêm auto-fill diameter khi chọn sản phẩm
```

**Detailed:**
```
feat(product): thêm tính năng auto-fill diameter

Khi user chọn product trong form tạo project, trường diameter
sẽ tự động được điền với giá trị từ product.

Features:
- Auto-fill khi chọn product
- Visual highlight khi fill thành công
- Cho phép user override giá trị

Closes #45
```

**Bug fix:**
```
fix(sorting): sửa lỗi undefined shift_name

Thêm JOIN với bảng shiftment trong sorting controller
để load đầy đủ thông tin ca làm việc.

Fixes #67
```

**Breaking change:**
```
refactor(database): chuyển diameter từ INT sang DECIMAL

BREAKING CHANGE: 
Cột diameter trong bảng product và project đã đổi từ INT(25)
sang DECIMAL(3,1). Migration script cần chạy để update database.

Migration: db/migration_optional_diameter_decimal.sql

Closes #89
```

## 🔀 Pull Request Process

### Before submitting

1. **Update documentation** nếu cần
2. **Add/update tests** nếu có
3. **Run tests** đảm bảo pass hết
4. **Update CHANGELOG.md** với changes
5. **Rebase** với branch main mới nhất

### PR Template

```markdown
## Mô tả
Mô tả ngắn gọn về thay đổi

## Loại thay đổi
- [ ] Bug fix
- [ ] New feature
- [ ] Breaking change
- [ ] Documentation update

## Checklist
- [ ] Code follows style guidelines
- [ ] Self-review completed
- [ ] Commented code (nếu cần)
- [ ] Documentation updated
- [ ] No new warnings
- [ ] Tests added/updated
- [ ] All tests passing
- [ ] CHANGELOG updated

## Related Issues
Closes #(issue number)

## Screenshots (nếu có)
Thêm screenshots nếu có UI changes

## Additional Notes
Thông tin bổ sung cho reviewers
```

### Review Process

PRs cần:
- ✅ Ít nhất 1 approval từ maintainer
- ✅ All CI checks passing
- ✅ No merge conflicts
- ✅ Code review comments resolved

### Merge

Sau khi approved:
1. Maintainer sẽ merge PR
2. Branch feature sẽ được delete
3. Changes sẽ có trong release tiếp theo

## ❓ Questions?

Nếu có câu hỏi:
- Tạo [Discussion](https://github.com/[username]/production-management-v2/discussions)
- Liên hệ qua email: support@example.com

---

Cảm ơn bạn đã đóng góp! 🙏
