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
        // NOTE: adding 'technical' / 'technical_staff' allows technical users to access leader pages.
        // If you want to keep leader pages strictly for leadership, remove these.
        $allowed_roles = ['leader', 'line_manager', 'admin', 'bod', 'system_admin', 'technical', 'technical_staff'];
        
        if (!in_array($role, $allowed_roles, true)) {
            log_message('error', 'Leader access denied for user ' . $this->session->userdata('username') . ' with role=' . $role);
            show_error('Access Denied - Insufficient Permissions. Your role: ' . var_export($role, true), 403, 'Forbidden');
        }
    }

    public function index()
    {

        $data = [
            'finished' => $this->db->query('SELECT * FROM finished_report JOIN project JOIN customer
            WHERE finished_report.id_project=project.id_project AND project.id_cust=customer.id_cust')->result(),
            
            'sorting' => $this->db->query('SELECT * FROM sorting_report JOIN plan_shift JOIN planning JOIN staff
            WHERE sorting_report.id_planshift=plan_shift.id_planshift AND plan_shift.id_staff=staff.id_staff AND plan_shift.id_plan=planning.id_plan')->result(),

            'project' => $this->crudModel->getData('project')->num_rows(),
            'planning' => $this->crudModel->getData('planning')->num_rows(),
            'plan_shift' => $this->crudModel->getData('plan_shift')->num_rows(),
            'finished_report' => $this->crudModel->getData('finished_report')->num_rows(),

            'content' => 'leader/beranda',
            'navlink' => 'beranda',
        ];

        $this->load->view('leader/vbackend', $data);
    }

    public function planning()
    {
    
        $data = [
            'planning' => $this->db->query('SELECT * FROM planning JOIN project WHERE planning.id_project=project.id_project')->result(),
            'project' => $this->db->query('SELECT * FROM project')->result(),
            'content' => 'leader/planning/planning',
            'navlink' => 'planning',
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

    public function machine()
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
        if ($this->uri->segment(3) === 'addmaterial') {
        
            $data = [
                'planshift' => $this->db->query('SELECT * FROM plan_shift JOIN staff WHERE plan_shift.id_staff=staff.id_staff AND ps_status = 1 AND staff.st_status=2')->result(),
                'material' => $this->db->query('SELECT * FROM material')->result(),

                'content' => 'leader/material/addmaterial',
                'navlink' => 'material',
            ];
        } elseif ($this->uri->segment(3) === 'addnewmaterial') {
        
            $data = [
                'content' => 'leader/material/addnewmaterial',
                'navlink' => 'material',
            ];

        } else {
            $data = [
                'material' => $this->db->query('SELECT * FROM p_material JOIN plan_shift JOIN material JOIN staff WHERE p_material.id_material = material.id_material AND p_material.id_planshift = plan_shift.id_planshift AND plan_shift.id_staff = staff.id_staff')->result(),
                'materials' => $this->db->query('SELECT * FROM material')->result(),
                'content' => 'leader/material/material',
                'navlink' => 'material',
            ];
        }

        $this->load->view('leader/vbackend', $data);
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

            $data = [
                'sorting' => $this->crudModel->getDataJoin($table, $onjoin),
                'content' => 'leader/sorting/sorting',
                'navlink' => 'sorting',
                ];
        }

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
            
                $data = [
                'detail' => [
                    'plan_name' => $tampil->plan_name,
                    'staff_name' => $tampil->staff_name,
                    'shift_name' => $tampil->shift_name,
                    'qty_target' => $tampil->qty_target,
                    'start_date' => $tampil->start_date,
                    'product_name' => $tampil->product_name,
                    'diameter' => $tampil->diameter,
                    'waste' => $tampil2->waste,
                    'finished' => $tampil2->finished,

                ],

                'p_machine' => $this->db->query('SELECT * FROM p_machine JOIN machine WHERE id_planshift = ' . $id . ' AND p_machine.id_machine = machine.id_machine')->result(),
                'p_material' => $this->db->query('SELECT * FROM p_material JOIN material WHERE id_planshift = ' . $id. ' AND p_material.id_material = material.id_material')->result(),
                'content' => 'leader/sorting/DetailSorting',
                'navlink' => 'sorting',
            ];

            $this->load->view('leader/vbackend', $data);

        } else {

            $id = $this->uri->segment(3);

            $tampil = $this->db->query(
                'SELECT * FROM plan_shift JOIN planning JOIN shiftment JOIN staff JOIN project JOIN product
                WHERE id_planshift = ' . $id. ' AND plan_shift.id_plan = planning.id_plan AND plan_shift.id_shift = shiftment.id_shift
                AND plan_shift.id_staff = staff.id_staff AND planning.id_project = project.id_project
                AND project.id_product = product.id_product')->row();
            
            $tampil2 = $this->db->query('SELECT * FROM sorting_report WHERE id_planshift = ' . $id. '')->row();
            
                $data = [
                'detail' => [
                    'plan_name' => $tampil->plan_name,
                    'staff_name' => $tampil->staff_name,
                    'shift_name' => $tampil->shift_name,
                    'qty_target' => $tampil->qty_target,
                    'start_date' => $tampil->start_date,
                    'product_name' => $tampil->product_name,
                    'diameter' => $tampil->diameter,
                    'waste' => $tampil2->waste,
                    'finished' => $tampil2->finished,

                ],

                'p_machine' => $this->db->query('SELECT * FROM p_machine JOIN machine WHERE id_planshift = ' . $id . ' AND p_machine.id_machine = machine.id_machine')->result(),
                'p_material' => $this->db->query('SELECT * FROM p_material JOIN material WHERE id_planshift = ' . $id. ' AND p_material.id_material = material.id_material')->result(),
                'content' => 'leader/sorting/sortingprint',
            ];

            $this->load->view('leader/vprint', $data);

        }

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
        if ($this->uri->segment(3) === 'addstaff') {

            $data = [
                'staff' => $this->db->query('SELECT * FROM staff')->result(),
                'content' => 'leader/staff/addstaff',
                'navlink' => 'staff',
                ];

        } elseif ($this->uri->segment(4) === 'update') {

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
            // Support search by staff code or skill
            $search_code = $this->input->post('search_code');
            $search_skill = $this->input->post('search_skill');

            if ($search_code || $search_skill) {
                $this->db->from('staff');
                if ($search_code) {
                    $this->db->where('id_staff', $search_code);
                }
                if ($search_skill) {
                    // some projects store skill column; use LIKE for partial match
                    $this->db->like('skill', $search_skill);
                }
                $results = $this->db->get()->result();

                    if (empty($results)) {
                        // No results - show error and options to retry or cancel
                        $this->session->set_flashdata('error', 'Không tìm thấy nhân viên với tiêu chí tìm kiếm. Vui lòng nhập lại hoặc hủy.');
                        $this->session->set_flashdata('search_not_found', true);
                        $data = [
                            'staff' => [],
                            'content' => 'leader/staff/staff',
                            'navlink' => 'staff',
                        ];
                } else {
                    $data = [
                        'staff' => $results,
                        'content' => 'leader/staff/staff',
                        'navlink' => 'staff',
                    ];
                }
            } else {
                $data = [
                    'staff' => $this->db->query('SELECT * FROM staff')->result(),
                    'content' => 'leader/staff/staff',
                    'navlink' => 'staff',
                ];
            }
        }

        $this->load->view('leader/vbackend', $data);
    }

    public function addStaff()
    {
        $staff_code = trim($this->input->post('id_staff_custom'));
        $staff_name = trim($this->input->post('staff_name'));
        $phone = trim($this->input->post('phone'));
        $email = trim($this->input->post('email'));
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
            'st_status' => $st_status,
        ];

        $this->crudModel->addData('staff', $add);

        $this->session->set_flashdata('flash', 'ditambah');

        redirect(site_url('leader/staff'));
    }

    public function updateStaff()
    {
        $id_staff = $this->input->post('id_staff');
        $staff_name = trim($this->input->post('staff_name'));
        $phone = trim($this->input->post('phone'));
        $email = trim($this->input->post('email'));
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
            'st_status' => $st_status,
        ];

        $this->crudModel->updateData('staff', 'id_staff', $id_staff, $update);

        $this->session->set_flashdata('flash', 'diubah');

        redirect(site_url('leader/staff'));
    }

    public function deleteStaff()
    {
        $id_staff = $this->uri->segment(3);

        $this->crudModel->deleteData('staff', 'id_staff', $id_staff);

        $this->session->set_flashdata('flash', 'dihapus');

        redirect(site_url('leader/staff'));
    }

    public function toggleStaffStatus()
    {
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
}
