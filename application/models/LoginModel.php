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

    /**
     * Check login - backward compatible với database cũ
     */
    public function check_login($table, $field1, $field2)
    {
        $this->db->select('*');
        $this->db->from($table);
        
        // Handle field1 and field2 as arrays or single conditions
        if (is_array($field1)) {
            foreach ($field1 as $key => $value) {
                $this->db->where($key, $value);
            }
        } else {
            $this->db->where($field1);
        }
        
        if (is_array($field2)) {
            foreach ($field2 as $key => $value) {
                $this->db->where($key, $value);
            }
        } else {
            $this->db->where($field2);
        }
        
        $this->db->limit(1);
        
        $query = $this->db->get();
        
        if ($query->num_rows() == 0) {
            return false;
        }
        
        return $query->result();
    }

    /**
     * Get user by ID - backward compatible
     */
    public function get_user_by_id($user_id)
    {
        $this->db->select('*');
        $this->db->from('user');
        $this->db->where('user_id', $user_id);
        $query = $this->db->get();
        
        return $query->row();
    }

    /**
     * Check whether the given staff (by staff_id) has an active shift right now.
     */
    public function has_active_shift($staff_id)
    {
        if (empty($staff_id)) return true;

        // If plan_shift table is not present, do not block login
        if (!$this->db->table_exists('plan_shift')) {
            return true;
        }

        $now = date('Y-m-d H:i:s');

        // 1) If plan_shift stores start_time / end_time directly
        if ($this->db->field_exists('start_time', 'plan_shift') && $this->db->field_exists('end_time', 'plan_shift')) {
            $this->db->from('plan_shift');
            $this->db->where('id_staff', $staff_id);
            $this->db->where("start_time <= '".$now."'");
            $this->db->where("end_time >= '".$now."'");
            if ($this->db->count_all_results() > 0) return true;
        }

        // 2) If plan_shift references shiftment and shiftment has start/end times
        if ($this->db->field_exists('id_shift', 'plan_shift') && $this->db->table_exists('shiftment')) {
            $shiftStartCols = ['start_time', 'time_start', 'begin_time'];
            $shiftEndCols = ['end_time', 'time_end', 'finish_time'];

            foreach ($shiftStartCols as $sCol) {
                foreach ($shiftEndCols as $eCol) {
                    if ($this->db->field_exists($sCol, 'shiftment') && $this->db->field_exists($eCol, 'shiftment')) {
                        $this->db->from('plan_shift p');
                        $this->db->join('shiftment s', 's.id = p.id_shift');
                        $this->db->where('p.id_staff', $staff_id);
                        $this->db->where("p.shift_date = '".date('Y-m-d')."'", null, false);
                        $this->db->where("s.$sCol <= '".$now."'", null, false);
                        $this->db->where("s.$eCol >= '".$now."'", null, false);
                        if ($this->db->count_all_results() > 0) return true;
                        break 2;
                    }
                }
            }
        }

        // 3) Fallback: if plan_shift contains a date column (shift_date) and there is an entry today
        if ($this->db->field_exists('shift_date', 'plan_shift')) {
            $cnt = $this->db->where('id_staff', $staff_id)->where('shift_date', date('Y-m-d'))->count_all_results('plan_shift');
            if ($cnt > 0) return true;
        }

        return false;
    }

    /**
     * Update last login timestamp (if column exists)
     */
    public function update_last_login($user_id)
    {
        // Only update if last_login column exists
        if (!$this->db->field_exists('last_login', 'user')) {
            return;
        }
        
        $this->db->where('user_id', $user_id);
        $this->db->update('user', ['last_login' => date('Y-m-d H:i:s')]);
    }

    /**
     * Log user activity to audit_log (if table exists)
     */
    public function log_activity($user_id, $username, $action, $module = 'auth', $record_id = null, $old_value = null, $new_value = null)
    {
        // Only log if audit_log table exists
        if (!$this->db->table_exists('audit_log')) {
            return;
        }
        
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
