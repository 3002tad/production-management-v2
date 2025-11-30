<?php

defined('BASEPATH') or exit('No direct script access allowed');

class IncidentModel extends CI_Model
{
    protected $table = 'incident_reports';

    public function __construct()
    {
        parent::__construct();
    }

    public function get_all($limit = 100, $offset = 0)
    {
        return $this->db->order_by('created_at', 'DESC')->get($this->table, $limit, $offset)->result();
    }

    public function get_by_id($id)
    {
        return $this->db->where('id', $id)->get($this->table)->row();
    }

    public function add($data)
    {
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    public function update($id, $data)
    {
        return $this->db->where('id', $id)->update($this->table, $data);
    }

    public function delete($id)
    {
        return $this->db->where('id', $id)->delete($this->table);
    }
}
