<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * @property CI_Session $session
 * @property CI_DB_query_builder $db
 * @property CrudModel $crudModel
 * @property CI_Input $input
 * @property CI_Form_validation $form_validation
 * @property CI_URI $uri
 */
class Warehouse extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('CrudModel', 'crudModel');
        $this->load->library('session');
        $this->load->helper(['url', 'form']);
        $this->load->library('form_validation');
        
        // Check if user is logged in
        if (!$this->session->userdata('user_id')) {
            redirect('login/');
        }
        
        // RBAC: Check if user has warehouse access
        // Prefer new RBAC field `role_name`, fallback to legacy `role`
        $role_name = strtolower(trim((string)$this->session->userdata('role_name')));
        $legacy_role = strtolower(trim((string)$this->session->userdata('role')));
        $role = $role_name ?: $legacy_role;

        // Allowed roles for Warehouse
        $allowed_roles = ['warehouse_staff', 'admin', 'bod', 'leader'];

        if (!in_array($role, $allowed_roles, true)) {
            show_error('Access Denied - Warehouse Staff Only', 403, 'Forbidden');
        }
    }

    /**
     * Warehouse Dashboard
     */
    public function index()
    {
        // Load materials
        $materials = $this->crudModel->getData('material')->result();

        // Compute required imports per material from planning.materials
        // planning.materials format: ["Material Name — 7,999", ...]
        // Only consider active plans per pl_status
        $plans = $this->db->query('SELECT materials FROM planning WHERE pl_status = 1')->result();
        $need_by_name = [];
        foreach ($plans as $pl) {
            $arr = [];
            if (!empty($pl->materials)) {
                // Try decode JSON. The field contains a JSON array of strings.
                $decoded = json_decode($pl->materials, true);
                if (is_array($decoded)) {
                    $arr = $decoded;
                }
            }
            foreach ($arr as $line) {
                // Expect pattern: "Name — number" (em dash) or "Name — Yêu cầu: XXX — Thiếu: YYY"
                if (!is_string($line)) continue;
                $parts = preg_split('/\s+—\s+/u', $line);
                if (!$parts || count($parts) < 2) continue;
                $name = trim($parts[0]);
                
                // Try to extract quantity from remaining parts
                $qty = 0;
                for ($i = 1; $i < count($parts); $i++) {
                    $part = trim($parts[$i]);
                    // Look for "Yêu cầu: XXX" pattern
                    if (preg_match('/Yêu\s*cầu:\s*([\d,\.]+)/u', $part, $m)) {
                        $qty = (int)str_replace([',', '.'], '', $m[1]);
                        break;
                    } else if (preg_match('/^[\d,\.]+$/', $part)) {
                        // If just a number, use it
                        $qty = (int)str_replace([',', '.'], '', $part);
                        break;
                    }
                }
                
                if (!isset($need_by_name[$name])) $need_by_name[$name] = 0;
                $need_by_name[$name] += max(0, $qty);
            }
        }

        // Attach qty_to_import to materials by matching by material_name
        foreach ($materials as $m) {
            $name = isset($m->material_name) ? $m->material_name : (isset($m->name) ? $m->name : null);
            $stock = isset($m->stock) ? (int)$m->stock : (isset($m->qty) ? (int)$m->qty : 0);
            $min_stock = isset($m->min_stock) ? (int)$m->min_stock : 0;
            $required = ($name && isset($need_by_name[$name])) ? (int)$need_by_name[$name] : 0;
            // Add 20% buffer for defect prediction
            $required_with_buffer = (int)ceil($required * 1.2);
            // Also ensure post-import stock meets minimum threshold
            $target_stock_level = max($required_with_buffer, $min_stock);
            $m->qty_to_import = max(0, $target_stock_level - $stock);
            $m->qty_required_raw = $required; // optional diagnostics
        }

        // Load active shifts and plans for inline stock-out modal
        $shifts = [];
        $plans = [];
        if ($this->db->table_exists('shift_material_confirmations')) {
            // Get shifts from today and previous days based on confirmed_at
            $shifts = $this->db->query('
                SELECT DISTINCT
                    smc.shift_id as id_planshift,
                    smc.shift_id,
                    smc.plan_id as id_plan,
                    DATE(smc.confirmed_at) as confirmed_date,
                    CONCAT(smc.shift_id, \' - Shift \', smc.shift_id) as ps_name
                FROM shift_material_confirmations smc
                WHERE smc.status = \'confirmed\'
                ORDER BY smc.confirmed_at DESC, smc.shift_id DESC
            ')->result();
        }
        if ($this->db->table_exists('planning')) {
            $plans = $this->db->query('SELECT id_plan, plan_name, materials FROM planning WHERE pl_status = 1 ORDER BY id_plan DESC')->result();
        }

        // Precompute total exported per plan and material
        $exports_sums = [];
        if ($this->db->table_exists('material_out')) {
            $rows = $this->db->query('SELECT id_plan, id_material, SUM(quantity) as total_qty FROM material_out GROUP BY id_plan, id_material')->result();
            foreach ($rows as $r) {
                $pid = (int)($r->id_plan ?? 0);
                $mid = (int)$r->id_material;
                $qty = (int)$r->total_qty;
                if ($pid <= 0 || $mid <= 0) continue;
                if (!isset($exports_sums[$pid])) $exports_sums[$pid] = [];
                $exports_sums[$pid][$mid] = $qty;
            }
        }

        // Compute remaining quantity to export per material across active plans
        // Match planned materials by name to material id
        $material_by_name = [];
        foreach ($materials as $m) {
            $nm = isset($m->material_name) ? $m->material_name : (isset($m->name) ? $m->name : null);
            if ($nm) $material_by_name[mb_strtolower(trim($nm))] = (int)(isset($m->id_material) ? $m->id_material : (isset($m->id) ? $m->id : 0));
        }
        $need_export_by_material = [];
        if (!empty($plans)) {
            foreach ($plans as $pl) {
                $plan_id = (int)$pl->id_plan;
                $arr = [];
                if (!empty($pl->materials)) {
                    $decoded = json_decode($pl->materials, true);
                    if (is_array($decoded)) $arr = $decoded;
                }
                foreach ($arr as $line) {
                    if (!is_string($line)) continue;
                    $parts = preg_split('/\s+—\s+/u', $line);
                    if (!$parts || count($parts) < 2) continue;
                    $name = mb_strtolower(trim($parts[0]));
                    
                    // Extract quantity from remaining parts
                    $planned = 0;
                    for ($i = 1; $i < count($parts); $i++) {
                        $part = trim($parts[$i]);
                        // Look for "Yêu cầu: XXX" pattern
                        if (preg_match('/Yêu\s*cầu:\s*([\d,\.]+)/u', $part, $m)) {
                            $planned = (int)str_replace([',', '.'], '', $m[1]);
                            break;
                        } else if (preg_match('/^[\d,\.]+$/', $part)) {
                            // If just a number, use it
                            $planned = (int)str_replace([',', '.'], '', $part);
                            break;
                        }
                    }
                    
                    $mid = isset($material_by_name[$name]) ? (int)$material_by_name[$name] : 0;
                    if ($mid <= 0) continue;
                    $exported = isset($exports_sums[$plan_id][$mid]) ? (int)$exports_sums[$plan_id][$mid] : 0;
                    $remaining = max(0, $planned - $exported);
                    if (!isset($need_export_by_material[$mid])) $need_export_by_material[$mid] = 0;
                    $need_export_by_material[$mid] += $remaining;
                }
            }
        }
        // Attach qty_to_export to materials
        foreach ($materials as $m) {
            $mid = (int)(isset($m->id_material) ? $m->id_material : (isset($m->id) ? $m->id : 0));
            $m->qty_to_export = isset($need_export_by_material[$mid]) ? (int)$need_export_by_material[$mid] : 0;
        }

        // Build plan name lookup for recent stock-out display
        $plan_name_map = [];
        if ($this->db->table_exists('planning')) {
            $plrows = $this->db->select('id_plan, plan_name')->get('planning')->result();
            foreach ($plrows as $pr) {
                $plan_name_map[(int)$pr->id_plan] = $pr->plan_name;
            }
        }

        // Material id -> name map for history display
        $material_name_map = [];
        foreach ($materials as $m) {
            $mid = (int)(isset($m->id_material) ? $m->id_material : (isset($m->id) ? $m->id : 0));
            $nm  = isset($m->material_name) ? $m->material_name : (isset($m->name) ? $m->name : null);
            if ($mid > 0 && $nm) { $material_name_map[$mid] = $nm; }
        }

        // Build per-plan progress data for inline dashboard rendering
        $plans_data = [];
        if (!empty($plans)) {
            // Map material name -> id
            $material_by_name = [];
            foreach ($materials as $m) {
                $nm = isset($m->material_name) ? $m->material_name : (isset($m->name) ? $m->name : null);
                if ($nm) $material_by_name[mb_strtolower(trim($nm))] = (int)(isset($m->id_material) ? $m->id_material : (isset($m->id) ? $m->id : 0));
            }
            foreach ($plans as $pl) {
                $plan_id = (int)$pl->id_plan;
                $materials_arr = [];
                if (!empty($pl->materials)) {
                    $decoded = json_decode($pl->materials, true);
                    if (is_array($decoded)) $materials_arr = $decoded;
                }
                $items = [];
                $total_planned = 0;
                $total_exported = 0;
                foreach ($materials_arr as $line) {
                    if (!is_string($line)) continue;
                    $parts = preg_split('/\s+—\s+/u', $line);
                    if (!$parts || count($parts) < 2) continue;
                    $name = trim($parts[0]);
                    $qty_str = trim($parts[1]);
                    $planned = (int)str_replace([',', '.'], '', $qty_str);
                    $key = mb_strtolower($name);
                    $mid = isset($material_by_name[$key]) ? (int)$material_by_name[$key] : 0;
                    $exported = ($mid && isset($exports_sums[$plan_id][$mid])) ? (int)$exports_sums[$plan_id][$mid] : 0;
                    $remaining = max(0, $planned - $exported);
                    $items[] = [
                        'name' => $name,
                        'id_material' => $mid,
                        'planned' => $planned,
                        'exported' => $exported,
                        'remaining' => $remaining,
                    ];
                    $total_planned += $planned;
                    $total_exported += min($planned, $exported);
                }
                $progress_pct = $total_planned > 0 ? round(($total_exported / $total_planned) * 100, 1) : 0.0;
                $plans_data[] = [
                    'id_plan' => $plan_id,
                    'plan_name' => $pl->plan_name,
                    'items' => $items,
                    'total_planned' => $total_planned,
                    'total_exported' => $total_exported,
                    'progress_pct' => $progress_pct,
                ];
            }
        }

        // Dashboard summary numbers expected by view
        $project = 0;      // Active projects/plans
        $planning = 0;     // Total plans
        $plan_shift = 0;   // Active shifts
        $finished_report = 0; // Unknown source; keep 0 for now
        if ($this->db->table_exists('planning')) {
            $planning = (int)$this->db->count_all('planning');
            $project = (int)$this->db->where('pl_status', 1)->from('planning')->count_all_results();
        }
        if (!empty($shifts)) {
            $plan_shift = count($shifts);
        } elseif ($this->db->table_exists('shift_material_confirmations')) {
            // Count distinct shifts from shift_material_confirmations
            $plan_shift = (int)$this->db->where('status', 'confirmed')->select('DISTINCT shift_id')->from('shift_material_confirmations')->count_all_results();
        }

        // Recent history for quick view sections with names attached
        $recent_stock_in = [];
        if ($this->db->table_exists('material_entry')) {
            $recent_stock_in = $this->db->order_by('created_at', 'DESC')->limit(10)->get('material_entry')->result();
            foreach ($recent_stock_in as $r) {
                $mid = (int)($r->id_material ?? 0);
                $r->material_name = isset($material_name_map[$mid]) ? $material_name_map[$mid] : ('#'.$mid);
            }
        }
        $recent_stock_out = [];
        if ($this->db->table_exists('material_out')) {
            $recent_stock_out = $this->db->order_by('created_at', 'DESC')->limit(10)->get('material_out')->result();
            foreach ($recent_stock_out as $r) {
                $pid = (int)($r->id_plan ?? 0);
                $mid = (int)($r->id_material ?? 0);
                $r->plan_name = ($pid && isset($plan_name_map[$pid])) ? $plan_name_map[$pid] : '';
                $r->material_name = isset($material_name_map[$mid]) ? $material_name_map[$mid] : ('#'.$mid);
            }
        }

        $data = [
            'total_materials' => $this->crudModel->getData('material')->num_rows(),
            'low_stock_materials' => $this->db->query('SELECT COUNT(*) as count FROM material WHERE stock < 100')->row()->count,
            'recent_materials' => $materials,
            'materials' => $materials,
            'shifts' => $shifts,
            'plans' => $plans,
            'plans_data' => $plans_data,
            'exports_sums' => $exports_sums,
            // New dashboard variables
            'project' => $project,
            'planning' => $planning,
            'plan_shift' => $plan_shift,
            'finished_report' => $finished_report,
            'finished' => [],
            'sorting' => [],
            // Recent history for quick view sections
            'recent_stock_in' => $recent_stock_in,
            'recent_stock_out' => $recent_stock_out,
            'content' => 'warehouse/Beranda',
            'navlink' => 'warehouse',
        ];

        $this->load->view('warehouse/VBackend', $data);
    }

    /**
     * Material Management (Nguyên vật liệu)
     */
    public function material()
    {
        // Support sub-actions in URL like 'warehouse/material/addnewmaterial' or 'warehouse/material/addmaterial'
        $seg3 = $this->uri->segment(3);
        if ($seg3 === 'addmaterial') {
            // Load shifts from shift_material_confirmations instead of plan_shift
            $shifts = [];
            if ($this->db->table_exists('shift_material_confirmations')) {
                $shifts = $this->db->query('
                    SELECT DISTINCT
                        smc.shift_id as id_planshift,
                        smc.shift_id,
                        DATE(smc.confirmed_at) as confirmed_date,
                        CONCAT(smc.shift_id, \' - Shift \', smc.shift_id) as ps_name
                    FROM shift_material_confirmations smc
                    WHERE smc.status = \'confirmed\'
                    ORDER BY smc.confirmed_at DESC, smc.shift_id DESC
                ')->result();
            }
            
            $data = [
                'shifts' => $shifts,
                'materials' => $this->db->query('SELECT * FROM material')->result(),
                'content' => 'warehouse/material/AddMaterial',
                'navlink' => 'material',
            ];
        } elseif ($seg3 === 'addnewmaterial') {
            $data = [
                'content' => 'warehouse/material/AddNewMaterial',
                'navlink' => 'material',
            ];
        } else {
            $data = [
                'material_input' => $this->db->query('
                    SELECT 
                        id_material,
                        material_name,
                        stock,
                        min_stock,
                        uom,
                        (min_stock - stock) as thiếu_bao_nhiêu
                    FROM material 
                    WHERE stock < min_stock
                    ORDER BY (min_stock - stock) DESC
                    LIMIT 10
                ')->result(),
                'materials' => $this->db->query('SELECT * FROM material WHERE id_material IS NOT NULL')->result(),
                'content' => 'warehouse/material/Material',
                'navlink' => 'material',
            ];
        }

        $this->load->view('warehouse/VBackend', $data);
    }

    /**
     * Show Add New Material form
     */
    public function addNewMaterialForm()
    {
        $data = [
            'content' => 'warehouse/material/AddNewMaterial',
            'navlink' => 'material',
        ];
        $this->load->view('warehouse/VBackend', $data);
    }

    /**
     * Handle Add New Material (POST)
     */
    public function addNewMaterial()
    {
        $id_material   = trim($this->input->post('id_material'));
        $material_name = trim($this->input->post('material_name'));
        $material_type = trim($this->input->post('material_type'));
        $uom           = trim($this->input->post('uom'));
        $stock_raw     = $this->input->post('stock');
        $min_raw       = $this->input->post('min_stock');

        // Chỉ bắt buộc tên và đơn vị tính
        if ($material_name === '' || $uom === '') {
            $this->session->set_flashdata('material_alert', 'Thiếu dữ liệu bắt buộc (Tên/UoM)');
            $this->session->set_flashdata('material_alert_level', 'error');
            redirect(site_url('warehouse/material/addnewmaterial'));
            return;
        }

        $stock = ($stock_raw !== null && $stock_raw !== '') ? (int) $stock_raw : 0;
        $min_stock = ($min_raw !== null && $min_raw !== '') ? (int) $min_raw : 0;
        if ($min_stock < 0) {
            $this->session->set_flashdata('material_alert', 'Giá trị không hợp lệ');
            $this->session->set_flashdata('material_alert_level', 'error');
            redirect(site_url('warehouse/material/addnewmaterial'));
            return;
        }

        $base = [
            'material_name' => $material_name,
            'material_type' => $material_type,
            'stock'         => $stock,
            'min_stock'     => $min_stock,
            'uom'           => $uom,
        ];

        if ($id_material !== '') {
            // Nếu người dùng nhập mã, kiểm tra trùng trước khi insert
            $exists = $this->crudModel->getDataWhere('material', 'id_material', $id_material)->num_rows() > 0;
            if ($exists) {
                $this->session->set_flashdata('material_alert', 'Mã NVL đã tồn tại');
                $this->session->set_flashdata('material_alert_level', 'error');
                redirect(site_url('warehouse/material/addnewmaterial'));
                return;
            }
            $payload = array_merge(['id_material' => $id_material], $base, ['created_at' => date('Y-m-d H:i:s')]);
            $this->crudModel->addData('material', $payload);
            $this->session->set_flashdata('material_alert', 'Lưu thành công');
            $this->session->set_flashdata('material_alert_level', 'success');
            redirect(site_url('warehouse/material'));
            return;
        }

        // Nếu để trống mã: tự động sinh mã, kiểm tra trùng, thử lại tối đa 5 lần
        $attempts = 0;
        $max_attempts = 20;
        $inserted = false;
        while ($attempts < $max_attempts && !$inserted) {
            $attempts++;
            $candidate = $this->crudModel->generateCode(1, 'id_material', 'material');
            $exists = $this->crudModel->getDataWhere('material', 'id_material', $candidate)->num_rows() > 0;
            if ($exists) {
                continue; // trùng, thử lại
            }
            $payload = array_merge(['id_material' => $candidate], $base, ['created_at' => date('Y-m-d H:i:s')]);
            $this->crudModel->addData('material', $payload);
            $inserted = true;
            break;
        }
        if (!$inserted) {
            $this->session->set_flashdata('material_alert', 'Không thể tạo mã tự động, vui lòng thử lại');
            $this->session->set_flashdata('material_alert_level', 'error');
            redirect(site_url('warehouse/material/addnewmaterial'));
            return;
        }
        $this->session->set_flashdata('material_alert', 'Lưu thành công');
        $this->session->set_flashdata('material_alert_level', 'success');
        redirect(site_url('warehouse/material'));
    }

    /**
     * Handle adding a production material usage (p_material)
     */
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
                'material_type' => isset($tampil->material_type) ? $tampil->material_type : '',
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
        $material_type = trim($this->input->post('material_type'));
        $stock = trim($this->input->post('stock'));
        $min_stock = ($this->input->post('min_stock') !== null && $this->input->post('min_stock') !== '') ? (int) $this->input->post('min_stock') : 0;
        $uom = trim($this->input->post('uom')) !== '' ? trim($this->input->post('uom')) : 'g';

        if ($new_id === '') {
            $new_id = $old_id;
        }

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
            'material_type' => $material_type,
            'stock'         => $stock,
            'min_stock'     => $min_stock,
            'uom'           => $uom,
            'updated_at'    => date('Y-m-d H:i:s'),
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

    /**
     * Show add material form
     */
    public function add_material()
    {
        $data = [
            'content' => 'warehouse/material/AddMaterial',
            'navlink' => 'material',
        ];

        $this->load->view('warehouse/VBackend', $data);
    }

    /**
     * Handle create material (POST)
     */
    public function create_material()
    {
        // Validate inputs
        $this->form_validation->set_rules('name', 'Name', 'required|trim');
        $this->form_validation->set_rules('code', 'Code', 'required|trim');
        $this->form_validation->set_rules('unit', 'Unit', 'required|trim');
        $this->form_validation->set_rules('stock', 'Stock', 'required|integer');

        if ($this->form_validation->run() === false) {
            // Back to form with errors — use AddMaterial view to match views folder
            $data = [
                'content' => 'warehouse/material/AddMaterial',
                'navlink' => 'material',
            ];
            return $this->load->view('warehouse/VBackend', $data);
        }

        $payload = [
            'name' => $this->input->post('name', true),
            'code' => $this->input->post('code', true),
            'unit' => $this->input->post('unit', true),
            'stock' => (int)$this->input->post('stock', true),
            'created_at' => date('Y-m-d H:i:s'),
        ];

        $this->crudModel->addData('material', $payload);
        $this->session->set_flashdata('success', 'Material created successfully.');

        redirect('warehouse/material');
    }

    /**
     * Show edit material form
     */
    public function edit_material($id)
    {
        $material = $this->db->get_where('material', ['id' => (int)$id])->row();
        if (!$material) {
            show_404();
        }

        $data = [
            'material' => $material,
            'content' => 'warehouse/material/UpdateMaterial',
            'navlink' => 'material',
        ];

        $this->load->view('warehouse/VBackend', $data);
    }

    /**
     * Handle update material (POST)
     */
    public function update_material($id)
    {
        $material = $this->db->get_where('material', ['id' => (int)$id])->row();
        if (!$material) {
            show_404();
        }

        $this->form_validation->set_rules('name', 'Name', 'required|trim');
        $this->form_validation->set_rules('code', 'Code', 'required|trim');
        $this->form_validation->set_rules('unit', 'Unit', 'required|trim');
        $this->form_validation->set_rules('stock', 'Stock', 'required|integer');

        if ($this->form_validation->run() === false) {
            $data = [
                'material' => $material,
                'content' => 'warehouse/material/UpdateMaterial',
                'navlink' => 'material',
            ];
            return $this->load->view('warehouse/VBackend', $data);
        }

        $payload = [
            'name' => $this->input->post('name', true),
            'code' => $this->input->post('code', true),
            'unit' => $this->input->post('unit', true),
            'stock' => (int)$this->input->post('stock', true),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $this->crudModel->updateData('material', 'id', (int)$id, $payload);
        $this->session->set_flashdata('success', 'Material updated successfully.');

        redirect('warehouse/material');
    }

    /**
     * Delete a material
     */
    public function delete_material($id)
    {
        $this->crudModel->deleteData('material', 'id', (int)$id);
        $this->session->set_flashdata('success', 'Material deleted successfully.');
        redirect('warehouse/material');
    }

    /**
     * Stock In (Nhập kho NVL) - Form
     */
    public function stock_in()
    {
        // Hidden entry: use dashboard modal instead
        redirect(site_url('warehouse'));
    }

    /**
     * Stock Out (Xuất kho NVL)
     */
    public function stock_out()
    {
        // Redirect with explicit query param to open modal once
        redirect(site_url('warehouse?open=stock_out'));
    }

    /**
     * Handle Stock In submit
     */
    public function save_stock_in()
    {
        $id_material = trim($this->input->post('id_material'));
        $qty_raw     = $this->input->post('quantity');
        $date_entry  = $this->input->post('date_entry');
        $supplier    = trim($this->input->post('supplier'));

        if ($id_material === '' || $id_material === null) {
            $this->session->set_flashdata('error', 'Vui lòng chọn nguyên liệu.');
            redirect(site_url('warehouse/stock_in'));
            return;
        }
        $quantity = ($qty_raw !== null && $qty_raw !== '') ? (int)$qty_raw : 0;
        if ($quantity <= 0) {
            $this->session->set_flashdata('error', 'Số lượng nhập phải lớn hơn 0.');
            redirect(site_url('warehouse/stock_in'));
            return;
        }

        $material = $this->crudModel->getDataWhere('material', 'id_material', $id_material)->row();
        if (!$material) {
            $this->session->set_flashdata('error', 'Nguyên liệu không tồn tại.');
            redirect(site_url('warehouse/stock_in'));
            return;
        }

        // Handle optional file upload
        $attachment_path = null;
        if (!empty($_FILES) && isset($_FILES['attachment']) && (int)($_FILES['attachment']['size'] ?? 0) > 0) {
            $upload_path = FCPATH . 'asset/uploads/material/';
            if (!is_dir($upload_path)) {
                @mkdir($upload_path, 0755, true);
            }
            $config = [
                'upload_path'   => $upload_path,
                'allowed_types' => 'pdf|jpg|jpeg|png|doc|docx',
                'max_size'      => 5120,
                'encrypt_name'  => true,
            ];
            $this->load->library('upload', $config);
            if ($this->upload->do_upload('attachment')) {
                $up = $this->upload->data();
                $attachment_path = 'asset/uploads/material/' . $up['file_name'];
            } else {
                $this->session->set_flashdata('error', 'Upload file thất bại: ' . $this->upload->display_errors('', ''));
                redirect(site_url('warehouse/stock_in'));
                return;
            }
        }

        // Update stock and optional fields
        $this->db->set('stock', 'stock + ' . (int)$quantity, false);
        if (!empty($date_entry)) $this->db->set('date_entry', $date_entry);
        if (!empty($supplier)) $this->db->set('supplier', $supplier);
        if (!empty($attachment_path)) $this->db->set('attachment', $attachment_path);
        $this->db->where('id_material', $id_material);
        $this->db->update('material');

        // Ensure material_entry table exists; then log transaction
        if ($this->db->table_exists('material_entry') === false) {
            $this->db->query("CREATE TABLE IF NOT EXISTS material_entry (
                id INT AUTO_INCREMENT PRIMARY KEY,
                id_material INT NOT NULL,
                quantity INT NOT NULL,
                date_entry DATE NULL,
                supplier VARCHAR(255) NULL,
                attachment VARCHAR(512) NULL,
                created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
            $this->db->query("CREATE INDEX idx_material_entry_material ON material_entry(id_material);");
        }
        $this->db->insert('material_entry', [
            'id_material' => (int)$id_material,
            'quantity'    => (int)$quantity,
            'date_entry'  => !empty($date_entry) ? $date_entry : null,
            'supplier'    => !empty($supplier) ? $supplier : null,
            'attachment'  => $attachment_path,
        ]);

        $this->session->set_flashdata('success', 'Nhập kho nguyên liệu thành công.');
        $redirect_to = $this->input->post('redirect_to');
        if (!empty($redirect_to)) {
            redirect($redirect_to);
        }
        redirect(site_url('warehouse'));
    }

    /**
     * Handle Stock Out submit (Xuất kho NVL cho ca)
     */
    public function save_stock_out()
    {
        $id_planshift = trim($this->input->post('id_planshift')); // This will be shift_id from shift_material_confirmations
        $items = $this->input->post('items'); // associative: id_material => qty
        $date_out = $this->input->post('date_out');
        $note = trim($this->input->post('note'));
        $id_plan = (int)($this->input->post('id_plan') ?? 0);

        // Shift is optional per new requirement; plan recommended
        if (!is_array($items) || count($items) === 0) {
            $this->session->set_flashdata('error', 'Vui lòng nhập số lượng xuất cho ít nhất một nguyên liệu.');
            redirect(site_url('warehouse/stock_out'));
            return;
        }

        // Convert id_planshift (shift_id from shift_material_confirmations) to integer
        $shift_id = !empty($id_planshift) ? (int)$id_planshift : 0;

        // Upload attachment if present
        $attachment_path = null;
        if (!empty($_FILES) && isset($_FILES['attachment']) && (int)($_FILES['attachment']['size'] ?? 0) > 0) {
            $upload_path = FCPATH . 'asset/uploads/material/';
            if (!is_dir($upload_path)) {
                @mkdir($upload_path, 0755, true);
            }
            $config = [
                'upload_path'   => $upload_path,
                'allowed_types' => 'pdf|jpg|jpeg|png|doc|docx',
                'max_size'      => 5120,
                'encrypt_name'  => true,
            ];
            $this->load->library('upload', $config);
            if ($this->upload->do_upload('attachment')) {
                $up = $this->upload->data();
                $attachment_path = 'asset/uploads/material/' . $up['file_name'];
            } else {
                $this->session->set_flashdata('error', 'Upload file thất bại: ' . $this->upload->display_errors('', ''));
                redirect(site_url('warehouse/stock_out'));
                return;
            }
        }

        // Ensure material_out table exists; then log transaction per item
        if ($this->db->table_exists('material_out') === false) {
            $this->db->query("CREATE TABLE IF NOT EXISTS material_out (
                id INT AUTO_INCREMENT PRIMARY KEY,
                shift_id INT NULL COMMENT 'Reference to shift_material_confirmations.shift_id',
                id_plan INT NULL,
                id_material INT NOT NULL,
                quantity INT NOT NULL,
                date_out DATE NULL,
                note VARCHAR(512) NULL,
                attachment VARCHAR(512) NULL,
                created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
            $this->db->query("CREATE INDEX idx_material_out_shift ON material_out(shift_id);");
            $this->db->query("CREATE INDEX idx_material_out_material ON material_out(id_material);");
        } else {
            // Ensure id_plan column exists for linking to planning
            $has_id_plan = false;
            $cols = $this->db->query("SHOW COLUMNS FROM material_out")->result();
            foreach ($cols as $c) { if (isset($c->Field) && $c->Field === 'id_plan') { $has_id_plan = true; break; } }
            if (!$has_id_plan) {
                $this->db->query("ALTER TABLE material_out ADD COLUMN id_plan INT NULL");
            }
            // Check if table has old id_planshift column and migrate to shift_id if needed
            $has_shift_id = false;
            $has_id_planshift = false;
            foreach ($cols as $c) { 
                if (isset($c->Field) && $c->Field === 'shift_id') { $has_shift_id = true; }
                if (isset($c->Field) && $c->Field === 'id_planshift') { $has_id_planshift = true; }
            }
            if (!$has_shift_id && $has_id_planshift) {
                $this->db->query("ALTER TABLE material_out ADD COLUMN shift_id INT NULL");
                // Optional: migrate data from id_planshift to shift_id if needed
            }
        }

        // Sanitize items: remove empty or non-numeric values
        $clean_items = [];
        foreach ($items as $id_material_key => $qty_raw) {
            $id_material_key = (int)$id_material_key;
            $qty = (int)$qty_raw;
            if ($id_material_key > 0 && $qty > 0) {
                $clean_items[$id_material_key] = $qty;
            }
        }
        if (empty($clean_items)) {
            $this->session->set_flashdata('error', 'Vui lòng chọn nguyên liệu và nhập số lượng hợp lệ.');
            redirect(site_url('warehouse'));
            return;
        }

        // Validate: if exporting for a plan/shift, check if each material has enough stock
        if ($id_plan > 0) {
            $plan = $this->crudModel->getDataWhere('planning', 'id_plan', $id_plan)->row();
            if ($plan && !empty($plan->materials)) {
                $decoded = json_decode($plan->materials, true);
                if (is_array($decoded)) {
                    foreach ($clean_items as $id_material => $qty_to_export) {
                        // Find this material in plan
                        $material_found = false;
                        $planned_qty = 0;
                        foreach ($decoded as $line) {
                            if (!is_string($line)) continue;
                            $parts = preg_split('/\s+—\s+/u', $line);
                            if (!$parts || count($parts) < 2) continue;
                            $name = trim($parts[0]);
                            
                            // Parse new format: "Material Name — Yêu cầu: 100 — Thiếu: 50"
                            $planned = 0;
                            for ($i = 1; $i < count($parts); $i++) {
                                $part = trim($parts[$i]);
                                if (preg_match('/Yêu\s*cầu:\s*([\d,\.]+)/u', $part, $m)) {
                                    $planned = (int)str_replace([',', '.'], '', $m[1]);
                                    break;
                                } else if (preg_match('/^[\d,\.]+$/', $part)) {
                                    $planned = (int)str_replace([',', '.'], '', $part);
                                    break;
                                }
                            }
                            
                            // Check if this is the material we're exporting
                            $key = mb_strtolower(trim($name));
                            $mat = $this->crudModel->getDataWhere('material', 'id_material', $id_material)->row();
                            if ($mat) {
                                $mat_name_lower = mb_strtolower(trim($mat->material_name ?? $mat->name ?? ''));
                                if ($key === $mat_name_lower) {
                                    $material_found = true;
                                    $planned_qty = $planned;
                                    break;
                                }
                            }
                        }
                        
                        if ($material_found && $planned_qty > 0) {
                            // Get already exported
                            $exported = 0;
                            if ($this->db->table_exists('material_out')) {
                                $result = $this->db->query('SELECT COALESCE(SUM(quantity), 0) as total FROM material_out WHERE id_plan = ? AND id_material = ?', [$id_plan, $id_material])->row();
                                $exported = (int)($result->total ?? 0);
                            }
                            
                            // Check if exporting more than available (planned - already exported)
                            $remaining = max(0, $planned_qty - $exported);
                            if ($qty_to_export > $remaining) {
                                $this->session->set_flashdata('error', 'Xuất quá số lượng yêu cầu cho NVL. Kế hoạch: '.$planned_qty.', đã xuất: '.$exported.', còn lại: '.$remaining.'.');
                                redirect(site_url('warehouse?open=stock_out'));
                                return;
                            }
                        }
                    }
                }
            }
        }

        // Begin transaction
        $this->db->trans_start();
        foreach ($clean_items as $id_material => $qty) {
            if ($qty <= 0) continue;
            $mat = $this->crudModel->getDataWhere('material', 'id_material', $id_material)->row();
            if (!$mat) continue;
            $current_stock = (int)($mat->stock ?? 0);
            $min_stock = (int)($mat->min_stock ?? 0);
            // Do not allow export that would drop below minimum stock threshold
            $new_stock = $current_stock - $qty;
            if ($new_stock < $min_stock) {
                // Abort: set error and rollback
                $this->db->trans_rollback();
                $this->session->set_flashdata('error', 'Xuất vượt tồn tối thiểu cho NVL '.($mat->material_name ?? $id_material).'. Tồn hiện tại: '.$current_stock.', tối thiểu: '.$min_stock.'.');
                redirect(site_url('warehouse/stock_out'));
                return;
            }
            $this->crudModel->updateData('material', 'id_material', $id_material, [
                'stock' => $new_stock,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            $insert = [
                'shift_id'     => $shift_id > 0 ? $shift_id : null,
                'id_plan'      => $id_plan ?: null,
                'id_material'  => (int)$id_material,
                'quantity'     => (int)$qty,
                'date_out'     => !empty($date_out) ? $date_out : null,
                'note'         => !empty($note) ? $note : null,
                'attachment'   => $attachment_path,
            ];
            $this->db->insert('material_out', $insert);
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === false) {
            $this->session->set_flashdata('error', 'Xuất kho thất bại. Vui lòng thử lại.');
            redirect(site_url('warehouse/stock_out'));
            return;
        }

        $this->session->set_flashdata('success', 'Xuất kho nguyên liệu thành công.');
        $redirect_to = $this->input->post('redirect_to');
        if (!empty($redirect_to)) {
            redirect($redirect_to);
        }
        redirect(site_url('warehouse'));
    }

    /**
     * Get material entry history (JSON)
     */
    public function material_entry_history($id_material = null)
    {
        if ($id_material === null) {
            $id_material = $this->input->get('id_material');
        }
        $id_material = (int)$id_material;
        if ($id_material <= 0) {
            show_404();
            return;
        }
        $entries = [];
        if ($this->db->table_exists('material_entry')) {
            $entries = $this->db
                ->order_by('created_at', 'DESC')
                ->limit(20)
                ->get_where('material_entry', ['id_material' => $id_material])
                ->result();
        }
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['entries' => $entries]));
    }

    /**
     * Get material stock-out history (JSON)
     */
    public function material_out_history($id_material = null)
    {
        if ($id_material === null) {
            $id_material = $this->input->get('id_material');
        }
        $id_material = (int)$id_material;
        if ($id_material <= 0) {
            show_404();
            return;
        }
        $entries = [];
        if ($this->db->table_exists('material_out')) {
            $entries = $this->db
                ->order_by('created_at', 'DESC')
                ->limit(20)
                ->get_where('material_out', ['id_material' => $id_material])
                ->result();
        }
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['entries' => $entries]));
    }

    /**
     * JSON: Remaining materials for a given plan (planned - exported) with current stock
     */
    public function plan_remaining_materials()
    {
        $id_plan = (int)($this->input->get('id_plan') ?? 0);
        $shift_id = (int)($this->input->get('shift_id') ?? 0);
        
        if ($id_plan <= 0) {
            return $this->output->set_status_header(400)->set_output(json_encode(['error' => 'Invalid plan id']));
        }

        // Load plan
        $plan = $this->db->get_where('planning', ['id_plan' => $id_plan])->row();
        if (!$plan) {
            return $this->output->set_status_header(404)->set_output(json_encode(['error' => 'Plan not found']));
        }

        // Map material name -> id, and id -> material row
        $materials = $this->crudModel->getData('material')->result();
        $material_by_name = [];
        $material_row_by_id = [];
        foreach ($materials as $m) {
            $mid = (int)(isset($m->id_material) ? $m->id_material : (isset($m->id) ? $m->id : 0));
            $nm = isset($m->material_name) ? $m->material_name : (isset($m->name) ? $m->name : null);
            if ($nm) {
                $material_by_name[mb_strtolower(trim($nm))] = $mid;
            }
            if ($mid > 0) { $material_row_by_id[$mid] = $m; }
        }

        // Get exported qty for this plan from material_out table
        $exported = [];
        if ($this->db->table_exists('material_out')) {
            $query = $this->db->select('id_material, SUM(quantity) as total_qty')
                ->from('material_out')
                ->where('id_plan', $id_plan);
            $query = $query->group_by('id_material');
            $rows = $query->get()->result();
            
            foreach ($rows as $row) {
                $mid = (int)($row->id_material ?? 0);
                $qty = (int)($row->total_qty ?? 0);
                if ($mid > 0 && $qty > 0) {
                    $exported[$mid] = $qty;
                }
            }
        }

        // Get required_qty from shift_material_confirmations for suggestion
        // This is used to suggest default quantity when user selects a shift
        $shift_required = [];
        if ($this->db->table_exists('shift_material_confirmations') && $shift_id > 0) {
            $rows = $this->db->where('plan_id', $id_plan)
                ->where('shift_id', $shift_id)
                ->get('shift_material_confirmations')->result();
            
            foreach ($rows as $row) {
                if (!empty($row->snapshot_json)) {
                    $snapshot = json_decode($row->snapshot_json, true);
                    if (isset($snapshot['details']) && is_array($snapshot['details'])) {
                        foreach ($snapshot['details'] as $detail) {
                            $mid = (int)($detail['id_material'] ?? 0);
                            $required = (int)($detail['required_qty'] ?? 0);
                            if ($mid > 0 && $required > 0) {
                                $shift_required[$mid] = $required;
                            }
                        }
                    }
                }
            }
        }

        // Parse planned materials from plan.materials JSON
        $planned_items = [];
        $materials_arr = [];
        if (!empty($plan->materials)) {
            $decoded = json_decode($plan->materials, true);
            if (is_array($decoded)) $materials_arr = $decoded;
        }
        foreach ($materials_arr as $line) {
            if (!is_string($line)) continue;
            // Expected format: "Material Name — Yêu cầu: 60,000 — Thiếu: 118,000"
            // Extract material name and quantity
            $parts = preg_split('/\s+—\s+/u', $line);
            if (!$parts || count($parts) < 2) continue;
            
            $name = trim($parts[0]);
            
            // Try to extract quantity from parts
            // Could be in format: "Yêu cầu: 60,000" or just "60,000"
            $planned = 0;
            
            // Look for "Yêu cầu: XXX" pattern in remaining parts
            for ($i = 1; $i < count($parts); $i++) {
                $part = trim($parts[$i]);
                if (preg_match('/Yêu\s*cầu:\s*([\d,\.]+)/u', $part, $m)) {
                    $qty_str = $m[1];
                    $planned = (int)str_replace([',', '.'], '', $qty_str);
                    break;
                } else if (preg_match('/^[\d,\.]+$/', $part)) {
                    // If just a number, use it
                    $planned = (int)str_replace([',', '.'], '', $part);
                    break;
                }
            }
            
            $key = mb_strtolower($name);
            $mid = isset($material_by_name[$key]) ? (int)$material_by_name[$key] : 0;
            $exp = isset($exported[$mid]) ? (int)$exported[$mid] : 0;
            $remaining = max(0, $planned - $exp);
            $uom = '';
            $stock = 0;
            if ($mid > 0 && isset($material_row_by_id[$mid])) {
                $row = $material_row_by_id[$mid];
                $uom = isset($row->uom) ? $row->uom : '';
                $stock = (int)($row->stock ?? 0);
            }
            $shift_req = isset($shift_required[$mid]) ? (int)$shift_required[$mid] : 0;
            // Check if already exported enough for this shift
            $can_export = true;
            $export_remaining = $shift_req;
            if ($shift_id > 0 && $shift_req > 0 && $exp >= $shift_req) {
                $can_export = false;
                $export_remaining = 0;
            } else if ($shift_id > 0 && $shift_req > 0) {
                $export_remaining = max(0, $shift_req - $exp);
            }
            $planned_items[] = [
                'id_material' => $mid,
                'material_name' => $name,
                'uom' => $uom,
                'stock' => $stock,
                'planned' => (int)$planned,
                'exported' => $exp,
                'remaining' => $remaining,
                'shift_required' => $shift_req,
                'can_export' => $can_export,
                'export_remaining' => $export_remaining,
            ];
        }

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['items' => $planned_items]));
    }

    /**
     * Get shifts by date (for filtering shifts based on selected date)
     */
    public function get_shifts_by_date()
    {
        $date = $this->input->get('date');
        if (!$date) {
            return $this->output->set_status_header(400)->set_output(json_encode(['error' => 'Date is required']));
        }

        // Validate date format (YYYY-MM-DD)
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            return $this->output->set_status_header(400)->set_output(json_encode(['error' => 'Invalid date format']));
        }

        // Query shifts that match the selected date
        // Use shift_material_confirmations table instead of plan_shift
        // Filter by confirmed_at date matching the selected date
        $shifts = [];
        
        if ($this->db->table_exists('shift_material_confirmations')) {
            $shifts = $this->db->query('
                SELECT DISTINCT
                    smc.shift_id as id_planshift,
                    smc.shift_id,
                    smc.plan_id as id_plan,
                    DATE(smc.confirmed_at) as confirmed_date,
                    CONCAT(smc.shift_id, \' - Shift \', smc.shift_id) as ps_name
                FROM shift_material_confirmations smc
                WHERE smc.status = \'confirmed\'
                AND DATE(smc.confirmed_at) = ?
                ORDER BY smc.shift_id DESC
            ', [$date])->result();
        }

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['shifts' => $shifts]));
    }

    /**
     * Stock Report (Báo cáo tồn kho)
     */
    public function report()
    {
        $materials = $this->db->query('SELECT * FROM material')->result();
        $data = [
            'materials' => $materials,
            'content' => 'warehouse/report',
            'navlink' => 'report',
        ];
        $this->load->view('warehouse/VBackend', $data);
    }

    /**
     * Export Progress Dashboard: Show plans and export progress per material
     */
    public function export_dashboard()
    {
        // Load active plans
        $plans = [];
        if ($this->db->table_exists('planning')) {
            $plans = $this->db->query('SELECT id_plan, plan_name, materials FROM planning WHERE pl_status = 1 ORDER BY id_plan DESC')->result();
        }

        // Map material name (lowercase, trimmed) -> id_material
        $materials = $this->crudModel->getData('material')->result();
        $material_by_name = [];
        foreach ($materials as $m) {
            $nm = isset($m->material_name) ? $m->material_name : (isset($m->name) ? $m->name : null);
            if ($nm) {
                $key = mb_strtolower(trim($nm));
                $material_by_name[$key] = (int)(isset($m->id_material) ? $m->id_material : (isset($m->id) ? $m->id : 0));
            }
        }

        // Sum exported quantities grouped by plan and material
        $exports_sums = [];
        if ($this->db->table_exists('material_out')) {
            $rows = $this->db->query('SELECT id_plan, id_material, SUM(quantity) as total_qty FROM material_out GROUP BY id_plan, id_material')->result();
            foreach ($rows as $r) {
                $pid = (int)($r->id_plan ?? 0);
                $mid = (int)$r->id_material;
                $qty = (int)$r->total_qty;
                if ($pid <= 0 || $mid <= 0) continue;
                if (!isset($exports_sums[$pid])) $exports_sums[$pid] = [];
                $exports_sums[$pid][$mid] = $qty;
            }
        }

        // Build plan data
        $plans_data = [];
        foreach ($plans as $pl) {
            $plan_id = (int)$pl->id_plan;
            $plan_name = $pl->plan_name;
            $materials_arr = [];
            if (!empty($pl->materials)) {
                $decoded = json_decode($pl->materials, true);
                if (is_array($decoded)) $materials_arr = $decoded;
            }
            $items = [];
            $total_planned = 0;
            $total_exported = 0;
            foreach ($materials_arr as $line) {
                if (!is_string($line)) continue;
                $parts = preg_split('/\s+—\s+/u', $line);
                if (!$parts || count($parts) < 2) continue;
                $name = trim($parts[0]);
                
                // Parse new format: "Material Name — Yêu cầu: 100 — Thiếu: 50"
                $planned = 0;
                for ($i = 1; $i < count($parts); $i++) {
                    $part = trim($parts[$i]);
                    if (preg_match('/Yêu\s*cầu:\s*([\d,\.]+)/u', $part, $m)) {
                        $planned = (int)str_replace([',', '.'], '', $m[1]);
                        break;
                    } else if (preg_match('/^[\d,\.]+$/', $part)) {
                        // Fallback to old format: plain number
                        $planned = (int)str_replace([',', '.'], '', $part);
                        break;
                    }
                }
                $key = mb_strtolower($name);
                $mid = isset($material_by_name[$key]) ? (int)$material_by_name[$key] : 0;
                $exported = ($mid && isset($exports_sums[$plan_id][$mid])) ? (int)$exports_sums[$plan_id][$mid] : 0;
                $remaining = max(0, $planned - $exported);
                $items[] = [
                    'name' => $name,
                    'id_material' => $mid,
                    'planned' => $planned,
                    'exported' => $exported,
                    'remaining' => $remaining,
                ];
                $total_planned += $planned;
                $total_exported += min($planned, $exported);
            }
            $progress_pct = $total_planned > 0 ? round(($total_exported / $total_planned) * 100, 1) : 0.0;
            $plans_data[] = [
                'id_plan' => $plan_id,
                'plan_name' => $plan_name,
                'items' => $items,
                'total_planned' => $total_planned,
                'total_exported' => $total_exported,
                'progress_pct' => $progress_pct,
            ];
        }

        $data = [
            'plans_data' => $plans_data,
            'content' => 'warehouse/project/ExportDashboard',
            'navlink' => 'export_dashboard',
        ];
        $this->load->view('warehouse/VBackend', $data);
    }

    /**
     * Route remapper for finished goods section
     * Handles: /warehouse/finished/$section/$action
     */
    public function _remap($method, $params = [])
    {
        // Handle finished goods sub-routes
        if ($method === 'finished') {
            $section = isset($params[0]) ? $params[0] : 'dashboard';
            $action = isset($params[1]) ? $params[1] : null;
            
            // Route to appropriate method
            switch ($section) {
                case 'receipt':
                case 'receipts':
                    if ($action === 'new' || $action === 'form') {
                        return $this->finished_receipt_form();
                    } else {
                        return $this->finished_receipts();
                    }
                    break;
                case 'receipt_view':
                    // /warehouse/finished/receipt_view/7
                    $id = isset($params[1]) ? $params[1] : null;
                    return $this->finished_receipt_view($id);
                    break;
                case 'receipt_save':
                    // /warehouse/finished/receipt_save
                    return $this->finished_receipt_save();
                    break;
                case 'get_project_info':
                    // /warehouse/finished/get_project_info/1001
                    $id_project = isset($params[1]) ? $params[1] : null;
                    return $this->finished_get_project_info($id_project);
                    break;
                case 'receipt_cancel':
                    $id = isset($params[1]) ? $params[1] : null;
                    return $this->finished_receipt_cancel($id);
                    break;
                case 'delivery_cancel':
                    $id = isset($params[1]) ? $params[1] : null;
                    return $this->finished_delivery_cancel($id);
                    break;
                case 'deliveries':
                case 'issues':
                    if ($action === 'new') {
                        return $this->finished_delivery_form();
                    } else {
                        return $this->finished_deliveries();
                    }
                    break;
                case 'delivery_view':
                    // /warehouse/finished/delivery_view/7
                    $id = isset($params[1]) ? $params[1] : null;
                    return $this->finished_delivery_view($id);
                    break;
                case 'delivery_save':
                    // /warehouse/finished/delivery_save
                    return $this->finished_delivery_save();
                    break;
                default:
                    return $this->finished($section);
            }
        }
        
        // For other methods, use default behavior
        if (method_exists($this, $method)) {
            return call_user_func_array([$this, $method], $params);
        }
        show_404();
    }

    /**
     * Finished Goods (Kho thành phẩm) Dashboard
     * Route: /warehouse/finished
     * Sub-routes: /warehouse/finished/receipt, /warehouse/finished/issues
     */
    public function finished($section = 'dashboard')
    {
        // Handle sub-routes
        if ($section === 'receipt' || $section === 'receipts') {
            return $this->finished_receipts();
        }
        if ($section === 'issues') {
            return $this->finished_issues();
        }

        // Default: dashboard view
        // Get recent receipts
        $receipts_data = $this->db->query('
            SELECT fr.id_receipt, fr.quantity_received, fr.status, 
                   IFNULL(p.project_name, "N/A") as project_name
            FROM finished_receipt fr
            LEFT JOIN project p ON fr.id_project = p.id_project
            ORDER BY fr.id_receipt DESC
            LIMIT 10
        ')->result();

        // Get recent deliveries
        $deliveries_data = $this->db->query('
            SELECT fi.id_issue, fi.quantity_issued, fi.status, 
                   IFNULL(p.project_name, "N/A") as project_name
            FROM finished_issue fi
            LEFT JOIN project p ON fi.id_project = p.id_project
            ORDER BY fi.id_issue DESC
            LIMIT 10
        ')->result();

        $data = [
            'content' => 'warehouse/finished/dashboard',
            'navlink' => 'finished',
            'receipts_data' => $receipts_data,
            'deliveries_data' => $deliveries_data,
        ];
        $this->load->view('warehouse/VBackend', $data);
    }

    /**
     * Finished Products Inventory: Xem số lượng thành phẩm trong kho
     */
    public function finished_inventory()
    {
        // Get all finished products inventory
        $inventory = [];
        if ($this->db->table_exists('finished_stock')) {
            $inventory = $this->db->query('
                SELECT 
                    fs.id_stock,
                    fs.id_product,
                    fs.quantity_in_stock,
                    fs.last_updated,
                    COALESCE(pr.product_name, "N/A") AS product_name,
                    pr.id_product AS product_code
                FROM finished_stock fs
                LEFT JOIN product pr ON fs.id_product = pr.id_product
                ORDER BY fs.last_updated DESC
            ')->result();
        }

        // Get summary statistics
        $total_quantity = 0;
        if (!empty($inventory)) {
            foreach ($inventory as $item) {
                $total_quantity += $item->quantity_in_stock;
            }
        }

        $data = [
            'inventory' => $inventory,
            'total_quantity' => $total_quantity,
            'content' => 'warehouse/finished/inventory',
            'navlink' => 'finished_inventory',
        ];
        $this->load->view('warehouse/VBackend', $data);
    }

    /**
     * Project: Tiến độ & Kế hoạch (màn hình riêng)
     */
    public function project()
    {
        // Reuse same computation as export_dashboard but render different view name
        $plans = [];
        if ($this->db->table_exists('planning')) {
            $plans = $this->db->query('SELECT id_plan, plan_name, materials FROM planning WHERE pl_status = 1 ORDER BY id_plan DESC')->result();
        }

        $materials = $this->crudModel->getData('material')->result();
        $material_by_name = [];
        $material_uom_by_id = [];
        foreach ($materials as $m) {
            $nm = isset($m->material_name) ? $m->material_name : (isset($m->name) ? $m->name : null);
            $mid = (int)(isset($m->id_material) ? $m->id_material : (isset($m->id) ? $m->id : 0));
            if ($nm) $material_by_name[mb_strtolower(trim($nm))] = $mid;
            if ($mid > 0) $material_uom_by_id[$mid] = isset($m->uom) ? (string)$m->uom : '';
        }

        $exports_sums = [];
        if ($this->db->table_exists('material_out')) {
            $rows = $this->db->query('SELECT id_plan, id_material, SUM(quantity) as total_qty FROM material_out GROUP BY id_plan, id_material')->result();
            foreach ($rows as $r) {
                $pid = (int)($r->id_plan ?? 0);
                $mid = (int)$r->id_material;
                $qty = (int)$r->total_qty;
                if ($pid <= 0 || $mid <= 0) continue;
                if (!isset($exports_sums[$pid])) $exports_sums[$pid] = [];
                $exports_sums[$pid][$mid] = $qty;
            }
        }

        $plans_data = [];
        foreach ($plans as $pl) {
            $plan_id = (int)$pl->id_plan;
            $materials_arr = [];
            if (!empty($pl->materials)) {
                $decoded = json_decode($pl->materials, true);
                if (is_array($decoded)) $materials_arr = $decoded;
            }
            $items = [];
            $total_planned = 0;
            $total_exported = 0;
            foreach ($materials_arr as $line) {
                if (!is_string($line)) continue;
                $parts = preg_split('/\s+—\s+/u', $line);
                if (!$parts || count($parts) < 2) continue;
                $name = trim($parts[0]);
                
                // Parse new format: "Material Name — Yêu cầu: 100 — Thiếu: 50"
                $planned = 0;
                for ($i = 1; $i < count($parts); $i++) {
                    $part = trim($parts[$i]);
                    if (preg_match('/Yêu\s*cầu:\s*([\d,\.]+)/u', $part, $m)) {
                        $planned = (int)str_replace([',', '.'], '', $m[1]);
                        break;
                    } else if (preg_match('/^[\d,\.]+$/', $part)) {
                        // Fallback to old format: plain number
                        $planned = (int)str_replace([',', '.'], '', $part);
                        break;
                    }
                }
                $key = mb_strtolower($name);
                $mid = isset($material_by_name[$key]) ? (int)$material_by_name[$key] : 0;
                $exported = ($mid && isset($exports_sums[$plan_id][$mid])) ? (int)$exports_sums[$plan_id][$mid] : 0;
                $remaining = max(0, $planned - $exported);
                $items[] = [
                    'name' => $name,
                    'id_material' => $mid,
                    'planned' => $planned,
                    'exported' => $exported,
                    'remaining' => $remaining,
                    'uom' => isset($material_uom_by_id[$mid]) ? $material_uom_by_id[$mid] : '',
                ];
                $total_planned += $planned;
                $total_exported += min($planned, $exported);
            }
            $progress_pct = $total_planned > 0 ? round(($total_exported / $total_planned) * 100, 1) : 0.0;
            $plans_data[] = [
                'id_plan' => $plan_id,
                'plan_name' => $pl->plan_name,
                'items' => $items,
                'total_planned' => $total_planned,
                'total_exported' => $total_exported,
                'progress_pct' => $progress_pct,
            ];
        }

        $data = [
            'plans_data' => $plans_data,
            'content' => 'warehouse/project/Project',
            'navlink' => 'project',
        ];
        $this->load->view('warehouse/VBackend', $data);
    }
    
    /**
     * Finished Receipts list
     */
    public function finished_receipts()
    {
        $receipts = [];
        $total = 0;
        $limit = 10;
        $page = (int)$this->input->get('page', true);
        if ($page <= 0) { $page = 1; }
        $offset = ($page - 1) * $limit;
        if ($this->db->table_exists('finished_receipt')) {
            $total = (int)$this->db->count_all('finished_receipt');
            
            // Join with project table to get project_name
            $sql = "SELECT 
                      fr.id_receipt,
                      fr.receipt_code,
                      fr.id_project,
                      IFNULL(p.project_name, 'N/A') AS project_name,
                      fr.quantity_received,
                      fr.created_by,
                      fr.created_by_name,
                      fr.created_date,
                      fr.status
                    FROM finished_receipt fr
                    LEFT JOIN project p ON fr.id_project = p.id_project
                    ORDER BY fr.created_date DESC
                    LIMIT {$limit} OFFSET {$offset}";
            
            $receipts = $this->db->query($sql)->result();
        }
        $data = [
            'receipts' => $receipts,
            'total' => $total,
            'limit' => $limit,
            'page' => $page,
            'content' => 'warehouse/finished/receipt_list',
            'navlink' => 'finished',
        ];
        $this->load->view('warehouse/VBackend', $data);
    }
    
    /**
     * Finished Issues (Deliveries) list
     */
    public function finished_issues()
    {
        $issues = [];
        $total = 0;
        $limit = 10;
        $page = (int)$this->input->get('page', true);
        if ($page <= 0) { $page = 1; }
        $offset = ($page - 1) * $limit;
        if ($this->db->table_exists('finished_issue')) {
            $total = (int)$this->db->count_all('finished_issue');
            
            // Join with project table to get project_name
            $sql = "SELECT 
                      fi.id_issue,
                      fi.issue_code,
                      fi.id_project,
                      IFNULL(p.project_name, 'N/A') AS project_name,
                      fi.quantity_requested,
                      fi.quantity_issued,
                      fi.created_by,
                      fi.created_by_name,
                      fi.created_date,
                      fi.status
                    FROM finished_issue fi
                    LEFT JOIN project p ON fi.id_project = p.id_project
                    ORDER BY fi.created_date DESC
                    LIMIT {$limit} OFFSET {$offset}";
            
            $issues = $this->db->query($sql)->result();
        }
        
        // Get total stock from finished_stock table
        $total_stock = 0;
        if ($this->db->table_exists('finished_stock')) {
            $row = $this->db->select('SUM(quantity_in_stock) as qty')->get('finished_stock')->row();
            if ($row && isset($row->qty)) {
                $total_stock = (int)$row->qty;
            }
        }
        
        $data = [
            'issues' => $issues,
            'total' => $total,
            'limit' => $limit,
            'page' => $page,
            'total_stock' => $total_stock,
            'content' => 'warehouse/finished/delivery_list',
            'navlink' => 'finished',
        ];
        $this->load->view('warehouse/VBackend', $data);
    }
    
    /**
     * Alias for finished_issues() - deliveries (xuất hàng)
     */
    public function finished_deliveries()
    {
        return $this->finished_issues();
    }

    /**
     * Finished Receipt form (create)
     */
    public function finished_receipt_form()
    {
        $this->load->model('FinishedReceiptModel');
        
        // Clear any previous error flashdata to prevent stale errors showing
        $this->session->unset_userdata('error');
        
        $batches = [];
        $projects = [];
        
        try {
            $batches = $this->FinishedReceiptModel->getQcPassedBatches();
        } catch (Exception $e) {
            log_message('error', 'Error in getQcPassedBatches: ' . $e->getMessage());
            $batches = [];
        }

        // Load projects list for dropdown with stats
        if ($this->db->table_exists('project')) {
            try {
                $projects = $this->db->query('
                    SELECT 
                        p.id_project, 
                        p.project_name, 
                        p.qty_request as qty_target,
                        COALESCE((SELECT SUM(quantity_received) FROM finished_receipt WHERE id_project = p.id_project AND status = "posted"), 0) as qty_received
                    FROM project p
                    WHERE COALESCE((SELECT SUM(quantity_received) FROM finished_receipt WHERE id_project = p.id_project AND status = "posted"), 0) < p.qty_request
                    ORDER BY p.project_name
                ')->result();
            } catch (Exception $e) {
                log_message('error', 'Error loading projects: ' . $e->getMessage());
                $projects = [];
            }
        }

        $data = [
            'content' => 'warehouse/finished/receipt_form',
            'navlink' => 'finished',
            'batches' => $batches,
            'projects' => $projects
        ];
        $this->load->view('warehouse/VBackend', $data);
    }

    /**
     * Finished Receipt view (detail)
     */
    public function finished_receipt_view($id = null)
    {
        if (!$id) {
            $this->session->set_flashdata('error', 'Mã phiếu không hợp lệ');
            redirect('warehouse/finished/receipt');
        }

        $receipt = null;
        $project = null;
        if ($this->db->table_exists('finished_receipt')) {
            $receipt = $this->db->where('id_receipt', (int)$id)->get('finished_receipt')->row();
            
            if (!$receipt) {
                $this->session->set_flashdata('error', 'Không tìm thấy phiếu nhập #' . $id);
                redirect('warehouse/finished/receipt');
            }

            // Get project information if id_project exists in receipt
            if (isset($receipt->id_project)) {
                $project = $this->db->where('id_project', (int)$receipt->id_project)->get('project')->row();
            }
        } else {
            $this->session->set_flashdata('error', 'Bảng dữ liệu không tồn tại');
            redirect('warehouse/finished/receipt');
        }

        $data = [
            'receipt' => $receipt,
            'project' => $project,
            'content' => 'warehouse/finished/receipt_view',
            'navlink' => 'finished',
        ];
        $this->load->view('warehouse/VBackend', $data);
    }

    /**
     * Finished Delivery form (create)
     */
    public function finished_delivery_form()
    {
        // Load projects (orders) with quantities from finished_issue
        $projects = [];
        if ($this->db->table_exists('project')) {
            $sql = "SELECT 
                      p.id_project,
                      p.id_product,
                      p.project_name,
                      p.qty_request,
                      COALESCE(SUM(CASE WHEN fi.status IN ('full', 'partial') THEN fi.quantity_issued ELSE 0 END), 0) AS qty_already_issued
                    FROM project p
                    LEFT JOIN finished_issue fi ON p.id_project = fi.id_project
                    GROUP BY p.id_project, p.id_product, p.project_name, p.qty_request
                    ORDER BY p.id_project DESC
                    LIMIT 50";
            
            $projects = $this->db->query($sql)->result();
            
            // Calculate remaining quantities and get product-specific stock for each project
            foreach ($projects as $p) {
                $p->qty_request = (int)$p->qty_request;
                $p->qty_already_issued = (int)$p->qty_already_issued;
                $p->qty_remaining = max(0, $p->qty_request - $p->qty_already_issued);
                
                // Get stock for this specific product
                $p->qty_available = 0;
                if ($this->db->table_exists('finished_stock') && isset($p->id_product)) {
                    $stock = $this->db->select('quantity_in_stock')
                                     ->where('id_product', $p->id_product)
                                     ->get('finished_stock')
                                     ->row();
                    if ($stock) {
                        $p->qty_available = (int)$stock->quantity_in_stock;
                    }
                }
            }
        }

        // Compute total current stock for display
        $current_stock = 0;
        if ($this->db->table_exists('finished_stock')) {
            $row = $this->db->select('SUM(quantity_in_stock) as qty')->get('finished_stock')->row();
            if ($row && isset($row->qty)) {
                $current_stock = (int)$row->qty;
            }
        }

        $data = [
            'current_stock' => $current_stock,
            'projects' => $projects,
            'content' => 'warehouse/finished/delivery_form',
            'navlink' => 'finished',
        ];
        $this->load->view('warehouse/VBackend', $data);
    }

    /**
     * Finished Delivery view (detail)
     */
    public function finished_delivery_view($id = null)
    {
        $issue = null;
        $project = null;
        $current_stock = 0;

        if ($id && $this->db->table_exists('finished_issue')) {
            $issue = $this->db->where('id_issue', (int)$id)->get('finished_issue')->row();
            
            if ($issue) {
                // Get project information
                $project = $this->db->where('id_project', $issue->id_project)
                                   ->get('project')
                                   ->row();
                
                // Get current stock
                if ($this->db->table_exists('finished_stock')) {
                    $row = $this->db->select('SUM(quantity_in_stock) as qty')->get('finished_stock')->row();
                    if ($row && isset($row->qty)) {
                        $current_stock = (int)$row->qty;
                    }
                }
            }
        }

        $data = [
            'issue' => $issue,
            'project' => $project,
            'current_stock' => $current_stock,
            'content' => 'warehouse/finished/delivery_view',
            'navlink' => 'finished',
        ];
        $this->load->view('warehouse/VBackend', $data);
    }

    /**
     * Finished Delivery save - handle form submission
     */
    public function finished_delivery_save()
    {
        $id_project = (int)$this->input->post('id_project', true);
        $quantity_issued = (int)$this->input->post('quantity_issued', true);
        $notes = $this->input->post('notes', true);

        if (!$id_project) {
            $this->session->set_flashdata('error', 'Vui lòng chọn đơn hàng');
            redirect('warehouse/finished/deliveries');
        }

        if ($quantity_issued <= 0) {
            $this->session->set_flashdata('error', 'Vui lòng nhập số lượng xuất hợp lệ (> 0)');
            redirect('warehouse/finished/deliveries');
        }

        // Check if project exists
        $project = $this->db->where('id_project', $id_project)->get('project')->row();
        if (!$project) {
            $this->session->set_flashdata('error', 'Đơn hàng không tồn tại');
            redirect('warehouse/finished/deliveries');
        }

        // Check current stock
        $current_stock = 0;
        if ($this->db->table_exists('finished_stock')) {
            $row = $this->db->select('SUM(quantity_in_stock) as qty')->get('finished_stock')->row();
            if ($row && isset($row->qty)) {
                $current_stock = (int)$row->qty;
            }
        }

        if ($quantity_issued > $current_stock) {
            $this->session->set_flashdata('error', 'Không đủ hàng - Tồn kho: ' . $current_stock . ' cái');
            redirect('warehouse/finished/deliveries');
        }

        // Generate issue code
        $date = date('YmdHis');
        $random = substr(str_shuffle('0123456789'), 0, 3);
        $issue_code = 'XK-' . $date . '-' . $random;

        // Determine status
        $quantity_requested = $project->qty_request ?? 0;
        $qty_already_issued = 0;
        if ($this->db->table_exists('finished_issue')) {
            $issued = $this->db->where('id_project', $id_project)
                               ->where_in('status', ['full', 'partial'])
                               ->select('SUM(quantity_issued) as total')
                               ->get('finished_issue')
                               ->row();
            if ($issued) {
                $qty_already_issued = (int)$issued->total;
            }
        }

        $total_issued = $qty_already_issued + $quantity_issued;
        $status = 'full';
        if ($total_issued < $quantity_requested) {
            $status = 'partial';
        } elseif ($total_issued > $quantity_requested) {
            // Vẫn lưu là partial nếu vượt, nhưng đã giao đủ
            $status = 'full';
        }

        $issue_data = [
            'issue_code' => $issue_code,
            'id_project' => $id_project,
            'quantity_requested' => $quantity_requested,
            'quantity_issued' => $quantity_issued,
            'created_by' => $this->session->userdata('user_id'),
            'created_by_name' => $this->session->userdata('username'),
            'created_date' => date('Y-m-d H:i:s'),
            'notes' => $notes,
            'status' => $status
        ];

        if ($this->db->table_exists('finished_issue')) {
            $this->db->insert('finished_issue', $issue_data);
            $issue_id = $this->db->insert_id();

            // Update stock - use model method for consistency
            if ($this->db->table_exists('finished_stock')) {
                $product_id = $project->id_product ?? 1;
                $this->load->model('FinishedReceiptModel');
                $this->FinishedReceiptModel->updateStockAfterIssue($quantity_issued, $product_id);
            }

            $this->session->set_flashdata('success', 'Xuất thành công - Phiếu #' . $issue_id);
            redirect('warehouse/finished/deliveries');
        } else {
            $this->session->set_flashdata('error', 'Lỗi: Không thể lưu phiếu xuất');
            redirect('warehouse/finished/deliveries');
        }
    }

    /**
     * Finished Receipt save - handle form submission
     */
    public function finished_receipt_save()
    {
        $this->load->model('FinishedReceiptModel');
        
        $id_finished_report = $this->input->post('id_finished_report', true);
        $quantity_received = (int)$this->input->post('quantity_received', true);
        $notes = $this->input->post('notes', true);

        if (!$id_finished_report) {
            $this->session->set_flashdata('error', 'Vui lòng chọn ca/lô');
            redirect('warehouse/finished/receipt_form');
        }
        if ($quantity_received <= 0) {
            $this->session->set_flashdata('error', 'Vui lòng nhập số lượng hợp lệ (> 0)');
            redirect('warehouse/finished/receipt_form');
        }

        // Try to fetch from shift_closures - link thông qua closure_id
        $batch = null;
        $quantity_planned = 0;
        $id_project = null;
        
        if ($this->db->table_exists('shift_closures')) {
            $batch = $this->db->where('closure_id', $id_finished_report)
                              ->get('shift_closures')
                              ->row();
            
            if ($batch) {
                // Lấy thông tin từ shift_closures
                $quantity_planned = (int)$batch->total_good;  // Sử dụng total_good từ closure
                
                // Nếu có warehouse_request_id, lấy project từ đó
                if ($batch->warehouse_request_id) {
                    // Tìm project liên kết
                    $proj = $this->db->select('id_project')->limit(1)->get_where('planning', ['id_plan' => $batch->warehouse_request_id])->row();
                    if ($proj) {
                        $id_project = $proj->id_project;
                    }
                }
            }
        }

        // Nếu không có thì để user chọn project từ form
        if (!$batch) {
            $this->session->set_flashdata('error', 'Ca/lô không tồn tại (closure_id: ' . $id_finished_report . ')');
            redirect('warehouse/finished/receipt_form');
        }

        // Nếu không lấy được id_project từ shift_closures, có thể để trống hoặc lấy từ form
        // Bây giờ form sẽ gửi id_project
        $id_project = (int)($this->input->post('id_project') ?? $id_project ?? 0);

        $receipt_data = [
            'id_project' => $id_project > 0 ? $id_project : null,
            'id_finished_report' => $id_finished_report,
            'quantity_received' => $quantity_received,
            'quantity_planned' => $quantity_planned,
            'created_by' => $this->session->userdata('user_id'),
            'created_by_name' => $this->session->userdata('username'),
            'notes' => $notes,
            'created_date' => date('Y-m-d H:i:s'),
            'status' => 'posted'
        ];

        $receipt_id = $this->FinishedReceiptModel->createReceipt($receipt_data);

        if ($receipt_id) {
            // Lấy id_product từ project để cập nhật tồn kho chính xác
            $product_id = 1; // Mặc định
            if ($id_project > 0) {
                $proj_info = $this->db->select('id_product')->get_where('project', ['id_project' => $id_project])->row();
                if ($proj_info) {
                    $product_id = $proj_info->id_product;
                }
            }
            
            $this->FinishedReceiptModel->updateStockAfterReceipt($quantity_received, $product_id);
            $this->session->set_flashdata('success', 'Nhập thành công - Phiếu #' . $receipt_id);
            redirect('warehouse/finished/receipt_view/' . $receipt_id);
        } else {
            $db_error = $this->db->error();
            $error_msg = !empty($db_error['message']) ? $db_error['message'] : 'Không thể lưu phiếu vào cơ sở dữ liệu';
            $this->session->set_flashdata('error', $error_msg);
            redirect('warehouse/finished/receipt_form');
        }
    }

    /**
     * Get project info via AJAX
     */
    public function finished_get_project_info($id_project = null)
    {
        header('Content-Type: application/json');
        
        if (!$id_project) {
            echo json_encode(['success' => false, 'message' => 'Project ID không hợp lệ']);
            exit;
        }
        
        try {
            $id_project = (int)$id_project;
            
            if (!$this->db->table_exists('project')) {
                echo json_encode(['success' => false, 'message' => 'Bảng project không tồn tại']);
                exit;
            }
            
            // Get project info
            $project = $this->db->select('id_project, project_name, qty_request as qty_target')
                               ->where('id_project', $id_project)
                               ->get('project')
                               ->row();
            
            if (!$project) {
                echo json_encode(['success' => false, 'message' => 'Không tìm thấy dự án']);
                exit;
            }
            
            // Get quantity already received for this project
            $qty_received = 0;
            if ($this->db->table_exists('finished_receipt')) {
                $result = $this->db->select('SUM(quantity_received) as total')
                                  ->where('id_project', $id_project)
                                  ->where('status', 'posted')
                                  ->get('finished_receipt')
                                  ->row();
                if ($result && $result->total) {
                    $qty_received = (int)$result->total;
                }
            }
            
            echo json_encode([
                'success' => true,
                'qty_target' => (int)$project->qty_target,
                'qty_received' => $qty_received
            ]);
        } catch (Exception $e) {
            log_message('error', 'Error in finished_get_project_info: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Lỗi hệ thống']);
        }
        exit;
    }

    /**
     * Finished Receipt cancel
     */
    /**
     * Finished Delivery cancel
     */
    public function finished_delivery_cancel($issue_id = null)
    {
        $this->load->model('FinishedIssueModel');

        if (!$issue_id) {
            $this->session->set_flashdata('error', 'ID phiếu không hợp lệ');
            redirect('warehouse/finished/deliveries');
        }

        $issue = $this->FinishedIssueModel->getIssueById($issue_id);

        if (!$issue) {
            $this->session->set_flashdata('error', 'Phiếu không tồn tại');
            redirect('warehouse/finished/deliveries');
        }

        $ok = false;
        try {
            $ok = $this->FinishedIssueModel->cancelIssue($issue_id);
        } catch (Exception $e) {
            log_message('error', 'Error cancelling issue: ' . $e->getMessage());
            $ok = false;
        }

        if ($ok) {
            $this->session->set_flashdata('success', 'Hủy phiếu thành công');
        } else {
            $this->session->set_flashdata('error', 'Lỗi: Không thể hủy phiếu');
        }

        redirect('warehouse/finished/deliveries');
    }

    /**
     * Finished Receipt cancel
     */
    public function finished_receipt_cancel($receipt_id = null)
    {
        $this->load->model('FinishedReceiptModel');

        if (!$receipt_id) {
            $this->session->set_flashdata('error', 'ID phiếu không hợp lệ');
            redirect('warehouse/finished/receipt');
        }

        $receipt = $this->FinishedReceiptModel->getReceiptById($receipt_id);

        if (!$receipt) {
            $this->session->set_flashdata('error', 'Phiếu không tồn tại');
            redirect('warehouse/finished/receipt');
        }

        // Use model method to cancel so stock reversal logic runs
        $ok = false;
        try {
            $ok = $this->FinishedReceiptModel->cancelReceipt($receipt_id);
        } catch (Exception $e) {
            log_message('error', 'Error cancelling receipt: ' . $e->getMessage());
            $ok = false;
        }

        if ($ok) {
            $this->session->set_flashdata('success', 'Hủy phiếu thành công');
        } else {
            $this->session->set_flashdata('error', 'Lỗi: Không thể hủy phiếu');
        }

        redirect('warehouse/finished/receipt');
    }

    /**
     * Get material info by ID (JSON)
     * Returns: material details including qty_to_import
     */
    public function get_material_info()
    {
        $id_material = (int)($this->input->get('id_material') ?? 0);
        
        if ($id_material <= 0) {
            return $this->output
                ->set_status_header(400)
                ->set_content_type('application/json')
                ->set_output(json_encode(['error' => 'Invalid material ID']));
        }

        // Get material details
        $material = $this->crudModel->getDataWhere('material', 'id_material', $id_material)->row();
        
        if (!$material) {
            return $this->output
                ->set_status_header(404)
                ->set_content_type('application/json')
                ->set_output(json_encode(['error' => 'Material not found']));
        }

        // Load materials with qty_to_import (same logic as index())
        $materials = $this->crudModel->getData('material')->result();
        
        // Compute required imports per material from planning.materials
        $plans = $this->db->query('SELECT materials FROM planning WHERE pl_status = 1')->result();
        $need_by_name = [];
        foreach ($plans as $pl) {
            $arr = [];
            if (!empty($pl->materials)) {
                $decoded = json_decode($pl->materials, true);
                if (is_array($decoded)) {
                    $arr = $decoded;
                }
            }
            foreach ($arr as $line) {
                if (!is_string($line)) continue;
                $parts = preg_split('/\s+—\s+/u', $line);
                if (!$parts || count($parts) < 2) continue;
                $name = trim($parts[0]);
                
                // Try to extract quantity from remaining parts
                $qty = 0;
                for ($i = 1; $i < count($parts); $i++) {
                    $part = trim($parts[$i]);
                    // Look for "Yêu cầu: XXX" pattern
                    if (preg_match('/Yêu\s*cầu:\s*([\d,\.]+)/u', $part, $m)) {
                        $qty = (int)str_replace([',', '.'], '', $m[1]);
                        break;
                    } else if (preg_match('/^[\d,\.]+$/', $part)) {
                        // If just a number, use it
                        $qty = (int)str_replace([',', '.'], '', $part);
                        break;
                    }
                }
                
                if (!isset($need_by_name[$name])) $need_by_name[$name] = 0;
                $need_by_name[$name] += max(0, $qty);
            }
        }

        // Calculate qty_to_import for this material
        $name = isset($material->material_name) ? $material->material_name : (isset($material->name) ? $material->name : null);
        $stock = isset($material->stock) ? (int)$material->stock : (isset($material->qty) ? (int)$material->qty : 0);
        $min_stock = isset($material->min_stock) ? (int)$material->min_stock : 0;
        $required = ($name && isset($need_by_name[$name])) ? (int)$need_by_name[$name] : 0;
        $required_with_buffer = (int)ceil($required * 1.2);
        $target_stock_level = max($required_with_buffer, $min_stock);
        $qty_to_import = max(0, $target_stock_level - $stock);

        $response = [
            'id_material' => (int)$material->id_material,
            'material_name' => $material->material_name ?? $material->name ?? '',
            'current_stock' => $stock,
            'min_stock' => $min_stock,
            'qty_to_import' => $qty_to_import,
            'uom' => $material->uom ?? 'g'
        ];

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }

    /**
     * Logout warehouse user
     */
    public function logout()
    {
        $this->load->model('LoginModel', 'login');
        
        // Log logout activity before destroying session
        if ($this->session->userdata('user_id')) {
            $this->login->log_activity(
                $this->session->userdata('user_id'),
                $this->session->userdata('username'),
                'logout',
                'auth'
            );
        }

        $this->session->sess_destroy();
        redirect('login/');
    }
}
