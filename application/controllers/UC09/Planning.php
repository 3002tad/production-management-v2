<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Planning extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('CrudModel', 'crudModel');
        $this->load->library('session');
        $this->load->helper('form');
        // Allow loading StaffModel and module views from application/modules/staff
        $this->load->add_package_path(APPPATH . 'modules/staff/');
        
        // Check if user is logged in
        if (!$this->session->userdata('user_id')) {
            redirect('login/');
            return;
        }
        
        // RBAC: Check if user has leader/line manager access
        // Allow: BOD, System Admin, Line Manager, and temporarily other roles
        $role_name = $this->session->userdata('role_name');
        $level = $this->session->userdata('level');
        $old_role = $this->session->userdata('role'); // Backward compatibility
        
        $has_access = false;
        
        // New RBAC system
        if ($role_name) {
            $allowed_roles = ['bod', 'system_admin', 'line_manager', 'warehouse_staff', 'qc_staff', 'technical_staff'];
            $has_access = in_array($role_name, $allowed_roles) || ($level >= 50);
        }
        // Old system fallback
        elseif ($old_role === 'leader' || $old_role === 'admin') {
            $has_access = true;
        }
        
        if (!$has_access) {
            show_error('Access Denied - Insufficient Permissions', 403, 'Forbidden');
        }
    }

    /**
     * Compute next versioned plan name.
     * - If submitted base equals existing base (ignoring trailing (vN)), increment N.
     * - If different base, start at v1.
     * @param string|null $existing_name
     * @param string|null $submitted_name
     * @return string|null
     */
    private function nextVersionedName($existing_name, $submitted_name)
    {
        $ex = trim((string)$existing_name);
        $sub = trim((string)$submitted_name);

        // strip trailing (vN) from names
        $ex_base = preg_replace('/\s*\(v\d+\)\s*$/i', '', $ex);
        $sub_base = preg_replace('/\s*\(v\d+\)\s*$/i', '', $sub);

        // if submitted is empty, fall back to existing base
        if ($sub_base === '') {
            $sub_base = $ex_base;
        }

        // determine existing version number (if any)
        $existing_version = 0;
        if (preg_match('/\(v(\d+)\)\s*$/i', $ex, $m)) {
            $existing_version = intval($m[1]);
        }

        // if bases equal (case-insensitive), increment; otherwise start at 1
        if (mb_strtolower(trim($ex_base)) === mb_strtolower(trim($sub_base))) {
            $next = $existing_version + 1;
        } else {
            $next = 1;
        }

        if ($sub_base === '') return null;
        return $sub_base . '(v' . $next . ')';
    }
    


    /**
     * Danh sách Kế hoạch cho vai trò Leader
     * Tải các bản ghi từ bảng `planning` (kèm project) và render view leader/planning/Planning
     */
    public function planning()
    {
        // Load list of plans with related project information using Active Record
        try {
            $planning = $this->db
                ->select('planning.*, project.project_name')
                ->from('planning')
                ->join('project', 'planning.id_project = project.id_project', 'left')
                ->order_by('planning.id_plan', 'DESC')
                ->get()
                ->result();

            $projects = $this->db->order_by('id_project', 'ASC')->get('project')->result();

            $data = [
                'planning' => $planning,
                'project' => $projects,
                'content' => 'leader/planning/Planning',
                'navlink' => 'planning',
            ];

            $this->load->view('leader/vbackend', $data);
        } catch (Exception $e) {
            log_message('error', 'Planning::planning error: ' . $e->getMessage());
            show_error('Không thể tải danh sách kế hoạch', 500, 'Lỗi nội bộ');
        }
    }

    /**
     * Hiển thị form sửa và xử lý cập nhật kế hoạch (leader)
     */
    public function ChangePlanning($id_plan = null)
    {
        // normalize id from URI if not provided
        $id_plan = $id_plan ?: $this->uri->segment(3);

        // If form submitted -> update (server-side validation included)
        if ($this->input->server('REQUEST_METHOD') === 'POST') {
            $id_plan_post = $this->input->post('id_plan') ?: $id_plan;

            // load existing plan to validate changes
            $existing = null;
            if (!empty($id_plan_post)) {
                $existing = $this->crudModel->getDataWhere('planning', 'id_plan', $id_plan_post)->row();
            }

            $newQty = $this->input->post('qty_target') !== null ? (int)$this->input->post('qty_target') : null;

            // Server-side enforcement: require qty_target to be different when editing an existing plan
            if ($existing && $newQty !== null && isset($existing->qty_target) && ((int)$existing->qty_target) === $newQty) {
                // keep an error message and redirect back to edit form
                $this->session->set_flashdata('error', 'Số lượng mục tiêu không thay đổi. Vui lòng chỉnh số lượng trước khi lưu.');
                redirect(site_url('leader/ChangePlanning/' . $id_plan_post));
                return;
            }

            // compute versioned plan name (increment (vN) on each leader edit)
            $submitted_name = trim($this->input->post('plan_name')) ?: null;
            $versioned_name = $this->nextVersionedName($existing->plan_name ?? '', $submitted_name);

            $update = [
                // only set plan_name when we could determine a base name
                'plan_name' => $versioned_name !== null ? $versioned_name : null,
                'id_project' => trim($this->input->post('id_project')) ?: null,
                'qty_target' => $newQty,
                'start_date' => trim($this->input->post('start_date')) ?: null,
                'end_date' => trim($this->input->post('end_date')) ?: null,
                'machine_id' => trim($this->input->post('machine_id')) ?: null,
                'note' => trim($this->input->post('note')) ?: null,
                'pl_status' => $this->input->post('pl_status') ? 1 : 0,
            ];

            // Remove null keys so updateData doesn't overwrite with empty strings unintentionally
            $update = array_filter($update, function ($v) { return $v !== null; });

            if (!empty($id_plan_post)) {
                $this->crudModel->updateData('planning', 'id_plan', $id_plan_post, $update);
                $this->session->set_flashdata('flash', 'diubah');
            }
            redirect(site_url('leader/planning'));
            return;
        }

        // Show edit form
        $plan = $this->crudModel->getDataWhere('planning', 'id_plan', $id_plan)->row();
        // Prepare project options for select
        $projects = $this->db->query('SELECT * FROM project')->result();
        $order_options_html = '';
        if (!empty($projects)) {
            foreach ($projects as $p) {
                $val = htmlspecialchars($p->id_project ?? '', ENT_QUOTES);
                $qty = htmlspecialchars($p->qty_request ?? $p->suggested_target ?? 0, ENT_QUOTES);
                $delivery = htmlspecialchars($p->delivery_date ?? $p->end_date ?? '', ENT_QUOTES);
                $product_id = htmlspecialchars($p->id_product ?? '', ENT_QUOTES);
                $created = isset($p->created_at) ? htmlspecialchars(substr($p->created_at,0,10), ENT_QUOTES) : '';
                $label = htmlspecialchars($p->project_name ?? $p->id_project ?? '', ENT_QUOTES);
                $sel = (isset($plan->id_project) && $plan->id_project == ($p->id_project ?? '')) ? ' selected' : '';
                $order_options_html .= "<option value=\"{$val}\" data-qty=\"{$qty}\" data-delivery=\"{$delivery}\" data-product-id=\"{$product_id}\"" . (!empty($created) ? " data-created=\"{$created}\"" : "") . "{$sel}>{$label}</option>\n";
            }
        }

        // Prepare machine options and materials for the ChangePlanning view
        // Replicate UC08 logic: read from `machines` (or fallback) and apply QC/status filters,
        // map flexible column names and produce friendly labels like 'Dây chuyền N'.
        $machine_options_html = '';
        $machines_raw = [];
        // prefer table 'machines' used by UC08, fallback to 'machine'
        if ($this->db->table_exists('machines')) {
            // choose safe order column if available
            $orderColM = null;
            $mcandidates = ['id_machine','id','machine_id','id_machines'];
            foreach ($mcandidates as $cc) { if ($this->db->field_exists($cc, 'machines')) { $orderColM = $cc; break; } }
            if ($orderColM) {
                $machines_raw = $this->db->order_by($orderColM,'ASC')->get('machines')->result();
            } else {
                $machines_raw = $this->db->get('machines')->result();
            }
        } elseif ($this->db->table_exists('machine')) {
            $orderColM = null;
            $mcandidates = ['id_machine','id','machine_id','id_machines'];
            foreach ($mcandidates as $cc) { if ($this->db->field_exists($cc, 'machine')) { $orderColM = $cc; break; } }
            if ($orderColM) {
                $machines_raw = $this->db->order_by($orderColM,'ASC')->get('machine')->result();
            } else {
                $machines_raw = $this->db->get('machine')->result();
            }
        }

        $machines = [];
        if (!empty($machines_raw)) {
            foreach ($machines_raw as $m) {
                // only include machines with code starting with 'QC'
                $mcode = $m->code ?? ($m->mc_code ?? null);
                if (!isset($mcode) || !preg_match('/^QC/i', $mcode)) {
                    continue;
                }

                // determine status field
                $status = null;
                if (isset($m->mc_status)) {
                    $status = $m->mc_status;
                } elseif (isset($m->status)) {
                    $status = $m->status;
                } elseif (isset($m->mc_stats)) {
                    $status = $m->mc_stats;
                } elseif (isset($m->machine_status)) {
                    $status = $m->machine_status;
                }

                if ($status !== null) {
                    if (is_numeric($status)) {
                        if (intval($status) !== 1) { continue; }
                    } else {
                        $s = strtolower(trim($status));
                        $excluded = ['broken', 'maintenance', 'down', 'unavailable', 'repair'];
                        if (in_array($s, $excluded, true)) { continue; }
                    }
                }

                // map id/name/capacity with flexible column names
                $mid = isset($m->id_machine) ? $m->id_machine : (isset($m->id) ? $m->id : (isset($m->machine_id) ? $m->machine_id : (isset($m->id_machines) ? $m->id_machines : null)));
                $mname = isset($m->machine_name) ? $m->machine_name : (isset($m->name) ? $m->name : null);
                $cap = isset($m->capacity) ? $m->capacity : (isset($m->cap) ? $m->cap : 0);

                // friendly label
                $label = $mname ?? ('Máy ' . ($mid ?? ''));
                if (!empty($mcode) && preg_match('/^QC[^0-9]*(\d+)/i', $mcode, $matches)) {
                    $num = intval($matches[1]);
                    $label = 'Dây chuyền ' . ($num > 0 ? $num : $matches[1]);
                }

                $machines[] = (object)[
                    'id_machine' => $mid,
                    'machine_name' => $mname,
                    'code' => $mcode,
                    'capacity' => $cap,
                    'label' => $label
                ];
            }
        }

        // If filtering removed everything but raw rows existed, expose raw rows as fallback
        if (empty($machines) && !empty($machines_raw)) {
            foreach ($machines_raw as $m) {
                $mid = $m->id_machine ?? ($m->id ?? ($m->machine_id ?? null));
                $mname = $m->machine_name ?? ($m->name ?? null);
                $mcode = $m->code ?? ($m->mc_code ?? null);
                $cap = isset($m->capacity) ? $m->capacity : (isset($m->cap) ? $m->cap : 0);
                $label = $mname ?? ('Máy ' . ($mid ?? ''));
                $machines[] = (object)[
                    'id_machine' => $mid,
                    'machine_name' => $mname,
                    'code' => $mcode,
                    'capacity' => $cap,
                    'label' => $label
                ];
            }
        }

        // build options
        foreach ($machines as $m) {
            $mid = htmlspecialchars($m->id_machine ?? ($m->id ?? ''), ENT_QUOTES);
            $cap = htmlspecialchars($m->capacity ?? 0, ENT_QUOTES);
            $label = htmlspecialchars(($m->label ?? ($m->machine_name ?? ('Máy ' . ($m->id_machine ?? $m->id ?? '')))) . ' (công suất: ' . ($m->capacity ?? 0) . ')', ENT_QUOTES);
            $selm = (isset($plan->machine_id) && (string)$plan->machine_id === (string)($m->id_machine ?? $m->code ?? '')) ? ' selected' : '';
            $machine_options_html .= "<option value=\"{$mid}\" data-capacity=\"{$cap}\"{$selm}>{$label}</option>\n";
        }

        // Try to load incident descriptions related to this plan (several possible schema layouts)
        $incident_description = '';
        try {
            if ($this->db->table_exists('incident_reports')) {
                // We will always try to get id_machine directly from incident_reports if present.
                // Join to plan-shift tables to limit incidents to this plan when possible.
                if ($this->db->table_exists('production_shifts')) {
                    $q = $this->db->select('ir.incident_description, ir.id_machine, ir.line_id')
                        ->from('incident_reports ir')
                        ->join('production_shifts ps', 'ir.shift_id = ps.shift_id', 'inner')
                        ->where('ps.id_plan', $id_plan)
                        ->get();
                } elseif ($this->db->table_exists('plan_shift')) {
                    $q = $this->db->select('ir.incident_description, ir.id_machine, ir.line_id')
                        ->from('incident_reports ir')
                        ->join('plan_shift ps', 'ir.shift_id = ps.id_shift', 'inner')
                        ->where('ps.id_plan', $id_plan)
                        ->get();
                } else {
                    // If neither shift table exists, attempt a direct filter by id_plan column on incident_reports if present
                    if ($this->db->field_exists('id_plan', 'incident_reports')) {
                        $q = $this->db->select('incident_description, id_machine, line_id')
                            ->from('incident_reports')
                            ->where('id_plan', $id_plan)
                            ->get();
                    } else {
                        $q = null;
                    }
                }

                if (!empty($q) && $q->num_rows() > 0) {
                    $rows = $q->result();
                    $items = [];
                    foreach ($rows as $r) {
                        $desc = trim($r->incident_description ?? '');
                        $mid = isset($r->id_machine) && $r->id_machine !== null && $r->id_machine !== '' ? $r->id_machine : null;
                        $lid = isset($r->line_id) && $r->line_id !== null && $r->line_id !== '' ? $r->line_id : null;
                        if ($mid && $lid) {
                            $items[] = 'Máy ' . $mid . ' (Line ' . $lid . '): ' . $desc;
                        } elseif ($mid) {
                            $items[] = 'Máy ' . $mid . ': ' . $desc;
                        } elseif ($lid) {
                            $items[] = 'Line ' . $lid . ': ' . $desc;
                        } else {
                            $items[] = $desc;
                        }
                    }
                    $incident_description = implode("\n\n---\n\n", array_filter($items, function ($v) { return $v !== ''; }));
                }
            }
        } catch (Exception $e) {
            log_message('error', 'Planning::ChangePlanning incident fetch error: ' . $e->getMessage());
            $incident_description = '';
        }

        $data = [
            'plan' => $plan,
            'order_options_html' => $order_options_html,
            'machine_options_html' => $machine_options_html,
            'selected_project_id' => isset($plan->id_project) ? $plan->id_project : null,
            'incident_description' => $incident_description,
            'content' => 'leader/planning/ChangePlanning',
            'navlink' => 'planning',
        ];

        $this->load->view('leader/vbackend', $data);
    }

    /**
     * POST handler: cập nhật Kế hoạch hiện có (Leader - UC09)
     */
    public function updatePlan()
    {
        try {
            $this->load->model('PlanModel');

            $id_plan = $this->input->post('id_plan');
            if (empty($id_plan)) {
                throw new Exception('Thiếu id_plan');
            }

            $id_project = $this->input->post('id_project');
            $qty_target = $this->input->post('qty_target');
            $end_date_post = $this->input->post('end_date') ?: null;
            $start_date = $this->input->post('start_date') ?: null;
            $finish_date = $this->input->post('finish_date') ?: null;
            // Do not accept or process materials from leader edit form; materials are managed elsewhere
            $note = trim($this->input->post('note')) ?: null;
            $machine_id = $this->input->post('machine_id') ?: null;
            $suggested_shifts = $this->input->post('suggested_shifts') !== null && $this->input->post('suggested_shifts') !== '' ? intval($this->input->post('suggested_shifts')) : null;
            $lines_json = $this->input->post('lines');
            $lines = [];
            if (!empty($lines_json)) {
                $decoded = json_decode($lines_json, true);
                if (is_array($decoded)) $lines = $decoded; else $lines = $lines_json;
            }

            if (empty($id_project) || empty($qty_target)) {
                $this->session->set_flashdata('error', 'Thiếu thông tin bắt buộc: Đơn hàng hoặc Số lượng');
                redirect(site_url('leader/ChangePlanning/' . $id_plan));
                return;
            }

            // Validate dates vs project's delivery date when available.
            $delivery_date = null;
            try {
                $order = $this->PlanModel->getOrderDetails($id_project);
                if ($order) {
                    $delivery_date = $order->entry_date ?? ($order->delivery_date ?? ($order->due_date ?? null));
                }
            } catch (Exception $e) { $delivery_date = null; }

            if (!empty($delivery_date)) {
                if (!empty($start_date) && strtotime($start_date) >= strtotime($delivery_date)) {
                    $this->session->set_flashdata('error', 'Ngày bắt đầu không được trùng hoặc trễ hơn hạn giao của đơn hàng');
                    redirect(site_url('leader/ChangePlanning/' . $id_plan));
                    return;
                }
                $created_date = $order->created_at ?? null;
                if (!empty($start_date) && !empty($created_date) && strtotime($start_date) < strtotime(substr($created_date,0,10))) {
                    $this->session->set_flashdata('error', 'Ngày bắt đầu không được trước ngày tạo đơn hàng');
                    redirect(site_url('leader/ChangePlanning/' . $id_plan));
                    return;
                }

                if (empty($finish_date)) {
                    $finish_date = date('Y-m-d', strtotime($delivery_date . ' -1 day'));
                }
                if (!empty($finish_date) && strtotime($finish_date) >= strtotime($delivery_date)) {
                    $this->session->set_flashdata('error', 'Ngày kết thúc phải trước hạn giao của đơn hàng');
                    redirect(site_url('leader/ChangePlanning/' . $id_plan));
                    return;
                }
            }

            if (!empty($start_date) && !empty($finish_date) && strtotime($start_date) > strtotime($finish_date)) {
                $this->session->set_flashdata('error', 'Ngày bắt đầu không được sau ngày kết thúc');
                redirect(site_url('leader/ChangePlanning/' . $id_plan));
                return;
            }

            // compute versioned plan name (increment (vN) on each leader edit)
            $existing_plan = $this->PlanModel->getPlanById($id_plan);
            $submitted = trim($this->input->post('plan_name')) ?: null;
            $versioned = $this->nextVersionedName($existing_plan->plan_name ?? '', $submitted);

            $plan_data = [
                'plan_name' => $versioned !== null ? $versioned : null,
                'id_project' => $id_project,
                'qty_target' => intval($qty_target),
                'end_date' => $finish_date ?: ($end_date_post ?: null),
                'start_date' => $start_date ?: null,
                'finish_date' => $finish_date ?: null,
                'lines' => $lines,
                'note' => $note,
                'machine_id' => $machine_id,
                'suggested_shifts' => $suggested_shifts,
                // When a leader edits and saves a plan, set it back to 'not approved' (draft)
                'pl_status' => 0,
            ];

            $result = $this->PlanModel->updatePlan($id_plan, $plan_data);
            if ($result['success']) {
                $this->session->set_flashdata('flash', 'diubah');
                redirect(site_url('leader/planning'));
                return;
            } else {
                throw new Exception($result['message'] ?? 'Lỗi khi cập nhật kế hoạch');
            }

        } catch (Exception $e) {
            $this->session->set_flashdata('error', 'Lỗi khi cập nhật kế hoạch: ' . $e->getMessage());
            redirect(site_url('leader/ChangePlanning/' . ($this->input->post('id_plan') ?: '')));
        }
    }


}
