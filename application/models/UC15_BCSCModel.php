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
        return $this->db->select('ir.id, ir.user_id, ir.id_machine, ir.id_planshift, ir.category, ir.severity_level, ir.incident_description, ir.media_path, ir.status, ir.assignee_id, ir.resolution_notes, ir.resolved_at, ir.created_at, ir.updated_at, u.username as user_name, m.machine_name, a.username as assignee_name')
            ->from($this->table . ' ir')
            ->join('user u', 'ir.user_id = u.user_id', 'left')
            ->join('machine m', 'ir.id_machine = m.id_machine', 'left')
            ->join('user a', 'ir.assignee_id = a.user_id', 'left')
            ->order_by('ir.created_at', 'DESC')
            ->get()
            ->result();
    }

    /**
     * Get incident by ID with details
     */
    public function get_by_id($id)
    {
        return $this->db->select('ir.*, u.username as user_name, m.machine_name, a.username as assignee_name')
            ->from($this->table . ' ir')
            ->join('user u', 'ir.user_id = u.user_id', 'left')
            ->join('machine m', 'ir.id_machine = m.id_machine', 'left')
            ->join('user a', 'ir.assignee_id = a.user_id', 'left')
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

    /**
     * Get incident reports by category
     */
    public function get_by_category($category)
    {
        return $this->db->where('category', $category)
            ->order_by('severity_level', 'DESC')
            ->order_by('created_at', 'DESC')
            ->get($this->table)
            ->result();
    }

    /**
     * Get incident reports by severity level
     */
    public function get_by_severity($severity)
    {
        return $this->db->where('severity_level', $severity)
            ->order_by('created_at', 'DESC')
            ->get($this->table)
            ->result();
    }

    /**
     * Get high priority incidents (severity >= 3 and status != 1)
     */
    public function get_high_priority()
    {
        return $this->db->where('severity_level >=', 3)
            ->where('status !=', 1)
            ->order_by('severity_level', 'DESC')
            ->order_by('created_at', 'DESC')
            ->get($this->table)
            ->result();
    }

    /**
     * Get incidents assigned to a user
     */
    public function get_assigned_to($user_id)
    {
        return $this->db->where('assignee_id', $user_id)
            ->where('status !=', 1)
            ->order_by('severity_level', 'DESC')
            ->order_by('created_at', 'DESC')
            ->get($this->table)
            ->result();
    }

    /**
     * Get incident statistics
     */
    public function get_statistics()
    {
        return [
            'total' => $this->count_all(),
            'pending' => $this->count_pending(),
            'in_progress' => $this->db->where('status', 2)->count_all_results($this->table),
            'completed' => $this->count_completed(),
            'critical' => $this->db->where('severity_level', 4)->count_all_results($this->table),
            'high' => $this->db->where('severity_level', 3)->count_all_results($this->table),
        ];
    }
}

