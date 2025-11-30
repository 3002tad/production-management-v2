# UC1: Customer Management - Database Migrations

## 📋 Mục đích
Migrations cho **Use Case 1: Quản lý Khách hàng**

## 🎯 Chức năng
- Tạo/sửa/xóa khách hàng
- Xem danh sách khách hàng với thống kê đơn hàng
- Tìm kiếm theo tên, email, số điện thoại

## 📁 Files trong folder này

### 001_add_customer_indexes.sql
**Mục đích**: Tối ưu hóa performance cho bảng `customer`

**Indexes tạo**:
- `idx_customer_search`: Tối ưu search (cust_name, email, telp)
- `idx_customer_active`: Tối ưu filter active status
- `idx_customer_created`: Tối ưu ORDER BY created_at

**Expected Performance**:
- Search: 75% faster (600ms → 150ms)
- List: 69% faster (800ms → 250ms)
- Filter: 50% faster

**Cách chạy**:
```sql
SOURCE d:/PHAT TRIEN UNG DUNG/production-management-v2/db/migrations/cap1/usecase1/001_add_customer_indexes.sql;
```

## ⚠️ Lưu ý
- **KHÔNG modify schema** của bảng `customer` hiện tại
- **KHÔNG xóa data** cũ
- Chỉ **thêm indexes** để tăng tốc
- Backward compatible 100%

## 🔗 Related Files
- Controller: `application/controllers/bod/CustomerController.php`
- Model: `application/models/CustomerModel.php`
- View: `application/views/bod/customer/CustomerListView.php`

## 👥 Owner
**Cấp 1 - Danh** (UC1, UC2, UC7)

## 📅 Created
November 27, 2025
