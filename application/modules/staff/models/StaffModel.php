<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class StaffModel extends CI_Model {
    private $table = 'staff';

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    // Lấy danh sách nhân sự
    public function getAll() {
        return $this->db->get($this->table)->result();
    }

    // Lấy thông tin một nhân sự
    public function getById($id) {
        return $this->db->where('id_staff', $id)->get($this->table)->row();
    }

    // Thêm nhân sự mới
    public function insert($data) {
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    // Cập nhật thông tin nhân sự
    public function update($id, $data) {
        // Log thông tin debug
        log_message('debug', 'Updating staff with ID: ' . $id);
        log_message('debug', 'Update data: ' . print_r($data, true));
        
        $this->db->where('id_staff', $id);
        $result = $this->db->update($this->table, $data);
        
        // Kiểm tra xem việc cập nhật có thành công không
        if ($result) {
            // Refresh data từ database
            $this->db->reset_query();
            // Log kết quả thành công
            log_message('debug', 'Staff update successful');
            return true;
        }
        // Log lỗi nếu cập nhật thất bại
        log_message('error', 'Staff update failed. Database error: ' . $this->db->error()['message']);
        return false;
    }

    // Xóa nhân sự
    public function delete($id) {
        return $this->db->where('id_staff', $id)->delete($this->table);
    }

    // Tìm kiếm nhân sự
    public function search($keyword) {
        $this->db->like('staff_name', $keyword);
        return $this->db->get($this->table)->result();
    }
}
