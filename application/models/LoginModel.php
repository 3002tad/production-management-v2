<?php

defined('BASEPATH') or exit('No direct script access allowed');

class LoginModel extends CI_Model
{
    public function is_logged_in()
    {
        return $this->session->userdata('user_id');
    }

    public function is_role()
    {
        // Support both old and new role system during migration
        $role_name = $this->session->userdata('role_name');
        return $role_name ? $role_name : $this->session->userdata('role');
    }

        public function check_login($username, $password)
{
            // Query từ bảng user
            $this->db->select('u.user_id, u.username, u.password, u.role_id, u.full_name, u.email, u.phone, u.is_active, u.temp_password, u.must_change_password, u.staff_id');
        $this->db->select('r.role_name, r.role_display_name, r.level, r.description as role_description');
        $this->db->select('s.department, s.position');
            $this->db->from('user u');
            $this->db->join('roles r', 'r.role_id = u.role_id', 'left');
            $this->db->join('staff s', 's.id_staff = u.staff_id', 'left');
        $this->db->where('u.username', $username);
        // Accept either stored password or temp_password (admin reset). Keep is_active check in controller to show locked message explicitly.
        $this->db->where("(u.password = " . $this->db->escape($password) . " OR u.temp_password = " . $this->db->escape($password) . ")", NULL, FALSE);
            
            $query = $this->db->get();
                        return $query->row();
            }

    /**
     * Get user by ID with role information
     */
    public function get_user_by_id($user_id)
    {
        if ($this->db->table_exists('roles')) {
            $this->db->select('
                u.*,
                r.role_name,
                r.role_display_name,
                r.level
            ');
            $this->db->from('user u');
            $this->db->join('roles r', 'r.role_id = u.role_id', 'left');
            $this->db->where('u.user_id', $user_id);
            $query = $this->db->get();

            return $query->row();
        }

        // Legacy fallback: roles table missing
        $this->db->select('u.*');
        $this->db->from('user u');
        $this->db->where('u.user_id', $user_id);
        $query = $this->db->get();

        $row = $query->row();
        if ($row) {
            // expose role_name for compatibility
            $row->role_name = isset($row->role) ? $row->role : null;
            $row->role_display_name = null;
            $row->level = null;
        }

        return $row;
    }

    /**
     * Update last login timestamp
     */
    public function update_last_login($user_id)
    {
        $this->db->where('user_id', $user_id);
        $this->db->update('user', ['last_login' => date('Y-m-d H:i:s')]);
    }

    /**
     * Log user activity to audit_log
     */
    public function log_activity($user_id, $username, $action, $module = 'auth', $record_id = null, $old_value = null, $new_value = null)
    {
        $data = [
            'user_id' => $user_id,
            'username' => $username,
            'action' => $action,
            'module' => $module,
            'record_id' => $record_id,
            'old_value' => $old_value ? json_encode($old_value) : null,
            'new_value' => $new_value ? json_encode($new_value) : null,
            'ip_address' => $this->input->ip_address(),
            'user_agent' => $this->input->user_agent()
        ];
        
        $this->db->insert('audit_log', $data);
    }
}
