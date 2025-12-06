<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class WarehouseModel extends CI_Model {

    public function __construct()
    {
        parent::__construct();
    }

    // Return simple stock summary grouped by product
    public function getStockSummary()
    {
        // Defensive: check table exists
        if (!$this->db->table_exists('finished_stock')) {
            return [];
        }

        return $this->db->select('product_id, SUM(qty) as qty')
            ->from('finished_stock')
            ->group_by('product_id')
            ->get()
            ->result();
    }

    public function getQcPassedClosures()
    {
        // Look for closures or sessions that are DECIDED and APPROVE
        if ($this->db->table_exists('qc_sessions')) {
            return $this->db->where('status','DECIDED')->where('result','APPROVE')->get('qc_sessions')->result();
        }
        return [];
    }

    public function createReceipt($closure_id, $qty, $note)
    {
        if (!$this->db->table_exists('finished_receipt')) return false;

        $data = [
            'closure_id' => $closure_id,
            'qty' => $qty,
            'note' => $note,
            'created_by' => $this->session->userdata('username'),
            'created_at' => date('Y-m-d H:i:s')
        ];

        $this->db->insert('finished_receipt', $data);
        $receipt_id = $this->db->insert_id();

        // Update stock (simple increment)
        if ($receipt_id && $this->db->table_exists('finished_stock')) {
            // naive update: assume finished_stock has product_id from closure mapping
            $closure = $this->db->where('id',$closure_id)->get('qc_sessions')->row();
            $product_id = $closure->product_id ?? null;
            if ($product_id) {
                $this->db->set('qty', 'qty + ' . (int)$qty, FALSE)
                    ->where('product_id', $product_id)
                    ->update('finished_stock');
            }
        }

        return $receipt_id;
    }

    public function getReceiptById($id)
    {
        if (!$this->db->table_exists('finished_receipt')) return null;
        return $this->db->where('id',$id)->get('finished_receipt')->row();
    }

    public function getReceipts()
    {
        if (!$this->db->table_exists('finished_receipt')) return [];
        return $this->db->order_by('created_at','DESC')->get('finished_receipt')->result();
    }

    public function getOpenProjects()
    {
        if (!$this->db->table_exists('project')) return [];
        return $this->db->where('pr_status !=', 3)->get('project')->result();
    }

    public function getAvailableStockByProduct($product_id)
    {
        if (!$this->db->table_exists('finished_stock')) return 0;
        $row = $this->db->select('SUM(qty) as qty')->where('product_id',$product_id)->get('finished_stock')->row();
        return (int) ($row->qty ?? 0);
    }

    public function createDelivery($project_id, $product_id, $qty, $note)
    {
        if (!$this->db->table_exists('finished_delivery')) return false;

        $data = [
            'project_id' => $project_id,
            'product_id' => $product_id,
            'qty' => $qty,
            'note' => $note,
            'created_by' => $this->session->userdata('username'),
            'created_at' => date('Y-m-d H:i:s')
        ];

        $this->db->insert('finished_delivery', $data);
        $delivery_id = $this->db->insert_id();

        // decrement stock if table exists
        if ($delivery_id && $this->db->table_exists('finished_stock')) {
            $this->db->set('qty', 'qty - ' . (int)$qty, FALSE)
                ->where('product_id', $product_id)
                ->update('finished_stock');
        }

        return $delivery_id;
    }

    public function getDeliveryById($id)
    {
        if (!$this->db->table_exists('finished_delivery')) return null;
        return $this->db->where('id',$id)->get('finished_delivery')->row();
    }

    public function getDeliveries()
    {
        if (!$this->db->table_exists('finished_delivery')) return [];
        return $this->db->order_by('created_at','DESC')->get('finished_delivery')->result();
    }
}
