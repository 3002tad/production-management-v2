<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Warehouse extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('CrudModel', 'crudModel');
        $this->load->library('session');
        // Chỉ cho phép: role_id = 3 (warehouse) hoặc role = 'warehouse' hoặc admin
        $role = $this->session->userdata('role');
        $roleId = (int) $this->session->userdata('role_id');
        $isWarehouse = ($roleId === 3) || ($role === 'warehouse');
        $isAdmin = ($role === 'admin') || ($roleId === 4);
        if (!($isWarehouse || $isAdmin)) {
            redirect('login/');
        }
    }

    public function index()
    {
        // Tổng quan kho: dùng cùng nguồn dữ liệu như trang Admin->index
        $data = [
            'finished' => $this->db->query('SELECT * FROM finished_report JOIN project JOIN customer WHERE finished_report.id_project=project.id_project AND project.id_cust=customer.id_cust')->result(),

            'sorting' => $this->db->query('SELECT * FROM sorting_report JOIN plan_shift JOIN planning JOIN staff WHERE sorting_report.id_planshift=plan_shift.id_planshift AND plan_shift.id_staff=staff.id_staff AND plan_shift.id_plan=planning.id_plan')->result(),

            'project' => $this->crudModel->getData('project')->num_rows(),
            'planning' => $this->crudModel->getData('planning')->num_rows(),
            'plan_shift' => $this->crudModel->getData('plan_shift')->num_rows(),
            'finished_report' => $this->crudModel->getData('finished_report')->num_rows(),

            'content' => 'warehouse/Beranda',
            'navlink' => 'warehouse',
        ];
        // View layout của kho
        $this->load->view('warehouse/VBackend', $data);
    }

    // Nếu cần map thêm menu con, có thể bổ sung các action dưới đây
    public function material()
    {
        // Hành vi giống Admin::material
        if ($this->uri->segment(3) === 'addmaterial') {
            $data = [
                'planshift' => $this->db->query('SELECT * FROM plan_shift JOIN staff WHERE plan_shift.id_staff=staff.id_staff AND ps_status = 1 AND staff.st_status=2')->result(),
                'material' => $this->db->query('SELECT * FROM material')->result(),
                'content' => 'warehouse/material/AddMaterial',
                'navlink' => 'material',
            ];
        } else {
            $data = [
                'material' => $this->db->query('SELECT * FROM p_material JOIN plan_shift JOIN material JOIN staff WHERE p_material.id_material = material.id_material AND p_material.id_planshift = plan_shift.id_planshift AND plan_shift.id_staff = staff.id_staff')->result(),
                'materials' => $this->db->query('SELECT * FROM material')->result(),
                'content' => 'warehouse/material/Material',
                'navlink' => 'material',
            ];
        }
        $this->load->view('warehouse/VBackend', $data);
    }

    public function addNewMaterialForm()
    {
        $data = [
            'content' => 'warehouse/material/AddNewMaterial',
            'navlink' => 'material',
        ];
        $this->load->view('warehouse/VBackend', $data);
    }

    public function addNewMaterial()
    {
        // Xử lý lưu NVL mới (cho phép admin hoặc kho)
        $role = $this->session->userdata('role');
        $roleId = (int) $this->session->userdata('role_id');
        if (!($role === 'admin' || $roleId === 4 || $role === 'warehouse' || $roleId === 3)) {
            redirect('login/');
            return;
        }

        $id_material   = trim($this->input->post('id_material'));
        $material_name = trim($this->input->post('material_name'));
        $uom           = trim($this->input->post('uom'));
        $stock_raw     = $this->input->post('stock');
        $min_raw       = $this->input->post('min_stock');

        // Yêu cầu bắt buộc: Mã/Tên/UoM
        if ($id_material === '' || $material_name === '' || $uom === '') {
            $this->session->set_flashdata('material_alert', 'Thiếu dữ liệu bắt buộc (Mã/Tên/UoM)');
            $this->session->set_flashdata('material_alert_level', 'error');
            redirect(site_url('warehouse/material/addnewmaterial')); // quay lại form thêm mới NVL
            return;
        }

        // Ép kiểu và validate min_stock không âm
        $stock = ($stock_raw !== null && $stock_raw !== '') ? (int) $stock_raw : 0;
        $min_stock = ($min_raw !== null && $min_raw !== '') ? (int) $min_raw : 0;
        if ($min_stock < 0) {
            $this->session->set_flashdata('material_alert', 'Giá trị không hợp lệ');
            $this->session->set_flashdata('material_alert_level', 'error');
            redirect(site_url('warehouse/material/addnewmaterial'));
            return;
        }

        // Check trùng mã
        $exists = $this->crudModel->getDataWhere('material', 'id_material', $id_material)->num_rows() > 0;
        if ($exists) {
            $this->session->set_flashdata('material_alert', 'Mã NVL đã tồn tại');
            $this->session->set_flashdata('material_alert_level', 'error');
            redirect(site_url('warehouse/material/addnewmaterial'));
            return;
        }

        $add = [
            'id_material'   => $id_material,
            'material_name' => $material_name,
            'stock'         => $stock,
            'min_stock'     => $min_stock,
            'uom'           => $uom,
        ];

        $this->crudModel->addData('material', $add);

        // Thành công: không hiện alert popup (chỉ lưu thông báo nếu cần dùng nơi khác)
        $this->session->set_flashdata('material_alert', 'Lưu thành công');
        $this->session->set_flashdata('material_alert_level', 'success');
        redirect(site_url('warehouse/material'));
    }

    public function addMaterial()
    {
        $add = [
            'id_pmaterial' => $this->crudModel->generateCode(1, 'id_pmaterial', 'p_material'),
            'id_planshift' => trim($this->input->post('id_planshift')),
            'id_material' => trim($this->input->post('id_material')),
            'used_stock' => trim($this->input->post('used_stock')),
        ];

        $stock = $this->crudModel->getDataWhere('material', 'id_material', $add['id_material'])->row();

        $update = [
            'stock' => $stock->stock - (int) $add['used_stock'],
        ];

        $this->crudModel->updateData('material', 'id_material', $add['id_material'], $update);
        $this->crudModel->addData('p_material', $add);

        redirect(site_url('warehouse/material'));
    }

    public function editMaterial()
    {
        $id = $this->uri->segment(3);
        $tampil = $this->crudModel->getDataWhere('material', 'id_material', $id)->row();

        $data = [
            'detail' => [
                'id_material' => $tampil->id_material,
                'material_name' => $tampil->material_name,
                'stock' => $tampil->stock,
                'min_stock' => isset($tampil->min_stock) ? $tampil->min_stock : 0,
                'uom' => isset($tampil->uom) ? $tampil->uom : 'g',
            ],
            'content' => 'warehouse/material/UpdateMaterial',
            'navlink' => 'material',
        ];

        $this->load->view('warehouse/VBackend', $data);
    }

    public function updateMaterial()
    {
        $old_id = trim($this->input->post('old_id_material'));
        $new_id = trim($this->input->post('id_material'));
        $material_name = trim($this->input->post('material_name'));
        $stock = trim($this->input->post('stock'));
        $min_stock = ($this->input->post('min_stock') !== null && $this->input->post('min_stock') !== '') ? (int) $this->input->post('min_stock') : 0;
        $uom = trim($this->input->post('uom')) !== '' ? trim($this->input->post('uom')) : 'g';

        if ($new_id === '') {
            $new_id = $old_id;
        }

        // Validate bắt buộc và min_stock không âm
        if ($material_name === '' || $uom === '') {
            $this->session->set_flashdata('material_alert', 'Thiếu dữ liệu bắt buộc (Mã/Tên/UoM)');
            $this->session->set_flashdata('material_alert_level', 'error');
            redirect(site_url('warehouse/editMaterial/'.$old_id));
            return;
        }
        if ($min_stock < 0) {
            $this->session->set_flashdata('material_alert', 'Giá trị không hợp lệ');
            $this->session->set_flashdata('material_alert_level', 'error');
            redirect(site_url('warehouse/editMaterial/'.$old_id));
            return;
        }

        if ($new_id !== $old_id) {
            $exists = $this->crudModel->getDataWhere('material', 'id_material', $new_id)->num_rows() > 0;
            if ($exists) {
                $this->session->set_flashdata('material_alert', 'Mã NVL đã tồn tại');
                $this->session->set_flashdata('material_alert_level', 'error');
                redirect(site_url('warehouse/editMaterial/'.$old_id));
                return;
            }
        }

        $update = [
            'id_material'   => $new_id,
            'material_name' => $material_name,
            'stock'         => $stock,
            'min_stock'     => $min_stock,
            'uom'           => $uom,
        ];
    $this->crudModel->updateData('material', 'id_material', $old_id, $update);

        if ($new_id !== $old_id) {
            $this->crudModel->updateData('p_material', 'id_material', $old_id, ['id_material' => $new_id]);
        }

        $this->session->set_flashdata('material_alert', 'Lưu thành công');
        $this->session->set_flashdata('material_alert_level', 'success');
        redirect(site_url('warehouse/material'));
    }

    public function deleteMaterialMaster()
    {
        $id_material = $this->uri->segment(3);

        // Kiểm tra tham chiếu an toàn bằng binding tham số
        $ref_in_use = $this->db->query('SELECT 1 FROM p_material WHERE id_material = ?', [$id_material])->num_rows() > 0;
        if ($ref_in_use) {
            $this->session->set_flashdata('material_alert', 'Không thể xóa vì đang được sử dụng');
            $this->session->set_flashdata('material_alert_level', 'error');
            redirect(site_url('warehouse/material'));
            return;
        }

        $this->crudModel->deleteData('material', 'id_material', $id_material);
        $this->session->set_flashdata('material_alert', 'Xóa thành công');
        $this->session->set_flashdata('material_alert_level', 'success');
        redirect(site_url('warehouse/material'));
    }

    public function project()
    {
        $data = [
            'project' => $this->db->query('SELECT * FROM project JOIN customer JOIN product WHERE project.id_cust=customer.id_cust AND project.id_product=product.id_product')->result(),
            'content' => 'warehouse/project/Project',
            'navlink' => 'project',
        ];
        $this->load->view('warehouse/VBackend', $data);
    }

    public function sorting()
    {
        $data = [
            'sorting' => $this->db->query('SELECT sorting_report.*, planning.plan_name, staff.staff_name, shiftment.shift_name
                                            FROM sorting_report
                                            JOIN plan_shift ON sorting_report.id_planshift = plan_shift.id_planshift
                                            JOIN planning ON plan_shift.id_plan = planning.id_plan
                                            JOIN staff ON plan_shift.id_staff = staff.id_staff
                                            JOIN shiftment ON plan_shift.id_shift = shiftment.id_shift')->result(),
            'content' => 'warehouse/sorting/Sorting',
            'navlink' => 'sorting',
        ];
        $this->load->view('warehouse/VBackend', $data);
    }

    public function finished()
    {
        $data = [
            'finished' => $this->db->query('SELECT finished_report.*, project.project_name, customer.cust_name
                                            FROM finished_report
                                            JOIN project ON finished_report.id_project = project.id_project
                                            JOIN customer ON project.id_cust = customer.id_cust')->result(),
            'content' => 'warehouse/finished/Finished',
            'navlink' => 'finished',
        ];
        $this->load->view('warehouse/VBackend', $data);
    }
}