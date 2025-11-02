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
        return $this->session->userdata('role');
    }

    /**
     * Check login và JOIN với bảng roles để lấy role_name
     * Hỗ trợ RBAC mới (role_id FK) + backward compatible với role text cũ
     */
    public function check_login($table, $field1, $field2)
    {
        // JOIN với bảng roles để lấy role_name từ role_id
        $this->db->select('u.*, r.role_name');
        $this->db->from($table . ' u');
        $this->db->join('roles r', 'u.role_id = r.role_id', 'left');
        $this->db->where($field1);
        $this->db->where($field2);
        $this->db->limit(1);
        
        $query = $this->db->get();
        
        if ($query->num_rows() == 0) {
            return false;
        } else {
            $result = $query->result();
            
            // Nếu có role_name từ JOIN, dùng nó (ưu tiên RBAC mới)
            // Ngược lại dùng cột role text cũ (backward compatible)
            foreach ($result as $row) {
                if (!empty($row->role_name)) {
                    $row->role = $row->role_name; // Override role text bằng role_name từ FK
                }
            }
            
            return $result;
        }
    }
}
