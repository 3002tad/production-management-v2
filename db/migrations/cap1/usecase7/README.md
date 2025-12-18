# UC7: Order Management - Database Migrations

## 📋 Mục đích
Migrations cho **Use Case 7: Tiếp nhận và tạo Đơn hàng**

## 🎯 Chức năng
- Tạo đơn hàng mới từ khách hàng
- Kiểm tra công suất sản xuất
- Auto-generate project name (ORD-{cust}-{date}-{seq})
- Quản lý trạng thái đơn hàng

## 📁 Files trong folder này

### 001_add_project_indexes.sql
**Mục đích**: Tối ưu hóa performance cho bảng `project` (đơn hàng)

**Indexes tạo**:
- `idx_project_customer`: Tối ưu JOIN với customer (FK)
- `idx_project_product`: Tối ưu JOIN với product (FK)
- `idx_project_status`: Tối ưu filter theo trạng thái
- `idx_project_stats`: Tối ưu thống kê (COUNT, SUM)

**Expected Performance**:
- List orders: 60% faster (1000ms → 400ms)
- JOIN với customer/product: 40% faster
- Thống kê: 50% faster

**Cách chạy**:
```sql
SOURCE d:/PHAT TRIEN UNG DUNG/production-management-v2/db/migrations/cap1/usecase7/001_add_project_indexes.sql;
```

## ⚠️ Lưu ý
- **KHÔNG modify schema** của bảng `project` hiện tại
- FK constraints vẫn giữ nguyên (customer, product)
- Chỉ **thêm indexes** để tăng tốc
- Backward compatible 100%

## 📊 Project Name Format
```
ORD-{id_cust}-{YYYYMMDD}-{seq}
Example: ORD-1001-20251127-001
```

## 🔗 Related Files
- Controller: `application/controllers/bod/OrderController.php`
- Model: `application/models/OrderModel.php`
- View: `application/views/bod/order/OrderListView.php`

## 👥 Owner
**Cấp 1 - Danh** (UC1, UC2, UC7)

## 📅 Created
November 27, 2025
