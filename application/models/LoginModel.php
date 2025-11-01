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

    public function check_login($table, $field1, $field2)
    {
        $this->db->select('*');
        $this->db->from($table);
        $this->db->where('username', $field1['username']);
        $this->db->where('password', $field2['password']);
        $this->db->limit(1);
        $query = $this->db->get();
        if ($query->num_rows() == 0) {
            return false;
        } else {
            return $query->result();
        }
    }
}
