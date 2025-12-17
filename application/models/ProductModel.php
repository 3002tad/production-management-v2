<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ProductModel extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Lấy tất cả sản phẩm kèm theo số lượng NVL trong BOM.
     * @return array
     */
    public function getAllProducts($filters = [])
    {
        // Cung cấp alias `total_orders` và `blocking_orders` để phù hợp với view (dùng DISTINCT để tránh trùng)
        // blocking_orders đếm số project có pr_status trong (1,2,3) và sẽ ngăn việc sửa sản phẩm
        $this->db->select('p.*, COALESCE(JSON_LENGTH(p.bom), 0) as bom_count, COUNT(DISTINCT pr.id_project) as total_orders, COALESCE(SUM(CASE WHEN pr.pr_status IN (1,2,3) THEN 1 ELSE 0 END), 0) as blocking_orders');
        $this->db->from('product p');
        $this->db->join('project pr', 'p.id_product = pr.id_product', 'left');

        // Áp dụng bộ lọc một cách an toàn
        if (!empty($filters['keyword'])) {
            $this->db->group_start();
            $this->db->like('p.product_name', $filters['keyword']);
            $this->db->or_like('p.summary', $filters['keyword']);
            $this->db->group_end();
        }

        if (isset($filters['has_bom']) && $filters['has_bom'] !== '') {
            if ($filters['has_bom'] === 'yes') {
                $this->db->where('JSON_LENGTH(p.bom) > 0', NULL, FALSE);
            } elseif ($filters['has_bom'] === 'no') {
                $this->db->where("(p.bom IS NULL OR JSON_LENGTH(p.bom) = 0)");
            }
        }

        if (isset($filters['min_orders']) && is_numeric($filters['min_orders'])) {
            $min = intval($filters['min_orders']);
            $this->db->having('COUNT(DISTINCT pr.id_project) >= ' . $min);
        }

        if (isset($filters['is_active']) && $filters['is_active'] !== '') {
            $val = intval($filters['is_active']);
            $this->db->where('p.is_active', $val);
        }

        $this->db->group_by('p.id_product');
        $this->db->order_by('p.product_name', 'ASC');
        return $this->db->get()->result();
    }

    /**
     * Lấy thông tin sản phẩm theo ID.
     * @param string $productId
     * @return object|null
     */
    public function getProductById($productId)
    {
        return $this->db->get_where('product', ['id_product' => $productId])->row();
    }

    /**
     * Lấy danh sách tất cả nguyên vật liệu đang hoạt động.
     * @return array Danh sách các nguyên vật liệu.
     */
    public function getMaterialsList()
    {
        // Trả về danh sách nguyên vật liệu cùng tồn kho hiện tại để hiển thị ở giao diện
        $this->db->select('id_material, material_name, uom, COALESCE(stock, 0) as stock');
        $this->db->from('material');
        $this->db->order_by('material_name', 'ASC');
        return $this->db->get()->result();
    }

    /**
     * Tạo một sản phẩm mới và Định mức NVL (BOM) của nó trong một transaction.
     * Đảm bảo rằng cả hai đều được lưu thành công hoặc không có gì được lưu.
     *
     * @param array $productData Dữ liệu cho bảng 'product'.
     * @param array $materials   Mảng các nguyên vật liệu cho BOM.
     *                           Mỗi item là một mảng ['id_material' => x, 'quantity' => y].
     * @return mixed Trả về ID sản phẩm mới nếu thành công, ngược lại trả về false.
     */
    public function createProductWithBom($productData, $materials)     {
        $this->db->trans_begin();

        // Chuẩn bị dữ liệu BOM dưới dạng JSON
        $bomJson = [];
        if (!empty($materials) && is_array($materials)) {
            foreach ($materials as $material) {
                // Chấp nhận cả NVL đã có id và NVL tùy chỉnh không có id
                $qty = isset($material['quantity_per_unit']) ? $material['quantity_per_unit'] : (isset($material['quantity']) ? $material['quantity'] : null);
                $hasQty = is_numeric($qty) && $qty > 0;
                $hasName = !empty($material['material_name']);
                $hasId = !empty($material['id_material']);

            if ($hasQty && ($hasId || $hasName)) {
                    $bomJson[] = [
                        'id_material'       => $hasId ? $material['id_material'] : null,
                        'material_name'     => $material['material_name'] ?? null,
                        'quantity_per_unit' => (float)$qty,
                        'uom'               => $material['uom'] ?? null
                    ];
            }
}
        }
        $productData['bom'] = !empty($bomJson) ? json_encode($bomJson, JSON_UNESCAPED_UNICODE) : null;

        $this->db->insert('product', $productData);
        $productId = $productData['id_product'];

            if ($this->db->trans_status() === FALSE) {
                            $this->db->trans_rollback();
            return false;
        } else {
            $this->db->trans_commit();
            return $productId;
        }
    }

    /**
     * Lấy thông tin sản phẩm và BOM của nó.
     * 
     * @param string $productId ID của sản phẩm.
     * @return object|null Trả về object sản phẩm kèm theo mảng 'bom_items' hoặc null nếu không tìm thấy.
     */
    public function getProductByIdWithBom($productId)
    {
        // 1. Lấy thông tin sản phẩm chính
        $product = $this->db->get_where('product', ['id_product' => $productId])->row();

        if (!$product) {
                return null;
            }
        
        $product->bom_items = []; // Khởi tạo mảng rỗng cho bom_items

        // 2. Nếu cột 'bom' có dữ liệu JSON, giải mã nó
        if (!empty($product->bom)) {
            $bom_from_json = json_decode($product->bom, true);

            if (is_array($bom_from_json)) {
                $material_ids = array_column($bom_from_json, 'id_material');
        
        if (!empty($material_ids)) {
                    // Lấy thông tin material_name và uom từ bảng material
                    // Include current stock for BOM display and calculations
                    $materials_data = $this->db->select('id_material, material_name, uom, COALESCE(stock, 0) as stock')
                                               ->where_in('id_material', $material_ids)
                                               ->get('material')
                                               ->result_array();
                    
                    $material_map = [];
                    foreach ($materials_data as $m) {
                        $material_map[$m['id_material']] = $m;
    }

    // Ghép thông tin từ bảng material vào bom_items
                    foreach ($bom_from_json as $item) {
                        // Normalize unit key used by views: set both `uom` and `unit`
                        $item['unit'] = $item['uom'] ?? $item['unit'] ?? null;

                        if (!empty($item['id_material']) && isset($material_map[$item['id_material']])) {
                            $item['material_name'] = $material_map[$item['id_material']]['material_name'];
                            $item['uom'] = $material_map[$item['id_material']]['uom'];
                            // Keep compatibility: set `unit` to material.uom when material exists
                            $item['unit'] = $material_map[$item['id_material']]['uom'] ?? $item['unit'];
                            $item['stock'] = $material_map[$item['id_material']]['stock'];
                        } else {
                            $item['stock'] = 0;
                            // Giữ unit nếu có trong BOM, ngược lại null
                            $item['unit'] = $item['unit'] ?? null;
                        }

                        $product->bom_items[] = (object)$item; // Convert to object for consistency
                    }
                }
            }
        }

        // Backwards-compatibility for views expecting bom_data['materials'] (associative arrays)
        $product->bom_data = ['materials' => []];
        if (!empty($product->bom_items)) {
            foreach ($product->bom_items as $itm) {
                // Convert object item -> associative array for legacy views
                $product->bom_data['materials'][] = json_decode(json_encode($itm), true);
            }
        }

        return $product;
    }

    /**
     * Cập nhật một sản phẩm và Định mức NVL (BOM) của nó trong một transaction.
     * 
     * @param string $productId   ID của sản phẩm cần cập nhật.
     * @param array  $productData Dữ liệu mới cho bảng 'product'.
     * @param array  $materials   Mảng nguyên vật liệu mới cho BOM.
     * @return bool True nếu thành công, ngược lại false.
     */
    /**
     * Tìm các bảng tham chiếu đến product(id_product) và đếm số hàng tham chiếu.
     * Trả về mảng ['table_name' => count]
     *
     * @param string|int $productId
     * @return array
     */
    public function findReferences($productId)
    {
        $refs = [];
        try {
            $refs_sql = "SELECT TABLE_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE REFERENCED_TABLE_NAME = 'product' AND REFERENCED_COLUMN_NAME = 'id_product' AND TABLE_SCHEMA = DATABASE()";
            $tables = $this->db->query($refs_sql)->result_array();
        } catch (Exception $e) {
            log_message('error', 'Failed to query INFORMATION_SCHEMA for product references: ' . $e->getMessage());
            return [];
        }

        foreach ($tables as $r) {
            $table = $r['TABLE_NAME'];
            $escaped_table = $this->db->escape_str($table);
            $count_row = $this->db->query("SELECT COUNT(*) as c FROM `" . $escaped_table . "` WHERE `id_product` = ?", [$productId])->row();
            $count = isset($count_row->c) ? (int)$count_row->c : 0;
            if ($count > 0) $refs[$table] = $count;
        }

        return $refs;
    }

    public function updateProductWithBom($productId, $productData, $materials) {
        $this->db->trans_begin();

        // Chuẩn bị dữ liệu BOM dưới dạng JSON
        $bomJson = [];
        if (!empty($materials) && is_array($materials)) {
            foreach ($materials as $material) {
                $qty = isset($material['quantity_per_unit']) ? $material['quantity_per_unit'] : (isset($material['quantity']) ? $material['quantity'] : null);
                $hasQty = is_numeric($qty) && $qty > 0;
                $hasName = !empty($material['material_name']);
                $hasId = !empty($material['id_material']);

                if ($hasQty && ($hasId || $hasName)) {
            $bomJson[] = [
                'id_material'       => $hasId ? $material['id_material'] : null,
                'material_name'     => $material['material_name'] ?? null,
                'quantity_per_unit' => (float)$qty,
                        'uom'               => $material['uom'] ?? null
            ];
        }
}
        }
        $productData['bom'] = !empty($bomJson) ? json_encode($bomJson, JSON_UNESCAPED_UNICODE) : null;

        // 1. Cập nhật thông tin sản phẩm chính
        $this->db->where('id_product', $productId);
        $this->db->update('product', $productData);
        
        // Các bước xóa và thêm batch vào bảng 'bom' riêng biệt sẽ không còn cần thiết.

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return false;
        } else {
            $this->db->trans_commit();
            
            // ===== REFRESH PROJECT ANALYSIS =====
            // Khi BOM thay đổi, cần cập nhật phân tích của tất cả đơn hàng liên quan
            try {
                $this->load->model('OrderModel');
                $refresh_result = $this->OrderModel->refreshProductWarnings($productId);
                // Log result for debugging (optional)
                log_message('info', 'BOM updated for product ' . $productId . ': ' . $refresh_result['message']);
            } catch (Exception $e) {
                // Don't fail the update if refresh fails, just log it
                log_message('error', 'Failed to refresh project warnings after BOM update: ' . $e->getMessage());
            }
            
            return true;
        }
    }

    /**
     * Xóa một sản phẩm.
     * Do có `ON DELETE CASCADE` trên bảng `bom`, các định mức liên quan sẽ tự động bị xóa.
     * 
     * @param string $productId ID của sản phẩm cần xóa.
     * @return array
     */
    public function deleteProduct($productId)
    {
        // Tái sử dụng hàm tìm tham chiếu để trả về chẩn đoán nhất quán
        $blocking = $this->findReferences($productId);
        if (!empty($blocking)) {
            $parts = [];
            foreach ($blocking as $t => $c) {
                $parts[] = $t . ' (' . $c . ')';
            }
            $message = 'Không thể xóa sản phẩm vì tồn tại tham chiếu trong: ' . implode(', ', $parts) . '. Hãy xóa hoặc cập nhật dữ liệu liên quan trước.';
            log_message('warning', 'Product delete blocked for id=' . $productId . ' references: ' . json_encode($blocking));
            return ['success' => false, 'message' => $message, 'refs' => $blocking];
        }

        // Also keep the backwards-compatible project usage check (additional safeguard)
        $usage_count = $this->db->where('id_product', $productId)->count_all_results('project');
        if ($usage_count > 0) {
            return ['success' => false, 'message' => 'Không thể xóa sản phẩm đang được sử dụng trong ' . $usage_count . ' đơn hàng.'];
        }

        $this->db->where('id_product', $productId);
        $this->db->delete('product');

        // Check for DB error details
        $db_error = $this->db->error(); // ['code' => ..., 'message' => ...]

        if ($db_error['code'] !== 0) {
            // Log the underlying DB error for debugging
            log_message('error', 'Product delete failed for id=' . $productId . ' DB error: ' . json_encode($db_error));
            return ['success' => false, 'message' => 'Lỗi database khi xóa sản phẩm: ' . ($db_error['message'] ?: 'Không xác định')];
        }

        // Ensure a row was actually deleted
        if ($this->db->affected_rows() > 0) {
            log_message('info', 'Product deleted successfully id=' . $productId);
            return ['success' => true, 'message' => 'Xóa sản phẩm thành công.'];
        }

        log_message('warning', 'Product delete affected 0 rows for id=' . $productId);
        return ['success' => false, 'message' => 'Không tìm thấy sản phẩm để xóa hoặc không thể xóa (ràng buộc khoá ngoại).'];
    }
}