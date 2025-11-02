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
        return $this->session->userdata('role_name');
    }

    public function check_login($table, $field1, $field2)
    {
        $this->db->select('user.*, roles.role_name');
        $this->db->from('user');
        $this->db->join('roles', 'user.role_id = roles.role_id', 'left');
        $this->db->where($field1);
        $this->db->where($field2);
        $this->db->where('user.is_active', 1); // chỉ cho phép user active
        $this->db->limit(1);

        $query = $this->db->get();

        if ($query->num_rows() == 0) {
            return false;
        } else {
            $user = $query->row();

            // cập nhật thời gian last login
            $this->db->where('user_id', $user->user_id);
            $this->db->update('user', ['last_login' => date('Y-m-d H:i:s')]);

            return $user; // trả về object user
        }
    }

    public function get_user_role($role_id)
    {
        $this->db->select('role_name');
        $this->db->from('roles');
        $this->db->where('role_id', $role_id);

        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            return $query->row()->role_name;
        }

        return false;
    }
}
?>
