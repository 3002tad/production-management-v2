<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Leader extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('CrudModel', 'crudModel');
        $this->load->library('session');
        
        // Check if user is logged in
        if (!$this->session->userdata('user_id')) {
            redirect('login/');
        }
        
        // RBAC: Check if user has leader/line manager access
        $role = $this->session->userdata('role');
        if (empty($role)) {
            $role = $this->session->userdata('role_name');
        }
        $role = strtolower(trim((string)$role));
        
        // Allowed roles for Leader page
        // NOTE: adding 'technical' / 'technical_staff' / 'worker' allows these users to access leader pages.
        // If you want to keep leader pages strictly for leadership, remove these.
        $allowed_roles = ['leader', 'line_manager', 'admin', 'bod', 'system_admin', 'technical', 'technical_staff', 'worker'];
        
        if (!in_array($role, $allowed_roles, true)) {
            log_message('error', 'Leader access denied for user ' . $this->session->userdata('username') . ' with role=' . $role);
            show_error('Access Denied - Insufficient Permissions. Your role: ' . var_export($role, true), 403, 'Forbidden');
        }

        // Store role in property for later use
        $this->user_role = $role;
    }

    /**
     * Check if user can manage staff (add/edit/delete)
     * Only Leader, Admin, BOD can manage staff
     * Technical staff can only view dashboard/incident reports
     */
    private function check_staff_permission()
    {
        $staff_management_roles = ['leader', 'line_manager', 'admin', 'bod', 'system_admin'];

        if (!in_array($this->user_role, $staff_management_roles, true)) {
            show_error('Access Denied - Quản lý nhân sự chỉ cho phép Leader, Admin, BOD. Role của bạn: ' . $this->user_role, 403, 'Forbidden');
        }
    }

    public function index()
    {
        // Merge: show recent finished & sorting summaries and gather machine capacity
        $machine_capacity = [];
        try {
            $this->load->model('leader/ProductionSimulatorModel', 'simModel');
            $active_shifts = $this->simModel->getActiveShiftsForSimulation();
            foreach ($active_shifts as $shift) {
                $summaries = $this->simModel->getShiftProductionSummary($shift->shift_id);
                foreach ($summaries as $row) {
                    $key = (string)($row->machine_id ?? ('code:' . ($row->machine_code ?? 'unknown')));
                    if (!isset($machine_capacity[$key])) {
                        $machine_capacity[$key] = [
                            'machine_id' => $row->machine_id ?? null,
                            'machine_code' => $row->machine_code ?? '',
                            'machine_name' => $row->machine_name ?? '',
                            'total_good' => (int)($row->total_good ?? 0),
                            'total_defect' => (int)($row->total_defect ?? 0),
                            'total_produced' => (int)($row->total_produced ?? 0),
                            'avg_efficiency' => (float)($row->avg_efficiency ?? 0),
                            'total_downtime' => (int)($row->total_downtime ?? 0),
                            'shift_id' => $shift->shift_id,
                            'shift_name' => $shift->shift_name ?? ''
                        ];
                    } else {
                        $machine_capacity[$key]['total_good'] += (int)($row->total_good ?? 0);
                        $machine_capacity[$key]['total_defect'] += (int)($row->total_defect ?? 0);
                        $machine_capacity[$key]['total_produced'] += (int)($row->total_produced ?? 0);
                        $machine_capacity[$key]['total_downtime'] += (int)($row->total_downtime ?? 0);
                        $prev_eff = $machine_capacity[$key]['avg_efficiency'];
                        $machine_capacity[$key]['avg_efficiency'] = ($prev_eff + (float)($row->avg_efficiency ?? 0)) / 2.0;
                    }
                }
            }
        } catch (Exception $e) {
            log_message('error', 'Leader::index capacity fetch error: ' . $e->getMessage());
        }

        if (!empty($machine_capacity)) {
            $machine_capacity = array_values($machine_capacity);
            usort($machine_capacity, function($a, $b) {
                return ($b['total_produced'] <=> $a['total_produced']);
            });
            $machine_capacity = array_slice($machine_capacity, 0, 5);
        }

        $finished = $this->db->query("SELECT fr.id_finished, fr.total_finished, fr.fdate, p.project_name, p.qty_request, c.cust_name FROM finished_report fr JOIN project p ON fr.id_project = p.id_project LEFT JOIN customer c ON p.id_cust = c.id_cust ORDER BY fr.id_finished DESC LIMIT 10")->result();

        $sorting = $this->db->query("SELECT sr.id_sorting, sr.finished, sr.waste, (sr.finished + sr.waste) as qty_output, ps.id_plan, s.staff_name FROM sorting_report sr JOIN plan_shift ps ON sr.id_planshift = ps.id_planshift JOIN staff s ON ps.id_staff = s.id_staff LEFT JOIN planning pl ON ps.id_plan = pl.id_plan ORDER BY sr.id_sorting DESC LIMIT 10")->result();

        $data = [
            'finished' => $finished,
            'sorting' => $sorting,

            'project' => $this->crudModel->getData('project')->num_rows(),
            'planning' => $this->crudModel->getData('planning')->num_rows(),
            'plan_shift' => $this->crudModel->getData('plan_shift')->num_rows(),
            'finished_report' => $this->crudModel->getData('shift_closures')->num_rows(),

            // New card data: capacity per machine for active shifts
            'machine_capacity' => $machine_capacity,

            'content' => 'leader/beranda',
            'navlink' => 'beranda',
        ];

        // Fetch new incidents (status = 0) to show in dashboard notification
        $new_incidents = $this->db->select('ir.*, z.zone_name, pl.line_code, pl.line_name, m.name as machine_name, m.code as machine_code')
                                   ->from('incident_reports ir')
                                   ->join('production_lines pl', 'ir.line_id = pl.id', 'left')
                                   ->join('zones z', 'pl.zone_id = z.zone_id', 'left')
                                   ->join('machines m', 'ir.id_machine = m.id', 'left')
                                   ->where('ir.status', 0)
                                   ->order_by('ir.created_at', 'DESC')
                                   ->get()
                                   ->result();
        $data['new_incidents'] = $new_incidents;
        $data['new_incident_count'] = count($new_incidents);

        $this->load->view('leader/vbackend', $data);
    }

    public function planning()
    {
        // Use UC8 logic: list projects with latest planning record per project
        $sql = "
            SELECT p.*, pl.id_plan, pl.pl_status, pl.qty_target,
                   prod.product_name, prod.diameter, c.cust_name,
                   CASE WHEN (COALESCE(pl.needs_review,0) = 1) THEN 1 ELSE 0 END AS plan_needs_review
            FROM project p
            LEFT JOIN (
                SELECT id_project, MAX(id_plan) AS id_plan
                FROM planning
                GROUP BY id_project
            ) latest ON latest.id_project = p.id_project
            LEFT JOIN planning pl ON pl.id_plan = latest.id_plan
            LEFT JOIN product prod ON p.id_product = prod.id_product
            LEFT JOIN customer c ON p.id_cust = c.id_cust
            ORDER BY p.entry_date DESC, p.id_project DESC
        ";

        $rows = $this->db->query($sql)->result();

        $data = [
            'data' => $rows,
            'content' => 'leader/planning/planning',
            'navlink' => 'planning',
        ];

        $this->load->view('leader/vbackend', $data);
    }

    /**
     * Read-only orders listing for Leader
     */
    public function orders()
    {
        $this->load->model('OrderModel');

        $filters = [
            'keyword' => $this->input->get('keyword', true),
            'status' => $this->input->get('status', true),
            'customer_id' => $this->input->get('customer_id', true),
            'product_id' => $this->input->get('product_id', true),
            'date_from' => $this->input->get('date_from', true),
            'date_to' => $this->input->get('date_to', true),
        ];

        $orders = $this->OrderModel->getAllOrders($filters);

        $data = [
            'orders' => $orders,
            'filters' => $filters,
            'content' => 'leader/order/Orders',
            'navlink' => 'orders',
        ];

        $this->load->view('leader/vbackend', $data);
    }

    /**
     * Read-only order detail for Leader
     */
    public function order($id_project = null)
    {
        $this->load->model('OrderModel');
        $id = $id_project ?? $this->uri->segment(3);
        if (!$id) {
            show_error('Thiếu mã đơn hàng', 400, 'Bad Request');
        }

        $order = $this->OrderModel->getOrderById($id);
        if (!$order) {
            show_error('Không tìm thấy đơn hàng', 404, 'Not Found');
        }

        // Optional: fetch related planning info count
        $plans = $this->db->select('pl.*')
                          ->from('planning pl')
                          ->where('pl.id_project', (int)$id)
                          ->get()->result();

        $data = [
            'order' => $order,
            'plans' => $plans,
            'content' => 'leader/order/OrderView',
            'navlink' => 'orders',
        ];

        $this->load->view('leader/vbackend', $data);
    }

    public function plan_shift()
    {
        if ($this->uri->segment(4) === 'view') {
            $id = $this->uri->segment(3);

            $tampil = $this->db->query('SELECT * FROM planning JOIN project JOIN customer JOIN product WHERE id_plan = ' . $id. ' AND planning.id_project = project.id_project
            AND project.id_cust = customer.id_cust AND project.id_product = product.id_product')->row();

            $data = [
                'plan' => [
                    'id_plan' => $tampil->id_plan,
                    'plan_name' => $tampil->plan_name,
                    'project_name' => $tampil->project_name,
                    'qty_request' => $tampil->qty_request,
                    'qty_target' => $tampil->qty_target,
                    'end_date' => $tampil->end_date,
                    'entry_date' => $tampil->entry_date,
                    'cust_name' => $tampil->cust_name,
                    'address' => $tampil->address,
                    'telp' => $tampil->telp,
                    'email' => $tampil->email,
                    'product_name' => $tampil->product_name,
                    'diameter' => $tampil->diameter,
                ],

                'planshift' => $this->db->query('SELECT * FROM plan_shift JOIN shiftment JOIN staff WHERE id_plan = ' . $id. ' AND plan_shift.id_shift = shiftment.id_shift
                AND plan_shift.id_staff = staff.id_staff')->result(),

                'content' => 'leader/planning/DetailPlanning',
                'navlink' => 'planning',
            ];

        } else {

            $table = 'plan_shift';

            $onjoin = [
                'planning' => $table.'.id_plan=planning.id_plan',
                'shiftment' => $table.'.id_shift=shiftment.id_shift',
                'staff' => $table.'.id_staff=staff.id_staff',
            ];

            $data = [
                'plan_shift' => $this->crudModel->getDataJoin($table, $onjoin),
                'planning' => $this->db->query('SELECT * FROM planning')->result(),
                'shiftment' => $this->db->query('SELECT * FROM shiftment')->result(),
                'staff' => $this->db->query('SELECT * FROM staff')->result(),
                'content' => 'leader/shiftment/shiftment',
                'navlink' => 'plan_shift',
                ];
        }

        $this->load->view('leader/vbackend', $data);
    }

    public function Production()
    {
        if ($this->uri->segment(3) === 'addproduction') {

            $data = [
                'planshift' => $this->db->query('SELECT * FROM plan_shift JOIN staff WHERE plan_shift.id_staff=staff.id_staff AND staff.st_status=2')->result(),
                'planning' => $this->db->query('SELECT * FROM planning WHERE pl_status = 1')->result(),
                'shiftment' => $this->db->query('SELECT * FROM shiftment')->result(),
                'staff' => $this->db->query('SELECT * FROM staff')->result(),

                'content' => 'leader/production/addproduction',
                'navlink' => 'production',
            ];

        } else {
            $table = 'plan_shift';

            $onjoin = [
                'shiftment' => $table.'.id_shift=shiftment.id_shift',
                'staff' => $table.'.id_staff=staff.id_staff',
                'planning' => $table.'.id_plan=planning.id_plan',
                'project' => 'planning.id_project=project.id_project',
            ];

            $data = [
                'production' => $this->crudModel->getDataJoin($table, $onjoin),
                'planning' => $this->db->query('SELECT * FROM planning')->result(),
                'shiftment' => $this->db->query('SELECT * FROM shiftment')->result(),
                'staff' => $this->db->query('SELECT * FROM staff')->result(),
                'project' => $this->db->query('SELECT * FROM project')->result(),
                'content' => 'leader/production/production',
                'navlink' => 'production',
                ];
        }

        $this->load->view('leader/vbackend', $data);
    }

    public function detail_production()
    {
        if ($this->uri->segment(4) === 'view') {
            $id = $this->uri->segment(3);

            $tampil = $this->db->query(
                'SELECT * FROM plan_shift JOIN planning JOIN shiftment JOIN staff JOIN project JOIN product
                WHERE id_planshift = ' . $id. ' AND plan_shift.id_plan = planning.id_plan AND plan_shift.id_shift = shiftment.id_shift
                AND plan_shift.id_staff = staff.id_staff AND planning.id_project = project.id_project
                AND project.id_product = product.id_product')->row();

            $data = [
                'detail' => [
                    'id_planshift' => $tampil->id_planshift,
                    'plan_name' => $tampil->plan_name,
                    'staff_name' => $tampil->staff_name,
                    'shift_name' => $tampil->shift_name,
                    'qty_target' => $tampil->qty_target,
                    'start_date' => $tampil->start_date,
                    'product_name' => $tampil->product_name,
                    'diameter' => $tampil->diameter,
                ],

                'p_machine' => $this->db->query('SELECT * FROM p_machine JOIN machine WHERE id_planshift = ' . $id . ' AND p_machine.id_machine = machine.id_machine')->result(),
                'p_material' => $this->db->query('SELECT * FROM p_material JOIN material WHERE id_planshift = ' . $id. ' AND p_material.id_material = material.id_material')->result(),
                'content' => 'leader/production/DetailProduction',
                'navlink' => 'production',
            ];

        } elseif ($this->uri->segment(4) === 'addsorting') {
            $id = $this->uri->segment(3);

            $tampil = $this->db->query(
                'SELECT * FROM plan_shift JOIN planning JOIN shiftment JOIN staff JOIN project JOIN product
                WHERE id_planshift = ' . $id. ' AND plan_shift.id_plan = planning.id_plan AND plan_shift.id_shift = shiftment.id_shift
                AND plan_shift.id_staff = staff.id_staff AND planning.id_project = project.id_project
                AND project.id_product = product.id_product')->row();

            $data = [
                'detail' => [
                    'id_planshift' => $tampil->id_planshift,
                    'plan_name' => $tampil->plan_name,
                    'staff_name' => $tampil->staff_name,
                    'shift_name' => $tampil->shift_name,
                    'qty_target' => $tampil->qty_target,
                    'start_date' => $tampil->start_date,
                    'product_name' => $tampil->product_name,
                    'diameter' => $tampil->diameter,
                ],

                'p_machine' => $this->db->query('SELECT * FROM p_machine JOIN machine WHERE id_planshift = ' . $id . ' AND p_machine.id_machine = machine.id_machine')->result(),
                'p_material' => $this->db->query('SELECT * FROM p_material JOIN material WHERE id_planshift = ' . $id. ' AND p_material.id_material = material.id_material')->result(),
                'content' => 'leader/production/AddSorting',
                'navlink' => 'production',
            ];
        }

        $this->load->view('leader/vbackend', $data);
    }

    // OLD machine function - renamed to avoid conflict with new Machine module
    // TODO: Migrate to new Machine module or remove if deprecated
    public function machine_old()
    {
        if ($this->uri->segment(3) === 'addmachine') {
        
            $data = [
                'planshift' => $this->db->query('SELECT * FROM plan_shift JOIN staff WHERE plan_shift.id_staff=staff.id_staff AND ps_status = 1 AND staff.st_status=2')->result(),
                'machine' => $this->db->query('SELECT * FROM machine WHERE mc_status = 1')->result(),

                'content' => 'leader/machine/addmachine',
                'navlink' => 'machine',
            ];

        } elseif ($this->uri->segment(3) === 'addnewmachine') {
        
            $data = [
                'content' => 'leader/machine/addnewmachine',
                'navlink' => 'machine',
            ];

        } else {
            $data = [
                'p_machine' => $this->db->query('SELECT * FROM p_machine JOIN plan_shift JOIN machine JOIN staff WHERE p_machine.id_planshift = plan_shift.id_planshift AND p_machine.id_machine = machine.id_machine AND plan_shift.id_staff = staff.id_staff')->result(),
                'machines' => $this->db->query('SELECT * FROM machine')->result(),
                'content' => 'leader/machine/machine',
                'navlink' => 'machine',
            ];
        }

        $this->load->view('leader/vbackend', $data);
    }

    public function addMachine()
    {
            $id_machine = $this->input->post('id_machine');

            $update = [
                'mc_status' => trim($this->input->post('mc_status')),
            ];

            $add = [
                'id_pmachine' => $this->crudModel->generateCode(1, 'id_pmachine', 'p_machine'),
                'id_planshift' => trim($this->input->post('id_planshift')),
                'id_machine' => trim($this->input->post('id_machine')),
                'mc_stats' => trim($this->input->post('mc_stats')),

            ];

            $this->crudModel->updateData('machine', 'id_machine', $id_machine, $update);

            $this->crudModel->addData('p_machine', $add);

            redirect(site_url('leader/machine'));
    }

    public function addNewMachine()
    {
            $add = [
                'id_machine' => $this->crudModel->generateCode(1, 'id_machine', 'machine'),
                'machine_name' => trim($this->input->post('machine_name')),
                'capacity' => trim($this->input->post('capacity')),
                'mc_status' => trim($this->input->post('mc_status')),
            ];

            $this->crudModel->addData('machine', $add);

            redirect(site_url('leader/machine'));
    }


    public function finishMachine()
    {
        $id_pmachine = $this->uri->segment(3);
        
        $id = $this->uri->segment(4);

        $this->db->query('UPDATE p_machine SET mc_stats = 1 WHERE id_machine = ' . $id . '');

        $this->db->query('UPDATE machine SET mc_status = 1 WHERE id_machine = ' . $id . '');

        redirect(site_url('leader/production'));
    }

    public function material()
    {
        // Show warehouse material list in read-only mode for leader
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
            'content' => 'leader/material/Material',
            'navlink' => 'material',
            'is_readonly' => true,
        ];

        $this->load->view('leader/VBackend', $data);
    }

    /**
     * Finished Products Inventory: Xem số lượng thành phẩm trong kho (Read-only for leader)
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
            'content' => 'leader/finished/inventory',
            'navlink' => 'finished_inventory',
        ];
        $this->load->view('leader/VBackend', $data);
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

        redirect(site_url('leader/material'));
    }

    public function addNewMaterial()
    {
            $add = [
                'id_material' => $this->crudModel->generateCode(1, 'id_material', 'material'),
                'material_name' => trim($this->input->post('material_name')),
                'stock' => trim($this->input->post('stock')),
            ];

            $this->crudModel->addData('material', $add);

            redirect(site_url('leader/material'));
    }

    public function deleteMaterial()
    {
        $id_pmaterial = $this->uri->segment(3);

        $id_material = $this->uri->segment(4);

        $used_stock = $this->crudModel->getDataWhere('p_material', 'id_pmaterial', $id_pmaterial)->row();

        $stock = $this->crudModel->getDataWhere('material', 'id_material', $id_material)->row();

        $update = [
            'stock' => $stock->stock + (int) $used_stock->used_stock,
        ];

        $this->crudModel->updateData('material', 'id_material', $id_material, $update);

        $this->crudModel->deleteData('p_material', 'id_pmaterial', $id_pmaterial);

        redirect(site_url('leader/material'));
    }

    public function sorting()
    {
        if ($this->uri->segment(3) === 'addsorting') {
        
                $data = [
                    'planshift' => $this->db->query('SELECT * FROM plan_shift JOIN staff JOIN planning JOIN project WHERE ps_status = 1 AND plan_shift.id_staff=staff.id_staff AND plan_shift.id_plan = planning.id_plan AND planning.id_project = project.id_project ')->result(),
                    'sorting_report' => $this->db->query('SELECT * FROM sorting_report')->result(),
                    'p_machine' => $this->db->query('SELECT * FROM p_machine')->result(),
    
                    'content' => 'leader/sorting/addsorting',
                    'navlink' => 'sorting',
                ];

        } else {
            $table = 'sorting_report';

            $onjoin = [
                'plan_shift' => $table.'.id_planshift=plan_shift.id_planshift',
                'planning' => 'plan_shift.id_plan=planning.id_plan',
                'staff' => 'plan_shift.id_staff=staff.id_staff',
            ];

            $sorting = $this->crudModel->getDataJoin($table, $onjoin);

            $id_project = $this->input->get('id_project', true);
            $id_project = $id_project ? (int)$id_project : null;

            // Projects list for dropdown
            $projects = $this->db->query("SELECT p.*, c.cust_name, pr.product_name FROM project p LEFT JOIN customer c ON p.id_cust = c.id_cust LEFT JOIN product pr ON p.id_product = pr.id_product")->result();

            // Provide a generic `data` list used by the Sorting view.
            // Include planning.id_plan via LEFT JOIN so the view can skip projects that already have planning.
            $orders_data = $this->db->query("SELECT p.*, c.cust_name, pr.product_name, planning.id_plan FROM project p LEFT JOIN customer c ON p.id_cust = c.id_cust LEFT JOIN product pr ON p.id_product = pr.id_product LEFT JOIN planning ON planning.id_project = p.id_project")->result();

            if ($id_project) {
                $project = $this->db->query('SELECT p.*, c.cust_name, pr.product_name FROM project p LEFT JOIN customer c ON p.id_cust = c.id_cust LEFT JOIN product pr ON p.id_product = pr.id_product WHERE p.id_project = ?', [$id_project])->row();

                $total_finished = 0;
                if ($this->db->table_exists('finished_report')) {
                    $total_finished = (int)($this->db->select_sum('total_finished')->where('id_project', $id_project)->get('finished_report')->row()->total_finished ?? 0);
                }

                // Build plan and shift information using robust UC8 logic
                $plan = null;
                $plan_shifts = [];
                $production_shifts_map = [];
                $shiftIds = [];

                // Load plan for project if exists
                if ($this->db->table_exists('planning')) {
                    $plan = $this->db->where('id_project', $id_project)->get('planning')->row();
                }

                // Load plan_shift from multiple possible table names / schemas
                $planShiftTableCandidates = ['plan_shift', 'plan_shifts', 'planshift', 'planshifts'];
                foreach ($planShiftTableCandidates as $tbl) {
                    if ($this->db->table_exists($tbl)) {
                        if (!empty($plan) && $this->db->field_exists('id_plan', $tbl)) {
                            $plan_shifts = $this->db->get_where($tbl, ['id_plan' => $plan->id_plan])->result();
                        } else {
                            // fallback: try to find by project id columns if available
                            $found = false;
                            $projectCols = ['id_project', 'project_id', 'id_project_ref'];
                            foreach ($projectCols as $pc) {
                                if ($this->db->field_exists($pc, $tbl)) {
                                    $rows = $this->db->get_where($tbl, [$pc => $id_project])->result();
                                    if (!empty($rows)) { $plan_shifts = $rows; $found = true; break; }
                                }
                            }
                            if (!$found) {
                                $plan_shifts = $this->db->get($tbl)->result();
                            }
                        }
                        break;
                    }
                }

                // normalize plan_shifts properties to use shift_id and id_planshift
                if (!empty($plan_shifts)) {
                    foreach ($plan_shifts as &$pps_norm) {
                        if (!isset($pps_norm->shift_id) && isset($pps_norm->id_shift)) $pps_norm->shift_id = $pps_norm->id_shift;
                        if (!isset($pps_norm->id_planshift) && isset($pps_norm->id)) $pps_norm->id_planshift = $pps_norm->id;
                    }
                    unset($pps_norm);
                }

                // totals: keep total_received if available but don't load details
                $total_received = 0;
                if ($this->db->table_exists('finished_receipt')) {
                    $total_received = (int)($this->db->select_sum('quantity_received')->where('id_project', $id_project)->get('finished_receipt')->row()->quantity_received ?? 0);
                }

                // issues
                $issues = [];
                $total_issued = 0;
                if ($this->db->table_exists('finished_issue')) {
                    $issues = $this->db->where('id_project', $id_project)->get('finished_issue')->result();
                    $total_issued = (int)($this->db->select_sum('quantity_issued')->where('id_project', $id_project)->get('finished_issue')->row()->quantity_issued ?? 0);
                }

                // Attempt to load production_shifts and map them by shift_id/id
                if ($this->db->table_exists('production_shifts')) {
                    $ps_rows = [];
                    if (!empty($plan->id_plan) && ($this->db->field_exists('id_plan', 'production_shifts') || $this->db->field_exists('plan_id', 'production_shifts'))) {
                        $field = $this->db->field_exists('id_plan', 'production_shifts') ? 'id_plan' : 'plan_id';
                        $ps_rows = $this->db->where($field, $plan->id_plan)->get('production_shifts')->result();
                    } else {
                        $shiftIdsTmp = array_map(function($ps) { return isset($ps->shift_id) ? $ps->shift_id : (isset($ps->id_shift) ? $ps->id_shift : null); }, $plan_shifts);
                        $shiftIdsTmp = array_filter($shiftIdsTmp, function($v) { return !is_null($v) && $v !== ''; });
                        if (!empty($shiftIdsTmp)) {
                            $ps_rows = $this->db->where_in('shift_id', $shiftIdsTmp)->get('production_shifts')->result();
                        }
                    }

                    if (!empty($ps_rows)) {
                        foreach ($ps_rows as $r) {
                            $key = isset($r->shift_id) ? $r->shift_id : (isset($r->id) ? $r->id : null);
                            if ($key !== null) $production_shifts_map[$key] = $r;
                        }
                        if (empty($shiftIds)) { $shiftIds = array_keys($production_shifts_map); }

                        // attach production shift details into plan_shift entries when possible
                        if (!empty($plan_shifts)) {
                            foreach ($plan_shifts as &$pps) {
                                $key = isset($pps->shift_id) ? $pps->shift_id : ($pps->id_shift ?? null);
                                $pps->production_shift = ($key !== null) ? ($production_shifts_map[$key] ?? null) : null;
                            }
                            unset($pps);
                        }
                    } else {
                        // fallback: try locate production_shifts by project id columns
                        $projCols = ['id_project', 'project_id', 'id_project_ref'];
                        foreach ($projCols as $pc) {
                            if ($this->db->field_exists($pc, 'production_shifts')) {
                                $rows = $this->db->get_where('production_shifts', [$pc => $id_project])->result();
                                if (!empty($rows)) {
                                    foreach ($rows as $r) {
                                        $key = isset($r->shift_id) ? $r->shift_id : (isset($r->id) ? $r->id : null);
                                        if ($key !== null) $production_shifts_map[$key] = $r;
                                    }
                                    $shiftIds = array_keys($production_shifts_map);
                                    break;
                                }
                            }
                        }
                    }

                    // If there are production shifts but no plan_shifts, convert production_shifts into plan_shifts-like objects
                    if (empty($plan_shifts) && !empty($production_shifts_map)) {
                        $plan_shifts = [];
                        foreach ($production_shifts_map as $key => $r) {
                            $obj = new stdClass();
                            $obj->id_planshift = $r->shift_id ?? ($r->id ?? null);
                            $obj->shift_id = $r->shift_id ?? ($r->id ?? null);
                            $obj->id_staff = $r->id_staff ?? ($r->staff_id ?? null);
                            $obj->shift_date = $r->shift_date ?? $r->start_date ?? null;
                            $obj->start_time = $r->start_time ?? null;
                            $obj->end_time = $r->end_time ?? null;
                            $obj->start_date = $r->start_date ?? $r->shift_date ?? ($r->start_time ?? null);
                            $obj->end_date = $r->end_date ?? $r->end_time ?? null;
                            if (isset($r->shift_name) && $r->shift_name !== '') {
                                $obj->shift_name = $r->shift_name;
                            } elseif (isset($r->shift_code) && $r->shift_code !== '') {
                                $obj->shift_name = $r->shift_code;
                            } else {
                                $obj->shift_name = 'Shift ' . ($obj->shift_id ?? '');
                            }
                            $plan_shifts[] = $obj;
                        }
                    }
                }

                // attach production_shifts_map into data so view can use it if needed
                $data_production_shifts_map = $production_shifts_map;

                // find shift closures related to this project using production shift IDs first, then fallback
                $shift_closures = [];
                if ($this->db->table_exists('shift_closures')) {
                    if (!empty($shiftIds)) {
                        $rows = $this->db->where_in('shift_id', $shiftIds)->get('shift_closures')->result();
                        if (!empty($rows)) $shift_closures = $rows;
                    }
                    if (empty($shift_closures)) {
                        $rows = $this->db->query('SELECT sc.* FROM shift_closures sc LEFT JOIN finished_report fr ON sc.warehouse_request_id = fr.id_finished WHERE fr.id_project = ?', [$id_project])->result();
                        if (!empty($rows)) $shift_closures = $rows;
                    }
                }

                // Filter closures by known shiftIds
                if (!empty($shift_closures) && !empty($shiftIds)) {
                    $shiftIdsStr = array_map('strval', $shiftIds);
                    $shift_closures = array_values(array_filter($shift_closures, function($sc) use ($shiftIdsStr) {
                        $sid = isset($sc->shift_id) ? $sc->shift_id : (isset($sc->shiftId) ? $sc->shiftId : null);
                        if ($sid === null) return false;
                        return in_array((string)$sid, $shiftIdsStr, true);
                    }));
                }

                // attach production shift details to closures when available
                if (!empty($shift_closures) && !empty($production_shifts_map)) {
                    foreach ($shift_closures as &$sc) {
                        $sc->production_shift = $production_shifts_map[$sc->shift_id] ?? null;
                    }
                    unset($sc);
                }

                $data = [
                    'sorting' => $sorting,
                    // view expects `$data` variable (list of orders/projects)
                    'data' => $orders_data,
                    'projects' => $projects,
                    'selected_project_id' => $id_project,
                    'project' => $project,
                    'total_finished' => $total_finished,
                    'plan' => $plan,
                    'plan_shifts' => $plan_shifts,
                    'issues' => $issues,
                    'total_received' => $total_received,
                    'total_issued' => $total_issued,
                    'shift_closures' => $shift_closures,
                    'production_shifts_map' => $data_production_shifts_map ?? [],
                    'content' => 'leader/sorting/Sorting',
                    'navlink' => 'sorting',
                ];
            } else {
                $data = [
                    'sorting' => $sorting,
                    'data' => $orders_data,
                    'projects' => $projects,
                    'selected_project_id' => null,
                    'project' => null,
                    'total_finished' => 0,
                    'plan' => null,
                    'plan_shifts' => [],
                    'issues' => [],
                    'total_received' => 0,
                    'total_issued' => 0,
                    'shift_closures' => [],
                    'content' => 'leader/sorting/Sorting',
                    'navlink' => 'sorting',
                ];
            }
        }

        // Add incident notifications (used across leader views)
        $new_incidents = $this->db->where('status', 0)->order_by('created_at', 'DESC')->get('incident_reports')->result();
        $data['new_incidents'] = $new_incidents;
        $data['new_incident_count'] = count($new_incidents);

        $this->load->view('leader/vbackend', $data);
    }

    public function detail_sorting()
    {
        if ($this->uri->segment(4) === 'view') {

            $id = $this->uri->segment(3);

            $tampil = $this->db->query(
                'SELECT * FROM plan_shift JOIN planning JOIN shiftment JOIN staff JOIN project JOIN product
                WHERE id_planshift = ' . $id. ' AND plan_shift.id_plan = planning.id_plan AND plan_shift.id_shift = shiftment.id_shift
                AND plan_shift.id_staff = staff.id_staff AND planning.id_project = project.id_project
                AND project.id_product = product.id_product')->row();
            
            $tampil2 = $this->db->query('SELECT * FROM sorting_report WHERE id_planshift = ' . $id. '')->row();
            if (empty($tampil)) {
                show_error('Không tìm thấy ca/plan liên quan (id_planshift=' . htmlspecialchars($id) . ').', 404);
                return;
            }

            // Ensure sorting report row exists before reading properties
            $tampil2 = $tampil2 ?? (object) ['waste' => 0, 'finished' => 0];

            $data = [
                'detail' => [
                    'plan_name' => $tampil->plan_name ?? '-',
                    'staff_name' => $tampil->staff_name ?? '-',
                    'shift_name' => $tampil->shift_name ?? '-',
                    'qty_target' => $tampil->qty_target ?? 0,
                    'start_date' => $tampil->start_date ?? '-',
                    'product_name' => $tampil->product_name ?? '-',
                    'diameter' => $tampil->diameter ?? '-',
                    'waste' => $tampil2->waste ?? 0,
                    'finished' => $tampil2->finished ?? 0,

                ],

                'p_machine' => $this->db->query('SELECT * FROM p_machine JOIN machine WHERE id_planshift = ' . $id . ' AND p_machine.id_machine = machine.id_machine')->result(),
                'p_material' => $this->db->query('SELECT * FROM p_material JOIN material WHERE id_planshift = ' . $id. ' AND p_material.id_material = material.id_material')->result(),
                'content' => 'leader/sorting/DetailSorting',
                'navlink' => 'sorting',
            ];


    // Get new incidents (for notification box)
    $new_incidents = $this->db->where('status', 0)->order_by('created_at', 'DESC')->get('incident_reports')->result();
    $data['new_incidents'] = $new_incidents;
    $data['new_incident_count'] = count($new_incidents);

    $this->load->view('leader/vbackend', $data);
        } else {

            $id = $this->uri->segment(3);

            $tampil = $this->db->query(
                'SELECT * FROM plan_shift JOIN planning JOIN shiftment JOIN staff JOIN project JOIN product
                WHERE id_planshift = ' . $id. ' AND plan_shift.id_plan = planning.id_plan AND plan_shift.id_shift = shiftment.id_shift
                AND plan_shift.id_staff = staff.id_staff AND planning.id_project = project.id_project
                AND project.id_product = product.id_product')->row();

            $tampil2 = $this->db->query('SELECT * FROM sorting_report WHERE id_planshift = ' . $id. '')->row();

            if (empty($tampil)) {
                show_error('Không tìm thấy ca/plan liên quan (id_planshift=' . htmlspecialchars($id) . ').', 404);
                return;
            }

            $tampil2 = $tampil2 ?? (object) ['waste' => 0, 'finished' => 0];

            $data = [
                'detail' => [
                    'plan_name' => $tampil->plan_name ?? '-',
                    'staff_name' => $tampil->staff_name ?? '-',
                    'shift_name' => $tampil->shift_name ?? '-',
                    'qty_target' => $tampil->qty_target ?? 0,
                    'start_date' => $tampil->start_date ?? '-',
                    'product_name' => $tampil->product_name ?? '-',
                    'diameter' => $tampil->diameter ?? '-',
                    'waste' => $tampil2->waste ?? 0,
                    'finished' => $tampil2->finished ?? 0,

                ],

                'p_machine' => $this->db->query('SELECT * FROM p_machine JOIN machine WHERE id_planshift = ' . $id . ' AND p_machine.id_machine = machine.id_machine')->result(),
                'p_material' => $this->db->query('SELECT * FROM p_material JOIN material WHERE id_planshift = ' . $id. ' AND p_material.id_material = material.id_material')->result(),
                'content' => 'leader/sorting/sortingprint',
            ];

            $this->load->view('leader/vprint', $data);

        }

    }
    
    /**
     * Leader: Detail report view
     * URL: leader/detail_report/?id_project=123
     */
    public function detail_report()
    {
        $id_project = $this->input->get('id_project') ?: $this->uri->segment(3);

        // load project list for selector
        $projects = $this->db->select('p.id_project, p.project_name, c.cust_name, pr.product_name')
                              ->from('project p')
                              ->join('customer c', 'p.id_cust = c.id_cust', 'left')
                              ->join('product pr', 'p.id_product = pr.id_product', 'left')
                              ->order_by('p.entry_date', 'DESC')
                              ->get()->result();

        $data = [
            'navlink' => 'report',
            'projects' => $projects,
            'selected_project_id' => $id_project,
        ];

        if (empty($id_project)) {
            $data['content'] = 'leader/sorting/detail_report';
            $this->load->view('leader/vbackend', $data);
            return;
        }

        $data['content'] = 'leader/sorting/detail_report';

        $this->load->model('OrderModel');
        $this->load->model('PlanModel');

        $order = $this->OrderModel->getOrderById($id_project);
        $data['project'] = $order;

        // latest plan
        $requested_plan = $this->input->get('id_plan') ?: null;
        if (!empty($requested_plan)) {
            $plan = $this->PlanModel->getPlanById($requested_plan);
        } else {
            $plan_row = $this->db->order_by('id_plan', 'DESC')->get_where('planning', ['id_project' => $id_project])->row();
            $plan = $plan_row ? $this->PlanModel->getPlanById($plan_row->id_plan) : null;
        }
        $data['plan'] = $plan;

        // plan shifts + attach production shift info when available (reuse UC8 logic)
        $plan_shifts = [];
        $production_shifts_map = [];
        $shiftIds = [];

        $planShiftTableCandidates = ['plan_shift', 'plan_shifts', 'planshift', 'planshifts'];
        foreach ($planShiftTableCandidates as $tbl) {
            if ($this->db->table_exists($tbl)) {
                if ($plan && $this->db->field_exists('id_plan', $tbl)) {
                    $plan_shifts = $this->db->get_where($tbl, ['id_plan' => $plan->id_plan])->result();
                } else {
                    $found = false;
                    $projectCols = ['id_project', 'project_id', 'id_project_ref'];
                    foreach ($projectCols as $pc) {
                        if ($this->db->field_exists($pc, $tbl)) {
                            $rows = $this->db->get_where($tbl, [$pc => $id_project])->result();
                            if (!empty($rows)) { $plan_shifts = $rows; $found = true; break; }
                        }
                    }
                    if (!$found) { $plan_shifts = $this->db->get($tbl)->result(); }
                }
                break;
            }
        }

        if (!empty($plan_shifts)) {
            foreach ($plan_shifts as &$pps_norm) {
                if (!isset($pps_norm->shift_id) && isset($pps_norm->id_shift)) $pps_norm->shift_id = $pps_norm->id_shift;
                if (!isset($pps_norm->id_planshift) && isset($pps_norm->id)) $pps_norm->id_planshift = $pps_norm->id;
            }
            unset($pps_norm);
        }

        if ($this->db->table_exists('production_shifts')) {
            $ps_rows = [];
            if (!empty($plan->id_plan) && ($this->db->field_exists('id_plan', 'production_shifts') || $this->db->field_exists('plan_id', 'production_shifts'))) {
                $field = $this->db->field_exists('id_plan', 'production_shifts') ? 'id_plan' : 'plan_id';
                $ps_rows = $this->db->where($field, $plan->id_plan)->get('production_shifts')->result();
            } else {
                $shiftIds = array_map(function($ps) { return isset($ps->shift_id) ? $ps->shift_id : (isset($ps->id_shift) ? $ps->id_shift : null); }, $plan_shifts);
                $shiftIds = array_filter($shiftIds, function($v) { return !is_null($v) && $v !== ''; });
                if (!empty($shiftIds)) {
                    $ps_rows = $this->db->where_in('shift_id', $shiftIds)->get('production_shifts')->result();
                }
            }

            if (!empty($ps_rows)) {
                foreach ($ps_rows as $r) {
                    $key = isset($r->shift_id) ? $r->shift_id : (isset($r->id) ? $r->id : null);
                    if ($key !== null) $production_shifts_map[$key] = $r;
                }
                if (empty($shiftIds)) { $shiftIds = array_keys($production_shifts_map); }

                if (!empty($plan_shifts)) {
                    foreach ($plan_shifts as &$pps) {
                        $key = isset($pps->shift_id) ? $pps->shift_id : ($pps->id_shift ?? null);
                        $pps->production_shift = ($key !== null) ? ($production_shifts_map[$key] ?? null) : null;
                    }
                    unset($pps);
                }
            } else {
                $projCols = ['id_project', 'project_id', 'id_project_ref'];
                foreach ($projCols as $pc) {
                    if ($this->db->field_exists($pc, 'production_shifts')) {
                        $rows = $this->db->get_where('production_shifts', [$pc => $id_project])->result();
                        if (!empty($rows)) {
                            foreach ($rows as $r) {
                                $key = isset($r->shift_id) ? $r->shift_id : (isset($r->id) ? $r->id : null);
                                if ($key !== null) $production_shifts_map[$key] = $r;
                            }
                            $shiftIds = array_keys($production_shifts_map);
                            break;
                        }
                    }
                }
            }

            if (empty($plan_shifts) && !empty($production_shifts_map)) {
                $plan_shifts = [];
                foreach ($production_shifts_map as $key => $r) {
                    $obj = new stdClass();
                    $obj->id_planshift = $r->shift_id ?? ($r->id ?? null);
                    $obj->shift_id = $r->shift_id ?? ($r->id ?? null);
                    $obj->id_staff = $r->id_staff ?? ($r->staff_id ?? null);
                    $obj->shift_date = $r->shift_date ?? $r->start_date ?? null;
                    $obj->start_time = $r->start_time ?? null;
                    $obj->end_time = $r->end_time ?? null;
                    $obj->start_date = $r->start_date ?? $r->shift_date ?? ($r->start_time ?? null);
                    $obj->end_date = $r->end_date ?? $r->end_time ?? null;
                    if (isset($r->shift_name) && $r->shift_name !== '') {
                        $obj->shift_name = $r->shift_name;
                    } elseif (isset($r->shift_code) && $r->shift_code !== '') {
                        $obj->shift_name = $r->shift_code;
                    } else {
                        $obj->shift_name = 'Shift ' . ($obj->shift_id ?? '');
                    }
                    $plan_shifts[] = $obj;
                }
            }
        }

        $data['plan_shifts'] = $plan_shifts;
        $data['production_shifts_map'] = $production_shifts_map;

        $data['total_finished'] = (int)($this->db->select_sum('total_finished')->where('id_project', $id_project)->get('finished_report')->row()->total_finished ?? 0);
        $data['total_received'] = (int)($this->db->select_sum('quantity_received')->where('id_project', $id_project)->get('finished_receipt')->row()->quantity_received ?? 0);
        $data['total_issued'] = (int)($this->db->select_sum('quantity_issued')->where('id_project', $id_project)->get('finished_issue')->row()->quantity_issued ?? 0);

        $shift_closures = [];
        if ($this->db->table_exists('shift_closures')) {
            if (!empty($shiftIds)) {
                $rows = $this->db->where_in('shift_id', $shiftIds)->get('shift_closures')->result();
                if (!empty($rows)) $shift_closures = $rows;
            }
            if (empty($shift_closures)) {
                $rows = $this->db->query('SELECT sc.* FROM shift_closures sc LEFT JOIN finished_report fr ON sc.warehouse_request_id = fr.id_finished WHERE fr.id_project = ?', [$id_project])->result();
                if (!empty($rows)) $shift_closures = $rows;
            }
        }

        if (!empty($shift_closures) && !empty($production_shifts_map)) {
            foreach ($shift_closures as &$sc) {
                $sc->production_shift = $production_shifts_map[$sc->shift_id] ?? null;
            }
            unset($sc);
        }

        $data['shift_closures'] = $shift_closures;

        $new_incidents = $this->db->where('status', 0)->order_by('created_at', 'DESC')->get('incident_reports')->result();
        $data['new_incidents'] = $new_incidents;
        $data['new_incident_count'] = count($new_incidents);

        $this->load->view('leader/vbackend', $data);
    }
    
    public function addSorting2()
    {
        $id_planshift = $this->input->post('id_planshift');
        //var_dump($id_planshift);

        $finished = $this->crudModel->getDataWhere('sorting_report', 'id_planshift', $id_planshift)->row();
        
        $id_project = $this->db->query('SELECT planning.id_project FROM plan_shift JOIN planning
        WHERE id_planshift ='.$id_planshift.' AND plan_shift.id_plan = planning.id_plan')->row();
        
        $finished_report = $this->db->query('SELECT * FROM finished_report WHERE id_project = '.$id_project->id_project.'')->row();

        $add = [
            'id_sorting' => $this->crudModel->generateCode(1, 'id_sorting', 'sorting_report'),
            'id_planshift' => trim($this->input->post('id_planshift')),
            'waste' => trim($this->input->post('waste')),
            'finished' => trim($this->input->post('finished')),
        ];

        if (!empty($finished_report->id_project)){

            $update_total = [
                'total_finished' => $add['finished'] + (int) $finished_report->total_finished,
            ];
    
            $this->crudModel->updateData('finished_report', 'id_project', $id_project->id_project, $update_total);
            
        } else {

        $add_finished = [
            'id_finished' => $this->crudModel->generateCode(1, 'id_finished', 'finished_report'),
            'id_project' => $id_project->id_project,
            'total_finished' => $add['finished']
        ];

        $this->crudModel->addData('finished_report', $add_finished);

        }
        
        $update = [
            'ps_status' => trim($this->input->post('ps_status')),
        ];

        $this->crudModel->addData('sorting_report', $add);
        $this->crudModel->updateData('plan_shift', 'id_planshift', $id_planshift, $update);
        $this->session->set_flashdata('flash', 'ditambah');

        redirect(site_url('leader/sorting'));
    }
    
    public function detailplanning()
    {
        $data = [
            'content' => 'leader/planning/DetailPlanning',
            'navlink' => 'detailplanning',
        ];

        $this->load->view('leader/vbackend', $data);
    }

    // =====================================================
    // STAFF MANAGEMENT (Quản lý Nhân sự)
    // Only Leader has full CRUD access
    // =====================================================
    
    public function staff()
    {
        // Check staff management permission (Technical NOT allowed)
        $this->check_staff_permission();

        // Support filters: department, position, status, search_code
        $department = $this->input->get('department');
        $position = $this->input->get('position');
        $status = $this->input->get('status');
        $search_code = $this->input->get('search_code');

        if ($this->uri->segment(4) === 'add') {
            // Get roles for department dropdown
            $roles = $this->db->select('role_id, role_display_name')->where('is_active', 1)->get('roles')->result_array();
            // Get users for position dropdown (using full_name)
            $users = $this->db->select('user_id, full_name')->where('is_active', 1)->get('user')->result_array();
            $data = [
                'staff' => $this->db->query('SELECT * FROM staff')->result(),
                'roles' => $roles,
                'users' => $users,
                'content' => 'leader/staff/addstaff',
                'navlink' => 'staff',
            ];
        } elseif ($this->uri->segment(4) === 'update') {
            $id = $this->uri->segment(3);
            
            $tampil = $this->crudModel->getDataWhere('staff', 'id_staff', $id)->row();
            // Get roles for department dropdown
            $roles = $this->db->select('role_id, role_display_name')->where('is_active', 1)->get('roles')->result_array();
            // Get users for position dropdown (using full_name)
            $users = $this->db->select('user_id, full_name')->where('is_active', 1)->get('user')->result_array();
            $data = [
                'detail' => [
                    'id_staff' => $tampil->id_staff,
                    'staff_name' => $tampil->staff_name,
                    'phone' => $tampil->phone,
                    'email' => $tampil->email,
                    'department' => $tampil->department,
                    'position' => $tampil->position,
                    'st_status' => $tampil->st_status,
                ],
                'roles' => $roles,
                'users' => $users,
                'content' => 'leader/staff/updatestaff',
                'navlink' => 'staff',
                ];
        } elseif ($this->uri->segment(4) === 'delete') {
            $id = $this->uri->segment(3);
            
            $tampil = $this->crudModel->getDataWhere('staff', 'id_staff', $id)->row();
            $data = [
                'detail' => [
                    'id_staff' => $tampil->id_staff,
                    'staff_name' => $tampil->staff_name,
                    'phone' => $tampil->phone,
                    'email' => $tampil->email,
                    'st_status' => $tampil->st_status,
                ],
                'content' => 'leader/staff/deletestaff',
                'navlink' => 'staff',
                ];
        } else {
            // Support filters: department, position, status, search_code
            $department = $this->input->get('department');
            $position = $this->input->get('position');
            $status = $this->input->get('status');
            $search_code = $this->input->get('search_code');
            $this->db->select('staff.*, roles.role_display_name as department_name');
            $this->db->from('staff');
            $this->db->join('roles', 'staff.department = roles.role_id', 'left');
            if ($department) {
                $this->db->where('staff.department', $department);
            }
            if ($position) {
                $this->db->where('staff.position', $position);
            }
            if ($status !== null && $status !== '') {
                $this->db->where('staff.st_status', $status);
            }
            if ($search_code) {
                $this->db->where('staff.id_staff', $search_code);
            }
            $results = $this->db->get()->result();
            // Get statistics
            $stats = [];
            $stats['total'] = $this->db->count_all('staff');
            $stats['active'] = $this->db->where('st_status', 1)->count_all_results('staff');
            // Count staff with user accounts using JOIN
            $this->db->select('COUNT(DISTINCT staff.id_staff) as count');
            $this->db->from('staff');
            $this->db->join('user', 'staff.id_staff = user.staff_id', 'left');
            $this->db->where('user.staff_id IS NOT NULL');
            $with_user_result = $this->db->get()->row();
            $stats['with_user'] = $with_user_result ? $with_user_result->count : 0;
            $stats['without_user'] = $stats['total'] - $stats['with_user'];
            // Get departments and positions for filters
            $departments = [];
            $positions = [];
            $departments_result = $this->db->select('role_id, role_display_name')->where('is_active', 1)->get('roles')->result_array();
            foreach ($departments_result as $dept) {
                $departments[$dept['role_id']] = $dept['role_display_name'];
            }
            $positions_result = $this->db->select('user_id, full_name')->where('is_active', 1)->get('user')->result_array();
            foreach ($positions_result as $pos) {
                $positions[$pos['user_id']] = $pos['full_name'];
            }
            $data = [
                'staff' => $results,
                'statistics' => $stats,
                'departments' => $departments,
                'positions' => $positions,
                'content' => 'leader/staff/staff',
                'navlink' => 'staff',
            ];
        }

        $this->load->view('leader/vbackend', $data);
    }

    public function addStaff()
    {
        // Check staff management permission (Technical NOT allowed)
        $this->check_staff_permission();

        $staff_code = trim($this->input->post('id_staff_custom'));
        $staff_name = trim($this->input->post('staff_name'));
        $phone = trim($this->input->post('phone'));
        $email = trim($this->input->post('email'));
        $department = trim($this->input->post('department'));
        $position = trim($this->input->post('position'));
        $st_status = trim($this->input->post('st_status', 1));

        // Validate phone: allow + and digits, length 7-15
        if (!preg_match('/^\+?\d{7,15}$/', $phone)) {
            $this->session->set_flashdata('error', 'Số điện thoại không hợp lệ (7-15 chữ số, có thể có +)');
            redirect(site_url('leader/staff/addstaff'));
            return;
        }

        // Validate email using filter_var
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->session->set_flashdata('error', 'Email không hợp lệ');
            redirect(site_url('leader/staff/addstaff'));
            return;
        }

        // Check staff code uniqueness if provided
        if (!empty($staff_code)) {
            $exists = $this->db->where('id_staff', $staff_code)->get('staff')->row();
            if ($exists) {
                // Provide retry/cancel options via flash message and redirect back
                $this->session->set_flashdata('error', 'Mã nhân viên đã tồn tại. Vui lòng chọn "Nhập lại" hoặc "Hủy".');
                $this->session->set_flashdata('code_exists', true);
                redirect(site_url('leader/staff/addstaff'));
                return;
            }
        } else {
            // Generate code if not provided
            $staff_code = $this->crudModel->generateCode(1, 'id_staff', 'staff');
        }

        $add = [
            'id_staff' => $staff_code,
            'staff_name' => $staff_name,
            'phone' => $phone,
            'email' => $email,
            'department' => $department,
            'position' => $position,
            'st_status' => $st_status,
        ];

        $this->crudModel->addData('staff', $add);

        $this->session->set_flashdata('flash', 'ditambah');

        redirect(site_url('leader/staff'));
    }

    public function updateStaff()
    {
        // Check staff management permission (Technical NOT allowed)
        $this->check_staff_permission();

        $id_staff = $this->input->post('id_staff');
        $staff_name = trim($this->input->post('staff_name'));
        $phone = trim($this->input->post('phone'));
        $email = trim($this->input->post('email'));
        $department = trim($this->input->post('department'));
        $position = trim($this->input->post('position'));
        $st_status = trim($this->input->post('st_status', 1));
        // Validate phone: allow + and digits, length 7-15
        if (!preg_match('/^\+?\d{7,15}$/', $phone)) {
            $this->session->set_flashdata('error', 'Số điện thoại không hợp lệ (7-15 chữ số, có thể có +)');
            redirect(site_url('leader/staff/'.$id_staff.'/update'));
            return;
        }

        // Validate email using filter_var
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->session->set_flashdata('error', 'Email không hợp lệ');
            redirect(site_url('leader/staff/'.$id_staff.'/update'));
            return;
        }

            // Check phone uniqueness (exclude current staff)
            $exists = $this->db->where('phone', $phone)->where('id_staff !=', $id_staff)->get('staff')->row();
            if ($exists) {
                $this->session->set_flashdata('error', 'Số điện thoại đã tồn tại cho nhân viên khác');
                redirect(site_url('leader/staff/'.$id_staff.'/update'));
                return;
            }

            $update = [
            'staff_name' => $staff_name,
            'phone' => $phone,
            'email' => $email,
            'department' => $department,
            'position' => $position,
            'st_status' => $st_status,
        ];

        $this->crudModel->updateData('staff', 'id_staff', $id_staff, $update);

        $this->session->set_flashdata('flash', 'diubah');

        redirect(site_url('leader/staff'));
    }

    public function deleteStaff()
    {
        // Check staff management permission (Technical NOT allowed)
        $this->check_staff_permission();

        $id_staff = $this->uri->segment(3);

        $this->crudModel->deleteData('staff', 'id_staff', $id_staff);

        $this->session->set_flashdata('flash', 'dihapus');

        redirect(site_url('leader/staff'));
    }

    public function toggleStaffStatus()
    {
        // Check staff management permission (Technical NOT allowed)
        $this->check_staff_permission();

        // Chuyển trạng thái tuần tự: 1 -> 2 -> 3 -> 1
        $id_staff = $this->uri->segment(3);

        $staff = $this->crudModel->getDataWhere('staff', 'id_staff', $id_staff)->row();
        if (empty($staff)) {
            $this->session->set_flashdata('error', 'Nhân sự không tồn tại');
            redirect(site_url('leader/staff'));
            return;
        }

        // Cycle through three statuses
        if ($staff->st_status == 1) {
            $new_status = 2; // Đã xếp lịch
        } elseif ($staff->st_status == 2) {
            $new_status = 3; // Ngừng hoạt động
        } else {
            $new_status = 1; // Sẵn sàng
        }

        $update = [
            'st_status' => $new_status,
        ];

        $this->crudModel->updateData('staff', 'id_staff', $id_staff, $update);

        $status_text = ($new_status == 1) ? 'Sẵn sàng' : (($new_status == 2) ? 'Đã xếp lịch' : 'Ngừng hoạt động');
        $this->session->set_flashdata('flash', 'Trạng thái nhân sự đã thay đổi thành ' . $status_text);

        redirect(site_url('leader/staff'));
    }

    public function logout()
    {
        $this->session->unset_userdata('role');
        $this->session->unset_userdata('user_id');
        redirect('login/');
    }
    
    // ================================================================================
    // SHIFT CLOSURE METHODS - Migration 017
    // ================================================================================
    
    /**
     * Bắt đầu ca làm việc
     */
    public function start_shift($shift_id)
    {
        try {
            $this->load->model('leader/ShiftClosureModel', 'shiftClosure');
            
            // Check if shift exists and not already started
            $shift = $this->db->where('shift_id', $shift_id)->get('production_shifts')->row();
            
            if (!$shift) {
                $this->session->set_flashdata('error', 'Không tìm thấy ca làm việc');
                redirect('leader/shift/detail/' . $shift_id);
                return;
            }
            
            if ($shift->shift_status == 2) {
                $this->session->set_flashdata('warning', 'Ca này đã được bắt đầu');
                redirect('leader/shift/detail/' . $shift_id);
                return;
            }
            
            if ($shift->shift_status == 3 || $shift->is_closed == 1) {
                $this->session->set_flashdata('error', 'Ca này đã hoàn thành hoặc đóng');
                redirect('leader/shift/detail/' . $shift_id);
                return;
            }
            
            // Update status to Running
            $user_id = $this->session->userdata('user_id');
            $result = $this->shiftClosure->updateShiftStatus($shift_id, 2, $user_id);
            
            if ($result) {
                $this->session->set_flashdata('success', 'Đã bắt đầu ca làm việc');
            } else {
                $this->session->set_flashdata('error', 'Lỗi khi bắt đầu ca');
            }
            
        } catch (Exception $e) {
            log_message('error', 'Leader::start_shift() - Error: ' . $e->getMessage());
            $this->session->set_flashdata('error', 'Lỗi hệ thống: ' . $e->getMessage());
        }
        
        redirect('leader/shift/detail/' . $shift_id);
    }
    
    /**
     * Kết thúc ca và hiển thị form chốt ca
     */
    public function end_shift($shift_id)
    {
        try {
            $this->load->model('leader/ShiftClosureModel', 'shiftClosure');
            
            // Check if shift exists and is running
            $shift = $this->db->where('shift_id', $shift_id)->get('production_shifts')->row();
            
            if (!$shift) {
                $this->session->set_flashdata('error', 'Không tìm thấy ca làm việc');
                redirect('leader/shift/detail/' . $shift_id);
                return;
            }
            
            if ($shift->shift_status != 2) {
                $this->session->set_flashdata('error', 'Ca phải ở trạng thái "Đang chạy" để kết thúc');
                redirect('leader/shift/detail/' . $shift_id);
                return;
            }
            
            if ($shift->is_closed == 1) {
                $this->session->set_flashdata('error', 'Ca này đã được chốt');
                redirect('leader/shift/detail/' . $shift_id);
                return;
            }
            
            // Get aggregated data for closure form (model helper)
            $closure_data = $this->shiftClosure->aggregateShiftData($shift_id);

            if (!$closure_data) {
                $this->session->set_flashdata('error', 'Không thể tổng hợp dữ liệu ca');
                redirect('leader/shift/detail/' . $shift_id);
                return;
            }

            // Get defect reasons for dropdown
            $defect_reasons = $this->shiftClosure->getDefectReasons();

            // Attempt to locate related production shifts and shift_closures similar to UC8 logic
            $production_shifts_map = [];
            $shiftIds = [];
            $plan_id = null;
            if (!empty($closure_data['shift'])) {
                // closure_data['shift'] may be array or object
                $s = $closure_data['shift'];
                if (is_array($s) && isset($s['id_plan'])) $plan_id = $s['id_plan'];
                if (is_object($s) && isset($s->id_plan)) $plan_id = $s->id_plan;
            }

            if ($this->db->table_exists('production_shifts')) {
                $ps_rows = [];
                if (!empty($plan_id) && ($this->db->field_exists('id_plan', 'production_shifts') || $this->db->field_exists('plan_id', 'production_shifts'))) {
                    $field = $this->db->field_exists('id_plan', 'production_shifts') ? 'id_plan' : 'plan_id';
                    $ps_rows = $this->db->where($field, $plan_id)->get('production_shifts')->result();
                }

                if (!empty($ps_rows)) {
                    foreach ($ps_rows as $r) {
                        $key = isset($r->shift_id) ? $r->shift_id : (isset($r->id) ? $r->id : null);
                        if ($key !== null) $production_shifts_map[$key] = $r;
                    }
                    $shiftIds = array_keys($production_shifts_map);
                }
            }

            // Find related shift closures: prefer closures by shift_id, fallback to those referencing finished_report for this shift's project
            $shift_closures = [];
            if ($this->db->table_exists('shift_closures')) {
                if (!empty($shiftIds)) {
                    $rows = $this->db->where_in('shift_id', $shiftIds)->get('shift_closures')->result();
                    if (!empty($rows)) $shift_closures = $rows;
                }

                if (empty($shift_closures)) {
                    // try to find by project reference via finished_report
                    $projId = null;
                    if (isset($plan_id) && !empty($plan_id)) {
                        $projRow = $this->db->where('id_plan', $plan_id)->get('planning')->row();
                        if ($projRow && isset($projRow->id_project)) $projId = $projRow->id_project;
                    }
                    if ($projId) {
                        $rows = $this->db->query('SELECT sc.* FROM shift_closures sc LEFT JOIN finished_report fr ON sc.warehouse_request_id = fr.id_finished WHERE fr.id_project = ?', [$projId])->result();
                        if (!empty($rows)) $shift_closures = $rows;
                    }
                }
            }

            // attach production shift details to closures when available
            if (!empty($shift_closures) && !empty($production_shifts_map)) {
                foreach ($shift_closures as &$sc) {
                    $sc->production_shift = $production_shifts_map[$sc->shift_id] ?? null;
                }
                unset($sc);
            }

            $data = [
                'title' => 'Chốt ca: ' . $shift->shift_name,
                'shift' => $closure_data['shift'],
                'machines' => $closure_data['machines'],
                'totals' => $closure_data['totals'],
                'warnings' => $closure_data['warnings'],
                'has_warnings' => $closure_data['has_warnings'],
                'thresholds' => $closure_data['thresholds'],
                'defect_reasons' => $defect_reasons,
                'shift_closures' => $shift_closures,
                'production_shifts_map' => $production_shifts_map,
                'content' => 'leader/shift/closure_form',
                'navlink' => 'shift'
            ];

            $this->load->view('leader/vbackend', $data);
            
        } catch (Exception $e) {
            log_message('error', 'Leader::end_shift() - Error: ' . $e->getMessage());
            $this->session->set_flashdata('error', 'Lỗi hệ thống: ' . $e->getMessage());
            redirect('leader/shift/detail/' . $shift_id);
        }
    }
    
    /**
     * Lưu phiếu chốt ca (AJAX)
     */
    public function save_closure()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
            return;
        }
        
        try {
            $this->load->model('leader/ShiftClosureModel', 'shiftClosure');
            
            $shift_id = $this->input->post('shift_id');
            $notes = $this->input->post('notes');
            $confirmed_data = $this->input->post('confirmed_data');
            $user_id = $this->session->userdata('user_id');
            
            // Decode confirmed data from JSON
            if (is_string($confirmed_data)) {
                $confirmed_data = json_decode($confirmed_data, true);
            }

            // Basic validation: ensure shift exists and is not already closed
            $shift = $this->db->where('shift_id', $shift_id)->get('production_shifts')->row();
            if (empty($shift)) {
                $this->output->set_content_type('application/json')->set_status_header(400)->set_output(json_encode(['success' => false, 'message' => 'Shift not found']));
                return;
            }
            if (isset($shift->is_closed) && $shift->is_closed == 1) {
                $this->output->set_content_type('application/json')->set_status_header(400)->set_output(json_encode(['success' => false, 'message' => 'Shift already closed']));
                return;
            }
            
            // Create closure via model
            $result = $this->shiftClosure->createClosure($shift_id, $user_id, $confirmed_data, $notes);

            // Normalize response to include success boolean and message
            if (is_array($result) || is_object($result)) {
                $out = (array)$result;
            } else {
                $out = ['success' => (bool)$result, 'message' => $result ? 'OK' : 'Failed'];
            }

            $this->output->set_content_type('application/json')->set_output(json_encode($out));
                
        } catch (Exception $e) {
            log_message('error', 'Leader::save_closure() - Error: ' . $e->getMessage());
            
            $this->output
                ->set_content_type('application/json')
                ->set_status_header(500)
                ->set_output(json_encode([
                    'success' => false,
                    'message' => 'Lỗi hệ thống: ' . $e->getMessage()
                ]));
        }
    }
    
    /**
     * Xem chi tiết phiếu chốt ca
     */
    public function closure_detail($closure_id)
    {
        try {
            $this->load->model('leader/ShiftClosureModel', 'shiftClosure');
            
            $closure = $this->shiftClosure->getClosureDetails($closure_id);
            
            if (!$closure) {
                $this->session->set_flashdata('error', 'Không tìm thấy phiếu chốt ca');
                redirect('leader/');
                return;
            }
            // Attach production shift info to closure when possible (UC8 style)
            $prod_shift = null;
            if (isset($closure->shift_id) && $this->db->table_exists('production_shifts')) {
                $ps = $this->db->where('shift_id', $closure->shift_id)->get('production_shifts')->row();
                if ($ps) $prod_shift = $ps;
            }
            // fallback: if closure references a warehouse_request -> finished_report -> project, try to load production_shifts by project
            if (!$prod_shift && isset($closure->warehouse_request_id) && $this->db->table_exists('finished_report')) {
                $fr = $this->db->where('id_finished', $closure->warehouse_request_id)->get('finished_report')->row();
                if ($fr && isset($fr->id_project) && $this->db->table_exists('production_shifts')) {
                    $rows = $this->db->get_where('production_shifts', ['id_project' => $fr->id_project])->result();
                    if (!empty($rows)) $prod_shift = $rows[0];
                }
            }
            if ($prod_shift) $closure->production_shift = $prod_shift;

            $data = [
                'title' => 'Chi tiết phiếu chốt ca: ' . ($closure->closure_code ?? ''),
                'closure' => $closure,
                'content' => 'leader/shift/closure_detail',
                'navlink' => 'shift'
            ];
            
            $this->load->view('leader/vbackend', $data);
            
        } catch (Exception $e) {
            log_message('error', 'Leader::closure_detail() - Error: ' . $e->getMessage());
            $this->session->set_flashdata('error', 'Lỗi hệ thống: ' . $e->getMessage());
            redirect('leader/');
        }
    }
}
