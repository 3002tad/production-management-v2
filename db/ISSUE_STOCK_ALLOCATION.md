# 🔍 PHÂN TÍCH: VẤN ĐỀ TRỪ DẦN TỒN KHO

**Ngày:** 7 tháng 12, 2025  
**Vấn đề:** Khi có nhiều đơn hàng cùng sản phẩm, tồn kho có bị trừ dần không?

---

## 📊 TÌNH HUỐNG THỰC TẾ (Theo ảnh giao diện)

### **Dữ liệu hiện tại:**
```
Product: Bút bi TL-079 (id_product = 1001)
Tồn kho: 35 cái

Đơn hàng 1 (PJ-TEST-1001): 10 cái → Badge "Có sẵn kho"
Đơn hàng 2 (ORD-1001-20251128-001): 3 cái → Badge "✓ OK"
Đơn hàng 3 (ORD-1001-20251207-001): 3 cái → Badge "Có sẵn kho"
Đơn hàng 4 (ORD-1001-20251207-002): 50 cái → Badge "✓ Bình thường"
```

---

## ❌ VẤN ĐỀ HIỆN TẠI

### **Logic code hiện tại (OrderModel.php line 343-380):**

```php
public function checkCapacity($id_product, $qty_request, $entry_date)
{
    // ===== 1. KIỂM TRA TỒN KHO =====
    $stock_query = $this->db->query("
        SELECT quantity_in_stock 
        FROM finished_stock 
        WHERE id_product = ?
    ", [$id_product]);
    
    $quantity_in_stock = 0; // LẤY TOÀN BỘ 35 CÁI
    if ($stock_query->num_rows() > 0) {
        $quantity_in_stock = $stock_query->row()->quantity_in_stock;
    }
    
    // KIỂM TRA: Nếu stock >= request → Giao ngay
    if ($quantity_in_stock >= $qty_request) {
        return [...]; // "Có sẵn kho"
    }
}
```

### **Vấn đề:**
**❌ MỖI ĐƠN HÀNG ĐỀU CHECK VỚI 35 CÁI TRONG KHO**
**❌ KHÔNG TRỪ DẦN KHI CÓ NHIỀU ĐƠN**

---

## 📋 CÁC TRƯỜNG HỢP CẦN XỬ LÝ

### **TRƯỜNG HỢP 1: Không quan tâm thứ tự (Logic hiện tại - SAI)**

**Tồn kho:** 35 cái

| Đơn hàng | Số lượng | Check logic | Kết quả hiện tại | Stock còn lại (ảo) |
|----------|----------|-------------|------------------|-------------------|
| Đơn 1 | 10 cái | 35 ≥ 10? ✅ | "Có sẵn kho" | 35 (không trừ) |
| Đơn 2 | 3 cái | 35 ≥ 3? ✅ | "Có sẵn kho" | 35 (không trừ) |
| Đơn 3 | 3 cái | 35 ≥ 3? ✅ | "Có sẵn kho" | 35 (không trừ) |
| Đơn 4 | 50 cái | 35 ≥ 50? ❌ | "Cần sản xuất 15" | 35 (không trừ) |

**Kết quả:**
- ❌ Đơn 1, 2, 3 đều báo "Có sẵn kho"
- ❌ Tổng giao: 10+3+3 = 16 cái
- ❌ Nhưng thực tế chỉ có 35 cái → Không đủ nếu giao hết
- ❌ Hệ thống không cảnh báo vượt tồn kho

---

### **TRƯỜNG HỢP 2: Trừ dần theo thứ tự ưu tiên (ĐÚNG - CẦN IMPLEMENT)**

**Tồn kho:** 35 cái  
**Thứ tự ưu tiên:** Theo `entry_date` (deadline sớm trước) hoặc `created_at` (tạo trước)

| Đơn hàng | Deadline | Số lượng | Stock check | Kết quả đúng | Stock còn lại (thật) |
|----------|----------|----------|-------------|--------------|---------------------|
| Đơn 3 | 10/12/2025 | 3 cái | 35 ≥ 3? ✅ | "Có 35 trong kho, dùng 3" | 35 - 3 = **32** |
| Đơn 1 | 31/12/2025 | 10 cái | 32 ≥ 10? ✅ | "Có 32 trong kho, dùng 10" | 32 - 10 = **22** |
| Đơn 2 | 31/12/2025 | 3 cái | 22 ≥ 3? ✅ | "Có 22 trong kho, dùng 3" | 22 - 3 = **19** |
| Đơn 4 | 31/12/2025 | 50 cái | 19 ≥ 50? ❌ | "Có 19 trong kho, cần SX thêm 31" | 19 - 19 = **0** |

**Kết quả:**
- ✅ Đơn 3: "Có 35 cái, dùng 3, còn 32"
- ✅ Đơn 1: "Có 32 cái, dùng 10, còn 22"
- ✅ Đơn 2: "Có 22 cái, dùng 3, còn 19"
- ✅ Đơn 4: "Có 19 cái, cần sản xuất thêm 31"
- ✅ Tổng chính xác: 35 cái đã phân bổ hết

---

### **TRƯỜNG HỢP 3: Vượt tồn kho (CẦN CẢNH BÁO)**

**Tồn kho:** 35 cái  
**Nhiều đơn hàng tổng > 35:**

| Đơn hàng | Số lượng | Stock check | Kết quả | Stock còn |
|----------|----------|-------------|---------|-----------|
| Đơn 1 | 20 cái | 35 ≥ 20? ✅ | "Có 35, dùng 20" | 15 |
| Đơn 2 | 30 cái | 15 ≥ 30? ❌ | "Có 15, cần SX thêm 15" | 0 |
| Đơn 3 | 10 cái | 0 ≥ 10? ❌ | "Hết stock, cần SX 10" | 0 |

**Kết quả:**
- ✅ Cảnh báo: "Tổng 3 đơn = 60 cái, stock chỉ 35"
- ✅ Đơn 1: Dùng 20 từ stock
- ✅ Đơn 2: Dùng 15 từ stock + SX thêm 15
- ✅ Đơn 3: Toàn bộ phải SX (10 cái)

---

## 🔧 GIẢI PHÁP ĐỀ XUẤT

### **Option 1: Trừ dần theo thứ tự (RECOMMENDED)**

**Ưu điểm:**
- ✅ Chính xác, công bằng
- ✅ Ưu tiên deadline sớm
- ✅ Không cảnh báo sai

**Nhược điểm:**
- ⚠️ Phức tạp hơn
- ⚠️ Cần query nhiều đơn hàng

**Implementation:**

```php
public function checkCapacity($id_product, $qty_request, $entry_date, $id_project = null)
{
    // ===== 1. LẤY TỒN KHO THỰC TẾ =====
    $total_stock = $this->getFinishedStock($id_product);
    
    // ===== 2. TRỪ ĐI CÁC ĐƠN HÀNG ĐÃ PHÂN BỔ (theo thứ tự ưu tiên) =====
    $allocated = $this->db->query("
        SELECT SUM(LEAST(qty_request, finished_stock_available)) as allocated
        FROM project
        WHERE id_product = ?
          AND pr_status < 3
          AND warning_flag = 1
          AND warning_details LIKE '%sufficient%'
          AND (entry_date < ? OR (entry_date = ? AND created_at < ?))
          AND id_project != ?
    ", [$id_product, $entry_date, $entry_date, $current_time, $id_project])->row();
    
    $available_stock = $total_stock - ($allocated->allocated ?? 0);
    
    // ===== 3. KIỂM TRA VỚI STOCK CÒN LẠI =====
    if ($available_stock >= $qty_request) {
        return [
            'feasible' => true,
            'warning_flag' => 1,
            'warning_details' => json_encode([
                'finished_stock_info' => "✓ Có $total_stock cái trong kho, đã phân bổ " . ($allocated->allocated ?? 0) . ", còn $available_stock cái, dùng $qty_request cho đơn này",
                'stock_status' => 'sufficient'
            ]),
            'finished_stock_available' => $available_stock
        ];
    } else {
        // Dùng hết stock còn lại + cần sản xuất
        $need_produce = $qty_request - $available_stock;
        return [
            'feasible' => true,
            'warning_flag' => 1,
            'warning_details' => json_encode([
                'finished_stock_info' => "✓ Có $available_stock cái trong kho (đã phân bổ " . ($allocated->allocated ?? 0) . "/$total_stock), cần sản xuất thêm $need_produce cái",
                'partial_stock' => true
            ]),
            'finished_stock_available' => $available_stock,
            'need_produce' => $need_produce
        ];
    }
}
```

---

### **Option 2: Cảnh báo tổng thể (SIMPLE)**

**Ưu điểm:**
- ✅ Đơn giản
- ✅ Chỉ cảnh báo khi vượt tổng

**Nhược điểm:**
- ⚠️ Không chi tiết
- ⚠️ Không ưu tiên deadline

**Implementation:**

```php
public function checkCapacity($id_product, $qty_request, $entry_date)
{
    // Kiểm tra như cũ
    $quantity_in_stock = 35; // Lấy từ DB
    
    // THÊM: Kiểm tra tổng các đơn hàng
    $total_orders = $this->db->query("
        SELECT SUM(qty_request) as total
        FROM project
        WHERE id_product = ?
          AND pr_status < 3
    ", [$id_product])->row();
    
    if ($total_orders->total > $quantity_in_stock) {
        $warnings['stock_warning'] = "⚠️ CẢNH BÁO: Tổng " . count($orders) . " đơn hàng = " . $total_orders->total . " cái, vượt tồn kho ($quantity_in_stock cái)";
    }
}
```

---

## 📊 SO SÁNH 2 OPTIONS

| Tiêu chí | Option 1: Trừ dần | Option 2: Cảnh báo tổng |
|----------|-------------------|------------------------|
| **Độ chính xác** | ✅ Rất cao | ⚠️ Trung bình |
| **Độ phức tạp** | ⚠️ Cao | ✅ Thấp |
| **Ưu tiên deadline** | ✅ Có | ❌ Không |
| **Phân bổ stock** | ✅ Chính xác từng đơn | ❌ Không phân bổ |
| **Cảnh báo vượt** | ✅ Chi tiết | ✅ Có nhưng đơn giản |
| **Performance** | ⚠️ Query nhiều | ✅ Query ít |
| **Khuyến nghị** | ⭐⭐⭐⭐⭐ | ⭐⭐⭐ |

---

## 🎯 KHUYẾN NGHỊ

### **Nên dùng: OPTION 1 - Trừ dần theo thứ tự**

**Lý do:**
1. ✅ **Chính xác 100%** - Mỗi đơn biết chính xác dùng bao nhiêu stock
2. ✅ **Công bằng** - Deadline sớm được ưu tiên
3. ✅ **Tránh conflict** - Không có 2 đơn tranh giành cùng 1 stock
4. ✅ **Production ready** - Đúng với nghiệp vụ thực tế

**Thứ tự ưu tiên:**
```sql
ORDER BY 
    entry_date ASC,     -- Deadline sớm trước
    created_at ASC,     -- Tạo trước ưu tiên
    id_project ASC      -- ID nhỏ hơn ưu tiên (backup)
```

---

## 🔍 KIỂM TRA HIỆN TẠI

### **Test case với database thật:**

**Giả sử:**
- Tồn kho: 35 cái
- Đơn 1: 10 cái (31/12/2025)
- Đơn 2: 3 cái (31/12/2025)
- Đơn 3: 3 cái (10/12/2025) ← Deadline sớm nhất
- Đơn 4: 50 cái (31/12/2025)

**Logic ĐÚNG:**
1. Đơn 3 (deadline 10/12): Dùng 3 từ stock, còn 32
2. Đơn 1 (deadline 31/12, tạo trước): Dùng 10 từ stock, còn 22
3. Đơn 2 (deadline 31/12, tạo sau): Dùng 3 từ stock, còn 19
4. Đơn 4 (deadline 31/12, tạo sau cùng): Dùng 19 từ stock + SX 31

**Logic SAI (hiện tại):**
- Tất cả đều check với 35 cái
- Không biết stock đã phân bổ cho đơn nào

---

## 📝 KẾT LUẬN

### **Trả lời câu hỏi:**

**"Nếu có sẵn trong kho 35 cái thì 2 đơn hàng trước chiếm hết 13 cái để giao sẵn, thì đơn hàng sau cùng sản phẩm thì có trừ tồn kho ra để thông báo không?"**

**Trả lời:** ❌ **HIỆN TẠI KHÔNG TRỪ DẦN**

**Giải thích:**
- ❌ Mỗi đơn hàng đều check với **TOÀN BỘ 35 cái** trong `finished_stock`
- ❌ Không tính đơn hàng trước đã dùng bao nhiêu
- ❌ Có thể xảy ra: 5 đơn hàng 10 cái đều báo "Có sẵn kho" → Tổng 50 > 35 (SAI!)

**Cần làm:**
- ✅ Implement logic trừ dần theo thứ tự ưu tiên
- ✅ Query tất cả đơn hàng cùng sản phẩm
- ✅ Sort theo deadline (entry_date) + created_at
- ✅ Trừ dần stock, mỗi đơn biết chính xác dùng bao nhiêu
- ✅ Cảnh báo khi tổng vượt stock

---

**File này mô tả:** Vấn đề hiện tại + 3 trường hợp + 2 giải pháp + Khuyến nghị implement

**Người phân tích:** GitHub Copilot (Claude Sonnet 4.5)  
**Ngày:** 7 tháng 12, 2025
