<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * ProductModel - Xử lý nghiệp vụ quản lý sản phẩm bút bi
 * 
 * Use Case: UC2 - Quản lý Sản phẩm
 * Actor: Ban Giám Đốc (BOD)
 * 
 * Pattern: Follow OrderModel structure for consistency
 * Database Schema: 
 *   - id_product: INT(25) AI
 *   - product_name: VARCHAR(50)
 *   - summary: LONGTEXT (thông tin chi tiết)
 *   - application: VARCHAR(100) (màu mực: Xanh, Đen, Đỏ, Nhiều màu)
 *   - diameter: DECIMAL(3,1) DEFAULT 0.5 (0.5, 0.7, 1.0 mm)
 *   - bom: JSON (định mức nguyên vật liệu)
 *   - is_active, created_at, updated_at, created_by
 * 
 * @author  Production Management System v2
 * @date    2025-11-24
 */
class ProductModel extends CI_Model
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Lấy tất cả sản phẩm với thống kê đơn hàng
     * Pattern giống OrderModel.getAllOrders()
     * 
     * @param bool $active_only Chỉ lấy sản phẩm đang sản xuất
     * @return array
     */
    public function getAllProducts($active_only = false)
    {
        $where_clause = $active_only ? 'WHERE p.is_active = 1' : '';
        
        $query = $this->db->query("
            SELECT 
                p.*,
                COUNT(DISTINCT pr.id_project) AS total_orders,
                COALESCE(SUM(pr.qty_request), 0) AS total_quantity,
                MAX(pr.created_at) AS last_order_date,
                CASE 
                    WHEN p.is_active = 1 THEN 'Đang sản xuất'
                    ELSE 'Ngừng sản xuất'
                END AS status_text,
                CONCAT(p.diameter, 'mm') AS diameter_display
            FROM product p
            LEFT JOIN project pr ON p.id_product = pr.id_product
            {$where_clause}
            GROUP BY p.id_product
            ORDER BY p.id_product ASC
        ");
        
        return $query->result();
    }

    /**
     * Lấy thông tin sản phẩm theo ID
     * Kèm theo BOM (JSON decoded) và thống kê
     * 
     * @param int $id_product
     * @return object|null
     */
    public function getProductById($id_product)
    {
        $query = $this->db->query("
            SELECT 
                p.*,
                COUNT(DISTINCT pr.id_project) AS total_orders,
                IFNULL(SUM(pr.qty_request), 0) AS total_quantity,
                SUM(IF(pr.pr_status = 3, 1, 0)) AS completed_orders,
                SUM(IF(pr.pr_status IN (1,2), 1, 0)) AS active_orders,
                MAX(pr.created_at) AS last_order_date,
                CONCAT(p.diameter, 'mm') AS diameter_display,
                u.username AS created_by_username
            FROM product p
            LEFT JOIN project pr ON p.id_product = pr.id_product
            LEFT JOIN user u ON p.created_by = u.user_id
            WHERE p.id_product = ?
            GROUP BY p.id_product
        ", [$id_product]);

        $product = $query->row();
        
        // Decode BOM JSON thành array để dễ xử lý
        if ($product) {
            if (!empty($product->bom)) {
                $bom_decoded = json_decode($product->bom, true);
                // Support both formats: [{}, {}] and ['materials' => [{}, {}]]
                if (isset($bom_decoded['materials'])) {
                    $product->bom_data = $bom_decoded;
                } else {
                    // Wrap in 'materials' key for consistency
                    $product->bom_data = ['materials' => $bom_decoded];
                }
            } else {
                $product->bom_data = ['materials' => []];
            }
        }
        
        return $product;
    }

    /**
     * Tạo ID sản phẩm tự động
     * Pattern giống CustomerModel.generateCustomerId()
     * Format: AUTO INCREMENT từ 1001
     * 
     * @return int
     */
    public function generateProductId()
    {
        $query = $this->db->query("
            SELECT COALESCE(MAX(id_product), 1000) + 1 AS next_id
            FROM product
        ");
        
        return (int) $query->row()->next_id;
    }

    /**
     * Tạo sản phẩm mới (với BOM)
     * Pattern giống OrderModel.createOrder() với transaction
     * 
     * @param array $product_data
     * @return array ['success' => bool, 'message' => string, 'id_product' => int|null]
     */
    public function addProduct($product_data)
    {
        $this->db->trans_start();

        try {
            // Validate trước khi insert
            $validation = $this->validateProductData($product_data);
            if (!$validation['valid']) {
                throw new Exception($validation['message']);
            }

            // Validate BOM nếu có
            if (!empty($product_data['bom'])) {
                $bom_validation = $this->validateBOM($product_data['bom']);
                if (!$bom_validation['valid']) {
                    throw new Exception($bom_validation['message']);
                }
                // Encode BOM thành JSON string
                $product_data['bom'] = json_encode($product_data['bom'], JSON_UNESCAPED_UNICODE);
            }

            // Generate ID tự động
            $product_data['id_product'] = $this->generateProductId();
            $product_data['is_active'] = 1;
            $product_data['created_by'] = $this->session->userdata('user_id');

            // Insert vào database
            $this->db->insert('product', $product_data);
            
            $insert_id = $product_data['id_product'];

            // Log activity
            $this->logActivity('create', $insert_id, null, $product_data);

            // Commit transaction
            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                throw new Exception('Lỗi khi lưu vào cơ sở dữ liệu');
            }

            return [
                'success' => true,
                'message' => 'Sản phẩm đã được tạo thành công',
                'id_product' => $insert_id
            ];

        } catch (Exception $e) {
            $this->db->trans_rollback();
            
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'id_product' => null
            ];
        }
    }

    /**
     * Cập nhật thông tin sản phẩm (kể cả BOM)
     * Pattern giống OrderModel.updateOrder()
     * 
     * @param int $id_product
     * @param array $update_data
     * @return array ['success' => bool, 'message' => string]
     */
    public function updateProduct($id_product, $update_data)
    {
        $this->db->trans_start();

        try {
            // Validate
            $validation = $this->validateProductData($update_data, $id_product);
            if (!$validation['valid']) {
                throw new Exception($validation['message']);
            }

            // Validate & encode BOM nếu có
            if (isset($update_data['bom'])) {
                if (!empty($update_data['bom'])) {
                    $bom_validation = $this->validateBOM($update_data['bom']);
                    if (!$bom_validation['valid']) {
                        throw new Exception($bom_validation['message']);
                    }
                    $update_data['bom'] = json_encode($update_data['bom'], JSON_UNESCAPED_UNICODE);
                } else {
                    $update_data['bom'] = null;
                }
            }

            // Lấy dữ liệu cũ để log
            $old_data = $this->getProductById($id_product);
            
            // Update (updated_at tự động)
            $this->db->where('id_product', $id_product);
            $this->db->update('product', $update_data);

            // Log activity
            $this->logActivity('update', $id_product, $old_data, $update_data);

            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                throw new Exception('Lỗi khi cập nhật dữ liệu');
            }

            return [
                'success' => true,
                'message' => 'Thông tin sản phẩm đã được cập nhật'
            ];

        } catch (Exception $e) {
            $this->db->trans_rollback();
            
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Xóa sản phẩm
     * Quy tắc: Không xóa được nếu đã có đơn hàng (FK constraint)
     * Pattern giống OrderModel.deleteOrder()
     * 
     * @param int $id_product
     * @return array ['success' => bool, 'message' => string]
     */
    public function deleteProduct($id_product)
    {
        $this->db->trans_start();

        try {
            // Kiểm tra FK constraint với project table
            if ($this->hasOrders($id_product)) {
                throw new Exception(
                    'Không thể xóa sản phẩm đã có đơn hàng. ' .
                    'Vui lòng xóa đơn hàng trước hoặc đánh dấu sản phẩm là không hoạt động.'
                );
            }

            // Lấy data để log
            $old_data = $this->getProductById($id_product);

            // Xóa sản phẩm
            $this->db->delete('product', ['id_product' => $id_product]);

            // Log activity
            $this->logActivity('delete', $id_product, $old_data, null);

            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                throw new Exception('Lỗi khi xóa sản phẩm');
            }

            return [
                'success' => true,
                'message' => 'Sản phẩm đã được xóa thành công'
            ];

        } catch (Exception $e) {
            $this->db->trans_rollback();
            
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Kiểm tra sản phẩm có đơn hàng không
     * FK constraint check - Pattern giống OrderModel check planning
     * 
     * @param int $id_product
     * @return bool
     */
    public function hasOrders($id_product)
    {
        $query = $this->db->get_where('project', ['id_product' => $id_product]);
        return $query->num_rows() > 0;
    }

    /**
     * Validate dữ liệu sản phẩm
     * Pattern giống OrderModel.validateOrderData()
     * 
     * Database constraints:
     *   - product_name: VARCHAR(50) - Tối đa 50 ký tự
     *   - summary: LONGTEXT
     *   - application: VARCHAR(100) - Màu mực
     *   - diameter: DECIMAL(3,1) - 0.5, 0.7, 1.0
     * 
     * @param array $data
     * @param int|null $id_product (Exclude khi check duplicate)
     * @return array ['valid' => bool, 'message' => string]
     */
    public function validateProductData($data, $id_product = null)
    {
        // 1. Tên sản phẩm
        if (empty($data['product_name'])) {
            return [
                'valid' => false,
                'message' => 'Vui lòng nhập tên sản phẩm'
            ];
        }
        
        // Kiểm tra ký tự hợp lệ: chữ, số, khoảng trắng và ký tự (.,- )
        if (!preg_match('/^[\p{L}0-9\s.,-]+$/u', $data['product_name'])) {
            return [
                'valid' => false,
                'message' => 'Tên sản phẩm chỉ chứa chữ cái, số và ký tự (.,-)'
            ];
        }
        
        if (strlen($data['product_name']) > 50) {
            return [
                'valid' => false,
                'message' => 'Tên sản phẩm tối đa 50 ký tự'
            ];
        }

        // 2. Đường kính (phải là số dương trong khoảng hợp lý)
        if (isset($data['diameter'])) {
            $diameter = floatval($data['diameter']);
            if ($diameter <= 0 || $diameter > 10) {
                return [
                    'valid' => false,
                    'message' => 'Đường kính phải là số dương từ 0.01mm đến 10mm'
                ];
            }
        }

        // 3. Màu mực/Application
        if (isset($data['application']) && strlen($data['application']) > 100) {
            return [
                'valid' => false,
                'message' => 'Màu mực tối đa 100 ký tự'
            ];
        }

        // Tất cả validation pass
        return [
            'valid' => true,
            'message' => 'OK'
        ];
    }

    /**
     * Validate BOM (Bill of Materials) structure
     * BOM format: {materials: [{id_material, material_name, quantity, unit}]}
     * 
     * @param array $bom
     * @return array ['valid' => bool, 'message' => string]
     */
    public function validateBOM($bom)
    {
        // BOM phải là array hoặc JSON string
        if (is_string($bom)) {
            $bom = json_decode($bom, true);
        }
        
        if (!is_array($bom)) {
            return [
                'valid' => false,
                'message' => 'BOM không đúng định dạng (phải là array)'
            ];
        }

        // Support cả 2 formats:
        // Format 1 (old): ['materials' => [{...}, {...}]]
        // Format 2 (new): [{...}, {...}]
        $materials = isset($bom['materials']) ? $bom['materials'] : $bom;
        
        if (!is_array($materials)) {
            return [
                'valid' => false,
                'message' => 'BOM materials phải là mảng'
            ];
        }

        // Validate từng material
        foreach ($materials as $index => $material) {
            // Cho phép material mới (không có id_material)
            if (!empty($material['id_material'])) {
                // Kiểm tra material tồn tại trong database (chỉ khi có ID)
                $exists = $this->db->get_where('material', [
                    'id_material' => $material['id_material']
                ]);
                
                if ($exists->num_rows() == 0) {
                    return [
                        'valid' => false,
                        'message' => "Material ID {$material['id_material']} không tồn tại"
                    ];
                }
            } else {
                // Material mới - phải có material_name
                if (empty($material['material_name'])) {
                    return [
                        'valid' => false,
                        'message' => "Material #{$index}: Phải có tên nguyên liệu"
                    ];
                }
            }

            // Kiểm tra quantity
            if (!isset($material['quantity']) || $material['quantity'] <= 0) {
                return [
                    'valid' => false,
                    'message' => "Material #{$index}: Số lượng phải lớn hơn 0"
                ];
            }

            // Kiểm tra unit
            if (empty($material['unit'])) {
                return [
                    'valid' => false,
                    'message' => "Material #{$index}: Thiếu đơn vị (unit)"
                ];
            }
        }

        return [
            'valid' => true,
            'message' => 'OK'
        ];
    }

    /**
     * Lấy danh sách nguyên vật liệu (cho BOM builder)
     * Dùng trong form tạo/sửa sản phẩm - Cache trong session
     * 
     * @param bool $refresh Force refresh cache
     * @return array
     */
    public function getMaterialsList($refresh = false)
    {
        // Cache trong session trong 5 phút
        $cache_key = 'materials_list_cache';
        $cache_time_key = 'materials_list_cache_time';
        $cache_duration = 300; // 5 phút
        
        if (!$refresh) {
            $cached_data = $this->session->userdata($cache_key);
            $cached_time = $this->session->userdata($cache_time_key);
            
            if ($cached_data && $cached_time && (time() - $cached_time < $cache_duration)) {
                return $cached_data;
            }
        }
        
        $query = $this->db->query("
            SELECT 
                id_material,
                material_name,
                stock,
                'g' AS unit,
                CONCAT(material_name, ' (Tồn: ', stock, 'g)') AS material_display
            FROM material
            WHERE stock > 0
            ORDER BY material_name ASC
        ");
        
        $materials = $query->result();
        
        // Lưu vào session cache
        $this->session->set_userdata($cache_key, $materials);
        $this->session->set_userdata($cache_time_key, time());

        return $materials;
    }

    /**
     * Ghi log hoạt động vào audit_log
     * Pattern giống OrderModel và CustomerModel
     * 
     * @param string $action (create, update, delete)
     * @param int $record_id
     * @param object|array|null $old_value
     * @param array|null $new_value
     * @return void
     */
    private function logActivity($action, $record_id, $old_value, $new_value)
    {
        $user_id = $this->session->userdata('user_id');
        $username = $this->session->userdata('username');
        
        $log_data = [
            'user_id'    => $user_id,
            'username'   => $username,
            'action'     => $action,
            'module'     => 'product',
            'record_id'  => $record_id,
            'old_value'  => $old_value ? json_encode($old_value, JSON_UNESCAPED_UNICODE) : null,
            'new_value'  => $new_value ? json_encode($new_value, JSON_UNESCAPED_UNICODE) : null,
            'ip_address' => $this->input->ip_address(),
            'user_agent' => $this->input->user_agent(),
        ];
        
        $this->db->insert('audit_log', $log_data);
    }

    /**
     * Tìm kiếm sản phẩm
     * 
     * @param string $keyword
     * @return array
     */
    public function searchProducts($keyword)
    {
        $query = $this->db->query("
            SELECT 
                p.*,
                COUNT(DISTINCT pr.id_project) AS total_orders,
                CONCAT(p.diameter, 'mm') AS diameter_display
            FROM product p
            LEFT JOIN project pr ON p.id_product = pr.id_product
            WHERE p.product_name LIKE ?
               OR p.application LIKE ?
               OR p.summary LIKE ?
            GROUP BY p.id_product
            ORDER BY p.created_at DESC
        ", ["%{$keyword}%", "%{$keyword}%", "%{$keyword}%"]);

        return $query->result();
    }

    /**
     * Lấy danh sách đường kính có sẵn
     * Giống OrderModel.getDiameters()
     * 
     * @return array
     */
    public function getDiameters()
    {
        return [
            '0.5' => '0.5mm',
            '0.7' => '0.7mm',
            '1.0' => '1.0mm'
        ];
    }

    /**
     * Thống kê sản phẩm
     * 
     * @return object
     */
    public function getProductStatistics()
    {
        $query = $this->db->query("
            SELECT 
                COUNT(*) AS total_products,
                SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) AS active_products,
                SUM(CASE WHEN is_active = 0 THEN 1 ELSE 0 END) AS inactive_products
            FROM product
        ");

        return $query->row();
    }
}
