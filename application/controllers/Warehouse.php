<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Warehouse extends CI_Controller
{
    /**
     * @var CI_DB_query_builder
     */
    public $db;

    /**
     * @var CI_Session
     */
    public $session;

    /**
     * @var CI_URI
     */
    public $uri;

    /**
     * @var CI_Input
     */
    public $input;

    /**
     * @var CI_Form_validation
     */
    public $form_validation;

    /**
     * @var LoginModel
     */
    public $login;

    /**
     * @var CrudModel
     */
    public $crudModel;
    /**
     * @var CI_Upload
     */
    public $upload;
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->model('CrudModel', 'crudModel');
        $this->load->model('LoginModel', 'login');
        $this->load->library('session');
        $this->load->helper('language');
        $this->load->language('app');

        // Accept new RBAC role_name values or legacy 'role'
        $role_name = $this->session->userdata('role_name') ?: $this->session->userdata('role');
        $level = $this->session->userdata('level');

        $allowed = ['bod', 'system_admin', 'warehouse_staff'];

        if (! (in_array($role_name, $allowed) || ($level !== null && $level >= 90)) ) {
            redirect('login/');
        }
    }

    public function index()
    {
        $data = [
            'materials' => $this->db->query('SELECT * FROM material')->result(),
            'total_materials' => $this->crudModel->getData('material')->num_rows(),

            'content' => 'warehouse/beranda',
            'navlink' => 'beranda',
        ];

        $this->load->view('warehouse/vbackend', $data);
    }

    public function material_entry()
    {
        if ($this->uri->segment(3) === 'add') {
            $data = [
                'materials' => $this->db->query('SELECT * FROM material')->result(),
                'content' => 'warehouse/material_entry/add',
                'navlink' => 'material_entry',
            ];

            $this->load->view('warehouse/vbackend', $data);
        } 
        else if ($this->uri->segment(4) === 'view') 
        {
            $id = $this->uri->segment(3);
            $material = $this->crudModel->getDataWhere('material', 'id_material', $id)->row();
            
            $data = [
                'detail' => $material,
                'content' => 'warehouse/material_entry/view',
                'navlink' => 'material_entry',
            ];

            $this->load->view('warehouse/vbackend', $data);
        }
        else 
        {
            $data = [
                'materials' => $this->db->query('SELECT * FROM material')->result(),
                'content' => 'warehouse/material_entry/list',
                'navlink' => 'material_entry',
            ];

            $this->load->view('warehouse/vbackend', $data);
        }
    }

    public function save_material_entry()
    {
        // Get input data and validate
        $material_name = trim($this->input->post('material_name'));
        $quantity = intval($this->input->post('quantity'));
    $date_entry = $this->input->post('date_entry');
    $supplier = trim($this->input->post('supplier'));
    $material_type = trim($this->input->post('material_type'));
    $unit = trim($this->input->post('unit'));
    $confirm_over_max = $this->input->post('confirm_over_max') ? true : false;
    $confirm_unit_mismatch = $this->input->post('confirm_unit_mismatch') ? true : false;

        // Validate input
        if (empty($material_name)) {
            $this->session->set_flashdata('error', 'Tên vật liệu không được để trống');
            redirect('warehouse/material_entry/add');
            return;
        }

        if ($quantity <= 0) {
            $this->session->set_flashdata('error', 'Số lượng phải lớn hơn 0');
            redirect('warehouse/material_entry/add');
            return;
        }

        // Check if material exists
        $existing_material = $this->crudModel->getDataWhere('material', 'material_name', $material_name)->row();

        // Basic unit expectations per material type
        $expected_units = [
            'Ink' => ['ml','g'],
            'Ball' => ['cái','set'],
            'Spring' => ['cái'],
            'Raw Material' => ['kg','g','cái'],
            'Other' => ['cái','kg','g','ml','set']
        ];

        // Determine current stock and thresholds
        $current_stock = $existing_material ? (int)($existing_material->stock ?? 0) : 0;
        // Prefer per-item max_stock if available, otherwise default
        $default_max = 10000;
        $max_stock = $existing_material && property_exists($existing_material, 'max_stock') && !empty($existing_material->max_stock)
            ? (int)$existing_material->max_stock
            : $default_max;

        $new_stock = $current_stock + $quantity;

        // Unit validation: if material_type defined, check expected units
        if (!empty($material_type) && !empty($unit)) {
            $allowed = $expected_units[$material_type] ?? $expected_units['Other'];
            if (!in_array($unit, $allowed)) {
                // Special auto-correct: Ink + cái => ml
                if ($material_type === 'Ink' && $unit === 'cái') {
                    $unit = 'ml';
                    $this->session->set_flashdata('info', 'Đơn vị cho "Mực" được tự động chuyển sang "ml".');
                } elseif (! $confirm_unit_mismatch) {
                    $this->session->set_flashdata('error', "Đơn vị '" . $unit . "' không hợp lệ cho loại vật liệu '" . $material_type . "'. Nếu chắc chắn, tick 'Tôi xác nhận đơn vị nhập có thể khác' và thử lại.");
                    redirect('warehouse/material_entry/add');
                    return;
                }
            }
        }

        // Max stock validation: if new_stock exceeds configured max and not confirmed, stop and ask for confirmation
        if ($new_stock > $max_stock && ! $confirm_over_max) {
            $this->session->set_flashdata('error', "Số lượng sau khi nhập (" . $new_stock . ") vượt quá mức tối đa cho phép ({$max_stock}). Nếu bạn chắc chắn muốn tiếp tục, tick 'Tôi xác nhận muốn nhập vượt mức quy định' và thử lại.");
            redirect('warehouse/material_entry/add');
            return;
        }
        // Handle file upload for attachment (if provided)
        $attachment_path = null;
        if (!empty($_FILES) && isset($_FILES['attachment']) && $_FILES['attachment']['size'] > 0) {
            $upload_path = FCPATH . 'asset/uploads/pending/';
            if (!is_dir($upload_path)) {
                @mkdir($upload_path, 0755, true);
            }

            $config['upload_path'] = $upload_path;
            $config['allowed_types'] = 'pdf|jpg|jpeg|png|doc|docx';
            $config['max_size'] = 5120; // 5MB
            $config['encrypt_name'] = true;

            $this->load->library('upload', $config);
            if ($this->upload->do_upload('attachment')) {
                $up = $this->upload->data();
                // Save relative path for web use
                $attachment_path = 'asset/uploads/pending/' . $up['file_name'];
            } else {
                // upload failed; set flash but continue saving other fields
                $this->session->set_flashdata('error', 'Upload file thất bại: ' . $this->upload->display_errors('', ''));
                redirect('warehouse/material_entry/add');
                return;
            }
        }

        try {
            if ($existing_material) {
                // Update existing material stock and optional fields
                $update = [
                    'stock' => "stock + {$quantity}"
                ];

                // Build query to update numeric stock and other provided fields
                $this->db->set('stock', 'stock + ' . (int)$quantity, false);
                if (!empty($date_entry)) $this->db->set('date_entry', $date_entry);
                if (!empty($supplier)) $this->db->set('supplier', $supplier);
                if (!empty($attachment_path)) $this->db->set('attachment', $attachment_path);
                if (!empty($material_type)) $this->db->set('material_type', $material_type);
                if (!empty($unit)) $this->db->set('unit', $unit);
                $this->db->where('material_name', $material_name);
                $this->db->update('material');
            } else {
                // Insert new material with details
                $material_data = [
                    'material_name' => $material_name,
                    'stock' => $quantity,
                    'date_entry' => (!empty($date_entry) ? $date_entry : null),
                    'supplier' => (!empty($supplier) ? $supplier : null),
                    'attachment' => $attachment_path,
                    'material_type' => (!empty($material_type) ? $material_type : null),
                    'unit' => (!empty($unit) ? $unit : null),
                ];
                $this->crudModel->addData('material', $material_data);
            }

            $this->session->set_flashdata('success', 'Cập nhật vật liệu thành công');
            redirect('warehouse/material_entry');
        } catch (Exception $e) {
            $this->session->set_flashdata('error', 'Có lỗi xảy ra: ' . $e->getMessage());
            redirect('warehouse/material_entry/add');
        }
    }

    public function report()
    {
        $data = [
            'materials' => $this->db->query('SELECT * FROM material')->result(),
            'content' => 'warehouse/report',
            'navlink' => 'report',
        ];

        $this->load->view('warehouse/vbackend', $data);
    }
}