<?php
defined('BASEPATH') or exit('No direct script access allowed');

class PersonnelModel extends CI_Model
{
    private $table = 'personnel';

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function get_all_personnel()
    {
        return $this->db->get($this->table)->result();
    }

    public function get_personnel($id)
    {
        return $this->db->get_where($this->table, ['id' => $id])->row();
    }

    public function add_personnel($data)
    {
        return $this->db->insert($this->table, $data);
    }

    public function update_personnel($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update($this->table, $data);
    }

    public function delete_personnel($id)
    {
        return $this->db->delete($this->table, ['id' => $id]);
    }
}