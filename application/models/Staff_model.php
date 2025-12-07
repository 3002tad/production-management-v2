<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Staff_model extends CI_Model {
    /**
     * Kiểm tra quyền thao tác của user hiện tại
     * @param string $action: create|update|delete|set_active
     * @return bool
     */
    private function can_leader_action($action)
    {
        $role = $this->session->userdata('role');
        $role = strtolower(trim($role));
        if ($role === 'leader') {
            return true;
        }
        // Các quyền khác chỉ được xem
        return false;
    }

    protected $table = 'staff';

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function search($code = null, $skill = null)
    {
        $this->db->from($this->table);
        if ($code) {
            $this->db->where('code', $code);
        }
        if ($skill) {
            $this->db->like('skill', $skill);
        }
        $query = $this->db->get();
        return $query->result();
    }

    public function exists_code($code)
    {
        return $this->db->where('code', $code)->count_all_results($this->table) > 0;
    }

    /**
     * Check whether a phone exists in the staff table.
     * @param string $phone Input phone (may contain non-digits)
     * @param int|null $exclude_id Optional staff id to exclude from check (useful on update)
     * @return bool
     */
    public function exists_phone($phone, $exclude_id = null)
    {
        if (empty($phone)) return false;
        // normalize input: keep digits only
        $clean = preg_replace('/\D+/', '', $phone);
        if ($clean === '') return false;

        // Compare stored phone after removing common non-digit characters
        $expr = "REPLACE(REPLACE(REPLACE(REPLACE(phone, ' ', ''), '-', ''), '(', ''), ')', '')";
        $this->db->from($this->table);
        $this->db->where("{$expr} =", $clean);
        if (!is_null($exclude_id)) {
            $this->db->where('id <>', (int)$exclude_id);
        }
        return $this->db->count_all_results() > 0;
    }
    public function create($data)
    {
        if (!$this->can_leader_action('create')) {
            return false; // Chỉ leader mới được thêm
        }
        $row = [
            'code' => $data['code'],
            'name' => $data['name'],
            'skill' => isset($data['skill']) ? $data['skill'] : null,
            'phone' => isset($data['phone']) ? preg_replace('/\D+/', '', $data['phone']) : null,
            'active' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ];
        return $this->db->insert($this->table, $row);
    }

    public function get($id)
    {
        if (!$id) return null;
        return $this->db->where('id', $id)->get($this->table)->row();
    }

    public function update($id, $data)
    {
        if (!$this->can_leader_action('update')) {
            return false; // Chỉ leader mới được sửa
        }
        $row = [];
        if (isset($data['code'])) $row['code'] = $data['code'];
        if (isset($data['name'])) $row['name'] = $data['name'];
        if (isset($data['skill'])) $row['skill'] = $data['skill'];
        if (isset($data['phone'])) $row['phone'] = preg_replace('/\D+/', '', $data['phone']);
        if (isset($data['active'])) $row['active'] = $data['active'];
        if (empty($row)) return false;
        $this->db->where('id', $id);
        return $this->db->update($this->table, $row);
    }

    public function skill_exists($skill)
    {
        // If project has a skills table, check it. If not, consider existence by non-empty.
        if (empty($skill)) return false;
        if ($this->db->table_exists('skills')) {
            return $this->db->where('name', $skill)->count_all_results('skills') > 0;
        }
        // fallback: accept provided skills only if non-empty
        return true;
    }

    public function has_active_assignments($id)
    {
        // Try to detect assignment tables: planning, plan_shift or similar
        // We'll check common tables used by this project.
        $tables = ['planning1', 'plan_shift', 'p_material', 'p_machine'];
        foreach ($tables as $t) {
            if ($this->db->table_exists($t)) {
                $cnt = $this->db->where('staff_id', $id)->where('status <>', 'completed')->count_all_results($t);
                if ($cnt > 0) return true;
            }
        }
        return false;
    }

    public function set_active($id, $flag)
    {
        if (!$this->can_leader_action('set_active')) {
            return false; // Chỉ leader mới được cập nhật trạng thái
        }
        return $this->db->where('id', $id)->update($this->table, ['active' => (int)$flag]);
    }

    /**
     * Xóa nhân sự (chỉ leader)
     */
    public function delete($id)
    {
        if (!$this->can_leader_action('delete')) {
            return false; // Chỉ leader mới được xóa
        }
        return $this->db->where('id', $id)->delete($this->table);
    }
}

?>
