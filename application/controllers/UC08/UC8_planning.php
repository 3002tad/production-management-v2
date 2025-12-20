<?php

defined('BASEPATH') or exit('No direct script access allowed');



class UC8_planning extends CI_Controller
{
    /**
     * Constructor - Kiểm tra phân quyền Ban Giám Đốc
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('CrudModel', 'crudModel');
        $this->load->model('OrderModel');
        $this->load->model('PlanModel');
        $this->load->library('session');
        
        // Check if user is logged in
        if (!$this->session->userdata('user_id')) {
            redirect('login/');
            return;
        }
        
        // RBAC: Check if user has BOD access
        // Allow: BOD (role_name = 'bod', level 100) and System Admin (level >= 90)
        $role_name = $this->session->userdata('role_name');
        $level = $this->session->userdata('level');
        $old_role = $this->session->userdata('role'); // Backward compatibility
        
        $has_access = false;
        
        // New RBAC system
        if ($role_name) {
            $allowed_roles = ['bod', 'system_admin'];
            $has_access = in_array($role_name, $allowed_roles) || ($level >= 90);
        }
        // Old system fallback
        elseif ($old_role === 'admin' || $old_role === 'bod') {
            $has_access = true;
        }
        
        if (!$has_access) {
            show_error('Access Denied - BOD Only', 403, 'Forbidden');
        }
    }

    /**
     * Dashboard - Trang chủ Ban Giám Đốc
     */
    public function index()
    {
        $data = [
            // Đơn hàng hoàn thành - JOIN đúng với cột total_finished
            'finished' => $this->db->query('
                SELECT fr.id_finished, fr.total_finished, fr.fdate,
                       p.project_name, p.qty_request,
                       c.cust_name 
                FROM finished_report fr
                JOIN project p ON fr.id_project = p.id_project
                JOIN customer c ON p.id_cust = c.id_cust
                ORDER BY fr.id_finished DESC
                LIMIT 10
            ')->result(),
            
            // Báo cáo sản xuất - Sử dụng cột đúng: finished + waste
            'sorting' => $this->db->query('
                SELECT sr.id_sorting, sr.finished, sr.waste,
                       (sr.finished + sr.waste) as qty_output,
                       ps.id_plan,
                       s.staff_name
                FROM sorting_report sr
                JOIN plan_shift ps ON sr.id_planshift = ps.id_planshift
                JOIN staff s ON ps.id_staff = s.id_staff
                JOIN planning pl ON ps.id_plan = pl.id_plan
                ORDER BY sr.id_sorting DESC
                LIMIT 10
            ')->result(),

            // Số liệu thống kê
            'project' => $this->crudModel->getData('project')->num_rows(),
            'planning' => $this->crudModel->getData('planning')->num_rows(),
            'plan_shift' => $this->crudModel->getData('plan_shift')->num_rows(),
            'finished_report' => $this->crudModel->getData('finished_report')->num_rows(),

            'content' => 'bod/beranda',
            'navlink' => 'beranda',
        ];

        $this->load->view('bod/vbackend', $data);
    }

    /**
     * AJAX endpoint: trả về BOM (materials array) cho một product id
     * GET param: id (id_product) hoặc URI segment 3
     * Response: JSON { success: bool, materials: [...] }
     */
    public function getProductBom()
    {
        $this->load->model('ProductModel');
        $id = $this->input->get('id') ?: $this->uri->segment(3);
        if (empty($id)) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Missing product id']);
            return;
        }

        // Use the method that returns BOM items decoded and enriched with stock
        $product = $this->ProductModel->getProductByIdWithBom($id);
        $materials = [];
        if ($product && isset($product->bom_data) && isset($product->bom_data['materials'])) {
            $materials = $product->bom_data['materials'];
        }

        header('Content-Type: application/json');
        if (empty($materials)) {
            echo json_encode(['success' => false, 'message' => 'No BOM found for product ' . $id, 'materials' => []], JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode(['success' => true, 'materials' => $materials], JSON_UNESCAPED_UNICODE);
        }
    }



      
    /**
     * Xóa Kế hoạch (POST)
     * - POST param: id_plan
     * - Kiểm tra: không xóa khi đã phê duyệt (PlanModel sẽ kiểm tra)
     */
    public function deletePlan()
    {
        $id_plan = $this->input->post('id_plan') ?: $this->uri->segment(3);
        $hasError = false;

        try {
            if (empty($id_plan)) {
                throw new Exception('Thiếu id_plan');
            }

            // ensure PlanModel available
            $this->load->model('PlanModel');

            $res = $this->PlanModel->deletePlan(intval($id_plan));
            if (empty($res) || empty($res['success'])) {
                throw new Exception($res['message'] ?? 'Không thể xóa kế hoạch');
            }

            $this->session->set_flashdata('success_js', json_encode([
                'title' => 'Xóa kế hoạch',
                'message' => 'Kế hoạch đã được xóa (ID: ' . $id_plan . ')'
            ]));
        } catch (Exception $e) {
            $hasError = true;
            $this->session->set_flashdata('error_js', json_encode([
                'message' => 'Không thể xóa kế hoạch',
                'details' => [$e->getMessage()]
            ]));
        }

        redirect(site_url('BOD/plans?msg=' . ($hasError ? 'error' : 'success')));
    }







    
    /**
     * Form tạo Kế hoạch sản xuất (BGĐ)
     * Hiển thị form để BGĐ chọn đơn hàng (đã duyệt), nhập target và phân rã line/ca.
     */
    public function createPlan()
    {
        // respect optional GET param `project_id` but normalize via CI input (used for selected flag)
        $selected_project_id = $this->input->get('project_id') ?: null;
        // If editing an existing plan, load it
        $plan = null;
        $plan_id = $this->input->get('plan_id') ?: null;
        if (!empty($plan_id)) {
            $this->load->model('PlanModel');
            $plan = $this->PlanModel->getPlanById($plan_id);
            if ($plan && !empty($plan->id_project)) {
                // prefer plan's project as selected
                $selected_project_id = $plan->id_project;
            }
        }

        // Lấy danh sách các đơn hàng đã duyệt để lựa chọn
        $this->load->model('PlanModel');
        $orders = $this->PlanModel->getApprovedOrdersForPlanning();

        // If editing and the plan's project isn't in the $orders list (because
        // getApprovedOrdersForPlanning() excludes projects with an existing
        // planning), fetch that single project and add it so the select can show it.
        if ($plan && !empty($plan->id_project)) {
            $found = false;
            foreach ($orders as $o) {
                if (isset($o->id_project) && $o->id_project == $plan->id_project) { $found = true; break; }
            }
            if (!$found) {
                $proj = $this->PlanModel->getOrderDetails($plan->id_project);
                if ($proj) {
                    // build similar properties expected by view (opt_*)
                    $delivery = $proj->entry_date ?? ($proj->delivery_date ?? ($proj->due_date ?? null));
                    $proj->delivery_date = $delivery;
                    $proj->finished_stock_qty = $proj->finished_stock_qty ?? 0;
                    $req = isset($proj->qty_request) ? intval($proj->qty_request) : 0;
                    $proj->suggested_target = max(0, $req - ($proj->finished_stock_qty ?? 0));
                    $proj->opt_value = (string)($proj->id_project ?? '');
                    $proj->opt_data_qty = $proj->suggested_target;
                    $proj->opt_data_stock = $proj->finished_stock_qty ?? 0;
                    $proj->opt_data_delivery = $proj->delivery_date ?? '';
                    $proj->opt_label = ($proj->project_name ?? '') . ' — ' . ($proj->product_name ?? '') . ' — Yêu cầu: ' . number_format($proj->qty_request ?? 0) . ', Tồn: ' . number_format($proj->finished_stock_qty ?? 0) . ', Mục tiêu: ' . number_format($proj->suggested_target ?? 0);
                    $proj->opt_selected = true;
                    $orders[] = $proj;
                }
            }
        }

        // Chuẩn hoá trường ngày giao cho mỗi đơn hàng
        // Mục đích: tránh để view phải dò nhiều tên cột khác nhau (entry_date, delivery_date, due_date)
        // Controller sẽ gắn một thuộc tính `delivery_date` chuẩn để view dùng trực tiếp.
        // (Nếu không có ngày giao, giá trị sẽ là null/empty)
        // Normalize delivery date for each order (so view doesn't inspect DB fields)
        foreach ($orders as &$o) {
            $delivery = null;
            if (!empty($o->entry_date)) {
                $delivery = $o->entry_date;
            }
            if (empty($delivery) && !empty($o->delivery_date)) {
                $delivery = $o->delivery_date;
            }
            if (empty($delivery) && !empty($o->due_date)) {
                $delivery = $o->due_date;
            }
            // attach a normalized property used by the view
            $o->delivery_date = $delivery;
            // Compute finished stock for product (if table exists) and suggested target
            $stock_qty = 0;
            if ($this->db->table_exists('finished_stock')) {
                $fs = $this->db->get_where('finished_stock', ['id_product' => $o->id_product])->row();
                if ($fs) {
                    // prefer explicit column `quantity_in_stock` and `id_product` per request
                    if (isset($fs->quantity_in_stock)) {
                        $stock_qty = (int)$fs->quantity_in_stock;
                    } else {
                        // fallback to other possible column names
                        if (isset($fs->quantity)) $stock_qty = (int)$fs->quantity;
                        elseif (isset($fs->qty)) $stock_qty = (int)$fs->qty;
                        elseif (isset($fs->qty_finished)) $stock_qty = (int)$fs->qty_finished;
                        elseif (isset($fs->total_finished)) $stock_qty = (int)$fs->total_finished;
                    }
                }
            }
            // fallback: if finished_report exists with project-level finished, do not count here
            $o->finished_stock_qty = $stock_qty;
            $req = isset($o->qty_request) ? intval($o->qty_request) : 0;
            $o->suggested_target = max(0, $req - $stock_qty);
            // Prepare view-friendly option fields so the view can be dumb/simple
            $opt_value = (string)($o->id_project ?? '');
            $opt_data_qty = $o->suggested_target;
            $opt_data_stock = $o->finished_stock_qty ?? 0;
            $opt_data_delivery = $o->delivery_date ?? '';
            $opt_label = ($o->project_name ?? '') . ' — ' . ($o->product_name ?? '') . ' — Yêu cầu: ' . number_format($o->qty_request ?? 0) . ', Tồn: ' . number_format($opt_data_stock) . ', Mục tiêu: ' . number_format($opt_data_qty);
            $opt_selected = ($selected_project_id !== null && $selected_project_id == ($o->id_project ?? '')) ? true : false;

            $o->opt_value = $opt_value;
            $o->opt_data_qty = $opt_data_qty;
            $o->opt_data_stock = $opt_data_stock;
            $o->opt_data_delivery = $opt_data_delivery;
            $o->opt_label = $opt_label;
            $o->opt_selected = $opt_selected;
        }
        // tránh rò tham chiếu của biến $o sau khi dùng foreach by reference
        // nếu không unset, các vòng foreach tiếp theo có thể có hành vi bất ngờ
        unset($o);

        // Lấy danh sách máy từ bảng `machines` và áp dụng lọc/đặt nhãn
        // Ghi chú: bảng trong database của bạn dùng tên `machines` theo yêu cầu.
        // Filter/label logic sẽ tách trường id/name/code/capacity linh hoạt để tránh lỗi khi schema khác nhau.
        // Fetch machines from the 'machines' table and apply filtering/label logic
        // pick existing id column for ordering to avoid SQL errors
        $orderColM = null;
        $mcandidates = ['id_machine','id','machine_id','id_machines'];
        foreach ($mcandidates as $cc) { if ($this->db->field_exists($cc, 'machines')) { $orderColM = $cc; break; } }
        if ($orderColM) {
            $machines_raw = $this->db->order_by($orderColM,'ASC')->get('machines')->result();
        } else {
            $machines_raw = $this->db->get('machines')->result();
        }
        $machines = [];
        if (!empty($machines_raw)) {
            foreach ($machines_raw as $m) {
                // only include machines with code starting with 'QC'
                if (!isset($m->code) || !preg_match('/^QC/i', $m->code)) {
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
                        if (intval($status) !== 1) {
                            continue;
                        }
                    } else {
                        $s = strtolower(trim($status));
                        $excluded = ['broken', 'maintenance', 'down', 'unavailable', 'repair'];
                        if (in_array($s, $excluded, true)) {
                            continue;
                        }
                    }
                }

                // ánh xạ tên cột id (linh hoạt với nhiều tên cột khác nhau)
                $mid = null;
                if (isset($m->id_machine)) { $mid = $m->id_machine; }
                elseif (isset($m->id)) { $mid = $m->id; }
                elseif (isset($m->machine_id)) { $mid = $m->machine_id; }
                elseif (isset($m->id_machines)) { $mid = $m->id_machines; }

                // ánh xạ tên cột name/code/capacity với fallback
                $mname = $m->machine_name ?? ($m->name ?? null);
                $mcode = $m->code ?? ($m->mc_code ?? null);
                $cap = isset($m->capacity) ? $m->capacity : (isset($m->cap) ? $m->cap : 0);

                // tạo nhãn hiển thị cho option (ví dụ: 'Dây chuyền 1' hoặc tên máy)
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

        // Ensure arrays are defined for the view (avoid undefined variable issues)
        $orders = $orders ?: [];
        $machines = $machines ?: [];
        // Load product model to fetch materials list for the view
        $this->load->model('ProductModel');
        $materials = $this->ProductModel->getMaterialsList();
        $materials = $materials ?: [];

        // respect optional GET param `project_id` but normalize via CI input
        $selected_project_id = $this->input->get('project_id') ?: null;

        $data = [
            'orders' => $orders,
            'machines' => $machines,
            'materials' => $materials,
            'selected_project_id' => $selected_project_id,
            'plan' => $plan,
            'content' => 'bod/planning/PlanCreate', // view sẽ cần tạo (form)
            'navlink' => 'planning',
        ];

        // Build HTML for order options and machine options so view remains dumb
        $order_options_html = '';
        foreach ($orders as $o) {
            $val = htmlspecialchars($o->opt_value ?? ($o->id_project ?? ''), ENT_QUOTES);
            $qty = htmlspecialchars($o->opt_data_qty ?? ($o->suggested_target ?? ($o->qty_request ?? 0)), ENT_QUOTES);
            $stock = htmlspecialchars($o->opt_data_stock ?? ($o->finished_stock_qty ?? 0), ENT_QUOTES);
            $delivery = htmlspecialchars($o->opt_data_delivery ?? ($o->delivery_date ?? ''), ENT_QUOTES);
            // include product id so frontend can lookup BOM for the selected project
            $product_id = htmlspecialchars($o->id_product ?? '', ENT_QUOTES);
            $label = htmlspecialchars($o->opt_label ?? (($o->project_name ?? '') . ' — ' . ($o->product_name ?? '')), ENT_QUOTES);
            $sel = !empty($o->opt_selected) ? ' selected' : '';
            // include created_at for start date default (if available)
            $created = isset($o->created_at) ? htmlspecialchars(substr($o->created_at,0,10), ENT_QUOTES) : '';
            $order_options_html .= "<option value=\"{$val}\" data-qty=\"{$qty}\" data-stock=\"{$stock}\" data-delivery=\"{$delivery}\" data-product-id=\"{$product_id}\"" . (!empty($created) ? " data-created=\"{$created}\"" : "") . "{$sel}>{$label}</option>\n";
        }

        $machine_options_html = '';
        foreach ($machines as $m) {
            $mid = htmlspecialchars($m->id_machine ?? ($m->id ?? ''), ENT_QUOTES);
            $cap = htmlspecialchars($m->capacity ?? 0, ENT_QUOTES);
            // friendly label: prefer machine name or derive 'Dây chuyền N' from code when available
            $mname = isset($m->machine_name) ? $m->machine_name : (isset($m->name) ? $m->name : null);
            $mcode = isset($m->code) ? $m->code : (isset($m->mc_code) ? $m->mc_code : null);
            $label_val = $mname ?? ('Máy ' . ($mid ?? ''));
            if (!empty($mcode) && preg_match('/^QC[^0-9]*(\\d+)/i', $mcode, $matches)) {
                $num = intval($matches[1]);
                $label_val = 'Dây chuyền ' . ($num > 0 ? $num : $matches[1]);
            }
            $label = htmlspecialchars($label_val . ' (công suất: ' . ($m->capacity ?? 0) . ')', ENT_QUOTES);
            // mark selected machine when editing existing plan
            $selm = (isset($plan->machine_id) && (string)$plan->machine_id === (string)($m->id_machine ?? $m->id ?? '')) ? ' selected' : '';
            $machine_options_html .= "<option value=\"{$mid}\" data-capacity=\"{$cap}\"{$selm}>{$label}</option>\n";
        }

        // expose option HTML to view
        $data['order_options_html'] = $order_options_html;
        $data['machine_options_html'] = $machine_options_html;

        $this->load->view('bod/vbackend', $data);
    }

    /**
     * POST handler: lưu Kế hoạch sản xuất mới
     * - Dữ liệu mong đợi (từ form): id_project, qty_target, end_date, create_allocate (0/1), lines (JSON)
     */
    public function storePlan()
    {
        try {
            $this->load->model('PlanModel');

            // Đọc dữ liệu form
            // start_date / finish_date: ngày bắt đầu/kết thúc do user nhập (tùy chọn)
            // end_date: hạn giao (có thể do user sửa hoặc autofil từ đơn hàng)
            $id_project = $this->input->post('id_project');
            $qty_target = $this->input->post('qty_target');
            // posted `end_date` may represent the order's delivery date in the form,
            // but the DB `planning.end_date` should store the plan's finish date.
            $end_date_post = $this->input->post('end_date'); // delivery date posted (optional)
            $start_date = $this->input->post('start_date') ?: null;
            $finish_date = $this->input->post('finish_date') ?: null;
            $create_allocate = $this->input->post('create_allocate') ? 1 : 0;
            $auto_approve = $this->input->post('auto_approve') ? 1 : 0;

            // lines có thể được truyền dưới dạng JSON từ frontend
            $lines_json = $this->input->post('lines');
            $lines = [];
            if (!empty($lines_json)) {
                $decoded = json_decode($lines_json, true);
                if (is_array($decoded)) {
                    $lines = $decoded;
                } elseif (is_string($decoded)) {
                    // JSON string like "Dây chuyền 1" -> decoded is string
                    $lines = $decoded;
                } else {
                    // not valid JSON or other type, fallback to raw value
                    $lines = $lines_json;
                }
            }

            // Basic validation
            if (empty($id_project) || empty($qty_target)) {
                $this->session->set_flashdata('error_js', json_encode([
                    'message' => 'Thiếu thông tin bắt buộc: Đơn hàng hoặc Số lượng'
                ]));
                redirect(site_url('BOD/createPlan?msg=error'));
                return;
            }
            // VALIDATION NGÀY (Server-side)
            // Business rule: the project's delivery date (from the order) is authoritative
            // for the deadline. The DB column `planning.end_date` will store the plan's
            // finish date (the date the plan expects to finish production), so we
            // validate that the plan's finish/start are not after the project's delivery.
            $delivery_date = null;
            try {
                $order = $this->PlanModel->getOrderDetails($id_project);
                if ($order) {
                    $delivery_date = $order->entry_date ?? ($order->delivery_date ?? ($order->due_date ?? null));
                }
            } catch (Exception $e) { $delivery_date = null; }

            if (!empty($delivery_date)) {
                // server-side: ensure start date is not after or equal to delivery date
                if (!empty($start_date) && strtotime($start_date) >= strtotime($delivery_date)) {
                    $this->session->set_flashdata('error_js', json_encode([
                        'message' => 'Ngày bắt đầu không được trùng hoặc trễ hơn hạn giao của đơn hàng'
                    ]));
                    redirect(site_url('BOD/createPlan?msg=error'));
                    return;
                }

                // if start_date provided, ensure it's not before order created_at (if available)
                $created_date = $order->created_at ?? null;
                if (!empty($start_date) && !empty($created_date) && strtotime($start_date) < strtotime(substr($created_date,0,10))) {
                    $this->session->set_flashdata('error_js', json_encode([
                        'message' => 'Ngày bắt đầu không được trước ngày tạo đơn hàng'
                    ]));
                    redirect(site_url('BOD/createPlan?msg=error'));
                    return;
                }

                // plan finish_date must be strictly before order delivery; if finish missing, default to delivery - 1 day
                if (empty($finish_date)) {
                    $finish_date = date('Y-m-d', strtotime($delivery_date . ' -1 day'));
                }
                if (!empty($finish_date) && strtotime($finish_date) >= strtotime($delivery_date)) {
                    $this->session->set_flashdata('error_js', json_encode([
                        'message' => 'Ngày kết thúc phải trước hạn giao của đơn hàng'
                    ]));
                    redirect(site_url('BOD/createPlan?msg=error'));
                    return;
                }
            }

            // Ensure start <= finish when both provided
            if (!empty($start_date) && !empty($finish_date) && strtotime($start_date) > strtotime($finish_date)) {
                $this->session->set_flashdata('error_js', json_encode([
                    'message' => 'Ngày bắt đầu không được sau ngày kết thúc'
                ]));
                redirect(site_url('BOD/createPlan?msg=error'));
                return;
            }

            // Thêm fields materials, note, machine_id và tính số ca đề xuất
            // Process materials: the textarea may contain plain lines or a JSON array.
            $materials_text = $this->input->post('materials');
            $materials = [];
            if (!empty($materials_text)) {
                $decoded = json_decode($materials_text, true);
                if (is_array($decoded)) {
                    $materials = $decoded;
                } else {
                    // split by new line and trim empty lines
                    $lines_mat = preg_split('/\r?\n/', $materials_text);
                    $materials = array_values(array_filter(array_map('trim', $lines_mat), function($v) { return $v !== ''; }));
                }
            }

            $note = trim($this->input->post('note')) ?: null;
            $machine_id = $this->input->post('machine_id') ?: null;

            // Accept suggested_shifts from form if user computed it, otherwise estimate
            $posted_suggested = $this->input->post('suggested_shifts');
            if ($posted_suggested !== null && $posted_suggested !== '') {
                $suggested_shifts = intval($posted_suggested);
            } else {
                $est = $this->PlanModel->estimateShiftsNeeded(intval($qty_target), $machine_id);
                $suggested_shifts = $est['shifts_needed'] ?? 0;
            }

            // plan_name: use user-provided name when present; otherwise auto-generate
            $plan_name_input = trim($this->input->post('plan_name'));
            $plan_name = !empty($plan_name_input) ? $plan_name_input : ('KH-' . $id_project . '-' . time());

            $plan_data = [
                'plan_name' => $plan_name,
                'id_project' => $id_project,
                'qty_target' => intval($qty_target),
                // Store the plan's finish date in the DB column `end_date`.
                // If finish_date not provided, fall back to posted end_date (if any).
                'end_date' => $finish_date ?: ($end_date_post ?: null),
                'start_date' => $start_date ?: null,
                'finish_date' => $finish_date ?: null,
                'lines' => $lines,
                'create_allocate' => $create_allocate,
                'pl_status' => 0, // Lưu nháp, BGĐ sẽ phê duyệt sau
                'materials' => $materials,
                'note' => $note,
                'machine_id' => $machine_id,
                'suggested_shifts' => $suggested_shifts,
            ];

            $result = $this->PlanModel->createPlan($plan_data);

            if ($result['success']) {
                $id_plan = $result['id_plan'] ?? null;
                if ($auto_approve && $id_plan) {
                    $apr = $this->PlanModel->approvePlan($id_plan);
                    if (isset($apr['success']) && $apr['success'] === true) {
                        $this->session->set_flashdata('success_js', json_encode([
                            'title' => 'Thành công',
                            'message' => 'Kế hoạch đã được lưu và phê duyệt (ID: ' . $id_plan . ')'
                        ]));
                        redirect(site_url('BOD/planning?msg=created_and_approved'));
                    } else {
                        $this->session->set_flashdata('warning_js', json_encode([
                            'title' => 'Lưu thành công (phê duyệt thất bại)',
                            'message' => 'Kế hoạch đã được lưu nhưng không thể phê duyệt: ' . ($apr['message'] ?? '')
                        ]));
                        redirect(site_url('BOD/planning?msg=created_but_approve_failed'));
                    }
                }

                $this->session->set_flashdata('success_js', json_encode([
                    'title' => 'Thành công',
                    'message' => 'Kế hoạch đã được lưu (ID: ' . $result['id_plan'] . ')'
                ]));
                redirect(site_url('BOD/planning?msg=created'));
            } else {
                throw new Exception($result['message'] ?? 'Lỗi khi lưu kế hoạch');
            }

        } catch (Exception $e) {
            $this->session->set_flashdata('error_js', json_encode([
                'message' => 'Lỗi khi tạo kế hoạch',
                'details' => [$e->getMessage()]
            ]));
            redirect(site_url('BOD/createPlan?msg=error'));
        }
    }

    /**
     * POST handler: cập nhật Kế hoạch hiện có
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
            $end_date_post = $this->input->post('end_date'); // posted delivery date (optional)
            $start_date = $this->input->post('start_date') ?: null;
            $finish_date = $this->input->post('finish_date') ?: null;
            $materials_text = $this->input->post('materials');
            $materials = [];
            if (!empty($materials_text)) {
                $decoded = json_decode($materials_text, true);
                if (is_array($decoded)) $materials = $decoded; else { $lines_mat = preg_split('/\r?\n/', $materials_text); $materials = array_values(array_filter(array_map('trim', $lines_mat), function($v){ return $v !== ''; })); }
            }
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
                $this->session->set_flashdata('error_js', json_encode(['message' => 'Thiếu thông tin bắt buộc: Đơn hàng hoặc Số lượng']));
                redirect(site_url('BOD/createPlan?plan_id=' . $id_plan . '&msg=error'));
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
                    $this->session->set_flashdata('error_js', json_encode(['message' => 'Ngày bắt đầu không được trùng hoặc trễ hơn hạn giao của đơn hàng']));
                    redirect(site_url('BOD/createPlan?plan_id=' . $id_plan . '&msg=error'));
                    return;
                }
                $created_date = $order->created_at ?? null;
                if (!empty($start_date) && !empty($created_date) && strtotime($start_date) < strtotime(substr($created_date,0,10))) {
                    $this->session->set_flashdata('error_js', json_encode(['message' => 'Ngày bắt đầu không được trước ngày tạo đơn hàng']));
                    redirect(site_url('BOD/createPlan?plan_id=' . $id_plan . '&msg=error'));
                    return;
                }

                if (empty($finish_date)) {
                    $finish_date = date('Y-m-d', strtotime($delivery_date . ' -1 day'));
                }
                if (!empty($finish_date) && strtotime($finish_date) >= strtotime($delivery_date)) {
                    $this->session->set_flashdata('error_js', json_encode(['message' => 'Ngày kết thúc phải trước hạn giao của đơn hàng']));
                    redirect(site_url('BOD/createPlan?plan_id=' . $id_plan . '&msg=error'));
                    return;
                }
            }

            if (!empty($start_date) && !empty($finish_date) && strtotime($start_date) > strtotime($finish_date)) {
                $this->session->set_flashdata('error_js', json_encode(['message' => 'Ngày bắt đầu không được sau ngày kết thúc']));
                redirect(site_url('BOD/createPlan?plan_id=' . $id_plan . '&msg=error'));
                return;
            }

            $plan_data = [
                'plan_name' => trim($this->input->post('plan_name')) ?: null,
                'id_project' => $id_project,
                'qty_target' => intval($qty_target),
                // store finish date into planning.end_date
                'end_date' => $finish_date ?: ($end_date_post ?: null),
                'start_date' => $start_date ?: null,
                'finish_date' => $finish_date ?: null,
                'lines' => $lines,
                'materials' => $materials,
                'note' => $note,
                'machine_id' => $machine_id,
                'suggested_shifts' => $suggested_shifts,
            ];

            // Fetch old plan data to detect changes
            $old_plan = $this->PlanModel->getPlanById($id_plan);

            $result = $this->PlanModel->updatePlan($id_plan, $plan_data);
            if ($result['success']) {
                // --- Propagate qty_target change back to project safely ---
                try {
                    // Only if the plan is linked to a project, and qty_target changed
                    if (!empty($old_plan->id_project) && isset($plan_data['qty_target']) && intval($old_plan->qty_target) !== intval($plan_data['qty_target'])) {
                        $this->load->model('OrderModel');
                        $project = $this->PlanModel->getOrderDetails($old_plan->id_project);
                        if ($project) {
                            // If production already started, do NOT change project; mark plan needs review and audit
                            $has_finished = $this->db->where('id_project', $project->id_project)->count_all_results('finished_report') > 0;
                            if ($has_finished) {
                                // mark needs_review
                                if ($this->db->field_exists('needs_review', 'planning')) {
                                    $this->db->where('id_plan', $id_plan)->update('planning', ['needs_review' => 1]);
                                }
                                if ($this->db->table_exists('audit_log')) {
                                    $this->db->insert('audit_log', [
                                        'user_id' => $this->session->userdata('user_id'),
                                        'username' => $this->session->userdata('username'),
                                        'action' => 'mark_plan_needs_review',
                                        'module' => 'planning',
                                        'record_id' => $id_plan,
                                        'old_value' => null,
                                        'new_value' => json_encode(['reason' => 'project already has production reports, cannot change project qty from plan'], JSON_UNESCAPED_UNICODE),
                                        'ip_address' => $this->input->ip_address(),
                                        'user_agent' => $this->input->user_agent()
                                    ]);
                                }

                                $this->session->set_flashdata('warning_js', json_encode(['title' => 'Cần rà soát', 'message' => 'Không thể tự động cập nhật Số lượng đơn vì đơn đã có sản xuất. Kế hoạch đã được đánh dấu cần rà soát.']));

                            } else {
                                // Check capacity feasibility before updating project
                                $capacity_check = $this->OrderModel->checkCapacity($project->id_product, intval($plan_data['qty_target']), $project->entry_date, $project->id_project);
                                if (!$capacity_check['feasible']) {
                                    // Do not update project; mark plan needs review and log
                                    if ($this->db->field_exists('needs_review', 'planning')) {
                                        $this->db->where('id_plan', $id_plan)->update('planning', ['needs_review' => 1]);
                                    }
                                    if ($this->db->table_exists('audit_log')) {
                                        $this->db->insert('audit_log', [
                                            'user_id' => $this->session->userdata('user_id'),
                                            'username' => $this->session->userdata('username'),
                                            'action' => 'mark_plan_needs_review',
                                            'module' => 'planning',
                                            'record_id' => $id_plan,
                                            'old_value' => null,
                                            'new_value' => json_encode(['reason' => 'capacity check failed: ' . ($capacity_check['message'] ?? '')], JSON_UNESCAPED_UNICODE),
                                            'ip_address' => $this->input->ip_address(),
                                            'user_agent' => $this->input->user_agent()
                                        ]);
                                    }
                                    $this->session->set_flashdata('warning_js', json_encode(['title' => 'Không thể cập nhật Đơn', 'message' => 'Số lượng trong Kế hoạch yêu cầu rà soát về năng lực/công suất. Kế hoạch đã được đánh dấu cần rà soát.']));
                                } else {
                                    // Update project.qty_request and update capacity warning fields
                                    $update_proj = ['qty_request' => intval($plan_data['qty_target'])];
                                    // Merge capacity_check warnings into project if available
                                    if (isset($capacity_check['warning_flag'])) $update_proj['warning_flag'] = $capacity_check['warning_flag'];
                                    if (isset($capacity_check['warning_type'])) $update_proj['warning_type'] = $capacity_check['warning_type'];
                                    if (isset($capacity_check['warning_details'])) $update_proj['warning_details'] = $capacity_check['warning_details'];

                                    $this->db->where('id_project', $project->id_project)->update('project', $update_proj);

                                    $affected = $this->db->affected_rows();
                                    if ($affected > 0) {
                                        // Log the change
                                        if ($this->db->table_exists('audit_log')) {
                                            $this->db->insert('audit_log', [
                                                'user_id' => $this->session->userdata('user_id'),
                                                'username' => $this->session->userdata('username'),
                                                'action' => 'update_project_from_plan',
                                                'module' => 'project',
                                                'record_id' => $project->id_project,
                                                'old_value' => json_encode(['qty_request' => $project->qty_request], JSON_UNESCAPED_UNICODE),
                                                'new_value' => json_encode(['qty_request' => $plan_data['qty_target']], JSON_UNESCAPED_UNICODE),
                                                'ip_address' => $this->input->ip_address(),
                                                'user_agent' => $this->input->user_agent()
                                            ]);
                                        }

                                        // Refresh project's derived warnings so UI reflects the new analysis
                                        if (method_exists($this->OrderModel, 'refreshProjectWarnings')) {
                                            $this->OrderModel->refreshProjectWarnings($project->id_project);
                                        }

                                        $this->session->set_flashdata('success_js', json_encode(['title' => 'Cập nhật Đơn', 'message' => 'Số lượng đơn đã được cập nhật theo Kế hoạch.']));

                                    } else {
                                        // No rows affected - record and mark plan for manual review
                                        log_message('warning', 'UC8::updatePlan - project update affected 0 rows for project ' . ($project->id_project ?? ''));
                                        if ($this->db->field_exists('needs_review', 'planning')) {
                                            $this->db->where('id_plan', $id_plan)->update('planning', ['needs_review' => 1]);
                                        }
                                        if ($this->db->table_exists('audit_log')) {
                                            $this->db->insert('audit_log', [
                                                'user_id' => $this->session->userdata('user_id'),
                                                'username' => $this->session->userdata('username'),
                                                'action' => 'mark_plan_needs_review',
                                                'module' => 'planning',
                                                'record_id' => $id_plan,
                                                'old_value' => json_encode(['attempted_update' => $update_proj], JSON_UNESCAPED_UNICODE),
                                                'new_value' => json_encode(['reason' => 'update affected 0 rows, possible DB constraint or identical value'], JSON_UNESCAPED_UNICODE),
                                                'ip_address' => $this->input->ip_address(),
                                                'user_agent' => $this->input->user_agent()
                                            ]);
                                        }
                                        $this->session->set_flashdata('warning_js', json_encode(['title' => 'Không thể cập nhật Đơn', 'message' => 'Cập nhật Đơn không thành công — Kế hoạch đã được đánh dấu cần rà soát và ghi nhận trong nhật ký.']));
                                    }
                                }
                            }
                        }
                    }
                } catch (Exception $e) {
                    // Never fail the whole update if project sync fails; log and warn
                    log_message('error', 'UC8::updatePlan - failed to sync plan qty to project: ' . $e->getMessage());
                    $this->session->set_flashdata('warning_js', json_encode(['message' => 'Đã cập nhật Kế hoạch nhưng không thể đồng bộ Số lượng sang Đơn hàng (lỗi nội bộ).']));
                }

                // If caller requested auto-approve (button now always sets auto_approve=1), try to approve
                $auto_approve = $this->input->post('auto_approve') ? 1 : 0;
                if ($auto_approve && $id_plan) {
                    $apr = $this->PlanModel->approvePlan($id_plan);
                    if (isset($apr['success']) && $apr['success'] === true) {
                        $this->session->set_flashdata('success_js', json_encode([
                            'title' => 'Cập nhật và phê duyệt',
                            'message' => 'Kế hoạch đã được cập nhật và phê duyệt (ID: ' . $id_plan . ')'
                        ]));
                        redirect(site_url('BOD/planning?msg=updated_and_approved'));
                        return;
                    } else {
                        // updated but approve failed
                        $this->session->set_flashdata('warning_js', json_encode([
                            'title' => 'Cập nhật (phê duyệt thất bại)',
                            'message' => 'Kế hoạch đã được cập nhật nhưng không thể phê duyệt: ' . ($apr['message'] ?? '')
                        ]));
                        redirect(site_url('BOD/planning?msg=updated_but_approve_failed'));
                        return;
                    }
                }

                $this->session->set_flashdata('success_js', json_encode(['title' => 'Cập nhật', 'message' => 'Kế hoạch đã được cập nhật (ID: ' . $id_plan . ')']));
                redirect(site_url('BOD/planning?msg=updated'));
            } else {
                throw new Exception($result['message'] ?? 'Lỗi khi cập nhật kế hoạch');
            }

        } catch (Exception $e) {
            $this->session->set_flashdata('error_js', json_encode(['message' => 'Lỗi khi cập nhật kế hoạch', 'details' => [$e->getMessage()]]));
            redirect(site_url('BOD/createPlan?plan_id=' . ($this->input->post('id_plan') ?: '') . '&msg=error'));
        }
    }

    /**
     * Phê duyệt Kế hoạch (BGĐ) - action thực hiện phê duyệt
     * @param int $id_plan
     */
    public function approvePlan($id_plan = null)
    {
        try {
            $this->load->model('PlanModel');
            $id_plan = $id_plan ?: $this->input->post('id_plan');
            if (empty($id_plan)) {
                throw new Exception('Thiếu id_plan');
            }

            $res = $this->PlanModel->approvePlan($id_plan);
            if ($res['success']) {
                $this->session->set_flashdata('success_js', json_encode([
                    'title' => 'Phê duyệt',
                    'message' => 'Kế hoạch đã được phê duyệt'
                ]));
            } else {
                throw new Exception($res['message'] ?? 'Không thể phê duyệt kế hoạch');
            }

            redirect(site_url('BOD/planning?msg=approved'));

        } catch (Exception $e) {
            $this->session->set_flashdata('error_js', json_encode([
                'message' => 'Lỗi khi phê duyệt kế hoạch',
                'details' => [$e->getMessage()]
            ]));
            redirect(site_url('BOD/planning?msg=error'));
        }
    }



    /**
     * Kế hoạch sản xuất (View Only - Read Only)
     * BOD chỉ xem, không chỉnh sửa
     */
    public function planning()
    {
        // Hiển thị danh sách ĐƠN HÀNG cho mục planning.
        // Sử dụng bảng `project` làm nguồn chính và LEFT JOIN `planning` để
        // hiển thị cả các đơn hàng chưa có kế hoạch.
        // Use a subquery to join only the latest planning record per project
        $sql = "
            SELECT p.*, pl.id_plan, pl.pl_status, pl.qty_target,
                   prod.product_name, prod.diameter, c.cust_name,
                   CASE WHEN (COALESCE(pl.needs_review,0) = 1) THEN 1
                        WHEN EXISTS (
                            SELECT 1 FROM audit_log al WHERE al.module = 'planning' AND al.action = 'mark_plan_needs_review' AND al.record_id = pl.id_plan
                        ) THEN 1
                        ELSE 0 END AS plan_needs_review
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
            'content' => 'bod/planning/Planning',
            'navlink' => 'planning',
        ];

        $this->load->view('bod/vbackend', $data);
    }

    /**
     * Danh sách Kế hoạch (plans list)
     * Hiển thị các bản ghi từ bảng `planning` gồm: tên, số lượng, hạn giao, trạng thái
     */
    public function plans()
    {
        // Support filtering via GET: ?q=... (plan or project name), ?status=approved|pending|needs_review
        $q = trim((string)$this->input->get('q'));
        $status = trim((string)$this->input->get('status'));

        $whereClauses = [];
        if (!empty($q)) {
            $q_esc = $this->db->escape_like_str($q);
            $whereClauses[] = "(pl.plan_name LIKE '%" . $q_esc . "%' OR p.project_name LIKE '%" . $q_esc . "%')";
        }

        if (!empty($status)) {
            if ($status === 'approved') {
                $whereClauses[] = "pl.pl_status = 1";
            } elseif ($status === 'pending') {
                $whereClauses[] = "(pl.pl_status = 0 OR pl.pl_status IS NULL)";
            } elseif ($status === 'needs_review') {
                $whereClauses[] = "(COALESCE(pl.needs_review,0) = 1 OR EXISTS (SELECT 1 FROM audit_log al WHERE al.module = 'planning' AND al.action = 'mark_plan_needs_review' AND al.record_id = pl.id_plan))";
            }
        }

        $whereSql = '';
        if (!empty($whereClauses)) {
            $whereSql = 'WHERE ' . implode(' AND ', $whereClauses);
        }

        $sql = "
            SELECT pl.*, p.project_name,
                CASE
                    WHEN (COALESCE(pl.needs_review,0) = 1)
                        THEN 1
                    WHEN EXISTS (
                        SELECT 1 FROM audit_log al
                        WHERE al.module = 'planning' AND al.action = 'mark_plan_needs_review' AND al.record_id = pl.id_plan
                    ) THEN 1
                    ELSE 0
                END AS needs_review
            FROM planning pl
            LEFT JOIN project p ON pl.id_project = p.id_project
            " . $whereSql . "
            ORDER BY pl.end_date ASC, pl.id_plan DESC
        ";

        $rows = $this->db->query($sql)->result();

        $data = [
            'data' => $rows,
            'content' => 'bod/planning/PlanList',
            'navlink' => 'planning',
        ];

        $this->load->view('bod/vbackend', $data);
    }

    /**
     * Báo cáo tổng hợp
     * TODO: Implement comprehensive reporting dashboard
     */
    public function report()
    {
        // load project list for selector
        $projects = $this->db->select('p.id_project, p.project_name, c.cust_name, pr.product_name')
                              ->from('project p')
                              ->join('customer c', 'p.id_cust = c.id_cust', 'left')
                              ->join('product pr', 'p.id_product = pr.id_product', 'left')
                              ->order_by('p.entry_date', 'DESC')
                              ->get()->result();

        $id_project = $this->input->get('id_project') ?: $this->uri->segment(3);

        $data = [
            'navlink' => 'report',
            'projects' => $projects,
            'selected_project_id' => $id_project,
        ];

        // If no specific project requested, show the projects list view
        if (empty($id_project)) {
            $data['content'] = 'bod/report/List';
            $this->load->view('bod/vbackend', $data);
            return;
        }

        // Otherwise prepare detail report for the requested project
        $data['content'] = 'bod/report/Report';

        if (!empty($id_project)) {
            $this->load->model('OrderModel');
            $this->load->model('PlanModel');

            $order = $this->OrderModel->getOrderById($id_project);
            $data['project'] = $order;

            // latest plan
            // Allow caller to request a specific plan via ?id_plan=.. so the UI can
            // link directly from a plan to its report/shift closures.
            $requested_plan = $this->input->get('id_plan') ?: null;
            if (!empty($requested_plan)) {
                $plan = $this->PlanModel->getPlanById($requested_plan);
            } else {
            $plan_row = $this->db->order_by('id_plan', 'DESC')->get_where('planning', ['id_project' => $id_project])->row();
            $plan = $plan_row ? $this->PlanModel->getPlanById($plan_row->id_plan) : null;
            }
            $data['plan'] = $plan;

            // expose suggested shifts value (be resilient to column name differences)
            $data['suggested_shifts'] = null;
            if (!empty($plan)) {
                if (isset($plan->suggested_shifts)) {
                    $data['suggested_shifts'] = intval($plan->suggested_shifts);
                } elseif (isset($plan->suggested_shift)) {
                    $data['suggested_shifts'] = intval($plan->suggested_shift);
                }
            }

            // plan shifts + attach production shift info when available
            $plan_shifts = [];
            $production_shifts_map = [];
            $shiftIds = [];

            // load suggested plan shifts if table exists — try multiple possible table names to be resilient
            $plan_shifts = [];
            $planShiftTableCandidates = ['plan_shift', 'plan_shifts', 'planshift', 'planshifts'];
            foreach ($planShiftTableCandidates as $tbl) {
                if ($this->db->table_exists($tbl)) {
                    if ($plan && $this->db->field_exists('id_plan', $tbl)) {
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
                            // final fallback: return all rows so admin can inspect
                            $plan_shifts = $this->db->get($tbl)->result();
                        }
                    }
                    break;
                }
            }
            // normalize property name so views/controllers can use `shift_id` and `id_planshift`
            if (!empty($plan_shifts)) {
                foreach ($plan_shifts as &$pps_norm) {
                    if (!isset($pps_norm->shift_id) && isset($pps_norm->id_shift)) $pps_norm->shift_id = $pps_norm->id_shift;
                    if (!isset($pps_norm->id_planshift) && isset($pps_norm->id_planshift)) $pps_norm->id_planshift = $pps_norm->id_planshift;
                    if (!isset($pps_norm->id_planshift) && isset($pps_norm->id)) $pps_norm->id_planshift = $pps_norm->id;
                }
                unset($pps_norm);
            }

            // Prefer loading actual production shifts by plan_id when available
            if ($this->db->table_exists('production_shifts')) {
                $ps_rows = [];
                if (!empty($plan->id_plan) && ($this->db->field_exists('id_plan', 'production_shifts') || $this->db->field_exists('plan_id', 'production_shifts'))) {
                    $field = $this->db->field_exists('id_plan', 'production_shifts') ? 'id_plan' : 'plan_id';
                    $ps_rows = $this->db->where($field, $plan->id_plan)->get('production_shifts')->result();
                } else {
                    // fallback to using shift IDs referenced by plan_shift
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
                    // populate shiftIds from actual production_shifts when loaded by plan
                    if (empty($shiftIds)) {
                            $shiftIds = array_keys($production_shifts_map);
                    }

                    // attach production shift details into plan_shift entries when possible
                    if (!empty($plan_shifts)) {
                        foreach ($plan_shifts as &$pps) {
                            $key = isset($pps->shift_id) ? $pps->shift_id : ($pps->id_shift ?? null);
                            $pps->production_shift = ($key !== null) ? ($production_shifts_map[$key] ?? null) : null;
                        }
                        unset($pps);
                    }
                }
                // If we couldn't find plan_shifts but production shifts exist, try locate production_shifts by project id
                if (empty($ps_rows)) {
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
                        // Prefer explicit shift_date (date) for the day, then any start_date fallback
                        $obj->shift_date = $r->shift_date ?? $r->start_date ?? null;
                        // Keep separate time fields and also provide start_date/end_date as date-first values
                        $obj->start_time = $r->start_time ?? null;
                        $obj->end_time = $r->end_time ?? null;
                        $obj->start_date = $r->start_date ?? $r->shift_date ?? ($r->start_time ?? null);
                        $obj->end_date = $r->end_date ?? $r->end_time ?? null;
                        // prefer explicit shift_name, fallback to shift_code or constructed label
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

            // totals: finished_report, finished_receipt (received), finished_issue (issued), finished_stock
            $data['total_finished'] = (int)($this->db->select_sum('total_finished')->where('id_project', $id_project)->get('finished_report')->row()->total_finished ?? 0);
            $data['total_received'] = (int)($this->db->select_sum('quantity_received')->where('id_project', $id_project)->get('finished_receipt')->row()->quantity_received ?? 0);
            $data['total_issued'] = (int)($this->db->select_sum('quantity_issued')->where('id_project', $id_project)->get('finished_issue')->row()->quantity_issued ?? 0);

            // find shift closures related to this project using production shift IDs first, then fallback
            $shift_closures = [];
            if ($this->db->table_exists('shift_closures')) {
                if (!empty($shiftIds)) {
                    $rows = $this->db->where_in('shift_id', $shiftIds)->get('shift_closures')->result();
                    if (!empty($rows)) $shift_closures = $rows;
                }

                // fallback: closures that reference this project's finished_report id via warehouse_request_id
                if (empty($shift_closures)) {
                    $rows = $this->db->query("SELECT sc.* FROM shift_closures sc LEFT JOIN finished_report fr ON sc.warehouse_request_id = fr.id_finished WHERE fr.id_project = ?", [$id_project])->result();
                    if (!empty($rows)) $shift_closures = $rows;
                }
            }

            // Filter: only keep closures whose shift_id matches known production shift IDs
            if (!empty($shift_closures) && !empty($shiftIds)) {
                // normalize shiftIds to strings to avoid strict type mismatch between int/string
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

            $data['shift_closures'] = $shift_closures;
        }

        $this->load->view('bod/vbackend', $data);
    }
}

