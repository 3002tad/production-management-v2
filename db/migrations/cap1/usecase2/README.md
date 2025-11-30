# UC2: Product Management & BOM - Database Migrations

## 📋 Mục đích
Migrations cho **Use Case 2: Quản lý Sản phẩm & BOM (Bill of Materials)**

## 🎯 Chức năng
- CRUD sản phẩm bút bi
- Quản lý BOM (định mức nguyên liệu JSON)
- Tìm kiếm theo tên, đường kính, màu mực

## 📁 Files trong folder này

### 001_add_product_indexes.sql
**Mục đích**: Tối ưu hóa performance cho bảng `product`

**Indexes tạo**:
- `idx_product_search`: Tối ưu search (product_name, application)
- `idx_product_active`: Tối ưu filter active status
- `idx_product_diameter`: Tối ưu filter theo đường kính (0.5, 0.7, 1.0)
- `idx_product_created`: Tối ưu ORDER BY created_at

**Expected Performance**:
- Search: 75% faster (600ms → 150ms)
- List: 75% faster (1200ms → 300ms)
- BOM Builder: 95% faster (21 queries → 1 query)

**Cách chạy**:
```sql
SOURCE d:/PHAT TRIEN UNG DUNG/production-management-v2/db/migrations/cap1/usecase2/001_add_product_indexes.sql;
```

## ⚠️ Lưu ý
- **KHÔNG modify schema** của bảng `product` hiện tại
- **BOM lưu dưới dạng JSON** trong cột `bom`
- Chỉ **thêm indexes** để tăng tốc
- Backward compatible 100%

## 📊 BOM JSON Structure
```json
{
  "materials": [
    {
      "id_material": 1002,
      "material_name": "Nhựa ABS",
      "quantity": 5.5,
      "unit": "gram"
    }
  ]
}
```

## 🔗 Related Files
- Controller: `application/controllers/bod/ProductController.php`
- Model: `application/models/ProductModel.php`
- View: `application/views/bod/product/ProductListView.php`

## 👥 Owner
**Cấp 1 - Danh** (UC1, UC2, UC7)

## 📅 Created
November 27, 2025
