<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class UC15_BCSCModel extends CI_Model {

    protected $table = 'incident_reports';

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Get all incident reports with staff and machine details
     */
    public function get_all()
    {
        return $this->db->select('ir.id, ir.user_id, ir.id_machine, ir.id_planshift, ir.incident_description, ir.media_path, ir.status, ir.created_at, ir.updated_at, u.username as user_name, m.machine_name')
            ->from($this->table . ' ir')
            ->join('user u', 'ir.user_id = u.user_id', 'left')
            ->join('machine m', 'ir.id_machine = m.id_machine', 'left')
            ->order_by('ir.created_at', 'DESC')
            ->get()
            ->result();
    }

    /**
     * Get incident by ID with details
     */
    public function get_by_id($id)
    {
        return $this->db->select('ir.*, u.username as user_name, m.machine_name')
            ->from($this->table . ' ir')
            ->join('user u', 'ir.user_id = u.user_id', 'left')
            ->join('machine m', 'ir.id_machine = m.id_machine', 'left')
            ->where('ir.id', $id)
            ->get()
            ->row();
    }

    /**
     * Insert new incident report
     */
    public function insert($data)
    {
        return $this->db->insert($this->table, $data);
    }

    /**
     * Update incident report
     */
    public function update($id, $data)
    {
        return $this->db->where('id', $id)->update($this->table, $data);
    }

    /**
     * Delete incident report
     */
    public function delete($id)
    {
        return $this->db->where('id', $id)->delete($this->table);
    }

    /**
     * Get incident reports by user
     */
    public function get_by_user($user_id)
    {
        return $this->db->where('user_id', $user_id)
            ->order_by('created_at', 'DESC')
            ->get($this->table)
            ->result();
    }

    /**
     * Get incident reports by machine
     */
    public function get_by_machine($machine_id)
    {
        return $this->db->where('id_machine', $machine_id)
            ->order_by('created_at', 'DESC')
            ->get($this->table)
            ->result();
    }

    /**
     * Get incident reports by status
     */
    public function get_by_status($status)
    {
        return $this->db->where('status', $status)
            ->order_by('created_at', 'DESC')
            ->get($this->table)
            ->result();
    }

    /**
     * Count total incidents
     */
    public function count_all()
    {
        return $this->db->count_all($this->table);
    }

    /**
     * Count pending incidents (status = 0)
     */
    public function count_pending()
    {
        return $this->db->where('status', 0)->count_all_results($this->table);
    }

    /**
     * Count completed incidents (status = 1)
     */
    public function count_completed()
    {
        return $this->db->where('status', 1)->count_all_results($this->table);
    }
}
