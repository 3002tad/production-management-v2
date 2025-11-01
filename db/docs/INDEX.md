# 📚 Database Documentation Index

Thư mục này chứa tài liệu kỹ thuật, changelogs và hướng dẫn liên quan đến database.

---

## 📋 Danh sách Documents

### **1. DATABASE_RELATIONSHIPS.md** 🔗
- **Mục đích:** Hiểu cấu trúc database và mối quan hệ giữa các tables
- **Nội dung:**
  - ERD (Entity Relationship Diagram)
  - 12 Foreign Key constraints chi tiết
  - Mô tả từng relationship
  - CASCADE behaviors
- **Đọc khi:**
  - Cần hiểu data flow trong hệ thống
  - Debug lỗi foreign key violations
  - Thiết kế features mới cần join tables
  - Onboarding developer mới

**Quick View:**
```
customer → project → planning → plan_shift → production
                                           ↘
                                            sorting → staff
         ↓                                  ↓
     finished_report ← finished        machine
```

---

### **2. MIGRATION_README.md** 📖
- **Mục đích:** Hướng dẫn các migrations cũ (trước RBAC)
- **Nội dung:**
  - Lịch sử migrations từ version 1.0 → 1.3
  - Hướng dẫn chạy migrations ballpen units, diameter, FKs
  - Troubleshooting migration issues
- **Trạng thái:** ⚠️ Outdated - Đã được thay thế bởi `/migrations/README.md`
- **Đọc khi:**
  - Cần hiểu lịch sử database changes
  - Tham khảo cách viết migrations
  - Debug issues với features cũ

---

### **3. CHANGELOG_DIAMETER.md** 📝
- **Mục đích:** Chi tiết về diameter feature
- **Nội dung:**
  - **Phase 1:** Add diameter column (VARCHAR)
  - **Phase 2:** Convert to DECIMAL(3,1)
  - **Phase 3:** Make optional (NULL allowed)
  - Code changes trong Product, Project controllers
  - View updates
- **Đọc khi:**
  - Làm việc với diameter field
  - Cần hiểu tại sao diameter là DECIMAL không phải VARCHAR
  - Debug validation issues

**Key Changes:**
```
Application: "Đầu bút" → "Đường kính"
Data Type: VARCHAR(10) → DECIMAL(3,1) NULL
Values: "0.5mm", "0.7mm", "1.0mm" → 0.5, 0.7, 1.0
```

---

### **4. UPDATE_SUMMARY_DECIMAL_AUTOFILL.md** 🔄
- **Mục đích:** Tóm tắt 2 updates lớn
- **Nội dung:**
  
  **Part 1: DECIMAL Conversion**
  - Lý do chuyển từ VARCHAR → DECIMAL
  - Migration steps
  - Validation rules
  
  **Part 2: Auto-fill Diameter**
  - JavaScript auto-fill khi tạo project từ product
  - AJAX load product details
  - Code implementation

- **Đọc khi:**
  - Cần overview nhanh 2 features chính
  - Implement tính năng tương tự
  - Review architecture decisions

---

### **5. FIX_FONT_GUIDE.md** 🔧
- **Mục đích:** Fix tiếng Việt hiển thị lỗi (garbled text)
- **Nội dung:**
  - Nguyên nhân: charset latin1 không support tiếng Việt
  - Giải pháp: Convert sang utf8mb4_unicode_ci
  - Migration SQL script
  - Config CodeIgniter database
  - Testing checklist
  
- **Đọc khi:**
  - Tiếng Việt hiển thị ??? hoặc ký tự lạ
  - Setup database mới
  - Migrate database từ hệ thống cũ
  - Config new environment

**Quick Fix:**
```sql
ALTER DATABASE db_production CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE table_name CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

---

## 🗂️ Documents Grouped by Topic

### **Database Structure & Design**
- `DATABASE_RELATIONSHIPS.md` - ERD & Foreign Keys

### **Migration History**
- `MIGRATION_README.md` - Old migrations guide
- `CHANGELOG_DIAMETER.md` - Diameter feature history
- `UPDATE_SUMMARY_DECIMAL_AUTOFILL.md` - Major updates summary

### **Troubleshooting**
- `FIX_FONT_GUIDE.md` - UTF-8 encoding fix

---

## 📊 Document Status

| Document | Status | Last Updated | Relevance |
|----------|--------|--------------|-----------|
| DATABASE_RELATIONSHIPS.md | ✅ Current | 2023-11-09 | High |
| MIGRATION_README.md | ⚠️ Outdated | 2023-11-09 | Medium (historical) |
| CHANGELOG_DIAMETER.md | ✅ Current | 2023-11-09 | High |
| UPDATE_SUMMARY_DECIMAL_AUTOFILL.md | ✅ Current | 2023-11-09 | High |
| FIX_FONT_GUIDE.md | ✅ Current | 2023-11-09 | High |

---

## 🔍 Quick Reference

### **Tôi muốn hiểu...**

| Mục đích | Đọc file |
|----------|----------|
| Cấu trúc database tổng quan | `DATABASE_RELATIONSHIPS.md` |
| Tại sao diameter là DECIMAL? | `CHANGELOG_DIAMETER.md` |
| Fix tiếng Việt lỗi font | `FIX_FONT_GUIDE.md` |
| Lịch sử migrations | `MIGRATION_README.md` |
| Auto-fill diameter như thế nào? | `UPDATE_SUMMARY_DECIMAL_AUTOFILL.md` |

### **Tôi gặp lỗi...**

| Lỗi | Giải pháp |
|-----|-----------|
| Foreign key constraint fails | `DATABASE_RELATIONSHIPS.md` - check relationships |
| Tiếng Việt hiển thị ??? | `FIX_FONT_GUIDE.md` - convert to utf8mb4 |
| Diameter validation error | `CHANGELOG_DIAMETER.md` - check DECIMAL rules |
| Migration fails | `MIGRATION_README.md` - troubleshooting section |

---

## 📝 Document Guidelines

Khi thêm document mới:

1. **Naming Convention:** `SCREAMING_SNAKE_CASE.md`
2. **Header:** Luôn có mục đích, nội dung, khi nào đọc
3. **Format:** Markdown với emoji để dễ scan
4. **Update:** Cập nhật INDEX.md này
5. **Status:** Đánh dấu Current/Outdated

---

## 🔗 Related Resources

- [Database README](../README.md) - Overview thư mục db
- [Migrations README](../migrations/README.md) - RBAC migrations guide
- [Archives README](../archives/README.md) - Old migrations
- [Project README](../../README.md) - Tổng quan dự án

---

**Last Updated:** November 1, 2025  
**Maintained by:** Production Management Team
