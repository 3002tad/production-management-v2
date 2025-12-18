<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * UserManagementModel - UC6: Quản lý Người dùng & Phân quyền
 * 
 * MÔ TẢ: Model xử lý CRUD user theo đặc tả UC6 (KHÔNG dùng bcrypt, procedures phức tạp)
 * ACTOR: Admin (system_admin role)
 * 
 * METHODS:
 * - getAllUsers(): Lấy danh sách user với thống kê
 * - getUserById(): Lấy chi tiết 1 user
 * - createUser(): Tạo user mới + gán role + đặt mật khẩu tạm
 * - updateUser(): Cập nhật thông tin/đổi vai trò (check last admin)
 * - lockUser(): Khóa tài khoản
 * - unlockUser(): Mở khóa tài khoản
 * - resetPassword(): Sinh mật khẩu tạm + buộc đổi lần đầu
 * - checkLastAdmin(): Kiểm tra còn admin nào khác không
 * 
 * @author Cặp 1 - UC6 Team
 * @version 1.0
 */
class UserManagementModel extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    // ========================================================================
    // PHẦN: CÁC PHƯƠNG THỨC LẤY DỮ LIỆU (READ)
    // ========================================================================

    /**
     * Lấy danh sách tất cả users với thống kê
     * 
     * @param array $filters ['role_id' => int, 'is_active' => 0|1, 'search' => string]
     * @return array Danh sách users với role info và audit stats
     */
    public function getAllUsers($filters = [])
    {
        $this->db->select('
            u.*,
            s.staff_name,
            s.phone,
            s.email,
            r.role_name,
            r.role_display_name,
            r.level AS role_level,
            creator.username AS created_by_username
        ');
        $this->db->from('user u');
        $this->db->join('staff s', 'u.staff_id = s.id_staff', 'left');
        $this->db->join('roles r', 'u.role_id = r.role_id', 'left');
        $this->db->join('user creator', 'u.created_by = creator.user_id', 'left');

        // Filters
        if (isset($filters['role_id']) && $filters['role_id'] != '') {
            $this->db->where('u.role_id', $filters['role_id']);
        }
        if (isset($filters['is_active']) && $filters['is_active'] != '') {
            $this->db->where('u.is_active', $filters['is_active']);
        }
        if (isset($filters['search']) && $filters['search'] != '') {
            $search = $filters['search'];
            $this->db->group_start();
            $this->db->like('u.username', $search);
            $this->db->or_like('u.full_name', $search);
            $this->db->or_like('u.email', $search);
            $this->db->group_end();
        }

        $this->db->order_by('u.created_at', 'DESC');

        $query = $this->db->get();
        return $query->result();
    }

    /**
     * Lấy chi tiết 1 user theo ID
     * 
     * @param int $user_id
     * @return object|null User object hoặc null
     */
    public function getUserById($user_id)
    {
        $this->db->select('
            u.*,
            s.staff_name,
            s.phone,
            s.email,
            r.role_name,
            r.role_display_name,
            r.level AS role_level,
            creator.username AS created_by_username,
            creator.full_name AS created_by_fullname
        ');
        $this->db->from('user u');
        $this->db->join('staff s', 'u.staff_id = s.id_staff', 'left');
        $this->db->join('roles r', 'u.role_id = r.role_id', 'left');
        $this->db->join('user creator', 'u.created_by = creator.user_id', 'left');
        $this->db->where('u.user_id', $user_id);

        $query = $this->db->get();
        return $query->row();
    }

    /**
     * Lấy danh sách staff chưa có tài khoản user
     * 
     * @param array $filters
     * @return array Danh sách staff objects với thông tin department và position
     */
    public function getStaffWithoutUser($filters = [])
    {
        $query = $this->db->query("
            SELECT s.*, 
                   COALESCE(s.department, 'Chưa Phân Loại') as department,
                   COALESCE(s.position, 'Chưa Phân Loại') as position
            FROM staff s 
            LEFT JOIN user u ON s.id_staff = u.staff_id 
            WHERE u.staff_id IS NULL 
            ORDER BY s.created_at DESC
        ");
        return $query->result();
    }
    
    /**
     * Lấy danh sách roles đang hoạt động cho dropdown
     *
     * @return array
     */
    public function getRoles()
    {
        // Nếu bảng roles chưa tồn tại (chưa chạy migration RBAC) thì trả mảng rỗng
        if (!$this->db->table_exists('roles')) {
            return [];
        }

        $this->db->select('role_id, role_name, role_display_name, level, is_active');
        $this->db->from('roles');
        $this->db->where('is_active', 1);
        $this->db->order_by('level', 'DESC');

        return $this->db->get()->result();
    }

    // ========================================================================
    // PHẦN: TẠO (CREATE)
    // ========================================================================

    /**
     * Tạo user mới cho staff có sẵn + gán role + đặt mật khẩu tạm
     * Basic Flow 1: Tạo người dùng + gán vai trò từ staff data
     * 
     * @param int $staff_id ID của staff
     * @param array $data ['username', 'password', 'role_id']
     * @param int $created_by User ID admin thực hiện tạo
     * @return array ['success' => bool, 'message' => string, 'user_id' => int]
     */
    public function createUser($staff_id, $data, $created_by)
    {
        // Lấy thông tin staff
        $staff = $this->db->get_where('staff', ['id_staff' => $staff_id])->row();
        if (!$staff) {
            return [
                'success' => false,
                'message' => 'Nhân viên không tồn tại.'
            ];
        }

        // Validate phía server: mẫu username và độ dài
        $username = isset($data['username']) ? trim($data['username']) : '';
        if ($username === '' || strlen($username) < 3 || strlen($username) > 11 || !preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
            return [
                'success' => false,
                'message' => 'Username không hợp lệ. Chỉ cho phép chữ, số và gạch dưới, từ 3-11 ký tự.'
            ];
        }

        // Ensure staff does not already have a user (server-side enforcement)
        $existsStaffUser = $this->db->get_where('user', ['staff_id' => $staff_id])->row();
        if ($existsStaffUser) {
            return [
                'success' => false,
                'message' => 'Nhân viên đã được gán tài khoản. Vui lòng kiểm tra danh sách người dùng.'
            ];
        }

        // Kiểm tra role_id có tồn tại
        if (!isset($data['role_id']) || !$this->db->get_where('roles', ['role_id' => $data['role_id']])->row()) {
            return [
                'success' => false,
                'message' => 'Vai trò không hợp lệ. Vui lòng chọn vai trò.'
            ];
        }

        // Validation 1: Check username đã tồn tại (case-insensitive)
        $this->db->where('LOWER(username)', strtolower($username));
        $existing = $this->db->get('user')->row();
        if ($existing) {
            return [
                'success' => false,
                'message' => 'Username đã tồn tại. Vui lòng chọn username khác.'
            ];
        }

        // Validation 2: Check password strength (Alternative Flow 6.3)
        if (strlen($data['password']) < 6 || strlen($data['password']) > 11) {
            return [
                'success' => false,
                'message' => 'Mật khẩu phải từ 6 đến 11 ký tự.'
            ];
        }

        // Insert user mới
        $insert_data = [
            'staff_id'               => $staff_id,
            'username'               => $data['username'],
            'password'               => $data['password'], // Plaintext theo đặc tả
            'role_id'                => $data['role_id'],
            'full_name'              => $staff->staff_name, // Lấy từ staff
            'email'                  => $staff->email,
            'phone'                  => $staff->phone,
            'is_active'              => 1,
            'must_change_password'   => 1, // Bắt buộc đổi password lần đầu
            'created_by'             => $created_by,
            'created_at'             => date('Y-m-d H:i:s'),
            'updated_at'             => date('Y-m-d H:i:s')
        ];

        // Use transaction to guard insert + audit
        $this->db->trans_start();
        $this->db->insert('user', $insert_data);
        $user_id = $this->db->insert_id();

        if ($user_id) {
            // Log audit
            $this->logAudit($created_by, 'create', 'user', $user_id, null, $insert_data);
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE || !$user_id) {
            $this->db->trans_rollback();
            return [
                'success' => false,
                'message' => 'Lỗi kỹ thuật khi tạo user. Vui lòng thử lại.'
            ];
        }

        return [
            'success' => true,
            'message' => 'Tạo user thành công. User phải đổi mật khẩu khi đăng nhập lần đầu.',
            'user_id' => $user_id
        ];
    }

    // ========================================================================
    // PHẦN: CẬP NHẬT (UPDATE)
    // ========================================================================

    /**
     * Cập nhật thông tin user / đổi vai trò
     * Basic Flow 2: Cập nhật thông tin/đổi vai trò
     * 
     * @param int $user_id
     * @param array $data ['full_name', 'email', 'phone', 'role_id']
     * @param int $updated_by User ID admin thực hiện update
     * @return array ['success' => bool, 'message' => string]
     */
    public function updateUser($user_id, $data, $updated_by)
    {
        // Lấy thông tin user hiện tại
        $user = $this->getUserById($user_id);
        if (!$user) {
            return [
                'success' => false,
                'message' => 'Không tìm thấy user.'
            ];
        }

        // CRITICAL CHECK: Không được hạ bậc admin duy nhất (Bước 4 Basic Flow 2)
        if (isset($data['role_id']) && $data['role_id'] != $user->role_id) {
            if ($user->role_name === 'system_admin') {
                // Check còn admin nào khác không
                if ($this->checkLastAdmin($user_id)) {
                    return [
                        'success' => false,
                        'message' => 'Không thể hạ bậc admin duy nhất. Phải có ít nhất 1 admin trong hệ thống.'
                    ];
                }
            }
            // Validate the new role exists
            if (!$this->db->get_where('roles', ['role_id' => $data['role_id']])->row()) {
                return [
                    'success' => false,
                    'message' => 'Vai trò được chọn không hợp lệ.'
                ];
            }
        }

        // Validation email - không cần vì không update email trong user

        // Prepare update data - chỉ update role_id
        $update_data = [];
        if (isset($data['role_id'])) $update_data['role_id'] = $data['role_id'];
        $update_data['updated_at'] = date('Y-m-d H:i:s');

        // Update
        $this->db->where('user_id', $user_id);
        $result = $this->db->update('user', $update_data);

        if ($result) {
            // Log audit
            $this->logAudit($updated_by, 'update', 'user', $user_id, $user, $update_data);

            return [
                'success' => true,
                'message' => 'Cập nhật thông tin user thành công. Quyền hiệu lực ngay.'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Lỗi kỹ thuật khi cập nhật. Vui lòng thử lại.'
            ];
        }
    }

    // ========================================================================
    // LOCK/UNLOCK OPERATIONS
    // ========================================================================

    /**
     * Khóa tài khoản user
     * Basic Flow 3: Khoá/Mở khoá tài khoản
     * 
     * @param int $user_id
     * @param string $reason Lý do khóa
     * @param int $locked_by User ID admin thực hiện khóa
     * @return array ['success' => bool, 'message' => string]
     */
    public function lockUser($user_id, $reason, $locked_by)
    {
        $user = $this->getUserById($user_id);
        if (!$user) {
            return ['success' => false, 'message' => 'Không tìm thấy user.'];
        }

        // Không cho khóa admin duy nhất
        if ($user->role_name === 'system_admin' && $this->checkLastAdmin($user_id)) {
            return [
                'success' => false,
                'message' => 'Không thể khóa admin duy nhất trong hệ thống.'
            ];
        }

        $update_data = [
            'is_active'   => 0,
            'updated_at'  => date('Y-m-d H:i:s')
        ];

        $this->db->where('user_id', $user_id);
        $result = $this->db->update('user', $update_data);

        if ($result) {
            // Log audit
            $this->logAudit($locked_by, 'lock', 'user', $user_id, $user, [
                'reason' => $reason,
                'is_active' => 0
            ]);

            return [
                'success' => true,
                'message' => 'Đã khóa tài khoản user. User không thể đăng nhập.'
            ];
        } else {
            return ['success' => false, 'message' => 'Lỗi kỹ thuật khi khóa user.'];
        }
    }

    /**
     * Mở khóa tài khoản user
     * 
     * @param int $user_id
     * @param int $unlocked_by User ID admin thực hiện mở khóa
     * @return array ['success' => bool, 'message' => string]
     */
    public function unlockUser($user_id, $unlocked_by)
    {
        $user = $this->getUserById($user_id);
        if (!$user) {
            return ['success' => false, 'message' => 'Không tìm thấy user.'];
        }

        $update_data = [
            'is_active'   => 1,
            'updated_at'  => date('Y-m-d H:i:s')
        ];

        $this->db->where('user_id', $user_id);
        $result = $this->db->update('user', $update_data);

        if ($result) {
            // Log audit
            $this->logAudit($unlocked_by, 'unlock', 'user', $user_id, $user, [
                'is_active' => 1
            ]);

            return [
                'success' => true,
                'message' => 'Đã mở khóa tài khoản. User có thể đăng nhập lại.'
            ];
        } else {
            return ['success' => false, 'message' => 'Lỗi kỹ thuật khi mở khóa user.'];
        }
    }

    // ========================================================================
    // RESET PASSWORD OPERATION
    // ========================================================================

    /**
     * Đặt lại mật khẩu tạm + buộc đổi lần đầu
     * Basic Flow 4: Đặt lại mật khẩu
     * 
     * @param int $user_id
     * @param int $reset_by User ID admin thực hiện reset
     * @return array ['success' => bool, 'message' => string, 'temp_password' => string]
     */
    public function resetPassword($user_id, $reset_by)
    {
        $user = $this->getUserById($user_id);
        if (!$user) {
            return ['success' => false, 'message' => 'Không tìm thấy user.'];
        }

        // Sinh mật khẩu tạm ngẫu nhiên 8 ký tự (chữ hoa + số)
        $temp_password = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 8));

        $update_data = [
            'password'              => $temp_password, // Lưu tạm để user login
            'temp_password'         => $temp_password, // Lưu để admin biết
            'must_change_password'  => 1, // Bắt buộc đổi khi login
            'updated_at'            => date('Y-m-d H:i:s')
        ];

        $this->db->where('user_id', $user_id);
        $result = $this->db->update('user', $update_data);

        if ($result) {
            // Log audit
            $this->logAudit($reset_by, 'reset_password', 'user', $user_id, $user, [
                'temp_password_generated' => 'YES'
            ]);

            return [
                'success' => true,
                'message' => 'Đặt lại mật khẩu thành công. User phải đổi mật khẩu khi đăng nhập tiếp theo.',
                'temp_password' => $temp_password
            ];
        } else {
            return ['success' => false, 'message' => 'Lỗi kỹ thuật khi reset password.'];
        }
    }

    // ========================================================================
    // HELPER METHODS
    // ========================================================================

    /**
     * Kiểm tra user này có phải admin duy nhất không
     * Dùng để prevent hạ bậc/xóa/khóa admin cuối cùng
     * 
     * @param int $user_id
     * @return bool TRUE nếu là admin duy nhất
     */
    public function checkLastAdmin($user_id)
    {
        // Check user này có phải system_admin không
        $this->db->select('u.user_id, r.role_name');
        $this->db->from('user u');
        $this->db->join('roles r', 'u.role_id = r.role_id');
        $this->db->where('u.user_id', $user_id);
        $this->db->where('r.role_name', 'system_admin');
        $this->db->where('u.is_active', 1);
        $user = $this->db->get()->row();

        if (!$user) {
            return false; // Không phải admin
        }

        // Đếm số admin còn lại (loại trừ user này)
        $this->db->select('COUNT(*) as admin_count');
        $this->db->from('user u');
        $this->db->join('roles r', 'u.role_id = r.role_id');
        $this->db->where('r.role_name', 'system_admin');
        $this->db->where('u.is_active', 1);
        $this->db->where('u.user_id !=', $user_id);
        $result = $this->db->get()->row();

        return ($result->admin_count == 0); // TRUE nếu không còn admin nào khác
    }

    /**
     * Ghi log audit
     * 
     * @param int $user_id
     * @param string $action (create, update, delete, lock, unlock, reset_password)
     * @param string $module
     * @param int $record_id
     * @param mixed $old_value
     * @param mixed $new_value
     */
    private function logAudit($user_id, $action, $module, $record_id, $old_value, $new_value)
    {
        // Lấy username để ghi log
        $user = $this->db->select('username')->get_where('user', ['user_id' => $user_id])->row();
        $username = $user ? $user->username : 'system';

        $log_data = [
            'user_id'    => $user_id,
            'username'   => $username,
            'action'     => $action,
            'module'     => $module,
            'record_id'  => $record_id,
            'old_value'  => is_object($old_value) || is_array($old_value) ? json_encode($old_value) : $old_value,
            'new_value'  => is_array($new_value) ? json_encode($new_value) : $new_value,
            'ip_address' => $this->input->ip_address(),
            'user_agent' => $this->input->user_agent(),
            'created_at' => date('Y-m-d H:i:s')
        ];

        $this->db->insert('audit_log', $log_data);
    }

    /**
     * Lấy audit log của user
     * 
     * @param int $user_id
     * @param int $limit
     * @return array
     */
    public function getUserAuditLog($user_id, $limit = 50)
    {
        $this->db->select('al.*, u.username, u.full_name');
        $this->db->from('audit_log al');
        $this->db->join('user u', 'al.user_id = u.user_id', 'left');
        $this->db->where('al.record_id', $user_id);
        $this->db->where('al.module', 'user');
        $this->db->order_by('al.created_at', 'DESC');
        $this->db->limit($limit);

        $query = $this->db->get();
        return $query->result();
    }

    /**
     * Lấy thống kê tổng hợp
     * 
     * @return array ['total_users', 'active_users', 'locked_users', 'must_change_password']
     */
    public function getStatistics()
    {
        $stats = [];

        // Tổng số user
        $stats['total_users'] = $this->db->count_all('user');

        // Số user đang hoạt động
        $this->db->where('is_active', 1);
        $stats['active_users'] = $this->db->count_all_results('user');

        // Số user bị khóa
        $this->db->where('is_active', 0);
        $stats['locked_users'] = $this->db->count_all_results('user');

        // Số user cần đổi mật khẩu (must_change_password)
        $this->db->where('must_change_password', 1);
        $stats['must_change_password'] = $this->db->count_all_results('user');

        return $stats;
    }
}
