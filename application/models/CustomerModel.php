<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * CustomerModel - Xử lý nghiệp vụ quản lý khách hàng
 * 
 * Use Case: UC1 - Quản lý Khách hàng
 * Actor: Ban Giám Đốc (BOD)
 * 
 * Pattern: Follow OrderModel structure for consistency
 * Cấu trúc bảng (tóm tắt):
 *   - id_cust: INT AUTO_INCREMENT (ID khách hàng)
 *   - cust_name: VARCHAR(50) - Tên khách hàng
 *   - address: VARCHAR(50) - Địa chỉ, tối đa 50 ký tự
 *   - telp: VARCHAR(20) - Số điện thoại (đã chuyển sang VARCHAR để hỗ trợ mã vùng)
 *   - email: VARCHAR(255) - Email liên hệ
 *   - is_active: TINYINT(1) DEFAULT 1 - Trạng thái hoạt động
 *   - notes: TEXT - Ghi chú
 *   - created_at, updated_at, created_by - Thông tin audit
 * 
 * @author  Production Management System v2
 * @date    2025-11-24
 */
class CustomerModel extends CI_Model
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
     * Lấy tất cả khách hàng với thống kê đơn hàng
     * Pattern giống OrderModel.getAllOrders()
     * 
     * @param bool $active_only Chỉ lấy khách hàng đang hoạt động
     * @return array
     */
    public function getAllCustomers($filters = [])
    {
        $this->db->select("c.*, COUNT(DISTINCT p.id_project) AS total_orders, COALESCE(SUM(p.qty_request), 0) AS total_quantity, MAX(p.created_at) AS last_order_date, CASE WHEN c.is_active = 1 THEN 'Đang hợp tác' ELSE 'Ngừng hợp tác' END AS status_text", FALSE);
        $this->db->from('customer c');
        $this->db->join('project p', 'c.id_cust = p.id_cust', 'left');

        // Keyword (name / email / phone)
        if (isset($filters['keyword']) && $filters['keyword'] !== '') {
            $this->db->group_start();
            $this->db->like('c.cust_name', $filters['keyword']);
            $this->db->or_like('c.email', $filters['keyword']);
            $this->db->or_like('c.telp', $filters['keyword']);
            $this->db->group_end();
        }

        // Active filter
        if (isset($filters['is_active']) && $filters['is_active'] !== '') {
            $this->db->where('c.is_active', (int)$filters['is_active']);
        }

        $this->db->group_by('c.id_cust');

        // Min orders (HAVING)
        if (isset($filters['min_orders']) && $filters['min_orders'] !== '') {
            $this->db->having('COUNT(DISTINCT p.id_project) >=', (int)$filters['min_orders']);
        }

        $this->db->order_by('c.id_cust', 'ASC');
        $query = $this->db->get();
        return $query->result();
    }

    /**
     * Lấy thông tin khách hàng theo ID
     * Kèm theo thống kê đơn hàng chi tiết
     * 
     * @param int $id_cust
     * @return object|null
     */
    public function getCustomerById($id_cust)
    {
        $query = $this->db->query("
            SELECT 
                c.*,
                COUNT(DISTINCT p.id_project) AS total_orders,
                IFNULL(SUM(p.qty_request), 0) AS total_quantity,
                SUM(IF(p.pr_status = 3, 1, 0)) AS completed_orders,
                SUM(IF(p.pr_status IN (1,2), 1, 0)) AS active_orders,
                MAX(p.created_at) AS last_order_date,
                u.username AS created_by_username
            FROM customer c
            LEFT JOIN project p ON c.id_cust = p.id_cust
            LEFT JOIN user u ON c.created_by = u.user_id
            WHERE c.id_cust = ?
            GROUP BY c.id_cust
        ", [$id_cust]);

        return $query->row();
    }

    /**
     * Tạo ID khách hàng tự động
     * Pattern giống OrderModel.generateProjectName()
     * Format: AUTO INCREMENT từ 1001
     * 
     * @return int
     */
    public function generateCustomerId()
    {
        $query = $this->db->query("
            SELECT COALESCE(MAX(id_cust), 1000) + 1 AS next_id
            FROM customer
        ");
        
        return (int) $query->row()->next_id;
    }

    /**
     * Tạo khách hàng mới
     * Pattern giống OrderModel.createOrder() với transaction
     * 
     * @param array $customer_data
     * @return array ['success' => bool, 'message' => string, 'id_cust' => int|null]
     */
    public function addCustomer($customer_data)
    {
        // Bắt đầu transaction
        $this->db->trans_start();

        try {
            // Validate trước khi insert
            $validation = $this->validateCustomerData($customer_data);
            if (!$validation['valid']) {
                throw new Exception($validation['message']);
            }

            // Generate ID tự động
            $customer_data['id_cust'] = $this->generateCustomerId();
            $customer_data['is_active'] = 1;
            $customer_data['created_by'] = $this->session->userdata('user_id');
            // created_at, updated_at tự động bởi DEFAULT CURRENT_TIMESTAMP

            // Insert vào database
            $this->db->insert('customer', $customer_data);
            
            $insert_id = $customer_data['id_cust'];

            // Log activity
            $this->logActivity('create', $insert_id, null, $customer_data);

            // Commit transaction
            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                throw new Exception('Lỗi khi lưu vào cơ sở dữ liệu');
            }

            return [
                'success' => true,
                'message' => 'Khách hàng đã được tạo thành công',
                'id_cust' => $insert_id
            ];

        } catch (Exception $e) {
            $this->db->trans_rollback();
            
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'id_cust' => null
            ];
        }
    }

    /**
     * Cập nhật thông tin khách hàng
     * Pattern giống OrderModel.updateOrder()
     * 
     * @param int $id_cust
     * @param array $update_data
     * @return array ['success' => bool, 'message' => string]
     */
    public function updateCustomer($id_cust, $update_data)
    {
        $this->db->trans_start();

        try {
            // Validate
            $validation = $this->validateCustomerData($update_data, $id_cust);
            if (!$validation['valid']) {
                throw new Exception($validation['message']);
            }

            // Lấy dữ liệu cũ để log
            $old_data = $this->getCustomerById($id_cust);
            
            // Update (updated_at tự động)
            $this->db->where('id_cust', $id_cust);
            $this->db->update('customer', $update_data);

            // Log activity
            $this->logActivity('update', $id_cust, $old_data, $update_data);

            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                throw new Exception('Lỗi khi cập nhật dữ liệu');
            }

            return [
                'success' => true,
                'message' => 'Thông tin khách hàng đã được cập nhật'
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
     * Update only customer notes (bypass full validation)
     * Used by AJAX endpoints when editing notes inline from order forms
     * @param int $id_cust
     * @param string $notes
     * @return array ['success' => bool, 'message' => string]
     */
    public function updateCustomerNotes($id_cust, $notes)
    {
        $this->db->trans_start();
        try {
            $old_data = $this->getCustomerById($id_cust);

            $this->db->where('id_cust', $id_cust)->update('customer', ['notes' => $notes]);

            $this->logActivity('update', $id_cust, $old_data, ['notes' => $notes]);

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                throw new Exception('Lỗi khi cập nhật ghi chú khách hàng');
            }

            return ['success' => true, 'message' => 'Ghi chú đã được cập nhật'];
        } catch (Exception $e) {
            $this->db->trans_rollback();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Xóa khách hàng
     * Quy tắc: Không xóa được nếu đã có đơn hàng (FK constraint)
     * Pattern giống OrderModel.deleteOrder()
     * 
     * @param int $id_cust
     * @return array ['success' => bool, 'message' => string]
     */
    public function deleteCustomer($id_cust)
    {
        $this->db->trans_start();

        try {
            // Kiểm tra FK constraint với project table
            if ($this->hasOrders($id_cust)) {
                throw new Exception(
                    'Không thể xóa khách hàng đã có đơn hàng. ' .
                    'Vui lòng xóa đơn hàng trước hoặc đánh dấu khách hàng là không hoạt động.'
                );
            }

            // Lấy data để log
            $old_data = $this->getCustomerById($id_cust);

            // Xóa khách hàng
            $this->db->delete('customer', ['id_cust' => $id_cust]);

            // Log activity
            $this->logActivity('delete', $id_cust, $old_data, null);

            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                throw new Exception('Lỗi khi xóa khách hàng');
            }

            return [
                'success' => true,
                'message' => 'Khách hàng đã được xóa thành công'
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
     * Kiểm tra khách hàng có đơn hàng không
     * FK constraint check - Pattern giống OrderModel check planning
     * 
     * @param int $id_cust
     * @return bool
     */
    public function hasOrders($id_cust)
    {
        $query = $this->db->get_where('project', ['id_cust' => $id_cust]);
        return $query->num_rows() > 0;
    }

    /**
     * Validate dữ liệu khách hàng
     * Pattern giống OrderModel.validateOrderData() - return array với message
     * 
     * Database constraints:
     *   - telp: INT(20) - Chỉ lưu SỐ, 8-15 chữ số
     *   - email: VARCHAR(25) - Tối đa 25 ký tự
     *   - address: VARCHAR(50) - Tối đa 50 ký tự
     * 
     * @param array $data
     * @param int|null $id_cust (Exclude khi check duplicate)
     * @return array ['valid' => bool, 'message' => string]
     */
    public function validateCustomerData($data, $id_cust = null)
    {
        // 1. Tên khách hàng
        if (empty($data['cust_name'])) {
            return [
                'valid' => false,
                'message' => 'Vui lòng nhập tên khách hàng'
            ];
        }
        
        if (!preg_match('/^[\p{L}\s.,-]+$/u', $data['cust_name'])) {
            return [
                'valid' => false,
                'message' => 'Tên chỉ chứa chữ cái, khoảng trắng và ký tự (.,-)' 
            ];
        }
        
        if (strlen($data['cust_name']) > 50) {
            return [
                'valid' => false,
                'message' => 'Tên khách hàng tối đa 50 ký tự'
            ];
        }

        // 2. Số điện thoại (INT - Chỉ số, không dấu cách)
        if (empty($data['telp'])) {
            return [
                'valid' => false,
                'message' => 'Vui lòng nhập số điện thoại'
            ];
        }
        
        if (!preg_match('/^[0-9]{8,15}$/', $data['telp'])) {
            return [
                'valid' => false,
                'message' => 'Số điện thoại từ 8-15 chữ số (chỉ số, không dấu cách)'
            ];
        }

        // 3. Email (VARCHAR(25) - GIỚI HẠN 25 ký tự)
        if (empty($data['email'])) {
            return [
                'valid' => false,
                'message' => 'Vui lòng nhập email'
            ];
        }
        
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return [
                'valid' => false,
                'message' => 'Email không hợp lệ'
            ];
        }
        
        if (strlen($data['email']) > 25) {
            return [
                'valid' => false,
                'message' => 'Email tối đa 25 ký tự (giới hạn database)'
            ];
        }
        
        // Check email trùng lặp
        $this->db->where('email', $data['email']);
        if ($id_cust) {
            $this->db->where('id_cust !=', $id_cust);
        }
        $existing = $this->db->get('customer');
        if ($existing->num_rows() > 0) {
            return [
                'valid' => false,
                'message' => 'Email đã được sử dụng bởi khách hàng khác'
            ];
        }

        // 4. Địa chỉ (VARCHAR(50) - GIỚI HẠN 50 ký tự)
        if (empty($data['address'])) {
            return [
                'valid' => false,
                'message' => 'Vui lòng nhập địa chỉ'
            ];
        }
        
        if (strlen($data['address']) > 50) {
            return [
                'valid' => false,
                'message' => 'Địa chỉ tối đa 50 ký tự (giới hạn database)'
            ];
        }

        // Tất cả validation pass
        return [
            'valid' => true,
            'message' => 'OK'
        ];
    }

    /**
     * Ghi log hoạt động vào audit_log
     * Pattern giống BOD.php sử dụng session userdata
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
            'module'     => 'customer',
            'record_id'  => $record_id,
            'old_value'  => $old_value ? json_encode($old_value, JSON_UNESCAPED_UNICODE) : null,
            'new_value'  => $new_value ? json_encode($new_value, JSON_UNESCAPED_UNICODE) : null,
            'ip_address' => $this->input->ip_address(),
            'user_agent' => $this->input->user_agent(),
        ];
        
        $this->db->insert('audit_log', $log_data);
    }

    /**
     * Tìm kiếm khách hàng
     * 
     * @param string $keyword
     * @return array
     */
    public function searchCustomers($keyword)
    {
        $keyword_escaped = $this->db->escape_like_str($keyword);
        
        $query = $this->db->query("
            SELECT 
                c.*,
                COUNT(DISTINCT p.id_project) AS total_orders,
                IF(c.is_active = 1, 'Đang hợp tác', 'Ngưng hợp tác') AS status_text
            FROM customer c
            LEFT JOIN project p ON c.id_cust = p.id_cust
            WHERE c.cust_name LIKE CONCAT('%', ?, '%')
               OR c.email LIKE CONCAT('%', ?, '%')
               OR c.telp LIKE CONCAT('%', ?, '%')
            GROUP BY c.id_cust
            ORDER BY c.id_cust ASC
        ", [$keyword_escaped, $keyword_escaped, $keyword_escaped]);

        return $query->result();
    }

    /**
     * Thống kê khách hàng
     * 
     * @return object
     */
    public function getCustomerStatistics()
    {
        $query = $this->db->query("
            SELECT 
                COUNT(*) AS total_customers,
                SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) AS active_customers,
                SUM(CASE WHEN is_active = 0 THEN 1 ELSE 0 END) AS inactive_customers
            FROM customer
        ");

        return $query->row();
    }
}
