<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * OrderModel - Xử lý nghiệp vụ đơn hàng bút bi
 *
 * @author  Production Management System v2
 * @date    2025-12-10
 * @logic
 *  - Không phụ thuộc trigger ở database; toàn bộ logic tồn kho xử lý trong ứng dụng.
 *  - Sử dụng database transactions để đảm bảo tính toàn vẹn dữ liệu (ACID).
 *  - Phương thức private `_updateInventoryAndWarnings` là nguồn chân lý duy nhất cho mọi thay đổi về tồn kho và cảnh báo.
 */
class OrderModel extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    // ========================================================================
    // PUBLIC GETTERS (Không thay đổi logic hiện tại)
    // ========================================================================

    public function getAllOrders($filters = [])
    {
        $this->db->select("p.*, c.cust_name, c.address, c.telp, c.email, pr.product_name, pr.application AS product_color, CASE WHEN p.pr_status = 1 THEN 'Đã duyệt' WHEN p.pr_status = 2 THEN 'Đang sản xuất' WHEN p.pr_status = 3 THEN 'Hoàn thành' ELSE 'Hủy' END AS status_text, CONCAT(p.diameter, 'mm') AS diameter_display", FALSE);
        $this->db->from('project p');
        $this->db->join('customer c', 'p.id_cust = c.id_cust');
        $this->db->join('product pr', 'p.id_product = pr.id_product');

        // Keyword search across project name, customer name, product name
        if (isset($filters['keyword']) && $filters['keyword'] !== '') {
            $this->db->group_start();
            $this->db->like('p.project_name', $filters['keyword']);
            $this->db->or_like('c.cust_name', $filters['keyword']);
            $this->db->or_like('pr.product_name', $filters['keyword']);
            $this->db->group_end();
        }

        if (isset($filters['product_id']) && $filters['product_id'] !== '') {
            $this->db->where('p.id_product', $filters['product_id']);
        }

        if (isset($filters['customer_id']) && $filters['customer_id'] !== '') {
            $this->db->where('p.id_cust', $filters['customer_id']);
        }

        if (isset($filters['status']) && $filters['status'] !== '') {
            $this->db->where('p.pr_status', (int)$filters['status']);
        }

        if (isset($filters['date_from']) && $filters['date_from'] !== '') {
            $this->db->where('p.entry_date >=', $filters['date_from']);
        }

        if (isset($filters['date_to']) && $filters['date_to'] !== '') {
            $this->db->where('p.entry_date <=', $filters['date_to']);
        }

        $this->db->order_by('p.created_at', 'DESC');
        $query = $this->db->get();
        return $query->result();
    }

    public function getOrderById($id_project)
    {
        $query = $this->db->query("
            SELECT
                p.*, c.cust_name, c.address, c.telp, c.email,
                pr.product_name, pr.application AS product_color,
                CASE
                    WHEN p.pr_status = 1 THEN 'Đã duyệt'
                    WHEN p.pr_status = 2 THEN 'Đang sản xuất'
                    WHEN p.pr_status = 3 THEN 'Hoàn thành'
                    ELSE 'Hủy'
                END AS status_text,
                CONCAT(p.diameter, 'mm') AS diameter_display
            FROM project p
            JOIN customer c ON p.id_cust = c.id_cust
            JOIN product pr ON p.id_product = pr.id_product
            WHERE p.id_project = ?
        ", [$id_project]);
        return $query->row();
    }
    
    public function getOrdersByCustomer($customer_id)
    {
        $query = $this->db->query("
            SELECT
                p.*, c.cust_name, c.address, c.telp, c.email,
                pr.product_name, pr.application AS product_color,
                CASE
                    WHEN p.pr_status = 1 THEN 'Đã duyệt'
                    WHEN p.pr_status = 2 THEN 'Đang sản xuất'
                    WHEN p.pr_status = 3 THEN 'Hoàn thành'
                    ELSE 'Hủy'
                END AS status_text,
                CONCAT(p.diameter, 'mm') AS diameter_display
            FROM project p
            JOIN customer c ON p.id_cust = c.id_cust
            JOIN product pr ON p.id_product = pr.id_product
            WHERE p.id_cust = ?
            ORDER BY p.created_at DESC
        ", [$customer_id]);
        return $query->result();
    }

    public function getOrdersByProduct($product_id)
    {
        $query = $this->db->query("
            SELECT
                p.*, c.cust_name, c.address, c.telp, c.email,
                pr.product_name, pr.application AS product_color,
                CASE
                    WHEN p.pr_status = 1 THEN 'Đã duyệt'
                    WHEN p.pr_status = 2 THEN 'Đang sản xuất'
                    WHEN p.pr_status = 3 THEN 'Hoàn thành'
                    ELSE 'Hủy'
                END AS status_text,
                CONCAT(p.diameter, 'mm') AS diameter_display
            FROM project p
            JOIN customer c ON p.id_cust = c.id_cust
            JOIN product pr ON p.id_product = pr.id_product
            WHERE p.id_product = ?
            ORDER BY p.created_at DESC
        ", [$product_id]);
        return $query->result();
    }
    
    public function getCustomers()
    {
        return $this->db->order_by('cust_name', 'ASC')->get('customer')->result();
    }

    public function getProducts()
    {
        return $this->db->order_by('product_name', 'ASC')->get('product')->result();
    }
    
    public function getDiameters()
    {
        // Should be dynamic from DB in the future, but static is fine for now.
        return ['0.5' => '0.5mm', '0.7' => '0.7mm', '1.0' => '1.0mm'];
    }

    // ========================================================================
    // DATA VALIDATION
    // ========================================================================
    
    public function validateOrderData($data)
    {
        if (empty($data['id_cust'])) return ['valid' => false, 'message' => 'Vui lòng chọn khách hàng'];
        if (empty($data['id_product'])) return ['valid' => false, 'message' => 'Vui lòng chọn sản phẩm'];
        if (empty($data['diameter'])) return ['valid' => false, 'message' => 'Vui lòng chọn đường kính'];
        if (empty($data['qty_request']) || !is_numeric($data['qty_request']) || $data['qty_request'] <= 0) return ['valid' => false, 'message' => 'Số lượng phải là một số lớn hơn 0'];
        if (empty($data['entry_date'])) return ['valid' => false, 'message' => 'Vui lòng nhập hạn giao'];
        if (strtotime($data['entry_date']) < strtotime(date('Y-m-d'))) return ['valid' => false, 'message' => 'Hạn giao phải từ hôm nay trở đi'];
        return ['valid' => true, 'message' => 'OK'];
    }

    // ========================================================================
    // CORE BUSINESS LOGIC: CRUD OPERATIONS (REFACTORED)
    // ========================================================================

    /**
     * Tạo đơn hàng mới.
     * Bọc trong transaction để đảm bảo tính toàn vẹn: tạo project và cập nhật kho.
     */
    public function createOrder($data)
    {
        $this->db->trans_start();

        // 1. Tạo project record
        $data['project_name'] = $this->generateProjectName($data['id_cust']);
        $data['created_at'] = date('Y-m-d H:i:s');
        $this->db->insert('project', $data);
        $id_project = $this->db->insert_id();

        // 2. Cập nhật tồn kho và cảnh báo
        $this->_updateInventoryAndWarnings($id_project);

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            return ['success' => false, 'message' => 'Lỗi khi tạo đơn hàng và cập nhật kho.', 'id_project' => null];
        }
        return ['success' => true, 'message' => 'Đơn hàng đã được tạo thành công!', 'id_project' => $id_project];
    }

    /**
     * Cập nhật một đơn hàng.
     * Logic an toàn: (1) Hoàn trả kho theo trạng thái CŨ, (2) Cập nhật đơn hàng, (3) Phân bổ lại kho theo trạng thái MỚI.
     */
    public function updateOrder($id_project, $data)
    {
        $this->db->trans_start();

        // 1. Hoàn trả lại kho dựa trên trạng thái CŨ của đơn hàng
        $this->_updateInventoryAndWarnings($id_project, true); // `true` for reversal

        // 2. Cập nhật thông tin đơn hàng

        $this->db->where('id_project', $id_project)->update('project', $data);

        // 3. Phân bổ lại kho và cập nhật cảnh báo theo thông tin MỚI
        $this->_updateInventoryAndWarnings($id_project);

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            return ['success' => false, 'message' => 'Lỗi khi cập nhật đơn hàng và kho.'];
        }
        return ['success' => true, 'message' => 'Đơn hàng đã được cập nhật thành công!'];
    }

    /**
     * Xóa một đơn hàng.
     * Logic: (1) Hoàn trả lại tất cả kho đã phân bổ cho đơn hàng này, (2) Xóa đơn hàng.
     */
    public function deleteOrder($id_project)
    {
        $this->db->trans_start();
        
        // 1. Hoàn trả lại kho trước khi xóa
        // Ensure the order exists before attempting reversal
        $order_to_reverse = $this->getOrderById($id_project);
        if (!$order_to_reverse) {
            // If order not found, rollback the transaction and return an error.
            // This prevents further operations on a non-existent order.
            $this->db->trans_rollback(); 
            return ['success' => false, 'message' => 'Đơn hàng không tồn tại hoặc đã bị xóa.'];
        }
        $this->_updateInventoryAndWarnings($id_project, true); // `true` for reversal

        // 2. Xóa project
        $this->db->delete('project', ['id_project' => $id_project]);
        
        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            $db_error = $this->db->error(); // Lấy chi tiết lỗi database
            $error_message = 'Lỗi database khi xóa đơn hàng. Code: ' . $db_error['code'] . ' Message: ' . $db_error['message'];
            log_message('error', $error_message); // Ghi log lỗi để debug phía server
            return ['success' => false, 'message' => $error_message];
        }
        return ['success' => true, 'message' => 'Đơn hàng đã được xóa và kho đã được hoàn trả.'];
    }

    // ========================================================================
    // CENTRALIZED INVENTORY & WARNING LOGIC (REFACTORED with stock_allocation)
    // ========================================================================

    /**
     * HÀM CỐT LÕI: Cập nhật tồn kho, phân bổ và cảnh báo cho một đơn hàng.
     * "Single source of truth" cho mọi thay đổi về kho, sử dụng `stock_allocation`.
     *
     * @param int  $id_project ID của project cần xử lý.
     * @param bool $is_reversal Cờ báo hiệu đây là hành động hoàn trả (true) hay phân bổ (false).
     */
    private function _updateInventoryAndWarnings($id_project, $is_reversal = false)
    {
        $order = $this->getOrderById($id_project);
        if (!$order) return; // Order đã bị xóa, không làm gì cả.

        if ($is_reversal) {
            // --- LOGIC HOÀN TRẢ ---
            // Dựa hoàn toàn vào `stock_allocation` đã lưu để đảo ngược nghiệp vụ một cách chính xác.
            $this->_reverseStockAllocation($order);
        } else {
            // --- LOGIC PHÂN BỔ (TẠO MỚI / CẬP NHẬT) ---
            // Nếu đơn hàng đã có warning_details từ checkCapacity(), tôn trọng nó
            if (!empty($order->warning_details) && $order->warning_details !== 'null') {
                // Đơn hàng đã có cảnh báo từ checkCapacity(), chỉ cần phân bổ kho
                if (in_array($order->pr_status, [1, 2])) {
                    $this->_applyStockAllocationOnly($order); // Hàm mới chỉ phân bổ kho, không tạo cảnh báo
                }
            } else {
                // Đơn hàng chưa có cảnh báo, tạo mới
                if (in_array($order->pr_status, [1, 2])) {
                    $this->_applyStockAllocation($order);
                } else {
                    // Các trạng thái khác không cần phân bổ, chỉ xóa cảnh báo.
                    $this->db->where('id_project', $id_project)->update('project', [
                        'warning_flag' => 0,
                        'warning_details' => json_encode(['status_info' => 'Trạng thái đơn hàng không yêu cầu phân bổ kho.']),
                        'capacity_level_used' => 0,
                        'stock_allocation' => NULL // Xóa phân bổ cũ nếu có
                    ]);
                }
            }
        }
    }

    /**
     * Áp dụng phân bổ kho cho một đơn hàng (trừ kho và lưu lại phân bổ).
     * SỬA LỖI: Sử dụng logic priority allocation giống checkCapacity()
     */
    private function _applyStockAllocation($order)
    {
        $qty_request = (int)$order->qty_request;

        // ===== TRANSACTION: ĐẢM BẢO ATOMICITY =====
        $this->db->trans_start();

        try {
            // ===== SỬA LỖI: SỬ DỤNG LOGIC PRIORITY ALLOCATION GIỐNG checkCapacity() =====

            // 1. Lấy tổng stock hiện tại
            $total_stock = $this->db->select('quantity_in_stock')
                                   ->where('id_product', $order->id_product)
                                   ->get('finished_stock')
                                   ->row()->quantity_in_stock ?? 0;

            // 2. Tính stock đã được cam kết cho production của các đơn priority cao hơn
            $allocated_query = $this->db->query("
                SELECT COALESCE(SUM(
                    CASE
                        WHEN p.qty_request <= p.finished_stock_available THEN 0  -- Đủ stock, không chiếm công suất
                        ELSE p.qty_request - p.finished_stock_available  -- Cần sản xuất, chiếm công suất
                    END
                ), 0) as allocated_production_capacity
                FROM project p
                WHERE p.id_product = ?
                  AND p.pr_status IN (1, 2)  -- Chờ duyệt, đã duyệt, đang sản xuất
                  AND p.id_project != ?
                  AND (
                      p.entry_date < ?
                      OR (p.entry_date = ? AND p.created_at < ?)
                  )
            ", [$order->id_product, $order->id_project, $order->entry_date, $order->entry_date, $order->created_at]);

            $allocated_production = $allocated_query->row()->allocated_production_capacity ?? 0;

            // 3. Stock available = tổng stock - stock đã cam kết cho production của các đơn priority cao hơn
            $available_stock = max(0, $total_stock - $allocated_production);

            // 4. Xác định phân bổ cho đơn hàng này
            $qty_from_stock = min($qty_request, $available_stock);
            $qty_for_production = $qty_request - $qty_from_stock;

            // ===== CẬP NHẬT DATABASE =====

            // --- BƯỚC 1: CẬP NHẬT KHO THÀNH PHẨM ---
            if ($qty_from_stock > 0) {
                $this->db->query(
                    "UPDATE `finished_stock` SET `quantity_in_stock` = `quantity_in_stock` - ? WHERE `id_product` = ?",
                    [$qty_from_stock, $order->id_product]
                );
            }

            // --- BƯỚC 2: CẬP NHẬT KHO NGUYÊN VẬT LIỆU ---
            if ($qty_for_production > 0) {
                $this->_updateMaterialStock($order->id_product, $qty_for_production, '-');
            }

            // --- BƯỚC 3: PHÂN TÍCH CẢNH BÁO ---
            $analysis = $this->_analyzeOrderFeasibility($order, $qty_from_stock, $qty_for_production);

            // --- BƯỚC 4: LƯU TRỮ KẾT QUẢ VÀ CẢNH BÁO ---
            $stock_allocation_data = [
                'from_stock' => $qty_from_stock,
                'for_production' => $qty_for_production,
                'total_allocated' => $qty_from_stock,
                'production_needed' => $qty_for_production
            ];

            $update_data = [
                'warning_flag'        => !empty($analysis['warnings']) ? 1 : 0,
                'warning_details'     => json_encode($analysis['warnings'], JSON_UNESCAPED_UNICODE),
                'capacity_level_used' => $analysis['capacity_level_used'],
                'finished_stock_available' => $available_stock - $qty_from_stock, // ← SỬA: Stock còn lại sau phân bổ
                'stock_allocation'    => json_encode($stock_allocation_data)
            ];

            $this->db->where('id_project', $order->id_project)->update('project', $update_data);

            // ===== COMMIT TRANSACTION =====
            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                throw new Exception('Transaction failed');
            }

        } catch (Exception $e) {
            // ===== ROLLBACK TRANSACTION =====
            $this->db->trans_rollback();
            log_message('error', 'Stock allocation failed for project ' . $order->id_project . ': ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Áp dụng phân bổ kho cho một đơn hàng (chỉ phân bổ kho, không tạo cảnh báo).
     * Dùng khi đơn hàng đã có warning_details từ checkCapacity()
     */
    private function _applyStockAllocationOnly($order)
    {
        $qty_request = (int)$order->qty_request;

        // ===== TRANSACTION: ĐẢM BẢO ATOMICITY =====
        $this->db->trans_start();

        try {
            // ===== SỬ DỤNG LOGIC PRIORITY ALLOCATION GIỐNG checkCapacity() =====

            // 1. Lấy tổng stock hiện tại
            $total_stock = $this->db->select('quantity_in_stock')
                                   ->where('id_product', $order->id_product)
                                   ->get('finished_stock')
                                   ->row()->quantity_in_stock ?? 0;

            // 2. Tính stock đã được cam kết cho production của các đơn priority cao hơn
            $allocated_query = $this->db->query("
                SELECT COALESCE(SUM(
                    CASE
                        WHEN p.qty_request <= p.finished_stock_available THEN 0  -- Đủ stock, không chiếm công suất
                        ELSE p.qty_request - p.finished_stock_available  -- Cần sản xuất, chiếm công suất
                    END
                ), 0) as allocated_production_capacity
                FROM project p
                WHERE p.id_product = ?
                  AND p.pr_status IN (1, 2)  -- Chờ duyệt, đã duyệt, đang sản xuất
                  AND p.id_project != ?
                  AND (
                      p.entry_date < ?
                      OR (p.entry_date = ? AND p.created_at < ?)
                  )
            ", [$order->id_product, $order->id_project, $order->entry_date, $order->entry_date, $order->created_at]);

            $allocated_production = $allocated_query->row()->allocated_production_capacity ?? 0;

            // 3. Stock available = tổng stock - stock đã cam kết cho production của các đơn priority cao hơn
            $available_stock = max(0, $total_stock - $allocated_production);

            // 4. Xác định phân bổ cho đơn hàng này
            $qty_from_stock = min($qty_request, $available_stock);
            $qty_for_production = $qty_request - $qty_from_stock;

            // ===== CẬP NHẬT DATABASE =====

            // --- BƯỚC 1: CẬP NHẬT KHO THÀNH PHẨM ---
            if ($qty_from_stock > 0) {
                $this->db->query(
                    "UPDATE `finished_stock` SET `quantity_in_stock` = `quantity_in_stock` - ? WHERE `id_product` = ?",
                    [$qty_from_stock, $order->id_product]
                );
            }

            // --- BƯỚC 2: CẬP NHẬT KHO NGUYÊN VẬT LIỆU ---
            if ($qty_for_production > 0) {
                $this->_updateMaterialStock($order->id_product, $qty_for_production, '-');
            }

            // --- BƯỚC 3: LƯU STOCK ALLOCATION (không cập nhật cảnh báo) ---
            $stock_allocation_data = [
                'from_stock' => $qty_from_stock,
                'for_production' => $qty_for_production,
                'total_allocated' => $qty_from_stock,
                'production_needed' => $qty_for_production
            ];

            $update_data = [
                'finished_stock_available' => $available_stock - $qty_from_stock, // ← SỬA: Stock còn lại sau phân bổ
                'stock_allocation' => json_encode($stock_allocation_data)
            ];

            $this->db->where('id_project', $order->id_project)->update('project', $update_data);

            // ===== COMMIT TRANSACTION =====
            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                throw new Exception('Transaction failed');
            }

        } catch (Exception $e) {
            // ===== ROLLBACK TRANSACTION =====
            $this->db->trans_rollback();
            log_message('error', 'Stock allocation failed for project ' . $order->id_project . ': ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Hoàn trả kho dựa trên `stock_allocation` đã lưu.
     */
    private function _reverseStockAllocation($order)
    {
        $allocation = json_decode($order->stock_allocation, true);

        // Nếu không có thông tin phân bổ, không có gì để hoàn trả.
        if (empty($allocation) || (empty($allocation['from_stock']) && empty($allocation['for_production']))) {
            return;
        }
        
        $qty_from_stock = (int)($allocation['from_stock'] ?? 0);
        $qty_for_production = (int)($allocation['for_production'] ?? 0);

        // --- BƯỚC 1: HOÀN TRẢ KHO THÀNH PHẨM ---
        if ($qty_from_stock > 0) {
            $this->db->query(
                "UPDATE `finished_stock` SET `quantity_in_stock` = `quantity_in_stock` + ? WHERE `id_product` = ?",
                [$qty_from_stock, $order->id_product]
            );
        }

        // --- BƯỚC 2: HOÀN TRẢ KHO NGUYÊN VẬT LIỆU ---
        if ($qty_for_production > 0) {
            $this->_updateMaterialStock($order->id_product, $qty_for_production, '+');
        }

        // --- BƯỚC 3: XÓA PHÂN BỔ SAU KHI HOÀN TRẢ ---
        // Quan trọng: Set `stock_allocation` về NULL để tránh hoàn trả hai lần.
        $this->db->where('id_project', $order->id_project)->update('project', ['stock_allocation' => NULL]);
    }

    /**
     * Helper: Cập nhật kho NVL cho một số lượng sản phẩm nhất định.
     * @param int    $id_product
     * @param int    $product_quantity Số lượng sản phẩm cần sản xuất/hoàn trả
     * @param string $operator '+' để hoàn trả, '-' để trừ kho
     */
    private function _updateMaterialStock($id_product, $product_quantity, $operator)
    {
        $product_info = $this->db->select('bom')->where('id_product', $id_product)->get('product')->row();
        $bom_materials = ($product_info && !empty($product_info->bom)) ? json_decode($product_info->bom, true) : [];

        if (empty($bom_materials)) return; // Không có BOM, không làm gì cả

        foreach ($bom_materials as $item) {
            $qty_per_unit = (float)$item['quantity_per_unit'];
            $total_material_change = $qty_per_unit * $product_quantity;

            if ($total_material_change > 0) {
                $this->db->query(
                    "UPDATE `material` SET `stock` = `stock` {$operator} ? WHERE `id_material` = ?",
                    [$total_material_change, $item['id_material']]
                );
            }
        }
    }

    /**
     * Phân tích tính khả thi của đơn hàng và tạo cảnh báo (không thay đổi dữ liệu).
     */
    private function _analyzeOrderFeasibility($order, $qty_from_stock, $qty_for_production)
    {
        $warnings = [];
        $qty_request = (int)$order->qty_request;

        // --- PHÂN TÍCH TỒN KHO & SẢN XUẤT ---
        if ($qty_for_production == 0) {
            $warnings['stock_status'] = "✔️ Sẵn hàng, có thể giao ngay " . number_format($qty_request) . " sản phẩm từ tồn kho.";
        } else {
            if ($qty_from_stock > 0) {
                $warnings['stock_status'] = "⚠️ Lấy " . number_format($qty_from_stock) . " từ kho, cần sản xuất thêm " . number_format($qty_for_production) . " sản phẩm.";
            } else {
                $warnings['stock_status'] = "🏭 Cần sản xuất " . number_format($qty_for_production) . " sản phẩm.";
            }
        }

        // --- PHÂN TÍCH NGUYÊN VẬT LIỆU (CHO LƯỢNG CẦN SẢN XUẤT) ---
        if ($qty_for_production > 0) {
            $product_info = $this->db->select('bom')->where('id_product', $order->id_product)->get('product')->row();
            $bom_materials = ($product_info && !empty($product_info->bom)) ? json_decode($product_info->bom, true) : [];
            
            if (!empty($bom_materials)) {
                $material_warnings = [];
                // Lấy thông tin stock của tất cả NVL cần thiết trong 1 query
                $material_ids = array_column($bom_materials, 'id_material');
                $materials_in_db = $this->db->where_in('id_material', $material_ids)->get('material')->result_array();
                $material_stock_map = array_column($materials_in_db, 'stock', 'id_material');
                $material_name_map = array_column($materials_in_db, 'material_name', 'id_material');

                foreach ($bom_materials as $item) {
                    $stock_available = $material_stock_map[$item['id_material']] ?? 0;
                    $needed = (float)$item['quantity_per_unit'] * $qty_for_production;
                    if ($stock_available < $needed) {
                        $material_warnings[] = "Thiếu " . number_format($needed - $stock_available) . " đơn vị " . ($material_name_map[$item['id_material']] ?? "NVL ID: {$item['id_material']}");
                    }
                }
                if (!empty($material_warnings)) {
                    $warnings['material_shortage'] = "Thiếu NVL: " . implode(', ', $material_warnings);
                }
            } else {
                $warnings['material_error'] = "❌ Sản phẩm chưa có Định Mức (BOM). Không thể kiểm tra và sản xuất.";
            }
        }
        
        return [
            'warnings' => $warnings,
            'capacity_level_used' => $qty_for_production > 0 ? 1 : 0 // Giả định dùng 1 line nếu có sản xuất
        ];
    }

    /**
     * Phân tích tính khả thi của đơn hàng và tạo cảnh báo (UC7)
     * @param int $id_product
     * @param int $qty_request
     * @param string $entry_date
     * @param int $id_project (optional, for updates)
     * @return array
    /**
     * Check capacity and materials for order creation (Updated for 9 warning scenarios)
     * Always approve if Level 1 or Level 2 capacity is sufficient
     * Only reject if both levels are insufficient
     */
    public function checkCapacity($id_product, $qty_request, $entry_date, $id_project = null)
    {
        // ===== 1. LẤY THÔNG TIN CƠ BẢN =====
        $product = $this->db->select('product_name, bom, diameter')->where('id_product', $id_product)->get('product')->row();
        if (!$product) {
            return ['feasible' => false, 'message' => 'Sản phẩm không tồn tại'];
        }

        $capacity_config = $this->db->get('capacity_config')->result_array();
        $level1 = array_filter($capacity_config, fn($c) => $c['level'] == 1)[0] ?? ['hours_per_shift' => 8, 'shifts_per_day' => 2, 'efficiency_rate' => 0.8];
        $level2 = array_filter($capacity_config, fn($c) => $c['level'] == 2)[0] ?? ['hours_per_shift' => 12, 'shifts_per_day' => 2, 'efficiency_rate' => 0.85];

        $products_per_shift_l1 = floor(500 * $level1['hours_per_shift'] * $level1['efficiency_rate']);
        $products_per_shift_l2 = floor(500 * $level2['hours_per_shift'] * $level2['efficiency_rate']);

        // ===== 2. TÍNH SỐ NGÀY CÒN LẠI =====
        $days_remaining = (strtotime($entry_date) - strtotime(date('Y-m-d'))) / 86400;
        $deadline_passed = $days_remaining < 0;
        $deadline_too_close = $days_remaining < 1; // Quá gấp nếu < 1 ngày

        // ===== 3. TÍNH STOCK ALLOCATION =====
        $total_stock = $this->db->select('quantity_in_stock')->where('id_product', $id_product)->get('finished_stock')->row()->quantity_in_stock ?? 0;

        $allocated_query = $this->db->query("
            SELECT COALESCE(SUM(
                CASE
                    WHEN p.qty_request <= p.finished_stock_available THEN 0
                ELSE p.qty_request - p.finished_stock_available
                END
            ), 0) as allocated_production_capacity
            FROM project p
            WHERE p.id_product = ?
              AND p.pr_status IN (1, 2)
              AND p.id_project != ?
              AND p.entry_date < ?
        ", [$id_product, $id_project, $entry_date]);

        $allocated_production = $allocated_query->row()->allocated_production_capacity ?? 0;
        $available_stock = max(0, $total_stock - $allocated_production);

        // ===== 4. CHECK DEADLINE FIRST =====
        // Xử lý 'gần hạn' như cảnh báo khẩn cấp (mặc định không chặn tạo đơn)
        if ($deadline_too_close) {
            $deadline_urgent = true;
            // We'll add the warning into the $warnings array later so it appears in analysis
        } else {
            $deadline_urgent = false;
        }

        // ===== 5. CHECK STOCK SUFFICIENCY =====
        if ($qty_request <= $available_stock) {
            // Scenario 1: Có sẵn kho (mang tính thông tin)
            return [
                'feasible' => true,
                'warning_type' => 'stock_available',
                // Thông tin: không đặt warning_flag để controller hiển thị thông báo thành công
                'warning_flag' => 0,
                'warning_details' => json_encode([
                    'finished_stock_info' => "✓ Có {$total_stock} cái trong kho, đã phân bổ {$allocated_production}, còn {$available_stock} cái khả dụng, dùng {$qty_request} cho đơn này",
                    'stock_status' => 'sufficient',
                    'capacity_info' => ['level' => 0, 'products_per_shift_level1' => $products_per_shift_l1, 'products_per_shift_level2' => $products_per_shift_l2]
                ]),
                'capacity_level_used' => 0,
                'finished_stock_available' => $available_stock,
                'nvl_sufficient_shifts' => null,
                'message' => 'Có sẵn kho'
            ];
        }

        $qty_to_produce = $qty_request - $available_stock;

        // ===== 6. CHECK CAPACITY =====
        $total_capacity_l1 = $products_per_shift_l1 * $level1['shifts_per_day'] * max(1, $days_remaining);
        $total_capacity_l2 = $products_per_shift_l2 * $level2['shifts_per_day'] * max(1, $days_remaining);

        $capacity_level_used = 1;
        $warning_type = 'normal';

        if ($qty_to_produce <= $total_capacity_l1) {
            $capacity_level_used = 1;
            $warning_type = ($available_stock > 0) ? 'normal' : 'ok';
        } elseif ($qty_to_produce <= $total_capacity_l2) {
            $capacity_level_used = 2;
            $warning_type = 'level_2_required';
        } else {
            // Scenario 8: Vượt công suất tối đa
            return [
                'feasible' => false,
                'warning_type' => 'capacity_exceeded',
                'message' => 'Vượt công suất tối đa, không thể sản xuất kịp'
            ];
        }

        // ===== 6. CHECK MATERIALS =====
        $bom = json_decode($product->bom, true) ?? [];
        $material_warnings = [];
        $missing_materials = [];
        $bottleneck_material = '';
        $min_products_possible = PHP_INT_MAX;
        $material_details = []; // Thêm array để lưu chi tiết NVL

        foreach ($bom as $item) {
            if (empty($item['id_material'])) {
                $missing_materials[] = $item['material_name'] ?? 'NVL không xác định';
                continue;
            }

            $material = $this->db->select('material_name, stock, uom')
                                ->where('id_material', $item['id_material'])
                                ->get('material')->row();

            if (!$material) continue;

            $qty_needed = $item['quantity_per_unit'] * $qty_to_produce;
            $products_possible = floor($material->stock / $item['quantity_per_unit']);

            // Tìm bottleneck material (material có thể sản xuất ít sản phẩm nhất)
            if ($products_possible < $min_products_possible) {
                $min_products_possible = $products_possible;
                $bottleneck_material = $material->material_name;
            }

            // Tạo chi tiết NVL cho UI
            $material_detail = [
                'material_name' => $material->material_name,
                'stock' => $material->stock,
                'uom' => $material->uom,
                'quantity_per_unit' => $item['quantity_per_unit'],
                'quantity_needed' => $qty_needed,
                'products_possible' => $products_possible,
                'shifts_possible' => ceil($products_possible / $products_per_shift_l1),
                'is_bottleneck' => false,
                'is_sufficient' => $material->stock >= $qty_needed
            ];

            if ($material->stock < $qty_needed) {
                $shortage = $qty_needed - $material->stock;
                $material_detail['quantity_shortage'] = $shortage;
                $material_warnings[] = "Thiếu {$shortage} {$material->uom} {$material->material_name}";
            }

            $material_details[] = $material_detail;
        }

        // Đánh dấu bottleneck material
        if ($bottleneck_material && !empty($material_details)) {
            foreach ($material_details as &$mat) {
                if ($mat['material_name'] === $bottleneck_material) {
                    $mat['is_bottleneck'] = true;
                    break;
                }
            }
        }

        // Tính số ca có thể làm dựa trên bottleneck material
        $material_shifts_available = $min_products_possible == PHP_INT_MAX ? 0 : ceil($min_products_possible / $products_per_shift_l1);

        // Tính số ca cần thiết cho đơn hàng này
        $shifts_needed = ceil($qty_to_produce / $products_per_shift_l1);

        // ===== 7. TỔNG HỢP WARNINGS =====
        $warnings = [];

        if ($available_stock > 0) {
            $warnings['finished_stock_info'] = "✓ Có {$available_stock} cái trong kho, cần sản xuất thêm {$qty_to_produce} cái";
            $warnings['stock_status'] = 'partial';
        } else {
            $warnings['finished_stock_info'] = "🏭 Cần sản xuất {$qty_to_produce} cái";
            $warnings['stock_status'] = 'depleted';
        }

        if ($capacity_warning) {
            $warnings['capacity_warning'] = $capacity_warning;
        }

        // Thêm chi tiết NVL vào warnings
        if (!empty($material_details)) {
            $warnings['material_details'] = $material_details;
        }

        // CẢNH BÁO NVL - HIỂN THỊ CHI TIẾT SỐ SẢN PHẨM CÓ THỂ TẠO
        if (!empty($material_warnings)) {
            // Đặt loại cảnh báo để badge giao diện khớp
            $warning_type = 'material_shortage';
            $warnings['material_warning'] = "⚠️ " . implode(', ', $material_warnings);
            if ($material_shifts_available < $shifts_needed) {
                if ($material_shifts_available == 0) {
                    $warnings['material_status'] = "❌ NVL không đủ để sản xuất (thiếu hoàn toàn)";
                } else {
                    $max_products_possible = $material_shifts_available * $products_per_shift_l1;
                    $warnings['material_status'] = "⚠️ NVL chỉ đủ sản xuất {$max_products_possible} sản phẩm (~{$material_shifts_available} ca)";
                }
            } else {
                $warnings['material_status'] = "✅ NVL đủ cho {$qty_to_produce} sản phẩm (~{$shifts_needed} ca)";
            }
        } elseif ($material_shifts_available > 0 && $material_shifts_available < $shifts_needed) {
            // Trường hợp có bottleneck nhưng không có shortage cụ thể
            $max_products_possible = $material_shifts_available * $products_per_shift_l1;
            $warning_type = 'material_shortage';
            $warnings['material_warning'] = "⚠️ NVL bottleneck: chỉ đủ sản xuất {$max_products_possible} sản phẩm";
            $warnings['material_status'] = "⚠️ NVL chỉ đủ sản xuất {$max_products_possible} sản phẩm (~{$material_shifts_available} ca)";
        } elseif ($material_shifts_available == 0 && !empty($bom_materials)) {
            // Không có NVL nào tồn tại
            $warning_type = 'new_material_shortage';
            $warnings['material_warning'] = "❌ Không có NVL nào tồn tại trong kho";
            $warnings['material_status'] = "❌ NVL không đủ để sản xuất (thiếu hoàn toàn)";
        } else {
            // NVL đủ
            $warnings['material_status'] = "✅ NVL đủ cho {$qty_to_produce} sản phẩm (~{$shifts_needed} ca)";
        }

        if (!empty($missing_materials)) {
            $warnings['missing_materials_warning'] = "Sản phẩm có " . count($missing_materials) . " NVL chưa tồn tại trong kho";
            $warnings['missing_materials_list'] = implode(', ', $missing_materials);
        }

        if ($bottleneck_material) {
            $warnings['bottleneck_material'] = $bottleneck_material;
        }

        if ($deadline_passed) {
            // Deadline already passed: blocking condition
            return [
                'feasible' => false,
                'warning_type' => 'deadline_overdue',
                'message' => "⏰ Deadline đã quá hạn {$days_overdue} ngày",
            ];
        } elseif ($days_remaining < 3) {
            $warnings['deadline_warning'] = "⏰ Còn {$days_remaining} ngày đến deadline, cần ưu tiên";
            $warnings['deadline_details'] = "Còn {$days_remaining} ngày, cần {$qty_to_produce} sản phẩm";
            // Use standardized warning_type so UI badge/text matches toast
            $warning_type = 'deadline_too_close';
        }

        // Thêm thông tin capacity
        $warnings['capacity_info'] = [
            'level' => $capacity_level_used,
            'products_per_shift_level1' => $products_per_shift_l1,
            'products_per_shift_level2' => $products_per_shift_l2
        ];

        return [
            'feasible' => true,
            'warning_flag' => !empty($warnings) ? 1 : 0,
            'warning_type' => $warning_type,
            'warning_details' => json_encode($warnings),
            'capacity_level_used' => $capacity_level_used,
            'finished_stock_available' => $available_stock,
            'material_shifts_available' => $material_shifts_available,
            'stock_allocation' => json_encode([
                'from_stock' => $available_stock,
                'for_production' => $qty_to_produce,
                'total_allocated' => min($qty_request, $available_stock),
                'production_needed' => $qty_to_produce
            ]),
            'message' => $this->formatCapacityMessage($warnings, $capacity_level_used)
        ];
    }

    /**
     * Format message for capacity check result
     */
    private function formatCapacityMessage($warnings, $capacity_level_used)
    {
        if (isset($warnings['finished_stock_info']) && strpos($warnings['finished_stock_info'], '✓ Có') === 0) {
            return "Có đủ hàng tồn kho";
        }

        $messages = [];
        if ($capacity_level_used == 2) {
            $messages[] = "Cần tăng ca (Level 2)";
        }
        if (isset($warnings['material_warning'])) {
            $messages[] = "Thiếu nguyên vật liệu";
        }
        if (isset($warnings['deadline_warning'])) {
            $messages[] = "Gần deadline";
        }

        return !empty($messages) ? implode(', ', $messages) : "Đơn hàng khả thi";
    }

    /**
     * Tạo tên Project tự động
     */
    private function generateProjectName($id_cust)
    {
        $date_part = date('Ymd');
        $this->db->where('id_cust', $id_cust);
        $this->db->where('DATE(created_at)', date('Y-m-d'));
        $count = $this->db->count_all_results('project');
        $seq = str_pad($count + 1, 3, '0', STR_PAD_LEFT);
        return "ORD-{$id_cust}-{$date_part}-{$seq}";
    }

    /**
     * Refresh warning analysis for a project
     * Used when BOM changes or manual refresh is needed
     */
    public function refreshProjectWarnings($id_project)
    {
        try {
            $project = $this->db->where('id_project', $id_project)->get('project')->row();
            if (!$project) {
                return ['success' => false, 'message' => 'Đơn hàng không tồn tại'];
            }

            // Chạy lại kiểm tra năng lực với dữ liệu hiện tại
            $capacity_check = $this->checkCapacity(
                $project->id_product,
                $project->qty_request,
                $project->entry_date,
                $id_project
            );

            // Cập nhật project với kết quả phân tích mới (dùng giá trị mặc định an toàn để tránh undefined index)
            $update_data = [
                'warning_details' => $capacity_check['warning_details'] ?? null,
                'warning_flag' => $capacity_check['warning_flag'] ?? 0,
                'warning_type' => $capacity_check['warning_type'] ?? null,
                'capacity_level_used' => $capacity_check['capacity_level_used'] ?? null,
                'finished_stock_available' => $capacity_check['finished_stock_available'] ?? 0,
                'material_shifts_available' => $capacity_check['material_shifts_available'] ?? null,
                'stock_allocation' => $capacity_check['stock_allocation'] ?? null
            ];

            $this->db->where('id_project', $id_project)->update('project', $update_data);

            return [
                'success' => true,
                'message' => 'Đã cập nhật phân tích kỹ thuật',
                'new_analysis' => $capacity_check
            ];

        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Lỗi cập nhật: ' . $e->getMessage()];
        }
    }

    /**
     * Refresh warnings for all projects of a product
     * Called when product BOM is updated
     */
    public function refreshProductWarnings($id_product)
    {
        try {
            $projects = $this->db->where('id_product', $id_product)
                                ->where_in('pr_status', [0, 1, 2]) // Chỉ các project đang active
                                ->get('project')->result();

            $updated_count = 0;
            foreach ($projects as $project) {
                $result = $this->refreshProjectWarnings($project->id_project);
                if ($result['success']) {
                    $updated_count++;
                }
            }

            return [
                'success' => true,
                'message' => "Đã cập nhật phân tích cho {$updated_count} đơn hàng",
                'updated_count' => $updated_count
            ];

        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Lỗi cập nhật hàng loạt: ' . $e->getMessage()];
        }
    }

    /**
     * Refresh warnings for all projects (optionally filtered by product)
     * @param int|null $id_product
     * @return array ['success' => bool, 'message' => string, 'success' => int, 'total' => int]
     */
    public function refreshAllWarnings($id_product = null)
    {
        try {
            $this->db->where_in('pr_status', [0,1,2]); // Chỉ các project đang active
            if ($id_product !== null) {
                $this->db->where('id_product', $id_product);
            }
            $projects = $this->db->get('project')->result();

            $total = count($projects);
            $success_count = 0;
            foreach ($projects as $p) {
                $res = $this->refreshProjectWarnings($p->id_project);
                if (isset($res['success']) && $res['success'] === true) $success_count++;
            }

            return [
                'ok' => true,
                'message' => "Đã làm mới cảnh báo cho {$success_count}/{$total} đơn hàng",
                'success' => $success_count,
                'total' => $total
            ];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Lỗi khi làm mới cảnh báo: ' . $e->getMessage()];
        }
    }
}
