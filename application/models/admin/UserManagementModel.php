<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class UserManagementModel extends CI_Model
{
    protected $table = 'user';

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function getAllUsers($filters = [])
    {
        $this->db->select('user.*, roles.role_name, staff.staff_name');
        $this->db->from($this->table);
        $this->db->join('roles', 'user.role_id = roles.role_id', 'left');
        $this->db->join('staff', 'user.staff_id = staff.id_staff', 'left');

        if (!empty($filters['role'])) {
            $this->db->where('user.role_id', $filters['role']);
        }

        if (isset($filters['status']) && $filters['status'] !== '') {
            $this->db->where('user.status', $filters['status']);
        }

        if (!empty($filters['search'])) {
            $this->db->group_start()
                ->like('user.username', $filters['search'])
                ->or_like('user.email', $filters['search'])
                ->or_like('staff.staff_name', $filters['search'])
                ->group_end();
        }

        $this->db->order_by('user.created_at', 'DESC');
        return $this->db->get()->result();
    }

    public function getUserById($user_id)
    {
        $this->db->select('user.*, roles.role_name, staff.staff_name');
        $this->db->from($this->table);
        $this->db->join('roles', 'user.role_id = roles.role_id', 'left');
        $this->db->join('staff', 'user.staff_id = staff.id_staff', 'left');
        $this->db->where('user.user_id', $user_id);
        return $this->db->get()->row();
    }

    public function getRoles()
    {
        return $this->db->get('roles')->result();
    }

    public function getStatistics()
    {
        $stats = [];

        $stats['total'] = $this->db->count_all($this->table);
        $stats['active'] = $this->db->where('status', 1)->count_all_results($this->table);
        $stats['inactive'] = $stats['total'] - $stats['active'];

        return $stats;
    }

    public function getStaffWithoutUser()
    {
        $this->db->select('staff.id_staff, staff.staff_name, staff.position, staff.staff_group');
        $this->db->from('staff');
        $this->db->where('staff.st_status', 1); // Only active staff
        $this->db->where('staff.id_staff NOT IN (SELECT staff_id FROM user WHERE staff_id IS NOT NULL)', null, false);
        $this->db->order_by('staff.staff_name');
        return $this->db->get()->result();
    }

    public function createUser($data, $created_by)
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        $data['status'] = 1; // Default active

        $this->db->insert($this->table, $data);
        $user_id = $this->db->insert_id();

        if ($user_id) {
            $this->logAudit($created_by, 'create', 'user', $user_id, null, $data);
            return ['success' => true, 'message' => 'User created successfully.', 'user_id' => $user_id];
        } else {
            return ['success' => false, 'message' => 'Failed to create user.'];
        }
    }

    public function updateUser($id, $data, $updated_by)
    {
        $old_data = $this->getUserById($id);
        if (!$old_data) {
            return ['success' => false, 'message' => 'User not found.'];
        }

        $data['updated_at'] = date('Y-m-d H:i:s');

        $this->db->where('user_id', $id);
        $updated = $this->db->update($this->table, $data);

        if ($updated) {
            $this->logAudit($updated_by, 'update', 'user', $id, $old_data, $data);
            return ['success' => true, 'message' => 'User updated successfully.'];
        } else {
            return ['success' => false, 'message' => 'Failed to update user.'];
        }
    }

    public function deleteUser($id, $deleted_by)
    {
        $old_data = $this->getUserById($id);
        if (!$old_data) {
            return ['success' => false, 'message' => 'User not found.'];
        }

        $this->db->where('user_id', $id);
        $deleted = $this->db->delete($this->table);

        if ($deleted) {
            $this->logAudit($deleted_by, 'delete', 'user', $id, $old_data, null);
            return ['success' => true, 'message' => 'User deleted successfully.'];
        } else {
            return ['success' => false, 'message' => 'Failed to delete user.'];
        }
    }

    public function toggleUserStatus($id, $updated_by)
    {
        $user = $this->getUserById($id);
        if (!$user) {
            return ['success' => false, 'message' => 'User not found.'];
        }

        $new_status = $user->status == 1 ? 0 : 1;
        $old_data = $user;
        $new_data = ['status' => $new_status, 'updated_at' => date('Y-m-d H:i:s')];

        $this->db->where('user_id', $id);
        $updated = $this->db->update($this->table, $new_data);

        if ($updated) {
            $this->logAudit($updated_by, 'update', 'user', $id, $old_data, $new_data);
            return ['success' => true, 'message' => 'User status updated successfully.'];
        } else {
            return ['success' => false, 'message' => 'Failed to update user status.'];
        }
    }

    private function logAudit($user_id, $action, $module, $record_id, $old_value, $new_value)
    {
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
}