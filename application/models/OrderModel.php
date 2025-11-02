<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * OrderModel - Xử lý nghiệp vụ đơn hàng bút bi
 * 
 * Use Case: Tiếp nhận & Tạo đơn hàng
 * Actor: Ban Giám Đốc (BOD)
 * 
 * @author  Production Management System v2
 * @date    2025-11-01
 */
class OrderModel extends CI_Model
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
     * Lấy tất cả đơn hàng (JOIN với customer, product)
     * Sắp xếp theo ngày tạo mới nhất
     * 
     * @return array
     */
    public function getAllOrders()
    {
        $query = $this->db->query("
            SELECT 
                p.*,
                c.cust_name,
                c.address,
                c.telp,
                c.email,
                pr.product_name,
                pr.application AS product_color,
                CASE 
                    WHEN p.risk_flag = 1 THEN 'Nguy cơ trễ hạn'
                    ELSE 'Bình thường'
                END AS risk_status,
                CASE 
                    WHEN p.pr_status = 0 THEN 'Chờ duyệt'
                    WHEN p.pr_status = 1 THEN 'Đã duyệt'
                    WHEN p.pr_status = 2 THEN 'Đang sản xuất'
                    WHEN p.pr_status = 3 THEN 'Hoàn thành'
                    ELSE 'Hủy'
                END AS status_text,
                CONCAT(p.diameter / 10, 'mm') AS diameter_display
            FROM project p
            JOIN customer c ON p.id_cust = c.id_cust
            JOIN product pr ON p.id_product = pr.id_product
            ORDER BY p.created_at DESC
        ");
        
        return $query->result();
    }

    /**
     * Lấy đơn hàng theo ID
     * 
     * @param int $id_project
     * @return object|null
     */
    public function getOrderById($id_project)
    {
        $query = $this->db->query("
            SELECT 
                p.*,
                c.cust_name,
                c.address,
                c.telp,
                c.email,
                pr.product_name,
                pr.application AS product_color,
                CONCAT(p.diameter / 10, 'mm') AS diameter_display
            FROM project p
            JOIN customer c ON p.id_cust = c.id_cust
            JOIN product pr ON p.id_product = pr.id_product
            WHERE p.id_project = ?
        ", [$id_project]);

        return $query->row();
    }

    /**
     * Lấy danh sách khách hàng
     * Sắp xếp theo tên
     * 
     * @return array
     */
    public function getCustomers()
    {
        return $this->db->query("
            SELECT 
                id_cust, 
                cust_name, 
                address, 
                telp, 
                email
            FROM customer
            ORDER BY cust_name ASC
        ")->result();
    }

    /**
     * Lấy danh sách sản phẩm
     * Sắp xếp theo tên
     * 
     * @return array
     */
    public function getProducts()
    {
        return $this->db->query("
            SELECT 
                id_product, 
                product_name, 
                application,
                diameter,
                summary
            FROM product
            ORDER BY product_name ASC
        ")->result();
    }

    /**
     * Validate dữ liệu đơn hàng
     * Alternative Flow 4.1: Kiểm tra thiếu dữ liệu bắt buộc
     * 
     * @param array $data
     * @return array ['valid' => bool, 'message' => string]
     */
    public function validateOrderData($data)
    {
        // Kiểm tra trường bắt buộc: id_cust
        if (empty($data['id_cust'])) {
            return [
                'valid' => false, 
                'message' => 'Vui lòng chọn khách hàng'
            ];
        }

        // Kiểm tra trường bắt buộc: id_product
        if (empty($data['id_product'])) {
            return [
                'valid' => false, 
                'message' => 'Vui lòng chọn sản phẩm'
            ];
        }

        // Kiểm tra trường bắt buộc: diameter
        if (empty($data['diameter'])) {
            return [
                'valid' => false, 
                'message' => 'Vui lòng chọn đường kính'
            ];
        }

        // Kiểm tra trường bắt buộc: qty_request
        if (empty($data['qty_request']) || $data['qty_request'] <= 0) {
            return [
                'valid' => false, 
                'message' => 'Số lượng phải lớn hơn 0'
            ];
        }

        // Kiểm tra trường bắt buộc: entry_date
        if (empty($data['entry_date'])) {
            return [
                'valid' => false, 
                'message' => 'Vui lòng nhập hạn giao'
            ];
        }

        // Kiểm tra hạn giao >= ngày hiện tại
        $today = date('Y-m-d');
        if ($data['entry_date'] < $today) {
            return [
                'valid' => false, 
                'message' => 'Hạn giao phải từ hôm nay trở đi'
            ];
        }

        // Kiểm tra khách hàng tồn tại trong database
        $customer = $this->db->get_where('customer', [
            'id_cust' => $data['id_cust']
        ]);
        
        if ($customer->num_rows() == 0) {
            return [
                'valid' => false, 
                'message' => 'Khách hàng không tồn tại trong hệ thống'
            ];
        }

        // Kiểm tra sản phẩm tồn tại trong database
        $product = $this->db->get_where('product', [
            'id_product' => $data['id_product']
        ]);
        
        if ($product->num_rows() == 0) {
            return [
                'valid' => false, 
                'message' => 'Sản phẩm không tồn tại trong hệ thống'
            ];
        }

        // Tất cả validation đều pass
        return [
            'valid' => true, 
            'message' => 'OK'
        ];
    }

    /**
     * Kiểm tra năng lực sản xuất sơ bộ
     * Dựa trên: Lịch máy hiện có + Định mức BOM
     * 
     * Alternative Flow 6.1: Vượt công suất
     * 
     * @param int $id_product
     * @param int $qty_request
     * @param string $entry_date (Y-m-d)
     * @return array ['feasible' => bool, 'message' => string, 'details' => array]
     */
    public function checkCapacity($id_product, $qty_request, $entry_date)
    {
        // 1. Tính tổng công suất máy khả dụng
        $capacity_query = $this->db->query("
            SELECT 
                SUM(m.capacity) AS total_capacity,
                COUNT(m.id_machine) AS available_machines
            FROM machine m
            WHERE m.mc_status = 1  -- Máy đang hoạt động
        ");

        $capacity = $capacity_query->row();
        $total_capacity = $capacity->total_capacity ?? 0;
        $available_machines = $capacity->available_machines ?? 0;

        // Nếu không có máy khả dụng
        if ($total_capacity == 0) {
            return [
                'feasible' => false, 
                'message' => 'Không có máy khả dụng',
                'details' => [
                    'total_capacity' => 0,
                    'available_machines' => 0,
                    'days_remaining' => 0,
                    'feasible_output' => 0,
                    'current_load' => 0,
                    'remaining_capacity' => 0
                ]
            ];
        }

        // 2. Tính số ngày còn lại đến hạn giao
        $today = date('Y-m-d');
        $date_diff = (strtotime($entry_date) - strtotime($today)) / (60 * 60 * 24);

        if ($date_diff < 0) {
            return [
                'feasible' => false, 
                'message' => 'Hạn giao đã quá hạn',
                'details' => [
                    'total_capacity' => $total_capacity,
                    'available_machines' => $available_machines,
                    'days_remaining' => $date_diff,
                    'feasible_output' => 0,
                    'current_load' => 0,
                    'remaining_capacity' => 0
                ]
            ];
        }

        // 3. Tính công suất khả thi
        // Giả định: 3 ca/ngày, hiệu suất 80%
        $shifts_per_day = 3;
        $efficiency = 0.8;
        $feasible_output = $total_capacity * $date_diff * $shifts_per_day * $efficiency;

        // 4. Tính tải hiện tại (đơn hàng đã có trong cùng khoảng thời gian)
        $current_load_query = $this->db->query("
            SELECT COALESCE(SUM(qty_request), 0) AS current_load
            FROM project
            WHERE entry_date BETWEEN ? AND ?
              AND pr_status IN (1, 2)  -- Đã duyệt hoặc đang sản xuất
        ", [$today, $entry_date]);

        $current_load = $current_load_query->row()->current_load;

        // 5. Tính công suất còn lại
        $remaining_capacity = $feasible_output - $current_load;

        // 6. So sánh với yêu cầu
        $details = [
            'total_capacity' => $total_capacity,
            'available_machines' => $available_machines,
            'days_remaining' => $date_diff,
            'feasible_output' => round($feasible_output),
            'current_load' => $current_load,
            'remaining_capacity' => round($remaining_capacity),
            'qty_request' => $qty_request,
            'shortage' => max(0, $qty_request - $remaining_capacity)
        ];

        if ($remaining_capacity >= $qty_request) {
            return [
                'feasible' => true, 
                'message' => sprintf(
                    'Công suất khả thi: %s đơn vị. Hiện tại đã dùng: %s. Còn lại: %s',
                    number_format($feasible_output),
                    number_format($current_load),
                    number_format($remaining_capacity)
                ),
                'details' => $details
            ];
        } else {
            // ALTERNATIVE FLOW 6.1: Vượt công suất
            return [
                'feasible' => false, 
                'message' => sprintf(
                    'Vượt công suất %s đơn vị. Cần %s, chỉ còn %s khả dụng',
                    number_format($qty_request - $remaining_capacity),
                    number_format($qty_request),
                    number_format($remaining_capacity)
                ),
                'details' => $details
            ];
        }
    }

    /**
     * Tạo tên Project tự động
     * Format: ORD-{id_cust}-{YYYYMMDD}-{seq}
     * 
     * Example: ORD-1001-20251101-001
     * 
     * @param int $id_cust
     * @return string
     */
    public function generateProjectName($id_cust)
    {
        // LẤY NGÀY TỪ MySQL (đảm bảo đồng bộ với created_at)
        $date_query = $this->db->query("SELECT DATE_FORMAT(CURDATE(), '%Y%m%d') AS date_part");
        $date_part = $date_query->row()->date_part; // 20251102 (từ MySQL, không phải PHP)
        
        // Đếm số đơn hàng của khách hàng này trong ngày
        $count_query = $this->db->query("
            SELECT COUNT(*) AS count
            FROM project
            WHERE id_cust = ?
              AND DATE(created_at) = CURDATE()
        ", [$id_cust]);

        $count = $count_query->row()->count;
        $seq = str_pad($count + 1, 3, '0', STR_PAD_LEFT); // 001, 002, 003...

        return "ORD-{$id_cust}-{$date_part}-{$seq}";
    }

    /**
     * Tạo đơn hàng + Project
     * Bước 7 trong Basic Flow
     * 
     * @param array $order_data
     * @return array ['success' => bool, 'message' => string, 'id_project' => int|null]
     */
    public function createOrder($order_data)
    {
        // Bắt đầu transaction để đảm bảo tính toàn vẹn dữ liệu
        $this->db->trans_start();

        try {
            // Insert vào bảng project
            $this->db->insert('project', $order_data);

            $insert_id = $this->db->insert_id();

            // Commit transaction
            $this->db->trans_complete();

            // Kiểm tra transaction status
            if ($this->db->trans_status() === FALSE) {
                throw new Exception('Lỗi khi lưu vào cơ sở dữ liệu. Vui lòng thử lại.');
            }

            return [
                'success' => true,
                'message' => 'Đơn hàng đã được tạo và duyệt thành công',
                'id_project' => $insert_id
            ];

        } catch (Exception $e) {
            // Rollback nếu có lỗi
            $this->db->trans_rollback();
            
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'id_project' => null
            ];
        }
    }

    /**
     * Cập nhật đơn hàng
     * 
     * @param int $id_project
     * @param array $update_data
     * @return array ['success' => bool, 'message' => string]
     */
    public function updateOrder($id_project, $update_data)
    {
        $this->db->trans_start();

        try {
            $this->db->where('id_project', $id_project);
            $this->db->update('project', $update_data);

            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                throw new Exception('Lỗi khi cập nhật đơn hàng');
            }

            return [
                'success' => true,
                'message' => 'Đơn hàng đã được cập nhật thành công'
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
     * Xóa đơn hàng
     * Lưu ý: Cần kiểm tra Foreign Key trước khi xóa
     * 
     * @param int $id_project
     * @return array ['success' => bool, 'message' => string]
     */
    public function deleteOrder($id_project)
    {
        $this->db->trans_start();

        try {
            // Kiểm tra xem đơn hàng có Planning hay không
            $planning_check = $this->db->get_where('planning', [
                'id_project' => $id_project
            ]);

            if ($planning_check->num_rows() > 0) {
                throw new Exception(
                    'Không thể xóa đơn hàng đã có kế hoạch sản xuất. ' .
                    'Vui lòng xóa kế hoạch trước.'
                );
            }

            // Xóa đơn hàng
            $this->db->delete('project', ['id_project' => $id_project]);

            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                throw new Exception('Lỗi khi xóa đơn hàng');
            }

            return [
                'success' => true,
                'message' => 'Đơn hàng đã được xóa thành công'
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
     * Lấy danh sách đường kính có sẵn
     * 
     * @return array
     */
    public function getDiameters()
    {
        return [
            5 => '0.5mm',
            7 => '0.7mm',
            10 => '1.0mm'
        ];
    }

    /**
     * Thống kê đơn hàng theo trạng thái
     * 
     * @return object
     */
    public function getOrderStatistics()
    {
        $query = $this->db->query("
            SELECT 
                COUNT(*) AS total_orders,
                SUM(CASE WHEN pr_status = 1 THEN 1 ELSE 0 END) AS approved,
                SUM(CASE WHEN pr_status = 2 THEN 1 ELSE 0 END) AS in_production,
                SUM(CASE WHEN pr_status = 3 THEN 1 ELSE 0 END) AS completed,
                SUM(CASE WHEN risk_flag = 1 THEN 1 ELSE 0 END) AS at_risk,
                SUM(qty_request) AS total_quantity
            FROM project
        ");

        return $query->row();
    }
}
