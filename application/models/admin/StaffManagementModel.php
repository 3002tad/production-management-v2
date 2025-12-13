<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class StaffManagementModel extends CI_Model
{
    protected $table = 'staff';

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    // ==================== READ OPERATIONS ====================

    public function getAllStaff($filters = [])
    {
        $this->db->from($this->table);

        if (!empty($filters['department'])) {
            $this->db->where('department', $filters['department']);
        }

        if (!empty($filters['position'])) {
            $this->db->where('position', $filters['position']);
        }

        if (isset($filters['status']) && $filters['status'] !== '') {
            $this->db->where('st_status', (int)$filters['status']);
        }

        if (!empty($filters['search'])) {
            $this->db->group_start()
                ->like('staff_name', $filters['search'])
                ->or_like('email', $filters['search'])
                ->or_like('phone', $filters['search'])
                ->group_end();
        }

        $this->db->order_by('created_at', 'DESC');
        return $this->db->get()->result();
    }

    public function getStaffById($staff_id)
    {
        return $this->db->where('id_staff', $staff_id)->get($this->table)->row();
    }

    public function getDepartments()
    {
        $this->db->select('department');
        $this->db->distinct();
        $this->db->where('department IS NOT NULL AND department != ""');
        $this->db->order_by('department');
        $result = $this->db->get($this->table)->result();
        return array_column($result, 'department');
    }

    public function getPositions()
    {
        $this->db->select('position');
        $this->db->distinct();
        $this->db->where('position IS NOT NULL AND position != ""');
        $this->db->order_by('position');
        $result = $this->db->get($this->table)->result();
        return array_column($result, 'position');
    }

    public function getStatistics()
    {
        $stats = [];

        // Total staff
        $stats['total'] = $this->db->count_all($this->table);

        // Active staff
        $stats['active'] = $this->db->where('st_status', 1)->count_all_results($this->table);

        // Staff with user accounts
        $stats['with_user'] = $this->db->where('id_staff IN (SELECT staff_id FROM user WHERE staff_id IS NOT NULL)', null, false)->count_all_results($this->table);

        // Staff without user accounts
        $stats['without_user'] = $stats['total'] - $stats['with_user'];

        return $stats;
    }

    // ==================== CRUD OPERATIONS ====================

    public function createStaff($data, $created_by)
    {
        // Generate ID tự động
        $data['id_staff'] = $this->generateStaffId();
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');

        // Check email unique
        if (!empty($data['email']) && $this->db->where('email', $data['email'])->count_all_results($this->table) > 0) {
            return ['success' => false, 'message' => 'Email đã tồn tại trong hệ thống.'];
        }

        $this->db->insert($this->table, $data);
        $staff_id = $this->db->insert_id();

        if ($staff_id) {
            // Log audit
            $this->logAudit($created_by, 'create', 'staff', $staff_id, null, $data);
            return ['success' => true, 'message' => 'Thêm nhân viên thành công.', 'staff_id' => $staff_id];
        } else {
            return ['success' => false, 'message' => 'Có lỗi xảy ra khi thêm nhân viên.'];
        }
    }

    public function updateStaff($id, $data, $updated_by)
    {
        $old_data = $this->getStaffById($id);
        if (!$old_data) {
            return ['success' => false, 'message' => 'Nhân viên không tồn tại.'];
        }

        $data['updated_at'] = date('Y-m-d H:i:s');

        // Check email unique (exclude current)
        if (!empty($data['email']) && $this->db->where('email', $data['email'])->where('id_staff !=', $id)->count_all_results($this->table) > 0) {
            return ['success' => false, 'message' => 'Email đã tồn tại trong hệ thống.'];
        }

        $this->db->where('id_staff', $id);
        $updated = $this->db->update($this->table, $data);

        if ($updated) {
            // Log audit
            $this->logAudit($updated_by, 'update', 'staff', $id, $old_data, $data);
            return ['success' => true, 'message' => 'Cập nhật nhân viên thành công.'];
        } else {
            return ['success' => false, 'message' => 'Có lỗi xảy ra khi cập nhật nhân viên.'];
        }
    }

    public function deleteStaff($id, $deleted_by)
    {
        $old_data = $this->getStaffById($id);
        if (!$old_data) {
            return ['success' => false, 'message' => 'Nhân viên không tồn tại.'];
        }

        // Check if has user account
        if ($this->hasUserAccount($id)) {
            return ['success' => false, 'message' => 'Không thể xóa nhân viên đã có tài khoản user.'];
        }

        $this->db->where('id_staff', $id);
        $deleted = $this->db->delete($this->table);

        if ($deleted) {
            // Log audit
            $this->logAudit($deleted_by, 'delete', 'staff', $id, $old_data, null);
            return ['success' => true, 'message' => 'Xóa nhân viên thành công.'];
        } else {
            return ['success' => false, 'message' => 'Có lỗi xảy ra khi xóa nhân viên.'];
        }
    }

    // ==================== HELPER METHODS ====================

    private function generateStaffId()
    {
        // Generate ID starting from 1001
        $max_id = $this->db->select_max('id_staff')->get($this->table)->row()->id_staff;
        return $max_id ? $max_id + 1 : 1001;
    }

    private function logAudit($user_id, $action, $module, $record_id, $old_value, $new_value)
    {
        // Assume audit_log table exists
        if ($this->db->table_exists('audit_log')) {
            $this->db->insert('audit_log', [
                'user_id' => $user_id,
                'action' => $action,
                'module' => $module,
                'record_id' => $record_id,
                'old_value' => json_encode($old_value),
                'new_value' => json_encode($new_value),
                'created_at' => date('Y-m-d H:i:s'),
                'ip_address' => $this->input->ip_address(),
                'user_agent' => $this->input->user_agent()
            ]);
        }
    }

    public function hasUserAccount($staff_id)
    {
        return $this->db->where('staff_id', $staff_id)->count_all_results('user') > 0;
    }

    public function exists_email($email, $exclude_id = null)
    {
        if (empty($email)) return false;
        $this->db->from($this->table);
        $this->db->where('email', $email);
        if (!is_null($exclude_id)) {
            $this->db->where('id_staff <>', (int)$exclude_id);
        }
        return $this->db->count_all_results() > 0;
    }
}